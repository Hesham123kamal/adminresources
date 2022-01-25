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

class AllCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.all_categories.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $categories = AllCategory::select('categories.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $categories = $categories->where('categories.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $categories = $categories->where('categories.name', 'LIKE', "%$name%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $categories = $categories->where('categories.url', 'LIKE', "%$url%");
        }
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
            $categories = $categories->whereBetween('categories.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'categories.id';
                break;
            case 1:
                $columnName = 'categories.name';
                break;
            case 2:
                $columnName = 'categories.url';
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
                $columnName = 'categories.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $categories = $categories->where(function ($q) use ($search) {
                $q->where('categories.name', 'LIKE', "%$search%")
                    ->orWhere('categories.url', 'LIKE', "%$search%")
                    ->orWhere('categories.id', '=', $search);
            });
        }

        $categories = $categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($categories as $category) {
            $category=makeDefaultImageGeneral($category,'image');
            $records["data"][] = [
                $category->id,
                $category->name,
                '<a href="' . e3mURL('categories/' . $category->url) . '" target="_blank">' . $category->url . '</a>',
//                $category->courses_count,
//                $category->webinar_count,
//                $category->webinar_offline_count,
//                $category->successstories_count,
//                $category->books_count,
                $category->createtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($category->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $category->id . '" type="checkbox" ' . ((!PerUser('all_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('all_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('all_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/all_categories/' . $category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('all_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $category->id . '" >
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
        return view('auth.all_categories.add');
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
                'url' => 'required|unique:mysql2.categories,url',
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'sort' => 'required|numeric',
                'description' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $category = new AllCategory();
            $category->name = $data['name'];
            $category->url = str_replace(' ','-',$data['url']);
            $category->sort = $data['sort'];
            $category->description = $data['description'];
            $category->published = $published;
            $category->image = $picName;
            $category->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $category->published_by = Auth::user()->id;
                $category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $category->unpublished_by = Auth::user()->id;
                $category->unpublished_date = date("Y-m-d H:i:s");
            }
            $category->lastedit_by = Auth::user()->id;
            $category->added_by = Auth::user()->id;
            $category->added_date = date("Y-m-d H:i:s");
            $category->lastedit_date = date("Y-m-d H:i:s");
            if ($category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.all_category'));
                return Redirect::to('admin/all_categories/create');
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
        $category = AllCategory::findOrFail($id);
        return view('auth.all_categories.edit', compact('category'));
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
        $category = AllCategory::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'url' => "required|unique:mysql2.categories,url,$id,id",
            'sort' => 'required|numeric',
            'description' => 'required',
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
                $category->image = $picName;
            }
            $category->name = $data['name'];
            $old_url=$category->url;
            $category->url = str_replace(' ','-',$data['url']);
            $category->sort = $data['sort'];
            $category->description = $data['description'];
            if ($published == 'yes' && $category->published=='no') {
                $category->published_by = Auth::user()->id;
                $category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $category->published=='yes') {
                $category->unpublished_by = Auth::user()->id;
                $category->unpublished_date = date("Y-m-d H:i:s");
            }
            $category->published = $published;
            $category->lastedit_by = Auth::user()->id;
            $category->lastedit_date = date("Y-m-d H:i:s");
            if ($category->save()){
                if($old_url != $category->url){
                    saveOldUrl($id,'categories',$old_url,$category->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.all_category'));
                return Redirect::to("admin/all_categories/$category->id/edit");
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
        $category = AllCategory::findOrFail($id);
        $category->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $category = AllCategory::findOrFail($id);
            if ($published == 'no') {
                $category->published = 'no';
                $category->unpublished_by = Auth::user()->id;
                $category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $category->published = 'yes';
                $category->published_by = Auth::user()->id;
                $category->published_date = date("Y-m-d H:i:s");
            }
            $category->save();
        } else {
            return redirect(404);
        }
    }

    public function getSubCategoriesByCategoryId(Request $request){
        $category=AllCategory::findOrFail($request->input('category_id'));
        $sub_categories=SubCategory::where('category_id','=',$category->id)->get();
        if($sub_categories!==null){
            $options='';
            foreach ($sub_categories as $sub_category) {
                $options.='<option value="'.$sub_category->id.'">'.$sub_category->name.'</option>';
            }
            return $options;
        }
    }
}
