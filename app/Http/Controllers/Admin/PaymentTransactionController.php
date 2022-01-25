<?php

namespace App\Http\Controllers\Admin;

use App\PaymentTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class PaymentTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.payment_transaction.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $transactions = PaymentTransaction::select('payment_transaction.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('payment_transaction.id', '=', $id);
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $transactions = $transactions->where('payment_transaction.email', 'LIKE', "%$email%");
        }
        if (isset($data['package']) && !empty($data['package'])) {
            $package	 = $data['package'];
            $transactions = $transactions->where('payment_transaction.package', 'LIKE', "%$package%");
        }
        if (isset($data['amount']) && !empty($data['amount'])) {
            $amount	 = $data['amount'];
            $transactions = $transactions->where('payment_transaction.amount', 'LIKE', "%$amount%");
        }
        if (isset($data['paymentstatus']) && !empty($data['paymentstatus'])) {
            $paymentstatus	 = $data['paymentstatus'];
            $transactions = $transactions->where('payment_transaction.paymentstatus', 'LIKE', "%$paymentstatus%");
        }
        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $customer_id	 = $data['customer_id'];
            $transactions = $transactions->where('payment_transaction.customer_id', 'LIKE', "%$customer_id%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('payment_transaction.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'payment_transaction.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'payment_transaction.id';
                break;
            case 1:
                $columnName = 'payment_transaction.email';
                break;
            case 2:
                $columnName = 'payment_transaction.package';
                break;
            case 3:
                $columnName = 'payment_transaction.amount';
                break;
            case 4:
                $columnName = 'payment_transaction.paymentstatus';
                break;
            case 5:
                $columnName = 'payment_transaction.customer_id';
                break;
            case 6:
                $columnName = 'payment_transaction.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('payment_transaction.id', '=', $search)
                    ->orWhere('payment_transaction.email', 'Like', "%$search%")
                    ->orWhere('payment_transaction.package', 'Like', "%$search%")
                    ->orWhere('payment_transaction.amount', 'Like', "%$search%")
                    ->orWhere('payment_transaction.paymentstatus', 'Like', "%$search%")
                    ->orWhere('payment_transaction.customer_id', 'Like', "%$search%");
            });
        }

        $transactions = $transactions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($transactions as $transaction) {
            $records["data"][] = [
                $transaction->id,
                $transaction->email,
                $transaction->package,
                $transaction->amount,
                $transaction->paymentstatus,
                $transaction->customer_id,
                $transaction->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $transaction->id . '" type="checkbox" ' . ((!PerUser('payment_transaction_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('payment_transaction_publish')) ? 'class="changeStatues"' : '') . ' ' . (($transaction->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $transaction->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('payment_transaction_edit')) ? '<li>
                                            <a href="' . URL('admin/payment_transaction/' . $transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('payment_transaction_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $transaction->id . '" >
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

    public function create()
    {
        return view('auth.payment_transaction.add');
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $validator = Validator::make($request->all(),
            array(
                'email' => 'required|email',
                'package' => 'required',
                'amount' => 'required',
                'paymentstatus' => 'required',
                'customer_id' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $coupon_id = (isset($data['coupon_id'])) ? $data['coupon_id'] : 0;
            $price = (isset($data['price'])) ? $data['price'] : '';
            $transaction = new PaymentTransaction();
            $transaction->email = $data['email'];
            $transaction->package = $data['package'];
            $transaction->amount = $data['amount'];
            $transaction->paymentstatus = $data['paymentstatus'];
            $transaction->customer_id = $data['customer_id'];
            $transaction->coupon_id = $coupon_id;
            $transaction->copon_id = $coupon_id;
            $transaction->price =  $price;
            //$transaction->published = $published;
            $transaction->createdtime = date("Y-m-d H:i:s");
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
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.payment_transaction'));
                return Redirect::to('admin/payment_transaction/create');
            }
        }
    }

    public function edit($id)
    {
        $transaction = PaymentTransaction::findOrFail($id);
        return view('auth.payment_transaction.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        $transaction=PaymentTransaction::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'email' => 'required|email',
                'package' => 'required',
                'amount' => 'required',
                'paymentstatus' => 'required',
                'customer_id' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $coupon_id = (isset($data['coupon_id'])) ? $data['coupon_id'] : 0;
            $price = (isset($data['price'])) ? $data['price'] : '';
            $transaction->email = $data['email'];
            $transaction->package = $data['package'];
            $transaction->amount = $data['amount'];
            $transaction->paymentstatus = $data['paymentstatus'];
            $transaction->customer_id = $data['customer_id'];
            $transaction->coupon_id = $coupon_id;
            $transaction->copon_id = $coupon_id;
            $transaction->price =  $price;
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
                Session::flash('success', Lang::get('main.update') . Lang::get('main.payment_transaction'));
                return Redirect::to("admin/payment_transaction/$transaction->id/edit");
            }
        }
    }

    public function destroy($id)
    {
        $transaction = PaymentTransaction::findOrFail($id);
        $transaction->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $transaction = PaymentTransaction::findOrFail($id);
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

}
