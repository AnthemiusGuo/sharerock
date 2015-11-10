<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms {
    private $CI;
    function __construct() {
        $this->CI = & get_instance();
        $this->lastCallbackResult = null;
    }

    function send($mobile = '', $message = '', $schedule_ts = '', $repeat = '', $seq_num = '') {
        $message .= "。系统发送【专车道】";
        $ret = $this->send_sdk($mobile, $message); 
        //$message .= "。系统发送，请勿回复。【第九城市】";
        //$ret = $this->send_9c($mobile, $message); 
        return $ret;
    }
    
    function send_9c($mobile = '', $message = '', $schedule_ts = '', $repeat = '', $seq_num = '') {
        $username = 'SDK-BBX-010-19321';
        $pwd = '6C9-9816';
        $password = strtoupper(md5($username . $pwd));
        $sdk_url = "http://sdk162.entinfo.cn/webservice.asmx/mt";
        $fields = "Sn={$username}&Pwd={$password}&Mobile={$mobile}&Content={$message}&stime={$schedule_ts}&Ext={$repeat}&Rrid={$seq_num}";
        $fields = iconv("utf-8", "gb2312//IGNORE", $fields);
        $ret = $this->CI->utility->get($sdk_url, $fields);
        $ret = simplexml_load_string($ret);
        $_ret = json_decode(json_encode($ret),true);
        if (substr($_ret[0], 0, 1) == '-' || empty($_ret[0])) {
            return false;
        }
        return true;
    }
    
    function send_sdk($mobile = '', $message = '', $schedule_ts = '', $repeat = '', $seq_num = '') {
        $username = 'zhuanchedao';
        $pwd = '100076';
        $password = strtoupper(md5($username . $pwd));
        $sdk_url = "http://sms.chanzor.com:8001/sms.aspx";
        $fields = "action=send&userid=&account={$username}&password={$pwd}&mobile={$mobile}&sendTime={$schedule_ts}&content=".rawurlencode($message);
        $gets = $this->CI->utility->post($sdk_url, $fields);
        $start = strpos($gets,"<?xml");
        $data = substr($gets,$start);
        $ret = simplexml_load_string($data);
        $_ret = json_decode(json_encode($ret),true);
        $this->lastCallbackResult = $_ret;
        if ($_ret['returnstatus'] !== 'Success') {
            return false;
        }
        return true;
    }
}
