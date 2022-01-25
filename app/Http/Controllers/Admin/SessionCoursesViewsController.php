<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\SessionCoursesViews;
use App\Http\Controllers\Controller;
use App\NormalUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SessionCoursesViewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.session_courses_views.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $scvs = SessionCoursesViews::leftjoin('users','users.id','=','session_courses_views.user_id')
            ->leftjoin('courses','courses.id','=','session_courses_views.course_id')
            ->select('session_courses_views.*','courses.name as course_name','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $scvs = $scvs->where('session_courses_views.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $scvs = $scvs->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['count']) && !empty($data['count'])) {
            $count = $data['count'];
            $scvs = $scvs->where('session_courses_views.count', '=', $count);
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $scvs = $scvs->where('courses.id','=', $course);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $scvs = $scvs->whereBetween('session_courses_views.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $scvs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'session_courses_views.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'session_courses_views.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'session_courses_views.count';
                break;
            case 4:
                $columnName = 'session_courses_views.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $scvs = $scvs->where(function ($q) use ($search) {
                $q->where('session_courses_views.id', '=', $search)
                    ->orWhere('session_courses_views.count', '=', $search)
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%");
            });
        }

        $scvs = $scvs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($scvs as $scv) {
            $course_name = $scv->course_name;
            $user_email = $scv->user_email;
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $scv->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $scv->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $scv->id,
                $course_name,
                $user_email,
                $scv->count,
                $scv->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $scv->id . '" type="checkbox" ' . ((!PerUser('session_courses_views_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('session_courses_views_publish')) ? 'class="changeStatues"' : '') . ' ' . (($scv->published=="yes") ? 'checked="checked"' : '') . ' ">
                                    <label for="checkbox-' . $scv->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $scv->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('session_courses_views_edit')) ? '<li>
                                            <a href="' . URL('admin/session_courses_views/' . $scv->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('session_courses_views_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $scv->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '


                                    </ul>
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

    public function create()
    {
        $courses=Courses::pluck('name', 'id');
        return view('auth.session_courses_views.add',compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $rules=array(
            'course' => 'required|exists:mysql2.courses,id',
            'count' => 'required|numeric',
        );
        if(isset($data['user'])) {
            $user = NormalUser::where('Email', $data['user'])->first();
            if ($user === null) {
                $rules['user'] = 'exists:mysql2.users,Email';
            }
            else{
                $user=$user->id;
            }
        }
        else{
            $user=0;
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $course_view = new SessionCoursesViews();
            $course_view->course_id = $data['course'];
            $course_view->user_id = $user;
            $course_view->count = $data['count'];
            $course_view->published = $published;
            $course_view->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $course_view->published_by = Auth::user()->id;
                $course_view->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $course_view->unpublished_by = Auth::user()->id;
                $course_view->unpublished_date = date("Y-m-d H:i:s");
            }
            $course_view->lastedit_by = Auth::user()->id;
            $course_view->added_by = Auth::user()->id;
            $course_view->lastedit_date = date("Y-m-d H:i:s");
            $course_view->added_date = date("Y-m-d H:i:s");
            if ($course_view->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.session_course_view'));
                return Redirect::to('admin/session_courses_views/create');
            }
        }
    }

    public function edit($id)
    {
        $course_view = SessionCoursesViews::findOrFail($id);
        $courses=Courses::pluck('name', 'id');
        $user=NormalUser::where('id', $course_view->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.session_courses_views.edit', compact('course_view','courses','user'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        $course_view = SessionCoursesViews::findOrFail($id);
        $rules=array(
            'course' => 'required|exists:mysql2.courses,id',
            'count' => 'required|numeric',
        );

        if(isset($data['user'])) {
            $user = NormalUser::where('Email    ', $data['user'])->first();
            if ($user === null) {
                $rules['user'] = 'exists:mysql2.users,Email';
            }
            else{
                $user=$user->id;
            }
        }
        else{
            $user=0;
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $course_view->user_id = $user;
            $course_view->course_id = $data['course'];
            $course_view->count = $data['count'];
            if ($published == 'yes' && $course_view->published=='no') {
                $course_view->published_by = Auth::user()->id;
                $course_view->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $course_view->published=='yes') {
                $course_view->unpublished_by = Auth::user()->id;
                $course_view->unpublished_date = date("Y-m-d H:i:s");
            }
            $course_view->published = $published;
            $course_view->lastedit_by = Auth::user()->id;
            $course_view->lastedit_date = date("Y-m-d H:i:s");
            $course_view->modifiedtime = date("Y-m-d H:i:s");
            if ($course_view->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.session_course_view'));
                return Redirect::to("admin/session_courses_views/$course_view->id/edit");
            }
        }
    }

    public function destroy($id)
    {
        $course_view = SessionCoursesViews::findOrFail($id);
        $course_view->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $course_view = SessionCoursesViews::findOrFail($id);
            if ($published == 'no') {
                $course_view->published = 'no';
                $course_view->unpublished_by = Auth::user()->id;
                $course_view->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $course_view->published = 'yes';
                $course_view->published_by = Auth::user()->id;
                $course_view->published_date = date("Y-m-d H:i:s");
            }
            $course_view->save();
        } else {
            return redirect(404);
        }
    }

}
