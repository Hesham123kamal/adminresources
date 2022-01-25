<?php

namespace App\Http\Controllers\Admin;

use App\AllCategory;
use App\SubCategory;
use App\Successstories;
use App\SuccesstoryCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SuccesstoryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.successtories_categories.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $successtories_categories = SuccesstoryCategory::leftjoin('categories','categories.id','=','successtories_categories.category_id')
            ->leftjoin('sup_categories','sup_categories.id','=','successtories_categories.sup_category_id')
            ->leftjoin('successtories','successtories.id','=','successtories_categories.successtory_id')
            ->select('successtories_categories.*','categories.name as category_name','sup_categories.name as sub_category_name','successtories.name as successtory_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $successtories_categories = $successtories_categories->where('successtories_categories.id', '=', $id);
        }
        if (isset($data['category']) && !empty($data['category'])) {
            $category = $data['category'];
            $successtories_categories = $successtories_categories->where('categories.name','LIKE', "%$category%");
        }
        if (isset($data['sub_category']) && !empty($data['sub_category'])) {
            $sub_category = $data['sub_category'];
            $successtories_categories = $successtories_categories->where('sup_categories.name','LIKE', "%$sub_category%");
        }
        if (isset($data['successtory']) && !empty($data['successtory'])) {
            $successtory = $data['successtory'];
            $successtories_categories = $successtories_categories->where('successtories.name','LIKE', "%$successtory%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $successtories_categories = $successtories_categories->whereBetween('successtories_categories.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $successtories_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'successtories_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'successtories_categories.id';
                break;
            case 1:
                $columnName = 'categories.name';
                break;
            case 2:
                $columnName = 'sup_categories.name';
                break;
            case 3:
                $columnName = 'successtories.name';
                break;
            case 4:
                $columnName = 'successtories_categories.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $successtories_categories = $successtories_categories->where(function ($q) use ($search) {
                $q->where('successtories_categories.id', '=', $search)
                    ->orWhere('categories.name', 'LIKE', "%$search%")
                    ->orWhere('sup_categories.name', 'LIKE', "%$search%")
                    ->orWhere('successtories.name', 'LIKE', "%$search%");
            });
        }

        $successtories_categories = $successtories_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($successtories_categories as $successtory_category) {
            $category_name = $successtory_category->category_name;
            $sub_category_name = $successtory_category->sub_category_name;
            $successtory_name = $successtory_category->successtory_name;
            if(PerUser('all_categories_edit') && $category_name !=''){
                $category_name= '<a target="_blank" href="' . URL('admin/all_categories/' . $successtory_category->category_id . '/edit') . '">' . $category_name . '</a>';
            }
            if(PerUser('sub_categories_edit') && $sub_category_name !=''){
                $sub_category_name= '<a target="_blank" href="' . URL('admin/sub_categories/' . $successtory_category->sup_category_id . '/edit') . '">' . $sub_category_name . '</a>';
            }
            if(PerUser('successstories_edit') && $successtory_name !=''){
                $successtory_name= '<a target="_blank" href="' . URL('admin/successstories/' . $successtory_category->successtory_id . '/edit') . '">' . $successtory_name . '</a>';
            }
            $records["data"][] = [
                $successtory_category->id,
                $category_name,
                $sub_category_name,
                $successtory_name,
                $successtory_category->createtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $successtory_category->id . '" type="checkbox" ' . ((!PerUser('successtories_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('successtories_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($successtory_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $successtory_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $successtory_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('successtories_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/successtories_categories/' . $successtory_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('successtories_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $successtory_category->id . '" >
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
        $sub_categories = SubCategory::pluck('name', 'id');
        $successtories = Successstories::pluck('name', 'id');
        return view('auth.successtories_categories.add',compact('categories','sub_categories','successtories'));
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
                'category' =>'required|exists:mysql2.categories,id',
                'sub_category' =>'required|exists:mysql2.sup_categories,id',
                'successtory' =>'required|exists:mysql2.successtories,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $successtory_category = new SuccesstoryCategory();
            $successtory_category->published = $published;
            $successtory_category->category_id = $data['category'];
            $successtory_category->sup_category_id = $data['sub_category'];
            $successtory_category->successtory_id = $data['successtory'];
            $successtory_category->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $successtory_category->published_by = Auth::user()->id;
                $successtory_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $successtory_category->unpublished_by = Auth::user()->id;
                $successtory_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $successtory_category->lastedit_by = Auth::user()->id;
            $successtory_category->added_by = Auth::user()->id;
            $successtory_category->lastedit_date = date("Y-m-d H:i:s");
            $successtory_category->added_date = date("Y-m-d H:i:s");
            if ($successtory_category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.successtory_category'));
                return Redirect::to('admin/successtories_categories/create');
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
        $successtory_category = SuccesstoryCategory::findOrFail($id);
        $categories = AllCategory::pluck('name', 'id');
        $sub_categories = SubCategory::where('category_id','=',$successtory_category->category_id)->pluck('name', 'id');
        $successtories = Successstories::pluck('name', 'id');
        return view('auth.successtories_categories.edit', compact('successtory_category','categories','sub_categories','successtories'));
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
        $successtory_category = SuccesstoryCategory::findOrFail($id);
        $rules=array(
            'category' =>'required|exists:mysql2.categories,id',
            'sub_category' =>'required|exists:mysql2.sup_categories,id',
            'successtory' =>'required|exists:mysql2.successtories,id',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $successtory_category->category_id = $data['category'];
            $successtory_category->sup_category_id = $data['sub_category'];
            $successtory_category->successtory_id = $data['successtory'];
            if ($published == 'yes' && $successtory_category->published=='no') {
                $successtory_category->published_by = Auth::user()->id;
                $successtory_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $successtory_category->published=='yes') {
                $successtory_category->unpublished_by = Auth::user()->id;
                $successtory_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $successtory_category->published = $published;
            $successtory_category->lastedit_by = Auth::user()->id;
            $successtory_category->lastedit_date = date("Y-m-d H:i:s");
            if ($successtory_category->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.successtory_category'));
                return Redirect::to("admin/successtories_categories/$successtory_category->id/edit");
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
        $successtory_category = SuccesstoryCategory::findOrFail($id);
        $successtory_category->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $successtory_category = SuccesstoryCategory::findOrFail($id);
            if ($published == 'no') {
                $successtory_category->published = 'no';
                $successtory_category->unpublished_by = Auth::user()->id;
                $successtory_category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $successtory_category->published = 'yes';
                $successtory_category->published_by = Auth::user()->id;
                $successtory_category->published_date = date("Y-m-d H:i:s");
            }
            $successtory_category->save();
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
