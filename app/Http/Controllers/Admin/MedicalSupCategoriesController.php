<?php

namespace App\Http\Controllers\Admin;

use App\MedicalCategories;
use App\MedicalSupCategories;
use App\Courses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MedicalSupCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::where('show_on','=','medical')->pluck('name','id')->toArray();
        return view('auth.medical_sup_categories.view', compact('courses'));
    }


    function search(Request $request)
    {
        $data = $request->input();
        $medical_sup_categories = MedicalSupCategories::select('medical_sup_categories.*','medical_categories.name AS category_name','courses.id as c_id','courses.name as course_name','medical_sup_categories_courses.course_id')
            ->leftJoin('medical_categories','medical_categories.id','=','medical_sup_categories.category_id')
            ->leftJoin('medical_sup_categories_courses','medical_sup_categories_courses.sup_category_id','=','medical_sup_categories.id')
            ->leftJoin('courses','courses.id','=','medical_sup_categories_courses.course_id')->groupBy('medical_sup_categories.id');

        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $medical_sup_categories = $medical_sup_categories->where('medical_sup_categories.id', '=', "$id");
        }
        if (isset($data['category_name']) && !empty($data['category_name'])) {
            $category_name = $data['category_name'];
            $medical_sup_categories = $medical_sup_categories->where('medical_categories.name', 'LIKE', "%$category_name%");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $medical_sup_categories = $medical_sup_categories->where('medical_sup_categories.name', 'LIKE', "%$name%");
        }
        if (isset($data['price']) && !empty($data['price'])) {
            $price = $data['price'];
            $medical_sup_categories = $medical_sup_categories->where('medical_sup_categories.price', '=', "$price");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $medical_sup_categories = $medical_sup_categories->where('medical_sup_categories.url', 'LIKE', "%$url%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $medical_sup_categories = $medical_sup_categories->where('courses.id', '=', "$course");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $medical_sup_categories = $medical_sup_categories->whereBetween('medical_sup_categories.added_date', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        $iTotalRecords = $medical_sup_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'medical_sup_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'medical_sup_categories.id';
                break;
            case 1:
                $columnName = 'medical_categories.name';
                break;
            case 2:
                $columnName = 'medical_sup_categories.name';
                break;
            case 3:
                $columnName = 'medical_sup_categories.price';
                break;
            case 4:
                $columnName = 'articles.url';
                break;
            case 5:
                $columnName = 'medical_sup_categories.added_date';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $medical_sup_categories = $medical_sup_categories->where(function ($q) use ($search) {
                $q->where('medical_sup_categories.name', 'LIKE', "%$search%")
                    ->orWhere('medical_categories.name', 'LIKE', "%$search%")
                    ->orWhere('medical_sup_categories.price', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('medical_sup_categories.url', 'LIKE', "%$search%")
                    ->orWhere('medical_sup_categories.id', '=', $search);
            });
        }

        $medical_sup_categories = $medical_sup_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($medical_sup_categories as $medical_category) {
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
                $medical_category->category_name,
                $medical_category->name,
                $medical_category->price,
                $medical_category->url,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="100%" src="' . assetURL($medical_category->image) . '"/></a>',
                $courses_string,
                $medical_category->added_date,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $medical_category->id . '" type="checkbox" ' . ((!PerUser('medical_sup_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('medical_sup_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($medical_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $medical_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $medical_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('medical_sup_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/medical_sup_categories/' . $medical_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('medical_sup_categories_delete')) ? '<li>
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
        $medical_categories=MedicalCategories::where('published','yes')->get()->pluck('name', 'id');
        $courses = Courses::where('show_on','=','medical')->get()->pluck('name', 'id');
        return view('auth.medical_sup_categories.add', compact('courses','medical_categories'));
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
        $validator = Validator::make($request->all(),
            array(
                'category_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'short_description' => 'required',
                'price' => 'required|numeric',
                'url' => 'required|unique:mysql2.medical_sup_categories,url',
                'picture' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('picture');
            $picName = uploadFileToE3melbusiness($pic);
            $medical_category = new MedicalSupCategories();
            $medical_category->category_id = $data['category_id'];
            $medical_category->name = $data['name'];
            $medical_category->description = $data['description'];
            $medical_category->short_description = $data['short_description'];
            $medical_category->price = $data['price'];
            $medical_category->image = $picName;
            $medical_category->url = str_replace(' ','-',$data['url']);
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
                if(isset($data['course'])){
                    $courses=(array)$data['course'];
                    $pivotData = array_fill(0, count($courses), ['sup_category_id' => $medical_category->id,'category_id' => $medical_category->category_id,'added_by'=>Auth::user()->id,'added_date'=>date("Y-m-d H:i:s")]);
                    $syncData  = array_combine($courses, $pivotData);
                    $medical_category->courses()->sync($syncData);

                }
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.medical_sup_category'));
                return Redirect::to('admin/medical_sup_categories/create');
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
        $medical_category = MedicalSupCategories::findOrFail($id);
        $medical_category=makeDefaultImageGeneral($medical_category,'image');
        $courses = Courses::where('show_on','=','medical')->get()->pluck('name', 'id');
        $medical_categories=MedicalCategories::where('published','yes')->get()->pluck('name', 'id');

        return view('auth.medical_sup_categories.edit', compact('medical_category','courses','medical_categories'));
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
        $medical_category = MedicalSupCategories::findOrFail($id);
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $rules=array(
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'price' => 'required|numeric',
            'url' => "required|unique:mysql2.medical_sup_categories,url,$id,id",
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
            $medical_category->category_id = $data['category_id'];
            $medical_category->name = $data['name'];
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
            if ($medical_category->save()) {
                if(isset($data['course'])){
                    $courses=(array)$data['course'];
                    $pivotData = array_fill(0, count($courses), ['category_id' => $medical_category->id,'category_id' => $medical_category->category_id,'added_by'=>Auth::user()->id,'added_date'=>date("Y-m-d H:i:s")]);
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
                return Redirect::to("admin/medical_sup_categories/$medical_category->id/edit");
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
        $medical_category = MedicalSupCategories::findOrFail($id);
        if (count($medical_category)) {
            $medical_category->delete();
        }
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $medical_category = MedicalSupCategories::findOrFail($id);
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
}
