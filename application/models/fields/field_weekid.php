<?php
include_once(APPPATH."models/fields/field_relate_simple_id.php");
class Field_weekid extends Field_relate_simple_id {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->set_relate_db('sWorkingweek','_id','name');
        $this->is_link = false;
        $this->add_where(WHERE_TYPE_WHERE,'packed',0);

    }
    public function init($value){
        parent::init($value);
    }
}
?>
