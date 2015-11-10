<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Docs extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
	}

	function index(){
		$this->typ = 'document';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Document';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('projectId','name','desc','uploadTS','uploadUid','relateID','fileLink');
		$this->needProjectId = true;

		// $this->need_plus = 'abiaozhun/baoyang_plus';

		$this->common_list();
    }

	function doUpload(){
        $jsonData = array();

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] ='txt|zip|rar|7z|doc|docx|dot|xls|xlm|pdf|ppt|pptx|jpg|png|bmp|tif|gif|msg|rar|zip|mp3|bak|xlsx';

        $config['max_size'] = '1000';
        $config['overwrite'] = false;
        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload("fileToUpload"))
        {
            $jsonData['errors'] = $this->upload->display_errors("","");
            $jsonRst = -1;
        }
        else

        {
            $info = $this->upload->data();
            $jsonData['url'] = site_url("")."/uploads/".$info['file_name'];
            $jsonData['is_image'] = $info['is_image'];
            $jsonData['client_name']  = $info['client_name'];
            $jsonRst = 1;

        }
        echo $this->exportData($jsonData,$jsonRst);
    }

	function create($typ,$relateTyp=0,$relateId=0) {
		$this->setViewType(VIEW_TYPE_HTML);
		$modelName = 'records/'.(ucfirst($typ)).'_model';

		$this->load->model($modelName,"dataInfo");
        $this->title_create = $this->dataInfo->title_create;

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doCreate/'.$typ;

		$this->dataInfo->field_list['relateTyp']->init($relateTyp);

        if ($relateTyp==1){
            $this->dataInfo->field_list['relateID']->set_relate_db('pProject','id','name');
        } elseif($relateTyp==2) {
            $this->dataInfo->field_list['relateID']->set_relate_db('cCrm','id','name');
        }
        $this->dataInfo->field_list['relateID']->init($relateId);

        $this->dataInfo->field_list['fileLink']->init("");

        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();

        $this->editor_typ = 0;
		$this->load->helper(array('form', 'url'));

        $this->template->load('default_lightbox_new', 'docs/new_document');
	}
}
