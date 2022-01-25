<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\OurPartnerFlags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OurPartnerFlagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.our_partner_flags.view');
    }


    function search(Request $request)
    {
        $data = $request->input();
        $partners_flags = OurPartnerFlags::select('our_partener_flags.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $partners_flags = $partners_flags->where('id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $partners_flags = $partners_flags->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $partners_flags = $partners_flags->whereBetween('createdtime', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $partners_flags->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'id';
                break;
            case 1:
                $columnName = 'name';
                break;
            case 2:
                $columnName = 'createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $partners_flags = $partners_flags->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $partners_flags = $partners_flags->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($partners_flags as $flag) {
//            $flag=makeDefaultImageGeneral($flag,'image','ourpartners/');
            $records["data"][] = [
                $flag->id,
                $flag->name,
                $flag->createdtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="40%" src="' . assetURL('ourpartners/' . $flag->image) . '"title="' . $flag->name . '" alt="' . $flag->name . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $flag->id . '" type="checkbox" ' . ((!PerUser('our_partner_flags_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('our_partner_flags_publish')) ? 'class="changeStatues"' : '') . ' ' . (($flag->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $flag->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $flag->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('our_partner_flags_edit')) ? '<li>
                                            <a href="' . URL('admin/our_partner_flags/' . $flag->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('our_partner_flags_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $flag->id . '" >
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
        return view('auth.our_partner_flags.add');
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
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000'
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic, true);
            $partner_flag = new OurPartnerFlags();
            $partner_flag->name = $data['name'];
            $partner_flag->published = $published;
            $partner_flag->image = $picName;
            if ($published == 'yes') {
                $partner_flag->published_by = Auth::user()->id;
                $partner_flag->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $partner_flag->unpublished_by = Auth::user()->id;
                $partner_flag->unpublished_date = date("Y-m-d H:i:s");
            }
            $partner_flag->lastedit_by = Auth::user()->id;
            $partner_flag->added_by = Auth::user()->id;
            $partner_flag->lastedit_date = date("Y-m-d H:i:s");
            $partner_flag->createdtime = date("Y-m-d H:i:s");
            $partner_flag->added_date = date("Y-m-d H:i:s");
            if ($partner_flag->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.our_partner_flags'));
                return Redirect::to('admin/our_partner_flags/create');
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
        $flag = OurPartnerFlags::findOrFail($id);
        return view('auth.our_partner_flags.edit', compact('flag'));
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
        $flag = OurPartnerFlags::findOrFail($id);
        $rules = array(
            'name' => 'required',
        );
        if ($request->file('image')) {
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $flag->name = $data['name'];
            if ($request->file('image')) {
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic, true);
                $flag->image = $picName;
            }
            if ($published == 'yes' && $flag->published == 'no') {
                $flag->published_by = Auth::user()->id;
                $flag->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $flag->published == 'yes') {
                $flag->unpublished_by = Auth::user()->id;
                $flag->unpublished_date = date("Y-m-d H:i:s");
            }
            $flag->published = $published;
            $flag->lastedit_by = Auth::user()->id;
            $flag->lastedit_date = date("Y-m-d H:i:s");
            if ($flag->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.our_partner_flags'));
                return Redirect::to("admin/our_partner_flags/$flag->id/edit");
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
        $flag = OurPartnerFlags::findOrFail($id);
        $flag->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $flag = OurPartnerFlags::findOrFail($id);
            if ($published == 'no') {
                $flag->published = 'no';
                $flag->unpublished_by = Auth::user()->id;
                $flag->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $flag->published = 'yes';
                $flag->published_by = Auth::user()->id;
                $flag->published_date = date("Y-m-d H:i:s");
            }
            $flag->save();
        } else {
            return redirect(404);
        }
    }
}
