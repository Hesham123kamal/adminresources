<?php

namespace App\Http\Controllers\Admin;

use App\Employee;
use App\UserLevel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.employee.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $employees = Employee::leftjoin('userlevels','userlevels.userlevelid','=','employees.UserLevel')
        ->select('employees.*','userlevels.userlevelname as level_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $employees = $employees->where('employees.id', '=', $id);
        }
        if (isset($data['username']) && !empty($data['username'])) {
            $username = $data['username'];
            $employees = $employees->where('employees.username', 'LIKE', "%$username%");
        }
        if (isset($data['support_level']) && !empty($data['support_level'])) {
            $support_level = $data['support_level'];
            $employees = $employees->where('employees.support_level', '=', $support_level);
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $password = $data['password'];
            $employees = $employees->where('employees.password', 'LIKE', "%$password%");
        }
        if (isset($data['userLevel']) && !empty($data['userLevel'])) {
            $userLevel = $data['userLevel'];
            $employees = $employees->where('userlevels.userlevelname','LIKE', $userLevel);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $employees = $employees->whereBetween('employees.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $employees->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'employees.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'employees.id';
                break;
            case 1:
                $columnName = 'employees.username';
                break;
            case 2:
                $columnName = 'employees.password';
                break;
            case 3:
                $columnName = 'userlevels.userlevelname';
                break;
            case 4:
                $columnName = 'employees.support_level';
                break;
            case 5:
                $columnName = 'employees.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $employees = $employees->where(function ($q) use ($search) {
                $q->where('employees.username', 'LIKE', "%$search%")
                    ->orWhere('employees.password', 'LIKE', "%$search%")
                    ->orWhere('userlevels.userlevelname', 'LIKE', "%$search%")
                    ->orWhere('employees.id', '=', $search)
                    ->orWhere('employees.support_level', '=', $search);
            });
        }

        $employees = $employees->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($employees as $employee) {
            $records["data"][] = [
                $employee->id,
                $employee->username,
                $employee->password,
                $employee->level_name,
                $employee->support_level,
                $employee->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $employee->id . '" type="checkbox" ' . ((!PerUser('employee_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('employee_publish')) ? 'class="changeStatues"' : '') . ' ' . (($employee->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $employee->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $employee->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('employee_edit')) ? '<li>
                                            <a href="' . URL('admin/employee/' . $employee->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('employee_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $employee->id . '" >
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
        $levels = UserLevel::pluck('userlevelname', 'userlevelid');
        return view('auth.employee.add',compact('levels'));
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
                'username' => 'required|unique:mysql2.employees,username',
                'password' => 'required',
                'user_level' =>'required|exists:mysql2.userlevels,userlevelid',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $employee = new Employee();
            $employee->logintype = (isset($data['login_type'])) ? $data['login_type'] : 0;
            $employee->support_level = (isset($data['support_level'])) ? $data['support_level'] : '';
            $employee->username = $data['username'];
            $employee->password = $data['password'];
            $employee->UserLevel = $data['user_level'];
            //$employee->published = $published;
            $employee->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $employee->published_by = Auth::user()->id;
//                $employee->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $employee->unpublished_by = Auth::user()->id;
//                $employee->unpublished_date = date("Y-m-d H:i:s");
//            }
            $employee->lastedit_by = Auth::user()->id;
            $employee->added_by = Auth::user()->id;
            $employee->lastedit_date = date("Y-m-d H:i:s");
            $employee->added_date = date("Y-m-d H:i:s");
            if ($employee->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.employee'));
                return Redirect::to('admin/employee/create');
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
        $employee = Employee::findOrFail($id);
        $levels = UserLevel::pluck('userlevelname', 'userlevelid');
        return view('auth.employee.edit', compact('employee','levels'));
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
        $employee = Employee::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'username' => "required|unique:mysql2.employees,username,$id,id",
                'password' => 'required',
                'user_level' =>'required|exists:mysql2.userlevels,userlevelid',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $employee->username = $data['username'];
            $employee->password = $data['password'];
            $employee->UserLevel = $data['user_level'];
            $employee->logintype = (isset($data['login_type'])) ? $data['login_type'] : 0;
            $employee->support_level = (isset($data['support_level'])) ? $data['support_level'] : '';
//            if ($published == 'yes' && $employee->published=='no') {
//                $employee->published_by = Auth::user()->id;
//                $employee->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $employee->published=='yes') {
//                $employee->unpublished_by = Auth::user()->id;
//                $employee->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $employee->published = $published;
            $employee->lastedit_by = Auth::user()->id;
            $employee->lastedit_date = date("Y-m-d H:i:s");
            if ($employee->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.employee'));
                return Redirect::to("admin/employee/$employee->id/edit");
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
        $employee = Employee::findOrFail($id);
        $employee->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $employee = Employee::findOrFail($id);
//            if ($published == 'no') {
//                $employee->published = 'no';
//                $employee->unpublished_by = Auth::user()->id;
//                $employee->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $employee->published = 'yes';
//                $employee->published_by = Auth::user()->id;
//                $employee->published_date = date("Y-m-d H:i:s");
//            }
//            $employee->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
