<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Index extends Common {
	function __construct() {
		parent::__construct(false,'a');
		$this->load->library('pagination');
	}

	function index() {
		$this->template->load('default_page', 'index/index');
	}

	function noAuth(){
		$this->template->load('default_error', 'index/noAuth');
	}

	function license(){
		$this->infoTitle = "用户协议";
		$this->load->library('markdown');
		$markdown_file_path = APPPATH.'views/index/license.md';
		$this->license_html = $this->markdown->parse_file($markdown_file_path);
		$this->template->load('default_lightbox_info', 'index/license');
	}

	function forgetMe($email,$zeit,$verify_code) {
		$email = base64_decode(urldecode($email));
		$real_verify_code = substr(md5($email.'xUUJKK'.$zeit),5,10);

		while (true) {
			if ($zeit<time()-86400){
				$this->result = false;
				$this->msg = "对不起，您的重置密码请求时间太久了，请重新请求重置密码！";
				break;
			}
			if ($verify_code!=$real_verify_code){
				$this->result = false;
				$this->msg = "您的重置密码请求不正确";
				break;
			}
			$this->result = true;
			$new_password = substr(md5($email.'eeEDD'.time()),7,8);
			$this->load->model('records/adminuser_model',"userInfo");

			$login_rst = $this->userInfo->forceChangePwd($email,$new_password);

			$this->msg = "重置密码成功，您的密码目前是 $new_password ，请复制并且登录后立刻修改密码！";
			break;
		}
		$this->template->load('default_before_login', 'index/forgetMe');

	}
	function forgot() {
		$this->title_create = "忘记密码";
        $this->createUrlC = 'index';
        $this->createUrlF = 'doForgot';
        $this->createPostFields = array(
        	'email'
        );

        $this->template->load('default_lightbox_new', 'index/forgot');
	}

	function doForgot() {
		$email = $this->input->post('email');
		if(!preg_match("/^[0-9a-zA-Z.]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$email )){
            $this->display_error("json","邮箱格式不正确");
        }
        $this->db->select('*')
                    ->from("uUser")
                    ->where('email', $email);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
        	$result = $query->row_array();

            $uName = $result['username'];
        	$zeit = time();
	        $verify_code = substr(md5($email.'xUUJKK'.$zeit),5,10);
	        $url = site_url('index/forgetMe').'/'.urlencode(base64_encode($email)).'/'.$zeit.'/'.$verify_code;
	        $content = "亲爱的{username}，您好！<br/>
<br/>
您在{datetime}提交了账号密码找回请求，请点击下面的链接修改密码。<br/>
<a href=\"{url}\" target=\"_blank\">{url}</a> <br/>
(如果您无法点击此链接，请将它复制到浏览器地址栏后访问)<br/>
为了保证您帐号的安全，该链接有效期为24小时，并且点击一次后失效！<br/>
<br/>
敬上，<br/>
NPONE团队<br/>
<br/>
http://www.npone.cn<br/>
客服邮箱：xxxx@npone.cn<br/>
";

			$content = str_replace(array('{username}',"{datetime}","{url}"),
			array($uName,date('y年m月d日 H:i:s',$zeit),$url),$content);
        	$this->sendMail($email,$content,"npone.cn账号密码找回");
        }

        $jsonRst = 1;
        $jsonData = array('succ'=>array());
        $jsonData['succ']['msg'] ='您的重置密码邮件已发送，请遵循邮件步骤重置密码';
        // $jsonData['err']['msg'] =$url;

        echo $this->exportData($jsonData,$jsonRst);
	}

	function mailbox(){
		$this->getPage();

		$this->login_verify();
		$this->infoTitle = "我的信箱";
        $this->load->model('lists/mail_list',"listInfo");
        $this->all_counts = $this->listInfo->get_all_count_with_uid($this->userInfo->uid);
        $this->listInfo->load_data_with_uid($this->userInfo->uid,$this->pageNow,5);

        $this->load->library('kuopage');

        $config = array();
		$config['base_url'] = site_url('index/mailbox/');
		$config['total_rows'] = $this->all_counts;
		$config['per_page'] = 5;
		$config['now_page'] = $this->pageNow;
		$config['last_link'] = false;
		$config['query_string_segment'] = 'page';
		$this->kuopage->initialize($config);

		$this->pages = $this->kuopage->create_links();


        $this->template->load('default_lightbox_list', 'index/mailbox');
	}

	function userInfo($uid)
	{
		$this->load->model('records/adminuser_model',"dataInfo");
        $this->dataInfo->init($uid);
		$this->infoTitle = "个人信息：".$this->dataInfo->field_list['username']->gen_show_html();
		$this->template->load('default_lightbox_info', 'index/userInfo');
	}

	function qqLogin(){
		$this->load->library("apiqq");

		$qq = $this->apiqq->getQQConfig();
		$this->apiqq->qq_login($qq['appid'],$qq['scope'],$qq['callback']);
	}



	function doQQLogin(){
		// http://www.callmenow.com/index.php/index/doQQLogin?code=FE6E14A1DBB51C34B13CEF0F90E04EBD&state=29d75610e537b5148e3aee6c192168fe
		$this->load->library("session");
		$this->load->library("apiqq");

		if ($this->session->userdata('logged_in') == TRUE)
        {
			header("Location:".site_url('index/index'));
			return;
        }
		$code = $this->input->get('code');
		if ($this->session->userdata('qq_code') == $code)
        {
			//已经访问过 code 信息了
			$userInfo = array('rst'=>2,
							'openid'=>$this->session->userdata('qq_openid'),
						);
			$info = array('rst'=>2,
			'access_token' => $this->session->userdata('qq_access_token'),
			'expires_in' => $this->session->userdata('qq_expires_in'),
			'refresh_token' => $this->session->userdata('qq_refresh_token')
						);
        } else {
			$rst = $this->apiqq->qq_callback($code);
			if ($rst['rst']!=1){
				if ($rst['rst']==-1 && $rst['error']==100019){
					//code超时了
					$this->session->sess_destroy();
					header("Location:".site_url('index/login'));
					return;
				}
				//出错
				var_dump($rst);
				return;
			}

			$info = $rst['params'];
			$userInfo = $this->apiqq->get_openid($info['access_token']);
			// var_dump($info,$userInfo);
			$data = array(
                   	'qq_openid'  => $userInfo['openid'],
					'qq_code'	=>	$code,
					'qq_access_token' => $info['access_token'],
					'qq_expires_in' => $info['expires_in'],
					'qq_refresh_token' => $info['refresh_token'],
                );

            $this->session->set_userdata($data);
		}

		if ($userInfo['rst']<0){

			//出错
			var_dump($userInfo);
			return;
		}

		$this->load->model('records/adminuser_model',"userModel");
        $login_rst = $this->userModel->verify_third_login('qq',$userInfo['openid']);
		if ($login_rst > 0) {
			//用户存在，直接登录
			$this->realLogin->process_login($this->userModel->field_list['email']->value,$this->userModel->uid,true,true);
			header("Location:".site_url('index/index'));
		} else {
			//取 qq 信息
			if ($this->session->userdata('qq_user')!==false){
				$this->third_user_info = $this->session->userdata('qq_user');
			} else {
				$third_plat_user = $this->apiqq->qq_get_user_info($userInfo['openid'],$info['access_token']);
				$this->third_user_info = $this->apiqq->filter_qq_user_info($third_plat_user);
				$this->session->set_userdata("qq_user",$this->third_user_info);
			}

			if ($third_plat_user['ret']!=0){
				return;
			}
			//走注册逻辑
			$this->third_plat = 'qq';
			$this->third_plat_name = 'QQ';
			$this->third_id = $userInfo['openid'];
			$this->pageClass = 'login';

			$this->template->load('default_before_login', 'index/bindThird');
		}
	}

	function login() {
		$this->is_login = false;
		$this->pageClass = 'login';
		if ($this->login->is_login()){
			$this->login->logout();
		}
		$this->loginname = get_cookie('loginname');
		if (!$this->loginname){
			$this->loginname = '';
		}
		$this->template->load('default_page', 'index/login');
	}
	function reg() {
		$this->is_login = false;
		$this->pageClass = 'login';
		if ($this->realLogin->is_login()){
			$this->realLogin->logout();
		}
		$this->template->load('default_before_login', 'index/reg');
	}


	function doReg(){

		$input_data = array();
		$input_data['loginName'] = $this->input->post('loginName');
		$input_data['pwd'] = $this->input->post('uPassword');
		$input_data['name'] = $this->input->post('uName');
		//这块需要做输入过滤，防XSS等，暂时省略

		$this->load->model('records/user_model',"userModel");

		$ret = $this->userModel->reg_user($input_data);
		if ($ret>0){
			$uid = $this->userModel->uid;
			$this->realLogin->process_login($input_data['email'],$uid,true);
			$data = array();
			$data['goto_url'] = site_url('index/index');
			$data['newId'] = $uid;
			echo $this->exportData($data,1);
		} else {
			$err_codes = array(-1=>array('id'=>'uEmail','msg'=>'用户已存在'),
								-2=>array('id'=>'uPhone','msg'=>'用户已存在'),
								-3=>array('id'=>'uPhone','msg'=>'手机号或邮箱必填一个'),
								-999=>array('id'=>'uPhone','msg'=>'服务器故障，请稍后重试'),
								);
			$err_code = isset($err_codes[$ret])? $err_codes[$ret]:array('id'=>'uEmail','msg'=>'未知错误');
			;

			echo $this->exportData(array('err'=>$err_code),$ret);
		}
	}

	function doLogin(){
		$loginName = $this->input->post('loginName');
		$pwd = $this->input->post('uPassword');
		$rememberMe = $this->input->post('uRememberMe');

		$this->load->model('records/user_model',"userModel");
    $login_rst = $this->userModel->verify_login($loginName,$pwd);
		if ($login_rst > 0) {
			$this->realLogin->process_login($loginName,$this->userModel->uid,$rememberMe,false);
			$data = array();
			$data['goto_url'] = site_url('index/index');
			echo $this->exportData($data,$login_rst);
		} else {
			$err_codes = array(-1=>array('id'=>'loginName','msg'=>'用户不存在'),
								-2=>array('id'=>'uPassword','msg'=>'密码不正确'));
			$err_code = isset($err_codes[$login_rst])? $err_codes[$login_rst]:array('id'=>'loginName','msg'=>'未知错误');
			;

			echo $this->exportData(array('err'=>$err_code),$login_rst);
		}
	}


	function doChangePwd(){
		$this->login_verify();
		$pwd = $this->input->post('uPassword');
		$pwdNew = $this->input->post('uPasswordNew');
		$login_rst = $this->userInfo->changePwd($pwd,$pwdNew);
		if ($login_rst > 0) {
			$data = array();
			$data['succMsg'] = '修改成功!';
			echo $this->exportData($data,$login_rst);
		} else {
			$err_codes = array(-1=>array('id'=>'uPassword','msg'=>'密码不正确'),
								-2=>array('id'=>'uPasswordNew','msg'=>'密码不正确'));
			$err_code = isset($err_codes[$login_rst])? $err_codes[$login_rst]:array('id'=>'uEmail','msg'=>'未知错误');
			;

			echo $this->exportData(array('err'=>$err_code),$login_rst);
		}
	}
	function doLogout(){

		$this->realLogin->logout();
		$this->load->library("session");
		$this->session->sess_destroy();
		header("Location:".site_url('index/login'));
	}
}
