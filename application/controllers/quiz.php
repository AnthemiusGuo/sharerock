<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Quiz extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
	}

	function position(){

        $this->admin_load_menus();
        $this->load->model('lists/Position_list',"listInfo");

		$this->listInfo->paged = true;
        $this->listInfo->perPage = 20;
		$this->listInfo->limit = $this->listInfo->perPage;
        $this->listInfo->nowPage = $this->pagination->get_tough_page();
        if($this->userInfo->field_list['isManager']->value!=3&&$this->userInfo->field_list['typ']->value!=9){
            $this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'createUid',$this->userInfo->id);
        }
		$this->listInfo->load_data_with_where();

		$this->perPage = 20;
        $config['per_page'] = $this->perPage;
        $config['base_url'] = site_url($this->controller_name.'/'.$this->method_name.'/').'/';

        $this->pagination->initialize($config);
        $this->cur_page = $this->pagination->get_cur_page();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/position/";

        $this->template->load('default_page', 'common/list_view');
    }
    function resume(){

        $this->admin_load_menus();
        $this->load->model('lists/Resume_list',"listInfo");

        $this->listInfo->paged = true;
        $this->listInfo->perPage = 20;
        $this->listInfo->limit = $this->listInfo->perPage;
        $this->listInfo->nowPage = $this->pagination->get_tough_page();

        if($this->userInfo->field_list['isManager']->value!=3&&$this->userInfo->field_list['typ']->value!=9){
            $this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'firstInterviewer',$this->userInfo->id);
            $this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'secondInterviewer',$this->userInfo->id);
			$this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'departId',$this->userInfo->field_list['departId']->value);
        }
        $this->listInfo->load_data_with_where();

        $this->perPage = 20;
        $config['per_page'] = $this->perPage;
        $config['base_url'] = site_url($this->controller_name.'/'.$this->method_name.'/').'/';

        $this->pagination->initialize($config);
        $this->cur_page = $this->pagination->get_cur_page();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/resume/";

        $this->template->load('default_page', 'common/list_view');
    }

	function doSthBeforeInsert($typ,$data){
		if($typ=="position"){
			$uid = $data['createUid'];
			$this->load->model('records/user_model',"userModel");
			$this->userModel->init_with_id($uid);
			$data['departId'] = $this->userModel->field_list['departId']->value;
			return $data;
		}
		else if($typ=="resume"){
			$position = $data['name'];
			$this->load->model('records/position_model',"positionModel");
			$this->positionModel->init_with_id(new MongoId($position));
			$data['departId'] = $this->positionModel->field_list['departId']->value;
			return $data;
		}
	}

	function doSthAfterInsert($typ,$data,$newId){
		if($typ=="resume"){
			$this->dataInfo->init_with_data($newId,$data);
			$title = '您有新的面试预约';
			$msg = $this->dataInfo->field_list['name']->gen_show_value().$data['candidate'].'，初试时间'.$this->dataInfo->field_list['firstReview']->gen_show_html();
			$url = 'quiz/resume';
			$notify = array();
			if (!$this->dataInfo->field_list['firstInterviewer']->isEmpty()){
				$notify[] = $this->dataInfo->field_list['firstInterviewer']->value;
			}
			if (!$this->dataInfo->field_list['secondInterviewer']->isEmpty()){
				$notify[] = $this->dataInfo->field_list['secondInterviewer']->value;
			}
			if (!$this->dataInfo->field_list['hr']->isEmpty()){
				$notify[] = $this->dataInfo->field_list['hr']->value;
			}
			$this->sendRtxNotify($notify,$title,$msg,$url);
		}
		return $data;
	}

	function editFirst($id){
		$this->setViewType(VIEW_TYPE_HTML);

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doUpdate/resume';

		$this->load->model('records/resume_model',"dataInfo");

        $this->dataInfo->init_with_id($id);

		$this->dataInfo->changeShowFields= array(
			array('firstInterviewer','firstResult'),
			array('firstDes'),
            );

        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();

        $this->editor_typ = 1;
        $this->title_create = $this->dataInfo->gen_editor_title();
		$this->doSthBeforeShowUpdatePage($typ,$id);
        $this->template->load('default_lightbox_edit', 'common/create_related');
	}

	function editSecond($id){
		$this->setViewType(VIEW_TYPE_HTML);

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doUpdate/resume';

		$this->load->model('records/resume_model',"dataInfo");

        $this->dataInfo->init_with_id($id);

		$this->dataInfo->changeShowFields= array(
			array('secondInterviewer','secondResult'),
            array('secondDes'),
            );

        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();

        $this->editor_typ = 1;
        $this->title_create = $this->dataInfo->gen_editor_title();
		$this->doSthBeforeShowUpdatePage($typ,$id);
        $this->template->load('default_lightbox_edit', 'common/create_related');
	}

}
