<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\CoursesCurriculum;
use App\CoursesSections;
use App\Http\Controllers\Controller;
use App\Instructors;
use App\Webinars;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class WebinarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parent_webinars = Webinars::where('webinar.show_on','e3melbusiness')->get();
        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.webinars.view', compact('instructors', 'parent_webinars'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $webinars = Webinars::where('webinar.show_on','e3melbusiness')->select('webinar.*', 'parent.name AS parent_name','instractors.name AS instructor_name','instractors.url AS instructor_url')->leftJoin('webinar AS parent', 'parent.id', '=', 'webinar.parent_id')->leftJoin('instractors','webinar.instractor','=','instractors.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $webinars = $webinars->where('webinar.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $webinars = $webinars->where('webinar.name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $webinars = $webinars->whereBetween('webinar.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['instructors']) && !empty($data['instructors'])) {
            $webinars = $data['instructors'];
            $webinars = $webinars->where('webinar.instractor', 'LIKE', "%$webinars%");
        }
        if (isset($data['location']) && !empty($data['location'])) {
            $location = $data['location'];
            $webinars = $webinars->where('webinar.location', 'LIKE', "%$location%");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $webinars = $webinars->where('webinar.type', 'LIKE', "%$type%");
        }
        if (isset($data['parent_id']) && !empty($data['parent_id'])) {
            $parent_id = $data['parent_id'];
            $webinars = $webinars->where('parent.name', 'LIKE', "%$parent_id%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $webinars = $webinars->where('webinar.url', 'LIKE', "%$url%");
        }


        $iTotalRecords = $webinars->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'webinar.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'webinar.id';
                break;
            case 1:
                $columnName = 'webinar.name';
                break;
            case 2:
                $columnName = 'parent.name';
                break;
            case 3:
                $columnName = 'instractors.name';
                break;
            case 4:
                $columnName = 'webinar.location';
                break;
            case 5:
                $columnName = 'webinar.url';
                break;
            case 6:
                $columnName = 'webinar.type';
                break;
            case 7:
                $columnName = 'webinar.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $webinars = $webinars->where(function ($q) use ($search) {
                $q->where('webinar.name', 'LIKE', "%$search%")
                    ->orWhere('webinar.instractor', 'LIKE', "%$search%")
                    ->orWhere('webinar.location', 'LIKE', "%$search%")
                    ->orWhere('webinar.type', 'LIKE', "%$search%")
                    ->orWhere('parent.parent_name', 'LIKE', "%$search%")
                    ->orWhere('webinar.meta_description', 'LIKE', "%$search%")
                    ->orWhere('webinar.description', 'LIKE', "%$search%")
                    ->orWhere('webinar.id', '=', $search);
            });
        }

        $webinars = $webinars->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($webinars as $webinar) {
            $records["data"][] = [
                $webinar->id,
                $webinar->name,
                $webinar->parent_name,
                '<a href="' . e3mURL('instractor/' . $webinar->instructor_url) . '" target="_blank">' . $webinar->instructor_name . '</a>',
                $webinar->location,
                $webinar->url,
                $webinar->type,
                $webinar->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $webinar->id . '" type="checkbox" ' . ((!PerUser('webinars_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('webinars_active')) ? 'class="changeStatues"' : '') . ' ' . (($webinar->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $webinar->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $webinar->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('webinars_edit')) ? '<li>
                                            <a href="' . URL('admin/webinars/' . $webinar->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                        ' . ((PerUser('webinars_convert')&&$webinar->parent_id==0&&$webinar->converted_course_id==0) ? '<li>
                                            <a data-name="'.$webinar->name.'" data-url="'.URL('admin/webinars/' . $webinar->id . '/convert').'" href="#" class="convertToCourse">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.convert') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('webinars_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $webinar->id . '" >
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
        $parent_webinars = Webinars::where('webinar.show_on','e3melbusiness')->get();
        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.webinars.add', compact('instructors', 'parent_webinars'));
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
                'name' => 'required',
                'description' => 'required',
                'pic' => 'required',
                'url' => 'required',
                'type' => 'required',
                'isfree' => 'required',
                'meta_description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $webinars = new Webinars();
            $webinars->show_on = 'e3melbusiness';
            $webinars->name = $data['name'];
            $webinars->short_description = $data['short_description'];
            $webinars->description = $data['description'];
            $webinars->link = $data['v_url'];
            $webinars->audio_link = $data['audio_link'];
            $webinars->image = $picName;
            $webinars->duetime = $data['duetime'];
            $webinars->instractor = $data['instructors'];
            $webinars->location = $data['location'];
            $webinars->url = str_replace(' ','-',$data['url']);
            $webinars->type = $data['type'];
            $webinars->parent_id = ($data['parent_id'])?$data['parent_id']:0;
            $webinars->isfree = $data['isfree'];
            $webinars->meta_description = $data['meta_description'];
            $webinars->published = $active;
            if ($active == 'yes') {
                $webinars->published_by = Auth::user()->id;
                $webinars->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $webinars->unpublished_by = Auth::user()->id;
                $webinars->unpublished_date = date("Y-m-d H:i:s");
            }
            $webinars->added_by = Auth::user()->id;
            $webinars->added_date = date("Y-m-d H:i:s");
            $webinars->createdtime = date("Y-m-d H:i:s");
            $webinars->lastedit_by = Auth::user()->id;
            $webinars->lastedit_date = date("Y-m-d H:i:s");
            if ($webinars->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.webinars'));
                return Redirect::to('admin/webinars/create');
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
        $webinar = Webinars::where('webinar.show_on','e3melbusiness')->find($id);
        $parent_webinars = Webinars::get();
        $instructors = Instructors::select('instractors.id', 'instractors.name')->get();
        return view('auth.webinars.edit', compact('instructors', 'parent_webinars','webinar'));
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
        $webinar = Webinars::find($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'description' => 'required',
                'url' => 'required',
                'type' => 'required',
                'isfree' => 'required',
                'meta_description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ( $request->file('pic')){
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $webinar->image = $picName;
            }
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $webinar->name = $data['name'];
            $webinar->short_description = $data['short_description'];
            $webinar->description = $data['description'];
            $webinar->link = $data['v_url'];
            $webinar->audio_link = $data['audio_link'];
            $webinar->duetime = $data['duetime'];
            $webinar->instractor = $data['instructors'];
            $webinar->location = $data['location'];
            $old_url=$webinar->url;
            $webinar->url = str_replace(' ','-',$data['url']);
            $webinar->type = $data['type'];
            $webinar->parent_id = ($data['parent_id'])?$data['parent_id']:0;
            $webinar->isfree = $data['isfree'];
            $webinar->meta_description = $data['meta_description'];
            if ($active == 'yes' && $webinar->published=='no') {
                $webinar->published_by = Auth::user()->id;
                $webinar->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no' && $webinar->published=='yes') {
                $webinar->unpublished_by = Auth::user()->id;
                $webinar->unpublished_date = date("Y-m-d H:i:s");
            }
            $webinar->published = $active;
            $webinar->lastedit_by = Auth::user()->id;
            $webinar->lastedit_date = date("Y-m-d H:i:s");
            if ($webinar->save()) {
                if($old_url != $webinar->url){
                    saveOldUrl($id,'webinar',$old_url,$webinar->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.webinars'));
                return Redirect::to("admin/webinars/$webinar->id/edit");
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
        $webinar = Webinars::where('webinar.show_on','e3melbusiness')->find($id);
        if (count($webinar)) {
            $webinar->delete();
            $webinar->deleted_by = Auth::user()->id;
            $webinar->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $webinar = Webinars::where('webinar.show_on','e3melbusiness')->find($id);
            if ($active == 'no') {
                $webinar->published = 'no';
                $webinar->unpublished_by = Auth::user()->id;
                $webinar->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $webinar->published = 'yes';
                $webinar->published_by = Auth::user()->id;
                $webinar->published_date = date("Y-m-d H:i:s");
            }
            $webinar->save();
        } else {
            return redirect(404);
        }
    }
    public function convert($id){
        $webinar=Webinars::where('parent_id',0)->find($id);
        if($webinar){
            if($webinar->converted_course_id){
                $course=Courses::find($webinar->converted_course_id);
                $supWebinars=Webinars::where('parent_id',$webinar->id)->get();

                if(count($course)){
                    $section=CoursesSections::where('course_id',$course->id)->where('type','normal')->where('name','المحاضرات')->first();
                    if(!count($section)){
                        $section=new CoursesSections();
                        $section->course_id=$course->id;
                        $section->type='normal';
                        $section->name='المحاضرات';
                        $section->published='yes';
                        $section->save();
                    }
                    $x=1;
                    foreach ($supWebinars as $we){
                        $cources_curriculum=CoursesCurriculum::where('course_id',$course->id)->where('section_id',$section->id)->where('description',$we->name)->first();
                        if(!count($cources_curriculum)){
                            $cources_curriculum=new CoursesCurriculum();
                            $cources_curriculum->course_id=$course->id;
                            $cources_curriculum->section_id=$section->id;
                            $cources_curriculum->language='arabic';
                            $cources_curriculum->questions_type='arabic_or_english';
                            $cources_curriculum->name=(strlen($x)==1)?'0'.$x:$x;
                            $cources_curriculum->description=$we->name;
                            $cources_curriculum->link=$we->link;
                            $cources_curriculum->audio_link=$we->audio_link;
                            $cources_curriculum->save();
                        }
                        $x++;
                    }

                    return redirect('https://www.e3melbusiness.com/courses/'.$course->url.'&preview=1');
                }
                return abort(404);
            }
            $supWebinars=Webinars::where('parent_id',$webinar->id)->get();
            $course=new Courses();
            $course->meta_description=$webinar->meta_description;
            $course->name=$webinar->name;
            $course->description=$webinar->description;
            $course->short_description=$webinar->short_description;
            $course->image=$webinar->image;
            $course->instractor=$webinar->instractor;
            $course->lectures=count($supWebinars);
            $course->intro_vedio=$webinar->link;
            $course->url=$webinar->url;
            $course->code=str_random(4);
            $course->show_on='all';
            $course->published='no';
            $course->save();
            $webinar->converted_course_id=$course->id;
            $webinar->converted_at=date('Y-m-d H:i:s');
            $webinar->converted_by=Auth::user()->id;
            $webinar->save();

            $section=new CoursesSections();
            $section->course_id=$course->id;
            $section->type='normal';
            $section->name='المحاضرات';
            $section->published='yes';
            $section->save();

            $x=1;

            foreach ($supWebinars as $we){
                $cources_curriculum=new CoursesCurriculum();
                $cources_curriculum->course_id=$course->id;
                $cources_curriculum->section_id=$section->id;
                $cources_curriculum->language='arabic';
                $cources_curriculum->questions_type='arabic_or_english';
                $cources_curriculum->name=(strlen($x)==1)?'0'.$x:$x;
                $cources_curriculum->description=$we->name;
                $cources_curriculum->link=$we->link;
                $cources_curriculum->audio_link=$we->audio_link;
                $cources_curriculum->save();
                $x++;
            }
            return redirect('https://www.e3melbusiness.com/courses/'.$course->url.'&preview=1');
        }
        return abort(404);
    }
}
