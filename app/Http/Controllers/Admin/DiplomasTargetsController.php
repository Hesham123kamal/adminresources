<?php

namespace App\Http\Controllers\Admin;

use App\Diplomas;
use App\DiplomasTargets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiplomasTargetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diplomas = Diplomas::pluck('name', 'id');
        return view('auth.diplomas_targets.view',compact('diplomas'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $diplomas_targets = DiplomasTargets::leftjoin('diplomas','diplomas.id','=','diplomas_targets.diploma_id')
                            ->select('diplomas_targets.*','diplomas.name as diploma_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $diplomas_targets = $diplomas_targets->where('diplomas_targets.id', '=', "$id");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $diplomas_targets = $diplomas_targets->where('diplomas_targets.diploma_id','=', $diploma);
        }
        if (isset($data['target']) && !empty($data['target'])) {
            $target = $data['target'];
            $diplomas_targets = $diplomas_targets->where('diplomas_targets.target', '=', $target);
        }
        if (isset($data['date_from']) && !empty($data['date_from']) && isset($data['date_to']) && !empty($data['date_to'])) {
            $date_from = $data['date_from'];
            $date_to = $data['date_to'];
            $diplomas_targets = $diplomas_targets->whereBetween('diplomas_targets.date', [$date_from, $date_to]);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $diplomas_targets = $diplomas_targets->whereBetween('diplomas_targets.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $diplomas_targets->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'diplomas_targets.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diplomas_targets.id';
                break;
            case 1:
                $columnName = 'diplomas.name';
                break;
            case 2:
                $columnName = 'diplomas_targets.target';
                break;
            case 3:
                $columnName = 'diplomas_targets.date';
                break;
            case 4:
                $columnName = 'diplomas_targets.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $diplomas_targets = $diplomas_targets->where(function ($q) use ($search) {
                $q->where('diplomas_targets.id', '=', $search)
                    ->orWhere('diplomas_targets.target', '=', $search)
                    ->orWhere('diplomas.name', 'LIKE', "%$search%");
            });
        }

        $diplomas_targets = $diplomas_targets->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($diplomas_targets as $diploma_target) {
            $diploma_name = $diploma_target->diploma_name;
            if(PerUser('diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/diplomas/' . $diploma_target->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            $records["data"][] = [
                $diploma_target->id,
                $diploma_name,
                $diploma_target->target,
                $diploma_target->date,
                $diploma_target->createtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $diploma_target->id . '" type="checkbox" ' . ((!PerUser('diplomas_targets_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('diplomas_targets_publish')) ? 'class="changeStatues"' : '') . ' ' . (($diploma_target->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $diploma_target->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $diploma_target->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diplomas_targets_edit')) ? '<li>
                                            <a href="' . URL('admin/diplomas_targets/' . $diploma_target->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_targets_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $diploma_target->id . '" >
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
        $diplomas = Diplomas::pluck('name', 'id');
        return view('auth.diplomas_targets.add',compact('diplomas'));
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
                'diploma' =>'required|exists:mysql2.diplomas,id',
                'target' => 'required|numeric',
                'date' => 'required|date_format:"Y-m-d"',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $diploma_target = new DiplomasTargets();
            $diploma_target->diploma_id = $data['diploma'];
            $diploma_target->target = $data['target'];
            $diploma_target->date = $data['date'];
//            $diploma_target->published = $published;
            $diploma_target->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $diploma_target->published_by = Auth::user()->id;
//                $diploma_target->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $diploma_target->unpublished_by = Auth::user()->id;
//                $diploma_target->unpublished_date = date("Y-m-d H:i:s");
//            }
            $diploma_target->lastedit_by = Auth::user()->id;
            $diploma_target->added_by = Auth::user()->id;
            $diploma_target->lastedit_date = date("Y-m-d H:i:s");
            $diploma_target->added_date = date("Y-m-d H:i:s");
            if ($diploma_target->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.diploma_target'));
                return Redirect::to('admin/diplomas_targets/create');
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
        $diploma_target = DiplomasTargets::findOrFail($id);
        $diplomas = Diplomas::pluck('name', 'id');
        return view('auth.diplomas_targets.edit', compact('diploma_target','diplomas'));
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
        $diploma_target = DiplomasTargets::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'diploma' =>'required|exists:mysql2.diplomas,id',
                'target' => 'required|numeric',
                'date' => 'required|date_format:"Y-m-d"'
            ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $diploma_target->diploma_id = $data['diploma'];
            $diploma_target->target = $data['target'];
            $diploma_target->date = $data['date'];
//            if ($published == 'yes' && $diploma_target->published=='no') {
//                $diploma_target->published_by = Auth::user()->id;
//                $diploma_target->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $diploma_target->published=='yes') {
//                $diploma_target->unpublished_by = Auth::user()->id;
//                $diploma_target->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $diploma_target->published = $published;
            $diploma_target->lastedit_by = Auth::user()->id;
            $diploma_target->lastedit_date = date("Y-m-d H:i:s");
            if ($diploma_target->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.diploma_target'));
                return Redirect::to("admin/diplomas_targets/$diploma_target->id/edit");
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
        $diploma_target = DiplomasTargets::findOrFail($id);
        $diploma_target->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $diploma_target = DiplomasTargets::findOrFail($id);
//            if ($published == 'no') {
//                $diploma_target->published = 'no';
//                $diploma_target->unpublished_by = Auth::user()->id;
//                $diploma_target->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $diploma_target->published = 'yes';
//                $diploma_target->published_by = Auth::user()->id;
//                $diploma_target->published_date = date("Y-m-d H:i:s");
//            }
//            $diploma_target->save();
//        } else {
//            return redirect(404);
//        }
//    }
}
