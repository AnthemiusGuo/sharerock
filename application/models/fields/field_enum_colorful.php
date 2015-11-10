<?php
include_once(APPPATH."models/fields/field_enum.php");
class Field_enum_colorful extends Field_enum {
    public $enum;
    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_enum";
        $this->colors = array();
    }
    public function setColor($enumArray){
        $this->colors = $enumArray;
    }

    public function gen_list_html(){
        if (isset($this->colors[$this->value])){
            $color = $this->colors[$this->value];
        } else {
            $color = 'primary';
        }
        return '<span class="label label-'.$color.'">'.$this->enum[$this->value].'</span>';
    }
    
    public function gen_show_html(){
        if (isset($this->colors[$this->value])){
            $color = $this->colors[$this->value];
        } else {
            $color = 'primary';
        }
        return '<span class="label label-'.$color.'">'.$this->enum[$this->value].'</span>';
    }



}
?>
