<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PromotionCodeUsed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use Response;

class PromotionCodeUsedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.promotion_code_used.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $promotion_code_used = PromotionCodeUsed::join('users','users.id','=','promotion_code_used.user_id')
            ->join('promotion_code','promotion_code.id','=','promotion_code_used.promotion_code_id')
            ->select('promotion_code_used.*','users.Email as user_email','promotion_code.code');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $promotion_code_used = $promotion_code_used->where('promotion_code_used.id', '=', $id);
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $promotion_code_used = $promotion_code_used->where('users.Email','LIKE', "%$email%");
        }
        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $promotion_code_used = $promotion_code_used->where('promotion_code.code','LIKE', "%$code%");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $promotion_code_used = $promotion_code_used->whereBetween('promotion_code_used.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $promotion_code_used->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'promotion_code_used.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'promotion_code_used.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'promotion_code.code';
                break;
            case 3:
                $columnName = 'promotion_code_used.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $promotion_code_used = $promotion_code_used->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('promotion_code.code', 'LIKE', "%$search%")
                    ->orWhere('promotion_code_used.id', '=', $search);
            });
        }

        $promotion_code_used = $promotion_code_used->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($promotion_code_used as $promotion_used) {
            $user_email = $promotion_used->user_email;
            if(PerUser('promotion_code_used') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $promotion_used->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $promotion_used->id,
                $user_email,
                $promotion_used->code,
                $promotion_used->createdtime,
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

    public function report1()
    {
        return view('auth.promotion_code_used.report1');
    }
    public function report2()
    {
        return view('auth.promotion_code_used.report2');
    }

    function search1(Request $request)
    {

        $data = $request->input();
        $promotion_code_used = PromotionCodeUsed::join('users','users.id','=','promotion_code_used.user_id')
            ->join('promotion_code','promotion_code.id','=','promotion_code_used.promotion_code_id')
            ->select('promotion_code.code','promotion_code_id','promotion_code_used.user_id','users.FullName','users.Email',
                DB::raw("(CASE WHEN promotion_code.type='life_time' THEN
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='diplomas' THEN
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='mba' THEN
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id)
                  ELSE
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id) 
                  END) AS used")
                );

        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $promotion_code_used = $promotion_code_used->where('promotion_code.code','LIKE', "%$code%");
        }
        if (isset($data['promotion_code_id']) && !empty($data['promotion_code_id'])) {
            $promotion_code_id= $data['promotion_code_id'];
            $promotion_code_used = $promotion_code_used->where('promotion_code_used.promotion_code_id', '=', $promotion_code_id);
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $promotion_code_used = $promotion_code_used->where('promotion_code_used.user_id', '=', $user_id);
        }
        if (isset($data['fullname']) && !empty($data['fullname'])) {
            $fullname = $data['fullname'];
            $promotion_code_used = $promotion_code_used->where('users.FullName','LIKE', "%$fullname%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $promotion_code_used = $promotion_code_used->where('users.Email','LIKE', "%$email%");
        }

        $iTotalRecords = $promotion_code_used->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'promotion_code_used.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'promotion_code.code';
                break;
            case 1:
                $columnName = 'promotion_code.id';
                break;
            case 2:
                $columnName = 'promotion_code_used.user_id';
                break;
            case 3:
                $columnName = 'users.FullName';
                break;
            case 4:
                $columnName = 'users.Email';
                break;
            case 5:
                $columnName = 'used';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $promotion_code_used = $promotion_code_used->where(function ($q) use ($search) {
                $q->where('promotion_code.code', 'LIKE', "%$search%")
                    ->orWhere('users.FullName', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('promotion_code.id', '=', $search)
                    ->orWhere('promotion_code_used.user_id', '=', $search);
            });
        }

        $promotion_code_used = $promotion_code_used->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($promotion_code_used as $promotion_used) {
            $user_email = $promotion_used->Email;
            $user_name = $promotion_used->FullName;
            if(PerUser('promotion_code_used_report1') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $promotion_used->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            if(PerUser('promotion_code_used_report1') && $user_name !=''){
                $user_name= '<a target="_blank" href="' . URL('admin/normal_user/' . $promotion_used->user_id . '/edit') . '">' . $user_name . '</a>';
            }
            $records["data"][] = [
                $promotion_used->code,
                $promotion_used->promotion_code_id,
                $promotion_used->user_id,
                $user_name,
                $user_email,
                $promotion_used->used,
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

    function search2(Request $request)
    {
        $data = $request->input();
        $promotion_code_used = PromotionCodeUsed::join('promotion_code','promotion_code.id','=','promotion_code_used.promotion_code_id')
            ->select('promotion_code.code',
                DB::raw("SUM((CASE WHEN promotion_code.type='life_time' THEN
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='diplomas' THEN
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                   WHEN promotion_code.type='mba' THEN
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id)
                 ELSE
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id)
                 END)) AS sum_used")
                ,
                DB::raw("COUNT(code) AS count_code")
            )->groupBy('code');


        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $promotion_code_used = $promotion_code_used->where('promotion_code.code','LIKE', "%$code%");
        }

        $iTotalRecords = $promotion_code_used->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'promotion_code.code';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'promotion_code.code';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $promotion_code_used = $promotion_code_used->where(function ($q) use ($search) {
                $q->where('promotion_code.code', 'LIKE', "%$search%");
            });
        }

        $promotion_code_used = $promotion_code_used->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($promotion_code_used as $promotion_used) {
            $records["data"][] = [
                $promotion_used->code,
                $promotion_used->sum_used,
                $promotion_used->count_code,
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

    public function report1_export()
    {
        $output = [ Lang::get('main.code'), Lang::get('main.code_id'),Lang::get('main.user_id'), Lang::get('main.user_name')
            ,Lang::get('main.user_email'),Lang::get('main.used')];

        $results = PromotionCodeUsed::join('users','users.id','=','promotion_code_used.user_id')
            ->join('promotion_code','promotion_code.id','=','promotion_code_used.promotion_code_id')
            ->select('promotion_code.code','promotion_code_id','promotion_code_used.user_id','users.FullName','users.Email',
                DB::raw("(CASE WHEN promotion_code.type='life_time' THEN
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='diplomas' THEN
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='mba' THEN
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id)
                  ELSE
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id) 
                  END) AS used"))->orderBy('promotion_code_used.id')->get();


        $filename = "result.csv";
        $handle = fopen($filename, 'w+');
        fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($handle, $output);

        foreach($results as $r) {
            fputcsv($handle, [
                $r->code,
                $r->promotion_code_id,
                $r->user_id,
                $r->FullName,
                $r->Email,
                $r->used,
            ]);
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="result.csv"'
        );

        return Response::download($filename, 'result.csv', $headers);

    }
    public function report2_export()
    {
        $output = [ Lang::get('main.code'), Lang::get('main.sum_used'),Lang::get('main.count_code')];

        $results = PromotionCodeUsed::join('promotion_code','promotion_code.id','=','promotion_code_used.promotion_code_id')
            ->select('promotion_code.code',
                DB::raw("SUM((CASE WHEN promotion_code.type='life_time' THEN
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='diplomas' THEN
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  WHEN promotion_code.type='mba' THEN
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id)
                  ELSE
                  (SELECT COUNT('X') FROM charge_transaction WHERE charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM diplomas_charge_transaction WHERE diplomas_charge_transaction.user_id=promotion_code_used.user_id)
                  +
                  (SELECT COUNT('X') FROM mba_charge_transaction WHERE mba_charge_transaction.user_id=promotion_code_used.user_id) 
                  END)) AS sum_used")
                ,
                DB::raw("COUNT(code) AS count_code")
            )->groupBy('code')->orderBy('promotion_code.code')->get();


        $filename = "result.csv";
        $handle = fopen($filename, 'w+');
        fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($handle, $output);

        foreach($results as $r) {
            fputcsv($handle, [
                $r->code,
                $r->sum_used,
                $r->count_code,
            ]);
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="result.csv"'
        );

        return Response::download($filename, 'result.csv', $headers);

    }


}
