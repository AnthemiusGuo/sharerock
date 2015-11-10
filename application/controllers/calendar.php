<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Calendar extends common {
	function __construct() {
		parent::__construct(true,'a');
        $this->load->library('pagination');
		$this->relates = array('projectId'=>$this->userInfo->field_list['projectId']->value);
	}

	function index(){
		$this->admin_load_menus();
		$this->template->load('default_page', 'calendar/calendar');
	}

	function calendarPeaple($reporterUid=""){
		$this->admin_load_menus();
		$this->reporterUid = $reporterUid;

		$this->load->model('lists/User_list',"reporterList");
		$this->reporterList->add_where(WHERE_TYPE_WHERE,"projectIds",$this->userInfo->field_list['projectId']->value);
		$this->reporterList->add_where(WHERE_TYPE_IN,"typ",array(0,1,2,3,4));

		$this->reporterList->load_data_with_where();

		$this->template->load('default_page', 'calendar/calendarPeaple');
	}

	function version(){
		$this->is_lightbox=false;

		if($this->userInfo->field_list['typ']->value==0||$this->userInfo->field_list['typ']->value==5||$this->userInfo->field_list['typ']->value==6||$this->userInfo->field_list['typ']->value==7||$this->userInfo->field_list['typ']->value==8||$this->userInfo->field_list['typ']->value==9){
			$this->canEdit=false;
		}
		$this->typ = 'version';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Version';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('projectId','name','desc','status','beginTS','endTS','realEndTS','packed');
		$this->needProjectId = true;

		// $this->need_plus = 'abiaozhun/baoyang_plus';

		$this->common_list();
	}

	function versionInfo($id){
		$this->show_method_name = 'version';
		$this->login_verify();
		$this->admin_load_menus();
		$this->is_lightbox=false;

		$this->load->model('lists/Contrast_list',"contrastList");
		$this->contrastList->add_where(WHERE_TYPE_WHERE,'versionId',$id);
		$this->contrastList->listKeys = array('createTS','name','plan','art');
		$this->contrastList->orderKey = array('createTS'=>'desc');
		$this->contrastList->load_data_with_where();

		$this->load->model('records/Version_model',"versionInfo");
		$this->versionInfo->init_with_id($id);

		$this->canEditFeature=($this->userInfo->field_list['typ']->in_array(array(1,2,3,4,100)));
		$this->admin_load_menus();
		$this->now_sub_menu = $sub_menu;

		$this->sub_menus = array(
			"index"=>array("name"=>"概要内容","show"=>true),
			"feature"=>array("name"=>"功能列表","show"=>true),
			"changelog"=>array("name"=>"每日进展","show"=>true)
        );

        if (isset($this->sub_menus[$sub_menu])){
            $this->now_sub_menu = $sub_menu;
        } else {
            $this->now_sub_menu = "index";
        }

		$this->load->model('lists/Feature_list',"featureList");

		$this->featureList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->featureList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->featureList->load_data_with_where();
		$this->arrFeaturesByStatus = array(2=>array(),5=>array(),3=>array(),1=>array(),4=>array(),0=>array());

		$this->arrStoryByFeatures = array();
		$this->arrAiByFeatures = array();

		foreach ($this->featureList->record_list as $key => $value) {
			$this->arrFeaturesByStatus[$value->field_list['status']->value][] = $value;
		}


		$this->load->model('lists/Common_list',"storyList");
        $this->storyList->setInfo('pStory','Story_list','Story_model');

		$this->storyList->orderKey = array('featureId'=>'asc','dueEndTS'=>'asc','status'=>'desc','priority'=>'desc');
		$this->storyList->listKeys = array('versionId','featureId','system','name','desc','status','dueUser','beginTS','dueEndTS','endTS');

		$this->storyList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->storyList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->storyList->load_data_with_where();

		$this->load->model('lists/Common_list',"aiList");
        $this->aiList->setInfo('pActionitem','Actionitem_list','Actionitem_model');

		$this->aiList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->aiList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->aiList->orderKey = array('featureId'=>'asc','endTS'=>'asc','status'=>'desc','priority'=>'desc');
		$this->aiList->listKeys = array('versionId','featureId','name','desc','dueUser','priority','status','dueEndTS','endTS');

		$this->aiList->load_data_with_where();

		$this->load->model('lists/Common_list',"needList");
        $this->needList->setInfo('aNeeds','Needs_list','Needs_model');

		$this->needList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->needList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->needList->load_data_with_where();

		$counterStoryListByStatus = array();
		$counterAiListByStatus = array();
		$counterNeedListByStatus = array();

		foreach ($this->storyList->record_list as $key => $value) {
			if (!isset($counterStoryListByStatus[$value->field_list['status']->value])){
				$counterStoryListByStatus[$value->field_list['status']->value] = 0;
			}
			$story = 1;
			if ($value->field_list['storyPoint']->value>0){
				$story = $value->field_list['storyPoint']->value;
			}
			$counterStoryListByStatus[$value->field_list['status']->value] +=$story;
			$this->arrStoryByFeatures[$value->field_list['featureId']->value][] = $key;
		}

		foreach ($this->aiList->record_list as $key => $value) {
			if (!isset($counterAiListByStatus[$value->field_list['status']->value])){
				$counterAiListByStatus[$value->field_list['status']->value] = 0;
			}
			$counterAiListByStatus[$value->field_list['status']->value]++;
			$this->arrAiByFeatures[$value->field_list['featureId']->value][] = $key;
		}

		foreach ($this->needList->record_list as $key => $value) {
			if (!isset($counterNeedListByStatus[$value->field_list['status']->value])){
				$counterNeedListByStatus[$value->field_list['status']->value] = 0;
			}
			$counterNeedListByStatus[$value->field_list['status']->value]+=$value->field_list['num']->value;
		}

		$needEnum = array(0=>'未确认',1=>'进行中',2=>'完工',3=>'已确认');
		$aiEnum = array(0=>'未设置',1=>'未启动',2=>'准备',3=>'进行中',4=>'完工',5=>'延迟');
		$storyEnum = array('未启动','等待前置','开发中','测试中','已结','移除');

		$this->dataChartStory = array();
		foreach ($storyEnum as $key => $value) {
			if (!isset($counterStoryListByStatus[$key])){
				$counterStoryListByStatus[$key] = 0;
			}
			$this->dataChartStory[] = array("label"=>$value,
					"data"=>$counterStoryListByStatus[$key]);
		}

		$this->dataChartAi = array();
		foreach ($aiEnum as $key => $value) {
			if (!isset($counterAiListByStatus[$key])){
				$counterAiListByStatus[$key] = 0;
			}
			$this->dataChartAi[] = array("label"=>$value,
					"data"=>$counterAiListByStatus[$key]);
		}


		$this->dataChartNeed = array();
		foreach ($needEnum as $key => $value) {
			if (!isset($counterNeedListByStatus[$key])){
				$counterNeedListByStatus[$key] = 0;
			}
			$this->dataChartNeed[] = array("label"=>$value,
					"data"=>$counterNeedListByStatus[$key]);
		}

		$this->template->load('default_page', 'calendar/versionInfo');
	}

	function cartogram($id){
		$data=array();
		$data['versionId']=$id;
		$data['createTS']=time();
		$this->load->model('records/contrast_model',"contrastInfo");

		$needEnum = array(0=>'未确认',1=>'进行中',2=>'完工',3=>'已确认');
		$aiEnum = array(0=>'未设置',1=>'未启动',2=>'准备',3=>'进行中',4=>'完工',5=>'延迟');
		$storyEnum = array('未启动','等待前置','开发中','测试中','已结','移除');

		$this->load->model('lists/Common_list',"storyList");
		$this->storyList->setInfo('pStory','Story_list','Story_model');

		$this->storyList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->storyList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->storyList->load_data_with_where();

		if($this->storyList->record_list!=null){
			$counterStoryListByStatus=array();
			foreach ($this->storyList->record_list as $key => $value) {
				if (!isset($counterStoryListByStatus[$value->field_list['status']->value])){
					$counterStoryListByStatus[$value->field_list['status']->value] = 0;
				}
				$story = 1;
				if ($value->field_list['storyPoint']->value>0){
					$story = $value->field_list['storyPoint']->value;
				}
				$counterStoryListByStatus[$value->field_list['status']->value] +=$story;
				$this->arrStoryByFeatures[$value->field_list['featureId']->value][] = $key;
			}
		}
		$counterStoryListNum=0;
		foreach($storyEnum as $key=>$value){
			if($counterStoryListByStatus[$key]==null){
				$counterStoryListByStatus[$key]=0;
			}else{
				$counterStoryListNum+=$counterStoryListByStatus[$key];
			}
		}

		$this->load->model('lists/Common_list',"aiList");
		$this->aiList->setInfo('pActionitem','Actionitem_list','Actionitem_model');

		$this->aiList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->aiList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->aiList->load_data_with_where();

		if($this->aiList->record_list!=null){
			$counterAiListByStatus=array();
			foreach ($this->aiList->record_list as $key => $value) {
				if (!isset($counterAiListByStatus[$value->field_list['status']->value])){
					$counterAiListByStatus[$value->field_list['status']->value] = 0;
				}
				$counterAiListByStatus[$value->field_list['status']->value]++;
				$this->arrAiByFeatures[$value->field_list['featureId']->value][] = $key;
			}
		}
		$counterAiListNum=0;
		foreach($aiEnum as $key=>$value){
			if($counterAiListByStatus[$key]==null){
				$counterAiListByStatus[$key]=0;
			}else{
				$counterAiListNum+=$counterAiListByStatus[$key];
			}
		}

		$this->load->model('lists/Common_list',"needList");
		$this->needList->setInfo('aNeeds','Needs_list','Needs_model');

		$this->needList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->needList->add_where(WHERE_TYPE_WHERE,'versionId',$id);

		$this->needList->load_data_with_where();
		if($this->needList->record_list!=null){
			$counterNeedListByStatus=array();
			foreach ($this->needList->record_list as $key => $value) {
				if (!isset($counterNeedListByStatus[$value->field_list['status']->value])){
					$counterNeedListByStatus[$value->field_list['status']->value] = 0;
				}
				$counterNeedListByStatus[$value->field_list['status']->value]+=$value->field_list['num']->value;
			}
		}
		$counterNeedListNum=0;
		foreach($needEnum as $key=>$value){
			if($counterNeedListByStatus[$key]==null){
				$counterNeedListByStatus[$key]=0;
			}else{
				$counterNeedListNum+=$counterNeedListByStatus[$key];
			}
		}

		$data['name']='程序总量:'.$counterStoryListNum.'<br>'.$storyEnum[0].':'.$counterStoryListByStatus[0].'/'.$storyEnum[1].':'.$counterStoryListByStatus[1].'/'.$storyEnum[2].':'.$counterStoryListByStatus[2].'/'.$storyEnum[3].':'.$counterStoryListByStatus[3].'/'.$storyEnum[4].':'.$counterStoryListByStatus[4].'/'.$storyEnum[5].':'.$counterStoryListByStatus[5];

		$data['plan']='策划总量:'.$counterAiListNum.'<br>'.$aiEnum[0].':'.$counterAiListByStatus[0].'/'.$aiEnum[1].':'.$counterAiListByStatus[1].'/'.$aiEnum[2].':'.$counterAiListByStatus[2].'/'.$aiEnum[3].':'.$counterAiListByStatus[3].'/'.$aiEnum[4].':'.$counterAiListByStatus[4].'/'.$aiEnum[5].':'.$counterAiListByStatus[5];

		$data['art']='美术总量:'.$counterNeedListNum.'<br>'.$needEnum[0].':'.$counterNeedListByStatus[0].'/'.$needEnum[1].':'.$counterNeedListByStatus[1].'/'.$needEnum[2].':'.$counterNeedListByStatus[2].'/'.$needEnum[3].':'.$counterNeedListByStatus[3];

		$this->contrastInfo->insert_db($data);
		$jsonData = array();
		$this->exportToRefer(1,$jsonData);
	}

	function feature($versionId=""){
		if($this->userInfo->field_list['typ']->value==0||$this->userInfo->field_list['typ']->value==5||$this->userInfo->field_list['typ']->value==6||$this->userInfo->field_list['typ']->value==7||$this->userInfo->field_list['typ']->value==8||$this->userInfo->field_list['typ']->value==9){
			$this->canEdit=false;
		}
		$this->typ = 'feature';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Feature';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('taskId','name','desc','status','dueUser','dueEndTS','endTS','packed');

		$this->versionId = $versionId;
		$this->plusId = $this->versionId;
		$this->load->model('lists/Version_list',"versionList");
		$this->is_lightbox = false;

		$this->versionList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->versionList->add_where(WHERE_TYPE_WHERE,'packed',0);

        $this->versionList->load_data_with_where();
		$this->need_plus = 'calendar/version_list';

		if ($versionId!=""){
			$this->relates['versionId'] = $versionId;
		}
		$this->common_list();
	}

	function featureDetail($featureId="") {
		$this->show_method_name = 'feature';
		$this->login_verify();
		$this->admin_load_menus();

		$this->load->model('records/Feature_model',"featureInfo");
		$this->featureInfo->init_with_id($featureId);

		$this->load->model('lists/Story_list',"storyList");
		$this->storyList->add_where(WHERE_TYPE_WHERE,"featureId",$featureId);
		$this->storyList->listKeys = array('featureId','system','name','desc','status','dueUser','storyPoint','beginTS','dueEndTS','endTS');
		$this->storyList->load_data_with_where();

		$this->load->model('lists/Actionitem_list',"actionitemList");
		$this->actionitemList->add_where(WHERE_TYPE_WHERE,"featureId",$featureId);
		$this->actionitemList->listKeys =  array('versionId','featureId','name','desc','dueUser','priority','status','dueEndTS','endTS');
		$this->actionitemList->load_data_with_where();

		$this->template->load('default_page', 'calendar/info');
	}

	function story($versionId=""){
		if($this->userInfo->field_list['typ']->value==0||$this->userInfo->field_list['typ']->value==5||$this->userInfo->field_list['typ']->value==6||$this->userInfo->field_list['typ']->value==7||$this->userInfo->field_list['typ']->value==8||$this->userInfo->field_list['typ']->value==9){
			$this->canEdit=false;
		}
		$this->typ = 'story';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Story';
		$this->searchKeys = array('name','system','status','dueUser');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('featureId','system','name','desc','status','dueUser','storyPoint','beginTS','dueEndTS','endTS');

		$this->versionId = $versionId;
		$this->plusId = $this->versionId;

		$this->load->model('lists/Version_list',"versionList");

		$this->versionList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->versionList->add_where(WHERE_TYPE_WHERE,'packed',0);

        $this->versionList->load_data_with_where();
		$this->need_plus = 'calendar/version_list';

		if ($versionId!=""){
			$this->relates['versionId'] = $versionId;
		}

		$this->common_list();
	}
	function doSthBeforeShowListPage($typ){
		if($typ=='story'||$typ=='feature'||$typ=='actionitem'){
			$this->needStatus=$this->input->get('needStatus');
			if($this->needStatus===false){
				$this->needStatus=3;
			}
			if($this->needStatus==2){
				$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',4);
			}
			if($this->needStatus==3){
				$this->listInfo->add_where(WHERE_TYPE_WHERE_NE,'status',4);
			}
		}
	}
	function storyByWeek($weekId=""){
		if($this->userInfo->field_list['typ']->value==0||$this->userInfo->field_list['typ']->value==5||$this->userInfo->field_list['typ']->value==6||$this->userInfo->field_list['typ']->value==7||$this->userInfo->field_list['typ']->value==8||$this->userInfo->field_list['typ']->value==9){
			$this->canEdit=false;
		}
		$this->typ = 'story';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Story';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('versionId','featureId','system','name','desc','status','dueUser','storyPoint','beginTS','dueEndTS','endTS');

		$this->weekId = $weekId;
		$this->load->model('lists/Workingweek_list',"weekList");
		$this->weekList->add_where(WHERE_TYPE_WHERE,'packed',0);

        $this->weekList->load_data_with_where();
		$this->need_plus = 'calendar/week_list';

		if ($weekId!=""){
			$this->relates['weekId'] = $weekId;
		}

		$this->common_list();
	}

	function actionitem($versionId=""){
		if($this->userInfo->field_list['typ']->value==0||$this->userInfo->field_list['typ']->value==5||$this->userInfo->field_list['typ']->value==6||$this->userInfo->field_list['typ']->value==7||$this->userInfo->field_list['typ']->value==8||$this->userInfo->field_list['typ']->value==9){
			$this->canEdit=false;
		}
		$this->typ = 'actionitem';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Actionitem';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('versionId','featureId','name','desc','dueUser','priority','status','dueEndTS','endTS');
		$this->orderKey = array('status'=>'desc','priority'=>'desc');
		// $this->need_plus = 'abiaozhun/baoyang_plus';
		$this->versionId = $versionId;
		$this->plusId = $this->versionId;

		$this->load->model('lists/Version_list',"versionList");

		$this->versionList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		$this->versionList->add_where(WHERE_TYPE_WHERE,'packed',0);
        $this->versionList->load_data_with_where();
		$this->need_plus = 'calendar/version_list';

		if ($versionId!=""){
			$this->relates['versionId'] = $versionId;
		}

		$this->common_list();
	}

	function changelog(){

        $this->admin_load_menus();
        $this->load->model('lists/Changelog_list',"listInfo");
		$this->canEdit=false;
		$this->listInfo->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->listInfo->load_data_with_where();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/changelog/";

        $this->template->load('default_page', 'common/list_view');
    }



	function actionitemByWeek($weekId=""){
		if($this->userInfo->field_list['typ']->value==0||$this->userInfo->field_list['typ']->value==5||$this->userInfo->field_list['typ']->value==6||$this->userInfo->field_list['typ']->value==7||$this->userInfo->field_list['typ']->value==8||$this->userInfo->field_list['typ']->value==9){
			$this->canEdit=false;
		}
		$this->typ = 'actionitem';
		$this->dataModelPrefix = 'p';

		$this->dataModelName = 'Actionitem';
		$this->searchKeys = array('name','desc');
		$this->quickSearchKeys = array('name','desc');
		$this->listKeys = array('versionId','featureId','name','desc','dueUser','priority','status','progress','dueEndTS','endTS');
		$this->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

		$this->weekId = $weekId;
		$this->load->model('lists/Workingweek_list',"weekList");
		$this->weekList->add_where(WHERE_TYPE_WHERE,'packed',0);

        $this->weekList->load_data_with_where();
		$this->need_plus = 'calendar/week_list';

		if ($weekId!=""){
			$this->relates['weekId'] = $weekId;
		}

		$this->common_list();
	}

	function featureInfo($featureId,$sub_menu="feature"){
		$this->admin_load_menus();
		$this->now_sub_menu = $sub_menu;

		$this->sub_menus = array(
			"feature"=>array("name"=>"功能列表","show"=>true),
            "dev"=>array("name"=>"开发内容","show"=>true),
			"ai"=>array("name"=>"待办事项","show"=>true),
            "diary"=>array("name"=>"日报","show"=>true),
            "weekly"=>array("name"=>"周报","show"=>true),
			"changelog"=>array("name"=>"每日进展","show"=>true)
            // "send"=>array("name"=>"留言反馈"),
        );

		if (isset($this->sub_menus[$sub_menu])){
            $this->now_sub_menu = $sub_menu;
        } else {
            $this->now_sub_menu = "feature";
        }

		$this->load->model('records/feature_model',"featureModel");
		$this->featureModel->init_with_id($featureId);


	}

	function week($sub_menu="feature"){
		$this->canEditFeature=($this->userInfo->field_list['typ']->value==1||$this->userInfo->field_list['typ']->value==2||$this->userInfo->field_list['typ']->value==3||$this->userInfo->field_list['typ']->value==4||$this->userInfo->field_list['typ']->value==100);
		$this->admin_load_menus();
		$this->now_sub_menu = $sub_menu;
		// $this->load->model('records/Workingweek_model',"currentWeekModel");
		// if ($weekId==""){
		// 	$this->currentWeekModel->init_with_where(array('isCurrent'=>1));
		// } else {
		// 	$this->currentWeekModel->init_with_id($weekId);
		// }
		//
		// if (!$this->currentWeekModel->is_inited){
		// 	$this->template->load('default_page', 'calendar/no_woringweek');
		// 	return;
		// }
		//
		// $this->currentWeekId = $this->currentWeekModel->id;

		//
		// $this->beginTS = $this->currentWeekModel->field_list['beginTS']->formatTSAsDayBeginTS();
		// $this->endTS = $this->currentWeekModel->field_list['endTS']->formatTSAsDayEndTS();


		$this->beginTS = $this->utility->getTS('beginThisWeek');
		$this->endTS = $this->utility->getTS('endThisWeek');
		// var_dump($this->beginTS,$this->endTS,date('Y-m-d H:i:s',$this->beginTS),date('Y-m-d H:i:s',$this->endTS));
		// exit;

		$this->sub_menus = array(
			"feature"=>array("name"=>"功能列表","show"=>true),
            "dev"=>array("name"=>"开发内容","show"=>true),
			"ai"=>array("name"=>"待办事项","show"=>true),
            "diary"=>array("name"=>"日报","show"=>true),
            "weekly"=>array("name"=>"周报","show"=>true),
			"changelog"=>array("name"=>"每日进展","show"=>true)
            // "send"=>array("name"=>"留言反馈"),
        );


        if (isset($this->sub_menus[$sub_menu])){
            $this->now_sub_menu = $sub_menu;
        } else {
            $this->now_sub_menu = "feature";
        }

		$this->load->model('lists/Feature_list',"featureList");


		$selectArr = array(
			'$and'=>array(
				array('projectId'=>$this->userInfo->field_list['projectId']->value),
				array('$or'=>
						array(
							array('status'=>array('$ne'=>4)),
							array('status'=>4,'dueEndTS'=>array('$gte'=>$this->beginTS)),
							array('status'=>4,'endTS'=>array('$gte'=>$this->beginTS)),
						)
					)
			)
		);
		$this->featureList->load_data_with_orignal_where($selectArr);
//		$this->field_list['status']->setEnum(array('思路','计划内','开发中','测试中','已结'));
		$this->arrFeaturesByStatus = array(2=>array(),5=>array(),3=>array(),1=>array(),4=>array(),0=>array());
		$this->arrStoryByFeatures = array();
		$this->arrAiByFeatures = array();

		foreach ($this->featureList->record_list as $key => $value) {
			$this->arrFeaturesByStatus[$value->field_list['status']->value][] = $value;
		}


		$this->load->model('lists/Common_list',"storyList");
        $this->storyList->setInfo('pStory','Story_list','Story_model');

		// $this->storyList->add_where(WHERE_TYPE_WHERE,'weekId',$this->currentWeekId);
		// $this->storyList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->storyList->orderKey = array('featureId'=>'asc','dueEndTS'=>'asc','status'=>'desc','priority'=>'desc');
		$this->storyList->listKeys = array('versionId','featureId','system','name','desc','status','dueUser','beginTS','dueEndTS','endTS');

		$selectArr = array();
		$selectArr[] = array('projectId' =>  $this->userInfo->field_list['projectId']->value);

		$selectArr[] = array('$or'=> array(
					//开始时间在周期内
					array("beginTS"=>
						array('$gte'=>$this->beginTS,'$lt'=>$this->endTS)),
					//已开始未结
					array("status"=>
							array('$in'=>array(1,2,3))),
					//预期结束时间在周期内
					array("dueEndTS"=>
						array('$gte'=>$this->beginTS,'$lt'=>$this->endTS)),
					//结束时间在周期内
					array("endTS"=>
						array('$gte'=>$this->beginTS,'$lt'=>$this->endTS)),
					//跨周期
					array("beginTS"=>
						array('$gte'=>$this->beginTS),
						"dueEndTS"=>
						array('$gt'=>$this->endTS)),
				));
		$this->storyList->load_data_with_orignal_where(array('$and'=>$selectArr));



		$this->load->model('lists/Common_list',"aiList");
        $this->aiList->setInfo('pActionitem','Actionitem_list','Actionitem_model');
		// $this->aiList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		// $this->aiList->add_where(WHERE_TYPE_WHERE,'weekId',$this->currentWeekId);

		$this->aiList->orderKey = array('featureId'=>'asc','endTS'=>'asc','status'=>'desc','priority'=>'desc');
		$this->aiList->listKeys = array('versionId','featureId','name','desc','dueUser','priority','status','dueEndTS','endTS');

		$selectArr = array();
		$selectArr[] = array('projectId' =>  $this->userInfo->field_list['projectId']->value);

		$selectArr[] = array('$or'=> array(
					//开始时间在周期内
					array("beginTS"=>
						array('$gte'=>$this->beginTS,'$lt'=>$this->endTS)),
					//已开始未结
					array("status"=>
							array('$in'=>array(0,1,2,3))),
					//预期结束时间在周期内
					array("dueEndTS"=>
						array('$gte'=>$this->beginTS,'$lt'=>$this->endTS)),
					//结束时间在周期内
					array("endTS"=>
						array('$gte'=>$this->beginTS,'$lt'=>$this->endTS)),
					//跨周期
					array("beginTS"=>
						array('$lt'=>$this->beginTS),
						"dueEndTS"=>
						array('$gte'=>$this->endTS)),
				));
		$this->aiList->load_data_with_orignal_where(array('$and'=>$selectArr));

		foreach ($this->storyList->record_list as $key => $value) {
			$this->arrStoryByFeatures[$value->field_list['featureId']->value][] = $key;
		}

		foreach ($this->aiList->record_list as $key => $value) {
			$this->arrAiByFeatures[$value->field_list['featureId']->value][] = $key;
		}


		$this->load->model('lists/Changelog_list',"diaryList");
		$this->diaryList->add_where(WHERE_TYPE_WHERE_GT,'beginTS',$this->utility->getTS("beginToday"));
		$this->diaryList->add_where(WHERE_TYPE_WHERE_LT,'beginTS',$this->utility->getTS("endToday"));
		$this->diaryList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->diaryList->load_data_with_where();



		$this->load->model('lists/Changelog_list',"changelogList");
		$this->changelogList->add_where(WHERE_TYPE_WHERE_GT,'beginTS',$this->beginTS);
		$this->changelogList->add_where(WHERE_TYPE_WHERE_LT,'beginTS',$this->endTS);
		$this->changelogList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->changelogList->load_data_with_where();

		$this->storyChangLog = array();
		$this->aiChangLog = array();
		$this->featureChangLog = array();

		foreach ($this->changelogList->record_list as $key => $value) {
			$relate_turple = $value->field_list['relate_turple'];
			if ($relate_turple->relate_typ==1){
				if (!isset($this->storyChangLog[$relate_turple->relate_id])){
					$this->storyChangLog[$relate_turple->relate_id] = array();
				}
				$this->storyChangLog[$relate_turple->relate_id][] = $value;
			} else if ($relate_turple->relate_typ==3){
				if (!isset($this->aiChangLog[$relate_turple->relate_id])){
					$this->aiChangLog[$relate_turple->relate_id] = array();
				}
				$this->aiChangLog[$relate_turple->relate_id][] = $value;
			} else if ($relate_turple->relate_typ==2){
				if (!isset($this->featureChangLog[$relate_turple->relate_id])){
					$this->featureChangLog[$relate_turple->relate_id] = array();
				}
				$this->featureChangLog[$relate_turple->relate_id][] = $value;
			}
		}

		$this->template->load('default_page', 'calendar/week');
	}

	function doSthBeforeInsert($typ,$data){
		switch ($typ) {
			case 'story':
			case 'actionitem':
				//根据feature自动更新版本
				$featureId = $data['featureId'];
				$this->load->model('records/feature_model',"featureModel");
				$this->featureModel->init_with_id($featureId);
				$data['versionId'] = $this->featureModel->field_list['versionId']->value;

				return $data;
				break;

			default:
				return $data;
				break;
		}
	}

	function doSthAfterInsert($typ,$data,$newId){
		$config_null_weekId = '55c31052643fa6ae31c025af';
		$newId = (string)$newId;
		switch ($typ) {
			case 'feature':
				$this->load->model('records/actionitem_model',"aiDataModel");
				$this->load->model('records/story_model',"storyDataModel");

				$new_data = array();

				$new_data['name'] = '策划案';
				$new_data['desc'] =  '';
				$new_data['solution'] =  '';
				$new_data['priority'] = 2;
				$new_data['status'] = 0;

				$new_data['projectId'] = $data['projectId'];
				$new_data['versionId'] = $data['versionId'];
				$new_data['featureId'] = $newId;
				// $new_data['weekId'] = $config_null_weekId;

				$new_data['dueUser'] = $data['dueUser'];


				$this->aiDataModel->insert_db($new_data);

				if ($data['hasArt']==1){
					//Actionitem 增加 UI 跟进
					//Story 增加 陪表
					$new_data = array();

					$new_data['name'] = '美术需求';
			        $new_data['desc'] =  '';
			        $new_data['solution'] =  '';
			        $new_data['priority'] = 2;
			        $new_data['status'] = 0;

			        $new_data['projectId'] = $data['projectId'];

			        $new_data['versionId'] = $data['versionId'];

			        $new_data['featureId'] = $newId;
					// $new_data['weekId'] = $config_null_weekId;

			        $new_data['dueUser'] = $data['dueUser'];


					$this->aiDataModel->insert_db($new_data);
				}
				if ($data['hasUI']==1){
					//Actionitem 增加 UI 跟进
					//Story 增加 陪表
					$new_data = array();

					$new_data['name'] = '跟进UI';
			        $new_data['desc'] =  '';
			        $new_data['solution'] =  '';
			        $new_data['priority'] = 2;
			        $new_data['status'] = 0;

			        $new_data['projectId'] = $data['projectId'];

			        $new_data['versionId'] = $data['versionId'];

			        $new_data['featureId'] = $newId;
					// $new_data['weekId'] = $config_null_weekId;

			        $new_data['dueUser'] = $data['dueUser'];


					$this->aiDataModel->insert_db($new_data);
				}
				if ($data['hasExcel']==1){
					//Story 增加 陪表

					$new_data = array();

					$new_data['name'] = '配表';
			        $new_data['desc'] =  '';
			        $new_data['solution'] =  '';
			        $new_data['priority'] = 2;
			        $new_data['status'] = 0;

			        $new_data['projectId'] = $data['projectId'];

			        $new_data['versionId'] = $data['versionId'];

			        $new_data['featureId'] = $newId;
					// $new_data['weekId'] = $config_null_weekId;

			        $new_data['dueUser'] = $data['dueUser'];


					$this->aiDataModel->insert_db($new_data);
				}
				if ($data['hasCode']==1){
					//Story 增加 UI 跟进
					//Story 增加 陪表
					$new_data = array();

					$new_data['name'] = '';
					$new_data['desc'] =  '';
					$new_data['solution'] =  '';
					$new_data['priority'] = 0;
					$new_data['system'] = 0;
					$new_data['status'] = 0;

					$new_data['projectId'] = $data['projectId'];

					$new_data['versionId'] = $data['versionId'];

					$new_data['featureId'] = $newId;
					// $new_data['weekId'] = $config_null_weekId;


					$new_data['dueUser'] = '';

					$new_data['storyPoint'] = 0;

					$this->storyDataModel->insert_db($new_data);

					$new_data['system'] = 1;
					$this->storyDataModel->insert_db($new_data);

				}
				break;

			default:
				# code...
				break;
		}
	}

	function doSthBeforeUpdate($typ,$data,$id){
		switch ($typ) {

			case 'story':
			case 'actionitem':
				//根据feature自动更新版本
				if (isset($data['featureId'])){
					$featureId = $data['featureId'];
					$this->load->model('records/feature_model',"featureModel");
					$this->featureModel->init_with_id($featureId);
					$data['versionId'] = $this->featureModel->field_list['versionId']->value;
				}
				if (isset($data['status']) && $data['status']==4){
					if (!isset($data['endTS'])){
						$data['endTS'] = time();
					}
				}
				break;
			default:
				break;
		}
		return $data;
	}

	function doSthAfterUpdate($typ,$data,$id){
		switch ($typ) {
			case 'feature':
				//根据feature自动更新版本
				if (isset($data['versionId'])){
					$versionId = $data['versionId'];
					$this->load->model('records/actionitem_model',"aiDataModel");
					$this->load->model('records/story_model',"storyDataModel");
					$data = array('versionId'=>$versionId);

					$this->aiDataModel->update_db_by_where($data,array('featureId'=>$id));
					$this->storyDataModel->update_db_by_where($data,array('featureId'=>$id));
				}
				break;

			default:
				# code...
				break;
		}
	}


	function doSthAfterDelete($typ,$id){
		switch ($typ) {
			case 'feature':
				$this->load->model('records/actionitem_model',"aiDataModel");
				$this->load->model('records/story_model',"storyDataModel");
				$this->storyDataModel->delete_db_where(array('featureId'=>$id));
				$this->aiDataModel->delete_db_where(array('featureId'=>$id));

				break;
			default:
				break;
		}
	}

	function calList(){
		$start = $this->input->get('start');
		$end = $this->input->get('end');

		$cldMyStory = ($this->input->get('cldMyStory')=="true");
		$cldMyActionitem = ($this->input->get('cldMyActionitem')=="true");
		$cldMyNeeds =  ($this->input->get('cldMyNeeds')=="true");

// var_dump($cldMyStory);
// var_dump($cldMyActionitem);
// var_dump($cldMyNeeds);

		$this->load->model('lists/Common_list',"storyList");
        $this->storyList->setInfo('pStory','Story_list','Story_model');
		// $this->storyList->add_where(WHERE_TYPE_WHERE_LT,'endTS',$end);
		// $this->storyList->add_where(WHERE_TYPE_WHERE_GT,'endTS',$start);

		$this->storyList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->storyList->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

        $this->storyList->load_data_with_where();

		$this->load->model('lists/Common_list',"aiList");
        $this->aiList->setInfo('pActionitem','Actionitem_list','Actionitem_model');
		$this->aiList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		// $this->aiList->add_where(WHERE_TYPE_WHERE_LT,'endTS',$end);
		// $this->aiList->add_where(WHERE_TYPE_WHERE_GT,'endTS',$start);

		$this->aiList->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

        $this->aiList->load_data_with_where();

		$this->load->model('lists/Needs_list',"needsList");
		$this->needsList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);

		$this->needsList->load_data_with_where();

		$events = array();
        $i = 0;
		if($cldMyStory){
		// $this->field_list['status']->setEnum(array('未启动','等待前置','开发中','测试中','已结'));
		$colors = array('#777','#d9534f','#337ab7','#5bc0de','#5cb85c','#777');
        foreach($this->storyList->record_list as  $this_record) {
            $events[] = array(
                        "id"=>$this_record->id,
						"typ"=>'story',
                        "title"=>
							$this_record->field_list['system']->gen_show_value().'开发'.$this_record->field_list['name']->value.
							" @ ".$this_record->field_list['featureId']->gen_show_value().
							" by ".$this_record->field_list['dueUser']->gen_show_value(),

                        "start"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
                        "allDay"=>true,
                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
                //         start: new Date(y, m, d - 5),
                //         end: new Date(y, m, d - 2),
                //         backgroundColor: layoutColorCodes['green']
                );
            $i++;
        }
		}

		if($cldMyActionitem){
		// $this->field_list['status']->setEnum(array(0=>'未设置',1=>'未启动',2=>'准备',3=>'进行中',4=>'完工'));

		$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

		foreach($this->aiList->record_list as  $this_record) {
            $events[] = array(
                        "id"=>$this_record->id,
						"typ"=>'actionitem',

                        "title"=>$this_record->field_list['name']->value." @ ".$this_record->field_list['featureId']->gen_show_value().
						" by ".$this_record->field_list['dueUser']->gen_show_value(),

                        "start"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
                        "allDay"=>true,
                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
                //         start: new Date(y, m, d - 5),
                //         end: new Date(y, m, d - 2),
                //         backgroundColor: layoutColorCodes['green']
                );
            $i++;
        }
		}

		if($cldMyNeeds){
		$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

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
		}


        echo json_encode($events);
	}

	public function calListPeaple($reporterUid=""){
		$start = $this->input->get('start');
		$end = $this->input->get('end');


		$this->load->model('lists/Common_list',"storyList");
        $this->storyList->setInfo('pStory','Story_list','Story_model');
		// $this->storyList->add_where(WHERE_TYPE_WHERE_LT,'endTS',$end);
		// $this->storyList->add_where(WHERE_TYPE_WHERE_GT,'endTS',$start);

		$this->storyList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		if ($reporterUid!=""){
			$this->storyList->add_where(WHERE_TYPE_WHERE,'dueUser',$reporterUid);
		}
		$this->storyList->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

        $this->storyList->load_data_with_where();

		$this->load->model('lists/Common_list',"aiList");
        $this->aiList->setInfo('pActionitem','Actionitem_list','Actionitem_model');
		$this->aiList->add_where(WHERE_TYPE_WHERE,'projectId',$this->userInfo->field_list['projectId']->value);
		if ($reporterUid!=""){
			$this->aiList->add_where(WHERE_TYPE_WHERE,'dueUser',$reporterUid);
		}
		// $this->aiList->add_where(WHERE_TYPE_WHERE_LT,'endTS',$end);
		// $this->aiList->add_where(WHERE_TYPE_WHERE_GT,'endTS',$start);

		$this->aiList->orderKey = array('endTS'=>'asc','status'=>'desc','priority'=>'desc');

        $this->aiList->load_data_with_where();



		$events = array();
        $i = 0;
		// $this->field_list['status']->setEnum(array('未启动','等待前置','开发中','测试中','已结'));
		$colors = array('#777','#d9534f','#337ab7','#5bc0de','#5cb85c','#777');
        foreach($this->storyList->record_list as  $this_record) {
            $events[] = array(
                        "id"=>$this_record->id,
						"typ"=>'story',
                        "title"=>
							$this_record->field_list['system']->gen_show_value().'开发'.$this_record->field_list['name']->value.
							" @ ".$this_record->field_list['featureId']->gen_show_value().
							" by ".$this_record->field_list['dueUser']->gen_show_value(),

                        "start"=>($this_record->field_list['beginTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['beginTS']->value,
                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
                        "allDay"=>true,
                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
                //         start: new Date(y, m, d - 5),
                //         end: new Date(y, m, d - 2),
                //         backgroundColor: layoutColorCodes['green']
                );
            $i++;
        }

		// $this->field_list['status']->setEnum(array(0=>'未设置',1=>'未启动',2=>'准备',3=>'进行中',4=>'完工'));

		$colors = array('#777','#777','#f0ad4e','#337ab7','#5cb85c','#d9534f');

		foreach($this->aiList->record_list as  $this_record) {
            $events[] = array(
                        "id"=>$this_record->id,
						"typ"=>'actionitem',

                        "title"=>$this_record->field_list['name']->value." @ ".$this_record->field_list['featureId']->gen_show_value().
						" by ".$this_record->field_list['dueUser']->gen_show_value(),

                        "start"=>($this_record->field_list['beginTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['beginTS']->value,
                        "end"=>($this_record->field_list['endTS']->isEmpty())?$this_record->field_list['dueEndTS']->value:$this_record->field_list['endTS']->value,
                        "allDay"=>true,
                        "backgroundColor"=>$colors[$this_record->field_list['status']->value]
                //         start: new Date(y, m, d - 5),
                //         end: new Date(y, m, d - 2),
                //         backgroundColor: layoutColorCodes['green']
                );
            $i++;
        }

        echo json_encode($events);
	}

	function doSthBeforeShowUpdatePage($typ,$id){

		switch ($typ) {
			case 'task':
				if ($this->dataInfo->field_list['createUid']->value!=$this->userInfo->id){
					//不是我发的任务，编辑字段少很多
					$this->dataInfo->changeShowFields = array(
			                array('status','progress'),
			                array('endTS'),
			                array('desc')
			        );

				}
				# code...
				break;
			case 'story':
				if ($this->userInfo->field_list['typ']->value==2||$this->userInfo->field_list['typ']->value==3&&$this->dataInfo->field_list['dueUser']->value!=$this->userInfo->id){
					$this->dataInfo->changeShowFields = array(
						array('status','null'),
						array('beginTS','endTS'),
						array('desc'),
					);
				}else if($this->userInfo->field_list['typ']->value==2||$this->userInfo->field_list['typ']->value==3&&$this->dataInfo->field_list['dueUser']->value==$this->userInfo->id){
					$this->dataInfo->changeShowFields = array(
						array('status','dueUser'),
						array('beginTS','endTS'),
						array('desc'),
					);
				}
				break;
			case 'actionitem':
				if ($this->userInfo->field_list['typ']->value==2||$this->userInfo->field_list['typ']->value==3&&$this->dataInfo->field_list['dueUser']->value!=$this->userInfo->id){
					$this->dataInfo->changeShowFields = array(
		                array('status','dueEndTS'),
		                array('beginTS','endTS'),
						array('dueUser','null'),

		                array('desc'),
					);
				}else if($this->userInfo->field_list['typ']->value==2||$this->userInfo->field_list['typ']->value==3&&$this->dataInfo->field_list['dueUser']->value==$this->userInfo->id){
					$this->dataInfo->changeShowFields = array(
						array('status','dueEndTS'),
		                array('beginTS','endTS'),
						array('dueUser','null'),

		                array('desc'),
					);
				}
				break;

			default:
				# code...
				break;
		}
	}
	function createPresent($typ,$featureId){
		$this->setViewType(VIEW_TYPE_HTML);
		$modelName = 'records/'.(ucfirst($typ)).'_model';

		$this->load->model($modelName,"dataInfo");
		$this->title_create = $this->dataInfo->title_create;

		$this->createUrlC = $this->controller_name;
		$this->createUrlF = 'doCreatePresent/'.$typ.'/'.$featureId;

		if (isset($this->related_field) && $this->related_field!=''){
			if ($id!=""){
				$this->dataInfo->field_list[$this->related_field]->init($id);
				$this->related_id = $id;
			} else {
				$this->related_field = "";
			}
		}
		$this->createPostFields = $this->dataInfo->buildChangeNeedFields();

		if($typ=="actionitem"){
	        $this->modifyNeedFields = array(
	                array('versionId'),
	                array('name','priority'),
	                array('status','dueUser'),

	                array('beginTS','null'),
	                array('dueEndTS','endTS'),
	                array('desc'),
	            );
		}
		else if($typ=="story"){
			$this->modifyNeedFields = array(
                array('name','null'),

                array('system','priority'),
                array('dueUser','null'),


                array('status','storyPoint'),
                array('beginTS','null'),
                array('dueEndTS','endTS'),
                array('desc'),
			);
		}
		$this->editor_typ = 0;
		$this->template->load('default_lightbox_new', 'common/create_related');
	}
	function doCreatePresent($typ,$featureId){
        $this->setViewType(VIEW_TYPE_JSON);

		$modelName = 'records/'.(ucfirst($typ)).'_model';

        $jsonRst = 1;
        $zeit = time();

        $this->load->model($modelName,"dataInfo");
        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $data = array();
        foreach ($this->createPostFields as $value) {
            $data[$value] = $this->dataInfo->field_list[$value]->gen_value($this->input->post($value));
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

		// if($typ=="actionitem"){
	        $data['featureId']=$featureId;
		// }
		// if($typ="story"){
		//
		// }

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
}
