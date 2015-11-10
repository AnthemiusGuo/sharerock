<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/position_model.php");
class Position_list extends List_model {
    public function __construct() {
        parent::__construct('qPosition');
        parent::init("Position_list","Position_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','num','fNum','createUid','createTS','endDate','state');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
