<?php
include_once(APPPATH."models/fields/field_array_hash.php");
include_once(APPPATH."models/records/thirdplat_model.php");
class Field_array_thirdplat extends Field_array_hash {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array_hash";
        $this->field_typ = "thirdplat_model";

        $this->dataModel = new $this->field_typ();
        
        $this->listFields = array('third_typ','third_id');
        $this->showListFields = array(
                    array('third_typ','third_id'),
                );
        $this->mustFields = array('third_typ'=>false,'third_id'=>true);
        $this->templates = '平台{third_typ} : ID: {third_id}';
        $this->value = array();
        $this->real_data = array();
        
    }
    public function init($value){
        $this->value = $value;
        foreach ($value as $key => $item) {
            $dataModel = new $this->field_typ();
            $dataModel->init_with_data($item['_id'],$item);
            $this->real_data[] = $dataModel;
        }
    }
    public function gen_list_html($limit = 0){
        $_html = '';
        foreach ($this->real_data as $item) {
            $_html .= '平台 '.$item->field_list['third_typ']->gen_show_value().' ; ID '.$item->field_list['third_id']->gen_show_html().'<br/>';
        }
        return $_html;
    }
    public function gen_show_html(){
        $_html = '<ul class="list-group">';
        foreach ($this->real_data as $item) {
            $_html .='<li class="list-group-item">';
            $_html .= '平台 '.$item->field_list['third_typ']->gen_show_value().' ; ID '.$item->field_list['third_id']->gen_show_html();
            $_html .='</li>';
        }
        $_html .= "</ul>";
        return $_html;
    }
    public function gen_value($input){
        // var_dump($input);
        // exit;
        if (is_array($input)){
            $real_input = $input;
        } else {
            $real_input = json_decode($input,true);
        }
        
        if ($real_input==NULL){
            //解析失败
            return array();
        }
        $real_data = array();
     
        foreach ($real_input as $id => $item) {
            if ($id<0 || !isset($item['_id'])) {
                //新建数据
                $real_id = new MongoId();
                $data = array('_id'=>$real_id);
            } else {
                $real_id = new MongoId($id);
                $data = array('_id'=>$real_id);
            }
            foreach ($this->listFields as $k) {
                $data[$k] = $this->dataModel->field_list[$k]->gen_value($item[$k]);
            }
            $real_data[] = $data;
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
        $editor = $this->CI->load->view('editor/array_common', '', true);
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
