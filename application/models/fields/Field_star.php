<?php
include_once(APPPATH."models/fields/field_float.php");
class Field_star extends Field_float {
    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_float";
        $this->value = -1;
    }
    public function build_validator(){
        if ($this->is_must_input){
            $validater .= ' required="required" ';
        }
        $validater .= Fields::build_validator();
        return $validater;
    }
    
    public function init($value){
        parent::init($value);
    }
    public function gen_list_html(){
        if ($this->value==-1){
            return '-';
        }
        return round($this->value,1);
    }
    public function gen_show_value(){
        if ($this->value==-1){
            return '-';
        }
        return round($this->value,1);
    }
    public function gen_show_html(){
        return $this->CI->utility->show_score($this->value);
    }
    
    public function gen_value($input){
        $input = (float)$input;
        return $input;
    }
}
?>
