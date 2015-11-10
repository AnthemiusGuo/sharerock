<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Admin extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
	}

	function index(){

        $this->admin_load_menus();
        $this->load->model('lists/User_list',"listInfo");
		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);
		$this->listInfo->paged = true;
        $this->listInfo->perPage = 20;
		$this->listInfo->limit = $this->listInfo->perPage;
        $this->listInfo->nowPage = $this->pagination->get_tough_page();
		$this->listInfo->add_where(WHERE_TYPE_WHERE_NE,'packed',1);
		$this->listInfo->load_data_with_where();

		$this->perPage = 20;
        $config['per_page'] = $this->perPage;
        $config['base_url'] = site_url($this->controller_name.'/'.$this->method_name.'/').'/';

        $this->pagination->initialize($config);
        $this->cur_page = $this->pagination->get_cur_page();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/user/";

        $this->template->load('default_page', 'common/list_view');
    }
    function department(){

        $this->admin_load_menus();
        $this->load->model('lists/Department_list',"listInfo");
		$this->listInfo->paged = true;
		$this->listInfo->perPage = 20;
		$this->listInfo->limit = $this->listInfo->perPage;
		$this->listInfo->nowPage = $this->pagination->get_tough_page();
		$this->listInfo->load_data_with_where();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/department/";

        $this->template->load('default_page', 'common/list_view');
    }
	function departMembers($departId='null'){
		if($this->userInfo->field_list['departId']->value==$departId ){
			$this->canEdit=true;
			$this->create_link =  $this->controller_name . "/create/user/".$departId;
		}else{
			$this->canEdit=false;
		}
		if($this->userInfo->field_list['isManager']->value==0){
			$this->canEdit=false;
		}else if($this->userInfo->field_list['isManager']->value==3){
			$this->canEdit=true;
			$this->create_link =  $this->controller_name . "/create/user/";
		}
		$this->admin_load_menus();
		$this->departId=$departId;
		// $this->typ = 'departMembers';

		$this->load->model('lists/User_list',"listInfo");
		// $this->dataModelName = 'uUser';
		// $this->searchKeys = array('name');
		// $this->quickSearchKeys = array('name');
		// $this->listKeys = array('name','desc','status','dueUser','endTS','realEndTS','packed');
		if($departId=="null"){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'departId',"0");
		}
		else if ($departId!=""){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'departId',$departId);
		}
		$this->listInfo->paged = true;
        $this->listInfo->perPage = 20;
		$this->listInfo->limit = $this->listInfo->perPage;
        $this->listInfo->nowPage = $this->pagination->get_tough_page();
		$this->listInfo->add_where(WHERE_TYPE_WHERE_NE,'packed',1);
		$this->listInfo->load_data_with_where();

		$this->load->model('lists/Department_list',"departList");
		$this->departList->load_data();

		$this->need_plus = 'admin/depart_list';

		// $this->common_list();
		$this->template->load('default_page', 'common/list_view');
}

	function editMyself(){
		$this->setViewType(VIEW_TYPE_HTML);

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doUpdate/user';

        $this->dataInfo=$this->userInfo;

		$this->dataInfo->changeShowFields= array(
                array('name','loginName'),
                array('pwd'),
                array('crateId'),
                array('intro'),
            );

        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();

        $this->editor_typ = 1;
        $this->title_create = $this->dataInfo->gen_editor_title();
		$this->doSthBeforeShowUpdatePage($typ,$id);
        $this->template->load('default_lightbox_edit', 'common/create_related');
	}

	function deleteUser($id){
		$this->load->model('records/User_model',"oneInfo");
		$this->oneInfo->init_with_id($id);
		// var_dump($this->oneInfo);exit;
		$data=array();
		$data['packed']=1;
		$this->oneInfo->update_db($data);
		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}

	function create($typ,$id=""){
		if ($typ=="user" && $id!=""){
			$this->related_field = 'departId';
		}
		parent::create($typ,$id);
	}

}
