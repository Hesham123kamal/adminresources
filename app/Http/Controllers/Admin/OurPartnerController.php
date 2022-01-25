<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\OurPartner;
use App\OurPartnerFlags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class  OurPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.our_partner.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $partners = OurPartner::select('our_partener.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $partners = $partners->where('id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $partners = $partners->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $partners = $partners->where('country', 'LIKE', "%$country%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $partners = $partners->where('phone', 'LIKE', "%$phone%");
        }
        if (isset($data['responsible_name']) && !empty($data['responsible_name'])) {
            $responsible_name = $data['responsible_name'];
            $partners = $partners->where('responsible_name', 'LIKE', "%$responsible_name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $partners = $partners->whereBetween('createdtime', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $partners->count();
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
            case 3:
                $columnName = 'country';
                break;
            case 4:
                $columnName = 'phone';
                break;
            case 5:
                $columnName = 'responsible_name';
                break;
            case 6:
                $columnName = 'createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $partners = $partners->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('phone', 'LIKE', "%$search%")
                    ->orWhere('responsible_name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $partners = $partners->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->with('flag')
            ->get();

        foreach ($partners as $partner) {
//            $partner=makeDefaultImageGeneral($partner,'image','ourpartners/');
            $flag_path=(isset($partner->flag))?$partner->flag->image:'';
            $records["data"][] = [
                $partner->id,
                $partner->name,
                $partner->country,
                (($partner->flag)?'<img width="50%" src="' . assetURL('ourpartners/' . $flag_path) . '" title="' . $partner->flag->name . '" alt="' . $partner->flag->name . '"/>':''),
                $partner->phone,
                $partner->responsible_name,
                $partner->createdtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL('ourpartners/' . $partner->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $partner->id . '" type="checkbox" ' . ((!PerUser('our_partner_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('our_partner_publish')) ? 'class="changeStatues"' : '') . ' ' . (($partner->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $partner->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $partner->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('our_partner_edit')) ? '<li>
                                            <a href="' . URL('admin/our_partner/' . $partner->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('our_partner_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $partner->id . '" >
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
        $flags = OurPartnerFlags::get();
        return view('auth.our_partner.add', compact('flags'));
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
                'title' => 'required',
                'flag_id' => 'required',
                'name' => 'required',
                'country' => 'required',
                'phone' => 'required',
                'responsible_name' => 'required',
//                'support_phone' => 'required',
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000'
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic, true);
            $partner = new OurPartner();
            $partner->name = $data['name'];
            $partner->flag_id = $data['flag_id'];
            $partner->title = $data['title'];
            $partner->country = $data['country'];
            $partner->phone = $data['phone'];
            $partner->responsible_name = $data['responsible_name'];
            $partner->support_phone = $data['support_phone'];
            $partner->published = $published;
            $partner->image = $picName;
            if ($published == 'yes') {
                $partner->published_by = Auth::user()->id;
                $partner->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $partner->unpublished_by = Auth::user()->id;
                $partner->unpublished_date = date("Y-m-d H:i:s");
            }
            $partner->lastedit_by = Auth::user()->id;
            $partner->added_by = Auth::user()->id;
            $partner->lastedit_date = date("Y-m-d H:i:s");
            $partner->added_date = date("Y-m-d H:i:s");
            $partner->createdtime = date("Y-m-d H:i:s");
            if ($partner->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.our_partner'));
                return Redirect::to('admin/our_partner/create');
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
        $partner = OurPartner::findOrFail($id);
        $flags = OurPartnerFlags::get();
        return view('auth.our_partner.edit', compact('partner', 'flags'));
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
        $partner = OurPartner::findOrFail($id);
        $rules = array(
            'title' => 'required',
            'flag_id' => 'required',
            'name' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'responsible_name' => 'required',
//            'support_phone' => 'required',
        );
        if ($request->file('image')) {
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $partner->name = $data['name'];
            $partner->flag_id = $data['flag_id'];
            $partner->title = $data['title'];
            $partner->country = $data['country'];
            $partner->phone = $data['phone'];
            $partner->responsible_name = $data['responsible_name'];
            $partner->support_phone = $data['support_phone'];
            if ($request->file('image')) {
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic, true);
                $partner->image = $picName;
            }
            if ($published == 'yes' && $partner->published == 'no') {
                $partner->published_by = Auth::user()->id;
                $partner->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $partner->published == 'yes') {
                $partner->unpublished_by = Auth::user()->id;
                $partner->unpublished_date = date("Y-m-d H:i:s");
            }
            $partner->published = $published;
            $partner->lastedit_by = Auth::user()->id;
            $partner->lastedit_date = date("Y-m-d H:i:s");
            if ($partner->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.our_partner'));
                return Redirect::to("admin/our_partner/$partner->id/edit");
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
        $partner = OurPartner::findOrFail($id);
        $partner->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $partner = OurPartner::findOrFail($id);
            if ($published == 'no') {
                $partner->published = 'no';
                $partner->unpublished_by = Auth::user()->id;
                $partner->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $partner->published = 'yes';
                $partner->published_by = Auth::user()->id;
                $partner->published_date = date("Y-m-d H:i:s");
            }
            $partner->save();
        } else {
            return redirect(404);
        }
    }
}
