<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/mytext_model.php");
class Mytext_list extends List_model {
    public function __construct() {
        parent::__construct('mMytext');
        parent::init("Mytext_list","Mytext_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = false;
        $this->orderKey = array('editTS'=>'asc');
    }

    public function build_list_titles(){
        return array('name','editTS');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
