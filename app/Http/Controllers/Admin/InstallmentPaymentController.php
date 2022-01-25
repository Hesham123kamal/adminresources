<?php

namespace App\Http\Controllers\Admin;

use App\InstallmentPayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class  InstallmentPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.installment_payment.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $payments = InstallmentPayment::select('installment_payment.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $payments = $payments->where('id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $payments = $payments->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $payments = $payments->where('email', 'LIKE', "%$email%");
        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $payments = $payments->where('mobile', 'LIKE', "%$mobile%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $payments = $payments->whereBetween('createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $payments->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'installment_payment.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'installment_payment.id';
                break;
            case 1:
                $columnName = 'installment_payment.name';
                break;
            case 2:
                $columnName = 'installment_payment.email';
                break;
            case 3:
                $columnName = 'installment_payment.mobile';
                break;
            case 4:
                $columnName = 'installment_payment.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $payments = $payments->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search)
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('mobile', 'LIKE', "%$search%");

            });
        }

        $payments = $payments->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($payments as $payment) {
            $records["data"][] = [
                $payment->id,
                $payment->name,
                $payment->email,
                $payment->mobile,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $payment->id . '" type="checkbox" ' . ((!PerUser('installment_payment_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('installment_payment_publish')) ? 'class="changeStatues"' : '') . ' ' . (($payment->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $payment->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                $payment->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $payment->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('installment_payment_edit')) ? '<li>
                                            <a href="' . URL('admin/installment_payment/' . $payment->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('installment_payment_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $payment->id . '" >
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
        return view('auth.installment_payment.add');
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
                'email' => 'required|email',
                'mobile' => 'required'
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $payment = new InstallmentPayment();
            $payment->name = $data['name'];
            $payment->email = $data['email'];
            $payment->mobile = $data['mobile'];
            //$payment->published = $published;
            $payment->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $payment->published_by = Auth::user()->id;
//                $payment->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $payment->unpublished_by = Auth::user()->id;
//                $payment->unpublished_date = date("Y-m-d H:i:s");
//            }
            $payment->lastedit_by = Auth::user()->id;
            $payment->added_by = Auth::user()->id;
            $payment->lastedit_date = date("Y-m-d H:i:s");
            $payment->added_date = date("Y-m-d H:i:s");
            if ($payment->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.installment_payment'));
                return Redirect::to('admin/installment_payment/create');
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
        $payment = InstallmentPayment::findOrFail($id);
        return view('auth.installment_payment.edit', compact('payment'));
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
        $payment = InstallmentPayment::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'email' => 'required|email',
                'mobile' => 'required'
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $payment->name = $data['name'];
            $payment->email = $data['email'];
            $payment->mobile = $data['mobile'];
//            if ($published == 'yes' && $payment->published=='no') {
//                $payment->published_by = Auth::user()->id;
//                $payment->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $payment->published=='yes') {
//                $payment->unpublished_by = Auth::user()->id;
//                $payment->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $payment->published = $published;
            $payment->lastedit_by = Auth::user()->id;
            $payment->lastedit_date = date("Y-m-d H:i:s");
            if ($payment->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.installment_payment'));
                return Redirect::to("admin/installment_payment/$payment->id/edit");
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
        $payment = InstallmentPayment::findOrFail($id);
        $payment->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $payment = InstallmentPayment::findOrFail($id);
//            if ($published == 'no') {
//                $payment->published = 'no';
//                $payment->unpublished_by = Auth::user()->id;
//                $payment->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $payment->published = 'yes';
//                $payment->published_by = Auth::user()->id;
//                $payment->published_date = date("Y-m-d H:i:s");
//            }
//            $payment->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
