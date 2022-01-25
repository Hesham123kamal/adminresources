<?php

namespace App\Http\Controllers;

use App\Country;
use App\Diplomas;
use App\DiplomasChargeTransaction;
use App\NormalUser;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function register(Request $request){
        if($request->name&&$request->email&&$request->mobile&&$request->country_code&&$request->diploma_code){
            $name=$request->name;
            $email=$request->email;
            $mobile=$request->mobile;
            $diploma_code=$request->diploma_code;
            $country_code=$request->country_code;
            $diploma=Diplomas::where('uniqid',$diploma_code)->first();
            if($diploma){
                $user=NormalUser::where(function($q)use($request){$q->where('Email',$request->email)->orWhere('Mobile',$request->mobile);})->first();
                $country=Country::where('code',$request->country_code)->first();
                if(!$country){
                    return response()->json(['success'=>false,'message'=>'Invalid Country code']);
                }
                if(!$user){
                    $mobile=$country_code.ltrim($mobile,"0");
                    $user=new NormalUser();
                    $user->rwaq = 1;
                    $user->FullName = $name;
                    $user->Email = $email;
                    $user->Mobile = $mobile;
                    $user->Password = $mobile;
                    $user->country = $country->id;
                    $user->createdtime = date("Y-m-d H:i:s");
                    $user->RegisterDate = date("Y-m-d H:i:s");
                    $user->RegisterReferrer = 'RWAQ API';
                    $user->leadsource = 'RWAQ';
                    $user->RegisterIP = $request->ip();
                    $user->save();
                    $parameters=array(
                        'name'=> $name,
                        'email' => $email,
                        'Email' => $email,
                        'Password' => $user->Password,
                        'username' => $name,
                        'country' => $country->id,
                        'phone' => $mobile,
                        'register_link' => $user->RegisterReferrer,
                        'leadsource' => $user->leadsource ,
                    );
                    //$sent=$this->registry->emails->sendemail($parameters['Email'],$parameters);
                    self::transfer($parameters);
                }
                $diplomas_charge_transaction=new DiplomasChargeTransaction();
                $diplomas_charge_transaction->diploma_id=$diploma->id;
                $diplomas_charge_transaction->user_id=$user->id;
                $diplomas_charge_transaction->diploma_name = $diploma->name;
                $diplomas_charge_transaction->diploma_price = $diploma->ksa_price;
                $diplomas_charge_transaction->period = 9;
                $diplomas_charge_transaction->start_date = date('Y-m-d H:i:s');
                $diplomas_charge_transaction->end_date = date('Y-m-d H:i:s',strtotime('+9 month'));
                $diplomas_charge_transaction->rwaq = 1;
                $diplomas_charge_transaction->pending = 0;
                $diplomas_charge_transaction->subscrip_type = 'diploma_paid';
                $diplomas_charge_transaction->save();
                sendDiplomasChargeTransaction($diplomas_charge_transaction->id);
                return response()->json(['success'=>true,'message'=>'success']);
            }
            return response()->json(['success'=>false,'message'=>'Invalid Diploma code']);
        }
        return response()->json(['success'=>false,'message'=>'Failed']);

    }
    public function  diplomas(){
//        $diplomas=Diplomas::where('published','yes')->get();
//        $data=[];
//        foreach ($diplomas as $key=>$diploma){
//            $data[]=[
//              'id'=>  base64_encode('diplomas-'.$diploma->id),
//              'name'=>  $diploma->name,
//              'en_name'=>  $diploma->en_name,
//              'description'=>  $diploma->description,
//              'image'=>  e3mURL('assets/images/'.$diploma->image),
//            ];
//        }
//        return response()->json(['success'=>true,'message'=>'success','result'=>$data]);
        $diplomas=Diplomas::get();
        foreach ($diplomas as $diploma){
            $diploma->uniqid=uniqid('',true);
            $diploma->save();
        }
    }

    function transfer($data)
    {

        //  if (strtoupper (ip_info2())=='EGYPT' && $data['country']=='64')
        if ( $data['country']=='64')
        {
            $url = "http://crmegy.e3melbusiness.com/webservice/new_lead.php";

        }else
        {
            $url = "http://crmksa2.almoasherbiz.com/webservice/new_lead.php";
        }

        /* if ( $last=='ksa')
         {
             $url = "http://crmksa2.almoasherbiz.com/webservice/new_lead.php";

         }else
         {
             $url = "http://crmegy.e3melbusiness.com/webservice/new_lead.php";

         }*/


        // echo $url;
        $content="";
        foreach($data as $key=>$value) { $content .= $key.'='.$value.'&'; }
        //echo $content;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        $json_response = curl_exec($curl);
        print_r($data);
        echo  $json_response;
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        // echo 'Transfer Record '. ' Is '. $json_response.'<br>';
        //$response = json_decode($json_response, true);
        //echo $response['name'];
        //var_dump($response);
    }
}
