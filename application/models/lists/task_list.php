<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/task_model.php");
class Task_list extends List_model {
    public function __construct() {
        parent::__construct('tTask');
        parent::init("Task_list","Task_model");
        $this->quickSearchWhere = array("name","desc");
        $this->is_lightbox = false;
        $this->orderKey = array('dueEndTS'=>'asc');
        $this->listKeys = array('name','status','progress','dueUser','beginTS','dueEndTS','endTS');
        $this->shortListKeys = array('name','progress','dueUser','status','beginTS');
    }

    public function build_list_titles(){
        return $this->listKeys;
    }
    public function build_short_list_titles(){
        return $this->shortListKeys;
    }

    public function build_search_infos(){
        return array('name','status');
    }
}
?>
