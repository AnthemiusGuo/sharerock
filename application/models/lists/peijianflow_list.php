<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/peijianflow_model.php");
class Peijianflow_list extends List_model {
    public function __construct() {
        parent::__construct('bPeijianFlow');
        parent::init("Peijianflow_list","Peijianflow_model");
        $this->is_lightbox = true;
    }

    public function build_list_titles(){
        return array('bookShowId','orgId','peijianming','counter','counterO','typ','uid','chengben','chengbenO','beginTS');
    }

    public function build_inline_list_titles(){
        return array('bookShowId','orgId','counter','counterO','typ','uid','chengben','chengbenO','beginTS');
    }
}
?>
