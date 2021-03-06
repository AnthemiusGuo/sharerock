<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/story_model.php");
class Story_list extends List_model {
    public function __construct() {
        parent::__construct('pStory');
        parent::init("Story_list","Story_model");
        $this->quickSearchWhere = array("name","phone");
        $this->is_lightbox = false;
        $this->orderKey = array('typ'=>'asc');
    }

    public function build_list_titles(){
        return array('featureId','system','name','desc','status','dueUser','storyPoint','beginTS','dueEndTS','endTS');
    }

    public function build_search_infos(){
        return array('name');
    }
}
?>
