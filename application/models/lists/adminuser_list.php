<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/adminuser_model.php");
class Adminuser_list extends List_model {
    public function __construct() {
        parent::__construct('aUser');
        parent::init("Adminuser_list","Adminuser_model");
        $this->quickSearchWhere = array("name","phone");
        $this->orderKey = array("order"=>"asc");

    }

    public function build_list_titles(){
        return array('name','phone','typ','orgId','intro');
    }

    public function build_search_infos(){
        return array('name','phone');
    }
}
?>
