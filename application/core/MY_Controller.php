<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (! defined('__DEFINED_WHERE_TYPE__')) {
    define("__DEFINED_WHERE_TYPE__", "__DEFINED_WHERE_TYPE__");
    define("WHERE_TYPE_WHERE", 0);
    define("WHERE_TYPE_IN", 1);
    define("WHERE_TYPE_LIKE", 2);
    define("WHERE_TYPE_NOT_IN", 3);
    define("WHERE_TYPE_OR_WHERE", 10);
    define("WHERE_TYPE_OR_IN", 11);
    define("WHERE_TYPE_OR_LIKE", 12);
    define("WHERE_TYPE_WHERE_GT", 21);
    define("WHERE_TYPE_WHERE_LT", 22);
    define("WHERE_TYPE_WHERE_GTE", 23);
    define("WHERE_TYPE_WHERE_LTE", 24);
    define("WHERE_TYPE_WHERE_NE", 25);




    define("WHERE_TXT", 99);
}
define("VIEW_TYPE_PAGE", 1);
define("VIEW_TYPE_HTML", 2);
define("VIEW_TYPE_JSON", 3);

class P_Controller extends CI_Controller {
	public $uid = null;
	public $userInfo;
	public $orgList = array();
	public $orgId = 0;
	public $orgName = '';
	public $viewType;
	public $pageClass = 'normal';
	public $menus = array();
    public $need_plus = '';
    public $isVip = false;
    public $hasSearch = true;
    public $backInt = 0;
    public $res_ver = '2.0.3.1014';
    public $has_menu = true;

	function __construct($login_verify = true,$userTyp='a') {

		parent::__construct();
		date_default_timezone_set("Asia/Shanghai");
        P_Controller::Register();

		$this->is_login = false;
        // $this->db = $this->cimongo;
        if (DB_TYPE=="MONGO"){
            $this->db = $this->cimongo;
        } else {

        }
        $this->force_lightbox = false;
		$this->load->helper('url');
        $this->controller_name = ($this->uri->segment(1)===false||$this->uri->segment(1)=="")?'index':$this->uri->segment(1);
        $this->system_name = substr($this->controller_name,0,1);
        $this->method_name = ($this->uri->segment(2)===false||$this->uri->segment(2)=="")?'index':$this->uri->segment(2);
        if ($this->method_name =='info'){
            $this->method_name = 'index';
        }
        $this->searchInfo = array('t'=>'no');

        $this->userTyp = $userTyp;
        if ($userTyp=='u'){
            $this->realLogin = $this->adminlogin;
        } else {
            $this->realLogin = $this->login;
        }

        if($login_verify) {
			$this->login_verify(true);
            $this->canEdit = $this->checkEditRule();
		} else {
            $this->login_verify(false);
		}
        $this->title = array($this->config->item('base_title'));
        $this->perPage = 10;
        $this->cur_page = 1;
	}

	public function admin_load_menus(){
        $this->load->library('menu');

		$this->menus = $this->menu->load_menu($this->userInfo->field_list['typ']->value);

        array_unshift($this->title,$this->menus[$this->controller_name]['menu_array'][$this->method_name]['name']);
	}


	function setViewType($viewType){
		$this->viewType =$viewType;
	}
	function checkEditRule(){
		if ($this->controller_name=="crm"){
			return $this->checkActionRule("Crm","Edit");
		} else {
			return $this->checkActionRule("Project","Edit");
		}

	}
	function checkRule($module,$action){
		if ($this->accessRule[$module]!=null){
			if (!in_array($action, $this->accessRule[$module])){
				$this->display_error("no_access");
			}
		} else {
			$this->display_error("no_access");
		}
	}

	function checkActionRule($module,$action){
        return true;
		if ($this->accessRule[$module]!=null){
			if (!in_array($action, $this->accessRule[$module])){
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	function display_error($error_typ,$error_msg=""){
		$msg_array = array("no_access"=>"您没有权限使用该功能","common"=>"出错啦！");
		if ($error_msg==""){
			$this->error_msg = isset($msg_array[$error_typ])?$msg_array[$error_typ]:$msg_array['common'];
		} else {
			$this->error_msg = $error_msg;
		}
		if ($error_typ=="json" || $this->viewType == VIEW_TYPE_JSON){
			$jsonRst = -1000;
            $jsonData = array();
            $jsonData['err']['msg'] =$this->error_msg;
            echo $this->exportData($jsonData,$jsonRst);
            exit;
		} elseif ($this->viewType == VIEW_TYPE_PAGE) {
			if (!file_exists(APPPATH."views/error/".$error_typ.".php")) {
				$error_typ = "common";
			}

			ob_start();
			$buffer = $this->template->load('default_npo', 'error/'.$error_typ, array(),true);
			ob_end_clean();
			echo $buffer;
		} elseif ($this->viewType == VIEW_TYPE_HTML){
			if (!file_exists(APPPATH."views/error/".$error_typ.".php")) {
				$error_typ = "common";
			}

			ob_start();
			$buffer = $this->template->load('default_lightbox_info', 'error/'.$error_typ, array(),true);
			ob_end_clean();
			echo $buffer;
		}

		exit;
	}


	function buildSearch(){
        $searchInfo = $this->input->get('s');
		if ($searchInfo==="" || $searchInfo===false) {
			$this->quickSearchValue = "";
			return;
		}
		$this->searchInfo = (json_decode(base64_decode(urldecode($searchInfo)),true));
		if ($this->searchInfo['t']=="quick"){
			$this->quickSearchValue = $this->searchInfo['i'];
		}
	}

    function load_org_info($force_check = false) {
        if (!$this->is_login) {
            return;
        }
        if ($this->userInfo->field_list['orgId']->isEmpty() || $this->userInfo->field_list['orgId']->value_checked<=0) {
            if ($force_check){
                header("Location:".site_url('aindex/index'));
            }
            return;
        }

        $this->load->model('records/org_model',"myOrgInfo");

        $this->myOrgInfo->init_with_id($this->userInfo->field_list['orgId']->value);
    }

	function login_verify($force=true) {
        if ($this->userTyp=="m"){
            $this->load->model('records/user_model',"userInfo");
        } else {
            $this->load->model('records/adminuser_model',"userInfo");
        }
		if ($this->realLogin->is_login() !== true) {
            if ($this->userTyp=="m"){
                $this->load->library("session");
                $third_plat = $this->session->userdata('third_plat');
                $third_id = $this->session->userdata('third_id');
                //检查微信登录
                if ($third_plat!==false && $third_id!==false){
                    //有微信，检查是否已经绑定
                    $login_rst = $this->userInfo->verify_third_login($third_plat,$third_id);

                	if ($this->userInfo->is_inited){
                		$this->realLogin->process_login($this->userInfo->field_list['phone']->value,$this->userInfo->uid,true,true);
                    }
                }
            }
        }
        if ($this->realLogin->is_login() !== true) {
            if ($force){
                $this->goto_login();
            } else {
                return;
            }
        }

		$this->uid = $this->realLogin->uid;
        if (!MongoId::isValid($this->uid)){
            if ($force){
                $this->goto_login();
            } else {
                return;
            }
        }



		$init_result = $this->userInfo->init_by_uid($this->uid);

		if ($init_result<0){
            if ($force==true){
                $this->realLogin->logout();
                $this->goto_login();
            }
            $this->uid = null;
		} else {
            $this->is_login = true;
            if (isset($this->userInfo->field_list['orgId'])){
                $this->myOrgId = $this->userInfo->field_list['orgId']->value;

            }
		};
        if ($this->userTyp=="u"){
            $this->adminTyp = $this->userInfo->field_list['typ']->value;
        }
	}

    function goto_login(){
        if ($this->userTyp=="m"){
            $this->load->library("session");
            $third_plat = $this->session->userdata('third_plat');
            $third_id = $this->session->userdata('third_id');
            //检查微信登录
            //如果有微信登录记录，直接跳绑定
		//var_dump($third_plat,$third_id);exit;
            if ($third_plat===false || $third_id===false){
                //否则，跳 api
                header("Location:".site_url('api/wxjump/index'));
                exit;
            } else {
                //有微信，检查是否已经绑定
                $login_rst = $this->userInfo->verify_third_login($third_plat,$third_id);

            	if ($this->userInfo->is_inited){
            		$this->realLogin->process_login($this->userInfo->field_list['phone']->value,$this->userInfo->uid,true,true);
                } else {
                    header("Location:".site_url('mindex/bind'));
                    exit;
                }
            }
        } else {
            header("Location:".site_url($this->userTyp.'index/login'));
            exit;
        }
    }

	function login_init() {


	}

	public function genBreadCrumb(){
		return "<ul class='breadcrumb'>
		<li><a href='#'><span class='glyphicon glyphicon-home'></span> Home</a></li>
		<li><a href='#'><span class='glyphicon {$this->Menus->show_menus[$this->controller_name]['icon']}'></span> {$this->Menus->show_menus[$this->controller_name]['name']}</a></li>
		<li class='active'><span class='glyphicon glyphicon-circle-arrow-right'></span> {$this->Menus->show_menus[$this->controller_name]['menu_array'][$this->method_name]['name']}</a></li>
		</ul>";
	}

	function build_request($question_mark = false) {
		$get = $this->input->get();
		if(!$get) {
			return '';
		}
		if($question_mark) {
			return '?'.http_build_query($get);
		}
		return http_build_query($get);
	}


	public function resultEncode($ret)
    {
        return json_encode($ret);
    }

    public function resultDecode($enret)
    {
        return json_decode($enret , true);
    }



    public function exportData($data , $num = 0)
    {
        $ret = array(
            'data' => $data,
            'rstno' => $num,
        );
        return  $this->resultEncode($ret);
    }

    public function checkMenus(){
    	if (empty($this->orgList)){
    		if ($this->userInfo->field_list['isAdmin']->value==1){
				$this->Menus->limit_access("index,admin");

    		} else {
				$this->Menus->limit_access("index");

    		}
		} else {
			$this->Menus->limit_access_by_rule($this->accessRule);
			if ($this->userInfo->field_list['isAdmin']->value==1){
				$key = 'admin';
				$this->Menus->show_menus[$key] = $this->Menus->all_menus[$key];
    		}

		}
    }

    public function sendMail($email,$content,$title){
    	$this->load->library('email');

    	$config['protocol'] = 'smtp';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = false;
		$config['smtp_host'] = '172.18.238.10';
		$config['smtp_user'] = 'webmaster@huopuyun.com';
		$config['smtp_pass'] = 'Abc123';
		$config['smtp_port'] = '25';
		$config['mailtype'] = 'html';

  //   	$config['protocol'] = 'smtp';
		// $config['charset'] = 'utf-8';
		// $config['wordwrap'] = false;
		// $config['smtp_host'] = 'smtp.126.com';
		// $config['smtp_user'] = 'nponechina';
		// $config['smtp_pass'] = 'npone123';
		// $config['smtp_port'] = '25';
		// $config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from('nponechina@126.com', 'NPOne平台');
		$this->email->to($email);

		$this->email->subject($title);
		$this->email->message($content);

		$this->email->send();
    }

    public function getPage(){
    	$this->pageNow = $this->input->get('page');
    	if ($this->pageNow===false){
    		$this->pageNow = 0;
    	} else {
    		$this->pageNow = (int)$this->pageNow -1;
    	}
    	if ((int)$this->pageNow<=0){
    		$this->pageNow = 0;
    	}

    }

    public function getSubTab($default){
    	$this->tabNow = $this->input->get('tab');
    	if ($this->tabNow===false){
    		$this->tabNow = $default;
    	}
    }

	public function getSearch(){
    	$this->searchs = $this->input->get('search');
    }
    public static function Register() {
        if (function_exists('__autoload')) {
            //    Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__autoload');
        }
        //    Register ourselves with SPL
        return spl_autoload_register(array('P_Controller', 'LoadRecords'));
    }   //    function Register()


    /**
     * Autoload a class identified by name
     *
     * @param    string    $pClassName        Name of the object to load
     */
    public static function LoadRecords($pClassName){

        if ((class_exists($pClassName,FALSE)) || (strpos($pClassName, '_model')===false)) {
            //    Either already loaded, or not a model class request
            return FALSE;
        }

        $pClassFilePath = APPPATH .'models/records/'.strtolower($pClassName) .'.php';
        if ((file_exists($pClassFilePath) === FALSE) || (is_readable($pClassFilePath) === FALSE)) {
            //    Can't load
            return FALSE;
        }
        require($pClassFilePath);
    }   //    function Load()
    //

    public function exportToRefer($jsonRst,$jsonDataPlus=array()){
        $this->load->library('user_agent');
        $this->refer = $this->agent->referrer();
        $jsonData = $jsonDataPlus;

        $jsonData['goto_url'] = $this->refer;
        echo $this->exportData($jsonData,$jsonRst);
    }

    public function log($typ,$info){
        log_message('error', $typ.':'.json_encode($info));
    }

}
