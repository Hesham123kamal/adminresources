<?php

namespace App\Http\Controllers\Admin;

use App\InternationalDiplomasChargeTransaction;
use App\InternationalDiplomasChargeTransactionSuspendLog;
use App\NormalUser;
use App\InternationalDiplomas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class InternationalDiplomasChargeTransactionSuspendLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diplomas = InternationalDiplomas::pluck('name', 'id');
        return view('auth.international_diplomas_charge_transaction_suspend_log.view',compact('diplomas'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $logs=InternationalDiplomasChargeTransactionSuspendLog::leftjoin('users', 'users.id', '=', 'international_diplomas_charge_transaction_suspend_log.user_id')
            ->leftjoin('international_diplomas', 'international_diplomas.id', '=', 'international_diplomas_charge_transaction_suspend_log.diploma_id')
            ->select('international_diplomas_charge_transaction_suspend_log.*', 'users.Email','international_diplomas.name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $logs = $logs->where('international_diplomas_charge_transaction_suspend_log.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $logs = $logs->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $logs = $logs->where('international_diplomas_charge_transaction_suspend_log.diploma_id', '=', $diploma);
        }
        if (isset($data['transaction_id']) && !empty($data['transaction_id'])) {
            $transaction_id = $data['transaction_id'];
            $logs = $logs->where('international_diplomas_charge_transaction_suspend_log.transaction_id', '=', $transaction_id);
        }
        if (isset($data['suspend']) && ($data['suspend']=='0' || $data['suspend']=='1')) {
            $suspend = $data['suspend'];
            $logs = $logs->where('international_diplomas_charge_transaction_suspend_log.suspend', '=', $suspend);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $logs = $logs->whereBetween('international_diplomas_charge_transaction_suspend_log.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $logs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'international_diplomas_charge_transaction_suspend_log.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'international_diplomas_charge_transaction_suspend_log.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'international_diplomas.name';
                break;
            case 3:
                $columnName = 'international_diplomas_charge_transaction_suspend_log.transaction_id';
                break;
            case 4:
                $columnName = 'international_diplomas_charge_transaction_suspend_log.suspend';
                break;
            case 5:
                $columnName = 'international_diplomas_charge_transaction_suspend_log.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $logs = $logs->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas_charge_transaction_suspend_log.id', '=', $search)
                    ->orWhere('international_diplomas_charge_transaction_suspend_log.transaction_id', '=', $search)
                    ->orWhere('international_diplomas_charge_transaction_suspend_log.suspend', '=', $search);
            });
        }

        $logs = $logs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($logs as $log) {
            $user = $log->Email;
            $diploma = $log->name;
            $trasaction = $log->transaction_id;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $log->user_id . '/edit') . '">' . $user . '</a>';
            }
            if(PerUser('international_diplomas_edit') && $diploma !=''){
                $diploma= '<a target="_blank" href="' . URL('admin/international_diplomas/' . $log->diploma_id . '/edit') . '">' . $diploma . '</a>';
            }
            if(PerUser('diplomas_charge_transaction_edit') && $trasaction !=''){
                $trasaction= '<a target="_blank" href="' . URL('admin/idct/' . $log->transaction_id . '/edit') . '">' . $trasaction . '</a>';
            }
            $records["data"][] = [
                $log->id,
                $user,
                $diploma,
                $trasaction,
                $log->suspend,
                $log->createtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $log->id . '" type="checkbox" ' . ((!PerUser('international_diplomas_charge_transaction_suspend_log_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('international_diplomas_charge_transaction_suspend_log_publish')) ? 'class="changeStatues"' : '') . ' ' . (($log->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $log->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $log->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('international_diplomas_charge_transaction_suspend_log_edit')) ? '<li>
                                            <a href="' . URL('admin/idctsl/' . $log->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('international_diplomas_charge_transaction_suspend_log_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $log->id . '" >
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
        $transactions_ids=InternationalDiplomasChargeTransaction::pluck('id', 'id');
        $diplomas=InternationalDiplomas::pluck('name', 'id');
        return view('auth.international_diplomas_charge_transaction_suspend_log.add',compact('transactions_ids','diplomas'));
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
            'user' => 'required|exists:mysql2.users,Email',
            'diploma' => 'required|exists:mysql2.international_diplomas,id',
            'transaction_id' => 'required|exists:mysql2.international_diplomas_charge_transaction,id',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $suspend = (isset($data['suspend'])) ? 1 : 0;
            $log = new InternationalDiplomasChargeTransactionSuspendLog();
            $log->user_id = $user_id;
            $log->diploma_id = $data['diploma'];
            $log->transaction_id = $data['transaction_id'];
            $log->suspend = $suspend;
            //$log->published = $published;
            $log->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $log->published_by = Auth::user()->id;
//                $log->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $log->unpublished_by = Auth::user()->id;
//                $log->unpublished_date = date("Y-m-d H:i:s");
//            }
            $log->lastedit_by = Auth::user()->id;
            $log->added_by = Auth::user()->id;
            $log->lastedit_date = date("Y-m-d H:i:s");
            $log->added_date = date("Y-m-d H:i:s");
            if ($log->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.international_diploma_charge_transaction_suspend_log'));
                return Redirect::to('admin/idctsl/create');
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
        $transaction = InternationalDiplomasChargeTransactionSuspendLog::findOrFail($id);
        $transactions_ids=InternationalDiplomasChargeTransaction::pluck('id', 'id');
        $diplomas=InternationalDiplomas::pluck('name', 'id');
        $user=isset($transaction->user)?$transaction->user->Email:'';
        return view('auth.international_diplomas_charge_transaction_suspend_log.edit', compact('transaction','transactions_ids','diplomas','user'));
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
        $log = InternationalDiplomasChargeTransactionSuspendLog::findOrFail($id);
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'diploma' => 'required|exists:mysql2.international_diplomas,id',
            'transaction_id' => 'required|exists:mysql2.international_diplomas_charge_transaction,id',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $suspend = (isset($data['suspend'])) ? 1 : 0;
            $log->user_id = $user_id;
            $log->diploma_id = $data['diploma'];
            $log->transaction_id = $data['transaction_id'];
            $log->suspend = $suspend;
//            if ($published == 'yes' && $log->published=='no') {
//                $log->published_by = Auth::user()->id;
//                $log->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $log->published=='yes') {
//                $log->unpublished_by = Auth::user()->id;
//                $log->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $log->published = $published;
            $log->lastedit_by = Auth::user()->id;
            $log->lastedit_date = date("Y-m-d H:i:s");
            if ($log->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.international_diploma_charge_transaction_suspend_log'));
                return Redirect::to("admin/idctsl/$log->id/edit");
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
        $log = InternationalDiplomasChargeTransactionSuspendLog::findOrFail($id);
        $log->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $log = InternationalDiplomasChargeTransactionSuspendLog::findOrFail($id);
//            if ($published == 'no') {
//                $log->published = 'no';
//                $log->unpublished_by = Auth::user()->id;
//                $log->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $log->published = 'yes';
//                $log->published_by = Auth::user()->id;
//                $log->published_date = date("Y-m-d H:i:s");
//            }
//            $log->save();
//        } else {
//            return redirect(404);
//        }
//    }

}
