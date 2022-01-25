<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Country;
use App\Recruit;
use App\NormalUser;
use App\Http\Controllers\Controller;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RecruitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruit.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $recruits=Recruit::select('recruit.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $recruits = $recruits->where('recruit.id', '=', $id);
        }
        if (isset($data['company_name']) && !empty($data['company_name'])) {
            $company_name = $data['company_name'];
            $recruits = $recruits->where('recruit.company_name', 'LIKE', "%$company_name%");
        }
        if (isset($data['wanted_job']) && !empty($data['wanted_job'])) {
            $wanted_job = $data['wanted_job'];
            $recruits = $recruits->where('recruit.wanted_job', 'LIKE', "%$wanted_job%");
        }
        if (isset($data['salary_from']) && !empty($data['salary_from'])) {
            $salary_from = $data['salary_from'];
            $recruits = $recruits->where('recruit.salary_from', 'LIKE', "%$salary_from%");
        }
        if (isset($data['salary_to']) && !empty($data['salary_to'])) {
            $salary_to = $data['salary_to'];
            $recruits = $recruits->where('recruit.salary_to', 'LIKE', "%$salary_to%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $recruits = $recruits->whereBetween('recruit.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $recruits->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'recruit.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruit.id';
                break;
            case 1:
                $columnName = 'recruit.company_name';
                break;
            case 2:
                $columnName = 'recruit.wanted_job';
                break;
            case 3:
                $columnName = 'recruit.salary_from';
                break;
            case 4:
                $columnName = 'recruit.salary_to';
                break;
            case 5:
                $columnName = 'recruit.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $recruits = $recruits->where(function ($q) use ($search) {
                $q->where('recruit.company_name', 'LIKE', "%$search%")
                    ->orWhere('recruit.wanted_job', 'LIKE', "%$search%")
                    ->orWhere('recruit.salary_from', 'LIKE', "%$search%")
                    ->orWhere('recruit.salary_to', 'LIKE', "%$search%")
                    ->orWhere('recruit.id', '=', $search);
            });
        }

        $recruits = $recruits->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($recruits as $recruit) {
            $records["data"][] = [
                $recruit->id,
                $recruit->company_name,
                $recruit->wanted_job,
                $recruit->salary_from,
                $recruit->salary_to,
                $recruit->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $recruit->id . '" type="checkbox" ' . ((!PerUser('recruit_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruit_publish')) ? 'class="changeStatues"' : '') . ' ' . (($recruit->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $recruit->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $recruit->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruit_edit')) ? '<li>
                                            <a href="' . URL('admin/recruit/' . $recruit->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruit_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $recruit->id . '" >
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
        return view('auth.recruit.add');
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
            'company_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'wanted_job' => 'required',
            'salary_type' => 'required|in:salary,under_negotiation',
            'salary_from' => 'required|numeric',
            'salary_to' => 'required|numeric',
            'experience_years_from' => 'required|numeric',
            'experience_years_to' => 'required|numeric',
            'address' => 'required',
            'logo' => 'nullable|mimes:jpeg,jpg,png,gif|max:5000',
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
            $field = (isset($data['field'])) ? $data['field'] : '';
            $company_description = (isset($data['company_description'])) ? $data['company_description'] : '';
            $description = (isset($data['description'])) ? $data['description'] : '';
            $responsible_name = (isset($data['responsible_name'])) ? $data['responsible_name'] : '';
            $url = (isset($data['url'])) ? $data['url'] : '';
            $company_url = (isset($data['company_url'])) ? $data['company_url'] : '';
            $recruit = new Recruit();
            $recruit->user_id = $user_id;
            $recruit->country_id = $country_id;
            $recruit->city_id = $city_id;
            $recruit->state_id = $state_id;
            $recruit->company_name = $data['company_name'];
            $recruit->filed = $field;
            $recruit->company_description = $company_description;
            $recruit->responsible_name = $responsible_name;
            $recruit->phone = $data['phone'];
            $recruit->email = $data['email'];
            $recruit->address = $data['address'];
            $recruit->wanted_job = $data['wanted_job'];
            $recruit->salary_type = $data['salary_type'];
            $recruit->salary_from = $data['salary_from'];
            $recruit->salary_to = $data['salary_to'];
            $recruit->experience_years_from = $data['experience_years_from'];
            $recruit->experience_years_to = $data['experience_years_to'];
            $recruit->description = $description;
            $recruit->url = $url;
            $recruit->company_url = $company_url;
            if($request->file('logo')){
                $pic = $request->file('logo');
                $picName = uploadFileToE3melbusiness($pic);
            }
            else{
                $picName='';
            }
            $recruit->logo = $picName;
            $recruit->published = $published;
            $recruit->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $recruit->published_by = Auth::user()->id;
                $recruit->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $recruit->unpublished_by = Auth::user()->id;
                $recruit->unpublished_date = date("Y-m-d H:i:s");
            }
            $recruit->lastedit_by = Auth::user()->id;
            $recruit->added_by = Auth::user()->id;
            $recruit->lastedit_date = date("Y-m-d H:i:s");
            $recruit->added_date = date("Y-m-d H:i:s");
            if ($recruit->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruit'));
                return Redirect::to('admin/recruit/create');
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
        $recruit=Recruit::findOrFail($id);
        $user=isset($recruit->user)?$recruit->user->Email:'';
        $country=isset($recruit->country)?$recruit->country->arab_name:'';
        $city=isset($recruit->city)?$recruit->city->arab_name:'';
        $state=isset($recruit->state)?$recruit->state->arab_name:'';
        return view('auth.recruit.edit',compact('recruit','user','country','city','state'));
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
        $recruit = Recruit::findOrFail($id);
        $rules=array(
            'user' => 'nullable|exists:mysql2.users,Email',
            'country' => 'nullable|exists:mysql2.country,arab_name',
            'city' => 'nullable|exists:mysql2.cities,arab_name',
            'state' => 'nullable|exists:mysql2.states,arab_name',
            'company_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'wanted_job' => 'required',
            'salary_type' => 'required|in:salary,under_negotiation',
            'salary_from' => 'required|numeric',
            'salary_to' => 'required|numeric',
            'experience_years_from' => 'required|numeric',
            'experience_years_to' => 'required|numeric',
            'address' => 'required',
            'logo' => 'nullable|mimes:jpeg,jpg,png,gif|max:5000',
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
            $field = (isset($data['field'])) ? $data['field'] : '';
            $company_description = (isset($data['company_description'])) ? $data['company_description'] : '';
            $description = (isset($data['description'])) ? $data['description'] : '';
            $responsible_name = (isset($data['responsible_name'])) ? $data['responsible_name'] : '';
            $url = (isset($data['url'])) ? $data['url'] : '';
            $company_url = (isset($data['company_url'])) ? $data['company_url'] : '';
            $recruit->user_id = $user_id;
            $recruit->country_id = $country_id;
            $recruit->city_id = $city_id;
            $recruit->state_id = $state_id;
            $recruit->company_name = $data['company_name'];
            $recruit->filed = $field;
            $recruit->company_description = $company_description;
            $recruit->responsible_name = $responsible_name;
            $recruit->phone = $data['phone'];
            $recruit->email = $data['email'];
            $recruit->address = $data['address'];
            $recruit->wanted_job = $data['wanted_job'];
            $recruit->salary_type = $data['salary_type'];
            $recruit->salary_from = $data['salary_from'];
            $recruit->salary_to = $data['salary_to'];
            $recruit->experience_years_from = $data['experience_years_from'];
            $recruit->experience_years_to = $data['experience_years_to'];
            $recruit->description = $description;
            $recruit->url = $url;
            $recruit->company_url = $company_url;
            if($request->file('logo')){
                $pic = $request->file('logo');
                $picName = uploadFileToE3melbusiness($pic);
            }
            else{
                $picName='';
            }
            $recruit->logo = $picName;
            if ($published == 'yes' && $recruit->published=='no') {
                $recruit->published_by = Auth::user()->id;
                $recruit->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $recruit->published=='yes') {
                $recruit->unpublished_by = Auth::user()->id;
                $recruit->unpublished_date = date("Y-m-d H:i:s");
            }
            $recruit->published = $published;
            $recruit->lastedit_by = Auth::user()->id;
            $recruit->lastedit_date = date("Y-m-d H:i:s");
            if ($recruit->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruit'));
                return Redirect::to("admin/recruit/$recruit->id/edit");
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
        $recruit = Recruit::findOrFail($id);
        $recruit->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $recruit = Recruit::findOrFail($id);
            if ($published == 'no') {
                $recruit->published = 'no';
                $recruit->unpublished_by = Auth::user()->id;
                $recruit->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $recruit->published = 'yes';
                $recruit->published_by = Auth::user()->id;
                $recruit->published_date = date("Y-m-d H:i:s");
            }
            $recruit->save();
        } else {
            return redirect(404);
        }
    }
}
