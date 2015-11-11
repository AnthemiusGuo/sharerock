<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Art extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
	}

	function index($typ=-1){

        $this->admin_load_menus();
        $this->load->model('lists/Needs_list',"listInfo");

		$this->typ=$typ;
		if($typ!=-1){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'typ',(int)$typ);
		}

		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);

		$this->listInfo->paged = true;
		$this->listInfo->perPage = 20;
		$this->listInfo->limit = $this->listInfo->perPage;
		$this->listInfo->nowPage = $this->pagination->get_tough_page();
		$this->listInfo->load_data_with_where();

        $this->listInfo->is_lightbox = false;

        $this->create_link =  $this->controller_name . "/create/needs/";

		$this->need_plus = 'art/typ_list';

        $this->template->load('default_page', 'common/list_view');
    }

	function create($typ,$id=""){
		if ($typ=="arkskill" && $id!=""){
			$this->related_field = 'needId';
			$this->related_id=$id;
		}
		parent::create($typ,$id);
	}

	function calendar(){
		$this->admin_load_menus();

		$this->template->load('default_page', 'art/calendar');
	}

	function needsInfo($needId="") {
        $this->show_method_name = 'index';

		$this->load->model('records/Needs_model',"needInfo");
		$this->needInfo->init_with_id($needId);

		$this->load->model('lists/Artskill_list',"arkskillList");
		$this->arkskillList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->arkskillList->add_where(WHERE_TYPE_WHERE,'needId',$needId);

		$this->arkskillList->load_data_with_where();

		$this->template->load('default_page', 'art/info');
	}

	function calList(){
		$start = $this->input->get('start');
		$end = $this->input->get('end');

		$colors = array('#d9534f','#337ab7','#f0ad4e','#5cb85c');

		$this->load->model('lists/Needs_list',"needsList");
		$this->needsList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->needsList->load_data_with_where();

		foreach($this->needsList->record_list as  $this_record) {
			$showTime=$this_record->field_list['dueEndTS']->value;
			$events[] = array(
                        "id"=>$this_record->id,
						"typ"=>'needs',
                        "title"=>
							'[美术需求]名称:'.$this_record->field_list['name']->value.' by:'.$this_record->field_list['dueUser']->gen_show_value(),

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


        echo json_encode($events);
	}

	function doConfirm($needsId){
		$this->load->model('records/Needs_model',"needInfo");
		$this->needInfo->init_with_id($needsId);

		$data=array();
		$data['beginTS']=time();
		$data['status']=1;

		$this->needInfo->update_db($data);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}

	function doFinish($needsId){
		$this->load->model('records/Needs_model',"needInfo");
		$this->needInfo->init_with_id($needsId);

		$data=array();
		$data['endTS']=time();
		$data['status']=2;

		$this->needInfo->update_db($data);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}

	function doSthBeforeInsert($typ,$data){
		if($typ=="needs"){
			$data['status']=0;
		}
		return $data;
	}

	function doSthAfterInsert($typ,$data,$newId){
		if($typ=="needs"){
			$title = '您有新的美术需求等待确认';
			$msg = $data['name'];
			$url = 'art/index';
			$this->sendRtxNotify(array($data['dueUser']),$title,$msg,$url);
		}
		return $data;
	}

	function doUpload($typ="",$field="",$id=""){
		if ($field=="doc" || $field=='docs'){
			$uploadDir = 'duploads';
		} else {
			$uploadDir = 'uploads';
		}
		if ($id==""){
			$domId = "input_".$field;
		} else {
			$domId = "input_".$field."_".$id;
		}
		$rst = $this->_upload($domId,$uploadDir);
		if ($rst['rstno']<0){
			echo $this->exportData(array('err'=>array('msg'=>$rst['msg'])),$rst['rstno']);
		} else {
			echo $this->exportData($rst,$rst['rstno']);

		}

	}


}
