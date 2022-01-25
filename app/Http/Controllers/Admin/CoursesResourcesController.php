<?php

namespace App\Http\Controllers\Admin;

use App\CourseResource;
use App\Courses;
use App\CoursesSections;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoursesResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses_resources = CourseResource::join('courses', 'courses_resources.course_id', '=', 'courses.id')
            ->join('courses_sections', 'courses_resources.section_id', '=', 'courses_sections.id')
            ->select('courses_resources.*', 'courses.name as course_name', 'courses_sections.name as section_name')->orderBy('createdtime', 'ASC');
        if(PerUser('remove_medical')){
            $courses_resources=$courses_resources->where('courses.show_on','!=','medical');
        }
           $courses_resources=$courses_resources ->get();
        // dd($courses_resources);

        if(PerUser('remove_medical')){
            $courses=Courses::where('courses.show_on','!=','medical')->pluck('name', 'id');
        }else{
            $courses = Courses::pluck('name', 'id');
        }
        $courses=$courses->toArray();

        return view('auth.courses_resources.view', compact('courses_resources', 'courses'));
    }

    function search(Request $request)
    {
        $courses_resources = CourseResource::leftJoin('courses', 'courses.id', '=', 'courses_resources.course_id')
            ->leftJoin('courses_sections', 'courses_sections.id', '=', 'courses_resources.section_id')
            ->select('courses_resources.*', 'courses.name as course_name', 'courses_sections.name as section_name');
        if(PerUser('remove_medical')){
            $courses_resources=$courses_resources->where('courses.show_on','!=','medical');
        }
        if (isset($request->course) && !empty($request->course)) {
            $courses_resources = $courses_resources->where('courses.id', '=', $request->course);
        }
        if (isset($request->section_name) && !empty($request->section_name)) {
            $courses_resources = $courses_resources->where('courses_sections.name', 'like', '%' . $request->section_name . '%');
        }
        if (isset($request->create_date_from) && !empty($request->create_date_from)) {
            $from = $request->create_date_from;
            $to = $request->create_date_to;
            $courses_resources = $courses_resources->whereBetween('courses_resources.createdtime', [$from, $to]);
        }
        if (isset($request->description) && !empty($request->description)) {
            $courses_resources = $courses_resources->where('courses_resources.description', 'like', '%' . $request->description . '%');
        }
        $iTotalRecords = $courses_resources->count();
        $iDisplayLength = intval($request['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($request['start']);
        $sEcho = intval($request['draw']);
        $records = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        // return($courses_resources);
        $records['data']=[];
        $courses_resources = $courses_resources->skip($iDisplayStart)->take($iDisplayLength)->orderBy('createdtime', 'DESC')->get();
        foreach ($courses_resources as $resource) {
            $course_name = $resource->course_name;
            $section_name = $resource->section_name;
            if (PerUser('courses_edit') && $course_name != '') {
                $course_name = '<a target="_blank" href="' . URL('admin/courses/' . $resource->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            if (PerUser('courses_sections_edit') && $section_name != '') {
                $section_name = '<a target="_blank" href="' . URL('admin/courses_sections/' . $resource->section_id . '/edit') . '">' . $section_name . '</a>';
            }
            $records['data'][] = [
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="1"><span></span></label>',
                $course_name,
                $section_name,
                $resource->createdtime,
                $resource->description,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $resource->id . '" type="checkbox" ' . ((!PerUser('courses_resources_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('courses_resources_publish')) ? 'class="changeStatues"' : '') . ' ' . (($resource->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $resource->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $resource->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_resources_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_resources/' . $resource->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_resources_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $resource->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '

                                    </ul>
                                </div>',
            ];
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return $records;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$courses = DB::connection('mysql2')->table('courses')->get();
        $courses = Courses::select('courses.*');
        if(PerUser('remove_medical')){
            $courses=$courses->where('courses.show_on','!=','medical');
        }
        $courses=$courses->get();
        //$courses_sections = DB::connection('mysql2')->table('courses_sections')->get();
        $courses_sections = CoursesSections::get();
        // dd( $courses_sections);
        return view('auth.courses_resources.add')->with('courses', $courses)->with('courses_sections', $courses_sections);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules=[
            'file' => 'required|mimes:pdf,xlsx,csv,txt',
            'course_name' => 'required',
            //'type' => 'required',
            'section_name' => 'required',
            //'questions_numbers' => 'required',
            'description' => 'required',
            //'duration' => 'required',
            'active' => 'required',
            'sent' => 'required',
            'isfree' => 'required',
            'sort' => 'required',
            //'link' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json(['message'=>$messsage,'success'=>false]);
        }else{
            $courses_resource = new CourseResource;
            // dd($request->file->getClientOriginalName());
            $file = $request->file('file');
            $fileName = uploadFileToE3melbusiness($file);
            $courses_resource->original_file_name = $request->file->getClientOriginalName();
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $courses_resource->published = $published;
            $courses_resource->name = $request->name;
            $courses_resource->file = $fileName;
            $courses_resource->course_id = $request->course_name;
            $courses_resource->section_id = $request->section_name;
            //$courses_resource->type = $request->type;
            $courses_resource->name = $request->name;
            //$courses_resource->questions_numbers = $request->questions_numbers;
            $courses_resource->description = $request->description;
            $courses_resource->duration = $request->duration;
            $courses_resource->active = $request->active;
            $courses_resource->sent = $request->sent;
            $courses_resource->isfree = $request->isfree;
            $courses_resource->sort = $request->sort;
            $courses_resource->link = $request->link;
            $courses_resource->createdtime = Carbon::now();
            if ($published == 'yes') {
                $courses_resource->published_by = Auth::user()->id;
                $courses_resource->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $courses_resource->unpublished_by = Auth::user()->id;
                $courses_resource->unpublished_date = date("Y-m-d H:i:s");
            }
            $courses_resource->lastedit_by = Auth::user()->id;
            $courses_resource->added_by = Auth::user()->id;
            $courses_resource->lastedit_date = date("Y-m-d H:i:s");
            $courses_resource->added_date = date("Y-m-d H:i:s");
            if($courses_resource->save()){
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.insert') . Lang::get('main.courses_resources') . '</li>';
                $messsage .= '</ul></div>';
                return response()->json(['message'=>$messsage,'success'=>true]);
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
        //$courses = DB::connection('mysql2')->table('courses')->get();
        $courses = Courses::get();
        //$courses_sections = DB::connection('mysql2')->table('courses_sections')->get();
        $courses_sections = CoursesSections::get();
        $course_resource = CourseResource::find($id);
        // dd($course_resource);
        return view('auth.courses_resources.edit', compact('courses', 'courses_sections', 'course_resource'));
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
//        dd($request->file());
        $data = $request->input();
        $courses_resource = CourseResource::find($id);
//        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules = array(
            'course_name' => 'required',
            //'type' => 'required',
            'section_name' => 'required',
            //'questions_numbers' => 'required',
            'description' => 'required',
            //'duration' => 'required',
            'active' => 'required',
            'sent' => 'required',
            'isfree' => 'required',
            'sort' => 'required',
        );
        if ($request->file('file')) {
            $rules['file'] = 'mimes:pdf,xlsx,csv,txt';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json(['message'=>$messsage,'success'=>false]);
        } else {
            if ($request->file('file')) {
                $courses_resource_input = $request->file('file');
                $courses_resourceName = uploadFileToE3melbusiness($courses_resource_input);
                $courses_resource->file = $courses_resourceName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $courses_resource->name = $data['name'];
            $courses_resource->course_id = $data['course_name'];
            $courses_resource->section_id = $data['section_name'];
            //$courses_resource->type = $data['type'];
            //$courses_resource->questions_numbers = $data['questions_numbers'];
            $courses_resource->description = $data['description'];
            $courses_resource->duration = $data['duration'];
            $courses_resource->sent = $data['sent'];
            $courses_resource->active = $data['active'];
            $courses_resource->isfree = $data['isfree'];
            $courses_resource->sort = $data['sort'];
            $courses_resource->link = $data['link'];
            if ($published == 'yes' && $courses_resource->published == 'no') {
                $courses_resource->published_by = Auth::user()->id;
                $courses_resource->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $courses_resource->published == 'yes') {
                $courses_resource->unpublished_by = Auth::user()->id;
                $courses_resource->unpublished_date = date("Y-m-d H:i:s");
            }
            $courses_resource->published = $published;
            $courses_resource->lastedit_by = Auth::user()->id;
            $courses_resource->lastedit_date = date("Y-m-d H:i:s");
            if ($courses_resource->save()) {
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.update') . Lang::get('main.courses_resources') . '</li>';
                $messsage .= '</ul></div>';
                return response()->json(['message'=>$messsage,'success'=>true]);
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
        // return $id;
//        $course_resource = DB::connection('mysql2')->table('courses_resources')->where('id', '=', $id)->first();
        /* if (file_exists(filePath().$course_resource->file)) {
             unlink(filePath().$course_resource->file);
         }*/
        $course_resource = CourseResource::findOrFail($id);
        // $image = \DB::connection('mysql2')->table('files')->where('id', $id)->first();
        // $file= $image->your_file_path;
        // $filename = public_path().'/uploads_folder/'.$file;
        // \File::delete($filename);
//        $course_resource->delete();
        $course_resource->deleted_at = date("Y-m-d H:i:s");
        $course_resource->save();
        // return redirect()->route('courses_resources');
        return response()->json(['done' => 'deleted successfully']);
    }

    function get_sections($id)
    {
        $sections = CoursesSections::where("course_id", $id)->pluck("name", "id");
        return $sections;
    }

    function getUpdate(Request $request, $id)
    {
        // return $request->isfree;
        $courses_resource = CourseResource::find($id);
        // dd($request->file);
        if ($request->file) {
            $course_resource_file = DB::connection('mysql2')->table('courses_resources')->where('id', '=', $id)->first();
            // $old_file = DB::connection('mysql2')->table('courses_resources')->select('file')->where('id', '=', $id)->get();
            /*if ($course_resource_file->file) {
                 if (file_exists('files/'.$course_resource_file->file)) {
                    unlink('files/'.$course_resource_file->file);
                }

            }*/
            // return $course_resource->file;
            $file = $request->file('file');
            $fileName = uploadFileToE3melbusiness($file);
            $courses_resource->file = $fileName;
            $courses_resource->original_file_name = $request->file->getClientOriginalName();
        }
        if ($request->course_name) {
            $courses_resource->course_id = $request->course_name;
        }
        if ($request->section_name) {
            $courses_resource->section_id = $request->section_name;
        }
        $published = (isset($data['published'])) ? 'yes' : 'no';
        $courses_resource->name = $request->name;
        //$courses_resource->type = $request->type;
        $courses_resource->name = $request->name;
        //$courses_resource->questions_numbers = $request->questions_numbers;
        $courses_resource->description = $request->description;
        $courses_resource->duration = $request->duration;
        $courses_resource->active = $request->active;
        $courses_resource->sent = $request->sent;
        $courses_resource->isfree = $request->isfree;
        $courses_resource->sort = $request->sort;
        $courses_resource->link = $request->link;
        $courses_resource->modifiedtime = Carbon::now();
        if ($published == 'yes' && $courses_resource->published == 'no') {
            $courses_resource->published_by = Auth::user()->id;
            $courses_resource->published_date = date("Y-m-d H:i:s");
        }
        if ($published == 'no' && $courses_resource->published == 'yes') {
            $courses_resource->unpublished_by = Auth::user()->id;
            $courses_resource->unpublished_date = date("Y-m-d H:i:s");
        }
        $courses_resource->published = $published;
        $courses_resource->lastedit_by = Auth::user()->id;
        $courses_resource->lastedit_date = date("Y-m-d H:i:s");
        Session::put([
            'success' => 'successfully edited'
        ]);
        $saved = $courses_resource->update();
        if (!$saved) {
            return response()->json(['error' => 'we couldnt save data']);
        }
        return response()->json(['success' => 'You have successfully upload file.']);
    }

    public function downloadFile($file_name)
    {
        // if ($request->ajax()) {

        $file = filePath() . $file_name;
        //$file ='C:/xampp5/htdocs/e3melbusinessV4/assets/images/t(31).pdf';
        //$content = file_get_contents($file);
        //header('Content-Type: application/octet-stream');
        //header("Content-Transfer-Encoding: Binary");
        //header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
//            flush();
//            readfile('assets/images/t(31).pdf');
//            exit;
        if(file_exists($file)){
            return \Response::make(file_get_contents($file), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; ' . $file_name,
            ]);

        }
        return abort(404);

        //return response()->json(['success' => true, 'file' => $file, 'content' => $content]);
        //}

    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $resource = CourseResource::findOrFail($id);
            if ($published == 'no') {
                $resource->published = 'no';
                $resource->unpublished_by = Auth::user()->id;
                $resource->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $resource->published = 'yes';
                $resource->published_by = Auth::user()->id;
                $resource->published_date = date("Y-m-d H:i:s");
            }
            $resource->save();
        } else {
            return redirect(404);
        }
    }
}
