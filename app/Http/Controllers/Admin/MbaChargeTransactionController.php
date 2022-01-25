<?php

namespace App\Http\Controllers\Admin;

use App\MbaChargeTransaction;
use App\Mba;
use App\NormalUser;
use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MbaChargeTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.mba_charge_transaction.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $transactions = MbaChargeTransaction::leftjoin('users','users.id','=','mba_charge_transaction.user_id')
            ->select('mba_charge_transaction.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('mba_charge_transaction.id', '=', "$id");
        }
//        if (isset($data['mba_name']) && !empty($data['mba_name'])) {
//            $mba_name = $data['mba_name'];
//            $transactions = $transactions->where('mba_charge_transaction.mba_name', 'LIKE', "%$mba_name%");
//        }
        if (isset($data['mba_price']) && !empty($data['mba_price'])) {
            $mba_price = $data['mba_price'];
            $transactions = $transactions->where('mba_charge_transaction.mba_price', 'LIKE', "%$mba_price%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $transactions = $transactions->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['currency']) && !empty($data['currency'])) {
            $currency = $data['currency'];
            $transactions = $transactions->where('mba_charge_transaction.currency', 'LIKE', "%$currency%");
        }
        if (isset($data['start_date_from']) && !empty($data['start_date_from']) && isset($data['start_date_to']) && !empty($data['start_date_to'])) {
            $start_date_from = $data['start_date_from'];
            $start_date_to = $data['start_date_to'];
            $transactions = $transactions->whereBetween('mba_charge_transaction.start_date', [$start_date_from .' 00:00:00', $start_date_to.' 23:59:59']);
        }
        if (isset($data['end_date_from']) && !empty($data['end_date_from']) && isset($data['end_date_to']) && !empty($data['end_date_to'])) {
            $end_date_from = $data['end_date_from'];
            $end_date_to = $data['end_date_to'];
            $transactions = $transactions->whereBetween('mba_charge_transaction.start_date', [$end_date_from .' 00:00:00', $end_date_to.' 23:59:59']);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('mba_charge_transaction.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'mba_charge_transaction.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba_charge_transaction.id';
                break;
//            case 1:
//                $columnName = 'mba_charge_transaction.mba_name';
//                break;
            case 1:
                $columnName = 'mba_charge_transaction.mba_price';
                break;
            case 2:
                $columnName = 'mba_charge_transaction.currency';
                break;
            case 3:
                $columnName = 'users.Email';
                break;
            case 4:
                $columnName = 'mba_charge_transaction.start_date';
                break;
            case 5:
                $columnName = 'mba_charge_transaction.end_date';
                break;
            case 6:
                $columnName = 'mba_charge_transaction.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('mba_charge_transaction.mba_price', 'LIKE', "%$search%")
                    ->orWhere('mba_charge_transaction.currency', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('mba_charge_transaction.id', '=', $search);
            });
        }

        $transactions = $transactions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($transactions as $transaction) {
            $user = $transaction->user_email;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $transaction->user_id . '/edit') . '">' . $user . '</a>';
            }
            $records["data"][] = [
                $transaction->id,
//                $transaction->mba_name,
                $transaction->mba_price,
                $transaction->currency,
                $user,
                $transaction->start_date,
                $transaction->end_date,
                $transaction->createtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $transaction->id . '" type="checkbox" ' . ((!PerUser('mba_charge_transaction_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('mba_charge_transaction_publish')) ? 'class="changeStatues"' : '') . ' ' . (($transaction->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $transaction->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('mba_charge_transaction_edit')) ? '<li>
                                            <a href="' . URL('admin/mba_charge_transaction/' . $transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('mba_charge_transaction_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $transaction->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('mba_charge_transaction_copy')) ? '<li>
                                            <a href="'.URL('admin/mba_charge_transaction/copy/'.$transaction->id).'" >
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
        $mbas=Mba::pluck('name', 'id');
        $employees=Employee::pluck('username', 'id');
        return view('auth.mba_charge_transaction.add',compact('mbas','employees'));
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
            //'mba' => 'required|exists:mysql2.mba,id',
            'mba_price' => 'required',
            'period' => 'required',
            'user' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            //'end_date' => 'required|date_format:"Y-m-d"',
            //'subscribe_type' => 'required|in:mba_free,mba_paid,mba_onlinepayment',
            //'amount' => 'required',
            'subscribe_country' => 'required|in:egy,ksa',
            'currency' => 'required',
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
            $mba_charge_transaction=MbaChargeTransaction::where('user_id',$user->id)->orderBy('end_date','DESC')->first();
            $start_date=date('Y-m-d H:i:s',strtotime($data['start_date']));
            $end_date=NULL;
            if(count($mba_charge_transaction)){
                if($mba_charge_transaction->end_date>$start_date){
                    $start_date=date('Y-m-d H:i:s',strtotime($mba_charge_transaction->end_date.' +1 day'));
                }
            }
            $end_date=date('Y-m-d H:i:s',strtotime($start_date.' +'.$data['period'].' months'));
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $suspend = (isset($data['suspend'])) ? 1 : 0;
            $pending = (isset($data['pending'])) ? 1 : 0;
            $employee_id = (isset($data['employee'])) ? $data['employee'] : 0;
            $coupon_id = (isset($data['coupon_id'])) ? $data['coupon_id'] : 0;
            $transaction = new MbaChargeTransaction();
            //$transaction->mba_id = $data['mba'];
            //$transaction->mba_name = Mba::where('id', $data['mba'])->first()->name;
            $transaction->mba_price = $data['mba_price'];
            $transaction->user_id = $user->id;
            $transaction->period = $data['period'];
            $transaction->start_date = $start_date;
            $transaction->end_date = $end_date;
            $transaction->pending = $pending;
            $transaction->subscrip_type = $data['subscribe_type'];
            $transaction->amount = $data['amount'];
            $transaction->subscrip_country = $data['subscribe_country'];
            $transaction->currency = $data['currency'];
            $transaction->coupon_id = $coupon_id;
            $transaction->employee_id = $employee_id;
            if ( $request->file('attach')){
                $attach = $request->file('attach');
                $attach_name = uploadFileToE3melbusiness($attach);
                $transaction->attach = $attach_name;
            }
            //$transaction->published = $published;
            $transaction->suspend = $suspend;
            if ($suspend == 1) {
                $transaction->suspend_date = date("Y-m-d H:i:s");
            }
            $transaction->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $transaction->published_by = Auth::user()->id;
//                $transaction->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $transaction->unpublished_by = Auth::user()->id;
//                $transaction->unpublished_date = date("Y-m-d H:i:s");
//            }
            $transaction->lastedit_by = Auth::user()->id;
            $transaction->added_by = Auth::user()->id;
            $transaction->lastedit_date = date("Y-m-d H:i:s");
            $transaction->added_date = date("Y-m-d H:i:s");
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.mba_charge_transaction'));
                return Redirect::to('admin/mba_charge_transaction/create');
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
        $transaction = MbaChargeTransaction::findOrFail($id);
        $transaction->start_date = date("Y-m-d", strtotime($transaction->start_date));
        $transaction->end_date = date("Y-m-d", strtotime($transaction->end_date));
        $mbas=Mba::pluck('name', 'id');
        $employees=Employee::pluck('username', 'id');
        $user=NormalUser::where('id', $transaction->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.mba_charge_transaction.edit', compact('transaction','mbas','employees','user'));
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
        $transaction = MbaChargeTransaction::findOrFail($id);
        $rules=array(
            //'mba' => 'required|exists:mysql2.mba,id',
            'mba_price' => 'required',
            'period' => 'required',
            'user' => 'required',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"',
            //'subscribe_type' => 'required|in:mba_free,mba_paid,mba_onlinepayment',
            //'amount' => 'required',
            'subscribe_country' => 'required|in:egy,ksa',
            'currency' => 'required',
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
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $suspend = (isset($data['suspend'])) ? 1 : 0;
            $pending = (isset($data['pending'])) ? 1 : 0;
            //$transaction->mba_id = $data['mba'];
            //$transaction->mba_name = Mba::where('id', $data['mba'])->first()->name;
            $transaction->mba_price = $data['mba_price'];
            $transaction->user_id = $user->id;
            $transaction->period = $data['period'];
            $transaction->start_date = $data['start_date'];
            $transaction->end_date = $data['end_date'];
            $transaction->pending = $pending;
            $transaction->subscrip_type = $data['subscribe_type'];
            $transaction->amount = $data['amount'];
            $transaction->subscrip_country = $data['subscribe_country'];
            $transaction->currency = $data['currency'];
            $transaction->coupon_id = $data['coupon_id'];
            $transaction->employee_id = $data['employee'];
            if ( $request->file('attach')){
                $attach = $request->file('attach');
                $attach_name = uploadFileToE3melbusiness($attach);
                $transaction->attach = $attach_name;
            }
            if ($transaction->suspend==0 && $suspend == 1) {
                $transaction->suspend_date = date("Y-m-d H:i:s");
            }
            $transaction->suspend = $suspend;
//            if ($published == 'yes' && $transaction->published=='no') {
//                $transaction->published_by = Auth::user()->id;
//                $transaction->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $transaction->published=='yes') {
//                $transaction->unpublished_by = Auth::user()->id;
//                $transaction->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $transaction->published = $published;
            $transaction->lastedit_by = Auth::user()->id;
            $transaction->lastedit_date = date("Y-m-d H:i:s");
            if ($transaction->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.mba_charge_transaction'));
                return Redirect::to("admin/mba_charge_transaction/$transaction->id/edit");
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
        $transaction = MbaChargeTransaction::findOrFail($id);
        $transaction->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $transaction = MbaChargeTransaction::findOrFail($id);
//            if ($published == 'no') {
//                $transaction->published = 'no';
//                $transaction->unpublished_by = Auth::user()->id;
//                $transaction->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $transaction->published = 'yes';
//                $transaction->published_by = Auth::user()->id;
//                $transaction->published_date = date("Y-m-d H:i:s");
//            }
//            $transaction->save();
//        } else {
//            return redirect(404);
//        }
//    }
    public function copy($id)
    {
        $transaction = MbaChargeTransaction::findOrFail($id);
        $transaction->createtime = date("Y-m-d H:i:s");
        $transaction->replicate()->save();
        return Redirect::to('admin/mba_charge_transaction/'.$transaction->id.'/edit');
    }

}
