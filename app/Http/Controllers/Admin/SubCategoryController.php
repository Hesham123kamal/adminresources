<?php

namespace App\Http\Controllers\Admin;

use App\AllCategory;
use App\SubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.sub_categories.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $sub_categories = SubCategory::leftjoin('categories','categories.id','=','sup_categories.category_id')
                            ->select('sup_categories.*','categories.name as category_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $sub_categories = $sub_categories->where('sup_categories.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $sub_categories = $sub_categories->where('sup_categories.name', 'LIKE', "%$name%");
        }
        if (isset($data['category']) && !empty($data['category'])) {
            $category = $data['category'];
            $sub_categories = $sub_categories->where('categories.name', 'LIKE', "%$category%");
        }
//        if (isset($data['url']) && !empty($data['url'])) {
//            $url = $data['url'];
//            $categories = $categories->where('categories.url', 'LIKE', "%$url%");
//        }
//        if (isset($data['courses_count']) && !empty($data['courses_count'])) {
//            $courses_count = $data['courses_count'];
//            $categories = $categories->where('categories.courses_count', '=', $courses_count);
//        }
//        if (isset($data['webinar_count']) && !empty($data['webinar_count'])) {
//            $webinar_count = $data['webinar_count'];
//            $categories = $categories->where('categories.webinar_count', '=', $webinar_count);
//        }
//        if (isset($data['webinar_offline_count']) && !empty($data['webinar_offline_count'])) {
//            $webinar_offline_count = $data['webinar_offline_count'];
//            $categories = $categories->where('categories.webinar_offline_count', '=', $webinar_offline_count);
//        }
//        if (isset($data['successstories_count']) && !empty($data['successstories_count'])) {
//            $successstories_count = $data['successstories_count'];
//            $categories = $categories->where('categories.successstories_count', '=', $successstories_count);
//        }
//        if (isset($data['books_count']) && !empty($data['books_count'])) {
//            $books_count = $data['books_count'];
//            $categories = $categories->where('categories.books_count', '=', $books_count);
//        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $sub_categories = $sub_categories->whereBetween('sup_categories.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $sub_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'sup_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'sup_categories.id';
                break;
            case 1:
                $columnName = 'sup_categories.name';
                break;
            case 2:
                $columnName = 'categories.name';
                break;
//            case 3:
//                $columnName = 'categories.courses_count';
//                break;
//            case 4:
//                $columnName = 'categories.webinar_count';
//                break;
//            case 5:
//                $columnName = 'categories.webinar_offline_count';
//                break;
//            case 6:
//                $columnName = 'categories.successstories';
//                break;
//            case 7:
//                $columnName = 'categories.books_count';
//                break;
            case 3:
                $columnName = 'sup_categories.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $sub_categories = $sub_categories->where(function ($q) use ($search) {
                $q->where('sup_categories.name', 'LIKE', "%$search%")
                    ->orWhere('sup_categories.id', '=', $search)
                    ->orWhere('categories.name', 'LIKE', "%$search%");
            });
        }

        $sub_categories = $sub_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($sub_categories as $sub_category) {
            $sub_category=makeDefaultImageGeneral($sub_category,'image');
            $category_name = $sub_category->category_name;
            if(PerUser('all_categories_edit') && $category_name !=''){
                $category_name= '<a target="_blank" href="' . URL('admin/all_categories/' . $sub_category->category_id . '/edit') . '">' . $category_name . '</a>';
            }
            $records["data"][] = [
                $sub_category->id,
                $sub_category->name,
                $category_name,
//                $category->courses_count,
//                $category->webinar_count,
//                $category->webinar_offline_count,
//                $category->successstories_count,
//                $category->books_count,
                $sub_category->createtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($sub_category->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $sub_category->id . '" type="checkbox" ' . ((!PerUser('sub_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('sub_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($sub_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $sub_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $sub_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('sub_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/sub_categories/' . $sub_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('sub_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $sub_category->id . '" >
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
        $categories = AllCategory::pluck('name', 'id');
        return view('auth.sub_categories.add',compact('categories'));
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
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'category' =>'required|exists:mysql2.categories,id',
                'url' => 'required|unique:mysql2.categories,url',
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $sub_category = new SubCategory();
            $sub_category->name = $data['name'];
            $sub_category->url = str_replace(' ','-',$data['url']);
            $sub_category->description = $data['description'];
            $sub_category->published = $published;
            $sub_category->image = $picName;
            $sub_category->category_id = $data['category'];
            $sub_category->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $sub_category->published_by = Auth::user()->id;
                $sub_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $sub_category->unpublished_by = Auth::user()->id;
                $sub_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $sub_category->lastedit_by = Auth::user()->id;
            $sub_category->added_by = Auth::user()->id;
            $sub_category->lastedit_date = date("Y-m-d H:i:s");
            $sub_category->added_date = date("Y-m-d H:i:s");
            if ($sub_category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.sub_category'));
                return Redirect::to('admin/sub_categories/create');
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
        $sub_category = SubCategory::findOrFail($id);
        $categories = AllCategory::pluck('name', 'id');
        return view('auth.sub_categories.edit', compact('sub_category','categories'));
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
        $request->merge(array('url' => str_replace(' ', '-', $data['url'])));
        $sub_category = SubCategory::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'url' => "required|unique:mysql2.categories,url,$id,id",
            'description' => 'required',
            'category' =>'required|exists:mysql2.categories,id',
        );

        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            if ($request->file('image')) {
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $sub_category->image = $picName;
            }
            $sub_category->name = $data['name'];
            $old_url=$sub_category->url;
            $sub_category->url = str_replace(' ','-',$data['url']);
            $sub_category->description = $data['description'];
            $sub_category->category_id = $data['category'];
            if ($published == 'yes' && $sub_category->published=='no') {
                $sub_category->published_by = Auth::user()->id;
                $sub_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $sub_category->published=='yes') {
                $sub_category->unpublished_by = Auth::user()->id;
                $sub_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $sub_category->published = $published;
            $sub_category->lastedit_by = Auth::user()->id;
            $sub_category->lastedit_date = date("Y-m-d H:i:s");
            if ($sub_category->save()){
                if($old_url != $sub_category->url){
                    saveOldUrl($id,'sup_categories',$old_url,$sub_category->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.sub_category'));
                return Redirect::to("admin/sub_categories/$sub_category->id/edit");
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
        $sub_category = SubCategory::findOrFail($id);
        $sub_category->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $sub_category = SubCategory::findOrFail($id);
            if ($published == 'no') {
                $sub_category->published = 'no';
                $sub_category->unpublished_by = Auth::user()->id;
                $sub_category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $sub_category->published = 'yes';
                $sub_category->published_by = Auth::user()->id;
                $sub_category->published_date = date("Y-m-d H:i:s");
            }
            $sub_category->save();
        } else {
            return redirect(404);
        }
    }
}
