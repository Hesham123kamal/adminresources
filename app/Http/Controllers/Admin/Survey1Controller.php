<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Survey1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Survey1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.survey.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $report = Survey1::select(
            'exam1_clients.id AS client_id',
            'exam1_clients.email AS client_email',
            'exam1_clients.gender AS client_gender',
            'exam1_clients.hight AS client_hight',
            'exam1_clients.weight AS client_weight',
            'exam1_sammry_report.table1',
            'exam1_sammry_report.score1',
            'exam1_sammry_report.table2',
            'exam1_sammry_report.score2',
            'exam1_sammry_report.table3',
            'exam1_sammry_report.score3',
            'exam1_sammry_report.table4',
            'exam1_sammry_report.score4',
            'exam2_sammry_report.max_table1',
            'exam2_sammry_report.score1 AS max_score1',
            'exam2_sammry_report.max_table2',
            'exam2_sammry_report.score2 AS max_score2',
            'exam2_sammry_report.max_table3',
            'exam2_sammry_report.score3 AS max_score3',
            'exam1_sammry_report.created_at'
        )
            ->join('exam1_clients','exam1_clients.id','=','exam1_sammry_report.client_id')
            ->join('exam2_sammry_report','exam1_clients.id','=','exam2_sammry_report.client_id')
        ;
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $report = $report->where('exam1_sammry_report.id', '=', $id);
        }
        if (isset($data['client_id']) && !empty($data['client_id'])) {
            $client_id = $data['client_id'];
            $report = $report->where('exam1_sammry_report.client_id', '=', $client_id);
        }
        if (isset($data['table1']) && !empty($data['table1'])) {
            $table1 = $data['table1'];
            $report = $report->where('exam1_sammry_report.table1', 'LIKE', "%$table1%");
        }
        if (isset($data['score1']) && !empty($data['score1'])) {
            $score1 = $data['score1'];
            $report = $report->where('exam1_sammry_report.score1', 'LIKE', "%$score1%");
        }
        if (isset($data['table2']) && !empty($data['table2'])) {
            $table2 = $data['table2'];
            $report = $report->where('exam1_sammry_report.table2', 'LIKE', "%$table2%");
        }
        if (isset($data['score2']) && !empty($data['score2'])) {
            $score2 = $data['score2'];
            $report = $report->where('exam1_sammry_report.score2', 'LIKE', "%$score2%");
        }
        if (isset($data['table3']) && !empty($data['table3'])) {
            $table3 = $data['table3'];
            $report = $report->where('exam1_sammry_report.table3', 'LIKE', "%$table3%");
        }
        if (isset($data['score3']) && !empty($data['score3'])) {
            $score3 = $data['score3'];
            $report = $report->where('exam1_sammry_report.score3', 'LIKE', "%$score3%");
        }
        if (isset($data['table4']) && !empty($data['table4'])) {
            $table4 = $data['table4'];
            $report = $report->where('exam1_sammry_report.table4', 'LIKE', "%$table4%");
        }
        if (isset($data['score4']) && !empty($data['score4'])) {
            $score4 = $data['score4'];
            $report = $report->where('exam1_sammry_report.score4', 'LIKE', "%$score4%");
        }
//        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
//            $created_time_from = $data['created_time_from'];
//            $created_time_to = $data['created_time_to'];
//            $report = $report->whereBetween('exam1_sammry_report.created_at', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
//        }
        if(!empty($data['created_time_from'])||!empty($data['created_time_to'])){
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            if($created_time_from&&$created_time_to){
                $report = $report->whereBetween('exam1_sammry_report.created_at', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
            }elseif($created_time_to){
                $report=$report->where(DB::raw('DATE_FORMAT(exam1_sammry_report.created_at,"%Y-%m-%d")'),'<=',$created_time_to);
            }elseif($created_time_from){
                $report=$report->where(DB::raw('DATE_FORMAT(exam1_sammry_report.created_at,"%Y-%m-%d")'),'>=',$created_time_from);
            }
        }
        if(!empty($data['client_id_from'])||!empty($data['client_id_to'])){
            $client_id_from=$data['client_id_from'];
            $client_id_to=$data['client_id_to'];
            if($client_id_from&&$client_id_to){
                $report = $report->whereBetween('exam1_sammry_report.client_id', [$client_id_from, $client_id_to]);
            }elseif($client_id_to){
                $report=$report->where('exam1_sammry_report.client_id','<=',$client_id_to);
            }elseif($client_id_from){
                $report=$report->where('exam1_sammry_report.client_id','>=',$client_id_from);
            }
        }
            //dd($report->toSql(),$created_time_from);
        $iTotalRecords = $report->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'exam1_sammry_report.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'exam1_clients.id';
                break;
            case 1:
                $columnName = 'exam1_clients.gender';
                break;
            case 2:
                $columnName = 'exam1_clients.hight';
                break;
            case 3:
                $columnName = 'exam1_clients.weight';
                break;
            case 4:
                $columnName = 'exam1_sammry_report.table1';
                break;
            case 5:
                $columnName = 'exam1_sammry_report.score1';
                break;
            case 6:
                $columnName = 'exam1_sammry_report.table2';
                break;
            case 7:
                $columnName = 'exam1_sammry_report.score2';
                break;
            case 8:
                $columnName = 'exam1_sammry_report.table3';
                break;
            case 9:
                $columnName = 'exam1_sammry_report.score3';
                break;
            case 10:
                $columnName = 'exam1_sammry_report.table4';
                break;
            case 11:
                $columnName = 'exam1_sammry_report.score4';
                break;
            case 12:
                $columnName = 'exam2_sammry_report.max_table1';
                break;
            case 13:
                $columnName = 'exam2_sammry_report.score1';
                break;
            case 14:
                $columnName = 'exam2_sammry_report.max_table2';
                break;
            case 15:
                $columnName = 'exam2_sammry_report.score2';
                break;
            case 16:
                $columnName = 'exam2_sammry_report.max_table3';
                break;
            case 17:
                $columnName = 'exam2_sammry_report.score3';
                break;
            case 18:
                $columnName = 'exam1_sammry_report.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $report = $report->where(function ($q) use ($search) {
                $q->where('exam1_sammry_report.client_id', '=', $search)
                    ->orWhere('exam1_clients.gender', 'LIKE', "%$search%")
                    ->orWhere('exam1_clients.hight', 'LIKE', "%$search%")
                    ->orWhere('exam1_clients.weight', 'LIKE', "%$search%")
                    ->orWhere('exam1_sammry_report.table1', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.score1', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.table2', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.score2', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.table3', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.score3', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.table4', 'Like', "%$search%")
                    ->orWhere('exam1_sammry_report.score4', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.max_table1', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.score1', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.max_table2', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.score2', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.max_table3', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.score3', 'Like', "%$search%")
                ;

            });
        }

        $report = $report->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        $groups=[
            'g1'=>1,
            'g2'=>2,
            'g3'=>3,
            'g4'=>4,
            'g5'=>5,
            'g6'=>6,
        ];
        foreach ($report as $r) {
            $records["data"][] = [
                $r->client_id,
                //$r->client_email,
                $r->client_gender,
                $r->client_hight,
                $r->client_weight,
                $r->table1,
                $r->score1,
                $r->table2,
                $r->score2,
                $r->table3,
                $r->score3,
                $r->table4,
                $r->score4,
                $groups[$r->max_table1],
                $r->max_score1,
                $groups[$r->max_table2],
                $r->max_score2,
                $groups[$r->max_table3],
                $r->max_score3,
                $r->created_at->format('Y-m-d H:i:s'),
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
