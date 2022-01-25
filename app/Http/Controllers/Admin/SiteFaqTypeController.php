<?php

namespace App\Http\Controllers\Admin;

use App\SiteFaqType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SiteFaqTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.site_faq_type.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $types = SiteFaqType::select('site_faq_type.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $types = $types->where('site_faq_type.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $types = $types->where('site_faq_type.name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $types = $types->whereBetween('site_faq_type.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $types->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'site_faq_type.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'site_faq_type.id';
                break;
            case 1:
                $columnName = 'site_faq_type.name';
                break;
            case 2:
                $columnName = 'site_faq_type.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $types = $types->where(function ($q) use ($search) {
                $q->where('site_faq_type.name', 'LIKE', "%$search%")
                    ->orWhere('site_faq_type.id', '=', $search);
            });
        }

        $types = $types->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($types as $type) {
            $records["data"][] = [
                $type->id,
                $type->name,
                $type->createdtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img alt="'.$type->name.'" width="50%" src="' . assetURL($type->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $type->id . '" type="checkbox" ' . ((!PerUser('site_faq_type_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('site_faq_type_publish')) ? 'class="changeStatues"' : '') . ' ' . (($type->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $type->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $type->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('site_faq_type_edit')) ? '<li>
                                            <a href="' . URL('admin/site_faq_type/' . $type->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('site_faq_type_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $type->id . '" >
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
        return view('auth.site_faq_type.add');
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
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $site_faq_type = new SiteFaqType();
            $site_faq_type->name = $data['name'];
            $site_faq_type->published = $published;
            $site_faq_type->image = $picName;
            $site_faq_type->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $site_faq_type->published_by = Auth::user()->id;
                $site_faq_type->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $site_faq_type->unpublished_by = Auth::user()->id;
                $site_faq_type->unpublished_date = date("Y-m-d H:i:s");
            }
            $site_faq_type->lastedit_by = Auth::user()->id;
            $site_faq_type->added_by = Auth::user()->id;
            $site_faq_type->lastedit_date = date("Y-m-d H:i:s");
            if ($site_faq_type->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.site_faq_type'));
                return Redirect::to('admin/site_faq_type/create');
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
        $site_faq_type = SiteFaqType::findOrFail($id);
        return view('auth.site_faq_type.edit', compact('site_faq_type'));
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
        $site_faq_type = SiteFaqType::findOrFail($id);
        $rules=array(
            'name' => 'required',
        );

        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $site_faq_type->name = $data['name'];
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $site_faq_type->image = $picName;
            }
            if ($published == 'yes' && $site_faq_type->published=='no') {
                $site_faq_type->published_by = Auth::user()->id;
                $site_faq_type->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $site_faq_type->published=='yes') {
                $site_faq_type->unpublished_by = Auth::user()->id;
                $site_faq_type->unpublished_date = date("Y-m-d H:i:s");
            }
            $site_faq_type->published = $published;
            $site_faq_type->lastedit_by = Auth::user()->id;
            $site_faq_type->lastedit_date = date("Y-m-d H:i:s");
            if ($site_faq_type->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.site_faq_type'));
                return Redirect::to("admin/site_faq_type/$site_faq_type->id/edit");
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
        $site_faq_type = SiteFaqType::findOrFail($id);
        $site_faq_type->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $site_faq_type = SiteFaqType::findOrFail($id);
            if ($published == 'no') {
                $site_faq_type->published = 'no';
                $site_faq_type->unpublished_by = Auth::user()->id;
                $site_faq_type->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $site_faq_type->published = 'yes';
                $site_faq_type->published_by = Auth::user()->id;
                $site_faq_type->published_date = date("Y-m-d H:i:s");
            }
            $site_faq_type->save();
        } else {
            return redirect(404);
        }
    }
}
