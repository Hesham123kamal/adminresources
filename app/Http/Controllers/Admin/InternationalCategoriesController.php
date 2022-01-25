<?php

namespace App\Http\Controllers\Admin;

use App\InternationalCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class InternationalCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.international_categories.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $categories = InternationalCategories::select('international_categories.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $categories = $categories->where('international_categories.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $categories = $categories->where('international_categories.name', 'LIKE', "%$name%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $categories = $categories->whereBetween('international_categories.added_date', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'international_categories.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'international_categories.id';
                break;
            case 1:
                $columnName = 'international_categories.name';
                break;
            case 2:
                $columnName = 'international_categories.added_date';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $categories = $categories->where(function ($q) use ($search) {
                $q->where('international_categories.name', 'LIKE', "%$search%")
                    ->orWhere('international_categories.id', '=', $search);
            });
        }

        $categories = $categories->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($categories as $category) {
            $records["data"][] = [
                $category->id,
                $category->name,
                $category->added_date,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $category->id . '" type="checkbox" ' . ((!PerUser('international_categories_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('international_categories_publish')) ? 'class="changeStatues"' : '') . ' ' . (($category->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $category->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $category->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('international_categories_edit')) ? '<li>
                                            <a href="' . URL('admin/international_categories/' . $category->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('international_categories_delete')) ? '<li>
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
        return view('auth.international_categories.add');
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
                'description'=>'required',
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'banner' => 'mimes:jpeg,jpg,png,gif|required|max:5000',

            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $image = $request->file('image');
            $picName = uploadFileToE3melbusiness($image);
            $banner = $request->file('banner');
            $bannerName = uploadFileToE3melbusiness($banner);
            $category = new InternationalCategories();
            $category->name = $data['name'];
            $category->description = $data['description'];
            $category->image = $picName;
            $category->banner = $bannerName;
            $category->published = $published;
            $category->added_date = date("Y-m-d H:i:s");
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
            $category->lastedit_date = date("Y-m-d H:i:s");
            if ($category->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.international_categories'));
                return Redirect::to('admin/international_categories/create');
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
        $category = InternationalCategories::findOrFail($id);
        return view('auth.international_categories.edit', compact('category'));
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
        $category = InternationalCategories::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'description'=>'required'

        );
        if ( $request->file('image')){
            $rules['image']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        if ( $request->file('banner')){
            $rules['banner']='mimes:jpeg,jpg,png,gif|required|max:5000';
        }


        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {

            $published = (isset($data['published'])) ? 'yes' : 'no';
            $category->name = $data['name'];
            $category->description = $data['description'];
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $category->image = $picName;
            }
            if ( $request->file('banner')){
                $banner = $request->file('banner');
                $bannerName = uploadFileToE3melbusiness($banner);
                $category->banner = $bannerName;
            }
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
                Session::flash('success', Lang::get('main.update') . Lang::get('main.international_categories'));
                return Redirect::to("admin/international_categories/$category->id/edit");
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
        $category = InternationalCategories::findOrFail($id);
        $category->deleted_by=Auth::user()->id;
        $category->deleted_at=date("Y-m-d H:i:s");
        $category->save();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $category = InternationalCategories::findOrFail($id);
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
}
