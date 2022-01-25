<?php

namespace App\Http\Controllers\Admin;

use App\Automation;
use App\ChargeTransaction;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;

class AutomationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view('auth.automation.view');
        return view('auth.automation.view_charge_transaction');
    }
    function search(Request $request)
    {

        $data = $request->input();
        $transactions = ChargeTransaction::leftjoin('users','users.id','=','charge_transaction.user_id')->where('subscrip_type','onlinepayment')->where(DB::raw('DATE_FORMAT(createtime,"%Y-%m-%d")'),'>=','2020-07-16')
            ->select('charge_transaction.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('charge_transaction.id', '=', $id);
        }
        if (isset($data['amount']) && !empty($data['amount'])) {
            $amount = $data['amount'];
            $transactions = $transactions->where('charge_transaction.amount', 'LIKE', "%$amount%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $transactions = $transactions->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['currency']) && !empty($data['currency'])) {
            $currency = $data['currency'];
            $transactions = $transactions->where('charge_transaction.currency', 'LIKE', "%$currency%");
        }
        if (isset($data['period'])) {
            $period = $data['period'];
            $transactions = $transactions->where('charge_transaction.period', '=', $period);
        }
        if (isset($data['pending'])) {
            $pending = $data['pending'];
            $transactions = $transactions->where('charge_transaction.pending', '=', $pending);
        }
        if (isset($data['subscribe_type'])) {
            $subscribe_type = $data['subscribe_type'];
            $transactions = $transactions->where('charge_transaction.subscrip_type', '=', $subscribe_type);
        }
        if (isset($data['utm_source'])) {
            $utm_source = $data['utm_source'];
            $transactions = $transactions->where('charge_transaction.utm_source', 'LIKE', "%$utm_source%");
        }
        if (isset($data['start_date_from']) && !empty($data['start_date_from']) && isset($data['start_date_to']) && !empty($data['start_date_to'])) {
            $start_date_from = $data['start_date_from'];
            $start_date_to = $data['start_date_to'];
            $transactions = $transactions->whereBetween('charge_transaction.start_date', [$start_date_from .' 00:00:00', $start_date_to.' 23:59:59']);
        }
        if (isset($data['end_date_from']) && !empty($data['end_date_from']) && isset($data['end_date_to']) && !empty($data['end_date_to'])) {
            $end_date_from = $data['end_date_from'];
            $end_date_to = $data['end_date_to'];
            $transactions = $transactions->whereBetween('charge_transaction.end_date', [$end_date_from .' 00:00:00', $end_date_to.' 23:59:59']);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('charge_transaction.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $transactions->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'charge_transaction.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'charge_transaction.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'charge_transaction.amount';
                break;
            case 3:
                $columnName = 'charge_transaction.currency';
                break;
            case 4:
                $columnName = 'charge_transaction.period';
                break;
            case 5:
                $columnName = 'charge_transaction.start_date';
                break;
            case 6:
                $columnName = 'charge_transaction.end_date';
                break;
            case 7:
                $columnName = 'charge_transaction.pending';
                break;
            case 8:
                $columnName = 'charge_transaction.subscrip_type';
                break;
            case 9:
                $columnName = 'charge_transaction.utm_source';
                break;
            case 10:
                $columnName = 'charge_transaction.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('charge_transaction.amount', 'LIKE', "%$search%")
                    ->orWhere('charge_transaction.currency', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('charge_transaction.period', '=', $search)
                    ->orWhere('charge_transaction.subscrip_type', '=', $search)
                    ->orWhere('charge_transaction.id', '=', $search);
            });
        }

        $transactions = $transactions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($transactions as $transaction) {
            $user_email = $transaction->user_email;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $transaction->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $transaction->id,
                $user_email,
                $transaction->amount,
                $transaction->currency,
                $transaction->period,
                $transaction->start_date,
                $transaction->end_date,
                $transaction->pending,
                $transaction->subscrip_type,
                $transaction->utm_source,
                $transaction->createtime,
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('charge_transaction_edit')) ? '<li>
                                            <a href="' . URL('admin/charge_transaction/' . $transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('charge_transaction_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $transaction->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('charge_transaction_copy')) ? '<li>
                                            <a href="'.URL('admin/charge_transaction/copy/'.$transaction->id).'" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.copy') . '
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

    function searchOld(Request $request)
    {

        $data = $request->input();
        $automations =Automation::leftjoin('users', 'users.id', '=', 'automation.user_id')
            ->select('automation.*', 'users.Email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $automations = $automations->where('automation.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $automations = $automations->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['package']) && !empty($data['package'])) {
            $package = $data['package'];
            $automations = $automations->where('automation.package', 'LIKE', "%$package%");
        }
        if (isset($data['payment_method']) && !empty($data['payment_method'])) {
            $payment_method = $data['payment_method'];
            $automations = $automations->where('automation.payment_method', 'LIKE', "%$payment_method%");
        }
        if (isset($data['shipping_address']) && !empty($data['shipping_address'])) {
            $shipping_address = $data['shipping_address'];
            $automations = $automations->where('automation.shipping_address', 'LIKE', "%$shipping_address%");
        }
        if (isset($data['bank_transfer']) && !empty($data['bank_transfer'])) {
            $bank_transfer = $data['bank_transfer'];
            $automations = $automations->where('automation.bank_transfer', 'LIKE', "%$bank_transfer%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $automations = $automations->whereBetween('automation.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $automations->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'automation.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'automation.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'automation.package';
                break;
            case 3:
                $columnName = 'automation.payment_method';
                break;
            case 4:
                $columnName = 'automation.shipping_address';
                break;
            case 5:
                $columnName = 'automation.bank_transfer';
                break;
            case 6:
                $columnName = 'automation.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $automations = $automations->where(function ($q) use ($search) {
                $q->where('automation.package', 'LIKE', "%$search%")
                    ->orWhere('automation.payment_method', 'LIKE', "%$search%")
                    ->orWhere('automation.shipping_address', 'LIKE', "%$search%")
                    ->orWhere('automation.bank_transfer', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('automation.id', '=', $search);
            });
        }

        $automations = $automations->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($automations as $automation) {
            $user = $automation->Email;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $automation->user_id . '/edit') . '">' . $user . '</a>';
            }
            $records["data"][] = [
                $automation->id,
                $user,
                $automation->package,
                $automation->payment_method,
                $automation->shipping_address,
                $automation->bank_transfer,
                $automation->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $automation->id . '" type="checkbox" ' . ((!PerUser('automation_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('automation_publish')) ? 'class="changeStatues"' : '') . ' ' . (($automation->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $automation->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $automation->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('automation_edit')) ? '<li>
                                            <a href="' . URL('admin/automation/' . $automation->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('automation_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $automation->id . '" >
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
        return view('auth.automation.add');
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
        $rules=array(
            'user' =>  'required|exists:mysql2.users,Email',
            'package' => 'required',
            'payment_method' => 'required',
            'shipping_address' => 'required',
            'bank_transfer' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $automation = new Automation();
            $automation->user_id = $user_id;
            $automation->package = $data['package'];
            $automation->payment_method = $data['payment_method'];
            $automation->shipping_address = $data['shipping_address'];
            $automation->bank_transfer = $data['bank_transfer'];
            //$automation->published = $published;
            $automation->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $automation->published_by = Auth::user()->id;
//                $automation->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $automation->unpublished_by = Auth::user()->id;
//                $automation->unpublished_date = date("Y-m-d H:i:s");
//            }
            $automation->lastedit_by = Auth::user()->id;
            $automation->added_by = Auth::user()->id;
            $automation->added_date = date("Y-m-d H:i:s");
            $automation->lastedit_date = date("Y-m-d H:i:s");
            if ($automation->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.automation'));
                return Redirect::to('admin/automation/create');
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
        $automation = Automation::findOrFail($id);
        $user=isset($automation->user)?$automation->user->Email:'';
        return view('auth.automation.edit', compact('automation','user'));
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
        $automation = Automation::findOrFail($id);
        $rules=array(
            'user' =>  'required|exists:mysql2.users,Email',
            'package' => 'required',
            'payment_method' => 'required',
            'shipping_address' => 'required',
            'bank_transfer' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $automation->user_id = $user_id;
            $automation->package = $data['package'];
            $automation->payment_method = $data['payment_method'];
            $automation->shipping_address = $data['shipping_address'];
            $automation->bank_transfer = $data['bank_transfer'];
//            if ($published == 'yes' && $automation->published=='no') {
//                $automation->published_by = Auth::user()->id;
//                $automation->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $automation->published=='yes') {
//                $automation->unpublished_by = Auth::user()->id;
//                $automation->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $automation->published = $published;
            $automation->lastedit_by = Auth::user()->id;
            $automation->lastedit_date = date("Y-m-d H:i:s");
            if ($automation->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.automation'));
                return Redirect::to("admin/automation/$automation->id/edit");
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
        $automation = Automation::findOrFail($id);
        $automation->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $automation = Automation::findOrFail($id);
//            if ($published == 'no') {
//                $automation->published = 'no';
//                $automation->unpublished_by = Auth::user()->id;
//                $automation->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $automation->published = 'yes';
//                $automation->published_by = Auth::user()->id;
//                $automation->published_date = date("Y-m-d H:i:s");
//            }
//            $automation->save();
//
//        } else {
//            return redirect(404);
//        }
//    }

}
