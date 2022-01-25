<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $types=Company::select('type')->distinct()->get();
        return view('auth.company.view',compact('types'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $companies = Company::select('companies.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $companies = $companies->where('companies.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $companies = $companies->where('companies.name', 'LIKE', "%$name%");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $companies = $companies->where('companies.type', '=', "$type");
        }
        if (isset($data['employees_numbers']) && !empty($data['employees_numbers'])) {
            $employees_numbers = $data['employees_numbers'];
            $companies = $companies->where('companies.employees_numbers', 'LIKE', "%$employees_numbers%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $companies = $companies->whereBetween('companies.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        $iTotalRecords = $companies->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'companies.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'companies.id';
                break;
            case 1:
                $columnName = 'companies.name';
                break;
            case 2:
                $columnName = 'companies.type';
                break;
            case 3:
                $columnName = 'companies.employees_numbers';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $companies = $companies->where(function ($q) use ($search) {
                $q->where('companies.name', 'LIKE', "%$search%")
                    ->orWhere('companies.type', 'LIKE', "%$search%")
                    ->orWhere('companies.employees_numbers', '=', $search)
                    ->orWhere('companies.id', '=', $search);
            });
        }

        $companies = $companies->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($companies as $company) {
            $records["data"][] = [
                $company->id,
                $company->name,
                $company->type,
                $company->employees_numbers,
                $company->createtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $company->id . '" type="checkbox" ' . ((!PerUser('company_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('company_publish')) ? 'class="changeStatues"' : '') . ' ' . (($company->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $company->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $company->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_jobs_edit')) ? '<li>
                                            <a href="' . URL('admin/company/' . $company->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_jobs_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $company->id . '" >
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
        return view('auth.company.add');
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
                'employees_numbers' =>'required|numeric',
                'amount' =>'numeric',
                'period' =>'required|numeric',
                'expiredDate' =>'date_format:"Y-m-d"',
                'type' =>'in:live,demo,test',
                'address' =>'required',
                'description' =>'required'
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $isfree = (isset($data['isfree'])) ? 1 : 0;
            $company = new Company();
            $company->employees_numbers = $data['employees_numbers'];
            $company->name = $data['name'];
            $company->amount = $data['amount'];
            $company->period = $data['period'];
            $company->expiredDate = $data['expiredDate'];
            $company->type = $data['type'];
            $company->address = $data['address'];
            $company->description = $data['description'];
            //$company->published = $published;
            $company->isfree = $isfree;
            $company->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $company->published_by = Auth::user()->id;
//                $company->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $company->unpublished_by = Auth::user()->id;
//                $company->unpublished_date = date("Y-m-d H:i:s");
//            }
            $company->lastedit_by = Auth::user()->id;
            $company->added_by = Auth::user()->id;
            $company->lastedit_date = date("Y-m-d H:i:s");
            $company->added_date = date("Y-m-d H:i:s");
            if ($company->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.company'));
                return Redirect::to('admin/company/create');
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
        $company=Company::findOrFail($id);
        $company->expiredDate = date("Y-m-d", strtotime($company->expiredDate));
        return view('auth.company.edit',compact('company'));
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
        $company = Company::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'employees_numbers' =>'required|numeric',
                'amount' =>'numeric',
                'period' =>'required|numeric',
                'expiredDate' =>'date_format:"Y-m-d"',
                'type' =>'in:live,demo,test',
                'address' =>'required',
                'description' =>'required'
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $isfree = (isset($data['isfree'])) ? 1 : 0;
            $company->employees_numbers = $data['employees_numbers'];
            $company->name = $data['name'];
            $company->amount = $data['amount'];
            $company->period = $data['period'];
            $company->expiredDate = $data['expiredDate'];
            $company->type = $data['type'];
            $company->address = $data['address'];
            $company->description = $data['description'];
            $company->isfree = $isfree;
//            if ($published == 'yes' && $company->published=='no') {
//                $company->published_by = Auth::user()->id;
//                $company->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $company->published=='yes') {
//                $company->unpublished_by = Auth::user()->id;
//                $company->unpublished_date = date("Y-m-d H:i:s");
//            }
           // $company->published = $published;
            $company->lastedit_by = Auth::user()->id;
            $company->lastedit_date = date("Y-m-d H:i:s");
            if ($company->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.company'));
                return Redirect::to("admin/company/$company->id/edit");
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
        $company = Company::findOrFail($id);
        $company->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $company = Company::findOrFail($id);
//            if ($published == 'no') {
//                $company->published = 'no';
//                $company->unpublished_by = Auth::user()->id;
//                $company->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $company->published = 'yes';
//                $company->published_by = Auth::user()->id;
//                $company->published_date = date("Y-m-d H:i:s");
//            }
//            $company->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
