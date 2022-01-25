<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mba;
use App\ModulesUsersSummary;
use App\MbaEmails;
use App\NormalUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class MbaProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules=Mba::pluck('name','id')->toArray();;
        return view('auth.mba_progress.view',compact('modules'));
    }

    public function emails()
    {
        return view('auth.mba_progress.emails');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $userIds=MbaEmails::select('user_id')->get()->toArray();
        $mba_results=ModulesUsersSummary::join('users','users.id','=','modules_users_summary.user_id')
            ->join('mba','modules_users_summary.module_id','=','mba.id')
            ->select(
                'users.Email AS user_email',
                'mba.name AS module_name',
                'modules_users_summary.project AS project_result',
                'modules_users_summary.exam AS exam_result',
                'modules_users_summary.progress AS progress'
            )->whereIn('users.id',$userIds);

        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $mba_results = $mba_results->where('users.Email', 'LIKE', "%$email%");
        }
        if (isset($data['mba_id']) && !empty($data['mba_id'])) {
            $mba_id = $data['mba_id'];
            $mba_results = $mba_results->where('mba.id', $mba_id);
        }
        if (isset($data['mba_name']) && !empty($data['mba_name'])) {
            $mba_name = $data['mba_name'];
            $mba_results = $mba_results->where('mba.name','LIKE', "%$mba_name%");
        }
        if (isset($data['project']) && !empty($data['project'])) {
            $project = $data['project'];
            $mba_results = $mba_results->where('modules_users_summary.project','=', "$project");
        }
        if (isset($data['exam']) && !empty($data['exam'])) {
            $exam = $data['exam'];
            $mba_results = $mba_results->where('modules_users_summary.exam','=', "$exam");
        }
        if (isset($data['progress']) && !empty($data['progress'])) {
            $progress = $data['progress'];
            $mba_results = $mba_results->where('modules_users_summary.progress','=', "$progress");
        }

        $iTotalRecords = $mba_results->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'users.Email';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'users.Email';
                break;
            case 1:
                $columnName = 'mba.name';
                break;
            case 2:
                $columnName = 'modules_users_summary.project';
                break;
            case 3:
                $columnName = 'modules_users_summary.exam';
                break;
            case 4:
                $columnName = 'modules_users_summary.progress';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $mba_results = $mba_results->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('mba.name', 'LIKE', "%$search%");
            });
        }
        //dd($mba_results->toSql());
        $mba_results = $mba_results->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($mba_results as $mba_result) {
//            $user_email = $mba_result->user_email;
//            if(PerUser('normal_user_edit') && $user_email !=''){
//                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $mba_result->user_id . '/edit') . '">' . $user_email . '</a>';
//            }
            $records["data"][] = [
                $mba_result->user_email,
                $mba_result->module_name,
                $mba_result->project_result,
                $mba_result->exam_result,
                $mba_result->progress,
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

    function emails_search(Request $request)
    {
        $data = $request->input();
        $emails_result=MbaEmails::select('*');

        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $emails_result = $emails_result->where('mba_emails.id', '=', $id);
        }

        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $emails_result = $emails_result->where('mba_emails.email', 'LIKE', "%$email%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $emails_result = $emails_result->whereBetween('mba_emails.added_at', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $emails_result->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mba_emails.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba_emails.id';
                break;
            case 1:
                $columnName = 'mba_emails.email';
                break;
            case 2:
                $columnName = 'mba_emails.added_at';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $emails_result = $emails_result->where(function ($q) use ($search) {
                $q->where('mba_emails.email', 'LIKE', "%$search%")
                    ->orWhere('mba_emails.id', '=', $search);
            });
        }

        $emails_result = $emails_result->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($emails_result as $email_result) {
            $records["data"][] = [
                $email_result->id,
                $email_result->email,
                $email_result->added_at,
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

    public function export()
    {
//        $data = $request->input();
//        $validator = Validator::make($request->all(),
//            array(
//                'csv_file' => 'required|mimes:csv,txt',
//            ));
//        if ($validator->fails()) {
//            return redirect()->back()->withErrors($validator->errors())->withInput();
//
//        }else {
//            $path = $request->file('csv_file')->getRealPath();
//            $data = array_map('str_getcsv', file($path));
//            $result = $data;
//            $emails=array();
//            if ($result > 0) {
//                foreach ($result as $row) {
//                    $emails[]=$row[0];
//                }
                $output = ['Email', 'Module','Project','Exam','Progress'];
                $emails=MbaEmails::select('email')->get()->toArray();

                $results = ModulesUsersSummary::join('users','users.id','=','modules_users_summary.user_id')
                    ->join('mba','modules_users_summary.module_id','=','mba.id')
                    ->select(
                        'users.Email AS user_email',
                        'mba.name AS module_name',
                        'modules_users_summary.project AS project_result',
                        'modules_users_summary.exam AS exam_result',
                        'modules_users_summary.progress AS progress'
                    )->whereIn('users.email',$emails)->orderBy('users.Email')->get();

                $filename = "result.csv";
                $handle = fopen($filename, 'w+');
                fputcsv($handle, $output);

                foreach($results as $r) {
                    fputcsv($handle, [
                        $r->user_email,
                        $r->module_name,
                        $r->project_result,
                        $r->exam_result,
                        $r->progress,
                    ]);
                }

                fclose($handle);

                $headers = array(
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="result.csv"'
                );

                return Response::download($filename, 'result.csv', $headers);

          //  }
       // }
    }

    public function create()
    {
        return view('auth.mba_progress.add');
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
            'email' => 'required|unique:mba_emails,email',
        );
        $user=NormalUser::where('Email', $data['email'])->first();
        if($user===null){
            $rules['email']= 'required|unique:mba_emails,email|exists:mysql2.users,email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $mba_email=new MbaEmails();
            $mba_email->email = $data['email'];
            $mba_email->user_id = $user->id;
            $mba_email->added_at = date("Y-m-d H:i:s");
            $mba_email->added_by = Auth::user()->id;
            if ($mba_email->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.email'));
                return Redirect::to('admin/mba_progress/create');
            }
        }
    }

}
