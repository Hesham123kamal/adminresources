<?php

namespace App\Http\Controllers;

use App\Survey1;
use App\Survey1Clients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SurveyResultController extends Controller
{
    public function index()
    {
        return view('auth.survey_result');
    }

    public function parse(Request $request)
    {

        $rules= array(
            'csv_file' => 'required|mimes:csv,txt',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $users = [];
            $path = $request->file('csv_file')->getRealPath();

            $data = array_map('str_getcsv', file($path));
            $result = array_slice($data, 1);

            if ($result > 0) {
                if(isset($request->input()['update_link'])){
                    $idsNot=[];
                    $idsIs=[];
                    foreach ($result as $row) {
                        $user = Survey1Clients::find($row[0]);
                        if(count($user)){
                            $user->result_link=$row[1];
                            $user->save();
                            $idsIs[]=$row[0];
                        }else{
                            $idsNot[]=$row[0];
                        }

                    }
                    //dd($idsIs,$idsNot);
                    Session::flash('success', 'Links Updated');
                    return Redirect::to("survey_result");
                }
                else {
                    $error_emails='';
                    foreach ($result as $row) {
                        $user = Survey1Clients::findOrFail($row[0]);
                        $email = $user->email;
                        $reportLink = $row[1];
                        $html = view('emails.survey_report_link', compact('user', 'reportLink'))->render();
                        $return=sendGridEmailToUser($html, $email, $user->name, 'نتائج اختبارات السمات الشخصية من إعمل بيزنس');
                        if($return['success']==false){
                            $error_emails.=($error_emails)?','.$email:$email;
                        }
                        $users[] = $email;
                    }
                    if($error_emails){
                        Session::flash('success', 'Emails Sends Without those '.$error_emails);
                    }else{
                        Session::flash('success', 'Emails Sends ');
                    }
                    return Redirect::to("survey_result");
                }
            }
        }

    }

    public function export(Request $request)
    {
        $rules= array(
            'csv_file' => 'required|mimes:csv,txt',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $result = array_slice($data, 1);
            $ids=array();
            if ($result > 0) {
                foreach ($result as $row) {
                    $ids[]=$row[0];
                }
                $output[] = ['Client', 'Gender','Height','Weight','Table 1', 'Score 1', 'Table 2','Score 2',
                    'Table 3','Score 3','Table 4','Score 4','Max Table 1','Max Score 1','Max Table 2',
                    'Max Score 2','Max Table 3','Max Score 3','Created Time'];
                $surveys = Survey1::join('exam1_clients','exam1_clients.id','=','exam1_sammry_report.client_id')
                    ->join('exam2_sammry_report','exam1_clients.id','=','exam2_sammry_report.client_id')
                    ->select(
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
                    )->whereIn('exam1_clients.id',$ids)->get();
                $groups=[
                    'g1'=>1,
                    'g2'=>2,
                    'g3'=>3,
                    'g4'=>4,
                    'g5'=>5,
                    'g6'=>6,
                ];
            foreach ($surveys as $r) {
                $output[] = [
                    $r->client_id,
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
            self::outputCSV($output,'result.csv');
            return;
        }

            }
    }

    public function sendSurveyEmail(Request $request){
        $rules= array(
            'client_id' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $message='<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m){
                $message.='<li>'.$m.'</li>';
            }
            $message.='</ul></div>';
            return response()->json(['message'=>$message,'success'=>false])->setCallback($request->input('callback'));
        } else {
            $data=$request->input();
            $user=Survey1Clients::findOrFail($data['client_id']);
            $email= $user->email;
            $reportLink=$user->result_link;
            if($reportLink){
                $html=view('emails.survey_report_link',compact('user','reportLink'))->render();
                sendGridEmailToUser($html,$email,$user->name,'نتائج اختبارات السمات الشخصية من إعمل بيزنس');
                return response()->json(['message'=>'success','success'=>true])->setCallback($request->input('callback'));
            }
            return response()->json(['message'=>'failed','success'=>false])->setCallback($request->input('callback'));
        }
    }

    function outputCSV($data,$file_name = 'file.csv') {
        # output headers so that the file is downloaded rather than displayed
        //header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file_name");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");
        $output="";
        # Then loop through the rows
        foreach ($data as $row) {
            # Add the rows to the body
            $output.=implode(',',$row)."\n";
        }
        # Close the stream off
        print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
    }
}
