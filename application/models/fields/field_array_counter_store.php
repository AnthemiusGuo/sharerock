<?php
include_once(APPPATH."models/fields/field_array_kv.php");
class Field_array_counter_store extends Field_array_kv {

    public function __construct($show_name,$name,$is_must_input=false) {

        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array_counter_store";

        $this->real_data = array();

        $this->keyField = '_id';
        $this->showField = 'name';
        $this->tableName = 'oOrg';

        //从数据库拉取所有的 key
        $this->getAllKeys();
        foreach ($this->allKeys as $key => $item) {
            $this->value[$key] = 0;
            $this->real_data[$key] = 0;
        }
        $this->default = $this->value;
    }
    public function init($value){
        if (gettype($value)!=="array"){
            $value = array();
        }

        $this->value = $value;

        foreach ($this->allKeys as $key => $item) {
            if (isset($value[$key])) {
                $this->real_data[$key] = $value[$key];
            } else {
                $this->real_data[$key] = 0;
            }
        }

    }
    public function gen_list_html($limit = 0){
        $_html = '';
        foreach ($this->real_data as $key=>$item) {
            $_html .= $this->allKeys[$key].': '.$this->real_data[$key].'<br/>';
        }
        return $_html;
    }
    public function gen_show_value(){
        $_html = '';
        foreach ($this->real_data as $key=>$item) {
            $_html .= $this->allKeys[$key].': '.$this->real_data[$key].'<br/>';
        }
        return $_html;
    }

    public function gen_show_html(){
        $_html = '<ul class="list-group">';
        foreach ($this->real_data as $key=>$item) {
            $_html .='<li class="list-group-item">';
            $_html .= $this->allKeys[$key].': '.$this->real_data[$key];
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
        $real_data = array();
        foreach ($real_input as $id => $item) {
            $real_input[$id] = (float)$item;
        }
        return $real_input;
    }
    public function build_validator(){
        $validater .= parent::build_validator();
        return $validater;
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
