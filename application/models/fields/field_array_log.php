<?php
include_once(APPPATH."models/fields/field_array_hash.php");
class Field_array_log extends Field_array_hash {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array_hash";

        $this->real_data = array();
        $this->data = array();
        $this->count = 0;
        
        $this->templates = '{chexingbianhao} : {chepaihao}/{chejiahao}/{fadongji}/{regTS}';
        $this->enum = array();
        $this->enum_reverse = array();
    }
    public function setEnum($arr){
        $this->enum = $arr;
        $this->enum_reverse = array_flip($arr);
    }

    public function getActionName($actionId){
        if (isset($this->enum[$actionId])){
            return $this->enum[$actionId];
        }
        return '['.$actionId.']';
    }

    public function getActionId($actionName){
        if (isset($this->enum_reverse[$actionName])){
            return $this->enum_reverse[$actionName];
        }
        return $actionName;
    }

    public function init($value){
        if (gettype($value)!=="array"){
            $value = array();
        }

        $this->real_data = $this->value = $value;
        $this->count = count($this->real_data);
    }
    public function gen_list_html($limit = 0){
        $_html = '';
        foreach ($this->real_data as $item) {
            $_html .= '车款: '.$item->field_list['chexingbianhao']->gen_show_html().' 车牌: '.$item->field_list['chepaihao']->gen_show_html().'<br/>';
        }
        return $_html;
    }
    public function gen_show_value(){
        $_html = '';
        foreach ($this->real_data as $item) {
            $_html .= '车款: '.$item->field_list['chexingbianhao']->gen_show_html().' 车牌: '.$item->field_list['chepaihao']->gen_show_html().'<br/>';
        }
        return $_html;
    }
    
    public function gen_show_html(){
        $_html = '<ul class="list-group">';
        foreach ($this->real_data as $item) {
            $_html .='<li class="list-group-item">';
            $_html .= '车款: '.$item->field_list['chexingbianhao']->gen_show_html().' 车牌: '.$item->field_list['chepaihao']->gen_show_html();
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
            if ($id<0) {
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
        $editor = '';
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
