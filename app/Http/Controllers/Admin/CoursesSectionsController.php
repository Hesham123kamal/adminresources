<?php

namespace App\Http\Controllers\Admin;

use App\CoursesSections;
use App\Courses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoursesSectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(PerUser('remove_medical')){
            $courses=Courses::where('show_on','!=','medical')->pluck('name','id')->toArray();
        }else{
            $courses=Courses::pluck('name','id')->toArray();
        }
        return view('auth.courses_sections.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $courses_sections = CoursesSections::leftjoin('courses','courses.id','=','courses_sections.course_id')
        ->select('courses_sections.*','courses.name as course_name');
        if(PerUser('remove_medical')){
            $courses_sections=$courses_sections->where('courses.show_on','!=','medical');
        }
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses_sections = $courses_sections->where('courses_sections.id', '=', "$id");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $courses_sections = $courses_sections->where('courses_sections.type',$type);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $courses_sections = $courses_sections->where('courses_sections.name', 'LIKE', "%$name%");
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $courses_sections = $courses_sections->where('courses_sections.sort',$sort);
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $courses_sections = $courses_sections->where('courses.id', '=', $course);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $courses_sections = $courses_sections->whereBetween('courses_sections.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $courses_sections->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'courses_sections.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'courses_sections.id';
                break;
            case 1:
                $columnName = 'courses_sections.type';
                break;
            case 2:
                $columnName = 'courses.name';
                break;
            case 3:
                $columnName = 'courses_sections.name';
                break;
            case 4:
                $columnName = 'courses_sections.sort';
                break;
            case 5:
                $columnName = 'courses_sections.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $courses_sections = $courses_sections->where(function ($q) use ($search) {
                $q->where('courses_sections.name', 'LIKE', "%$search%")
                    ->orWhere('courses_sections.type', '=', $search)
                    ->orWhere('courses_sections.id', '=', $search)
                    ->orWhere('courses.name', 'LIKE', "%$search%");
            });
        }

        $courses_sections = $courses_sections->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($courses_sections as $section) {
            $course_name = $section->course_name;
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $section->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            $records["data"][] = [
                $section->id,
                $section->type,
                $course_name,
                $section->name,
                $section->sort,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $section->id . '" type="checkbox" ' . ((!PerUser('courses_sections_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('courses_sections_publish')) ? 'class="changeStatues"' : '') . ' ' . (($section->published == 'yes') ? 'checked="checked"' : '') . '  id="checkbox-'.$section->id.'">
                                    <label for="checkbox-' . $section->id . '">
                                    </label>
                                </div>
                            </td>',
                $section->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $section->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_sections_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_sections/' . $section->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_sections_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $section->id . '" >
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
        if(PerUser('remove_medical')){
            $courses=Courses::where('show_on','!=','medical')->pluck('name','id');
        }else{
            $courses=Courses::pluck('name','id');
        }
        $maxsort=CoursesSections::max('sort')+1;
        return view('auth.courses_sections.add',compact('courses','maxsort'));
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
        $validator = Validator::make($request->all(),
            array(
                'course' =>'required|exists:mysql2.courses,id',
                'name' => 'required',
                'sort' => 'required',
                'type' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $section = new CoursesSections();
            $section->course_id = $data['course'];
            $section->type = $data['type'];
            $section->name = $data['name'];
            $section->sort = $data['sort'];
            $section->published = $published;
            $section->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $section->published_by = Auth::user()->id;
                $section->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $section->unpublished_by = Auth::user()->id;
                $section->unpublished_date = date("Y-m-d H:i:s");
            }
            $section->lastedit_by = Auth::user()->id;
            $section->added_by = Auth::user()->id;
            $section->lastedit_date = date("Y-m-d H:i:s");
            $section->added_date = date("Y-m-d H:i:s");
            if ($section->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.course_section'));
                return Redirect::to('admin/courses_sections/create');
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
        if(PerUser('remove_medical')){
            $courses=Courses::where('show_on','!=','medical')->pluck('name','id');
        }else{
            $courses=Courses::pluck('name','id');
        }
        $section = CoursesSections::findOrFail($id);
        return view('auth.courses_sections.edit', compact('courses','section'));
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
        $section = CoursesSections::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'course' =>'required|exists:mysql2.courses,id',
                'name' => 'required',
                'sort' => 'required',
                'type' => 'required',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $section->course_id = $data['course'];
            $section->name = $data['name'];
            $section->sort = $data['sort'];
            $section->type = $data['type'];
            if ($published == 'yes' && $section->published=='no') {
                $section->published_by = Auth::user()->id;
                $section->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $section->published=='yes') {
                $section->unpublished_by = Auth::user()->id;
                $section->unpublished_date = date("Y-m-d H:i:s");
            }
            $section->published = $published;
            $section->lastedit_by = Auth::user()->id;
            $section->lastedit_date = date("Y-m-d H:i:s");
            if ($section->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.course_section'));
                return Redirect::to("admin/courses_sections/$section->id/edit");
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
        $section = CoursesSections::findOrFail($id);
        $section->deleted_at=date("Y-m-d H:i:s");
        $section->save();
        //$section->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $section = CoursesSections::findOrFail($id);
            if ($published == 'no') {
                $section->published = 'no';
                $section->unpublished_by = Auth::user()->id;
                $section->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $section->published = 'yes';
                $section->published_by = Auth::user()->id;
                $section->published_date = date("Y-m-d H:i:s");
            }
            $section->save();
        } else {
            return redirect(404);
        }
    }
}
