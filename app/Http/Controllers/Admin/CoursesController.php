<?php

namespace App\Http\Controllers\Admin;

use App\AllCategory;
use App\Courses;
use App\Instructors;
use App\SubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.courses.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $courses = Courses::leftJoin('instractors', 'instractors.id' , '=','courses.instractor')
            ->select('courses.*', 'instractors.name as instructor_name');
        if(PerUser('remove_medical')){
            $courses=$courses->where('show_on','!=','medical');
        }
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses = $courses->where('courses.id', '=', $id);
        }
        if (isset($data['course_name']) && !empty($data['course_name'])) {
            $course_name = $data['course_name'];
            $courses = $courses->where('courses.name', 'like', "%$course_name%");
        }
        if (isset($data['instructor_name']) && !empty($data['instructor_name'])) {
            $instructor_name = $data['instructor_name'];
            $courses = $courses->where('instractors.name', 'like', "%$instructor_name%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $courses = $courses->where('courses.url', 'like', "%$url%");
        }
        if (isset($data['en_name']) && !empty($data['en_name'])) {
            $en_name = $data['en_name'];
            $courses = $courses->where('courses.en_name', 'like', "%$en_name%");
        }
        if (isset($data['rating']) && !empty($data['rating'])) {
            $rating = $data['rating'];
            $courses = $courses->where('courses.rating', '=', $rating);
        }
        if (isset($data['rating_count']) && !empty($data['rating_count'])) {
            $rating_count = $data['rating_count'];
            $courses = $courses->where('courses.rating_count', '=', $rating_count);
        }
        if (isset($data['view']) && !empty($data['view'])) {
            $view = $data['view'];
            $courses = $courses->where('courses.view', '=', $view);
        }
        if (isset($data['sent']) && !empty($data['sent'])) {
            $sent = $data['sent'];
            $courses = $courses->where('courses.sent', '=', $sent);
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $courses = $courses->where('courses.sort', '=', $sort);
        }
        if (isset($data['certificate_increment']) && !empty($data['certificate_increment'])) {
            $certificate_increment = $data['certificate_increment'];
            $courses = $courses->where('courses.certificate_increment', '=', $certificate_increment);
        }
        if (isset($data['show_on']) && !empty($data['show_on'])) {
            $show_on = $data['show_on'];
            $courses = $courses->where('courses.show_on', '=', $show_on);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $courses = $courses->whereBetween('courses.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        $iTotalRecords = $courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'courses.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
            case 2:
                $columnName = 'courses.url';
                break;
            case 3:
                $columnName = 'instractors.name';
                break;
            case 4:
                $columnName = 'courses.en_name';
                break;
            case 5:
                $columnName = 'courses.rating';
                break;
            case 6:
                $columnName = 'courses.rating_count';
                break;
            case 7:
                $columnName = 'courses.view';
                break;
            case 8:
                $columnName = 'courses.sent';
                break;
            case 9:
                $columnName = 'courses.certificate_increment';
                break;
            case 10:
                $columnName = 'courses.show_on';
                break;
            case 11:
                $columnName = 'courses.sort';
                break;
            case 12:
                $columnName = 'courses.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $courses = $courses->where(function ($q) use ($search) {
                $q->where('courses.name','LIKE',"%$search%")
                    ->orWhere('courses.url','LIKE',"%$search%")
                    ->orWhere('instractors.name','LIKE',"%$search%")
                    ->orWhere('courses.id', '=', $search)
                    ->orWhere('courses.en_name', 'LIKE', "%$search%")
                    ->orWhere('courses.rating', '=', $search)
                    ->orWhere('courses.rating_count', '=', $search)
                    ->orWhere('courses.view', '=', $search)
                    ->orWhere('courses.sent', '=', $search)
                    ->orWhere('courses.sort', '=', $search)
                    ->orWhere('courses.certificate_increment', '=', $search)
                    ->orWhere('courses.show_on', '=', $search);

            });
        }
        $courses = $courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($courses as $course) {
            $instructor_name = $course->instructor_name;
            if(PerUser('instructors_edit') && $instructor_name !=''){
                $instructor_name= '<a target="_blank" href="' . URL('admin/instructors/' . $course->instractor . '/edit') . '">' . $instructor_name . '</a>';
            }
            $records["data"][] = [
                $course->id,
                $course->name,
                '<a href="' . ($course->show_on=='medical' ? yottaURL('course/' . $course->url.'?preview=1') : e3mURL('courses/' . $course->url.'&preview=1')) . '" target="_blank">' . $course->url . '</a>',
                $instructor_name,
                $course->en_name,
                $course->rating,
                $course->rating_count,
                $course->view,
                $course->sent,
                $course->certificate_increment,
                $course->show_on,
                $course->sort,
                $course->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $course->id . '" type="checkbox" ' . ((!PerUser('courses_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('courses_publish')) ? 'class="changeStatues"' : '') . ' ' . (($course->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $course->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $course->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_edit')) ? '<li>
                                            <a href="' . URL('admin/courses/' . $course->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $course->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                        ' . ((PerUser('courses_preview')) ? '<li>
                                            <a href="' . ($course->show_on=='medical' ? yottaURL('course/' . $course->url.'?preview=1') : e3mURL('courses/' . $course->url.'&preview=1')) . '" target="_blank">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.preview') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_reset_password')) ? '<li>
                                            <a class="reset_password" data-id="' . $course->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.reset_password') . '
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
        $instructors = Instructors::pluck( 'name','id');
        $categories = AllCategory::pluck( 'name','id');
        return view('auth.courses.add',compact('instructors','categories'));
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
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $validator = Validator::make($request->all(),array(
            'name' => 'required',
            'description' => 'required',
            'get_from_course' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            'instructor' => 'required|exists:mysql2.instractors,id',
            'url' => 'required|unique:mysql2.courses,url',
            'isclose' => 'required|in:0,1',
//            'active' => 'required|in:0,1',
            'statues' => 'required|in:new,old',
            'location' => 'nullable|in:egy,ksa,onlyeg',
//            'code' => 'required',
            'code' => 'required|unique:mysql2.courses,code',
            'egy_price' => 'required',
            'ksa_price' => 'required',
            'intro_video' => 'required',
            'course_type' => 'nullable|in:paid,free',
            'category' => 'nullable|exists:mysql2.categories,id',
            'sub_category' => 'nullable|exists:mysql2.sup_categories,id',
            'meta_description' => 'required',
            'show_on' => 'nullable|in:courses,diplomas,diplomas_mba,mba,medical,all',
            'en_name' => 'required',
            'sort' => 'required',
        ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $course = new Courses();
            $course->name = $data['name'];
            $course->description = $data['description'];
            $course->references = $data['references'];
            $course->image = $picName;
            $course->instractor = $data['instructor'];
            $course->get_from_course = $data['get_from_course'];
            $course->url = str_replace(' ','-',$data['url']);
            $course->isclose = $data['isclose'];
//            $course->active = $data['active'];
            $course->statues = $data['statues'];
            $course->location = isset($data['location'])?$data['location']:'egy';
            $course->code = $data['code'];
            $course->sort = $data['sort'];
            $course->egy_price = $data['egy_price'];
            $course->ksa_price = $data['ksa_price'];
            $course->course_type = isset($data['course_type'])?$data['course_type']:'paid';
            $course->category_id = isset($data['category'])?$data['category']:0;
            $course->sup_category_id = isset($data['sub_category'])?$data['sub_category']:0;
            $course->meta_description = $data['meta_description'];
            $course->show_on = isset($data['show_on'])?$data['show_on']:'all';
            $course->en_name = $data['en_name'];
            $course->short_description = isset($data['short_description'])?$data['short_description']:'';
            $course->lectures = $data['lectures'];
            $course->length = $data['length'];
            $course->curriculum_number = isset($data['curriculum_number'])?$data['curriculum_number']:0;
            $course->intro_vedio = $data['intro_video'];
            $course->published = $published;
            $course->direction = $data['direction'];
            $course->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $course->published_by = Auth::user()->id;
                $course->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $course->unpublished_by = Auth::user()->id;
                $course->unpublished_date = date("Y-m-d H:i:s");
            }
            $course->lastedit_by = Auth::user()->id;
            $course->added_by = Auth::user()->id;
            $course->lastedit_date = date("Y-m-d H:i:s");
            $course->added_date = date("Y-m-d H:i:s");
            if ($course->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.course'));
                return Redirect::to('admin/courses/create');
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
        $course = Courses::select('courses.*');
        if(PerUser('remove_medical')){
            $course=$course->where('show_on','!=','medical');
        }
        $course=$course->findOrFail($id);
        $instructors = Instructors::pluck('name', 'id');
        $categories = AllCategory::pluck('name', 'id');
        $sub_categories = SubCategory::where('category_id','=',$course->category_id)->pluck('name', 'id');
        return view('auth.courses.edit',compact('course','instructors','categories','sub_categories'));
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
        $course = Courses::findOrFail($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules=array(
            'name' => 'required',
            'description' => 'required',
            'get_from_course' => 'required',
            'instructor' => 'required|exists:mysql2.instractors,id',
            'url' => "required|unique:mysql2.courses,url,$id,id",
            'isclose' => 'required|in:0,1',
//            'active' => 'required|in:0,1',
            'statues' => 'required|in:new,old',
            'location' => 'nullable|in:egy,ksa,onlyeg',
//            'code' => 'required',
            'code' => 'required|unique:mysql2.courses,code,'.$id.',id',
            'egy_price' => 'required',
            'ksa_price' => 'required',
            'intro_video' => 'required',
            'course_type' => 'nullable|in:paid,free',
            'category' => 'nullable|exists:mysql2.categories,id',
            'sub_category' => 'nullable|exists:mysql2.sup_categories,id',
            'meta_description' => 'required',
            'show_on' => 'nullable|in:courses,diplomas,diplomas_mba,mba,medical,all',
            'en_name' => 'required',
            'sort' => 'required',
        );
        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $course->image = $picName;
            }
            $course->name = $data['name'];
            $course->description = $data['description'];
            $course->references = $data['references'];
            $course->instractor = $data['instructor'];
            $course->get_from_course = $data['get_from_course'];
            $old_url=$course->url;
            $course->url = str_replace(' ','-',$data['url']);
            $course->isclose = $data['isclose'];
//            $course->active = $data['active'];
            $course->statues = $data['statues'];
            $course->location = isset($data['location'])?$data['location']:'egy';
            $course->code = $data['code'];
            $course->sort = $data['sort'];
            $course->egy_price = $data['egy_price'];
            $course->ksa_price = $data['ksa_price'];
            $course->course_type = isset($data['course_type'])?$data['course_type']:'paid';
            $course->category_id = isset($data['category'])?$data['category']:0;
            $course->sup_category_id = isset($data['sub_category'])?$data['sub_category']:0;
            $course->meta_description = $data['meta_description'];
            $course->show_on = isset($data['show_on'])?$data['show_on']:'all';
            $course->en_name = $data['en_name'];
            $course->short_description = isset($data['short_description'])?$data['short_description']:'';
            $course->lectures = $data['lectures'];
            $course->length = $data['length'];
            $course->curriculum_number = isset($data['curriculum_number'])?$data['curriculum_number']:0;
            $course->intro_vedio = $data['intro_video'];
            $course->direction = $data['direction'];
            if ($published == 'yes' && $course->published=='no') {
                $course->published_by = Auth::user()->id;
                $course->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $course->published=='yes') {
                $course->unpublished_by = Auth::user()->id;
                $course->unpublished_date = date("Y-m-d H:i:s");
            }
            $course->published = $published;
            $course->lastedit_by = Auth::user()->id;
            $course->lastedit_date = date("Y-m-d H:i:s");
            if ($course->save()){
                if($old_url != $course->url){
                    saveOldUrl($id,'courses',$old_url,$course->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.course'));
                return Redirect::to("admin/courses/$course->id/edit");
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
        $course = Courses::findOrFail($id);
        $course->deleted_at=date("Y-m-d H:i:s");
        $course->save();
        //$course->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $course = Courses::findOrFail($id);
            if ($published == 'no') {
                $course->published = 'no';
                $course->unpublished_by = Auth::user()->id;
                $course->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $course->published = 'yes';
                $course->published_by = Auth::user()->id;
                $course->published_date = date("Y-m-d H:i:s");
            }
            $course->save();
        } else {
            return redirect(404);
        }
    }
}
