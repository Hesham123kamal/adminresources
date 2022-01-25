<?php

namespace App\Http\Controllers\Admin;

use App\CompanyRequest;
use App\NormalUser;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class CompanyRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::pluck('name', 'id');
        return view('auth.company_request.view',compact('companies'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $requests = CompanyRequest::join('users','users.id','=','companies_requests.user_id')
            ->join('companies','companies.id','=','companies_requests.company_id')
            ->select('companies_requests.*','users.Email as user_email','companies.name as company_name')->where('deleted',0);
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $requests = $requests->where('companies_requests.user_id', '=', "$user_id");
        }
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $requests = $requests->where('companies_requests.id', '=', "$id");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $requests = $requests->where('companies_requests.email', 'LIKE', "%$email%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $requests = $requests->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $requests = $requests->where('companies.id', '=', $company);
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $requests = $requests->where('companies_requests.type', 'LIKE', "%$type%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $requests = $requests->whereBetween('companies_requests.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $requests->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'companies_requests.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'companies_requests.id';
                break;
            case 1:
                $columnName = 'companies_requests.user_id';
                break;
            case 2:
                $columnName = 'companies_requests.email';
                break;
            case 3:
                $columnName = 'companies_requests.createtime';
                break;
            case 4:
                $columnName = 'users.Email';
                break;
            case 5:
                $columnName = 'companies.name';
                break;
            case 6:
                $columnName = 'companies_requests.type';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $requests = $requests->where(function ($q) use ($search) {
                $q->where('companies_requests.id', '=', $search)
                    ->orWhere('companies_requests.email', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('companies.name', 'LIKE', "%$search%")
                    ->orWhere('companies_requests.type', 'LIKE', "%$search%");
            });
        }

        $requests = $requests->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($requests as $r) {
            $user_email = $r->user_email;
            $company_name = $r->company_name;
            if( $r->type=='lite_version'){
                $r->type='Lite Version';
            }
            else{
                $r->type='Default';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email = '<a target="_blank" href="' . URL('admin/normal_user/' . $r->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            if(PerUser('company_edit') && $company_name !=''){
                $company_name = '<a target="_blank" href="' . URL('admin/company/' . $r->company_id . '/edit') . '">' . $company_name . '</a>';
            }
            $records["data"][] = [
                $r->id,
                $r->user_id,
                $r->email,
                $r->createtime,
                $user_email,
                $company_name,
                $r->type,
                '<div class="btn-group text-center" id="single-order-' . $r->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('company_request_edit')) ? '<li>
                                            <a href="' . URL('admin/company_request/' . $r->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('company_request_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $r->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '


                                    </ul>
                                </div>'
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
        return response()->json($records)->setCallback($request->input('callback'));

    }

    public function create()
    {
        $companies = Company::pluck('name', 'id');
        return view('auth.company_request.add',compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $rules=array(
            'company' =>'required|exists:mysql2.companies,id',
            'user' => 'required',
            'type' => 'required|in:default,lite_version',
            'email' => 'required',
            'confirm' => 'required|in:0,1',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $company_request= new CompanyRequest();
            $company_request->company_id = $data['company'];
            $company_request->user_id = $user->id;
            $company_request->type = $data['type'];
            $company_request->email = $data['email'];
            $company_request->confirm = $data['confirm'];
            if ($company_request->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.company_request'));
                return Redirect::to('admin/company_request/create');
            }
        }
    }

    public function edit($id)
    {
        $company_request = CompanyRequest::findOrFail($id);
        $companies = Company::pluck('name', 'id');
        $user=NormalUser::where('id', $company_request->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.company_request.edit', compact('company_request','companies','user'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        $company_request = CompanyRequest::findOrFail($id);
        $rules=array(
            'company' => 'required|exists:mysql2.companies,id',
            'user' => 'required',
            'type' => 'required|in:default,lite_version',
            'email' => 'required',
            'confirm' => 'required|in:0,1',
        );

        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $company_request->user_id = $user->id;
            $company_request->company_id = $data['company'];
            $company_request->type = $data['type'];
            $company_request->email = $data['email'];
            $company_request->confirm = $data['confirm'];
            if ($company_request->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.company_request'));
                return Redirect::to("admin/company_request/$company_request->id/edit");
            }
        }
    }

    public function destroy($id)
    {
        $company_request = CompanyRequest::findOrFail($id);
        $company_request->deleted=1;
        $company_request->deleted_date=date("Y-m-d H:i:s");
        $company_request->save();
    }


}
