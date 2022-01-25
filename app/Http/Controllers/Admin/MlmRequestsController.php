<?php

namespace App\Http\Controllers\Admin;

use App\MlmRequests;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class MlmRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.mlm_requests.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $requests = MlmRequests::leftjoin('users','users.id','=','mlm_requests.user_id')
        ->select('mlm_requests.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $requests = $requests->where('mlm_requests.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $requests = $requests->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['ip']) && !empty($data['ip'])) {
            $ip = $data['ip'];
            $requests = $requests->where('mlm_requests.ip', 'LIKE', "%$ip%");
        }
        if (isset($data['data_string']) && !empty($data['data_string'])) {
            $data_string = $data['data_string'];
            $requests = $requests->where('mlm_requests.data', 'LIKE', "%$data_string%");
        }
        if (isset($data['url']) && !empty($data['url'])) {
            $url = $data['url'];
            $requests = $requests->where('mlm_requests.url','LIKE', "%$url%");
        }
        if (isset($data['response']) && !empty($data['response'])) {
            $response = $data['response'];
            $requests = $requests->where('mlm_requests.response','LIKE', "%$response%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $requests = $requests->whereBetween('mlm_requests.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $requests->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mlm_requests.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mlm_requests.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'mlm_requests.ip';
                break;
            case 3:
                $columnName = 'mlm_requests.data';
                break;
            case 4:
                $columnName = 'mlm_requests.url';
                break;
            case 5:
                $columnName = 'mlm_requests.response';
                break;
            case 6:
                $columnName = 'mlm_requests.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $requests = $requests->where(function ($q) use ($search) {
                $q->where('mlm_requests.ip', 'LIKE', "%$search%")
                    ->orWhere('mlm_requests.data', 'LIKE', "%$search%")
                    ->orWhere('mlm_requests.url', 'LIKE', "%$search%")
                    ->orWhere('mlm_requests.response', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('mlm_requests.id', '=', $search);
            });
        }

        $requests = $requests->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($requests as $r) {
            $user_email = $r->user_email;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $r->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $r->id,
                $user_email,
                $r->ip,
                $r->data,
                $r->url,
                $r->response,
                $r->createtime,
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
        return response()->json($records)->setCallback($request->input('callback'));
    }
    public function show()
    {
        return view('auth.mlm_requests.show');
    }

    public function process(Request $request)
    {
        if($request->get('query')){
            $query=$request->get('query');
            $user = NormalUser::where('Email','=', $query)->orWhere('Mobile','=', $query)->first();
            if(!$user){
                $output='<h3 class="text-center">'.Lang::get('main.no_results_available').'</h3>';
            }
            else{
                $countrycode=self::getCountryName($user->country,'code');
                $mobilewithdash= $countrycode.'-'.preg_replace('/^'.$countrycode.'/', '', $user->Mobile);
                $sponsor = NormalUser::where('id', '=', $user->sponsorId)->first();
                $sponsor_name=$sponsor!=null?$sponsor->FullName:'';
                $output="<table class='table borderless' style='margin:20px auto;width: 70% !important;'>
                  <tr>
                    <th>". Lang::get('main.id').":</th>
                    <td>$user->id</td>
                    <th>". Lang::get('main.sponsor_id').":</th>
                    <td>$user->sponsorId</td>
                  </tr>
                  <tr>
                    <th>". Lang::get('main.name').":</th>
                    <td>$user->FullName</td>
                    <th>". Lang::get('main.sponsor_name').":</th>
                    <td>$sponsor_name</td>
                  </tr>
                  <tr>
                    <th>". Lang::get('main.email').":</th>
                    <td>$user->Email</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th>". Lang::get('main.mobile').":</th>
                    <td>$mobilewithdash</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                  <td>
                       <button data-id='$user->id' class='btn green send_button'>". Lang::get('main.mlm_requests_send')."</button>
      
                       <button data-id='$user->id' class='btn green update_button'>". Lang::get('main.mlm_requests_update')."</button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan='4' id='response-data'></td>
                  </tr>
                </table>";
            }
            echo $output;
        }
    }

    public function send(Request $request)
    {
        $type=$request->get('type');
        $user_id=$request->get('id');
        $type=strtoupper($type);
        $user=NormalUser::findOrFail($user_id);
        $countrycode=self::getCountryName($user->country,'code');
        $mobilewithdash= $countrycode.'-'.preg_replace('/^'.$countrycode.'/', '', $user->Mobile);
        $data=[
            'SecretKey'=>'Secret',
            'MerchantId'=>'Merchant',
            'memberId'=>$user->id ,
            'sponsorId'=>$user->sponsorId,
            'JoiningDate'=>date("d/m/Y H:i:s", strtotime($user->RegisterDate)),
            'password'=>md5($user->Password),
            'gender'=>'',
            'fullName'=>$user->FullName,
            'BirthdayDate'=>'',
            'mobileNo'=>$mobilewithdash,
            'emailId'=>$user->Email,
            'status'=>'true',
            'ValidToDate'=>date("d/m/Y", strtotime(self::getAcademyExpiredDate($user_id))),
            'postalAddress'=>'',
            'country'=>self::getCountryName($user->country),
            'stateProvince'=>'',
            'city'=>'',
            'zipCode'=>'',
        ];
        if($type=='R'){
            $data['username']=$user->Email;
        }
        echo self::sendApiData($data,"https://mlm.e3melbusiness.com/API/Customer/RegisterOrUpdateCustomer");

    }
    public function getAcademyExpiredDate($user_id){
        $result=DB::connection('mysql2')->table('academy_charge_transaction')->where('user_id', $user_id)->first();
        if($result){
            return $result->expired_date;
        }
        return '';
    }

    public function getCountryName($country_id,$column='name'){
        $name='';
        $result=DB::connection('mysql2')->table('country')->where('id', $country_id)->first();
        if($result){
            $name=$result->$column;
        }
        return$name;
    }

    public function sendApiData($data,$url=""){
        $date=date('Y-m-d H:i:s');
        if($url){
            $user_id=(isset($data['memberId']))?$data['memberId']:'';
            $content="";
            foreach($data as $key=>$value) { $content .= $key.'='.trim($value).'&'; }
            //    echo $content;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $ip=$_SERVER['REMOTE_ADDR'];
            $mlm=new MlmRequests();
            $mlm->user_id=$user_id;
            $mlm->ip=$ip;
            $mlm->data=$content;
            $mlm->url=$url;
            $mlm->response=$json_response;
            $mlm->createtime=$date;
            $mlm->save();
            return $json_response;
        }
    }


}
