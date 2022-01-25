<?php

namespace App\Http\Controllers\Admin;

use App\MedicalChargeTransactions;
use App\NormalUser;
use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MedicalChargeTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.medical_charge_transactions.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $transactions = MedicalChargeTransactions::leftjoin('users','users.id','=','medical_charge_transactions.user_id')
                                        ->select('medical_charge_transactions.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('medical_charge_transactions.id', '=', $id);
        }
        if (isset($data['amount']) && !empty($data['amount'])) {
            $amount = $data['amount'];
            $transactions = $transactions->where('medical_charge_transactions.amount', 'LIKE', "%$amount%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $transactions = $transactions->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['currency']) && !empty($data['currency'])) {
            $currency = $data['currency'];
            $transactions = $transactions->where('medical_charge_transactions.currency', 'LIKE', "%$currency%");
        }
        if (isset($data['period'])) {
            $period = $data['period'];
            $transactions = $transactions->where('medical_charge_transactions.period', '=', $period);
        }
        if (isset($data['pending'])) {
            $pending = $data['pending'];
            $transactions = $transactions->where('medical_charge_transactions.pending', '=', $pending);
        }
        if (isset($data['subscribe_type'])) {
            $subscribe_type = $data['subscribe_type'];
            $transactions = $transactions->where('medical_charge_transactions.subscrip_type', '=', $subscribe_type);
        }
        if (isset($data['start_date_from']) && !empty($data['start_date_from']) && isset($data['start_date_to']) && !empty($data['start_date_to'])) {
            $start_date_from = $data['start_date_from'];
            $start_date_to = $data['start_date_to'];
            $transactions = $transactions->whereBetween('medical_charge_transactions.start_date', [$start_date_from .' 00:00:00', $start_date_to.' 23:59:59']);
        }
        if (isset($data['end_date_from']) && !empty($data['end_date_from']) && isset($data['end_date_to']) && !empty($data['end_date_to'])) {
            $end_date_from = $data['end_date_from'];
            $end_date_to = $data['end_date_to'];
            $transactions = $transactions->whereBetween('medical_charge_transactions.start_date', [$end_date_from .' 00:00:00', $end_date_to.' 23:59:59']);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('medical_charge_transactions.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'medical_charge_transactions.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'medical_charge_transactions.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'medical_charge_transactions.amount';
                break;
            case 3:
                $columnName = 'medical_charge_transactions.currency';
                break;
            case 4:
                $columnName = 'medical_charge_transactions.period';
                break;
            case 5:
                $columnName = 'medical_charge_transactions.start_date';
                break;
            case 6:
                $columnName = 'medical_charge_transactions.end_date';
                break;
            case 7:
                $columnName = 'medical_charge_transactions.pending';
                break;
            case 8:
                $columnName = 'medical_charge_transactions.subscrip_type';
                break;
            case 9:
                $columnName = 'medical_charge_transactions.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('medical_charge_transactions.amount', 'LIKE', "%$search%")
                    ->orWhere('medical_charge_transactions.currency', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('medical_charge_transactions.period', '=', $search)
                    ->orWhere('medical_charge_transactions.subscrip_type', '=', $search)
                    ->orWhere('medical_charge_transactions.id', '=', $search);
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
                $transaction->createtime,
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('medical_charge_transactions_edit')) ? '<li>
                                            <a href="' . URL('admin/medical_charge_transactions/' . $transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('medical_charge_transactions_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $transaction->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('medical_charge_transactions_copy')) ? '<li>
                                            <a class="copy_this" data-id="' . $transaction->id . '" >
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


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees=Employee::pluck('username', 'id');
        return view('auth.medical_charge_transactions.add',compact('employees'));
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
            'user' => 'required',
            'period' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            //'end_date' => 'required|date_format:"Y-m-d"',
            //'subscribe_type' => 'required',
            'amount' => 'required',
            //'subscribe_country' => 'required|in:egy,ksa',
            //'currency' => 'required',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        if($request->file('attach')){
            $rules['attach']= 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $medical_charge_transaction=MedicalChargeTransactions::where('user_id',$user->id)->orderBy('end_date','DESC')->first();
            $start_date=date('Y-m-d H:i:s',strtotime($data['start_date']));
            $end_date=NULL;
            if(count($medical_charge_transaction)){
                if($medical_charge_transaction->end_date>$start_date){
                    $start_date=date('Y-m-d H:i:s',strtotime($medical_charge_transaction->end_date.' +1 day'));
                }
            }
            $end_date=date('Y-m-d H:i:s',strtotime($start_date.' +'.$data['period'].' months'));
            $pending = (isset($data['pending'])) ? 1 : 0;
            $employee_id = (isset($data['employee'])) ? $data['employee'] : 0;
            $coupon_id = (isset($data['coupon_id'])) ? $data['coupon_id'] : 0;
            $transaction = new MedicalChargeTransactions();
            $transaction->user_id = $user->id;
            $transaction->period = $data['period'];
            $transaction->start_date = $start_date;
            $transaction->end_date = $end_date;
            $transaction->pending = $pending;
            $transaction->subscrip_type = $data['subscribe_type'];
            $transaction->amount = $data['amount'];
            $transaction->subscribe_country = $data['subscribe_country'];
            $transaction->currency = $data['currency'];
            $transaction->coupon_id = $coupon_id;
            $transaction->employee_id = $employee_id;
            if ( $request->file('attach')){
                $attach = $request->file('attach');
                $attach_name = uploadFileToE3melbusiness($attach);
                $transaction->attach = $attach_name;
            }
            $transaction->lastedit_by = Auth::user()->id;
            $transaction->added_by = Auth::user()->id;
            $transaction->lastedit_date = date("Y-m-d H:i:s");
            $transaction->added_date = date("Y-m-d H:i:s");
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.medical_charge_transactions'));
                return Redirect::to('admin/medical_charge_transactions/create');
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
        $transaction = MedicalChargeTransactions::findOrFail($id);
        $transaction->start_date = date("Y-m-d", strtotime($transaction->start_date));
        $transaction->end_date = date("Y-m-d", strtotime($transaction->end_date));
        $employees=Employee::pluck('username', 'id');
        $user=NormalUser::where('id', $transaction->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.medical_charge_transactions.edit', compact('transaction','employees','user'));
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
        $transaction = MedicalChargeTransactions::findOrFail($id);
        $rules=array(
            'user' => 'required',
            'period' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"',
            //'subscribe_type' => 'required',
            'amount' => 'required',
            //'subscribe_country' => 'required|in:egy,ksa',
            //'currency' => 'required',
        );

        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        if($request->file('attach')){
            $rules['attach']= 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $pending = (isset($data['pending'])) ? 1 : 0;
            $transaction->user_id = $user->id;
            $transaction->period = $data['period'];
            $transaction->start_date = $data['start_date'];
            $transaction->end_date = $data['end_date'];
            $transaction->pending = $pending;
            $transaction->subscrip_type = $data['subscribe_type'];
            $transaction->amount = $data['amount'];
            $transaction->subscribe_country = $data['subscribe_country'];
            $transaction->currency = $data['currency'];
            $transaction->coupon_id = $data['coupon_id'];
            $transaction->employee_id = $data['employee'];
            if ( $request->file('attach')){
                $attach = $request->file('attach');
                $attach_name = uploadFileToE3melbusiness($attach);
                $transaction->attach = $attach_name;
            }
            $transaction->lastedit_by = Auth::user()->id;
            $transaction->lastedit_date = date("Y-m-d H:i:s");
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.medical_charge_transactions'));
                return Redirect::to("admin/medical_charge_transactions/$transaction->id/edit");
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
        $transaction = MedicalChargeTransactions::findOrFail($id);
        $transaction->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $transaction = MedicalChargeTransactions::findOrFail($id);
            if ($published == 'no') {
                $transaction->published = 'no';
                $transaction->unpublished_by = Auth::user()->id;
                $transaction->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $transaction->published = 'yes';
                $transaction->published_by = Auth::user()->id;
                $transaction->published_date = date("Y-m-d H:i:s");
            }
            $transaction->save();
        } else {
            return redirect(404);
        }
    }

    public function copy(Request $request)
    {
        $id = $request->input('id');
        $transaction = MedicalChargeTransactions::findOrFail($id);
        $transaction->replicate()->save();
    }

}
