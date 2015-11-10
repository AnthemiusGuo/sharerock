<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apiwx {
	private $CI;
	public $uid;
	public $cookie_onlineId;
	public $lastCallbackResult;
	public $userInfo;

	public function __construct() {
		$this->CI =& get_instance();
		$this->db = $this->CI->cimongo;
		$this->wxInfo = array('access_token' => '',
		'expires_in'=>0 );
		$this->CI->load->library('Utility');

		$this->getWXConfig();
		//$this->_user = $this->CI->session->userdata('user');
	}

	public function getWXConfig(){
		$this->wxConfig = $this->CI->config->item('wx');
		// $qq['callback'] = site_url('index/doQQLogin');
		return $this->wxConfig;
	}

	public function gen_login_url($goto_url,$typ){
		// $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wxConfig['appid']."&redirect_uri=".urlencode($goto_url)."&response_type=code&scope=snsapi_base&state=".$typ."#wechat_redirect";
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wxConfig['appid']."&redirect_uri=".urlencode($goto_url)."&response_type=code&scope=snsapi_userinfo&state=".$typ."#wechat_redirect";
		return $url;
	}

	

	public function create_menu(){
		$access_token = $this->get_access_token();
		if ($access_token===false){
			return;
		}

		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
		$data = array(
		    "button"=>array(
		                array("type"=>"view",
               				"name"=>"预约服务",
               				"url"=>'http://fixucar.wanjiakehu.com/index.php/api/wxjump/create'),
		                array("type"=>"view",
               				"name"=>"历史订单",
               				"url"=>'http://fixucar.wanjiakehu.com/index.php/api/wxjump/lists'),
		                array("type"=>"view",
               				"name"=>"我的信息",
               				"url"=>'http://fixucar.wanjiakehu.com/index.php/api/wxjump/index'),
		                    )
      	);
		$rst = $this->CI->utility->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
    	$json = json_decode($rst,true);
    	$this->lastCallbackResult = $json;
    	if (isset($json['errcode'])){
    		if ($json['errcode']==0){
    			return true;
    		}
    		return false;
    	} else {
    		return true;
    	}

	}

	public function get_user_info($userInfo){
https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$userInfo['access_token'].'&openid='.$userInfo['openid'].'&lang=zh_CN';
		$rst = $this->CI->utility->curl_get($url);
		$json = json_decode($rst,true);	
    	$this->lastCallbackResult = $json;
    	if (isset($json['errcode']) && $json['errcode']!=0){

    		return false;
    	} else {
    		$this->baseUserInfo = $json;
    		return true;
    	}
	}

	public function refesh_token($userInfo){
		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->wxConfig['appid'].'&grant_type=refresh_token&refresh_token='.$userInfo['refresh_token'];
		$rst = $this->CI->utility->curl_get($url);
		$json = json_decode($rst,true);	
    	$this->lastCallbackResult = $json;
    	if (isset($json['errcode']) && $json['errcode']!=0){

    		return false;
    	} else {
    		$this->refreshUserInfo = $json;
    		return true;
    	}
	}

	public function get_user_access_token($code){
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wxConfig['appid'].'&secret='.$this->wxConfig['appSecret'].'&code='.$code.'&grant_type=authorization_code';
		$rst = $this->CI->utility->curl_get($url);
		$json = json_decode($rst,true);	
    	$this->lastCallbackResult = $json;
    	if (isset($json['errcode']) && $json['errcode']!=0){

    		return false;
    	} else {
    		$this->userInfo = $json;
    		return true;
    	}
	}

	public function sendMsg($user,$msgId,$content){
		$msgConfig = array('yuyue'=>'ARQHw2Tt0UuAYFX97K7JdL58HeuGdN8j37qDkSK4Oh8');
		$access_token = $this->get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
		$data =  array(
           "touser"=>$user,
           "template_id"=>$msgConfig[$msgId],
           "url"=>site_url('morder/lists'),
           "topcolor"=>"#FF0000",
           "data"=>$content
       	);
        
		$rst = $this->CI->utility->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
    	$json = json_decode($rst,true);
    	$this->lastCallbackResult = $json;
    	if (isset($json['errcode'])){
    		if ($json['errcode']==0){
    			return true;
    		}
            $this->log('sendMsg',$json);
    		return false;
    	} else {
    		return true;
    	}
	}

    public function log($typ,$info){
        log_message('error', $typ.':'.json_encode($info));
    }

	public function get_access_token(){
		$this->getWXConfig();

		$zeit = time();

		$this->db->limit(1);
		$query = $this->db->get('sWxInfo');
        if ($query->num_rows() > 0)
        {
            $this->wxInfo = $query->row_array();

        } else {
            $this->wxInfo = array('access_token' => '',
		'expires_in'=>0 );
            $this->db->insert('sWxInfo',$this->wxInfo);
        }

        if ($this->wxInfo['expires_in']==0 || $this->wxInfo['expires_in']<=$zeit-10){
        	//已经过期或者未获得，重新获取
        	$rst = $this->CI->utility->curl_get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wxConfig['appid'].'&secret='.$this->wxConfig['appSecret']);
        	$json = json_decode($rst,true);	
        	$this->lastCallbackResult = $json;

        	if (isset($json['errcode'])){
        		//err!!
        		return false;
        	}
        	$this->wxInfo['access_token'] = $json['access_token'];
        	$this->wxInfo['expires_in'] = $json['expires_in']+$zeit;

        	$this->db->update('sWxInfo',$this->wxInfo);
        	
        }
        return $this->wxInfo['access_token'];
	}
}
