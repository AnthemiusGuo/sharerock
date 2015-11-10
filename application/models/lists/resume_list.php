<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/resume_model.php");
class Resume_list extends List_model {
    public function __construct() {
        parent::__construct('qResume');
        parent::init("Resume_list","Resume_model");
        $this->quickSearchWhere = array("candidate");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('name','candidate','source','firstReview','firstResult','secondReview','secondResult','hrReview','result');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
