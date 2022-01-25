<?php

namespace App\Http\Controllers\Admin;

use App\NormalUser;
use App\CompaniesAdmins;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class CompaniesAdminsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::pluck('name', 'id');
        return view('auth.companies_admins.view',compact('companies'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $companies_admins = CompaniesAdmins::leftjoin('users','users.id','=','companies_admins.user_id')
            ->leftjoin('companies','companies.id','=','companies_admins.company_id')
            ->select('companies_admins.*','companies.name as company_name','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $companies_admins = $companies_admins->where('companies_admins.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $companies_admins = $companies_admins->where('users.Email', '=', $user);
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $companies_admins = $companies_admins->where('companies.id','=', "$company");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $companies_admins = $companies_admins   ->whereBetween('companies_admins.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $companies_admins->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'companies_admins.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'companies_admins.id';
                break;
            case 1:
                $columnName = 'companies.name';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'companies_admins.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $companies_admins = $companies_admins->where(function ($q) use ($search) {
                $q->where('companies_admins.id', '=', $search)
                    ->orWhere('companies.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%");
            });
        }

        $companies_admins = $companies_admins->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($companies_admins as $company_admin) {
            $company_name = $company_admin->company_name;
            $user_email = $company_admin->user_email;
            if(PerUser('company_edit') && $company_name !=''){
                $company_name= '<a target="_blank" href="' . URL('admin/company/' . $company_admin->company_id . '/edit') . '">' . $company_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $company_admin->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $company_admin->id,
                $company_name,
                $user_email,
                $company_admin->createtime,
                '<div class="btn-group text-center" id="single-order-' . $company_admin->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('companies_admins_edit')) ? '<li>
                                            <a href="' . URL('admin/companies_admins/' . $company_admin->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('companies_admins_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $company_admin->id . '" >
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
        $companies = Company::pluck('name', 'id');
        return view('auth.companies_admins.add',compact('companies'));
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
            'company' =>'required|exists:mysql2.companies,id',
            'user' => 'required',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $company_admin= new CompaniesAdmins();
            $company_admin->company_id = $data['company'];
            $company_admin->user_id = $user->id;
            $company_admin->createtime = date("Y-m-d H:i:s");
            $company_admin->modifiedtime = date("Y-m-d H:i:s");
            if ($company_admin->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.company_admin'));
                return Redirect::to('admin/companies_admins/create');
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
        $company_admin = CompaniesAdmins::findOrFail($id);
        $companies = Company::pluck('name', 'id');
        $user=NormalUser::where('id', $company_admin->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.companies_admins.edit', compact('company_admin','companies','user'));
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
        $company_admin = CompaniesAdmins::findOrFail($id);
        $rules=array(
            'company' => 'required|exists:mysql2.companies,id',
            'user' => 'required',
        );

        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $company_admin->user_id = $user->id;
            $company_admin->company_id = $data['company'];
            $company_admin->modifiedtime = date("Y-m-d H:i:s");
            if ($company_admin->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.company_admin'));
                return Redirect::to("admin/companies_admins/$company_admin->id/edit");
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
        $company_admin = CompaniesAdmins::findOrFail($id);
        $company_admin->delete();
    }

}
