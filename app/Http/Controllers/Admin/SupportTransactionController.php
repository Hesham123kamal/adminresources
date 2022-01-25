<?php

namespace App\Http\Controllers\Admin;

use App\Employee;
use App\SupportTransaction;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;

class SupportTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.support_transaction.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $support_transactions =SupportTransaction::leftjoin('users', 'users.id', '=', 'support_transaction.user_id')
            ->leftjoin('employees', 'employees.id', '=', 'support_transaction.employe_id')
            ->select('support_transaction.*', 'users.Email', 'employees.username');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $support_transactions = $support_transactions->where('support_transaction.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $support_transactions = $support_transactions->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['employee']) && !empty($data['employee'])) {
            $employee = $data['employee'];
            $support_transactions = $support_transactions->where('employees.username', 'LIKE', "%$employee%");
        }
        if (isset($data['old_email']) && !empty($data['old_email'])) {
            $old_email = $data['old_email'];
            $support_transactions = $support_transactions->where('support_transaction.old_email', 'LIKE', "%$old_email%");
        }
        if (isset($data['new_email']) && !empty($data['new_email'])) {
            $new_email = $data['new_email'];
            $support_transactions = $support_transactions->where('support_transaction.new_email', 'LIKE', "%$new_email%");
        }
        if (isset($data['old_phone']) && !empty($data['old_phone'])) {
            $old_phone = $data['old_phone'];
            $support_transactions = $support_transactions->where('support_transaction.old_phone', 'LIKE', "%$old_phone%");
        }
        if (isset($data['new_phone']) && !empty($data['new_phone'])) {
            $new_phone = $data['new_phone'];
            $support_transactions = $support_transactions->where('support_transaction.new_phone', 'LIKE', "%$new_phone%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $support_transactions = $support_transactions->whereBetween('support_transaction.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $support_transactions->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'support_transaction.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'support_transaction.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'employees.username';
                break;
            case 3:
                $columnName = 'support_transaction.old_email';
                break;
            case 4:
                $columnName = 'support_transaction.new_email';
                break;
            case 5:
                $columnName = 'support_transaction.old_phone';
                break;
            case 6:
                $columnName = 'support_transaction.new_phone';
                break;
            case 7:
                $columnName = 'support_transaction.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $support_transactions = $support_transactions->where(function ($q) use ($search) {
                $q->where('support_transaction.new_email', 'LIKE', "%$search%")
                    ->orWhere('support_transaction.old_email', 'LIKE', "%$search%")
                    ->orWhere('support_transaction.new_phone', 'LIKE', "%$search%")
                    ->orWhere('support_transaction.old_phone', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('employees.username', 'LIKE', "%$search%")
                    ->orWhere('support_transaction.id', '=', $search);
            });
        }

        $support_transactions = $support_transactions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($support_transactions as $support_transaction) {
            $user = $support_transaction->Email;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $support_transaction->user_id . '/edit') . '">' . $user . '</a>';
            }
            $employee = $support_transaction->username;
            if(PerUser('employee_edit') && $employee !=''){
                $employee= '<a target="_blank" href="' . URL('admin/employee/' . $support_transaction->employe_id . '/edit') . '">' . $employee . '</a>';
            }
            $records["data"][] = [
                $support_transaction->id,
                $user,
                $employee,
                $support_transaction->old_email,
                $support_transaction->new_email,
                $support_transaction->old_phone,
                $support_transaction->new_phone,
                $support_transaction->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $support_transaction->id . '" type="checkbox" ' . ((!PerUser('support_transaction_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('support_transaction_publish')) ? 'class="changeStatues"' : '') . ' ' . (($support_transaction->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $support_transaction->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $support_transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('support_transaction_edit')) ? '<li>
                                            <a href="' . URL('admin/support_transaction/' . $support_transaction->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('support_transaction_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $support_transaction->id . '" >
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
        $employees=Employee::pluck('username', 'id');
        return view('auth.support_transaction.add',compact('employees'));
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
                'user' =>  'required|exists:mysql2.users,Email',
                'employee' =>  'required|exists:mysql2.employees,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $send_credentials = (isset($data['send_credentials'])) ? 1 : 0;
            $support_transaction = new SupportTransaction();
            $support_transaction->user_id = $user_id;
            $support_transaction->employe_id = $data['employee'];
            $support_transaction->old_email = $data['old_email'];
            $support_transaction->new_email = $data['new_email'];
            $support_transaction->old_phone = $data['old_phone'];
            $support_transaction->new_phone = $data['new_phone'];
            $support_transaction->send_credentials = $send_credentials;
            //$support_transaction->published = $published;
            $support_transaction->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $support_transaction->published_by = Auth::user()->id;
//                $support_transaction->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $support_transaction->unpublished_by = Auth::user()->id;
//                $support_transaction->unpublished_date = date("Y-m-d H:i:s");
//            }
            $support_transaction->lastedit_by = Auth::user()->id;
            $support_transaction->added_by = Auth::user()->id;
            $support_transaction->lastedit_date = date("Y-m-d H:i:s");
            $support_transaction->added_date = date("Y-m-d H:i:s");
            if ($support_transaction->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.support_transaction'));
                return Redirect::to('admin/support_transaction/create');
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
        $transaction = SupportTransaction::findOrFail($id);
        $user=isset($transaction->user)?$transaction->user->Email:'';
        $employees=Employee::pluck('username', 'id');
        return view('auth.support_transaction.edit', compact('transaction','user','employees'));
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
        $support_transaction = SupportTransaction::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'user' =>  'required|exists:mysql2.users,Email',
                'employee' =>  'required|exists:mysql2.employees,id',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $send_credentials = (isset($data['send_credentials'])) ? 1 : 0;
            $support_transaction->user_id = $user_id;
            $support_transaction->employe_id = $data['employee'];
            $support_transaction->old_email = $data['old_email'];
            $support_transaction->new_email = $data['new_email'];
            $support_transaction->old_phone = $data['old_phone'];
            $support_transaction->new_phone = $data['new_phone'];
            $support_transaction->send_credentials = $send_credentials;
//            if ($published == 'yes' && $support_transaction->published=='no') {
//                $support_transaction->published_by = Auth::user()->id;
//                $support_transaction->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $support_transaction->published=='yes') {
//                $support_transaction->unpublished_by = Auth::user()->id;
//                $support_transaction->unpublished_date = date("Y-m-d H:i:s");
//            }
            //$support_transaction->published = $published;
            $support_transaction->lastedit_by = Auth::user()->id;
            $support_transaction->lastedit_date = date("Y-m-d H:i:s");
            if ($support_transaction->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.support_transaction'));
                return Redirect::to("admin/support_transaction/$support_transaction->id/edit");
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
        $support_transaction = SupportTransaction::findOrFail($id);
        $support_transaction->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $support_transaction = SupportTransaction::findOrFail($id);
//            if ($published == 'no') {
//                $support_transaction->published = 'no';
//                $support_transaction->unpublished_by = Auth::user()->id;
//                $support_transaction->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $support_transaction->published = 'yes';
//                $support_transaction->published_by = Auth::user()->id;
//                $support_transaction->published_date = date("Y-m-d H:i:s");
//            }
//            $support_transaction->save();
//        } else {
//            return redirect(404);
//        }
//    }

}
