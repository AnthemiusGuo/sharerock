<?php
include_once(APPPATH."models/fields/field_relate_simple_id.php");
class Field_relate_org extends Field_relate_simple_id {
    public $where = array();

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->set_relate_db('oOrg','_id','name');
        $this->needOrgId = 0;
        
    }


}
?>
