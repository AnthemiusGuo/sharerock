<?php
include_once(APPPATH."models/list_model.php");
include_once(APPPATH."models/records/workingweek_model.php");
class Workingweek_list extends List_model {
    public function __construct() {
        parent::__construct('sWorkingweek');
        parent::init("Workingweek_list","Workingweek_model");
    }

}
?>
