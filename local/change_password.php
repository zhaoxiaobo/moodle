<?php
require('../config.php');
require_once($CFG->libdir.'/authlib.php');


error_reporting(3);
$result = array();
if(isset($_REQUEST["password"]))
    $password = $_REQUEST["password"];
if(isset($_REQUEST["phone"]))
    $phone = $_REQUEST["phone"];

if ($phone && $password) {
    //获取用户数据
    $userparams = array('phone2' => $phone, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0, 'suspended' => 0);
    $user = $DB->get_record('user', $userparams);
    if($user)
    {
        //数据校验
        if ($user->auth === 'nologin' or !is_enabled_auth($user->auth)) {
            $result["status"]='0';
            $result["msg"]='nologin';
            echo json_encode($result);
            die;
        }

        if (isguestuser($user)) {
            $result["status"]='0';
            $result["msg"]='you are guest';
            echo json_encode($result);
        }

        $userauth = get_auth_plugin($user->auth);
        if (!$userauth->user_update_password($user, $password)) {
            $result["status"]='0';
            $result["msg"]='Failed to modify password';
            echo json_encode($result);
        }else{
            $result["status"]='1';
            $result["msg"]='Success';
            echo json_encode($result);
            add_to_log(SITEID, 'user', 'set password', "view.php?id=$user->id&amp;course=" . SITEID, $user->id);
        }
    }
}else{
    $result["status"]='0';
    $result["msg"]='Parameter is incorrect';
    echo json_encode($result); 
}


