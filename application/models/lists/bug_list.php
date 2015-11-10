<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/bug_model.php");
class Bug_list extends List_model {
    public function __construct() {
        parent::__construct('tBug');
        parent::init("Bug_list","Bug_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('level','priority','name','versionId','releaseId','status','createUid','dueUser','createTS','endTS');
    }

    public function build_search_infos(){
        return array('name','versionId','releaseId','status','createUid','dueUser','reStep');
    }
}
?>
