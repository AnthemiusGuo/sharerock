<?php
include_once(APPPATH."models/fields/field_array_relate.php");
include_once(APPPATH."models/records/user_model.php");

class Field_array_user extends Field_array_relate {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array";
        $this->real_data = array();
        $this->bookId = '';
        $this->value = array();
        $this->real_data = array();
        $this->set_relate_db('uUser','_id','name');
    }

    public function init($value){
        if (gettype($value)!="array"){
            $value = array();
        }
        $this->value = $value;
        foreach ($value as $key => $this_item) {
            if (MongoId::isValid($this_item)){
                $this->real_data[$key] = new User_model();
                $this->real_data[$key]->init_with_id($this_item);
            }

        }
    }

    public function gen_list_html(){
        $_html = "";
        foreach ($this->real_data as $key => $value) {
            $_html.='<span class="pinpaiName label label-success">'.$value->field_list['name']->value.'</span>&nbsp;&nbsp;';
        }
        return $_html;
    }

    public function setDefault($default){
        $this->default = $default;
        if (gettype($default)!="array"){
            $this->default = array();
        }
    }

    public function check_data_input($input)
    {
        if ($input==0){
            return false;
        }
        if (gettype($value)!="array"){
            return false;
        }
        return parent::check_data_input($input);
    }

    public function gen_front_show_html()
    {
        $_html = "";
        foreach ($this->real_data as $key => $value) {
            $_html.='<span class="label label-success label-large" onclick="modal_show(\'mdata\',\'jishiInfo\',\''.$value->id.'\',{})">'.$value->field_list['name']->value.'</span>&nbsp;&nbsp;';
        }
        return $_html;
    }

    public function gen_show_html(){
        $_html = "";
        foreach ($this->real_data as $key => $value) {
            $_html.='<span class="pinpaiName label label-success">'.$value->field_list['name']->value.'</span>&nbsp;&nbsp;';
        }
        return $_html;
    }
    public function gen_show_value(){
        $_html = "";
        foreach ($this->real_data as $key => $value) {
            $_html.= $value->field_list['name']->value.';';
        }
        return $_html;
    }

    public function gen_editor($typ=0){

        $this->setEnum();
        $this->editor_typ = $typ;
        $this->templates = "{_show_name}";
        $this->CI->relateEditorData = $this;
        $editor = $this->CI->load->view('editor/array_relate', '', true);
        return $editor;
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
