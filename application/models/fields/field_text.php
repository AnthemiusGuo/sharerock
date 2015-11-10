<?php
include_once(APPPATH."models/fields/field_string.php");

class Field_text extends Field_string {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_text";
        $this->value = "";
    }
    public function init($value){
        parent::init($value);
    }
    public function gen_list_html($len = 50){
        if (mb_strlen($this->value)>$len) {
            $str = strip_tags($this->value);
            if (mb_strlen($str)>$len) {
                return nl2br(mb_substr($str,0,$len)."...");
            } else {
                return nl2br($this->value);
            }


        } else {
            return nl2br($this->value);

        }
    }
    public function gen_show_html(){
        return nl2br($this->value);
    }

    public function gen_editor($typ=0){
        $inputName = $this->build_input_name($typ);
        if ($typ==1){
            $this->default = $this->value;
        }
        if ($typ==2){
            return "<input  autocomplete=\"on\" id=\"$inputName\"  name=\"$inputName\" class=\"{$this->input_class}\" placeholder=\"{$this->placeholder}\" type=\"text\" value=\"{$this->default}\"/>";

        } else {
            return "<textarea id=\"$inputName\" rows=\"6\" name=\"$inputName\" class=\"{$this->input_class}\">{$this->default}</textarea>";
        }
    }

    public function gen_editorAdd($typ=0,$tmp){
        $inputName = $this->build_input_name($typ);
        if ($typ==1){
            $this->default = $this->value;
        }
        return "<textarea id=\"$inputName\" rows=\"6\" name=\"$inputName\" class=\"{$this->input_class}\">{$this->default}$tmp</textarea>";
    }
}
?>
