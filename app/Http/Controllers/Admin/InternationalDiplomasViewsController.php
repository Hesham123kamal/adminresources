<?php

namespace App\Http\Controllers\Admin;

use App\InternationalDiplomas;
use App\InternationalDiplomasViews;
use App\Http\Controllers\Controller;
use App\NormalUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class InternationalDiplomasViewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diplomas=InternationalDiplomas::pluck('name','id')->toArray();
        return view('auth.international_diplomas_views.view',compact('diplomas'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $idvs = InternationalDiplomasViews::leftjoin('users','users.id','=','international_diplomas_views.user_id')
            ->leftjoin('international_diplomas','international_diplomas.id','=','international_diplomas_views.diploma_id')
            ->select('international_diplomas_views.*','international_diplomas.name as diploma_name','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $idvs = $idvs->where('international_diplomas_views.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $idvs = $idvs->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['count']) && !empty($data['count'])) {
            $count = $data['count'];
            $idvs = $idvs->where('international_diplomas_views.count', '=', $count);
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $idvs = $idvs->where('international_diplomas.id','=', $diploma);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $idvs = $idvs->whereBetween('international_diplomas_views.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $idvs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'international_diplomas_views.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'international_diplomas_views.id';
                break;
            case 1:
                $columnName = 'international_diplomas.name';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'international_diplomas_views.count';
                break;
            case 4:
                $columnName = 'international_diplomas_views.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $idvs = $idvs->where(function ($q) use ($search) {
                $q->where('international_diplomas_views.id', '=', $search)
                    ->orWhere('international_diplomas_views.count', '=', $search)
                    ->orWhere('international_diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%");
            });
        }

        $idvs = $idvs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($idvs as $idv) {
            $diploma_name = $idv->diploma_name;
            $user_email = $idv->user_email;
            if(PerUser('international_diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/international_diplomas/' . $idv->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $idv->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $idv->id,
                $diploma_name,
                $user_email,
                $idv->count,
                $idv->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $idv->id . '" type="checkbox" ' . ((!PerUser('international_diplomas_views_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('international_diplomas_views_publish')) ? 'class="changeStatues"' : '') . ' ' . (($idv->published=="yes") ? 'checked="checked"' : '') . ' ">
//                                    <label for="checkbox-' . $idv->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $idv->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('international_diplomas_views_edit')) ? '<li>
                                            <a href="' . URL('admin/international_diplomas_views/' . $idv->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('international_diplomas_views_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $idv->id . '" >
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

    public function create()
    {
        $diplomas=InternationalDiplomas::pluck('name','id');
        return view('auth.international_diplomas_views.add',compact('diplomas'));
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $rules=array(
            'diploma' => 'required|exists:mysql2.international_diplomas,id',
            'user' => 'required',
            'count' => 'required|numeric',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $international_diploma_view = new InternationalDiplomasViews();
            $international_diploma_view->diploma_id = $data['diploma'];
            $international_diploma_view->user_id = $user->id;
            $international_diploma_view->count = $data['count'];
            $international_diploma_view->createdtime = date("Y-m-d H:i:s");
            $international_diploma_view->modifiedtime = date("Y-m-d H:i:s");
            if ($international_diploma_view->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.international_diploma_view'));
                return Redirect::to('admin/international_diplomas_views/create');
            }
        }
    }

    public function edit($id)
    {
        $international_diploma_view = InternationalDiplomasViews::findOrFail($id);
        $diplomas=InternationalDiplomas::pluck('name','id');
        $user=NormalUser::where('id', $international_diploma_view->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.international_diplomas_views.edit', compact('international_diploma_view','diplomas','user'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        $international_diploma_view = InternationalDiplomasViews::findOrFail($id);
        $rules=array(
            'diploma' => 'required|exists:mysql2.international_diplomas,id',
            'user' => 'required',
            'count' => 'required|numeric',
        );

        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $international_diploma_view->user_id = $user->id;
            $international_diploma_view->diploma_id = $data['diploma'];
            $international_diploma_view->count = $data['count'];
            $international_diploma_view->modifiedtime = date("Y-m-d H:i:s");
            if ($international_diploma_view->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.international_diploma_view'));
                return Redirect::to("admin/international_diplomas_views/$international_diploma_view->id/edit");
            }
        }
    }

    public function destroy($id)
    {
        $international_diploma_view = InternationalDiplomasViews::findOrFail($id);
        $international_diploma_view->delete();
    }


}
