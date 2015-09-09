<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
$result = array();
$phone = optional_param('phone', true, PARAM_TEXT);

if(strlen($phone) != 11){
    $result["exception"] = "verification";
    $result["errorcode"] = "phone error";
    $result["message"] = "手机号无效";
}else{
    //获取用户信息
    $sql = ' deleted = 0 AND phone2 = :phone2';
    $sqlparams = array();
    $sqlparams["phone2"] = $phone;
    $users = $DB->get_records_select('user', $sql, $sqlparams, 'id ASC');
    if($users)
    {
        $users_info = (array)$users[$userid];
        //发送手机短信
        $url="http://ysy.crtvup.com.cn/userCenter/SingleVersion?itname=phonevalidate&phone=".$phone."&udid=1234344&sendtype=3";
        //$url="http://172.19.42.53:5000/userCenter/SingleVersion?itname=phonevalidate&phone=".$phone."&udid=1234344&sendtype=3";
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
        $con = curl_exec ( $ch );
        curl_close ( $ch );
        //$con = file_get_contents($url);
        $conten_arr = (array)json_decode($con);
        if ($conten_arr["status"] == "1") {
            $code = $conten_arr["code"];
            //===========返回结果集================            
            $result["code"]=$code;
            //$code = rand(1000 , 9999);
            if($code != ""){
                $result["code"] = $code;
            }else{
                $result["exception"] = "verification";
                $result["errorcode"] = "code get failure";
                $result["message"] = "验证码信息为空";
            }
        }else{
            $result["exception"] = "verification";
            $result["errorcode"] = "code get failure";
            $result["message"] = "验证码获取失败";
        }
    }else{
        $result["exception"] = "verification";
        $result["errorcode"] = "phone exit";
        $result["message"] = "手机号不存在";
    }

}
echo json_encode($result);