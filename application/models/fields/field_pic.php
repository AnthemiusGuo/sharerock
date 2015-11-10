<?php
include_once(APPPATH."models/fields/field_string.php");

class Field_pic extends Field_string {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_string";
        $this->value = '';
        $this->uploadUrl = "common/doUpload";
    }
    public function init($value){
        parent::init($value);
    }
    public function gen_list_html(){
        $len = $this->list_short_len;
        $str = $this->gen_show_html();
        if (mb_strlen($str)>$len) {
            return mb_substr($str, 0,$len-2)."...";
        } else {
            return $str;
        }
    }
    public function gen_show_html(){
        if ($this->value!=""){
            return '<img width="100%" src="'.static_url('uploads/'.$this->value).'"/>';
        } else {
            return '尚未上传';
        }

    }
    public function gen_search_element($default="="){
        $editor = "<select id=\"searchEle_{$this->name}\" name=\"searchEle_{$this->name}\" class=\"form-control input-sm\" value=\"{$default}\">";
        $editor.= "<option value=\"=\" ".(($default=="=")?"selected=\"selected\"":"").">=</option>";
        $editor.= "<option value=\"like\" ".(($default=="like")?"selected=\"selected\"":"").">包含</option>";
        $editor .= "</select>";
        return $editor;
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
        $this->CI->piceditorData = $this;
        $editor = $this->CI->load->view('editor/single_pic', '', true);
        return $editor;
    }
}
?>
