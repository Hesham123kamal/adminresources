<?php

namespace App\Http\Controllers\Admin;

use App\PartnerRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class PartnerRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.partner_requests.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $partner_requests = PartnerRequest::select('our_partener_request.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $partner_requests = $partner_requests->where('our_partener_request.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $partner_requests = $partner_requests->where('our_partener_request.name', 'LIKE', "%$name%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $partner_requests = $partner_requests->where('our_partener_request.phone', 'LIKE', "%$phone%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $partner_requests = $partner_requests->whereBetween('our_partener_request.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $partner_requests->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'our_partener_request.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'our_partener_request.id';
                break;
            case 1:
                $columnName = 'our_partener_request.name';
                break;
            case 2:
                $columnName = 'our_partener_request.phone';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $partner_requests = $partner_requests->where(function ($q) use ($search) {
                $q->where('our_partener_request.name', 'LIKE', "%$search%")
                    ->orWhere('our_partener_request.phone', 'LIKE', "%$search%")
                    ->orWhere('our_partener_request.id', '=', $search);
            });
        }

        $partner_requests = $partner_requests->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($partner_requests as $partner_request) {
            $records["data"][] = [
                $partner_request->id,
                $partner_request->name,
                $partner_request->phone,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $partner_request->id . '" type="checkbox" ' . ((!PerUser('partner_requests_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('partner_requests_publish')) ? 'class="changeStatues"' : '') . ' ' . (($partner_request->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $partner_request->id . '">
                                    </label>
                                </div>
                            </td>',
                $partner_request->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $partner_request->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('partner_requests_edit')) ? '<li>
                                            <a href="' . URL('admin/partner_requests/' . $partner_request->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('partner_requests_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $partner_request->id . '" >
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
        return view('auth.partner_requests.add');
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
                'phone' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $partner_request = new PartnerRequest();
            $partner_request->name = $data['name'];
            $partner_request->phone = $data['phone'];
            $partner_request->published = $published;
            if ($published == 'yes') {
                $partner_request->published_by = Auth::user()->id;
                $partner_request->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $partner_request->unpublished_by = Auth::user()->id;
                $partner_request->unpublished_date = date("Y-m-d H:i:s");
            }
            $partner_request->lastedit_by = Auth::user()->id;
            $partner_request->added_by = Auth::user()->id;
            $partner_request->lastedit_date = date("Y-m-d H:i:s");
            $partner_request->createdtime = date("Y-m-d H:i:s");
            $partner_request->added_date = date("Y-m-d H:i:s");
            if ($partner_request->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.partner_request'));
                return Redirect::to('admin/partner_requests/create');
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
        $partner_request = PartnerRequest::findOrFail($id);
        return view('auth.partner_requests.edit', compact('partner_request'));
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
        $partner_request = PartnerRequest::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'phone' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $partner_request->name = $data['name'];
            $partner_request->phone = $data['phone'];
            if ($published == 'yes' && $partner_request->published=='no') {
                $partner_request->published_by = Auth::user()->id;
                $partner_request->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $partner_request->published=='yes') {
                $partner_request->unpublished_by = Auth::user()->id;
                $partner_request->unpublished_date = date("Y-m-d H:i:s");
            }
            $partner_request->published = $published;
            $partner_request->lastedit_by = Auth::user()->id;
            $partner_request->lastedit_date = date("Y-m-d H:i:s");
            if ($partner_request->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.partner_requests'));
                return Redirect::to("admin/partner_requests/$partner_request->id/edit");
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
        $partner_request = PartnerRequest::findOrFail($id);
        $partner_request->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $partner_request = PartnerRequest::findOrFail($id);
            if ($published == 'no') {
                $partner_request->published = 'no';
                $partner_request->unpublished_by = Auth::user()->id;
                $partner_request->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $partner_request->published = 'yes';
                $partner_request->published_by = Auth::user()->id;
                $partner_request->published_date = date("Y-m-d H:i:s");
            }
            $partner_request->save();
        } else {
            return redirect(404);
        }
    }
}
