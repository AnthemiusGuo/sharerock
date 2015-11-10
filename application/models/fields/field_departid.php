<?php
include_once(APPPATH."models/fields/field_relate_simple_id.php");
class Field_departid extends Field_relate_simple_id {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->set_relate_db('gDepartment','_id','name');
        $this->is_link = false;

    }
    public function init($value){
        parent::init($value);
    }
}
?>
