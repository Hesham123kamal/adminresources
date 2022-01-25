<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\CoursesSections;
use App\CoursesCurriculum;
use App\Http\Controllers\Controller;
use Faker\Provider\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CourseCurriculumController extends Controller
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
        return view('auth.course_curriculum.view',compact('courses'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $curriculums = CoursesCurriculum::leftjoin('courses','courses.id','=','cources_curriculum.course_id')
                        ->leftjoin('courses_sections','courses_sections.id','=','cources_curriculum.section_id')
                        ->select('cources_curriculum.*','courses.name as course_name','courses.url as course_url','courses_sections.name as section_name');
        if(PerUser('remove_medical')){
            $curriculums=$curriculums->where('courses.show_on','!=','medical');
        }
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $curriculums = $curriculums->where('cources_curriculum.id', '=', "$id");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $curriculums = $curriculums->where('courses.id','=', $course);
        }
        if (isset($data['section']) && !empty($data['section'])) {
            $section = $data['section'];
            $curriculums = $curriculums->where('courses_sections.name','LIKE', "%$section%");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $curriculums = $curriculums->where('cources_curriculum.name', 'LIKE', "%$name%");
        }
        if (isset($data['description']) && !empty($data['description'])) {
            $description = $data['description'];
            $curriculums = $curriculums->where('cources_curriculum.description', 'LIKE', "%$description%");
        }
        if (isset($data['link']) && !empty($data['link'])) {
            $link = $data['link'];
            $curriculums = $curriculums->where('cources_curriculum.link', 'LIKE', "%$link%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $curriculums = $curriculums->whereBetween('cources_curriculum.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $curriculums->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'cources_curriculum.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'cources_curriculum.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
            case 2:
                $columnName = 'courses_sections.name';
                break;
            case 3:
                $columnName = 'cources_curriculum.name';
                break;
            case 4:
                $columnName = 'cources_curriculum.description';
                break;
            case 5:
                $columnName = 'cources_curriculum.link';
                break;
            case 7:
                $columnName = 'cources_curriculum.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $curriculums = $curriculums->where(function ($q) use ($search) {
                $q->where('cources_curriculum.name', 'LIKE', "%$search%")
                    ->orWhere('cources_curriculum.description', 'LIKE', "%$search%")
                    ->orWhere('cources_curriculum.link', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('courses_sections.name', 'LIKE', "%$search%")
                    ->orWhere('cources_curriculum.id', '=', $search);
            });
        }

        $curriculums = $curriculums->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($curriculums as $curriculum) {
            $course_name = $curriculum->course_name;
            $section_name=$curriculum->section_name;
            $course_url=$curriculum->course_url;
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $curriculum->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            if(PerUser('courses_sections_edit') && $section_name !=''){
                $section_name= '<a target="_blank" href="' . URL('admin/courses_sections/' . $curriculum->section_id . '/edit') . '">' . $section_name . '</a>';
            }
            $records["data"][] = [
                $curriculum->id,
                $course_name,
                $section_name,
                $curriculum->name,
                $curriculum->description,
                '<a target="_blank" href="' . $curriculum->link . '">' . $curriculum->link . '</a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $curriculum->id . '" type="checkbox" ' . ((!PerUser('course_curriculum_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('course_curriculum_publish')) ? 'class="changeStatues"' : '') . ' ' . (($curriculum->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $curriculum->id . '">
                                    </label>
                                </div>
                            </td>',
                $curriculum->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $curriculum->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('course_curriculum_edit')) ? '<li>
                                            <a href="' . URL('admin/course_curriculum/' . $curriculum->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('course_curriculum_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $curriculum->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                     ' . ((PerUser('course_curriculum_preview_audio')) ? '<li>
                                            <a href="' . e3mURL('courses/showCurriculumaudio/' . $curriculum->id . '/' .$course_url.'&preview=1'). '" target="_blank">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.preview_audio') . '
                                            </a>
                                        </li>' : '') . '
                                      ' . ((PerUser('course_curriculum_preview_video')) ? '<li>
                                            <a href="' . e3mURL('courses/showCurriculum/' . $curriculum->id . '/' .$course_url.'&preview=1'). '" target="_blank">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.preview_video') . '
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
        return view('auth.course_curriculum.add',compact( 'courses'));
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
        $rules=  array(
            'course' =>'required|exists:mysql2.courses,id',
            'section' =>'required|exists:mysql2.courses_sections,id',
            //'name' => 'required',
            //'description' => 'required',
            //'duration' => 'required',
            //'link' => 'required',
            'type' =>'required|in:default,exam,training',
            'questions_numbers' => 'required|numeric',
            'sort' => 'required|numeric',
            'language' =>'required|in:english,arabic',
        );
        if( isset($data['type']) && $data['type']=='exam'){
            $rules['question_time'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $isfree = (isset($data['isfree'])) ? 'yes' : 'no';
            $audio_link = (isset($data['audio_link'])) ? $data['audio_link'] : '';
            $curriculum = new CoursesCurriculum();
            $curriculum->course_id = $data['course'];
            $curriculum->section_id = $data['section'];
            $curriculum->name = $data['name'];
            $curriculum->description = $data['description'];
            $curriculum->duration = $data['duration'];
            $curriculum->link = $data['link'];
            $curriculum->type = $data['type'];
            if($data['type']=='exam'){
                $curriculum->question_time = $data['question_time'];
            }
            $curriculum->questions_numbers = $data['questions_numbers'];
            $curriculum->questions_type = isset($data['questions_type'])?$data['questions_type']:'arabic_or_english';
            $curriculum->sort = $data['sort'];
            $curriculum->language = $data['language'];
            $curriculum->published = $published;
            $curriculum->isfree = $isfree;
            $curriculum->audio_link = $audio_link;
            $curriculum->sent = 0;
            $curriculum->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $curriculum->published_by = Auth::user()->id;
                $curriculum->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $curriculum->unpublished_by = Auth::user()->id;
                $curriculum->unpublished_date = date("Y-m-d H:i:s");
            }
            $curriculum->lastedit_by = Auth::user()->id;
            $curriculum->added_by = Auth::user()->id;
            $curriculum->lastedit_date = date("Y-m-d H:i:s");
            $curriculum->added_date = date("Y-m-d H:i:s");
            if ($curriculum->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.course_curriculum'));
                return Redirect::to('admin/course_curriculum/create');
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
        $curriculum = CoursesCurriculum::findOrFail($id);
        if(PerUser('remove_medical')){
            $courses=Courses::where('show_on','!=','medical')->pluck('name','id');
        }else{
            $courses=Courses::pluck('name','id');
        }
        $sections = CoursesSections::where('course_id',$curriculum->course_id)->pluck('name', 'id');
        return view('auth.course_curriculum.edit',compact( 'curriculum','courses','sections'));
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
        $curriculum = CoursesCurriculum::findOrFail($id);
        $rules=array(
            'course' =>'required|exists:mysql2.courses,id',
            'section' =>'required|exists:mysql2.courses_sections,id',
            //'name' => 'required',
            //'description' => 'required',
            //'duration' => 'required',
            //'link' => 'required',
            'type' =>'required|in:default,exam,training',
            'questions_numbers' => 'required|numeric',
            'sort' => 'required|numeric',
            'language' =>'required|in:english,arabic',
        );
        if( isset($data['type']) && $data['type']=='exam'){
            $rules['question_time'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $isfree = (isset($data['isfree'])) ? 'yes' : 'no';
            $audio_link = (isset($data['audio_link'])) ? $data['audio_link'] : '';
            $curriculum->course_id = $data['course'];
            $curriculum->section_id = $data['section'];
            $curriculum->name = $data['name'];
            $curriculum->description = $data['description'];
            $curriculum->duration = $data['duration'];
            $curriculum->link = $data['link'];
            $curriculum->type = $data['type'];
            if($data['type']=='exam'){
                $curriculum->question_time = $data['question_time'];
            }
            $curriculum->questions_numbers = $data['questions_numbers'];
            $curriculum->questions_type = isset($data['questions_type'])?$data['questions_type']:'arabic_or_english';
            $curriculum->sort = $data['sort'];
            $curriculum->language = $data['language'];
            $curriculum->isfree = $isfree;
            $curriculum->audio_link = $audio_link;
            if ($published == 'yes' && $curriculum->published=='no') {
                $curriculum->published_by = Auth::user()->id;
                $curriculum->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $curriculum->published=='yes') {
                $curriculum->unpublished_by = Auth::user()->id;
                $curriculum->unpublished_date = date("Y-m-d H:i:s");
            }
            $curriculum->published = $published;
            $curriculum->lastedit_by = Auth::user()->id;
            $curriculum->lastedit_date = date("Y-m-d H:i:s");
            if ($curriculum->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.course_curriculum'));
                return Redirect::to("admin/course_curriculum/$curriculum->id/edit");
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
        $curriculum = CoursesCurriculum::findOrFail($id);
        $curriculum->deleted_at=date("Y-m-d H:i:s");
        $curriculum->save();
        //$curriculum->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $curriculum = CoursesCurriculum::findOrFail($id);
            if ($published == 'no') {
                $curriculum->published = 'no';
                $curriculum->unpublished_by = Auth::user()->id;
                $curriculum->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $curriculum->published = 'yes';
                $curriculum->published_by = Auth::user()->id;
                $curriculum->published_date = date("Y-m-d H:i:s");
            }
            $curriculum->save();
        } else {
            return redirect(404);
        }
    }

    public function getSectionsByCourseId(Request $request){
        $course=Courses::findOrFail($request->input('course_id'));
        $sections=CoursesSections::where('course_id','=',$course->id)->get();
        if($sections!==null){
            $options='';
            foreach ($sections as $section) {
                $options.='<option value="'.$section->id.'">'.$section->name.'</option>';
            }
            return $options;
        }
    }
}
