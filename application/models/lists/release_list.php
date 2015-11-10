<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/release_model.php");
class Release_list extends List_model {
    public function __construct() {
        parent::__construct('tRelease');
        parent::init("Release_list","Release_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','projectId','versionId','pushTime','status');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
