<?php
include_once(APPPATH."models/fields/field_array.php");

class Field_array_plain extends Field_array {
    
    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array";
        $this->real_data = array();
        $this->value = array();
    }

    public function init($value){
        if (gettype($value)!="array"){
            $value = array();
        }
        $this->real_data = $this->value = $value;
    }

    public function has($value){
        return in_array($value,$this->value);
    }

    public function gen_list_html(){
        return "";
    }

    public function gen_value($input){
        if ($input===""){
            $input="[]";
        }
        $real_input = json_decode($input,true);
        return $real_input;
    }

    public function setDefault($default){
        $this->default = $default;
        if (gettype($default)!="array"){
            $this->default = array();
        }
    }

    public function gen_editor($typ=0,$need_require_validator=true){
        $inputName = $this->build_input_name($typ);
        $validates = $this->build_validator($need_require_validator);
        if ($typ==1){
            $this->default = $this->value;
        }
        return "<textarea id=\"$inputName\" rows=\"6\" name=\"$inputName\" class=\"{$this->input_class}\">".json_encode($this->default)."</textarea>";
        
    }

    public function check_data_input($input)
    {
        if ($input===0){
            return false;
        }
        if (gettype($value)!="array"){
            return false;
        }
        return parent::check_data_input($input);
    }   
    public function gen_show_value(){
        return json_encode($this->value);
    }

    public function gen_show_html(){
        $_html = "";
        foreach ($this->real_data as $key => $value) {
            $_html.='<span class="label label-success">'.$value.'</span>&nbsp;&nbsp;';
        }
        return $_html;
    }

    public function importData($value){
        $values = explode("|",$value);
        $rst = array();
        foreach ($this->enum as $k => $v) {
            if (in_array($v,$values)){
                //有这个
                $rst[] = $k;
            } 
        }
        return json_encode($rst);
    }
}
?>