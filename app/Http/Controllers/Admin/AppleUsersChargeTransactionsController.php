<?php

namespace App\Http\Controllers\Admin;

use App\AppleUsers;
use App\AppleUsersChargeTransactions;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AppleUsersChargeTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.apple_users_charge_transactions.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $transactions = AppleUsersChargeTransactions::leftjoin('users','users.id','=','apple_users_charge_transactions.user_id')
                                        ->select('apple_users_charge_transactions.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('apple_users_charge_transactions.id', '=', $id);
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $transactions = $transactions->where('apple_users_charge_transactions.type', 'LIKE', "%$type%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $transactions = $transactions->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['type_id']) && !empty($data['type_id'])) {
            $type_id = $data['type_id'];
            $transactions = $transactions->where('apple_users_charge_transactions.type_id', '=', $type_id);
        }
        if (isset($data['start_date_from']) && !empty($data['start_date_from']) && isset($data['start_date_to']) && !empty($data['start_date_to'])) {
            $start_date_from = $data['start_date_from'];
            $start_date_to = $data['start_date_to'];
            $transactions = $transactions->whereBetween('apple_users_charge_transactions.start_date', [$start_date_from .' 00:00:00', $start_date_to.' 23:59:59']);
        }
        if (isset($data['end_date_from']) && !empty($data['end_date_from']) && isset($data['end_date_to']) && !empty($data['end_date_to'])) {
            $end_date_from = $data['end_date_from'];
            $end_date_to = $data['end_date_to'];
            $transactions = $transactions->whereBetween('apple_users_charge_transactions.end_date', [$end_date_from .' 00:00:00', $end_date_to.' 23:59:59']);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('apple_users_charge_transactions.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'apple_users_charge_transactions.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'apple_users_charge_transactions.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'apple_users_charge_transactions.type';
                break;
            case 3:
                $columnName = 'apple_users_charge_transactions.type_id';
                break;
            case 4:
                $columnName = 'apple_users_charge_transactions.start_date';
                break;
            case 5:
                $columnName = 'apple_users_charge_transactions.end_date';
                break;
            case 6:
                $columnName = 'apple_users_charge_transactions.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('apple_users_charge_transactions.type', 'LIKE', "%$search%")
                    ->orWhere('apple_users_charge_transactions.type', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('apple_users_charge_transactions.id', '=', $search);
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
                $transaction->type,
                $transaction->type_id,
                $transaction->start_date,
                $transaction->end_date,
                $transaction->createtime,
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('apple_users_charge_transactions_edit')) ? '<li>
                                            <a href="' . URL('admin/apple_users_charge_transactions/' . $transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('apple_users_charge_transactions_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $transaction->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '')
                                  . '</ul>
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
        $apple_users=AppleUsers::pluck('id');
        return view('auth.apple_users_charge_transactions.add',compact('apple_users'));
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
            'apple_user_id' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"',
            'quantity' => 'required',
            'product_id' => 'required',
            'transaction_id' => 'required',
            'original_transaction_id' => 'required',
            'purchase_date' => 'required',
            'purchase_date_ms' => 'required',
            'purchase_date_pst' => 'required',
            'original_purchase_date' => 'required',
            'original_purchase_date_ms' => 'required',
            'original_purchase_date_pst' => 'required',
            'is_trial_period' => 'required|in:true,false',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $type = (isset($data['type'])) ? $data['type'] : null;
            $type_id = (isset($data['type_id'])) ? $data['type_id'] : 0;
            $transaction = new AppleUsersChargeTransactions();
            $transaction->user_id = $user->id;
            $transaction->apple_user_id = $data['apple_user_id'];
            $transaction->start_date = $data['start_date'];
            $transaction->end_date = $data['end_date'];
            $transaction->quantity = $data['quantity'];
            $transaction->product_id = $data['product_id'];
            $transaction->transaction_id = $data['transaction_id'];
            $transaction->original_transaction_id = $data['original_transaction_id'];
            $transaction->purchase_date = $data['purchase_date'];
            $transaction->purchase_date_ms = $data['purchase_date_ms'];
            $transaction->purchase_date_pst = $data['purchase_date_pst'];
            $transaction->original_purchase_date = $data['original_purchase_date'];
            $transaction->original_purchase_date_ms = $data['original_purchase_date_ms'];
            $transaction->original_purchase_date_pst = $data['original_purchase_date_pst'];
            $transaction->is_trial_period = $data['is_trial_period'];
            $transaction->type = $type;
            $transaction->type_id = $type_id;
            $transaction->createtime = date("Y-m-d H:i:s");
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.apple_users_charge_transactions'));
                return Redirect::to('admin/apple_users_charge_transactions/create');
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
        $transaction = AppleUsersChargeTransactions::findOrFail($id);
        $transaction->start_date = date("Y-m-d", strtotime($transaction->start_date));
        $transaction->end_date = date("Y-m-d", strtotime($transaction->end_date));
        $user=NormalUser::where('id', $transaction->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        $apple_users=AppleUsers::pluck('id');
        return view('auth.apple_users_charge_transactions.edit', compact('transaction','user','apple_users'));
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
        $transaction = AppleUsersChargeTransactions::findOrFail($id);
        $rules=array(
            'user' => 'required',
            'apple_user_id' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"',
            'quantity' => 'required',
            'product_id' => 'required',
            'transaction_id' => 'required',
            'original_transaction_id' => 'required',
            'purchase_date' => 'required',
            'purchase_date_ms' => 'required',
            'purchase_date_pst' => 'required',
            'original_purchase_date' => 'required',
            'original_purchase_date_ms' => 'required',
            'original_purchase_date_pst' => 'required',
            'is_trial_period' => 'required|in:true,false',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $type = (isset($data['type'])) ? $data['type'] : null;
            $type_id = (isset($data['type_id'])) ? $data['type_id'] : 0;
            $transaction->user_id = $user->id;
            $transaction->apple_user_id = $data['apple_user_id'];
            $transaction->start_date = $data['start_date'];
            $transaction->end_date = $data['end_date'];
            $transaction->quantity = $data['quantity'];
            $transaction->product_id = $data['product_id'];
            $transaction->transaction_id = $data['transaction_id'];
            $transaction->original_transaction_id = $data['original_transaction_id'];
            $transaction->purchase_date = $data['purchase_date'];
            $transaction->purchase_date_ms = $data['purchase_date_ms'];
            $transaction->purchase_date_pst = $data['purchase_date_pst'];
            $transaction->original_purchase_date = $data['original_purchase_date'];
            $transaction->original_purchase_date_ms = $data['original_purchase_date_ms'];
            $transaction->original_purchase_date_pst = $data['original_purchase_date_pst'];
            $transaction->is_trial_period = $data['is_trial_period'];
            $transaction->type = $type;
            $transaction->type_id = $type_id;
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.apple_users_charge_transactions'));
                return Redirect::to("admin/apple_users_charge_transactions/$transaction->id/edit");
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
        $transaction = AppleUsersChargeTransactions::findOrFail($id);
        $transaction->delete();
    }

}
