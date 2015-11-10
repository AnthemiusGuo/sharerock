<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/note_model.php");
class Note_list extends List_model {
    public function __construct() {
        parent::__construct('tNote');
        parent::init("Note_list","Note_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','createUid','dueUser','createTS','endTS','typ');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
