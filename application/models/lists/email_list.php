<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/email_model.php");
class Email_list extends List_model {
    public function __construct() {
        parent::__construct('iEmail');
        parent::init("Email_list","Email_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','createUid','createTS','status');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
