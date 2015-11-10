<?php
include_once(APPPATH."models/list_model.php");
class Common_list extends List_model {
    public function __construct() {
        parent::__construct('');
        $this->searchInfo = array();
        $this->listInfo = array();
    }

    public function setInfo($tableName,$listName,$dataModelName){
        $this->tableName = $tableName;
        parent::init($listName,$dataModelName);
    }

    public function setSearchInfo($searchInfo){
        $this->searchInfo = $searchInfo;
    }

    public function setListInfo($listInfo){
        $this->listKeys = $listInfo;
    }

    public function build_search_infos(){
        return $this->searchInfo;
    }

    public function build_inline_list_titles(){
        return $this->listKeys;
    }
    public function build_short_list_titles(){
        return $this->listKeys;
    }
    public function build_list_titles(){
        return $this->listKeys;
    }
}
?>
