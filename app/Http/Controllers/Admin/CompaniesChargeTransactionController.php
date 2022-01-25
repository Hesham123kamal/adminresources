<?php

namespace App\Http\Controllers\Admin;

use App\CompaniesChargeTransaction;
use App\NormalUser;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CompaniesChargeTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::pluck('name', 'id');
        return view('auth.companies_charge_transaction.view',compact('companies'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $transactions=CompaniesChargeTransaction::leftjoin('users', 'users.id', '=', 'companies_charge_transaction.user_id')
            ->leftjoin('companies', 'companies.id', '=', 'companies_charge_transaction.company_id')
            ->select('companies_charge_transaction.*', 'users.Email','companies.name as company_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $transactions = $transactions->where('companies_charge_transaction.id', '=', $id);
        }
        if (isset($data['amount']) && !empty($data['amount'])) {
            $amount = $data['amount'];
            $transactions = $transactions->where('companies_charge_transaction.amount', 'LIKE', "%$amount%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $transactions = $transactions->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $transactions = $transactions->where('companies_charge_transaction.company_id', '=', $company);
        }
        if (isset($data['currency']) && !empty($data['currency'])) {
            $currency = $data['currency'];
            $transactions = $transactions->where('companies_charge_transaction.currency', 'LIKE', "%$currency%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $transactions = $transactions->whereBetween('companies_charge_transaction.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['start_date_from']) && !empty($data['start_date_from']) && isset($data['start_date_to']) && !empty($data['start_date_to'])) {
            $start_date_from = $data['start_date_from'];
            $start_date_to = $data['start_date_to'];
            $transactions = $transactions->whereBetween('companies_charge_transaction.start_date', [$start_date_from .' 00:00:00', $start_date_to.' 23:59:59']);
        }
        if (isset($data['end_date_from']) && !empty($data['end_date_from']) && isset($data['end_date_to']) && !empty($data['end_date_to'])) {
            $end_date_from = $data['end_date_from'];
            $end_date_to = $data['end_date_to'];
            $transactions = $transactions->whereBetween('companies_charge_transaction.end_date', [$end_date_from .' 00:00:00', $end_date_to.' 23:59:59']);
        }
        if (isset($data['suspend']) && !empty($data['suspend'])) {
            $suspend = $data['suspend'];
            $transactions = $transactions->where('companies_charge_transaction.suspend', '=', $suspend);
        }
        if (isset($data['suspend_date_from']) && !empty($data['suspend_date_from']) && isset($data['suspend_date_to']) && !empty($data['suspend_date_to'])) {
            $suspend_date_from = $data['suspend_date_from'];
            $suspend_date_to = $data['suspend_date_to'];
            $transactions = $transactions->whereBetween('companies_charge_transaction.suspend_date', [$suspend_date_from .' 00:00:00', $suspend_date_to.' 23:59:59']);
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
        $columnName = 'companies_charge_transaction.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'companies_charge_transaction.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'companies_charge_transaction.amount';
                break;
            case 3:
                $columnName = 'companies_charge_transaction.currency';
                break;
            case 4:
                $columnName = 'companies_charge_transaction.createtime';
                break;
            case 5:
                $columnName = 'companies_charge_transaction.start_date';
                break;
            case 6:
                $columnName = 'companies_charge_transaction.end_date';
                break;
            case 7:
                $columnName = 'companies_charge_transaction.suspend';
                break;
            case 8:
                $columnName = 'companies_charge_transaction.suspend_date';
                break;
            case 9:
                $columnName = 'companies.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('companies_charge_transaction.amount', 'LIKE', "%$search%")
                    ->orWhere('companies_charge_transaction.currency', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('companies.name', 'LIKE', "%$search%")
                    ->orWhere('companies_charge_transaction.id', '=', $search)
                    ->orWhere('companies_charge_transaction.suspend', '=', $search);
            });
        }

        $transactions = $transactions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($transactions as $transaction) {
            $user = $transaction->Email;
            $company = $transaction->company_name;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $transaction->user_id . '/edit') . '">' . $user . '</a>';
            }
            if(PerUser('company_edit') && $company !=''){
                $company= '<a target="_blank" href="' . URL('admin/company/' . $transaction->company_id . '/edit') . '">' . $company . '</a>';
            }
            $records["data"][] = [
                $transaction->id,
                $user,
                $transaction->amount,
                $transaction->currency,
                $transaction->createtime,
                $transaction->start_date,
                $transaction->end_date,
                $transaction->suspend,
                $transaction->suspend_date,
                $company,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $transaction->id . '" type="checkbox" ' . ((!PerUser('companies_charge_transaction_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('companies_charge_transaction_publish')) ? 'class="changeStatues"' : '') . ' ' . (($transaction->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $transaction->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $transaction->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                   
                                    ' . ((PerUser('companies_charge_transaction_delete')) ? '<li>
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

    public function destroy($id)
    {
        $transaction = CompaniesChargeTransaction::findOrFail($id);
        $transaction->delete();
    }

}
