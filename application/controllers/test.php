<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Test extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
	}

	function index($versionId=-1){
		$this->is_lightbox = false;
		if($this->userInfo->field_list['isManager']->value==0){
			$this->canEdit=false;
		}
        $this->admin_load_menus();
        $this->load->model('lists/Release_list',"listInfo");
		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);

		$this->versionId=$versionId;
		$this->load->model('lists/Version_list',"versionList");
		$this->versionList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->versionList->load_data_with_where();
		if($this->versionId!=-1){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'versionId',$this->versionId);
		}

		$this->listInfo->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->listInfo->load_data_with_where();
		$this->need_plus='test/release';
        // $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/release/";

        $this->template->load('default_page', 'common/list_view');
    }
	function myTest($status=-1){
		$this->is_lightbox = false;
		$this->canCreate=false;
        $this->admin_load_menus();
        $this->load->model('lists/Bug_list',"listInfo");
		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);
		$this->need_plus='test/bugList';

		$this->status=$status;
		if($this->status==-1){
			$this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'createUid',$this->userInfo->id);
			$this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'dueUser',$this->userInfo->id);
			$this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'relatedUsers',$this->userInfo->id);
		}else if($this->status==0){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'dueUser',$this->userInfo->id);
		}else if($this->status==1){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'createUid',$this->userInfo->id);
		}else if($this->status==2){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'relatedUsers',$this->userInfo->id);
		}

		$this->myTestType=$this->input->get('myTestType');
		if($this->myTestType==false){
			$this->myTestType=1;
		}
		if($this->myTestType==2){
			$this->listInfo->add_where(WHERE_TYPE_IN,'status',array(0,3));
		}else if($this->myTestType==3){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',1);
		}else if($this->myTestType==4){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',2);
		}

		$this->listInfo->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->listInfo->load_data_with_where();
        // $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/bug/";

        $this->template->load('default_page', 'common/list_view');
    }
	function bug($versionId=-1){
		$this->is_lightbox = false;
		$this->createAsLightbox=false;
        $this->admin_load_menus();
        $this->load->model('lists/Bug_list',"listInfo");
		$this->load->model('records/Bug_model',"bugInfo");

		$this->versionId=$versionId;
		$this->load->model('lists/Version_list',"versionList");
		$this->versionList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->versionList->load_data_with_where();

		$this->status=-1;
		$this->beginTS=0;
		$this->endTS=0;
		$this->begin="";
		$this->end="";

		$status=$this->input->get('status');
		if($status!==false){
			$this->status=$status;
		}
		if($this->status!=-1){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',(int)$this->status);
		}
		$begin=$this->input->get('begin');
		if($begin!==false && trim($begin)!=""){
			$this->beginTS=$this->utility->getTSFromDateString($begin);
			$this->begin=$begin;
		}
		$end=$this->input->get('end');
		if($end!==false && trim($end)!=""){
			$this->endTS=$this->utility->getTSFromDateString($end)+86400-1;
			$this->end=$end;
		}
		$this->need_plus='test/test_list';
		if ($this->beginTS!=0 && $this->endTS!=0){
			$this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'createTS',$this->endTS);
			$this->listInfo->add_where(WHERE_TYPE_WHERE_GT,'createTS',$this->beginTS);
		}

		// $this->testType=$this->input->get('testType');
		// if($this->testType==false){
		// 	$this->testType=1;
		// }
		// if($this->testType==2){
		// 	$this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'status',0);
		// 	$this->listInfo->add_where(WHERE_TYPE_OR_WHERE,'status',3);
		// }else if($this->testType==3){
		// 	$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',1);
		// }else if($this->testType==4){
		// 	$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',2);
		// }

		if($this->versionId!=-1){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'versionId',$this->versionId);
		}

		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);

		$this->listInfo->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->listInfo->load_data_with_where();
		// $this->need_plus='test/release';
        // $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/createBug/";

        $this->template->load('default_page', 'common/list_view');
    }

	function form(){
		$this->admin_load_menus();
		$this->everyOneSubmitBug = $this->input->get('everyOneSubmitBug');
		$this->everyOneSolveBug = $this->input->get('everyOneSolveBug');
		$this->everyOneCloseBug = $this->input->get('everyOneCloseBug');
		$this->bugSeriousLevel = $this->input->get('bugSeriousLevel');
		$this->bugSolveMethod = $this->input->get('bugSolveMethod');
		$this->bugStatus = $this->input->get('bugStatus');
		$this->bugActivateNum = $this->input->get('bugActivateNum');
		$this->bugType = $this->input->get('bugType');
		$this->bugDueUser = $this->input->get('bugDueUser');
		$this->versionBug = $this->input->get('versionBug');


		if($this->everyOneSubmitBug=="true"){
			$this->load->model('lists/Bug_list',"listInfo1");
			$this->listInfo1->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo1->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo1->load_data_with_where();

			$counterEveryOneSubmitBug = array();
			foreach ($this->listInfo1->record_list as $key => $value) {
				if (!isset($counterEveryOneSubmitBug[$value->field_list['createUid']->value])){
					$counterEveryOneSubmitBug[$value->field_list['createUid']->value] = 1;
				}else{
					$counterEveryOneSubmitBug[$value->field_list['createUid']->value]++;
				}
			}
			$this->dataChartEveryOneSubmitBug = array();
			foreach ($counterEveryOneSubmitBug as $key => $value) {
				$this->load->model('records/User_model',"uInfo1");
				$this->uInfo1->init_with_id($key);
				$this->dataChartEveryOneSubmitBug[] = array("label"=>$this->uInfo1->field_list['name']->value,
						"data"=>$value);
			}
		}
		if($this->everyOneSolveBug=="true"){
			$this->load->model('lists/Bug_list',"listInfo2");
			$this->listInfo2->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo2->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo2->load_data_with_where();

			$counterEveryOneSolveBug = array();
			foreach ($this->listInfo2->record_list as $key => $value) {
				if (!isset($counterEveryOneSolveBug[$value->field_list['dueUser']->value])){
					$counterEveryOneSolveBug[$value->field_list['dueUser']->value] = 1;
				}else{
					$counterEveryOneSolveBug[$value->field_list['dueUser']->value]++;
				}
			}
			$this->dataChartEveryOneSolveBug = array();
			foreach ($counterEveryOneSolveBug as $key => $value) {
				$this->load->model('records/User_model',"uInfo2");
				$this->uInfo2->init_with_id($key);
				$this->dataChartEveryOneSolveBug[] = array("label"=>$this->uInfo2->field_list['name']->value,
						"data"=>$value);
			}
		}
		if($this->everyOneCloseBug=="true"){
			$this->load->model('lists/Bug_list',"listInfo3");
			$this->listInfo3->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo3->add_where(WHERE_TYPE_WHERE,'status',2);
			$this->listInfo3->load_data_with_where();

			$counterEveryOneCloseBug = array();
			foreach ($this->listInfo3->record_list as $key => $value) {
				if (!isset($counterEveryOneCloseBug[$value->field_list['createUid']->value])){
					$counterEveryOneCloseBug[$value->field_list['createUid']->value] = 1;
				}else{
					$counterEveryOneCloseBug[$value->field_list['createUid']->value]++;
				}
			}
			$this->dataChartEveryOneCloseBug = array();
			foreach ($counterEveryOneCloseBug as $key => $value) {
				$this->load->model('records/User_model',"uInfo3");
				$this->uInfo3->init_with_id($key);
				$this->dataChartEveryOneCloseBug[] = array("label"=>$this->uInfo3->field_list['name']->value,
						"data"=>$value);
			}
		}
		if($this->bugSeriousLevel=="true"){
			$this->load->model('lists/Bug_list',"listInfo4");
			$this->listInfo4->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo4->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo4->load_data_with_where();

			$counterBugSeriousLevel = array();
			foreach ($this->listInfo4->record_list as $key => $value) {
				if (!isset($counterBugSeriousLevel[$value->field_list['level']->value])){
					$counterBugSeriousLevel[$value->field_list['level']->value] = 1;
				}else{
					$counterBugSeriousLevel[$value->field_list['level']->value]++;
				}
			}
			$bugLevel=array(0=>'S',1=>'A',2=>'B',3=>'C');
			$this->dataChartBugSeriousLevel = array();
			foreach ($counterBugSeriousLevel as $key => $value) {
				$this->dataChartBugSeriousLevel[] = array("label"=>$bugLevel[$key],
						"data"=>$value);
			}
		}
		if($this->bugStatus=="true"){
			$this->load->model('lists/Bug_list',"listInfo6");
			$this->listInfo6->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo6->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo6->load_data_with_where();

			$counterBugStatus = array();
			foreach ($this->listInfo6->record_list as $key => $value) {
				if (!isset($counterBugStatus[$value->field_list['status']->value])){
					$counterBugStatus[$value->field_list['status']->value] = 1;
				}else{
					$counterBugStatus[$value->field_list['status']->value]++;
				}
			}
			$bugState=array(0=>'激活',1=>'已解决',3=>'延期处理');
			$this->dataChartBugStatus = array();
			foreach ($counterBugStatus as $key => $value) {
				$this->dataChartBugStatus[] = array("label"=>$bugState[$key],
						"data"=>$value);
			}
		}
		if($this->bugActivateNum=="true"){
			$this->load->model('lists/Bug_list',"listInfo7");
			$this->listInfo7->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo7->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo7->load_data_with_where();

			$counterBugActivateNum = array();
			foreach ($this->listInfo7->record_list as $key => $value) {
				if (!isset($counterBugActivateNum[$value->field_list['activateNum']->value])){
					$counterBugActivateNum[$value->field_list['activateNum']->value] = 1;
				}else{
					$counterBugActivateNum[$value->field_list['activateNum']->value]++;
				}
			}
			$this->dataChartBugActivateNum = array();
			foreach ($counterBugActivateNum as $key => $value) {
				$this->dataChartBugActivateNum[] = array("label"=>$key,
						"data"=>$value);
			}
		}
		if($this->bugType=="true"){
			$this->load->model('lists/Bug_list',"listInfo8");
			$this->listInfo8->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo8->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo8->load_data_with_where();

			$counterBugType = array();
			foreach ($this->listInfo8->record_list as $key => $value) {
				if (!isset($counterBugType[$value->field_list['typ']->value])){
					$counterBugType[$value->field_list['typ']->value] = 1;
				}else{
					$counterBugType[$value->field_list['typ']->value]++;
				}
			}
			$bugTyp=array(0=>'代码错误',1=>'界面优化',2=>'任务',3=>'界面优化',4=>'文字/文档/语法',5=>'背景音乐',6=>'音效',7=>'特效',8=>'人工智能',9=>'本地化',10=>'帧率',11=>'碰撞',12=>'网络延迟',13=>'设计缺陷',14=>'配置相关',15=>'安装部署',16=>'安全相关',17=>'性能问题',18=>'标准规范',19=>'测试脚本',20=>'死机',21=>'其他');
			$this->dataChartBugType = array();
			foreach ($counterBugType as $key => $value) {
				$this->dataChartBugType[] = array("label"=>$bugTyp[$key],
						"data"=>$value);
			}
		}
		if($this->bugDueUser=="true"){
			$this->load->model('lists/Bug_list',"listInfo9");
			$this->listInfo9->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo9->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo9->load_data_with_where();

			$counterBugDueUser = array();
			foreach ($this->listInfo9->record_list as $key => $value) {
				if (!isset($counterBugDueUser[$value->field_list['dueUser']->value])){
					$counterBugDueUser[$value->field_list['dueUser']->value] = 1;
				}else{
					$counterBugDueUser[$value->field_list['dueUser']->value]++;
				}
			}
			$this->dataChartBugDueUser = array();
			foreach ($counterBugDueUser as $key => $value) {
				$this->load->model('records/User_model',"uInfo9");
				$this->uInfo9->init_with_id($key);
				$this->dataChartBugDueUser[] = array("label"=>$this->uInfo2->field_list['name']->value,
						"data"=>$value);
			}
		}
		if($this->versionBug=="true"){
			$this->load->model('lists/Bug_list',"listInfo10");
			$this->listInfo10->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
			$this->listInfo10->add_where(WHERE_TYPE_WHERE_NE,'status',2);
			$this->listInfo10->load_data_with_where();

			$counterVersionBug = array();
			foreach ($this->listInfo10->record_list as $key => $value) {
				if (!isset($counterVersionBug[$value->field_list['versionId']->value])){
					$counterVersionBug[$value->field_list['versionId']->value] = 1;
				}else{
					$counterVersionBug[$value->field_list['versionId']->value]++;
				}
			}
			$this->dataChartVersionBug = array();
			foreach ($counterVersionBug as $key => $value) {
				if($key!=null){
				$this->load->model('records/Version_model',"versionInfo");
				$this->versionInfo->init_with_id($key);
				$this->dataChartVersionBug[] = array("label"=>$this->versionInfo->field_list['name']->value,
						"data"=>$value);
					}else{
						$this->dataChartVersionBug[] = array("label"=>"没有选择版本",
						"data"=>$value);

					}
			}
		}
		$this->template->load('default_page', 'test/createForm');
	}

	// function demo(){
	// 	$this->is_lightbox = false;
	//
	// 	$this->admin_load_menus();
	// 	$this->load->model('lists/Demo_list',"listInfo");
	// 	$this->buildSearch();
	// 	$this->listInfo->build_where_with_search($this->searchInfo);
	// 	$this->listInfo->load_data_with_where();
	// 	// $this->listInfo->is_lightbox = true;
	//
	// 	$this->create_link =  $this->controller_name . "/createDemo/";
	//
	// 	$this->template->load('default_page', 'common/list_view');
	// }
	// function createDemo(){
	// 	$this->setViewType(VIEW_TYPE_HTML);
	// 	$this->admin_load_menus();
	//
    //     $this->load->model('records/Demo_model',"dataInfo");
    //     $this->title_create = $this->dataInfo->title_create;
	//
    //     $this->createUrlC = $this->controller_name;
    //     $this->createUrlF = 'doCreate/demo';
	//
	//
    //     $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
    //     $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();
	//
    //     $this->editor_typ = 0;
	// 	$this->template->load('default_page', 'test/demo');
	// }
	// function upLoad(){
	// 	$uploaddir="/var/www/html/management/webServer/pic/";
	// 	$serverdir="/management/webServer/pic/";
	// 	$picType=array("jpg","gif","bmp","jpeg","png");
	// 	$patch="";
	// 	$this->admin_load_menus();
	// 	$this->show_method_name="demo";
	//
	// 	$a=strtolower(substr(strrchr($_FILES['file']['name'],'.'),1));
	// 	if(!in_array(strtolower(substr(strrchr($_FILES['file']['name'],'.'),1)),$picType)){
	// 	    $text=implode(",",$picType);
	// 	    // var_dump($text);
	// 	    echo "请上传以下格式文件：",$text,"<br>";
	// 	}else{
	// 	    $filename=explode(".",$_FILES['file']['name']);
	// 	    do{
	// 			$hash='HMN-';
	// 		    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	// 		    $max=strlen($chars)-1;
	// 		    mt_srand((double)microtime()*1000000);
	// 		    for($i=0;$i<10;$i++){
	// 		        $hash.=$chars[mt_rand(0,$max)];
	// 		    }
	// 	        $filename[0]=$hash;
	// 	        $name=implode(".",$filename);
	// 	        $uploadfile=$uploaddir.$name;
	// 			$canLook=$serverdir.$name;
	// 	    }
	// 	    while(file_exists($uploadfile));
	// 	    if(move_uploaded_file($_FILES['file']['tmp_name'],$uploadfile)){
	// 	        // if(is_uploaded_file($_FILES['file']['tmp_name'])){
	// 			if(file_exists($uploadfile)){
	// 	            echo "<center>您的图片已经上传完毕，预览：</center><br><center><img src='$canLook'></center>";
	// 	            echo "<br><center><a href='javascript:history.go(-1)'>继续上传</a></center>";
	// 				$this->load->model('records/Demo_model',"dataInfo");
	// 				$data=array();
	// 				$data['name']=$name;
	// 				$this->dataInfo->insert_db($data);
	// 				// $jsonData = array();
	// 		        // $this->exportToRefer(1,$jsonData);
	// 	        }else{
	// 	            echo "上传失败!";
	// 	        }
	// 	    }
	// 	}
	// }
	function createBug(){
		$this->is_lightbox = false;
		$this->login_verify();
        $this->admin_load_menus();
		$this->show_method_name='bug';
		$this->setViewType(VIEW_TYPE_HTML);
		$this->tmp="[详细步骤]

[预期结果]
[实际结果]";
		$this->load->model('records/Project_model',"proInfo");
		$this->proInfo->init_with_id($this->userInfo->field_list['projectId']->value);
		$this->projectName=$this->proInfo->field_list['name']->value;

        $this->load->model('records/Bug_model',"dataInfo");
        $this->title_create = $this->dataInfo->title_create;

        $this->createUrlC = $this->controller_name;
		if (isset($this->related_field) && $this->related_field!=''){
			if ($id!=""){
				$this->dataInfo->field_list[$this->related_field]->init($id);
				$this->related_id = $id;
			} else {
				$this->related_field = "";
			}
		}
        $this->createPostFields=$this->dataInfo->buildChangeNeedFields();

        $this->editor_typ = 0;
        $this->template->load('default_create_page', 'test/createBug');
	}

	function infoBug($id){
		$this->show_method_name = 'bug';
		$this->login_verify();
		$this->admin_load_menus();

		$this->load->model('records/Bug_model',"dataInfo");
		$this->dataInfo->init_with_id($id);

		$this->template->load('default_page', 'test/bugInfo');

	}

	function editBug($id){
		$this->is_lightbox = false;
		$this->bugId=$id;
		$this->login_verify();
		$this->admin_load_menus();
		$this->show_method_name='bug';
		$this->setViewType(VIEW_TYPE_HTML);

		$this->load->model('records/Project_model',"proInfo");
		$this->proInfo->init_with_id($this->userInfo->field_list['projectId']->value);
		$this->projectName=$this->proInfo->field_list['name']->value;

		$this->load->model('records/Bug_model',"dataInfo");
		$this->dataInfo->init_with_id($id);

		$this->createUrlC = $this->controller_name;

		$this->createPostFields = $this->dataInfo->buildChangeNeedFields();

		$this->editor_typ = 1;
        $this->title_create = $this->dataInfo->gen_editor_title();
		$this->template->load('default_edit_page', 'test/createBug');
	}
	function doUpdateBug($typ,$id){
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


	function releaseInfo($releaseId){
		$this->show_method_name = 'index';
		$this->login_verify();
		$this->admin_load_menus();
		$this->releaseId=$releaseId;
		$this->load->model('records/Release_model',"releaseInfo");
		$this->releaseInfo->init_with_id($releaseId);

		$this->load->model('lists/Note_list',"noteList");
		$this->noteList->add_where(WHERE_TYPE_WHERE,"releaseId",$releaseId);
		$this->noteList->listKeys = array('name','createUid','dueUser','createTS','typ');
		$this->noteList->load_data_with_where();
		$this->noteList->is_lightbox = true;

		$this->load->model('lists/Bug_list',"bugList");
		$this->bugList->add_where(WHERE_TYPE_WHERE,"releaseId",$releaseId);
		// $this->bugList->add_where(WHERE_TYPE_WHERE,"status",2);
		$this->bugList->listKeys = array('level','priority','name','versionId','status','createUid','dueUser','createTS','endTS');
		$this->bugList->load_data_with_where();
		$this->bugList->is_lightbox = true;

		$this->template->load('default_page', 'test/info');
	}

	function doDelay($id){
		$this->load->model('records/Bug_model',"dataInfo");
		$this->dataInfo->init_with_id($id);
		$data=array();
		$data['status']=3;
		$this->dataInfo->update_db($data);
		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}
	// function doOK($id){
	// 	$this->setViewType(VIEW_TYPE_HTML);
	//
    //     $this->createUrlC = $this->controller_name;
    //     $this->createUrlF = 'doUpdate/bug';
	//
	// 	$this->load->model('records/Bug_model',"dataInfo");
	//
    //     $this->dataInfo->init_with_id($id);
	//
	//
    //     $this->createPostFields = array('desc');
    //     $this->modifyNeedFields = $this->dataInfo->buildChangeShowFields();
	//
    //     $this->editor_typ = 1;
    //     $this->title_create = $this->dataInfo->gen_editor_title();
	//
    //     $this->template->load('default_lightbox_edit', 'common/create_related');
	//
	// }
	function doActivation($id){
		$this->load->model('records/Bug_model',"dataInfo");
		$this->dataInfo->init_with_id($id);
		$data=array();
		$data['status']=0;
		$data['endTS']=0;
		$data['activateNum']=(int)$this->dataInfo->field_list['activateNum']->value+1;
		$this->dataInfo->update_db($data);
		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}
	function doClose($id){
		$this->load->model('records/Bug_model',"dataInfo");
		$this->dataInfo->init_with_id($id);
		if($this->dataInfo->field_list['releaseId']->value==""){
			$jsonRst = -1;
            $jsonData = array();
            $jsonData['err']['msg'] ='请填写release！';
            echo $this->exportData($jsonData,$jsonRst);
		}else{
			$data=array();
			$data['status']=2;
			$this->dataInfo->update_db($data);
			$jsonData = array();
        	$this->exportToRefer(1,$jsonData);
		}
	}

	function exportBug(){
		$this->load->model('lists/Common_list',"listInfo");

        $this->listInfo->setInfo('tBug','Bug_list','Bug_model');
        $this->listInfo->orderKey = array('typ'=>'asc');
		$this->listInfo->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
        $this->listInfo->load_data_with_where();

        $fileName = BASEPATH."../wwwroot/templates/template_bug.xlsx";
        $exportName = "exports/bug-".date('Y')."-".date('m')."-".date('d').'-'.substr(md5($searchInfo.time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();

        $title = 'bug导出表 '.date('Y')."-".date('m')."-".date('d');

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {

            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['name']->gen_show_value());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['reStep']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_record->field_list['desc']->gen_show_value());
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['picture']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['attachment']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['projectId']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['versionId']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['releaseId']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['typ']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['level']->gen_show_value());
            $objWorksheet->getCell('K'.$i)->setValue($this_record->field_list['priority']->gen_show_value());
            $objWorksheet->getCell('L'.$i)->setValue($this_record->field_list['status']->gen_show_value());
            $objWorksheet->getCell('M'.$i)->setValue($this_record->field_list['createUid']->gen_show_value());
			$objWorksheet->getCell('N'.$i)->setValue($this_record->field_list['dueUser']->gen_show_value());
			$objWorksheet->getCell('O'.$i)->setValue($this_record->field_list['IE']->gen_show_value());
			$objWorksheet->getCell('P'.$i)->setValue($this_record->field_list['relatedUsers']->gen_show_value());
			$objWorksheet->getCell('Q'.$i)->setValue($this_record->field_list['createTS']->gen_show_value());
			$objWorksheet->getCell('R'.$i)->setValue($this_record->field_list['endTS']->gen_show_value());
            $i++;
        }

        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
	}

	function doSthBeforeInsert($typ,$data){
		if($typ=="release"){
			$data['projectId']=$this->userInfo->field_list['projectId']->value;
		}
		if($typ=="bug"){
			$data['projectId']=$this->userInfo->field_list['projectId']->value;
			$data['activateNum']=0;
			if($data['status']==""){
				$data['status']=0;
			}
			if($data['releaseId']!=0){
				$this->load->model('records/Release_model',"releaseInfo");
				$this->releaseInfo->init_with_id($data['releaseId']);
				$data['versionId']=$this->releaseInfo->field_list['versionId']->value;
			}
		}
		return $data;
	}
	function doSthAfterInsert($typ,$data,$newId){
		if($typ=="bug"){
	        $jsonData['goto_url'] = site_url("test/bug");
	        echo $this->exportData($jsonData,1);exit;
		}
	}
	function doSthAfterUpdate($typ,$data,$id){
		if($typ=="bug"){
	        $jsonData['goto_url'] = site_url("test/bug");
	        echo $this->exportData($jsonData,1);exit;
		}
	}
	function doSthBeforeShowUpdatePage($typ,$id){
		if($typ=="bug"){
			$this->modifyNeedFields=array(array('desc'),);
		}
	}
	function doSthBeforeUpdate($typ,$data,$id){
		if($typ=="bug"){
			$this->load->model('records/Bug_model',"dataInfo");
			$this->dataInfo->init_with_id($id);
			$data['status']=1;
			$data['endTS']=time();
			$this->dataInfo->update_db($data);
			// $jsonData = array();
	        // $this->exportToRefer(1,$jsonData);
		}
		return $data;
	}
	function create($typ,$id=""){
		if ($typ=="note" && $id!=""){
			$this->related_field = 'releaseId';
			// $this->related_id=$id;
		}
		parent::create($typ,$id);
	}

	function doUpload($typ="",$field="",$id=""){
		if ($field=="attachment"){
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
