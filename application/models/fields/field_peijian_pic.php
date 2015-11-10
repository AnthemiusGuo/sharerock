<?php
include_once(APPPATH."models/fields/field_array.php");
class Field_peijian_pic extends Field_array {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array";
        $this->value = array('','','','','');
        $this->names = array('损坏位置','旧件拆后','新件装上去前','新旧对比','新件装好后');
    }
    public function init($value){
        if (gettype($value)!=="array"){
            $value = array('','','','','');
        }

        $this->value = $value;
    }
    public function gen_list_html($limit = 0){
        $_html = '';
        
        return $_html;
    }
    public function gen_show_html(){
        $_html = '<ul class="list-group">';
        foreach ($this->value as $key => $value) {
            $_html .='<li class="list-group-item">';
            $_html .= $this->names[$key].':';
            if ($value==""){
                $_html .= " - ";
            } else {
                $_html .= "<img src='".static_url($value)."' width='200px'/>";
            }
            $_html .='</li>';
        }
        $_html .= "</ul>";
        return $_html;
    }
    public function gen_value($input){
        $real_input = json_decode($input,true);
        if ($real_input==NULL){
            //解析失败
            return array();
        }
        return $real_data;
    }
    public function build_validator(){
        $validater .= parent::build_validator();
        return $validater;
    }
    public function gen_editor($typ=0){
        // $this->editor_url
        // $inputName = $this->build_input_name($typ);
        // $validates = $this->build_validator();
        // if ($typ==1){
        //     $this->default = $this->value;
        // }
        // return "<input id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" placeholder=\"{$this->placeholder}\" type=\"text\" value=\"{$this->default}\" $validates /> ";
        $this->editor_typ = $typ;
        $this->CI->editorData = $this;
        $editor = $this->CI->load->view('editor/array_pic', '', true);
        return $editor;
    }
    public function check_data_input($input)
    {
        if ($input==0){
            return false;
        }
        if (is_array($input) && count($input)==0){
            return false;
        }
        return parent::check_data_input($input);
    }
}
?>
