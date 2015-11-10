<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/contrast_model.php");
class Contrast_list extends List_model {
    public function __construct() {
        parent::__construct('qContrast');
        parent::init("Contrast_list","Contrast_model");
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = true;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('createTS','name','plan','art');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
