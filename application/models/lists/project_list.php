<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/project_model.php");
class Project_list extends List_model {
    public function __construct() {
        parent::__construct('sProject');
        parent::init("Project_list","Project_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','desc');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
