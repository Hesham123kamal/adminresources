<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\RecruitmentCompany;
use App\RecruitmentIndustry;
use App\City;
use App\State;
use App\Http\Controllers\Controller;
use Faker\Provider\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RecruitmentCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('auth.recruitment_companies.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $rec_companies = RecruitmentCompany::join('country','country.id','=','recruitment_companies.country_id')
                                            ->join('recruitment_industries','recruitment_industries.id','=','recruitment_companies.industry_id')
                                            ->select('recruitment_companies.*','country.arab_name as country_name','recruitment_industries.arab_name as industry_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $rec_companies = $rec_companies->where('recruitment_companies.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $rec_companies = $rec_companies->where('recruitment_companies.name', 'LIKE', "%$name%");
        }
        if (isset($data['country']) && !empty($data['country'])) {
            $country = $data['country'];
            $rec_companies = $rec_companies->where('country.arab_name','LIKE', "%$country%");
        }
        if (isset($data['industry']) && !empty($data['industry'])) {
            $industry = $data['industry'];
            $rec_companies = $rec_companies->where('recruitment_industries.arab_name', 'LIKE',"%$industry%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $rec_companies = $rec_companies->whereBetween('recruitment_companies.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $rec_companies->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'recruitment_companies.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruitment_companies.id';
                break;
            case 1:
                $columnName = 'recruitment_companies.name';
                break;
            case 2:
                $columnName = 'country.arab_name';
                break;
            case 3:
                $columnName = 'recruitment_industries.arab_name';
                break;
            case 4:
                $columnName = 'recruitment_companies.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $rec_companies = $rec_companies->where(function ($q) use ($search) {
                $q->where('recruitment_companies.name', 'LIKE', "%$search%")
                    ->orWhere('recruitment_companies.id', '=', $search)
                    ->orWhere('recruitment_industries.arab_name', 'LIKE', "%$search%")
                    ->orWhere('country.arab_name', 'LIKE', "%$search%");
            });
        }

        $rec_companies = $rec_companies->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($rec_companies as $rec_company) {
            $rec_company=makeDefaultImageGeneral($rec_company,'logo');
            $industry_name=$rec_company->industry_name;
            $country_name=$rec_company->country_name;
            if(PerUser('recruitment_industries') && $industry_name !=''){
                $industry_name= '<a target="_blank" href="' . URL('admin/recruitment_industries/' . $rec_company->	industry_id . '/edit') . '">' . $industry_name . '</a>';
            }
            $records["data"][] = [
                $rec_company->id,
                $rec_company->name,
                $country_name,
                $industry_name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($rec_company->logo) . '"/></a>',
                $rec_company->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $rec_company->id . '" type="checkbox" ' . ((!PerUser('recruitment_companies_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_companies_publish')) ? 'class="changeStatues"' : '') . ' ' . (($rec_company->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $rec_company->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $rec_company->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_companies_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_companies/' . $rec_company->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_companies_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $rec_company->id . '" >
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
        $industries = RecruitmentIndustry::pluck('arab_name', 'id');
        return view('auth.recruitment_companies.add',compact( 'industries'));
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
                'name' =>'required',
                'country' =>'required|exists:mysql2.country,arab_name',
                'city' =>'required|exists:mysql2.cities,arab_name',
                'state' =>'required|exists:mysql2.states,arab_name',
                'industry' =>'required|exists:mysql2.recruitment_industries,id',
                'logo' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
                'company_size' =>'required|in:1_10,11_50,51_100,101_500,501_1000,more_than_1000',
                'description' =>'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $rec_company = new RecruitmentCompany();
            $rec_company->user_id = Auth::user()->id;
            $country_id=Country::where('arab_name', $data['country'])->first()->id;
            $city_id=City::where('arab_name', $data['city'])->first()->id;
            $state_id=State::where('arab_name', $data['state'])->first()->id;
            $rec_company->name = $data['name'];
            $rec_company->industry_id = $data['industry'];
            $rec_company->city_id = $city_id;
            $rec_company->state_id = $state_id;
            $rec_company->country_id = $country_id;
            $rec_company->company_size = $data['company_size'];
            $pic = $request->file('logo');
            $picName = uploadFileToE3melbusiness($pic);
            $rec_company->logo = $picName;
            $rec_company->website = isset($data['website'])?$data['website']:'';
            $rec_company->founded_year = isset($data['founded_year'])?$data['founded_year']:'0000';
            $rec_company->facebook = isset($data['facebook'])?$data['facebook']:'';
            $rec_company->linkedin = isset($data['linkedin'])?$data['linkedin']:'';
            $rec_company->blog = isset($data['blog'])?$data['blog']:'';
            $rec_company->twitter = isset($data['twitter'])?$data['twitter']:'';
            $rec_company->description = isset($data['description'])?$data['description']:'';
            $rec_company->published = $published;
            $rec_company->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $rec_company->published_by = Auth::user()->id;
                $rec_company->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $rec_company->unpublished_by = Auth::user()->id;
                $rec_company->unpublished_date = date("Y-m-d H:i:s");
            }
            $rec_company->lastedit_by = Auth::user()->id;
            $rec_company->added_by = Auth::user()->id;
            $rec_company->lastedit_date = date("Y-m-d H:i:s");
            $rec_company->added_date = date("Y-m-d H:i:s");
            if ($rec_company->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_company'));
                return Redirect::to('admin/recruitment_companies/create');
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
        $rec_company = RecruitmentCompany::findOrFail($id);
        $industries = RecruitmentIndustry::pluck('arab_name', 'id');
        $country=isset($rec_company->country)?$rec_company->country->arab_name:'';
        $city=isset($rec_company->city)?$rec_company->city->arab_name:'';
        $state=isset($rec_company->state)?$rec_company->state->arab_name:'';
        return view('auth.recruitment_companies.edit',compact( 'rec_company','industries','country','city','state'));
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
        $rec_company = RecruitmentCompany::findOrFail($id);
        $rules=array(
            'name' =>'required',
            'country' =>'required|exists:mysql2.country,arab_name',
            'city' =>'required|exists:mysql2.cities,arab_name',
            'state' =>'required|exists:mysql2.states,arab_name',
            'industry' =>'required|exists:mysql2.recruitment_industries,id',
            'company_size' =>'required|in:1_10,11_50,51_100,101_500,501_1000,more_than_1000',
            'description' =>'required',
        );
        if ( $request->file('logo')){
            $rules['logo'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $country_id=Country::where('arab_name', $data['country'])->first()->id;
            $city_id=City::where('arab_name', $data['city'])->first()->id;
            $state_id=State::where('arab_name', $data['state'])->first()->id;
            $rec_company->name = $data['name'];
            $rec_company->industry_id = $data['industry'];
            $rec_company->country_id = $country_id;
            $rec_company->city_id = $city_id;
            $rec_company->state_id = $state_id;
            $rec_company->company_size = $data['company_size'];
            if ( $request->file('logo')) {
                $pic = $request->file('logo');
                $picName = uploadFileToE3melbusiness($pic);
                $rec_company->logo = $picName;
            }
            $rec_company->website = isset($data['website'])?$data['website']:'';
            $rec_company->founded_year = isset($data['founded_year'])?$data['founded_year']:'0000';
            $rec_company->facebook = isset($data['facebook'])?$data['facebook']:'';
            $rec_company->linkedin = isset($data['linkedin'])?$data['linkedin']:'';
            $rec_company->blog = isset($data['blog'])?$data['blog']:'';
            $rec_company->twitter = isset($data['twitter'])?$data['twitter']:'';
            $rec_company->description = isset($data['description'])?$data['description']:'';
            if ($published == 'yes' && $rec_company->published=='no') {
                $rec_company->published_by = Auth::user()->id;
                $rec_company->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $rec_company->published=='yes') {
                $rec_company->unpublished_by = Auth::user()->id;
                $rec_company->unpublished_date = date("Y-m-d H:i:s");
            }
            $rec_company->published = $published;
            $rec_company->lastedit_by = Auth::user()->id;
            $rec_company->lastedit_date = date("Y-m-d H:i:s");
            if ($rec_company->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_company'));
                return Redirect::to("admin/recruitment_companies/$rec_company->id/edit");
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
        $rec_company = RecruitmentCompany::findOrFail($id);
        $rec_company->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $rec_company = RecruitmentCompany::findOrFail($id);
            if ($published == 'no') {
                $rec_company->published = 'no';
                $rec_company->unpublished_by = Auth::user()->id;
                $rec_company->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $rec_company->published = 'yes';
                $rec_company->published_by = Auth::user()->id;
                $rec_company->published_date = date("Y-m-d H:i:s");
            }
            $rec_company->save();
        } else {
            return redirect(404);
        }
    }
}
