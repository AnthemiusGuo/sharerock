<?php
include_once(APPPATH."models/fields/field_related_multi_ids.php");
class Field_relate_multi_peaple extends Field_related_multi_ids {
    public $where = array();

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->set_relate_db('uUser','id','name');

    }


}
?>
