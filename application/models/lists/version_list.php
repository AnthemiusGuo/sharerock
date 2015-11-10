<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/version_model.php");
class Version_list extends List_model {
    public function __construct() {
        parent::__construct('pVersion');
        parent::init("Version_list","Version_model");
    }

}
?>
