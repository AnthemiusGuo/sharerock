<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/department_model.php");
class Department_list extends List_model {
    public function __construct() {
        parent::__construct('gDepartment');
        parent::init("Department_list","Department_model");
        // $this->quickSearchWhere = array("name","phone");
        // $this->is_lightbox = false;
        // $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','manager');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
