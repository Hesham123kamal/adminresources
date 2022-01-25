<?php

namespace App\Http\Controllers\Admin;

use App\Webinars;
use App\WebinarsViews;
use App\Http\Controllers\Controller;
use App\NormalUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class WebinarsViewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.webinars_views.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $sws = WebinarsViews::leftjoin('users','users.id','=','webinar_views.user_id')
            ->leftjoin('webinar','webinar.id','=','webinar_views.webinar_id')
            ->select('webinar_views.*','webinar.name as webinar_name','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $sws = $sws->where('webinar_views.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $sws = $sws->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['count']) && !empty($data['count'])) {
            $count = $data['count'];
            $sws = $sws->where('webinar_views.count', '=', $count);
        }
        if (isset($data['webinar']) && !empty($data['webinar'])) {
            $webinar = $data['webinar'];
            $sws = $sws->where('webinar.name','LIKE', "%$webinar%");

        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $sws = $sws->whereBetween('webinar_views.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $sws->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'webinar_views.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'webinar_views.id';
                break;
            case 1:
                $columnName = 'webinar.name';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'webinar_views.count';
                break;
            case 4:
                $columnName = 'webinar_views.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $sws = $sws->where(function ($q) use ($search) {
                $q->where('webinar_views.id', '=', $search)
                    ->orWhere('webinar_views.count', '=', $search)
                    ->orWhere('webinar.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%");
            });
        }

        $sws = $sws->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($sws as $sw) {
            $webinar_name = $sw->webinar_name;
            $user_email = $sw->user_email;
            if(PerUser('webinars_edit') && $webinar_name !=''){
                $webinar_name= '<a target="_blank" href="' . URL('admin/webinars/' . $sw->webinar_id . '/edit') . '">' . $webinar_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $sw->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $sw->id,
                $webinar_name,
                $user_email,
                $sw->count,
                $sw->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $sw->id . '" type="checkbox" ' . ((!PerUser('webinars_views_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('webinars_views_publish')) ? 'class="changeStatues"' : '') . ' ' . (($sw->published=="yes") ? 'checked="checked"' : '') . ' ">
                                    <label for="checkbox-' . $sw->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $sw->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('webinars_views_edit')) ? '<li>
                                            <a href="' . URL('admin/webinars_views/' . $sw->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('webinars_views_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $sw->id . '" >
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
        $webinars=Webinars::pluck('name', 'id');
        return view('auth.webinars_views.add',compact('webinars'));
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $rules=array(
            'webinar' => 'required|exists:mysql2.webinar,id',
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
            $webinar_view = new WebinarsViews();
            $webinar_view->webinar_id = $data['webinar'];
            $webinar_view->user_id = $user->id;
            $webinar_view->count = $data['count'];
            $webinar_view->published = $published;
            $webinar_view->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $webinar_view->published_by = Auth::user()->id;
                $webinar_view->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $webinar_view->unpublished_by = Auth::user()->id;
                $webinar_view->unpublished_date = date("Y-m-d H:i:s");
            }
            $webinar_view->lastedit_by = Auth::user()->id;
            $webinar_view->added_by = Auth::user()->id;
            $webinar_view->lastedit_date = date("Y-m-d H:i:s");
            $webinar_view->added_date = date("Y-m-d H:i:s");
            if ($webinar_view->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.webinar_view'));
                return Redirect::to('admin/webinars_views/create');
            }
        }
    }

    public function edit($id)
    {
        $webinar_view = WebinarsViews::findOrFail($id);
        $webinars=Webinars::pluck('name', 'id');
        $user=NormalUser::where('id', $webinar_view->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.webinars_views.edit', compact('webinar_view','webinars','user'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        $webinar_view = WebinarsViews::findOrFail($id);
        $rules=array(
            'webinar' => 'required|exists:mysql2.webinar,id',
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
            $webinar_view->user_id = $user->id;
            $webinar_view->webinar_id = $data['webinar'];
            $webinar_view->count = $data['count'];
            if ($published == 'yes' && $webinar_view->published=='no') {
                $webinar_view->published_by = Auth::user()->id;
                $webinar_view->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $webinar_view->published=='yes') {
                $webinar_view->unpublished_by = Auth::user()->id;
                $webinar_view->unpublished_date = date("Y-m-d H:i:s");
            }
            $webinar_view->published = $published;
            $webinar_view->lastedit_by = Auth::user()->id;
            $webinar_view->lastedit_date = date("Y-m-d H:i:s");
            $webinar_view->modifiedtime = date("Y-m-d H:i:s");
            if ($webinar_view->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.webinar_view'));
                return Redirect::to("admin/webinars_views/$webinar_view->id/edit");
            }
        }
    }

    public function destroy($id)
    {
        $webinar_view = WebinarsViews::findOrFail($id);
        $webinar_view->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $webinar_view = WebinarsViews::findOrFail($id);
            if ($published == 'no') {
                $webinar_view->published = 'no';
                $webinar_view->unpublished_by = Auth::user()->id;
                $webinar_view->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $webinar_view->published = 'yes';
                $webinar_view->published_by = Auth::user()->id;
                $webinar_view->published_date = date("Y-m-d H:i:s");
            }
            $webinar_view->save();
        } else {
            return redirect(404);
        }
    }


}
