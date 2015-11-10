<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/org_model.php");

class Org_list extends List_model {
    public function __construct() {
        parent::__construct('oOrg');
        parent::init("Org_list","Org_model");
        $this->is_lightbox = false;
    }

    public function build_search_infos(){
        return array('name','status','phone');
    }
    public function build_list_titles(){
        //姓名,类型,省份,状态,最后更新
        return array('name','addresses','status','phone','supperUid');
    }
}
?>
