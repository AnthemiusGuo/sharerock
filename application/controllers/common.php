<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class common extends P_Controller {
	function __construct() {
		parent::__construct(true,'a');
		$this->relates = array();
		$this->is_lightbox = true;
		$this->plusId = '';
	}

	function common_list(){
		$this->admin_load_menus();
        $this->buildSearch();

        $this->load->model('lists/Common_list',"listInfo");

        $this->listInfo->setInfo($this->dataModelPrefix.$this->dataModelName,$this->dataModelName.'_list',$this->dataModelName.'_model');

        $this->listInfo->setSearchInfo($this->searchKeys);
        $this->listInfo->quickSearchWhere = $this->quickSearchKeys;
        $this->listInfo->setListInfo($this->listKeys);
		if (isset($this->orderKey)){
			$this->listInfo->orderKey = $this->orderKey;
		}

        $this->listInfo->paged = true;
        $this->listInfo->perPage = 20;
		$this->listInfo->limit = $this->listInfo->perPage;
        $this->listInfo->nowPage = $this->pagination->get_tough_page();

		foreach ($this->relates as $key => $value) {
			$this->listInfo->add_where(WHERE_TYPE_WHERE,$key,$value);
		}
		$this->doSthBeforeShowListPage($this->typ);
        $this->listInfo->load_data_with_search($this->searchInfo);
        $this->listInfo->is_lightbox = $this->is_lightbox;

        $this->perPage = 20;
		$config['total_rows'] = $this->listInfo->all_record_counts;
        $config['per_page'] = $this->perPage;

        $config['base_url'] = site_url($this->controller_name.'/'.$this->method_name.'/'.$this->plusId).'/';

        $this->pagination->initialize($config);
        $this->cur_page = $this->pagination->get_cur_page();

        $this->info_link = $this->controller_name . "/info/".$this->typ."/";
        $this->create_link =  $this->controller_name . "/create/".$this->typ."/";
        $this->deleteCtrl = $this->controller_name;
        $this->deleteMethod = 'doDel/'.$this->typ;
        $this->template->load('default_page', 'common/list_view');
	}

	function info($typ,$id){
        $modelName = 'records/'.(ucfirst($typ)).'_model';

		$this->need_plus = '';
        $this->id = $id;
        $this->load->library('user_agent');
        $this->refer = $this->agent->referrer();

        $this->load->model($modelName,"dataInfo");
        $this->dataInfo->init_with_id($id);

        $this->showNeedFields = $this->dataInfo->buildDetailShowFields();

        $this->infoTitle = $this->dataInfo->buildInfoTitle();

        array_unshift($this->title,$this->dataInfo->field_list['name']->gen_show_value());

		if ($this->dataInfo->has_changelog){
			$this->load->model('lists/Changelog_list',"changelogList");
			$this->changelogList->listKeys = array('name','solution','dueUser','beginTS');
			$this->changelogList->add_where(WHERE_TYPE_WHERE,'relate_turple.t',$this->dataInfo->changelog_typ);
			$this->changelogList->add_where(WHERE_TYPE_WHERE,'relate_turple.v',$this->dataInfo->id);
			$this->changelogList->load_data_with_where();
		}
		$this->doSthBeforeShowInfoPage($typ,$id);
        $this->template->load('default_lightbox_info', 'common/info');

    }

	function create($typ,$id=""){
        $this->setViewType(VIEW_TYPE_HTML);
		$modelName = 'records/'.(ucfirst($typ)).'_model';

        $this->load->model($modelName,"dataInfo");
        $this->title_create = $this->dataInfo->title_create;

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doCreate/'.$typ;

        if (isset($this->related_field) && $this->related_field!=''){
            if ($id!=""){
                $this->dataInfo->field_list[$this->related_field]->init($id);
                $this->related_id = $id;
            } else {
                $this->related_field = "";
            }
        }



        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();

        $this->editor_typ = 0;
		$this->doSthBeforeShowCreatePage($typ,$id);
        $this->template->load('default_lightbox_new', 'common/create_related');
    }

    function edit($typ,$id){
        $this->setViewType(VIEW_TYPE_HTML);

		$modelName = 'records/'.(ucfirst($typ)).'_model';

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doUpdate/'.$typ;

        $this->load->model($modelName,"dataInfo");

        $this->dataInfo->init_with_id($id);


        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();
		$this->doSthBeforeShowUpdatePage($typ,$id);

        $this->editor_typ = 1;
        $this->title_create = $this->dataInfo->gen_editor_title();

        $this->template->load('default_lightbox_edit', 'common/create_related');
    }

    function doCreate($typ){
        $this->setViewType(VIEW_TYPE_JSON);

		$modelName = 'records/'.(ucfirst($typ)).'_model';

        $jsonRst = 1;
        $zeit = time();

        $this->load->model($modelName,"dataInfo");
        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $data = array();

        foreach ($this->dataInfo->field_list as $key => $this_field) {
			if ($this->input->post($key)===false){
				continue;
			}
            $data[$key] = $this->dataInfo->field_list[$key]->gen_value($this->input->post($key));
        }

		foreach ($this->relates as $key => $value) {
			$data[$key] = $value;
		}

        $checkRst = $this->dataInfo->check_data($data);
        if (!$checkRst){
            $jsonRst = -1;
            $jsonData = array();
            $jsonData['err']['id'] = 'creator_'.$this->dataInfo->get_error_field();
            $jsonData['err']['msg'] ='请填写所有星号字段！';
            echo $this->exportData($jsonData,$jsonRst);
            return;
        }

		if (isset($this->dataInfo->field_list['createUid'])){
			$data['createUid'] = $this->userInfo->id;
			$data['createTS'] = $zeit;
		}

		$data = $this->doSthBeforeInsert($typ,$data);

        $newId = $this->dataInfo->insert_db($data);

		$changelog = $this->input->post('changelog');
		if ($this->dataInfo->has_changelog){
			$data['_id'] = $newId;
			$this->dataInfo->write_changelog('create',$data,$changelog);
		}

        $this->doSthAfterInsert($typ,$data,$newId);

        $jsonData = array();

        $jsonData['newId'] = (string)$newId;

        $this->exportToRefer(1,$jsonData);
    }



    function doUpdate($typ,$id){
        $this->setViewType(VIEW_TYPE_JSON);
		$modelName = 'records/'.(ucfirst($typ)).'_model';

        $jsonRst = 1;
		$jsonData = array();
        $zeit = time();


        $this->load->model($modelName,"dataInfo");

        $this->dataInfo->init_with_id($id);
        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();

        $data = array();
        foreach ($this->dataInfo->field_list as $key=>$value) {
			if ($this->input->post($key)===false){
				continue;
			}
            $newValue = $this->dataInfo->field_list[$key]->gen_value($this->input->post($key));
            if ($newValue!="".$this->dataInfo->field_list[$key]->value){
                $data[$key] = $newValue;
            }
        }

		$changelog = $this->input->post('changelog');

		$data = $this->doSthBeforeUpdate($typ,$data,$id);

        if (empty($data) && (!$this->dataInfo->has_changelog || ($changelog===false || $changelog==""))) {
            $jsonRst = -2;
            $jsonData['err']['msg'] ='无变化';
            echo $this->exportData($jsonData,$jsonRst);
            return false;
        }

		if (!empty($data)){
			$checkRst = $this->dataInfo->check_data($data,false);
	        if (!$checkRst){
	            $jsonRst = -1;

	            $jsonData['err']['id'] = 'modify_'.$this->dataInfo->get_error_field();
	            $jsonData['err']['msg'] ='请填写所有星号字段！';
	            echo $this->exportData($jsonData,$jsonRst);
	            return false;
	        }
	        $zeit = time();

	        $this->dataInfo->update_db($data,$id);

			$this->doSthAfterUpdate($typ,$data,$id);
		}

		if ($this->dataInfo->has_changelog){
			$this->dataInfo->write_changelog('update',$data,$changelog);
		}

        $this->exportToRefer(1,$jsonData);
    }

    function doDel($typ,$id){
        $this->setViewType(VIEW_TYPE_JSON);

        $goto_url = $this->controller_name.'/cars';
		$modelName = 'records/'.(ucfirst($typ)).'_model';
        $jsonRst = 1;
        $zeit = time();

        $this->load->model($modelName,"dataInfo");
        $this->dataInfo->init_with_id($id);
        if (!$this->dataInfo->check_can_delete($id)){
            $err = $this->dataInfo->getLastError();

            $jsonRst = $err['errNo'];
            $jsonData = array();
            $jsonData['err']['msg'] = $err['msg'];
            echo $this->exportData($jsonData,$jsonRst);
            return;
        }


        $this->dataInfo->delete_related($id);
        $this->dataInfo->delete_db($id);

        //额外操作
        $this->doSthAfterDelete($typ,$id);

        $this->exportToRefer(1,$jsonData);
    }

	function doSthAfterInsert($typ,$data,$newId){
		//等继承，基类啥都不做
	}

	function doSthBeforeInsert($typ,$data){
		return $data;
	}

	function doSthBeforeUpdate($typ,$data,$id){
		return $data;
	}

	function doSthAfterUpdate($typ,$data,$id){
		//等继承，基类啥都不做
	}

	function doSthAfterDelete($typ,$id){
		//等继承，基类啥都不做
	}

	function doSthBeforeShowCreatePage($typ,$id){
		//等继承，基类啥都不做
	}

	function doSthBeforeShowUpdatePage($typ,$id){
		//等继承，基类啥都不做
	}

	function doSthBeforeShowInfoPage($typ,$id){
		//等继承，基类啥都不做
	}
	function doSthBeforeShowListPage($typ){

	}

	function doUpload($typ="",$field="",$id=""){
		if ($id==""){
			$domId = "input_".$field;
		} else {
			$domId = "input_".$field."_".$id;
		}
		$rst = $this->_upload($domId);
		if ($rst['rstno']<0){
			echo $this->exportData(array('err'=>array('msg'=>$rst['msg'])),$rst['rstno']);
		} else {
			echo $this->exportData($rst,$rst['rstno']);

		}

	}

	function _upload($domId,$upload_dir='uploads')
    {
        $config['upload_path'] = './misc/'.$upload_dir.'/';
        $config['allowed_types'] ='xlsx|png|jpg|jpeg|docx|doc|xls|ppt|txt|md';

        $config['max_size'] = '10000';
        $config['overwrite'] = false;
        $this->load->library('upload', $config);

        $info = $this->upload->data();
        if ( ! $this->upload->do_upload($domId))
        {

            $jsonData = array();
            $jsonData['rstno'] = -1;
            $jsonData['msg'] = $this->upload->display_errors("","");
            return $jsonData;
        }
        else

        {
            $info = $this->upload->data();
            $jsonData = array();
            $jsonData['rstno'] = 1;
            $jsonData['link'] = $info['file_name'];
            $jsonData['url'] = static_url("/uploads/".$info['file_name']);
            return $jsonData;
        }
    }
}
