<?php

namespace App\Http\Controllers\Admin;

use App\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.categories.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $categories = Categories::select('articles_category.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $categories = $categories->where('articles_category.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $categories = $categories->where('articles_category.name', 'LIKE', "%$name%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $picpath = $data['pic'];
            $categories = $categories->where('articles_category.picpath', 'LIKE', "%$picpath%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $categories = $categories->where('articles_category.url', 'LIKE', "%$url%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $categories = $categories->whereBetween('articles_category.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
                $columnName = 'articles_category.id';
                break;
            case 1:
                $columnName = 'articles_category.name';
                break;
            case 2:
                $columnName = 'articles_category.picpath';
                break;
            case 3:
                $columnName = 'articles_category.url';
                break;
            case 4:
                $columnName = 'articles_category.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $categories = $categories->where(function ($q) use ($search) {
                $q->where('articles_category.name', 'LIKE', "%$search%")
                    ->orWhere('articles_category.picpath', 'LIKE', "%$search%")
                    ->orWhere('articles_category.url', 'LIKE', "%$search%")
                    ->orWhere('articles_category.id', '=', $search);
            });
        }

        $categories = $categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($categories as $category) {
            $category=makeDefaultImageGeneral($category,'picpath');
            $records["data"][] = [
                $category->id,
                $category->name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img style="width:50%; " src="' . assetURL($category->picpath) . '"></a>',
                '<a href="' . e3mURL('blog/category/' .$category->url) . '" target="_blank">' . $category->url . '</a>',
                $category->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $category->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('categories_edit')) ? '<li>
                                            <a href="' . URL('admin/categories/' . $category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('categories_delete')) ? '<li>
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
        return view('auth.categories.add');
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
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'pic' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'url' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $categories = new Categories();
            $categories->name = $data['name'];
            $categories->picpath = $picName;
            $categories->url = str_replace(' ','-',$data['url']);
            $categories->published = $active;
            if ($active == 'yes') {
                $categories->published_by = Auth::user()->id;
                $categories->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $categories->unpublished_by = Auth::user()->id;
                $categories->unpublished_date = date("Y-m-d H:i:s");
            }
            $categories->added_by = Auth::user()->id;
            $categories->added_date = date("Y-m-d H:i:s");
            if ($categories->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.categories'));
                return Redirect::to('admin/categories/create');
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
        $category = Categories::find($id);
        return view('auth.categories.edit',compact('category'));
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
        $category = Categories::find($id);
        $rules= array(
            'name' => 'required',
            'url' => 'required',
        );
        if ( $request->file('pic')){
            $rules['pic']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            if ( $request->file('pic')){
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $category->picpath = $picName;
            }
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $category->name = $data['name'];
            $old_url=$category->url;
            $category->url = str_replace(' ','-',$data['url']);
            $category->published = $active;
            if ($active == 'yes') {
                $category->published_by = Auth::user()->id;
                $category->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $category->unpublished_by = Auth::user()->id;
                $category->unpublished_date = date("Y-m-d H:i:s");
            }
            $category->lastedit_by = Auth::user()->id;
            $category->lastedit_date = date("Y-m-d H:i:s");
            if ($category->save()) {
                if($old_url != $category->url){
                    saveOldUrl($id,'articles_category',$old_url,$category->url,Auth::user()->id,date("Y-m-d H:i:s"));
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.categories'));
                return Redirect::to("admin/categories/$category->id/edit");
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
        $category = Categories::find($id);
        if (count($category)) {
            $category->delete();
//            $category->deleted_by = Auth::user()->id;
//            $category->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $category = Categories::find($id);
            if ($active == 'no') {
                $category->published = 'no';
                $category->unpublished_by = Auth::user()->id;
                $category->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $category->published = 'yes';
                $category->published_by = Auth::user()->id;
                $category->published_date = date("Y-m-d H:i:s");
            }
            if($category->save()){
            }
        } else {
            return redirect(404);
        }
    }
}
