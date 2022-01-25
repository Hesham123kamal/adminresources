<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\RecruitmentCompany;
use App\RecruitmentJob;
use App\City;
use App\State;
use App\RecruitmentCurrency;
use App\Http\Controllers\Controller;
use Faker\Provider\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RecruitmentJobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('auth.recruitment_jobs.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $jobs = RecruitmentJob::leftjoin('recruitment_companies','recruitment_companies.id','=','recruitment_company_id')
                                ->leftjoin('country','country.id','=','recruitment_jobs.country_id')
                                ->select('recruitment_jobs.*','recruitment_companies.name as company_name','country.arab_name as country_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $jobs = $jobs->where('recruitment_jobs.id', '=', $id);
        }
        if (isset($data['title']) && !empty($data['title'])) {
            $title = $data['title'];
            $jobs = $jobs->where('recruitment_jobs.title', 'LIKE', "%$title%");
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $jobs = $jobs->where('recruitment_companies.name', 'LIKE', "%$company%");
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $jobs = $jobs->where('country.arab_name', 'LIKE', "%$country%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $jobs = $jobs->whereBetween('recruitment_jobs.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $jobs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'recruitment_jobs.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruitment_jobs.id';
                break;
            case 1:
                $columnName = 'recruitment_jobs.title';
                break;
            case 2:
                $columnName = 'recruitment_jobs.createdtime';
                break;
            case 3:
                $columnName = 'recruitment_companies.name';
                break;
            case 4:
                $columnName = 'country.arab_name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $jobs = $jobs->where(function ($q) use ($search) {
                $q->where('recruitment_jobs.title', 'LIKE', "%$search%")
                    ->orWhere('recruitment_jobs.id', '=', $search)
                    ->orWhere('recruitment_companies.name', 'LIKE', "%$search%")
                    ->orWhere('country.arab_name', 'LIKE', "%$search%");
            });
        }

        $jobs = $jobs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($jobs as $job) {
            $company_name = $job->company_name;
            if(PerUser('recruitment_companies_edit') && $company_name !=''){
                $company_name= '<a target="_blank" href="' . URL('admin/recruitment_companies/' . $job->recruitment_company_id . '/edit') . '">' . $company_name . '</a>';
            }
            $records["data"][] = [
                $job->id,
                $job->title,
                $job->createdtime,
                $company_name,
                $job->country_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $job->id . '" type="checkbox" ' . ((!PerUser('recruitment_jobs_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_jobs_publish')) ? 'class="changeStatues"' : '') . ' ' . (($job->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $job->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $job->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_jobs_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_jobs/' . $job->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_jobs_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $job->id . '" >
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
        $company = RecruitmentCompany::pluck('name', 'id');
        $currency = RecruitmentCurrency::pluck('name', 'id');
        return view('auth.recruitment_jobs.add',compact( 'company','currency'));
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
                'company' =>'required|exists:mysql2.recruitment_companies,id',
                'title' => 'required',
                'country' =>'required|exists:mysql2.country,arab_name',
                'city' =>'required|exists:mysql2.cities,arab_name',
                'state' =>'required|exists:mysql2.states,arab_name',
                'career' =>'required|in:student,entry_level,experienced_non_manager,manager,senior_management',
                'experience_years' =>'required',
                'salary_min' =>'required',
                'salary_max' =>'required',
                'currency' =>'required|exists:mysql2.recruitment_currencies,id',
                'time_period' =>'required|in:month,hour,day,week,year',
                'hidden_salary' =>'required',
                'salary_info' =>'required',
                'num_vacancies' =>'required',
                'description' =>'required',
                'requirements' =>'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $job = new RecruitmentJob();
            $country_id=Country::where('arab_name', $data['country'])->first()->id;
            $city_id=City::where('arab_name', $data['city'])->first()->id;
            $state_id=State::where('arab_name', $data['state'])->first()->id;
            $job->user_id = Auth::user()->id;
            $job->recruitment_company_id = $data['company'];
            $job->title = $data['title'];
            $job->city_id = $city_id;
            $job->state_id = $state_id;
            $job->country_id = $country_id;
            $job->career = $data['career'];
            $job->experience_years = $data['experience_years'];
            $job->salary_min = $data['salary_min'];
            $job->salary_max = $data['salary_max'];
            $job->currency_id = $data['currency'];
            $job->time_period = $data['time_period'];
            $job->hidden_salary = $data['hidden_salary'];
            $job->salary_info = $data['salary_info'];
            $job->num_vacancies = $data['num_vacancies'];
            $job->description = $data['description'];
            $job->requirement = $data['requirements'];
            $job->published = $published;
            $job->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $job->published_by = Auth::user()->id;
                $job->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $job->unpublished_by = Auth::user()->id;
                $job->unpublished_date = date("Y-m-d H:i:s");
            }
            $job->lastedit_by = Auth::user()->id;
            $job->added_by = Auth::user()->id;
            $job->lastedit_date = date("Y-m-d H:i:s");
            $job->added_date = date("Y-m-d H:i:s");
            if ($job->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_job'));
                return Redirect::to('admin/recruitment_jobs/create');
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
        $job = RecruitmentJob::findOrFail($id);
        $company = RecruitmentCompany::pluck('name', 'id');
        $currency = RecruitmentCurrency::pluck('name', 'id');
        $country=isset($job->country)?$job->country->arab_name:'';
        $city=isset($job->city)?$job->city->arab_name:'';
        $state=isset($job->state)?$job->state->arab_name:'';
        return view('auth.recruitment_jobs.edit',compact( 'job','country','city','state','company','currency'));
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
        $job = RecruitmentJob::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'company' =>'required|exists:mysql2.recruitment_companies,id',
                'title' => 'required',
                'country' =>'required|exists:mysql2.country,arab_name',
                'city' =>'required|exists:mysql2.cities,arab_name',
                'state' =>'required|exists:mysql2.states,arab_name',
                'career' =>'required|in:student,entry_level,experienced_non_manager,manager,senior_management',
                'experience_years' =>'required',
                'salary_min' =>'required',
                'salary_max' =>'required',
                'currency' =>'required|exists:mysql2.recruitment_currencies,id',
                'time_period' =>'required|in:month,hour,day,week,year',
                'hidden_salary' =>'required',
                'salary_info' =>'required',
                'num_vacancies' =>'required',
                'description' =>'required',
                'requirements' =>'required',
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $country_id=Country::where('arab_name', $data['country'])->first()->id;
            $city_id=City::where('arab_name', $data['city'])->first()->id;
            $state_id=State::where('arab_name', $data['state'])->first()->id;
            $job->recruitment_company_id = $data['company'];
            $job->title = $data['title'];
            $job->city_id = $city_id;
            $job->state_id = $state_id;
            $job->country_id = $country_id;
            $job->career = $data['career'];
            $job->experience_years = $data['experience_years'];
            $job->salary_min = $data['salary_min'];
            $job->salary_max = $data['salary_max'];
            $job->currency_id = $data['currency'];
            $job->time_period = $data['time_period'];
            $job->hidden_salary = $data['hidden_salary'];
            $job->salary_info = $data['salary_info'];
            $job->num_vacancies = $data['num_vacancies'];
            $job->description = $data['description'];
            $job->requirement = $data['requirements'];
            if ($published == 'yes' && $job->published=='no') {
                $job->published_by = Auth::user()->id;
                $job->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $job->published=='yes') {
                $job->unpublished_by = Auth::user()->id;
                $job->unpublished_date = date("Y-m-d H:i:s");
            }
            $job->published = $published;
            $job->lastedit_by = Auth::user()->id;
            $job->lastedit_date = date("Y-m-d H:i:s");
            if ($job->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_job'));
                return Redirect::to("admin/recruitment_jobs/$job->id/edit");
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
        $job = RecruitmentJob::findOrFail($id);
        $job->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $job = RecruitmentJob::findOrFail($id);
            if ($published == 'no') {
                $job->published = 'no';
                $job->unpublished_by = Auth::user()->id;
                $job->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $job->published = 'yes';
                $job->published_by = Auth::user()->id;
                $job->published_date = date("Y-m-d H:i:s");
            }
            $job->save();
        } else {
            return redirect(404);
        }
    }
}
