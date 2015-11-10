<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Email extends common {
	function __construct() {
		parent::__construct(true,'a');
        $this->load->library('pagination');
	}

	function index(){
		$this->admin_load_menus();
		$this->load->model('lists/Email_list',"dataInfo");

		$this->dataInfo->add_where(WHERE_TYPE_WHERE,'recUser',$this->userInfo->id);
		$this->dataInfo->orderKey=array('status'=>'asc','createTS'=>'desc');

		$this->dataInfo->load_data_with_where();

		$this->dataInfo->is_lightbox = true;

		// $this->create_link =  $this->controller_name . "/create/email/";

		$this->template->load('default_lightbox_info', 'common/mini_list_view');
	}

	function getEmailCount(){
		$this->admin_load_menus();
		$this->load->model('lists/Email_list',"dataInfo");

		$this->dataInfo->add_where(WHERE_TYPE_WHERE,'recUser',$this->userInfo->id);
		$this->dataInfo->add_where(WHERE_TYPE_WHERE,'status',0);

		$this->dataInfo->load_data_with_where();

		$this->num=count($this->dataInfo->record_list);

		$jsonData = $jsonDataPlus;

        $jsonData['emailcount'] = $this->num;
        echo $this->exportData($jsonData,1);
	}

	function doSthBeforeShowInfoPage($typ,$id){
		$this->load->model('records/Email_model',"dataInfo");

		$this->dataInfo->init_with_id($id);

		$data=array();
		$data['status']=1;

		$this->dataInfo->update_db($data);

		// $jsonData = array();
        // $this->exportToRefer(1,$jsonData);
	}

}
