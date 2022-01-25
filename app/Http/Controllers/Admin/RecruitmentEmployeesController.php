<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Country;
use App\RecruitmentEmployees;
use App\NormalUser;
use App\Http\Controllers\Controller;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RecruitmentEmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruitment_employees.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $employees=RecruitmentEmployees::select('recruitment_employees.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $employees = $employees->where('recruitment_employees.id', '=', $id);
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $employees = $employees->where('recruitment_employees.email', 'LIKE', "%$email%");
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $password = $data['password'];
            $employees = $employees->where('recruitment_employees.password', 'LIKE', "%$password%");
        }
        if (isset($data['fullname']) && !empty($data['fullname'])) {
            $fullname = $data['fullname'];
            $employees = $employees->where('recruitment_employees.fullname', 'LIKE', "%$fullname%");
        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $employees = $employees->where('recruitment_employees.mobile', 'LIKE', "%$mobile%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $employees = $employees->whereBetween('recruitment_employees.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'recruitment_employees.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruitment_employees.id';
                break;
            case 1:
                $columnName = 'recruitment_employees.email';
                break;
            case 2:
                $columnName = 'recruitment_employees.password';
                break;
            case 3:
                $columnName = 'recruitment_employees.fullname';
                break;
            case 4:
                $columnName = 'recruitment_employees.mobile';
                break;
            case 5:
                $columnName = 'recruitment_employees.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $employees = $employees->where(function ($q) use ($search) {
                $q->where('recruitment_employees.email', 'LIKE', "%$search%")
                    ->orWhere('recruitment_employees.password', 'LIKE', "%$search%")
                    ->orWhere('recruitment_employees.fullname', 'LIKE', "%$search%")
                    ->orWhere('recruitment_employees.mobile', 'LIKE', "%$search%")
                    ->orWhere('recruitment_employees.id', '=', $search);
            });
        }

        $employees = $employees->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($employees as $employee) {
            $records["data"][] = [
                $employee->id,
                $employee->email,
                $employee->password,
                $employee->fullname,
                $employee->mobile,
                $employee->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $employee->id . '" type="checkbox" ' . ((!PerUser('recruitment_employees_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_employees_publish')) ? 'class="changeStatues"' : '') . ' ' . (($employee->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $employee->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $employee->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_employees_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_employees/' . $employee->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_employees_delete')) ? '<li>
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
        return view('auth.recruitment_employees.add');
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
            'user' => 'nullable|exists:mysql2.users,Email',
            'country' => 'nullable|exists:mysql2.country,arab_name',
            'city' => 'nullable|exists:mysql2.cities,arab_name',
            'state' => 'nullable|exists:mysql2.states,arab_name',
            'email' => 'required|email|unique:mysql2.recruitment_employees,email',
            'password' => 'required',
            'fullname' => 'required',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:unspecified,single,married',
            'mobile' => 'required|unique:mysql2.recruitment_employees,mobile',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:5000',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:5000',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=isset($data['user'])?NormalUser::where('Email', $data['user'])->first()->id:0;
            $country_id=isset($data['country'])?Country::where('arab_name', $data['country'])->first()->id:0;
            $city_id=isset($data['city'])?City::where('arab_name', $data['city'])->first()->id:0;
            $state_id=isset($data['state'])?State::where('arab_name', $data['state'])->first()->id:0;
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $birthdate = (isset($data['birthdate'])) ? $data['birthdate'] : '0000-00-00';
            $number_dependants = (isset($data['number_dependants'])) ? $data['number_dependants'] : 0;
            $postal_code = (isset($data['postal_code'])) ? $data['postal_code'] : 0;
            $mobile_1 = (isset($data['mobile_1'])) ? $data['mobile_1'] : 0;
            $address = (isset($data['address'])) ? $data['address'] : '';
            $description = (isset($data['description'])) ? $data['description'] : '';
            $employee = new RecruitmentEmployees();
            $employee->user_id = $user_id;
            $employee->country_id = $country_id;
            $employee->nationality_id = $country_id;
            $employee->email = $data['email'];
            $employee->password = $data['password'];
            $employee->fullname = $data['fullname'];
            $employee->birthdate = $birthdate;
            $employee->gender = $data['gender'];
            $employee->postal_code = $postal_code;
            $employee->mobile = $data['mobile'];
            $employee->mobile_1 = $mobile_1;
            $employee->address = $address;
            $employee->marital_status = $data['marital_status'];
            $employee->number_dependants = $number_dependants;
            $employee->city_id = $city_id;
            $employee->state_id = $state_id;
            $employee->description = $description;
            if($request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
            }
            else{
                $picName='';
            }
            $employee->image = $picName;
            if($request->file('cv')){
                $cv = $request->file('cv');
                $cvName = uploadFileToE3melbusiness($cv);
            }
            else{
                $cvName='';
            }
            $employee->cv = $cvName;

            $employee->published = $published;
            $employee->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $employee->published_by = Auth::user()->id;
                $employee->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $employee->unpublished_by = Auth::user()->id;
                $employee->unpublished_date = date("Y-m-d H:i:s");
            }
            $employee->lastedit_by = Auth::user()->id;
            $employee->added_by = Auth::user()->id;
            $employee->lastedit_date = date("Y-m-d H:i:s");
            $employee->added_date = date("Y-m-d H:i:s");
            if ($employee->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_employee'));
                return Redirect::to('admin/recruitment_employees/create');
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
        $employee=RecruitmentEmployees::findOrFail($id);
        $user=isset($employee->user)?$employee->user->Email:'';
        $country=isset($employee->country)?$employee->country->arab_name:'';
        $city=isset($employee->city)?$employee->city->arab_name:'';
        $state=isset($employee->state)?$employee->state->arab_name:'';
        return view('auth.recruitment_employees.edit',compact('employee','user','country','city','state'));
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
        $employee = RecruitmentEmployees::findOrFail($id);
        $rules=array(
            'user' => 'nullable|exists:mysql2.users,Email',
            'country' => 'nullable|exists:mysql2.country,arab_name',
            'city' => 'nullable|exists:mysql2.cities,arab_name',
            'state' => 'nullable|exists:mysql2.states,arab_name',
            'email' => "required|email|unique:mysql2.recruitment_employees,email,$id,id",
            'password' => 'required',
            'fullname' => 'required',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:unspecified,single,married',
            'mobile' => "required|unique:mysql2.recruitment_employees,mobile,$id,id",
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:5000',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:5000',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=isset($data['user'])?NormalUser::where('Email', $data['user'])->first()->id:0;
            $country_id=isset($data['country'])?Country::where('arab_name', $data['country'])->first()->id:0;
            $city_id=isset($data['city'])?City::where('arab_name', $data['city'])->first()->id:0;
            $state_id=isset($data['state'])?State::where('arab_name', $data['state'])->first()->id:0;
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $birthdate = (isset($data['birthdate'])) ? $data['birthdate'] : '0000-00-00';
            $number_dependants = (isset($data['number_dependants'])) ? $data['number_dependants'] : 0;
            $postal_code = (isset($data['postal_code'])) ? $data['postal_code'] : 0;
            $mobile_1 = (isset($data['mobile_1'])) ? $data['mobile_1'] : 0;
            $address = (isset($data['address'])) ? $data['address'] : '';
            $description = (isset($data['description'])) ? $data['description'] : '';
            $employee->user_id = $user_id;
            $employee->country_id = $country_id;
            $employee->nationality_id = $country_id;
            $employee->email = $data['email'];
            $employee->password = $data['password'];
            $employee->fullname = $data['fullname'];
            $employee->birthdate = $birthdate;
            $employee->gender = $data['gender'];
            $employee->postal_code = $postal_code;
            $employee->mobile = $data['mobile'];
            $employee->mobile_1 = $mobile_1;
            $employee->address = $address;
            $employee->marital_status = $data['marital_status'];
            $employee->number_dependants = $number_dependants;
            $employee->city_id = $city_id;
            $employee->state_id = $state_id;
            $employee->description = $description;
            if($request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $employee->image = $picName;
            }
            if($request->file('cv')){
                $cv = $request->file('cv');
                $cvName = uploadFileToE3melbusiness($cv);
                $employee->cv = $cvName;
            }
            if ($published == 'yes' && $employee->published=='no') {
                $employee->published_by = Auth::user()->id;
                $employee->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $employee->published=='yes') {
                $employee->unpublished_by = Auth::user()->id;
                $employee->unpublished_date = date("Y-m-d H:i:s");
            }
            $employee->published = $published;
            $employee->lastedit_by = Auth::user()->id;
            $employee->lastedit_date = date("Y-m-d H:i:s");
            $employee->modifiedtime = date("Y-m-d H:i:s");
            if ($employee->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_employee'));
                return Redirect::to("admin/recruitment_employees/$employee->id/edit");
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
        $employee = RecruitmentEmployees::findOrFail($id);
        $employee->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $employee = RecruitmentEmployees::findOrFail($id);
            if ($published == 'no') {
                $employee->published = 'no';
                $employee->unpublished_by = Auth::user()->id;
                $employee->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $employee->published = 'yes';
                $employee->published_by = Auth::user()->id;
                $employee->published_date = date("Y-m-d H:i:s");
            }
            $employee->save();
        } else {
            return redirect(404);
        }
    }
}
