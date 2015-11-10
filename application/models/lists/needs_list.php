<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/needs_model.php");
class Needs_list extends List_model {
    public function __construct() {
        parent::__construct('aNeeds');
        parent::init("Needs_list","Needs_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = false;
        $this->orderKey = array('status'=>'asc','priority'=>'asc');
    }

    public function build_list_titles(){
        return array('projectId','versionId','typ','name','num','priority','status','createUid','dueUser','beginTS','dueEndTS','endTS');
    }

    public function build_search_infos(){
        return array('name','typ');
    }
}
?>
