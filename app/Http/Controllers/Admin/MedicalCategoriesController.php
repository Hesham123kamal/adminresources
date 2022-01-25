<?php

namespace App\Http\Controllers\Admin;

use App\MedicalCategories;
use App\Courses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MedicalCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::where('show_on','=','medical')->pluck('name','id')->toArray();
        return view('auth.medical_categories.view', compact('courses'));
    }


    function search(Request $request)
    {
        $data = $request->input();
        $medical_categories = MedicalCategories::select('medical_categories.*','courses.id as c_id','courses.name as course_name','medical_categories_courses.course_id')
            ->leftJoin('medical_categories_courses','medical_categories_courses.category_id','=','medical_categories.id')
            ->leftJoin('courses','courses.id','=','medical_categories_courses.course_id')->groupBy('medical_categories.id');

        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $medical_categories = $medical_categories->where('medical_categories.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $medical_categories = $medical_categories->where('medical_categories.name', 'LIKE', "%$name%");
        }
        if (isset($data['price']) && !empty($data['price'])) {
            $price = $data['price'];
            $medical_categories = $medical_categories->where('medical_categories.price', '=', "$price");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $medical_categories = $medical_categories->where('medical_categories.url', 'LIKE', "%$url%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $medical_categories = $medical_categories->where('courses.id', '=', "$course");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $medical_categories = $medical_categories->whereBetween('medical_categories.added_date', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        $iTotalRecords = $medical_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'medical_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'medical_categories.id';
                break;
            case 1:
                $columnName = 'medical_categories.name';
                break;
            case 2:
                $columnName = 'medical_categories.price';
                break;
            case 3:
                $columnName = 'articles.url';
                break;
            case 6:
                $columnName = 'medical_categories.added_date';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $medical_categories = $medical_categories->where(function ($q) use ($search) {
                $q->where('medical_categories.name', 'LIKE', "%$search%")
                    ->orWhere('medical_categories.price', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('medical_categories.url', 'LIKE', "%$search%")
                    ->orWhere('medical_categories.id', '=', $search);
            });
        }

        $medical_categories = $medical_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($medical_categories as $medical_category) {
            $courses=$medical_category->courses()->get();
            $courses_string='';
            if(count($courses)){
                if(PerUser('courses_edit')) {
                    foreach ($courses as $course) {
                        $courses_string .= '<a target="_blank" href="' . URL('admin/courses/' . $course->id . '/edit') . '"><span class="badge badge-info">' . $course->name . '</span></a>';
                    }
                }
                else{
                    foreach ($courses as $course) {
                        $courses_string .= '<span class="badge badge-info">' . $course->name . '</span>';
                    }
                }
            }
            $medical_category=makeDefaultImageGeneral($medical_category,'image');
            $records["data"][] = [
                $medical_category->id,
                $medical_category->name,
                $medical_category->price,
                $medical_category->url,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="100%" src="' . assetURL($medical_category->image) . '"/></a>',
                $courses_string,
                $medical_category->added_date,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $medical_category->id . '" type="checkbox" ' . ((!PerUser('medical_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('medical_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($medical_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $medical_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $medical_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('medical_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/medical_categories/' . $medical_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('medical_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $medical_category->id . '" >
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
        $courses = Courses::where('show_on','=','medical')->get()->pluck('name', 'id');
        $main_categories=MedicalCategories::where('parent_id',0)->get()->pluck('name','id');
        return view('auth.medical_categories.add', compact('courses','main_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules= array(
            'name' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'price' => 'required|numeric',
            'url' => 'required|unique:mysql2.medical_categories,url',
            'picture' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            'type' => 'required|in:main,sub',
            /*'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required',*/
        );
        if( isset($data['type']) && $data['type']=='sub'){
            $rules['parent_category'] = 'required|exists:mysql2.medical_categories,id';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('picture');
            $picName = uploadFileToE3melbusiness($pic);
            $medical_category = new MedicalCategories();
            $medical_category->name = $data['name'];
            $medical_category->description = $data['description'];
            $medical_category->short_description = $data['short_description'];
            $medical_category->price = $data['price'];
            $medical_category->image = $picName;
            $medical_category->url = str_replace(' ','-',$data['url']);
            $medical_category->meta_title = $data['meta_title'];
            $medical_category->meta_description = $data['meta_description'];
            $medical_category->meta_keywords = $data['meta_keywords'];
            if($data['type']=='sub'){
                $medical_category->parent_id = $data['parent_category'];
            }
            $medical_category->published = $published;
            if ($published == 'yes') {
                $medical_category->published_by = Auth::user()->id;
                $medical_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $medical_category->unpublished_by = Auth::user()->id;
                $medical_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $medical_category->added_by = Auth::user()->id;
            $medical_category->added_date = date("Y-m-d H:i:s");
            if ($medical_category->save()) {
                $path = ltrim($this->make_path($medical_category->parent_id,$medical_category->id),':');
                $medical_category->path=$path;
                $medical_category->save();
                if(isset($data['course'])){
                    $courses=(array)$data['course'];
                    $pivotData = array_fill(0, count($courses), ['category_id' => $medical_category->id,'added_by'=>Auth::user()->id,'added_date'=>date("Y-m-d H:i:s")]);
                    $syncData  = array_combine($courses, $pivotData);
                    $medical_category->courses()->sync($syncData);

                }
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.medical_category'));
                return Redirect::to('admin/medical_categories/create');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $medical_category = MedicalCategories::findOrFail($id);
        $medical_category=makeDefaultImageGeneral($medical_category,'image');
        $courses = Courses::where('show_on','=','medical')->get()->pluck('name', 'id');
        $main_categories=MedicalCategories::where('parent_id',0)->get()->pluck('name','id');
        return view('auth.medical_categories.edit', compact('medical_category','courses','main_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->input();
        $medical_category = MedicalCategories::findOrFail($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules=array(
            'name' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'price' => 'required|numeric',
            'url' => "required|unique:mysql2.medical_categories,url,$id,id",
            'type' => 'required|in:main,sub',
            /*'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required',*/
        );
        if ( $request->file('picture')){
            $rules['picture']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            if ( $request->file('picture')){
                $pic = $request->file('picture');
                $picName = uploadFileToE3melbusiness($pic);
                $medical_category->image = $picName;
            }
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $medical_category->name = $data['name'];
            $medical_category->meta_title = $data['meta_title'];
            $medical_category->meta_description = $data['meta_description'];
            $medical_category->meta_keywords = $data['meta_keywords'];
            if($data['type']=='sub'){
                $medical_category->parent_id = $data['parent_category'];
            }
            else{
                $medical_category->parent_id=0;
            }
            $medical_category->description = $data['description'];
            $medical_category->short_description = $data['short_description'];
            $medical_category->price = $data['price'];
            $old_url = $medical_category->url;
            $medical_category->url = str_replace(' ','-',$data['url']);
            if ($published == 'yes' && $medical_category->published=='no') {
                $medical_category->published_by = Auth::user()->id;
                $medical_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $medical_category->published=='yes') {
                $medical_category->unpublished_by = Auth::user()->id;
                $medical_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $medical_category->published = $published;
            $medical_category->lastedit_by = Auth::user()->id;
            $medical_category->lastedit_date = date("Y-m-d H:i:s");
            $path = ltrim($this->make_path($medical_category->parent_id,$id),':');
            $medical_category->path=$path;
            if ($medical_category->save()) {
                if(isset($data['course'])){
                    $courses=(array)$data['course'];
                    $pivotData = array_fill(0, count($courses), ['category_id' => $medical_category->id,'added_by'=>Auth::user()->id,'added_date'=>date("Y-m-d H:i:s")]);
                    $syncData  = array_combine($courses, $pivotData);
                    $medical_category->courses()->sync($syncData);

                }
                else{
                    $medical_category->courses()->detach();
                }
                if($old_url != $medical_category->url){
                    saveOldUrl($id,'articles',$old_url,$medical_category->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.medical_category'));
                return Redirect::to("admin/medical_categories/$medical_category->id/edit");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $medical_category = MedicalCategories::findOrFail($id);
        if (count($medical_category)) {
            $medical_category->delete();
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $medical_category = MedicalCategories::findOrFail($id);
            if ($published == 'no') {
                $medical_category->published = 'no';
                $medical_category->unpublished_by = Auth::user()->id;
                $medical_category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $medical_category->published = 'yes';
                $medical_category->published_by = Auth::user()->id;
                $medical_category->published_date = date("Y-m-d H:i:s");
            }
            $medical_category->save();
        } else {
            return redirect(404);
        }
    }

    public static function select_cat($cat_id,$selected=0,$y=null,$not_id=0){
        if($y===null){
            $y='&nbsp;&nbsp;';
        }
        $sections=MedicalCategories::where('parent_id',$cat_id)->get();
        foreach ($sections as $section) {
            if($section->id!=$not_id){
                echo'<option ';if($section->id==$selected||(is_array($section)&&in_array($section->id,$section))){echo'selected="selected"';}echo' value="'.$section->id.'">'.$y.'⁞.. '.$section->name.'</option>';
                if($section->parent_id!=0){
                    $y.='&nbsp;&nbsp;';
                    self::select_cat($section->id,$selected,$y,$not_id);
                }
                $y='&nbsp;&nbsp;';
            }
        }
    }
    private function make_path($parent_id,$current=null){
        $path='';
        if($current){
            $path=':c'.$current.":";
        }
        $section=MedicalCategories::where('id',$parent_id)->first();
        if($section){
            $path= $this->make_path($section->parent_id,null) . ':c' . $section->id . $path;
            return $path;
        }
        return $path;
    }
}
