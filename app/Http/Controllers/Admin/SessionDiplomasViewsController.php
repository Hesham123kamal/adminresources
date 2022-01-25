<?php

namespace App\Http\Controllers\Admin;

use App\Diplomas;
use App\SessionDiplomasViews;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SessionDiplomasViewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.session_diplomas_views.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $sdvs = SessionDiplomasViews::select('session_diplomas_views.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $sdvs = $sdvs->where('session_diplomas_views.id', '=', $id);
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $sdvs = $sdvs->where('session_diplomas_views.user_id', '=', $user_id);
        }
        if (isset($data['count']) && !empty($data['count'])) {
            $count = $data['count'];
            $sdvs = $sdvs->where('session_diplomas_views.count', '=', $count);
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $diplomas = Diplomas::where('name', 'LIKE', "%$diploma%")->pluck('id')->toArray();
            if ($diplomas !== null) {
                $sdvs = $sdvs->whereIn('session_diplomas_views.diploma_id', $diplomas);
            }
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $sdvs = $sdvs->whereBetween('session_diplomas_views.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $sdvs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'session_diplomas_views.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'session_diplomas_views.id';
                break;
            case 2:
                $columnName = 'session_diplomas_views.user_id';
                break;
            case 3:
                $columnName = 'session_diplomas_views.count';
                break;
            case 4:
                $columnName = 'session_diplomas_views.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $sdvs = $sdvs->where(function ($q) use ($search) {
                $q->where('session_diplomas_views.id', '=', $search)
                    ->orWhere('session_diplomas_views.user_id', '=', $search)
                    ->orWhere('session_diplomas_views.count', '=', $search);
            });
        }

        $sdvs = $sdvs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($sdvs as $sdv) {
            $diploma = isset($sdv->diploma) ? $sdv->diploma->name : '';
            $records["data"][] = [
                $sdv->id,
                $diploma,
                $sdv->user_id,
                $sdv->count,
                $sdv->createdtime,
                '',
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

}
