<?php

namespace App\Http\Controllers\Admin;

use App\MyCourses;
use App\NormalUser;
use App\Courses;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MyCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.my_courses.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $my_courses = MyCourses::leftjoin('users','users.id','=','my_courses.user_id')
                        ->leftjoin('companies','companies.id','=','my_courses.company_id')
                        ->leftjoin('courses','courses.id','=','my_courses.course_id')
                        ->select('my_courses.*','users.Email as user_email','companies.name as company_name','courses.name as course_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $my_courses = $my_courses->where('my_courses.id', '=', "$id");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $my_courses = $my_courses->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $my_courses = $my_courses->where('courses.id','=', $course);
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $my_courses = $my_courses->where('companies.name','LIKE', "%$company%");
        }
        if (isset($data['old'])) {
            $old = $data['old'];
            $my_courses = $my_courses->where('my_courses.old', '=', $old);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $my_courses = $my_courses->whereBetween('my_courses.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $my_courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'my_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'my_courses.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'courses.name';
                break;
            case 3:
                $columnName = 'companies.name';
                break;
            case 4:
                $columnName = 'my_courses.id';
                break;
            case 5:
                $columnName = 'my_courses.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $my_courses = $my_courses->where(function ($q) use ($search) {
                $q->where('my_courses.id', '=', $search)
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('companies.name', 'LIKE', "%$search%")
                    ->orWhere('my_courses.old', '=', $search);
            });
        }

        $my_courses = $my_courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($my_courses as $my_course) {
            $user = $my_course->user_email;
            $course = $my_course->course_name;
            $company = $my_course->company_name;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $my_course->user_id . '/edit') . '">' . $user . '</a>';
            }
            if(PerUser('courses_edit') && $course !=''){
                $course= '<a target="_blank" href="' . URL('admin/courses/' . $my_course->course_id . '/edit') . '">' . $course . '</a>';
            }
            if(PerUser('company_edit') && $company !=''){
                $company= '<a target="_blank" href="' . URL('admin/company/' . $my_course->company_id . '/edit') . '">' . $company . '</a>';
            }
            $old=($my_course->old==1)?'<span class="ss">'.$my_course->old.'</span>':'<span>'.$my_course->old.'</span>';
            $records["data"][] = [
                $my_course->id,
                $user,
                $course,
                $company,
                $old,
                $my_course->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $my_course->id . '" type="checkbox" ' . ((!PerUser('my_courses_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('my_courses_publish')) ? 'class="changeStatues"' : '') . ' ' . (($my_course->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $my_course->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $my_course->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('my_courses_edit')) ? '<li>
                                            <a href="' . URL('admin/my_courses/' . $my_course->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('my_courses_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $my_course->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '')
                                    . ((PerUser('my_courses_copy')) ? '<li>
                                            <a href="'.URL('admin/my_courses/copy/'.$my_course->id).'">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.copy') . '
                                            </a>
                                        </li>' : '') .

                                    '</ul>
                                </div>',
            ];
        }
        if (isset($data["customActionType"]) && $data["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records['postData'] = $data;
        //return response()->json($data)->setCallback($request->input('callback'));
        return response()->json($records)->setCallback($request->input('callback'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses=Courses::pluck('name', 'id');
        $companies=Company::pluck('name', 'id');
        return view('auth.my_courses.add',compact('courses','companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $rules=array(
            'user' => 'required',
            'course' => 'required|exists:mysql2.courses,id',
            'company' => 'nullable|exists:mysql2.companies,id',
            'expire_date' => 'nullable|date_format:"Y-m-d"',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $old = (isset($data['old'])) ? 1 : 0;
            $company_id = (isset($data['company'])) ? $data['company'] : 0;
            $expire_date = (isset($data['expire_date'])) ? $data['expire_date'] : '0000-00-00 00:00:00';
            $my_course = new MyCourses();
            $my_course->course_id = $data['course'];
            $my_course->user_id = $user->id;
            $my_course->company_id = $company_id;
            $my_course->expire_date = $expire_date;
//            $my_course->published = $published;
            $my_course->old = $old;
            $my_course->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $my_course->published_by = Auth::user()->id;
//                $my_course->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $my_course->unpublished_by = Auth::user()->id;
//                $my_course->unpublished_date = date("Y-m-d H:i:s");
//            }
            $my_course->lastedit_by = Auth::user()->id;
            $my_course->added_by = Auth::user()->id;
            $my_course->lastedit_date = date("Y-m-d H:i:s");
            $my_course->added_date = date("Y-m-d H:i:s");
            if ($my_course->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.my_course'));
                return Redirect::to('admin/my_courses/create');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $my_course = MyCourses::findOrFail($id);
        $my_course->expire_date = date("Y-m-d", strtotime($my_course->expire_date));
        $courses=Courses::pluck('name', 'id');
        $companies=Company::pluck('name', 'id');
        $user=NormalUser::where('id', $my_course->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.my_courses.edit', compact('my_course','courses','companies','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->input();
        $my_course = MyCourses::findOrFail($id);
        $rules=array(
            'user' => 'required',
            'course' => 'required|exists:mysql2.courses,id',
            'company' => 'nullable|exists:mysql2.companies,id',
            'expire_date' => 'nullable|date_format:"Y-m-d"',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $old = (isset($data['old'])) ? 1 : 0;
            $company_id = (isset($data['company'])) ? $data['company'] : 0;
            $expire_date = (isset($data['expire_date'])) ? $data['expire_date'] : '0000-00-00 00:00:00';
            $my_course = new MyCourses();
            $my_course->course_id = $data['course'];
            $my_course->user_id = $user->id;
            $my_course->company_id = $company_id;
            $my_course->expire_date = $expire_date;
            $my_course->old = $old;
//            if ($published == 'yes' && $my_course->published=='no') {
//                $my_course->published_by = Auth::user()->id;
//                $my_course->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $my_course->published=='yes') {
//                $my_course->unpublished_by = Auth::user()->id;
//                $my_course->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $my_course->published = $published;
            $my_course->lastedit_by = Auth::user()->id;
            $my_course->lastedit_date = date("Y-m-d H:i:s");
            if ($my_course->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.my_course'));
                return Redirect::to("admin/my_courses/$my_course->id/edit");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $my_course = MyCourses::findOrFail($id);
        $my_course->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $my_course = MyCourses::findOrFail($id);
//            if ($published == 'no') {
//                $my_course->published = 'no';
//                $my_course->unpublished_by = Auth::user()->id;
//                $my_course->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $my_course->published = 'yes';
//                $my_course->published_by = Auth::user()->id;
//                $my_course->published_date = date("Y-m-d H:i:s");
//            }
//            $my_course->save();
//        } else {
//            return redirect(404);
//        }
//    }
    public function copy($id)
    {
        $my_course = MyCourses::findOrFail($id);
        $my_course->createdtime = date("Y-m-d H:i:s");
        $my_course->replicate()->save();
        return Redirect::to('admin/my_courses/'.$my_course->id.'/edit');
    }

}
