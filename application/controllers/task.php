<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Task extends common {
	function __construct() {
		parent::__construct(true,'a');
		$this->load->library('pagination');
        $this->show_controller_name = 'index';

	}
	function mytext() {
		$this->login_verify();
		$this->admin_load_menus();
		$this->load->model('lists/Mytext_list',"listInfo");
		$this->buildSearch();
		$this->listInfo->build_where_with_search($this->searchInfo);

		$this->listInfo->add_where(WHERE_TYPE_WHERE,'createUid',$this->userInfo->id);
		$this->listInfo->load_data_with_where();

		$this->create_link =  $this->controller_name . "/create/mytext/";

		$this->template->load('default_page', 'common/list_view');
	}
	function managerweek(){
		$this->typ ="managerweek";
		$this->dataModelPrefix = 's';
		$this->dataModelName = 'Managerweek';
		$this->listKeys = array('confTS','beginTS','endTS');

		$this->hasSearch = false;

		$this->is_lightbox = false;
		$this->need_plus = 'conf/all_conf';
		$this->common_list();
	}

	function confitem(){
		$this->show_method_name = 'managerweek';

		$this->admin_load_menus();

		$this->typ ="confitem";
		$this->load->model('lists/Confitem_list',"listInfo");

        $this->listInfo->paged = true;
        $this->listInfo->perPage = 20;
        $this->listInfo->limit = $this->listInfo->perPage;
        $this->listInfo->nowPage = $this->pagination->get_tough_page();

		$this->listInfo->add_where(WHERE_TYPE_WHERE,'createUid',$this->userInfo->id);

		$this->listInfo->load_data_with_where();

        $this->perPage = 20;
        $config['per_page'] = $this->perPage;
        $config['base_url'] = site_url($this->controller_name.'/'.$this->method_name.'/').'/';

        $this->pagination->initialize($config);
        $this->cur_page = $this->pagination->get_cur_page();

        $this->listInfo->is_lightbox = true;

        $this->create_link =  $this->controller_name . "/create/confitem/";
		$this->need_plus = 'conf/all_conf';

        $this->template->load('default_page', 'common/list_view');
	}

	function create($typ,$id=""){
		if ($typ=="task" && $id!=""){
			$this->related_field = 'parentTaskId';
			$this->relateId = $id;
		}
		parent::create($typ,$id);
	}

	function edit($typ,$id){
		$this->send_hack = false;
		if ($typ=="task" && $id!=""){
			$this->related_field = 'parentTaskId';
			$this->relateId = $id;
		} else if ($typ == "sendtask"){
			$this->send_hack = true;
			$typ = "task";
		} else {

		}
		parent::edit($typ,$id);
	}
	function editTaskParent($taskId){
		$this->setViewType(VIEW_TYPE_HTML);

        $this->createUrlC = $this->controller_name;
        $this->createUrlF = 'doUpdate/task';

        $this->load->model('records/Task_model',"dataInfo");

        $this->dataInfo->init_with_id($taskId);


        $this->createPostFields = $this->dataInfo->buildChangeNeedFields();
        $this->modifyNeedFields = array(array('parentTaskId'),);

        $this->editor_typ = 1;
        $this->title_create = $this->dataInfo->gen_editor_title();

        $this->template->load('default_lightbox_edit', 'common/create_related');
	}
	function pushtask($status=-1) {
		$this->admin_load_menus();
		$this->create_link = 'task/create/task/';
        $this->hasSearch = true;
		$this->buildSearch();

		$this->load->model('lists/Task_list',"listInfo");
		$this->listInfo->add_where(WHERE_TYPE_WHERE,"createUid",$this->userInfo->id);
		$this->listInfo->build_where_with_search($this->searchInfo);
		
		$this->status=$status;
		if($status!=-1){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,'status',(int)$status);
		}
		$this->listInfo->load_data_with_where();

		$this->need_plus = 'task/status_list';

		$this->template->load('default_page', 'common/list_view');
	}

	function duetask($typ="task") {
		$this->admin_load_menus();
		$this->create_link = 'task/create/task/';
        $this->hasSearch = false;
        $this->canCreate = false;

		$this->typ = $typ;
		$this->taskType=$this->input->get('taskType');
		if($this->taskType===false){
			$this->taskType=3;
		}
		$this->need_plus = 'task/due_list';

		switch ($typ) {
			case 'task':
				$this->load->model('lists/Task_list',"listInfo");
				break;
			case 'story':
				$this->load->model('lists/Common_list',"listInfo");
				$this->listInfo->setInfo('pStory','Story_list','Story_model');
				$this->listInfo->listKeys = array('projectId','versionId','featureId','system','name','desc','status','dueUser','storyPoint','beginTS','endTS');
				break;
			case 'actionitem':
				$this->load->model('lists/Common_list',"listInfo");
				$this->listInfo->setInfo('pActionitem','Actionitem_list','Actionitem_model');
				$this->listInfo->listKeys = array('projectId','versionId','featureId','name','desc','dueUser','priority','status','priority','endTS');
				break;
			case 'relatedTask':
				$this->load->model('lists/Task_list',"listInfo");
				$this->listInfo->add_where(WHERE_TYPE_LIKE,"relatedUsers",$this->userInfo->id);
				break;
			default:
				# code...
				break;
		}
		if($this->taskType==2){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,"status",4);
		}
		if($this->taskType==3){
			$this->listInfo->add_where(WHERE_TYPE_WHERE_NE,"status",4);
		}

		if($typ!='relatedTask'){
			$this->listInfo->add_where(WHERE_TYPE_WHERE,"dueUser",$this->userInfo->id);
		}
		$this->listInfo->load_data_with_where();
        $this->listInfo->op_limit = "due";

		$this->template->load('default_page', 'common/list_view');
	}

	function reportertask($reporterUid = ""){
		$this->admin_load_menus();
		$this->hasSearch = false;
        $this->canCreate = false;

		$this->reporterUid = $reporterUid;
		$this->need_plus = 'task/reporter_list';

		$this->load->model('lists/User_list',"reporterList");
		$this->reporterList->add_where(WHERE_TYPE_WHERE,"reportTo",$this->userInfo->id);
		$this->reporterList->load_data_with_where();
		$uids = $this->reporterList->gen_id_array();

		if (!in_array($this->reporterUid,$uids)){
			$this->reporterUid = "";
		}

		$this->load->model('lists/Task_list',"listInfo");


		$this->load->model('records/Task_model',"taskInfo");
		$this->status=-1;
		$this->beginTS=0;
		$this->endTS=0;
		$this->begin="";
		$this->end="";
		$status=$this->input->get('s');
		if($status!==false){
			$this->status=$status;
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


		$selectArr = array();

		switch ($this->status) {
			case '-1':
				break;
			default:
				// $this->listInfo->add_where(WHERE_TYPE_WHERE,'status',$status);
				$selectArr[] = array('status' =>  $status);
				break;
		}
		if ($this->reporterUid==""){
			// $this->listInfo->add_where(WHERE_TYPE_IN,"dueUser",$uids);
			$selectArr[] = array("dueUser"=> array('$in'=>$uids));

		} else {
			$selectArr[] = array('$or'=> array(
						array("dueUser"=>$this->reporterUid),
						array("createUid"=>$this->reporterUid),
					));
			// $this->listInfo->add_where(WHERE_TYPE_OR_WHERE,"dueUser",$this->reporterUid);
			// $this->listInfo->add_where(WHERE_TYPE_OR_WHERE,"createUid",$this->reporterUid);

		}

		if ($this->beginTS!=0 && $this->endTS!=0){
			$selectArr[] = array('$or'=> array(
						array("beginTS"=>
							array('$gt'=>$this->beginTS,'$lt'=>$this->endTS)),
						array("dueEndTS"=>
							array('$gt'=>$this->beginTS,'$lt'=>$this->endTS)),
						array("beginTS"=>
							array('$lt'=>$this->beginTS),
							"dueEndTS"=>
							array('$gt'=>$this->endTS)),
					));
		}

		$this->listInfo->load_data_with_orignal_where(array('$and'=>$selectArr));

        $this->listInfo->op_limit = "due";

		$this->template->load('default_page', 'common/list_view');
	}

	function doLook($taskId){
		$this->load->model('records/Task_model',"taskInfo");
		$this->taskInfo->init_with_id($taskId);
		if(!in_array($this->userInfo->id,$this->taskInfo->field_list['relatedUsers']->value)&&$this->taskInfo->field_list['createUid']->value!=$this->userInfo->id&&$this->taskInfo->field_list['dueUser']->value!=$this->userInfo->id){
			$data['relatedUsers']=$this->taskInfo->field_list['relatedUsers']->value;
			$data['relatedUsers'][]=$this->userInfo->id;
			// var_dump($data['relatedUsers']);exit;
			$this->taskInfo->update_db($data);
			$jsonData = array();
	        $this->exportToRefer(1,$jsonData);
		}
	}

	function managerweekChatList($managerweekId,$sub_menu="index"){
		$this->login_verify();
		$this->show_method_name = 'managerweek';
		$this->admin_load_menus();

		$this->load->model('records/Managerweek_model',"confInfo");
		$this->confInfo->init_with_id($managerweekId);


		$this->sub_menus = array(
            "index"=>array("name"=>"上周回顾","show"=>true,"method"=>'managerweekInfo'),
			"next"=>array("name"=>"下周安排","show"=>true,"method"=>'managerweekInfo'),
			"chat"=>array("name"=>"需讨论","show"=>true,"method"=>'managerweekChatList'),
        );

        if (isset($this->sub_menus[$sub_menu])){
            $this->now_sub_menu = $sub_menu;
        } else {
            $this->now_sub_menu = "index";
        }


		$this->beginTS = $this->confInfo->field_list['beginTS']->formatTSAsDayBeginTS();
		$this->endTS = $this->confInfo->field_list['endTS']->formatTSAsDayEndTS();

		$this->load->model('lists/Confitem_list',"confItemList");
		$this->confItemList->orderKey = array('createTS'=>'asc');
		$this->confItemList->listKeys = array('name','desc','createUid','status','beginTS');

		$selectArr = array();

		$this->load->model('lists/User_list',"reporterList");
		$this->reporterList->add_where(WHERE_TYPE_WHERE,"reportTo",$this->userInfo->id);
		$this->reporterList->load_data_with_where();
		$uids = $this->reporterList->gen_id_array();
		$uids[] = $this->userInfo->id;

		$selectArr[] = array('createUid' =>  array('$in'=>$uids));

		$selectArr[] = array('$or'=> array(
					//开始时间在周期内
					array("beginTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//已开始未结
					array("status"=>
							array('$in'=>array(2,3))),
					//预期结束时间在周期内
					array("dueEndTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//结束时间在周期内
					array("endTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//跨周期
					array("beginTS"=>
						array('$lte'=>$this->beginTS),
						"dueEndTS"=>
						array('$gte'=>$this->endTS)),
				));

		$this->confItemList->load_data_with_orignal_where(array('$and'=>$selectArr));


		$this->template->load('default_page', 'conf/'.$this->now_sub_menu);
	}

	function managerweekInfo($managerweekId,$reporterUid=""){
		$this->login_verify();
		$this->show_method_name = 'managerweek';
		$this->admin_load_menus();

		$this->load->model('records/Managerweek_model',"confInfo");
		$this->confInfo->init_with_id($managerweekId);

		$this->reporterUid = $reporterUid;

		$this->load->model('lists/User_list',"reporterList");
		$this->reporterList->add_where(WHERE_TYPE_WHERE,"reportTo",$this->userInfo->id);
		$this->reporterList->load_data_with_where();
		$uids = $this->reporterList->gen_id_array();

		if (!in_array($this->reporterUid,$uids)){
			$this->reporterUid = $this->userInfo->id;
		}
		$this->sub_menus = array(
            "index"=>array("name"=>"上周回顾","show"=>true,"method"=>'managerweekInfo'),
			"next"=>array("name"=>"下周安排","show"=>true,"method"=>'managerweekInfo'),
			"chat"=>array("name"=>"需讨论","show"=>true,"method"=>'managerweekChatList'),
        );

        if (isset($this->sub_menus[$sub_menu])){
            $this->now_sub_menu = $sub_menu;
        } else {
            $this->now_sub_menu = "index";
        }

		$this->beginTS = $this->confInfo->field_list['beginTS']->formatTSAsDayBeginTS();
		$this->endTS = $this->confInfo->field_list['endTS']->formatTSAsDayEndTS();

		$this->load->model('lists/Task_list',"taskList");
		$this->taskList->orderKey = array('dueUser'=>'asc','dueEndTS'=>'asc');

		$selectArr = array();

		$selectArr[] = array('$or'=>array(
			array('createUid' =>  $this->reporterUid),
			array('dueUser'=> $this->reporterUid),
		));
		$selectArr[] = array('$or'=> array(
					//开始时间在周期内
					array("beginTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//已开始未结
					array("status"=>
							array('$in'=>array(2,3))),
					//预期结束时间在周期内
					array("dueEndTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//结束时间在周期内
					array("endTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//跨周期
					array("beginTS"=>
						array('$lte'=>$this->beginTS),
						"dueEndTS"=>
						array('$gte'=>$this->endTS)),
				));
		$this->taskList->orderKey = array('status'=>'desc');

		$this->taskList->load_data_with_orignal_where(array('$and'=>$selectArr));

		$this->load->model('lists/Task_list',"subTasksList");
		$this->realSubTasksList = array();

		$taskIds = $this->taskList->gen_id_array();
		$selectArr = array();

		$selectArr[] = array('parentTaskId' =>  array('$in'=>$taskIds));
		$selectArr[] = array('$or'=> array(
					//开始时间在周期内
					array("beginTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//已开始未结
					array("status"=>
							array('$in'=>array(2,3))),
					//预期结束时间在周期内
					array("dueEndTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//结束时间在周期内
					array("endTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//跨周期
					array("beginTS"=>
						array('$lte'=>$this->beginTS),
						"dueEndTS"=>
						array('$gte'=>$this->endTS)),
				));
		$this->subTasksList->orderKey = array('status'=>'asc');
		$this->subTasksList->load_data_with_orignal_where(array('$and'=>$selectArr));

		foreach ($this->subTasksList->record_list as $key => $value) {
			$parentTaskId = $value->field_list['parentTaskId']->value;
			$thisId = $value->id;
			if (in_array($thisId,$taskIds)){
				//主任务已经有这条，从主任吴u删除
				unset($this->taskList->record_list[$thisId]);
			}
			if (!isset($this->realSubTasksList[$parentTaskId])){
				$this->realSubTasksList[$parentTaskId] = array();
			}
			$this->realSubTasksList[$parentTaskId][] = $value;
		}
		$subTaskIds = $this->subTasksList->gen_id_array();
		foreach ($subTaskIds as $key => $value) {
			if (!in_array($value,$taskIds)){
				$taskIds[] = $value;
			}
		}


		$this->taskStatusArray = array();
		foreach ($this->taskList->record_list as $key => $value) {
			$this->taskStatusArray[$value->field_list['status']->value][] = $value;
		}

		$this->load->model('lists/Feature_list',"featureList");
		$this->realFeatureList = array();

		$selectArr = array();

		$selectArr[] = array('taskId' =>  array('$in'=>$taskIds));
		$selectArr[] = array('$or'=> array(
					//开始时间在周期内
					array("beginTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//已开始未结
					array("status"=>
							array('$in'=>array(2,3))),
					//预期结束时间在周期内
					array("dueEndTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//结束时间在周期内
					array("endTS"=>
						array('$gte'=>$this->beginTS,'$lte'=>$this->endTS)),
					//跨周期
					array("beginTS"=>
						array('$lte'=>$this->beginTS),
						"dueEndTS"=>
						array('$gte'=>$this->endTS)),
				));
		$this->featureList->orderKey = array('status'=>'asc');

		$this->featureList->load_data_with_orignal_where(array('$and'=>$selectArr));

		foreach ($this->featureList->record_list as $key => $value) {
			$parentTaskId = $value->field_list['taskId']->value;
			if (!isset($this->taskList->record_list[$parentTaskId])){
				if (!isset($this->subTasksList->record_list[$parentTaskId])){
					continue;
				}
				$parentTaskId = $this->subTasksList->record_list[$parentTaskId]->field_list['parentTaskId']->value;
			}
			if (!isset($this->realFeatureList[$parentTaskId])){
				$this->realFeatureList[$parentTaskId] = array();
			}
			$this->realFeatureList[$parentTaskId][] = $value;
		}



		$this->load->model('lists/Changelog_list',"changelogList");
		$this->changelogList->add_where(WHERE_TYPE_WHERE_GT,'beginTS',$this->beginTS);
		$this->changelogList->add_where(WHERE_TYPE_WHERE_LT,'beginTS',$this->endTS);
		$this->changelogList->add_where(WHERE_TYPE_WHERE,'relate_turple.t',4);

		$this->changelogList->load_data_with_where();

		$this->storyChangLog = array();
		$this->aiChangLog = array();
		$this->taskChangLog = array();

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
			}else if ($relate_turple->relate_typ==4){
				if (!isset($this->taskChangLog[$relate_turple->relate_id])){
					$this->taskChangLog[$relate_turple->relate_id] = array();
				}
				$this->taskChangLog[$relate_turple->relate_id][] = $value;
			}
		}

		$counterTaskListByStatus = array();
		// $counterSubTaskListByStatus = array();


		foreach ($this->taskList->record_list as $key => $value) {
			if (!isset($counterTaskListByStatus[$value->field_list['status']->value])){
				$counterTaskListByStatus[$value->field_list['status']->value] = 0;
			}
			$counterTaskListByStatus[$value->field_list['status']->value] ++;
			// $this->arrStoryByFeatures[$value->field_list['featureId']->value][] = $key;
		}

		foreach ($this->subTasksList->record_list as $key => $value) {
			if (!isset($counterTaskListByStatus[$value->field_list['status']->value])){
				$counterTaskListByStatus[$value->field_list['status']->value] = 0;
			}
			$counterTaskListByStatus[$value->field_list['status']->value]++;
			// $this->arrAiByFeatures[$value->field_list['featureId']->value][] = $key;
		}

		$taskEnum = array(0=>'未设置',1=>'未启动',2=>'准备',3=>'进行中',4=>'完工',5=>'延迟');

		$this->dataChartTask = array();
		foreach ($taskEnum as $key => $value) {
			if (!isset($counterTaskListByStatus[$key])){
				$counterTaskListByStatus[$key] = 0;
			}
			$this->dataChartTask[] = array("label"=>$value,
					"data"=>$counterTaskListByStatus[$key]);
		}

		// $this->dataChartSubTask = array();
		// foreach ($taskEnum as $key => $value) {
		// 	if (!isset($counterSubTaskListByStatus[$key])){
		// 		$counterSubTaskListByStatus[$key] = 0;
		// 	}
		// 	$this->dataChartSubTask[] = array("label"=>$value,
		// 			"data"=>$counterSubTaskListByStatus[$key]);
		// }

		$this->template->load('default_page', 'conf/index');
	}

	function taskinfo($taskId="") {
        $this->show_method_name = 'index';
		$this->login_verify();
		$this->admin_load_menus();

		$this->load->model('records/Task_model',"taskInfo");
		$this->taskInfo->init_with_id($taskId);

		$this->load->model('lists/Task_list',"subTasksList");
		$this->subTasksList->add_where(WHERE_TYPE_WHERE,"parentTaskId",$taskId);
		$this->subTasksList->listKeys = array('name','dueUser','status','dueEndTS');
		$this->subTasksList->load_data_with_where();

		$this->load->model('lists/Feature_list',"featureList");
		$this->featureList->add_where(WHERE_TYPE_WHERE,"taskId",$taskId);
		$this->featureList->listKeys = array('name','dueUser','status','dueEndTS');
		$this->featureList->load_data_with_where();


		$subIds = array($taskId);
		foreach ($this->subTasksList->record_list as $key => $value) {
			$subIds[] = $value->id;
		}

		$this->load->model('lists/Changelog_list',"changelogList");
		$this->changelogList->add_where(WHERE_TYPE_WHERE,'relate_turple.t',4);
		$this->changelogList->add_where(WHERE_TYPE_IN,'relate_turple.v',$subIds);
		// $this->changelogList->
		$this->changelogList->load_data_with_where();

		$this->template->load('default_page', 'task/info');
	}

	function doSthBeforeShowUpdatePage($typ,$id){
		switch ($typ) {
			case 'task':
				//dueUser设置为只看自己汇报关系的//如果还有report关系和自己平级的
				if ($this->userInfo->field_list['reportTo']->isEmpty()){
					$this->dataInfo->field_list['dueUser']->add_where(WHERE_TYPE_WHERE,'reportTo',$this->userInfo->id);
				} else {
					$this->dataInfo->field_list['dueUser']->add_where(WHERE_TYPE_IN,'reportTo',array($this->userInfo->id,$this->userInfo->field_list['reportTo']->value));
				}
				if ($this->send_hack){
					//转发
					$this->modifyNeedFields = array(
							array('dueUser')
					);
				}else if ($this->dataInfo->field_list['createUid']->value==$this->userInfo->id&&$this->dataInfo->field_list['dueUser']->value==$this->userInfo->id){
					$this->modifyNeedFields = array(
			                array('name'),
			                array('dueUser','parentTaskId'),
			                array('typ','priority'),
			                array('status','progress'),
			                array('relatedUsers'),
			                array('beginTS','dueEndTS'),
							array('endTS'),
			                array('desc'),
			                array('rate'),
			                array('solution'),
			        );
				}else if ($this->dataInfo->field_list['createUid']->value!=$this->userInfo->id){
					//不是我发的工作，编辑字段少很多
					if ($this->dataInfo->field_list['dueUser']->value==$this->userInfo->id){
						$this->modifyNeedFields = array(
				                array('status','progress'),
								array('relatedUsers'),

				                array('beginTS','endTS'),
				                array('desc')
				        );
					} else {
						$this->dataInfo->changeShowFields = array(
				                array('status','progress'),
				                array('endTS'),
				                array('desc')
				        );
					}

				}
				# code...
				break;

			default:
				# code...
				break;
		}
	}

    function doSthBeforeShowCreatePage($typ,$id){
        if ($typ=='task'){

            //dueUser设置为只看自己汇报关系的//如果还有report关系和自己平级的
			if ($this->userInfo->field_list['reportTo']->isEmpty()){
				$this->dataInfo->field_list['dueUser']->add_where(WHERE_TYPE_WHERE,'reportTo',$this->userInfo->id);
			} else {
				$this->dataInfo->field_list['dueUser']->add_where(WHERE_TYPE_IN,'reportTo',array($this->userInfo->id,$this->userInfo->field_list['reportTo']->value));
			}


            //父人物只看自己发的和别的发给自己的
            // $this->dataInfo->field_list['parentTaskId']->

            $this->dataInfo->field_list['parentTaskId']->add_where(WHERE_TYPE_OR_WHERE,'createUid',$this->userInfo->id);
            $this->dataInfo->field_list['parentTaskId']->add_where(WHERE_TYPE_OR_WHERE,'dueUser',$this->userInfo->id);
        }
    }

	function doSthAfterInsert($typ,$data,$newId){
		if ($typ=="task"){

			$this->dataInfo->init_with_data($newId,$data);
			$title = '您收到了新的工作安排';
			$msg = '内容:  '.$this->dataInfo->field_list['name']->gen_show_value()."\n";
			$msg .='预期完成时间: '.$this->dataInfo->field_list['dueEndTS']->gen_show_html()."\n";

			$url = 'task/taskinfo/'.(string)$newId;
			$this->sendRtxNotify(array($this->dataInfo->field_list['dueUser']->value),$title,$msg,$url);

			$this->load->model('records/User_model',"dueInfo");
			$this->dueInfo->init_with_id($this->dataInfo->field_list['dueUser']->value);
			$title2 = $this->dueInfo->field_list['name']->value.'有了新工作';
			$this->sendRtxNotify($this->dataInfo->field_list['relatedUsers']->value,$title2,$msg,$url);
		}
	}

	function doSthBeforeInsert($typ,$data){
		if($typ=="mytext"){
			$data['editTS']=time();
		}
		return $data;
	}

	function doSthBeforeUpdate($typ,$data,$id){
		if ($typ=="task"){
			$this->load->model('records/Task_model',"taskInfo");
			$this->taskInfo->init_with_id($id);
			if (isset($data['status'])){
				if ($data['status']==4){
					if(!isset($data['beginTS'])&&$this->taskInfo->field_list['beginTS']->value==0){
						$jsonRst = -1;
			            $jsonData['err']['id'] = 'modify_beginTS';
			            $jsonData['err']['msg'] ='确认完工前请先填写开始时间';
			            echo $this->exportData($jsonData,$jsonRst);
						exit;
			            return ;
					}
					if (!isset($data['endTS'])||$this->taskInfo->field_list['endTS']->value==0){
						$data['endTS'] = time();
					}
				}
				if ($data['status']!=4){
					if (isset($data['endTS'])||$this->taskInfo->field_list['endTS']->value){
						$data['endTS'] = 0;
					}
				}
			}
			if(!isset($data['relatedUsers']) && isset($data['dueUser']) && $data['dueUser']!=$this->dataInfo->field_list['dueUser']->value&&$this->dataInfo->field_list['dueUser']->value!=$this->dataInfo->field_list['createUid']->value){
				$data['relatedUsers']=$this->dataInfo->field_list['relatedUsers']->value;
				$data['relatedUsers'][]=$this->dataInfo->field_list['dueUser']->value;
			}
		}
		else if($typ=="mytext"){
			$data['editTS']=time();
		}
		return $data;
	}

	function doSthAfterUpdate($typ,$data,$id){
		if ($typ=="task"){
			$this->dataInfo->init_with_part_data($data);

			if (isset($data['status'])){
				if ($data['status']==4){
					$title = '工作安排结束';
				} else {
					$title = '工作安排进展: '.$this->dataInfo->field_list['status']->gen_show_value();
				}

				$msg = '内容:  '.$this->dataInfo->field_list['name']->gen_show_value()."\n";
				$msg .='预期完成时间: '.$this->dataInfo->field_list['dueEndTS']->gen_show_html()."\n";
				$msg .='完成时间: '.$this->dataInfo->field_list['endTS']->gen_show_html()."\n";

				$url = 'task/taskinfo/'.$this->dataInfo->id;
				$this->sendRtxNotify(array($this->dataInfo->field_list['createUid']->value),$title,$msg,$url);
				$this->sendRtxNotify($this->dataInfo->field_list['relatedUsers']->value,$title,$msg,$url);

				if ($data['status']==4 && !$this->dataInfo->field_list['parentTaskId']->isEmpty()){
					$this->load->model('records/Task_model',"parentTask");
					$this->parentTask->init_with_id($this->dataInfo->field_list['parentTaskId']->value);

					$title = '子工作安排结束';
					$msg = '父工作内容:  '.$this->parentTask->field_list['name']->gen_show_value()."\n";
					$msg = '内容:  '.$this->dataInfo->field_list['name']->gen_show_value()."\n";
					$msg .='预期完成时间: '.$this->dataInfo->field_list['dueEndTS']->gen_show_html()."\n";
					$msg .='完成时间: '.$this->dataInfo->field_list['endTS']->gen_show_html()."\n";

					$url = 'task/taskinfo/'.$this->dataInfo->id;
					$this->sendRtxNotify(array($this->parentTask->field_list['createUid']->value),$title,$msg,$url);
					$this->sendRtxNotify($this->dataInfo->field_list['relatedUsers']->value,$title,$msg,$url);
				}
			}
			if (isset($data['dueUser'])){
				$title = '有新的工作安排委托到您';
				$msg = '内容:  '.$this->dataInfo->field_list['name']->gen_show_value()."\n";
				$msg .='预期完成时间: '.$this->dataInfo->field_list['dueEndTS']->gen_show_html()."\n";
				$msg .='完成时间: '.$this->dataInfo->field_list['endTS']->gen_show_html()."\n";

				$url = 'task/taskinfo/'.$this->dataInfo->id;
				$this->sendRtxNotify(array($this->dataInfo->field_list['dueUser']->value),$title,$msg,$url);

				$this->load->model('records/User_model',"dueInfo");
				$this->dueInfo->init_with_id($this->dataInfo->field_list['dueUser']->value);
				$title2 = $this->dueInfo->field_list['name']->value.'的工作安排有了更新';
				$this->sendRtxNotify($this->dataInfo->field_list['relatedUsers']->value,$title2,$msg,$url);
			}
		}
	}

	function doEnd($id){
		$this->load->model('records/Confitem_model',"dataInfo");
		$this->dataInfo->init_with_id($id);
		$data=array();
		$data['status']=1;
		$this->dataInfo->update_db($data);

		$jsonData = array();
        $this->exportToRefer(1,$jsonData);
	}
}
