<?php
function convertToDateTimeLocal($date)
{
    return (!empty($date) && $date != '0000-00-00 00:00:00') ? date("Y-m-d", strtotime($date)) . 'T' . date("H:i:s", strtotime($date)) : '';
}

function PerUser($val)
{
    $UserPermissionsData = \Illuminate\Support\Facades\Request::get('UserPermissionsData');
    return (isset($UserPermissionsData->$val) && $UserPermissionsData->$val) ? true : false;
}

function timeAgo($time)
{
    $time = strtotime($time);
    $time = time() - $time; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }
}

function makeDefaultImage($post, $name)
{
    if (!(!empty($post->img_dir) && !empty($post->img) && file_exists(public_path($post->img_dir . $post->img)))) {
        $post->img_dir = '/img/' . $name . '/';
        $post->img = 'default_image.png';
    }
    return $post;
}

function makeDefaultImageGeneral($post, $image_field_name,$path='')
{
    if (empty($post->$image_field_name) || !file_exists(filePath().$path.$post->$image_field_name)) {
        $post->$image_field_name='none.png';
    }
    return $post;
}


function userSystem()
{
    if(Auth::check()){
        $system = \App\UsersSystems::where('user_id', Auth::user()->id)->first();
        if (!count((array)$system)) {
            $system = new \App\UsersSystems();
            $system->user_id = Auth::user()->id;
            $system->backend_lang = 'en';
            $system->save();
        }
        return $system;
    }
    return ['backend_lang'=>'ar'];
}

function getUserSystem($pars)
{
    $userSystemData = \Illuminate\Support\Facades\Request::get('UserSystem');
    return (isset($userSystemData->$pars)) ? $userSystemData->$pars : '';
}

function byUser($user_id, $string = null)
{
    $user = DB::table('users')->where('id', $user_id)->first();
    if (count($user)) {
        if ($user->img_dir == '' || $user->img == '') {
            $user->img_dir = 'img/Users/';
            $user->img = 'default_user.png';
        }
        return '<div class="zoom_img"><img class="img-polaroid " src="' . asset($user->img_dir . $user->img) . '" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . $string . $user->name . '"></div>';
    }
    return Lang::get('main.no_image');
}

function byAppUser($user_id, $string = null)
{
    $user = \App\AppUsers::find($user_id);
    if (count($user)) {
        if ($user->img_dir == '' && $user->img == '') {
            $user->img_dir = 'img/Users/';
            $user->img = 'default_user.png';
        }
        return '<div class="zoom_img"><img class="img-polaroid " src="' . asset($user->img_dir . $user->img) . '" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . $string . ' { ' . Lang::get('main.' . $user->type) . ' } ' . $user->name . '"></div>';
    }
    return Lang::get('main.no_image');
}

function FileImage($file, $folder_name, $input_name = 'image')
{
    $path = '/img/' . $folder_name . '/' . date('Y/m/d') . '/';
    if (!file_exists(public_path() . $path)) {
        File::makeDirectory(public_path() . $path, $mode = 0777, true, true);
    }
    if (!file_exists(public_path() . $path . 'thumbnail')) {
        File::makeDirectory(public_path() . $path . 'thumbnail', $mode = 0777, true, true);
    }
    //file new name
    $namefile = $folder_name . '_' . rand(0000, 9999) . '_' . time();
    //file extension
    $ext = $file->getClientOriginalExtension();
    //file old name
    $old_name = $file->getClientOriginalName();
    //convert the size of the file
    //$size = ImageUploader::GetSize($file->getSize());
    //get the mime type of the file
    $mimtype = $file->getMimeType();
    //making the new name with extension
    $mastername = $namefile . '.' . $ext;
    list($width, $height, $type, $attr) = getimagesize($_FILES[$input_name]['tmp_name']);
    $width_per = round(($width * 10) / 100);
    $height_per = round(($height * 10) / 100);
    $file->move(public_path() . $path, $mastername);
    Image::make(public_path() . $path . $mastername, array(
        'width' => $width_per,
        'height' => $height_per,
    ))->save(public_path() . $path . 'thumbnail/thumbnail_' . $mastername);
    return array('img' => $mastername, 'img_dir' => $path);
}

function FileImages($file, $folder_name, $x, $input_name = 'images')
{
    $path = '/img/' . $folder_name . '/' . date('Y/m/d') . '/';
    if (!file_exists(public_path() . $path)) {
        File::makeDirectory(public_path() . $path, $mode = 0777, true, true);
    }
    if (!file_exists(public_path() . $path . 'thumbnail')) {
        File::makeDirectory(public_path() . $path . 'thumbnail', $mode = 0777, true, true);
    }
    //file new name
    $namefile = $folder_name . '_' . rand(0000, 9999) . '_' . time();
    //file extension
    $ext = $file->getClientOriginalExtension();
    //file old name
    $old_name = $file->getClientOriginalName();
    //convert the size of the file
    //$size = ImageUploader::GetSize($file->getSize());
    //get the mime type of the file
    $mimtype = $file->getMimeType();
    //making the new name with extension
    $mastername = $namefile . '.' . $ext;
    list($width, $height, $type, $attr) = getimagesize($_FILES[$input_name]['tmp_name'][$x]);
    $width_per = round(($width * 10) / 100);
    $height_per = round(($height * 10) / 100);
    $file->move(public_path() . $path, $mastername);
    switch ($folder_name) {
        case'hotels':
            $imagesResize = [
                0 => ['width' => 60, 'height' => 60],
                1 => ['width' => 260, 'height' => 180],
                2 => ['width' => 400, 'height' => 200],
            ];
            break;
        case'flights':
            $imagesResize = [
                0 => ['width' => 60, 'height' => 60],
                1 => ['width' => 260, 'height' => 180],
                2 => ['width' => 400, 'height' => 200],
            ];
            break;
        default:
            $imagesResize = [];
            break;
    }
    foreach ($imagesResize as $imageSize) {
        $widthS = $imageSize['width'];
        $heightS = $imageSize['height'];
        Image::make(public_path() . $path . $mastername, array(
            'width' => $widthS,
            'height' => $heightS,
        ))->save(public_path() . $path . 'thumbnail/' . $widthS . '_' . $heightS . '_' . $mastername);
    }
    Image::make(public_path() . $path . $mastername, array(
        'width' => $width_per,
        'height' => $height_per,
    ))->save(public_path() . $path . 'thumbnail/thumbnail_' . $mastername);
    return array('img' => $mastername, 'img_dir' => $path);
}

function tableCount($table)
{
    if (Schema::hasTable($table)) {
        $section = DB::table($table)->count();
        return $section;
    }
    return 0;
}

function getDaysName()
{
    $timestamp = strtotime('next Sunday');
    $days = array();
    for ($i = 0; $i < 7; $i++) {
        $days[] = strtolower(strftime('%A', $timestamp));
        $timestamp = strtotime('+1 day', $timestamp);
    }
    return $days;
}

function fileNewName($name, $filePath)
{
    $actual_name = pathinfo($name, PATHINFO_FILENAME);
    $original_name = $actual_name;
    $extension = pathinfo($name, PATHINFO_EXTENSION);
    $i = 1;
    while (file_exists($filePath . $actual_name . "." . $extension)) {
        $actual_name = (string)$original_name . '(' . $i . ')';
        $name = $actual_name . "." . $extension;
        $i++;
    }
    return $name;
}

function uploadFileToE3melbusiness($file, $partner = false, $folder = null,$audio_books=false,$course_question=false)
{
    $filePath = '';
    $folder = ($folder) ? $folder . '/' : $folder;
    if ($partner == true) {
        $filePath = partnerFilePath();
    }elseif($audio_books){
        $filePath = audioBooksFilePath();
    }elseif($course_question){
        $filePath = coursesQuestionsFilePath();
    }else {
        $filePath = filePath();
    }
    $ext = $file->getClientOriginalExtension();
    //file old name
    $old_name = $file->getClientOriginalName();
    $newname = fileNewName($old_name, $filePath);
    if (!file_exists($filePath . $folder)) {
        File::makeDirectory($filePath . $folder, $mode = 0777, true, true);
    }
    $file->move($filePath . $folder, $newname);
    return $newname;
}

function e3mURL($url)
{
    return ($_SERVER['SERVER_NAME'] == 'localhost') ? 'http://localhost/e3melbusinessV5/' . $url : 'https://www.e3melbusiness.com/' . $url;
}

function yottaURL($url)
{
    return ($_SERVER['SERVER_NAME'] == 'localhost') ? 'http://localhost/yotta/' . $url : 'https://www.yottamedicalschool.com/' . $url;
}

function assetURL($url = '')
{
    //return 'https://www.e3melbusiness.com/assets/images/'.$url;
    return (!in_array($_SERVER['SERVER_NAME'],['e3melbusiness.com','www.e3melbusiness.com']) ) ? 'http://'.$_SERVER['SERVER_NAME'].'/e3melbusinessV5/assets/images/' . $url : "https://www.e3melbusiness.com/assets/images/" . $url;
}
function mainAssetURL($url = '')
{
    return (!in_array($_SERVER['SERVER_NAME'],['e3melbusiness.com','www.e3melbusiness.com']) ) ? 'http://'.$_SERVER['SERVER_NAME'].'/e3melbusinessV5/assets/' . $url : "https://www.e3melbusiness.com/assets/" . $url;
}
function filePath()
{
    //return explode('sa.e3melbusiness.com',public_path())[0].'sa.e3melbusiness.com/assets/images/';
    return (!in_array($_SERVER['SERVER_NAME'],['e3melbusiness.com','www.e3melbusiness.com']) ) ? explode('e3melbusinessV5', public_path())[0] . 'e3melbusinessV5/assets/images/' : explode('sa.e3melbusiness.com', public_path())[0] . 'sa.e3melbusiness.com/assets/images/';
}
function audioBooksFilePath()
{
    //return explode('sa.e3melbusiness.com',public_path())[0].'sa.e3melbusiness.com/assets/images/';
    return (!in_array($_SERVER['SERVER_NAME'],['e3melbusiness.com','www.e3melbusiness.com']) ) ? explode('adminResources', public_path())[0] . 'e3melbusinessV5/assets/img/audio_books/' : explode('sa.e3melbusiness.com', public_path())[0] . 'sa.e3melbusiness.com/assets/img/audio_books/';
}
function coursesQuestionsFilePath()
{
    //return explode('sa.e3melbusiness.com',public_path())[0].'sa.e3melbusiness.com/assets/images/';
    return (!in_array($_SERVER['SERVER_NAME'],['e3melbusiness.com','www.e3melbusiness.com']) ) ? explode('adminResources', public_path())[0] . 'e3melbusinessV5/assets/courses_questions/' : explode('sa.e3melbusiness.com', public_path())[0] . 'sa.e3melbusiness.com/assets/courses_questions/';
}

function partnerFilePath()
{
    return (!in_array($_SERVER['SERVER_NAME'],['e3melbusiness.com','www.e3melbusiness.com']) ) ? explode('e3melbusinessV5', public_path())[0] . 'e3melbusinessV5/assets/images/ourpartners/' : explode('sa.e3melbusiness.com', public_path())[0] . 'sa.e3melbusiness.com/assets/images/ourpartners/';
}

function sendGridEmailToUser($html,$email,$name,$subject){
    try{
        $sendEmail = new \SendGrid\Mail\Mail();
        $sendEmail->setFrom('Academy@e3melbusiness.com', 'أكاديمية إعمل بيزنس');
        $sendEmail->setSubject($subject);
        $sendEmail->addTo($email, $name);
        $sendEmail->addContent("text/html", $html);
    }catch (\SendGrid\Mail\TypeException $e){
        return ['message'=>$e->getMessage(),'success'=>false];
    }
    try {
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
    }catch (Exception $e){
        return ['message'=>$e->getMessage(),'success'=>false];
    }catch (\SendGrid\Mail\TypeException $e){
        return ['message'=>$e->getMessage(),'success'=>false];
    }
    try{
        $sendgrid->send($sendEmail);
    }catch (\SendGrid\Mail\TypeException $e){
        return ['message'=>$e->getMessage(),'success'=>false];
    }
    return ['message'=>'success','success'=>true];
}

/*chargeTransaction*/
function sendChargeTransactionToCRM($transaction_id,$tag_name,$email,$phone,$type,$name,$period,$start_date,$end_date,$price,$createdTime,$pending=null,$suspend=null,$rwaq=0){
    $url='http://crmegy.e3melbusiness.com/webservice/subscription.php';
    if($pending==0){
        $date=date('Y-m-d');
        $start_date=date('Y-m-d',strtotime($start_date));
        $end_date=date('Y-m-d',strtotime($end_date));
        if (!(($date >= $start_date) && ($date <= $end_date))&&!($start_date>$date&&$end_date>$date)){
            $pending=2;
        }
    }
    $data=[
        'transaction_id'=>$transaction_id,
        'tag_name'=>$tag_name,
        'email'=>$email,
        'phone'=>$phone,
        'type'=>$type,
        'name'=>$name,
        'period'=>$period,
        'start_date'=>date('Y-m-d',strtotime($start_date)),
        'end_date'=>date('Y-m-d',strtotime($end_date)),
        'unit_price'=>$price,
        'createdtime'=>date('Y-m-d H:i:s',strtotime($createdTime)),
        'pending'=>$pending,
        'suspend'=>$suspend,
        'rwaq'=>$rwaq,
    ];
    //print_r($data);
    //print_r($url);
    $content="";
    foreach($data as $key=>$value) { $content .= $key.'='.$value.'&'; }
    //echo $content;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    //echo $json_response;
    //echo '<br><br>';
    //echo $json_response;
    //$ip=$_SERVER['REMOTE_ADDR'];
    //$query="INSERT INTO api_logs_send SET `ip`='$ip',`data`='$content',`url`='$url',`response`='$json_response',`createtime`='$date'";
    //self::execquery($query);
    $url='http://crmksa2.almoasherbiz.com/webservice/subscription.php';
    //print_r($data);
    //print_r($url);
    $content="";
    foreach($data as $key=>$value) { $content .= $key.'='.$value.'&'; }
    //echo $content;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    //echo $json_response;
    //echo '<br><br>';
    //echo $json_response;
    //$ip=$_SERVER['REMOTE_ADDR'];
    //$query="INSERT INTO api_logs_send SET `ip`='$ip',`data`='$content',`url`='$url',`response`='$json_response',`createtime`='$date'";
    //self::execquery($query);
}//49858
function deleteChargeTransactionFromCRM($transaction_id,$tag_name){
    $url='http://crmegy.e3melbusiness.com/webservice/delete_subscription.php';
    $data=[
        'transaction_id'=>$transaction_id,
        'tag_name'=>$tag_name,
    ];
    //print_r($data);
    //print_r($url);
    $content="";
    foreach($data as $key=>$value) { $content .= $key.'='.$value.'&'; }
    //echo $content;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    //echo '<br><br>';
    //echo $json_response;
    //echo $json_response;
    //$ip=$_SERVER['REMOTE_ADDR'];
    //$query="INSERT INTO api_logs_send SET `ip`='$ip',`data`='$content',`url`='$url',`response`='$json_response',`createtime`='$date'";
    //self::execquery($query);
    $url='http://crmksa2.almoasherbiz.com/webservice/delete_subscription.php';
    //print_r($data);
    //print_r($url);
    $content="";
    foreach($data as $key=>$value) { $content .= $key.'='.$value.'&'; }
    //echo $content;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    //echo '<br><br>';
    //echo $json_response;
    //echo $json_response;
    //$ip=$_SERVER['REMOTE_ADDR'];
    //$query="INSERT INTO api_logs_send SET `ip`='$ip',`data`='$content',`url`='$url',`response`='$json_response',`createtime`='$date'";
    //self::execquery($query);
}
 function sendChargeTransaction($charge_id){
    $charge=\App\ChargeTransaction::select('charge_transaction.*','users.country AS country','users.Email AS email','users.Mobile AS phone')->join('users','users.id','charge_transaction.user_id')->find($charge_id);
    if(count($charge)){
        sendChargeTransactionToCRM($charge->id,'charge transaction',$charge->email,$charge->phone,$charge->subscrip_type,'charge for client',$charge->period,$charge->start_date,$charge->end_date,$charge->amount,$charge->createtime,$charge->pending,$charge->suspend);
    }


}
 function sendDiplomasChargeTransaction($charge_id){
     $charge=\App\DiplomasChargeTransaction::select('diplomas_charge_transaction.*','users.country AS country','users.Email AS email','users.Mobile AS phone')->join('users','users.id','diplomas_charge_transaction.user_id')->find($charge_id);
     if(count($charge)){
         sendChargeTransactionToCRM($charge->id,'diplomas transaction',$charge->email,$charge->phone,$charge->subscrip_type,$charge->diploma_name,$charge->period,$charge->start_date,$charge->end_date,$charge->diploma_price,$charge->createtime,$charge->pending,$charge->suspend,$charge->rwaq);
     }


}
 function sendMbaChargeTransaction($charge_id){
     $charge=\App\MbaChargeTransaction::select('mba_charge_transaction.*','users.country AS country','users.Email AS email','users.Mobile AS phone')->join('users','users.id','mba_charge_transaction.user_id')->find($charge_id);
     if(count($charge)){
         sendChargeTransactionToCRM($charge->id,'mba transaction',$charge->email,$charge->phone,$charge->subscrip_type,'MBA',$charge->period,$charge->start_date,$charge->end_date,$charge->mba_price,$charge->createtime,$charge->pending,$charge->suspend);
     }


}
 function sendMedicalChargeTransaction($charge_id){
     $charge=\App\MedicalChargeTransactions::select('medical_charge_transactions.*','users.country AS country','users.Email AS email','users.Mobile AS phone')->join('users','users.id','medical_charge_transactions.user_id')->find($charge_id);
     if(count($charge)){
         sendChargeTransactionToCRM($charge->id,'medical transaction',$charge->email,$charge->phone,$charge->subscrip_type,'Medical',$charge->period,$charge->start_date,$charge->end_date,$charge->amount,$charge->createtime,$charge->pending,$charge->suspend);
     }
}
function sendLiteVersionChargeTransaction($charge_id){
    $charge=\App\LiteVersionChargeTransaction::select('lite_version_charge_transaction.*','users.country AS country','users.Email AS email','users.Mobile AS phone')->join('users','users.id','lite_version_charge_transaction.user_id')->find($charge_id);
    if(count($charge)){
        sendChargeTransactionToCRM($charge->id,'lite version  transaction',$charge->email,$charge->phone,$charge->subscrip_type,'Lite Version',$charge->period,$charge->start_date,$charge->end_date,$charge->amount,$charge->createtime,$charge->pending,$charge->suspend);
    }
}
function updateAcademyChargeTransaction($user_id){
    $expiredDate=NULL;
    $charge=\App\ChargeTransaction::where('user_id',$user_id)->orderBy('end_date','DESC')->first();
    if(count($charge)){
        $expiredDate=($charge->end_date>$expiredDate)?$charge->end_date:$expiredDate;
    }
    $charge=\App\DiplomasChargeTransaction::where('user_id',$user_id)->orderBy('end_date','DESC')->first();
    if(count($charge)){
        $expiredDate=($charge->end_date>$expiredDate)?$charge->end_date:$expiredDate;
    }
    $charge=\App\MbaChargeTransaction::where('user_id',$user_id)->orderBy('end_date','DESC')->first();
    if(count($charge)){
        $expiredDate=($charge->end_date>$expiredDate)?$charge->end_date:$expiredDate;
    }
    $charge=\App\MedicalChargeTransactions::where('user_id',$user_id)->orderBy('end_date','DESC')->first();
    if(count($charge)){
        $expiredDate=($charge->end_date>$expiredDate)?$charge->end_date:$expiredDate;
    }
    if($expiredDate){
        $academy_charge_transaction=\App\AcademyChargeTransaction::where('user_id',$user_id)->first();
        if(count($academy_charge_transaction)){
            $type='U';
            $academy_charge_transaction->expired_date=$expiredDate;
            $academy_charge_transaction->save();
        }else{
            $type='R';
            $academy_charge_transaction=new \App\AcademyChargeTransaction();
            $academy_charge_transaction->user_id=$user_id;
            $academy_charge_transaction->expired_date=$expiredDate;
            $academy_charge_transaction->save();
        }
        RegisterOrUpdateCustomerAPI($user_id,$type);
    }

}
function getAcademyExpiredDate($user_id){
    $academy=\App\AcademyChargeTransaction::where('user_id',$user_id)->first();
    if(count($academy)){
        return $academy->expired_date;
    }
    return '';
}

function RegisterOrUpdateCustomerAPI($user_id,$type='R'){

    $type=strtoupper($type);
    $user=\App\NormalUser::find($user_id);
    $country=\App\Country::find($user->country);
    $countrycode=$country->code;
    $mobilewithdash= $countrycode.'-'.preg_replace('/^'.$countrycode.'/', '', $user->Mobile);
    if(isset($user->id)){
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
            /*    'DateOfBirthDay'=>22,
                'DateOfBirthMonth'=>4,
                'DateOfBirthYear'=>1991,*/
            'mobileNo'=>$mobilewithdash,//self::getCountryName($user->country,'code').'-'.$user->Mobile
            'emailId'=>$user->Email,
            'status'=>'true',
            /* 'nomDateOfBirthDay'=>date('d',strtotime($user->RegisterDate)),
             'nomDateOfBirthMonth'=>date('m',strtotime($user->RegisterDate)),
             'nomDateOfBirthYear'=>date('Y',strtotime($user->RegisterDate)),*/

            'ValidToDate'=>date("d/m/Y", strtotime(getAcademyExpiredDate($user_id))),
            'postalAddress'=>'',
            'country'=>$country->name,
            'stateProvince'=>'',
            'city'=>'',
            'zipCode'=>'',
        ];
        if($type=='R'){
            $data['username']=$user->Email;

            /* $data['JoiningDay']=date('d',strtotime($user->RegisterDate));
             $data['JoiningMonth']=date('m',strtotime($user->RegisterDate));
             $data['JoiningYear']=date('Y',strtotime($user->RegisterDate));*/
            // $data['JoiningDate']=date("d-m-Y H:i:s", strtotime($user->RegisterDate));
            // 'JoiningDate'=>date("d-m-Y H:i:s", strtotime($user->RegisterDate)),
            //  $data['password']=md5($user->Password);
        }
        // echo json_encode( $data);

        //self::sendApiData($data,$url="https://www.e3melbusiness.com/?page=testapi&action=sendRequest");
        sendApiData($data,"https://mlm.e3melbusiness.com/API/Customer/RegisterOrUpdateCustomer");
    }
}
function sendApiData($data,$url="")
{
    $date = date('Y-m-d H:i:s');
    if ($url) {
        $user_id = (isset($data['memberId'])) ? $data['memberId'] : '';
        $content = "";
        foreach ($data as $key => $value) {
            $content .= $key . '=' . trim($value) . '&';
        }
        //    echo $content;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //print_r($data);
        //echo '<br><br><br>';
        curl_close($curl);
        //echo $json_response;
        $ip = $_SERVER['REMOTE_ADDR'];
        $mlm_requests = new \App\MlmRequests();
        $mlm_requests->user_id = $user_id;
        $mlm_requests->ip = $ip;
        $mlm_requests->data = $content;
        $mlm_requests->url = $url;
        $mlm_requests->response = $json_response;
        $mlm_requests->createtime = $date;
        $mlm_requests->save();
    }
}

function saveOldUrl($table_id,$table_name,$old_url,$new_url,$add_by,$createtime){
    $record=new \App\OldUrls();
    $record->table_id=$table_id;
    $record->table_name=$table_name;
    $record->old_url=$old_url;
    $record->new_url=$new_url;
    $record->add_by=$add_by;
    $record->createtime=$createtime;
    $record->lastedit_by=$add_by;
    $record->lastedit_date=$createtime;
    $record->save();
}
function log_admin_action($user_id=null,$user_name=null,$user_action=null,$user_action_table=null,$user_action_table_id=null,$data_json=null){
    $log=new \App\AdminLog();
    $log->user_id=$user_id;
    $log->user_name=$user_name;
    $log->user_action=$user_action;
    $log->user_action_table=$user_action_table;
    $log->user_action_table_id=$user_action_table_id;
    $log->data_json=$data_json;
    $log->created_at=date("Y-m-d H:i:s");
    $log->save();
}

function sendRequestData($data,$url='https://www.e3melbusiness.com/?page=api&action=sendAdminEmail',$method='POST'){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST =>  $method,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo$response;
    }
}

function generateRandomString($length = 10,$small_letters='all',$capital_letters='all')
{
    $characters = '0123456789';
    if($small_letters=='all') {
        $characters .= 'abcdefghijklmnopqrstuvwxy';
    }
    if($capital_letters=='all'){
        $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
