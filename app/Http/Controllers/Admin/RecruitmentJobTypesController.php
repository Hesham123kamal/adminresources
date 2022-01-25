<?php

namespace App\Http\Controllers\Admin;

use App\RecruitmentJobTypes;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class  RecruitmentJobTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruitment_job_types.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $types = RecruitmentJobTypes::select('recruitment_job_types.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $types = $types->where('id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $types = $types->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $types = $types->whereBetween('createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $types->count();
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
            case 4:
                $columnName = 'createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $types = $types->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $types = $types->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($types as $type) {
            $records["data"][] = [
                $type->id,
                $type->name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $type->id . '" type="checkbox" ' . ((!PerUser('recruitment_job_types_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_job_types_publish')) ? 'class="changeStatues"' : '') . ' ' . (($type->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $type->id . '">
                                    </label>
                                </div>
                            </td>',
                $type->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $type->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_job_types_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_job_types/' . $type->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_job_types_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $type->id . '" >
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
        return view('auth.recruitment_job_types.add');
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
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $type = new RecruitmentJobTypes();
            $type->name = $data['name'];
            $type->published = $published;
            $type->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $type->published_by = Auth::user()->id;
                $type->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $type->unpublished_by = Auth::user()->id;
                $type->unpublished_date = date("Y-m-d H:i:s");
            }
            $type->lastedit_by = Auth::user()->id;
            $type->added_by = Auth::user()->id;
            $type->lastedit_date = date("Y-m-d H:i:s");
            $type->added_date = date("Y-m-d H:i:s");
            if ($type->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_job_type'));
                return Redirect::to('admin/recruitment_job_types/create');
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
        $type = RecruitmentJobTypes::findOrFail($id);
        return view('auth.recruitment_job_types.edit', compact('type'));
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
        $type = RecruitmentJobTypes::findOrFail($id);
        $validator = Validator::make($request->all(),array(
            'name' => 'required',
        ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $type->name = $data['name'];
            if ($published == 'yes' && $type->published=='no') {
                $type->published_by = Auth::user()->id;
                $type->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $type->published=='yes') {
                $type->unpublished_by = Auth::user()->id;
                $type->unpublished_date = date("Y-m-d H:i:s");
            }
            $type->published = $published;
            $type->lastedit_by = Auth::user()->id;
            $type->lastedit_date = date("Y-m-d H:i:s");
            if ($type->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_job_type'));
                return Redirect::to("admin/recruitment_job_types/$type->id/edit");
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
        $type = RecruitmentJobTypes::findOrFail($id);
        $type->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $type = RecruitmentJobTypes::findOrFail($id);
            if ($published == 'no') {
                $type->published = 'no';
                $type->unpublished_by = Auth::user()->id;
                $type->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $type->published = 'yes';
                $type->published_by = Auth::user()->id;
                $type->published_date = date("Y-m-d H:i:s");
            }
            $type->save();
        } else {
            return redirect(404);
        }
    }
}
