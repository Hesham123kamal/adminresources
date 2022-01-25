<?php

namespace App\Http\Controllers\Admin;

use App\UserContractid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserContractidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.user_contractid.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $contracts = UserContractid::select('user_contractid.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $contracts = $contracts->where('user_contractid.id', '=', "$id");
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $contracts = $contracts->where('user_contractid.user_id', 'LIKE', "%$user_id%");
        }
        if (isset($data['subscription_contractid']) && !empty($data['subscription_contractid'])) {
            $subscription_contractid = $data['subscription_contractid'];
            $contracts = $contracts->where('user_contractid.subscriptioncontractid', 'LIKE', "%$subscription_contractid%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $contracts = $contracts->whereBetween('user_contractid.created_time', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $contracts->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'user_contractid.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'user_contractid.id';
                break;
            case 1:
                $columnName = 'user_contractid.user_id';
                break;
            case 2:
                $columnName = 'user_contractid.subscriptioncontractid';
                break;
            case 3:
                $columnName = 'user_contractid.created_time';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $contracts = $contracts->where(function ($q) use ($search) {
                $q->where('user_contractid.user_id', 'LIKE', "%$search%")
                    ->orWhere('user_contractid.subscriptioncontractid', 'LIKE', "%$search%")
                    ->orWhere('user_contractid.id', '=', $search);
            });
        }

        $contracts = $contracts->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($contracts as $contract) {
            $records["data"][] = [
                $contract->id,
                $contract->user_id,
                $contract->subscriptioncontractid,
                $contract->created_time,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $contract->id . '" type="checkbox" ' . ((!PerUser('user_contractid_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('user_contractid_publish')) ? 'class="changeStatues"' : '') . ' ' . (($contract->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $contract->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $contract->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('user_contractid_edit')) ? '<li>
                                            <a href="' . URL('admin/user_contractid/' . $contract->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('user_contractid_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $contract->id . '" >
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
        return view('auth.user_contractid.add');
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
                'user_id' => 'required',
                'subscription_contractid' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $contract = new UserContractid();
            $contract->user_id = $data['user_id'];
            $contract->subscriptioncontractid = $data['subscription_contractid'];
            //$contract->published = $published;
            $contract->created_time = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $contract->published_by = Auth::user()->id;
//                $contract->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $contract->unpublished_by = Auth::user()->id;
//                $contract->unpublished_date = date("Y-m-d H:i:s");
//            }
            $contract->lastedit_by = Auth::user()->id;
            $contract->added_by = Auth::user()->id;
            $contract->lastedit_date = date("Y-m-d H:i:s");
            $contract->added_date = date("Y-m-d H:i:s");
            if ($contract->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.user_contractid'));
                return Redirect::to('admin/user_contractid/create');
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
        $contract = UserContractid::findOrFail($id);
        return view('auth.user_contractid.edit', compact('contract'));
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
        $contract = UserContractid::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'user_id' => 'required',
                'subscription_contractid' => 'required',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $contract->user_id = $data['user_id'];
            $contract->subscriptioncontractid = $data['subscription_contractid'];
//            if ($published == 'yes' && $contract->published=='no') {
//                $contract->published_by = Auth::user()->id;
//                $contract->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $contract->published=='yes') {
//                $contract->unpublished_by = Auth::user()->id;
//                $contract->unpublished_date = date("Y-m-d H:i:s");
//            }
            //$contract->published = $published;
            $contract->lastedit_by = Auth::user()->id;
            $contract->lastedit_date = date("Y-m-d H:i:s");
            if ($contract->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.user_contractid'));
                return Redirect::to("admin/user_contractid/$contract->id/edit");
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
        $contract = UserContractid::findOrFail($id);
        $contract->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $contract = UserContractid::findOrFail($id);
//            if ($published == 'no') {
//                $contract->published = 'no';
//                $contract->unpublished_by = Auth::user()->id;
//                $contract->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $contract->published = 'yes';
//                $contract->published_by = Auth::user()->id;
//                $contract->published_date = date("Y-m-d H:i:s");
//            }
//            $contract->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
