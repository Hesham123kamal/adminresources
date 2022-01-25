<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\DiplomaCourse;
use App\Diplomas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiplomaCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        $diplomas=Diplomas::pluck('name','id')->toArray();
        return view('auth.diploma_courses.view',compact('courses','diplomas'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $diploma_courses = DiplomaCourse::leftjoin('diplomas','diplomas.id','=','diplomas_courses.diploma_id')
            ->leftjoin('courses','courses.id','=','diplomas_courses.related_course')
            ->select('diplomas_courses.*','diplomas.name as diploma_name','courses.name as related_course_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $diploma_courses = $diploma_courses->where('diplomas_courses.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $diploma_courses = $diploma_courses->where('diplomas_courses.name', 'LIKE', "%$name%");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $diploma_courses = $diploma_courses->where('diplomas.name','LIKE', "%$diploma%");
        }
        if (isset($data['diploma_id']) && !empty($data['diploma_id'])) {
            $diploma_id = $data['diploma_id'];
            $diploma_courses = $diploma_courses->where('diplomas.id', $diploma_id);
        }
        if (isset($data['related_course']) && !empty($data['related_course'])) {
            $related_course = $data['related_course'];
            $diploma_courses = $diploma_courses->where('diplomas_courses.related_course','=', $related_course);
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $diploma_courses = $diploma_courses->where('diplomas_courses.sort','=', $sort);
        }
        if (isset($data['order_field']) && !empty($data['order_field'])) {
            $order = $data['order_field'];
            $diploma_courses = $diploma_courses->where('diplomas_courses.order','=', $order);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $diploma_courses = $diploma_courses->whereBetween('diplomas_courses.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $diploma_courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'diplomas_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diplomas_courses.id';
                break;
            case 1:
                $columnName = 'diplomas_courses.name';
                break;
            case 2:
                $columnName = 'diplomas_courses.createdtime';
                break;
            case 3:
                $columnName = 'diplomas.name';
                break;
            case 5:
                $columnName = 'courses.name';
                break;
            case 6:
                $columnName = 'diplomas_courses.sort';
                break;
            case 7:
                $columnName = 'diplomas_courses.order';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $diploma_courses = $diploma_courses->where(function ($q) use ($search) {
                $q->where('diplomas_courses.name', 'LIKE', "%$search%")
                    ->orWhere('diplomas_courses.id', '=', $search)
                    ->orWhere('diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('diplomas_courses.sort', '=', $search)
                    ->orWhere('diplomas_courses.order', '=', $search);
            });
        }
        $diploma_courses = $diploma_courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($diploma_courses as $course) {
            $course=makeDefaultImageGeneral($course,'image');
            $diploma_name = $course->diploma_name;
            $course_name = $course->related_course_name;
            if(PerUser('diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/diplomas/' . $course->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $course->related_course . '/edit') . '">' . $course_name . '</a>';
            }
            $records["data"][] = [
                $course->id,
                $course->name,
                $course->createdtime,
                $diploma_name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($course->image) . '"/></a>',
                $course_name,
                $course->sort,
                $course->order,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $course->id . '" type="checkbox" ' . ((!PerUser('diploma_courses_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('diploma_courses_publish')) ? 'class="changeStatues"' : '') . ' ' . (($course->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $course->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $course->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diploma_courses_edit')) ? '<li>
                                            <a href="' . URL('admin/diploma_courses/' . $course->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diploma_courses_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $course->id . '" >
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


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $diplomas = Diplomas::pluck('name', 'id');
        $courses = Courses::get();
        return view('auth.diploma_courses.add', compact('diplomas', 'courses'));
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
//        dd($data);
        $rules = array(
            'name' => 'required',
            'description' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            'diploma' => 'required|exists:mysql2.diplomas,id',
            'related_course' => 'required|exists:mysql2.courses,id',
            'sort' => 'required|integer',
            'order' => 'required|integer',
            'type' => 'required',
        );

        if ($data['type']=='according_to_country') {
            $rules['related_course_ksa'] = 'required_if:type,according_to_country|exists:mysql2.courses,id';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $course = new DiplomaCourse();
            $course->name = $data['name'];
            $course->type = $data['type'];
            $course->related_course = $data['related_course'];
            $course->related_course_ksa = ($data['type']=='according_to_country')?$data['related_course_ksa']:$data['related_course'];
            $course->sort = $data['sort'];
            $course->order = $data['order'];
            $course->description = $data['description'];
            $course->diploma_id = $data['diploma'];
//            $course->published = $published;
            $course->image = $picName;
            $course->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $course->published_by = Auth::user()->id;
//                $course->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $course->unpublished_by = Auth::user()->id;
//                $course->unpublished_date = date("Y-m-d H:i:s");
//            }
            $course->lastedit_by = Auth::user()->id;
            $course->added_by = Auth::user()->id;
            $course->lastedit_date = date("Y-m-d H:i:s");
            $course->added_date = date("Y-m-d H:i:s");
            if ($course->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.diploma_course'));
                return Redirect::to('admin/diploma_courses/create');
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
        $course = DiplomaCourse::findOrFail($id);
        $courses = Courses::get();
        $diplomas = Diplomas::pluck('name', 'id');
        return view('auth.diploma_courses.edit', compact('diplomas','courses','course'));
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
        $course = DiplomaCourse::findOrFail($id);
        $rules = array(
            'name' => 'required',
            'description' => 'required',
            'diploma' => 'required|exists:mysql2.diplomas,id',
            'related_course' => 'required|exists:mysql2.courses,id',
            'sort' => 'required|integer',
            'order' => 'required|integer',
            'type' => 'required',
        );
        if ($data['type']=='according_to_country') {
            $rules['related_course_ksa'] = 'required_if:type,according_to_country|exists:mysql2.courses,id';
        }

        if ($request->file('image')) {
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $course->name = $data['name'];
            $course->type = $data['type'];
            $course->related_course = $data['related_course'];
            $course->related_course_ksa = ($data['type']=='according_to_country')?$data['related_course_ksa']:$data['related_course'];
            $course->sort = $data['sort'];
            $course->order = $data['order'];
            $course->description = $data['description'];
            $course->diploma_id = $data['diploma'];
            if ($request->file('image')) {
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $course->image = $picName;
            }
//            if ($published == 'yes' && $course->published=='no') {
//                $course->published_by = Auth::user()->id;
//                $course->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $course->published=='yes') {
//                $course->unpublished_by = Auth::user()->id;
//                $course->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $course->published = $published;
            $course->lastedit_by = Auth::user()->id;
            $course->lastedit_date = date("Y-m-d H:i:s");
            if ($course->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.diploma_course'));
                return Redirect::to("admin/diploma_courses/$course->id/edit");
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
        $course = DiplomaCourse::findOrFail($id);
        $course->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $course = DiplomaCourse::findOrFail($id);
//            if ($published == 'no') {
//                $course->published = 'no';
//                $course->unpublished_by = Auth::user()->id;
//                $course->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $course->published = 'yes';
//                $course->published_by = Auth::user()->id;
//                $course->published_date = date("Y-m-d H:i:s");
//            }
//            $course->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
