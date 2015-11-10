<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."controllers/common.php");

class Refresh extends common {
	function __construct() {
		parent::__construct(false,'a');
	}

	function hour(){
		$this->endTS = $this->utility->getTS('endToday');

        $this->load->model('lists/Task_list',"listInfo");

		$this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'dueEndTS',$this->endTS);
		$this->listInfo->add_where(WHERE_TYPE_WHERE_NE,'status',4);
		$this->listInfo->load_data_with_where();
		foreach ($this->listInfo->record_list as $key => $this_record) {
			$title = '您的工作安排已经延迟，尚未提交！';
			$msg = '内容:  '.$this_record->field_list['name']->gen_show_value()."\n";
			$msg .='预期完成时间: '.$this_record->field_list['dueEndTS']->gen_show_html()."\n";

			$url = 'task/taskinfo/'.(string)$this_record->id;
			// var_dump(array($this->dataInfo->field_list['dueUser']->value),$title,$msg,$url);
			$this->sendRtxNotify(array($this->dataInfo->field_list['dueUser']->value),$title,$msg,$url);
			$this_record->update_db(array('status'=>4));
		}

		// $this->listInfo->update_db(array('status'=>4));


    }



}
