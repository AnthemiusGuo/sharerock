<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends P_Controller {
	function __construct() {
		parent::__construct(false,'a');
		$this->load->library('pagination');
	}

	function index() {
		$this->login_verify();
		$this->admin_load_menus();
		$this->task_create_link = 'task/create/task/';

		$this->load->model('lists/Task_list',"pushTaskList");
		$this->pushTaskList->add_where(WHERE_TYPE_WHERE,"createUid",$this->userInfo->id);
		$this->pushTaskList->limit = 10;
		$this->pushTaskList->keyList = array('name','dueUser','progress','status','dueEndTS');
		$this->pushTaskList->canEdit = true;
		$this->pushTaskList->load_data_with_where();

		$this->load->model('lists/Task_list',"dueTaskList");
		$this->dueTaskList->add_where(WHERE_TYPE_WHERE,"dueUser",$this->userInfo->id);
		$this->dueTaskList->limit = 10;
		$this->dueTaskList->canEdit = true;
		$this->dueTaskList->op_limit = "due";

		$this->dueTaskList->keyList = array('name','createUid','progress','status','dueEndTS');

		$this->dueTaskList->load_data_with_where();

		$this->template->load('default_page', 'index/dashboard');
	}

	function sendRTX(){
		$this->setViewType(VIEW_TYPE_HTML);
		$modelName = 'records/Rtx_model';

        $this->load->model($modelName,"dataInfo");
        $this->title_create = $this->dataInfo->title_create;

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doSendRtx';


        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();

        $this->editor_typ = 0;
        $this->template->load('default_lightbox_new', 'common/create_related');
	}

	function doSendRtx(){
		$recivers = $this->input->post('name');
		$title = $this->input->post('title');
		$msg = $this->input->post('msg');
		$delay = (int)$this->input->post('delay');
		$url = $this->input->post('url');

		$this->load->model('lists/User_list',"userlist");
		$this->load->model('records/User_model',"recInfo");
		$this->load->model('records/Email_model',"emailInfo");
		if($recivers=="all"){
			// var_dump("all");
			foreach ($this->userlist->field_list['_id']->value as $key => $value) {

	            $data=array();
	            $data['name']=$title;
	            $data['createUid']=$this->userInfo->id;
	            $data['recUser']=(String)$value;
	            $data['content']=$msg;
	            $data['status']=0;
	            $data['createTS']=time();
	            $this->emailInfo->insert_db($data);

	        }
		}else if(strstr($recivers,',')){
			// var_dump("array");
			$realRecivers=explode(',',$recivers);
			foreach($realRecivers as $key => $each){
				$this->recInfo->init_with_where(array('name'=>$each));
				$data=array();
				$data['name']=$title;
				$data['createUid']=$this->userInfo->id;
				$data['recUser']=(String)$this->recInfo->field_list['_id']->value;
				$data['content']=$msg;
				$data['status']=0;
				$data['createTS']=time();
				$this->emailInfo->insert_db($data);
			}
		}else if(!is_array($recivers) && $recivers!=""){
			// var_dump("one");
			$this->recInfo->init_with_where(array('name'=>$recivers));
			$data=array();
			$data['name']=$title;
			$data['createUid']=$this->userInfo->id;
			$data['recUser']=(String)$this->recInfo->field_list['_id']->value;
			$data['content']=$msg;
			$data['status']=0;
			$data['createTS']=time();
			$this->emailInfo->insert_db($data);
		}

		$this->load->library('Rtx');
		$this->rtx->sendNotify($recivers,$title,$msg,$url,$delay);
		echo $this->exportData(array('succ'=>array('msg'=>'发送成功！')),1);
	}

	function calList(){
		$start = (int)$this->input->get('start');
		$end = (int)$this->input->get('end');

		$cldMyTask = ($this->input->get('cldMyTask')=="true");
		$cldMyPushTask = ($this->input->get('cldMyPushTask')=="true");
		$cldMyDev =  ($this->input->get('cldMyDev')=="true");
		$cldMyInterview =  ($this->input->get('cldMyInterview')=="true");

		// var_dump($this->input->get('cldMyTask'),$cldMyTask,$cldMyPushTask,$cldMyDev,$cldMyInterview);exit;
		$this->load->model('lists/Common_list',"storyList");
        $this->storyList->setInfo('pStory','Story_list','Story_model');
		$this->storyList->add_where(WHERE_TYPE_WHERE_LT,'dueEndTS',$end);
		$this->storyList->add_where(WHERE_TYPE_WHERE_GT,'dueEndTS',$start);


		$this->storyList->add_where(WHERE_TYPE_WHERE,'dueUser',$this->userInfo->id);

		$this->storyList->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

        $this->storyList->load_data_with_where();

		$this->load->model('lists/Common_list',"aiList");
        $this->aiList->setInfo('pActionitem','Actionitem_list','Actionitem_model');
		$this->aiList->add_where(WHERE_TYPE_WHERE,'dueUser',$this->userInfo->id);

		$this->aiList->add_where(WHERE_TYPE_WHERE_LT,'dueEndTS',$end);
		$this->aiList->add_where(WHERE_TYPE_WHERE_GT,'dueEndTS',$start);

		$this->aiList->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

        $this->aiList->load_data_with_where();

		$this->load->model('lists/Resume_list',"resumeList");
		$this->resumeList->add_where(WHERE_TYPE_OR_WHERE,'firstInterviewer',$this->userInfo->id);
		$this->resumeList->add_where(WHERE_TYPE_OR_WHERE,'secondInterviewer',$this->userInfo->id);
		$this->resumeList->add_where(WHERE_TYPE_OR_WHERE,'hr',$this->userInfo->id);

        $this->resumeList->load_data_with_where();

		$this->load->model('lists/Needs_list',"needsList");
		$this->needsList->add_where(WHERE_TYPE_OR_WHERE,'dueUser',$this->userInfo->id);
		$this->needsList->add_where(WHERE_TYPE_OR_WHERE,'createUid',$this->userInfo->id);

		$this->needsList->load_data_with_where();

		$this->load->model('lists/Task_list',"taskList");
		if ($cldMyPushTask){
			$this->taskList->add_where(WHERE_TYPE_OR_WHERE,'createUid',$this->userInfo->id);
		}

		if ($cldMyTask){
			$this->taskList->add_where(WHERE_TYPE_OR_WHERE,'dueUser',$this->userInfo->id);
		}


        $this->taskList->load_data_with_where();

		$events = array();
        $i = 0;
		if ($cldMyDev){
			// $this->field_list['status']->setEnum(array('未启动','等待前置','开发中','测试中','已结'));
			$colors = array('#777','#d9534f','#337ab7','#5bc0de','#5cb85c','#777');
	        foreach($this->storyList->record_list as  $this_record) {
	            $events[] = array(
	                        "id"=>$this_record->id,
							"typ"=>'story',
	                        "title"=>
								'[故事]'.$this_record->field_list['system']->gen_show_value().'开发'.$this_record->field_list['name']->value.
								" @ ".$this_record->field_list['featureId']->gen_show_value(),

	                        "start"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
	                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
	                        "allDay"=>true,
	                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
	                //         start: new Date(y, m, d - 5),
	                //         end: new Date(y, m, d - 2),
	                //         backgroundColor: layoutColorCodes['green']
	                //     }
	                );
	            $i++;
	        }


			// $this->field_list['status']->setEnum(array(0=>'未设置',1=>'未启动',2=>'准备',3=>'进行中',4=>'完工'));

			$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

			foreach($this->aiList->record_list as  $this_record) {
	            $events[] = array(
	                        "id"=>$this_record->id,
							"typ"=>'actionitem',

	                        "title"=>'[事项]'.$this_record->field_list['name']->value." @ ".$this_record->field_list['featureId']->gen_show_value(),

	                        "start"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
	                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
	                        "allDay"=>true,
	                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
	                //         start: new Date(y, m, d - 5),
	                //         end: new Date(y, m, d - 2),
	                //         backgroundColor: layoutColorCodes['green']
	                //     }
	                );
	            $i++;
	        }
			$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

			foreach($this->needsList->record_list as  $this_record) {
				$showTime=$this_record->field_list['dueEndTS']->value;
				$events[] = array(
	                        "id"=>$this_record->id,
							"typ"=>'needs',
	                        "title"=>
								'[美术需求]名称:'.$this_record->field_list['name']->value.'/发起者:'.$this_record->field_list['createUid']->gen_show_value().'/负责人:'.$this_record->field_list['dueUser']->gen_show_value(),

	                        "start"=>$showTime,
	                        "end"=>$showTime,
	                        "allDay"=>true,
	                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
	                //         start: new Date(y, m, d - 5),
	                //         end: new Date(y, m, d - 2),
	                //         backgroundColor: layoutColorCodes['green']
	                //     }
	                );
	            $i++;
	        }

		}

		if ($cldMyPushTask || $cldMyTask){
			$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

			foreach($this->taskList->record_list as  $this_record) {
				if ($this_record->field_list['createUid']->value==$this->userInfo->id){
					$title = '[发的工作]'.$this_record->field_list['name']->value ." to ".$this_record->field_list['dueUser']->gen_show_value();
				} else {
					$title = '[工作]'.$this_record->field_list['name']->value ." by ".$this_record->field_list['createUid']->gen_show_value();
				}
	            $events[] = array(
	                        "id"=>$this_record->id,
							"typ"=>'task',

	                        "title"=>$title,

	                        "start"=>($this_record->field_list['beginTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['beginTS']->value,
	                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
	                        "allDay"=>true,
	                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
	                //         start: new Date(y, m, d - 5),
	                //         end: new Date(y, m, d - 2),
	                //         backgroundColor: layoutColorCodes['green']
	                //     }
	                );
	            $i++;
	        }
		}

		if ($cldMyInterview){
			$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

			foreach($this->resumeList->record_list as  $this_record) {
				if($this_record->field_list['firstInterviewer']->value==$this->userInfo->id){
					$time='/时间:'.$this_record->field_list['firstReview']->gen_show_hour();
					$showTime=$this_record->field_list['firstReview']->value;
				}else if($this_record->field_list['secondInterviewer']->value==$this->userInfo->id){
					$time='/时间:'.$this_record->field_list['secondReview']->gen_show_hour();
					$showTime=$this_record->field_list['secondReview']->value;
				}else if($this_record->field_list['hr']->value==$this->userInfo->id){
					$time='/时间:'.$this_record->field_list['hrReview']->gen_show_hour();
					$showTime=$this_record->field_list['hrReview']->value;
				}
				$events[] = array(
	                        "id"=>$this_record->id,
							"typ"=>'resume',
	                        "title"=>
								'[面试预约]姓名:'.$this_record->field_list['candidate']->value.'/岗位:'.$this_record->field_list['name']->gen_show_value().$time,

	                        "start"=>$showTime,
	                        "end"=>$showTime,
	                        "allDay"=>true,
	                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
	                //         start: new Date(y, m, d - 5),
	                //         end: new Date(y, m, d - 2),
	                //         backgroundColor: layoutColorCodes['green']
	                //     }
	                );
	            $i++;
	        }
		}

        echo json_encode($events);
	}

	function managerweek(){

	}



	function doTest(){
			$content = "亲爱的{username}，您好！<br/>
<br/>
您在{datetime}提交了账号密码找回请求，请点击下面的链接修改密码。<br/>";


			$this->sendMail("1964398291@qq.com",$content,"感谢您注册npone.cn");
	}
	function doTest2(){
		$headers = 'From: zhujun@the9.com' . "\r\n" .
    'Reply-To: zhujun@the9.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    	$ret = mail("1964398291@qq.com", "邮箱认证" ,  "sssssssss",$headers);
    	var_dump($ret);
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
		if ($this->realLogin->is_login()){
			$this->realLogin->logout();
		}
		$this->loginname = get_cookie('loginname');
		if (!$this->loginname){
			$this->loginname = '';
		}
		$this->template->load('default_before_login', 'index/login');
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
