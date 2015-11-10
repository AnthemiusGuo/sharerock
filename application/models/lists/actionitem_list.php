<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/actionitem_model.php");
class Actionitem_list extends List_model {
    public function __construct() {
        parent::__construct('pActionitem');
        parent::init("Actionitem_list","Actionitem_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('status'=>'asc');
    }

    public function build_list_titles(){
        return  array('versionId','featureId','name','desc','dueUser','priority','status','dueEndTS','endTS');
    }

    public function build_search_infos(){
        return array('name','typ');
    }
}
?>
