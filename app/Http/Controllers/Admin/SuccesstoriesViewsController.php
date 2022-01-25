<?php

namespace App\Http\Controllers\Admin;

use App\Successstories;
use App\SuccesstoriesViews;
use App\Http\Controllers\Controller;
use App\NormalUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SuccesstoriesViewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.successtories_views.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $ssvs = SuccesstoriesViews::leftjoin('users','users.id','=','successtories_views.user_id')
            ->leftjoin('successtories','successtories.id','=','successtories_views.successtory_id')
            ->select('successtories_views.*','successtories.name as successtory_name','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $ssvs = $ssvs->where('successtories_views.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $ssvs = $ssvs->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['count']) && !empty($data['count'])) {
            $count = $data['count'];
            $ssvs = $ssvs->where('successtories_views.count', '=', $count);
        }
        if (isset($data['successtory']) && !empty($data['successtory'])) {
            $successtory = $data['successtory'];
            $ssvs = $ssvs->where('successtories.name','LIKE', "%$successtory%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $ssvs = $ssvs->whereBetween('successtories_views.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $ssvs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'successtories_views.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'successtories_views.id';
                break;
            case 1:
                $columnName = 'successtories.name';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'successtories_views.count';
                break;
            case 4:
                $columnName = 'successtories_views.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $ssvs = $ssvs->where(function ($q) use ($search) {
                $q->where('successtories_views.id', '=', $search)
                    ->orWhere('successtories_views.count', '=', $search)
                    ->orWhere('successtories.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%");
            });
        }

        $ssvs = $ssvs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($ssvs as $ssv) {
            $successtory_name = $ssv->successtory_name;
            $user_email = $ssv->user_email;
            if(PerUser('successstories_edit') && $successtory_name !=''){
                $successtory_name= '<a target="_blank" href="' . URL('admin/successstories/' . $ssv->successtory_id . '/edit') . '">' . $successtory_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $ssv->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $ssv->id,
                $successtory_name,
                $user_email,
                $ssv->count,
                $ssv->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $ssv->id . '" type="checkbox" ' . ((!PerUser('successtories_views_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('successtories_views_publish')) ? 'class="changeStatues"' : '') . ' ' . (($ssv->published=="yes") ? 'checked="checked"' : '') . ' ">
                                    <label for="checkbox-' . $ssv->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $ssv->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('successtories_views_edit')) ? '<li>
                                            <a href="' . URL('admin/successtories_views/' . $ssv->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('successtories_views_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $ssv->id . '" >
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
        $successtories=Successstories::pluck('name', 'id');
        return view('auth.successtories_views.add',compact('successtories'));
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $rules=array(
            'successtory' => 'required|exists:mysql2.successtories,id',
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
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $successtory_view = new SuccesstoriesViews();
            $successtory_view->successtory_id = $data['successtory'];
            $successtory_view->user_id = $user->id;
            $successtory_view->count = $data['count'];
            $successtory_view->published = $published;
            $successtory_view->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $successtory_view->published_by = Auth::user()->id;
                $successtory_view->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $successtory_view->unpublished_by = Auth::user()->id;
                $successtory_view->unpublished_date = date("Y-m-d H:i:s");
            }
            $successtory_view->lastedit_by = Auth::user()->id;
            $successtory_view->added_by = Auth::user()->id;
            $successtory_view->lastedit_date = date("Y-m-d H:i:s");
            $successtory_view->added_date = date("Y-m-d H:i:s");
            if ($successtory_view->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.successtory_view'));
                return Redirect::to('admin/successtories_views/create');
            }
        }
    }

    public function edit($id)
    {
        $successtory_view = SuccesstoriesViews::findOrFail($id);
        $successtories=Successstories::pluck('name', 'id');
        $user=NormalUser::where('id', $successtory_view->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.successtories_views.edit', compact('successtory_view','successtories','user'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        $successtory_view = SuccesstoriesViews::findOrFail($id);
        $rules=array(
            'successtory' => 'required|exists:mysql2.successtories,id',
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
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $successtory_view->user_id = $user->id;
            $successtory_view->successtory_id = $data['successtory'];
            $successtory_view->count = $data['count'];
            if ($published == 'yes' && $successtory_view->published=='no') {
                $successtory_view->published_by = Auth::user()->id;
                $successtory_view->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $successtory_view->published=='yes') {
                $successtory_view->unpublished_by = Auth::user()->id;
                $successtory_view->unpublished_date = date("Y-m-d H:i:s");
            }
            $successtory_view->published = $published;
            $successtory_view->lastedit_by = Auth::user()->id;
            $successtory_view->lastedit_date = date("Y-m-d H:i:s");
            $successtory_view->modifiedtime = date("Y-m-d H:i:s");
            if ($successtory_view->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.successtory_view'));
                return Redirect::to("admin/successtories_views/$successtory_view->id/edit");
            }
        }
    }

    public function destroy($id)
    {
        $successtory_view = SuccesstoriesViews::findOrFail($id);
        $successtory_view->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $successtory_view = SuccesstoriesViews::findOrFail($id);
            if ($published == 'no') {
                $successtory_view->published = 'no';
                $successtory_view->unpublished_by = Auth::user()->id;
                $successtory_view->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $successtory_view->published = 'yes';
                $successtory_view->published_by = Auth::user()->id;
                $successtory_view->published_date = date("Y-m-d H:i:s");
            }
            $successtory_view->save();
        } else {
            return redirect(404);
        }
    }

}
