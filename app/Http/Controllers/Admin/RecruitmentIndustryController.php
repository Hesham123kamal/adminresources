<?php

namespace App\Http\Controllers\Admin;

use App\RecruitmentIndustry;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class  RecruitmentIndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruitment_industries.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $industries = RecruitmentIndustry::select('recruitment_industries.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $industries = $industries->where('id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $industries = $industries->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['arab_name']) && !empty($data['arab_name'])) {
            $arab_name = $data['arab_name'];
            $industries = $industries->where('arab_name', 'LIKE', "%$arab_name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $industries = $industries->whereBetween('createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $industries->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'id';
                break;
            case 1:
                $columnName = 'name';
                break;
            case 1:
                $columnName = 'arab_name';
                break;
            case 4:
                $columnName = 'createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $industries = $industries->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('arab_name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $industries = $industries->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($industries as $industry) {
            $records["data"][] = [
                $industry->id,
                $industry->name,
                $industry->arab_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $industry->id . '" type="checkbox" ' . ((!PerUser('recruitment_industries_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_industries_publish')) ? 'class="changeStatues"' : '') . ' ' . (($industry->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $industry->id . '">
                                    </label>
                                </div>
                            </td>',
                $industry->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $industry->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_industries_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_industries/' . $industry->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_industries_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $industry->id . '" >
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
        return view('auth.recruitment_industries.add');
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
                'arab_name' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $industry = new RecruitmentIndustry();
            $industry->name = $data['name'];
            $industry->arab_name = $data['arab_name'];
            $industry->published = $published;
            $industry->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $industry->published_by = Auth::user()->id;
                $industry->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $industry->unpublished_by = Auth::user()->id;
                $industry->unpublished_date = date("Y-m-d H:i:s");
            }
            $industry->lastedit_by = Auth::user()->id;
            $industry->added_by = Auth::user()->id;
            $industry->lastedit_date = date("Y-m-d H:i:s");
            $industry->added_date = date("Y-m-d H:i:s");
            if ($industry->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_industry'));
                return Redirect::to('admin/recruitment_industries/create');
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
        $industry = RecruitmentIndustry::findOrFail($id);
        return view('auth.recruitment_industries.edit', compact('industry'));
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
        $industry = RecruitmentIndustry::findOrFail($id);
        $validator = Validator::make($request->all(),array(
            'name' => 'required',
            'arab_name' => 'required',
        ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $industry->name = $data['name'];
            $industry->arab_name = $data['arab_name'];
            if ($published == 'yes' && $industry->published=='no') {
                $industry->published_by = Auth::user()->id;
                $industry->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $industry->published=='yes') {
                $industry->unpublished_by = Auth::user()->id;
                $industry->unpublished_date = date("Y-m-d H:i:s");
            }
            $industry->published = $published;
            $industry->lastedit_by = Auth::user()->id;
            $industry->lastedit_date = date("Y-m-d H:i:s");
            if ($industry->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_industry'));
                return Redirect::to("admin/recruitment_industries/$industry->id/edit");
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
        $industry = RecruitmentIndustry::findOrFail($id);
        $industry->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $industry = RecruitmentIndustry::findOrFail($id);
            if ($published == 'no') {
                $industry->published = 'no';
                $industry->unpublished_by = Auth::user()->id;
                $industry->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $industry->published = 'yes';
                $industry->published_by = Auth::user()->id;
                $industry->published_date = date("Y-m-d H:i:s");
            }
            $industry->save();
        } else {
            return redirect(404);
        }
    }
}
