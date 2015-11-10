<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/confitem_model.php");
class Confitem_list extends List_model {
    public function __construct() {
        parent::__construct('sConfitem');
        parent::init("Confitem_list","Confitem_model");
        $this->quickSearchWhere = array("name","desc");
        $this->orderKey = array('beginTS'=>'asc');
        $this->listKeys = array('name','status','beginTS');
    }

    public function build_list_titles(){
        return $this->listKeys;
    }
    public function build_short_list_titles(){
        return $this->shortListKeys;
    }

    public function build_search_infos(){
        return array('name','status');
    }
}
?>
