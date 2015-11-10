<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Project extends common {
	function __construct() {
		parent::__construct(true,'a');
        $this->load->library('pagination');
	}

	function index(){
		$this->typ = 'project';
		$this->dataModelPrefix = 's';
		$this->dataModelName = 'Project';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('name','desc');
		// $this->need_plus = 'abiaozhun/baoyang_plus';

		$this->common_list();
	}

	function workingweek(){
		$this->typ = 'workingweek';
		$this->dataModelPrefix = 's';
		$this->dataModelName = 'Workingweek';
		$this->searchKeys = array('name');
		$this->quickSearchKeys = array('name');
		$this->listKeys = array('name','isCurrent','orderId','packed','beginTS','endTS');
		$this->orderKey = array('orderId'=>'desc');
		// $this->need_plus = 'abiaozhun/baoyang_plus';

		$this->common_list();
	}

	function doChangeProject($projectId){
		$data=array();
		$data['projectId']=$projectId;

		$this->userInfo->update_db($data);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}



}
