<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/feature_model.php");
class Feature_list extends List_model {
    public function __construct() {
        parent::__construct('pFeature');
        parent::init("Feature_list","Feature_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = false;
        $this->orderKey = array('dueEndTS'=>'desc');
        $this->listKeys = array('name','desc','status','dueUser','dueEndTS','endTS','packed');
    }

    public function build_list_titles(){
        return $this->listKeys;
    }
    public function build_short_list_titles(){
        return array('taskId','name','solution','dueUser','status','beginTS');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
