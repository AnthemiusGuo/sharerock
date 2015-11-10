<?php
include_once(APPPATH."models/fields/field_array.php");

class Field_tag extends Field_array {
    
    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_tag";
        $this->default = array();
        $this->value = array();
    }


    public function init($value){
        parent::init($value);
    }
    public function gen_search_result_show($value){
        return $this->enum[$value];
    }
    public function setEnum($enumArray){
        $this->enum = $enumArray;
    }
    public function gen_search_element($default="like"){
        return "<input type='hidden' name='searchEle_{$this->name}' id='searchEle_{$this->name}' value='like'>包含";
    }

    public function gen_list_html(){
        foreach ($this->value as $key => $value) {
            if (isset($this->enum[$value])){
                $real_value = $this->enum[$value];
            } else {
                $real_value = "未知";
            }
            $string .= '<span class="label label-success">'.$real_value.'</span>'."\n";
        }
        return $string;
    }
    public function gen_show_html(){
        foreach ($this->value as $key => $value) {
            if (isset($this->enum[$value])){
                $real_value = $this->enum[$value];
            } else {
                $real_value = "未知";
            }
            $string .= '<span class="label label-success">'.$real_value.'</span>'."\n";
        }
        return $string;
    }

    public function gen_editor($typ=0){
        $inputName = $this->build_input_name($typ);
        $validates = $this->build_validator();
        if ($typ==1){
            $this->default = $this->value;
            if ($this->default==false){
                $this->default = array();
            }
        }
        // $string = '<div class="checkbox">';
        // if ($typ!=2){
        //     $string = "<select multiple id=\"$inputName\" name=\"$inputName\" class=\"{$this->input_class}\" $validates>";
        // } else {
        //     $string = "<select id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" $validates>";
        
        // }
        
        // foreach ($this->enum as $key => $value) {
            
        //     $string .= '<option value="'.$key.'">'.$value.'</option>'."\n";
        // }
        // $string .= "</select>";
        if ($typ==2){
            $width = 'width:42%;text-align:left;padding-top:3px;margin-top:5px;';
        } else {
            $width = "";
        }
        if ($typ==2){
            $string = "<select id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" $validates>";
            foreach ($this->enum as $key => $value) {
                $string.= "<option ". (($key==$this->default)?'selected="selected"':'') ." value=\"$key\">$value</option>";
            }
            $string .= "</select>";
        } else {
            foreach ($this->enum as $key => $value) {
                if (in_array($key,$this->default)){
                    $plus = 'checked="checked"';
                } else {
                    $plus = "";
                }
                $string .= '<label class="checkbox-inline" style="'.$width.'"><input type="checkbox" name="'.$inputName.'[]" class="'.$inputName.'" id="'.$inputName.$key.'" value="'.$key.'" '.$plus.'/>'.$value."</label>";
            } 
        }
        

        
        // $string .= "</div>";
        return $string;
    }
    public function gen_value($input){
        return $input;
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