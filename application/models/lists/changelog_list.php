<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/changelog_model.php");
class Changelog_list extends List_model {
    public function __construct() {
        parent::__construct('pChangelog');
        parent::init("Changelog_list","Changelog_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = true;
        $this->orderKey = array('beginTS'=>'desc');
        $this->listKeys = array('typ','name','changes','solution','dueUser','relate_turple','beginTS');
    }

    public function build_list_titles(){
        return $this->listKeys;
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
