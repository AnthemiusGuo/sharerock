<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/crate_model.php");
class Crate_list extends List_model {
    public function __construct() {
        parent::__construct('oCrate');
        parent::init("Crate_list","Crate_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('createTS'=>'desc');
    }

    public function build_list_titles(){
        return array('name','createUid','crateId','createTS','endTS','state','examineUser','dealUser');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
