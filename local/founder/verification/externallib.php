<?php
require_once($CFG->libdir . "/externallib.php");

class local_verification_external extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_verification_code_parameters() {
        return new external_function_parameters(
            array('phone' => new external_value(PARAM_TEXT, 'the info of phone2')
            )
        );
    }

    /**
     * Returns verification code
     * @return array verification code
     */
    public static function get_verification_code($phone) {
        global $USER,$DB;

        $params = self::validate_parameters(self::get_verification_code_parameters(),
            array('phone' => $phone));
        //发送手机短信
        $url="http://ysy.crtvup.com.cn/userCenter/SingleVersion?itname=phonevalidate&phone=".$phone."&udid=1234344&sendtype=3";
        //$url="http://172.19.42.53:5000/userCenter/SingleVersion?itname=phonevalidate&phone=".$phone."&udid=1234344&sendtype=3";
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
        $con = curl_exec ( $ch );
        curl_close ( $ch );

        $conten_arr = (array)json_decode($con);
        if ($conten_arr["status"] == "1") {
            $code = $conten_arr["code"];
            //===========返回结果集================
            $result=array();
            $result["code"]=$code;
            return $result;
        }else{
            throw new moodle_exception('Failed to send text messages', 'error');
        }
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_verification_code_returns() {
        return new external_single_structure(
            array(
                'code' => new external_value(PARAM_RAW, 'Verification code information')
            )
        );
    }



    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function check_verification_code_parameters() {
        return new external_function_parameters(
            array('userid' => new external_value(PARAM_TEXT, 'the info of userid'),
                'code' => new external_value(PARAM_INT, 'Verification code information')

            )
        );
    }

    /**
     * Returns resulet
     * @return array resulet
     */
    public static function check_verification_code($userid,$code) {
        global $USER,$DB,$CFG;
        require_once($CFG->dirroot."/user/lib.php");

        $params = self::validate_parameters(self::check_verification_code_parameters(),
            array('userid' => $userid , 'code' => $code));

        //================查询条件===============
        $sql = ' deleted = 0 AND id = :id AND verification_code = :verification_code';
        $sqlparams = array();
        $sqlparams["id"] = $userid;
        $sqlparams["verification_code"] = $code;

        //===============返回结果=============
        $users = array();
        $result=array();

        $users = $DB->get_records_select('user', $sql, $sqlparams, 'id ASC');
        if (empty($users))
            $result["result"]='false';
        else
            $result["result"]='true';

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function check_verification_code_returns() {
        return new external_single_structure(
            array(
                'result' => new external_value(PARAM_RAW, 'resulet of chek code')
            )
        );
    }
}