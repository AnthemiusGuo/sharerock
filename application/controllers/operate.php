<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Operate extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
	}

	function index($state=1){
		$this->canCreate=false;
        $this->admin_load_menus();
        $this->load->model('lists/Crate_list',"listInfo");
		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);

		$this->need_plus='operate/state';
		$this->state=$state;
		if($this->state==1){
			$this->listInfo->add_where(WHERE_TYPE_IN,'state',array(0,3));
		}else if($this->state==2){
			$this->listInfo->add_where(WHERE_TYPE_IN,'state',array(1,2));
		}
		$this->listInfo->load_data_with_where();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/crate/";

        $this->template->load('default_page', 'common/list_view');
    }

	function reqCrate(){

		$this->show_controller_name = 'index';
		$this->admin_load_menus();
		$this->load->model('lists/Crate_list',"listInfo");

		$this->listInfo->add_where(WHERE_TYPE_WHERE,"createUid",$this->userInfo->id);
		$this->listInfo->load_data_with_where();

		$this->listInfo->is_lightbox = true;

		$this->create_link =  $this->controller_name . "/create/crate/";

		$this->template->load('default_page', 'common/list_view');
	}


	function doCancel($crateId){
		$title = $this->userInfo->field_list['name']->value.'-已经取消开机箱';
		$msg = '取消时间：'.time();
		$url = 'operate/index';
		$this->load->model("lists/User_list","examineList");
		$this->examineList->add_where(WHERE_TYPE_WHERE,'isManager',3);
		$this->examineList->load_data_with_where();
		$examines=array();
		foreach($this->examineList->record_list as $key=>$value){
			$examines[]=$value->id;
		}
		$this->sendRtxNotify($examines,$title,$msg,$url);

		$this->load->model("records/Crate_model","crateInfo");
		$this->crateInfo->init_with_id($crateId);

		$data=array();
		$data['state']=1;
		$data['endTS']="";
		$this->crateInfo->update_db($data);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}
	function doExamine($crateId){
		$this->load->model("records/Crate_model","crateInfo");
		$this->crateInfo->init_with_id($crateId);

		$data=array();
		$data['state']=3;
		$data['examineUser']=$this->userInfo->id;
		$this->crateInfo->update_db($data);

		$title = "有新的开机箱需要执行";
		$msg = $this->crateInfo->field_list['createUid']->gen_show_html().":".$this->crateInfo->field_list['name']->value;
		$url = 'operate/index';
		$this->load->model("lists/User_list","dealList");
		$this->dealList->add_where(WHERE_TYPE_WHERE,'typ',6);
		$this->dealList->load_data_with_where();
		$deals=array();
		foreach($this->dealList->record_list as $key=>$value){
			$deals[]=$value->id;
		}
		$this->sendRtxNotify($deals,$title,$msg,$url);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}
	function doConfirm($crateId){
		$this->load->model("records/Crate_model","crateInfo");
		$this->crateInfo->init_with_id($crateId);

		$data=array();
		$data['state']=2;
		$data['dealUser']=$this->userInfo->id;
		$data['endTS']=time();
		$this->crateInfo->update_db($data);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);

	}

	function doSthBeforeInsert($typ,$data){
		if($typ=="crate"){
			$data['crateId']=$this->userInfo->field_list['crateId']->value;
			$data['state']=0;
		}
		return $data;
	}

	function doSthAfterInsert($typ,$data,$newId){
		if($typ=="crate"){
			$title = '有新的开机箱申请等待审批';
			$msg = $data['name'];
			$url = 'operate/index';
			$this->load->model("lists/User_list","examineList");
			$this->examineList->add_where(WHERE_TYPE_WHERE,'isManager',3);
			$this->examineList->load_data_with_where();
			$examines=array();
			foreach($this->examineList->record_list as $key=>$value){
				$examines[]=$value->id;
			}
			$this->sendRtxNotify($examines,$title,$msg,$url);
		}
	}

}
