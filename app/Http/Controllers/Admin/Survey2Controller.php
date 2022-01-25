<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Survey2;
use Illuminate\Http\Request;

class Survey2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.survey2.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $report = Survey2::select('exam2_sammry_report.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $report = $report->where('exam2_sammry_report.id', '=', $id);
        }
        if (isset($data['client_id']) && !empty($data['client_id'])) {
            $client_id = $data['client_id'];
            $report = $report->where('exam2_sammry_report.client_id', '=', $client_id);
        }
        if (isset($data['max_table1']) && !empty($data['max_table1'])) {
            $max_table1 = $data['max_table1'];
            $report = $report->where('exam2_sammry_report.max_table1', 'LIKE', "%$max_table1%");
        }
        if (isset($data['score1']) && !empty($data['score1'])) {
            $score1 = $data['score1'];
            $report = $report->where('exam2_sammry_report.score1', 'LIKE', "%$score1%");
        }
        if (isset($data['max_table2']) && !empty($data['max_table2'])) {
            $max_table2 = $data['max_table2'];
            $report = $report->where('exam2_sammry_report.max_table2', 'LIKE', "%$max_table2%");
        }
        if (isset($data['score2']) && !empty($data['score2'])) {
            $score2 = $data['score2'];
            $report = $report->where('exam2_sammry_report.score2', 'LIKE', "%$score2%");
        }
        if (isset($data['max_table3']) && !empty($data['max_table3'])) {
            $max_table3 = $data['max_table3'];
            $report = $report->where('exam2_sammry_report.max_table3', 'LIKE', "%$max_table3%");
        }
        if (isset($data['score3']) && !empty($data['score3'])) {
            $score3 = $data['score3'];
            $report = $report->where('exam2_sammry_report.score3', 'LIKE', "%$score3%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $report = $report->whereBetween('exam2_sammry_report.created_at', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $report->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'exam2_sammry_report.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'exam2_sammry_report.id';
                break;
            case 1:
                $columnName = 'exam2_sammry_report.client_id';
                break;
            case 2:
                $columnName = 'exam2_sammry_report.max_table1';
                break;
            case 3:
                $columnName = 'exam2_sammry_report.score1';
                break;
            case 4:
                $columnName = 'exam2_sammry_report.max_table2';
                break;
            case 5:
                $columnName = 'exam2_sammry_report.score2';
                break;
            case 6:
                $columnName = 'exam2_sammry_report.max_table3';
                break;
            case 7:
                $columnName = 'exam2_sammry_report.score3';
                break;
            case 8:
                $columnName = 'exam2_sammry_report.created_at';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $report = $report->where(function ($q) use ($search) {
                $q->where('exam2_sammry_report.id', '=', $search)
                    ->orWhere('exam2_sammry_report.client_id', '=', $search)
                    ->orWhere('exam2_sammry_report.max_table1', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.score1', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.max_table2', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.score2', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.max_table3', 'Like', "%$search%")
                    ->orWhere('exam2_sammry_report.score3', 'Like', "%$search%");
            });
        }

        $report = $report->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($report as $r) {
            $records["data"][] = [
                $r->id,
                $r->client_id,
                $r->max_table1,
                $r->score1,
                $r->max_table2,
                $r->score2,
                $r->max_table3,
                $r->score3,
                $r->created_at->format('Y-m-d H:i:s'),
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
