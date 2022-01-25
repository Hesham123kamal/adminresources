<?php

namespace App\Http\Controllers\Admin;

use App\AllCategory;
use App\SubCategory;
use App\Webinars;
use App\WebinarCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class WebinarCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.webinars_categories.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $webinars_categories = WebinarCategory::leftjoin('categories','categories.id','=','webinars_categories.category_id')
            ->leftjoin('sup_categories','sup_categories.id','=','webinars_categories.sup_category_id')
            ->leftjoin('webinar','webinar.id','=','webinars_categories.webinar_id')
            ->select('webinars_categories.*','categories.name as category_name','sup_categories.name as sub_category_name','webinar.name as webinar_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $webinars_categories = $webinars_categories->where('webinars_categories.id', '=', $id);
        }
        if (isset($data['category']) && !empty($data['category'])) {
            $category = $data['category'];
            $webinars_categories = $webinars_categories->where('categories.name','LIKE', "%$category%");
        }
        if (isset($data['sub_category']) && !empty($data['sub_category'])) {
            $sub_category = $data['sub_category'];
            $webinars_categories = $webinars_categories->where('sup_categories.name','LIKE', "%$sub_category%");
        }
        if (isset($data['webinar']) && !empty($data['webinar'])) {
            $webinar = $data['webinar'];
            $webinars_categories = $webinars_categories->where('webinar.name','LIKE', "%$webinar%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $webinars_categories = $webinars_categories->whereBetween('webinars_categories.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $webinars_categories->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'webinars_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'webinars_categories.id';
                break;
            case 1:
                $columnName = 'categories.name';
                break;
            case 2:
                $columnName = 'sup_categories.name';
                break;
            case 3:
                $columnName = 'webinar.name';
                break;
            case 4:
                $columnName = 'webinars_categories.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $webinars_categories = $webinars_categories->where(function ($q) use ($search) {
                $q->where('webinars_categories.id', '=', $search)
                    ->orWhere('categories.name', 'LIKE', "%$search%")
                    ->orWhere('sup_categories.name', 'LIKE', "%$search%")
                    ->orWhere('webinar.name', 'LIKE', "%$search%");
            });
        }

        $webinars_categories = $webinars_categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($webinars_categories as $webinar_category) {
            $category_name = $webinar_category->category_name;
            $sub_category_name = $webinar_category->sub_category_name;
            $webinar_name = $webinar_category->webinar_name;
            if(PerUser('all_categories_edit') && $category_name !=''){
                $category_name= '<a target="_blank" href="' . URL('admin/all_categories/' . $webinar_category->category_id . '/edit') . '">' . $category_name . '</a>';
            }
            if(PerUser('sub_categories_edit') && $sub_category_name !=''){
                $sub_category_name= '<a target="_blank" href="' . URL('admin/sub_categories/' . $webinar_category->sup_category_id . '/edit') . '">' . $sub_category_name . '</a>';
            }
            if(PerUser('webinars_edit') && $webinar_name !=''){
                $webinar_name= '<a target="_blank" href="' . URL('admin/webinars/' . $webinar_category->webinar_id . '/edit') . '">' . $webinar_name . '</a>';
            }
            $records["data"][] = [
                $webinar_category->id,
                $category_name,
                $sub_category_name,
                $webinar_name,
                $webinar_category->createtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $webinar_category->id . '" type="checkbox" ' . ((!PerUser('webinars_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('webinars_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($webinar_category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $webinar_category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $webinar_category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('webinars_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/webinars_categories/' . $webinar_category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('webinars_categories_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $webinar_category->id . '" >
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
        $webinars = Webinars::pluck('name', 'id');
        return view('auth.webinars_categories.add',compact('categories','sub_categories','webinars'));
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
                'webinar' =>'required|exists:mysql2.webinar,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $webinar_category = new WebinarCategory();
            $webinar_category->published = $published;
            $webinar_category->category_id = $data['category'];
            $webinar_category->sup_category_id = $data['sub_category'];
            $webinar_category->webinar_id = $data['webinar'];
            $webinar_category->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $webinar_category->published_by = Auth::user()->id;
                $webinar_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $webinar_category->unpublished_by = Auth::user()->id;
                $webinar_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $webinar_category->lastedit_by = Auth::user()->id;
            $webinar_category->added_by = Auth::user()->id;
            $webinar_category->lastedit_date = date("Y-m-d H:i:s");
            $webinar_category->added_date = date("Y-m-d H:i:s");
            if ($webinar_category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.webinar_category'));
                return Redirect::to('admin/webinars_categories/create');
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
        $webinar_category = WebinarCategory::findOrFail($id);
        $categories = AllCategory::pluck('name', 'id');
        $sub_categories = SubCategory::where('category_id','=',$webinar_category->category_id)->pluck('name', 'id');
        $webinars = Webinars::pluck('name', 'id');
        return view('auth.webinars_categories.edit', compact('webinar_category','categories','sub_categories','webinars'));
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
        $webinar_category = WebinarCategory::findOrFail($id);
        $rules=array(
            'category' =>'required|exists:mysql2.categories,id',
            'sub_category' =>'required|exists:mysql2.sup_categories,id',
            'webinar' =>'required|exists:mysql2.webinar,id',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $webinar_category->category_id = $data['category'];
            $webinar_category->sup_category_id = $data['sub_category'];
            $webinar_category->webinar_id = $data['webinar'];
            if ($published == 'yes' && $webinar_category->published=='no') {
                $webinar_category->published_by = Auth::user()->id;
                $webinar_category->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $webinar_category->published=='yes') {
                $webinar_category->unpublished_by = Auth::user()->id;
                $webinar_category->unpublished_date = date("Y-m-d H:i:s");
            }
            $webinar_category->published = $published;
            $webinar_category->lastedit_by = Auth::user()->id;
            $webinar_category->lastedit_date = date("Y-m-d H:i:s");
            if ($webinar_category->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.webinar_category'));
                return Redirect::to("admin/webinars_categories/$webinar_category->id/edit");
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
        $webinar_category = WebinarCategory::findOrFail($id);
        $webinar_category->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $webinar_category = WebinarCategory::findOrFail($id);
            if ($published == 'no') {
                $webinar_category->published = 'no';
                $webinar_category->unpublished_by = Auth::user()->id;
                $webinar_category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $webinar_category->published = 'yes';
                $webinar_category->published_by = Auth::user()->id;
                $webinar_category->published_date = date("Y-m-d H:i:s");
            }
            $webinar_category->save();
        } else {
            return redirect(404);
        }
    }

}
