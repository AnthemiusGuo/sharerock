<?php
include_once(APPPATH."models/fields/fields.php");
include_once(APPPATH."models/records/serviceuser_model.php");

class Field_array_service extends Fields {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array";
        $this->real_data = array();
        $this->bookId = '';
        $this->value = array();
        $this->real_data = array();
        $this->count = 0;
        $this->field_typ = 'Serviceuser_model';

        $this->dataModel = new $this->field_typ();

        $this->listFields = array('typ','biaoshi','name','jiage','counter','chengben','xinghaoId');
        $this->showListFields = array(
                    array('typ','name'),
                    array('pinpai','xinghao'),
                    array('baoxiu','jiage')
                );
        $this->mustFields = array('typ'=>true,'name'=>true,'pinpai'=>true);
        $this->templates = '{typ} : {pinpai}-{name} {xinghao}(保修{baoxiu}月,参考价格{jiage})';
    }

    public function setbookId($id){
        $this->bookId = $id;
    }
    public function init($value){

        if (!is_array($value)){
            $value = array();
        }
        $this->value = $value;
        foreach ($value as $key => $this_item) {
            $this->real_data[(string)$this_item['_id']] = new Serviceuser_model();
            $this->real_data[(string)$this_item['_id']]->init_with_data($this_item['_id'],$this_item);
        }
        $this->count = count($value);
    }

    public function gen_list_html(){
        return "";
    }
    public function gen_show_html(){
        $this->CI->peijianDataInfo = $this;
        $editor = $this->CI->load->view('editor/peijians_show', '', true);
        return $editor;
    }
    public function gen_front_show_html(){
        $_html = "";
        foreach ($this->real_data as $key => $this_item) {
            $_html .= $this_item->gen_front_show_html().'<br/>';
        }
        return $_html;
    }
    public function gen_show_value(){
        $_html = "";
        foreach ($this->real_data as $key => $this_item) {
            $_html .= $this_item->gen_show_value().'<br/>';
        }
        return $_html;
    }

    public function setDefault($default){
        $this->default = json_decode($default,true);
        if ($this->default==false){
            $this->default = array();
        }
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
        return parent::check_data_input($input);
            $this->default = json_decode($this->value,true);
            if ($this->default==false){
                $this->default = array();
            }
        }
    //     // $string = '<div class="checkbox">';
    //     // if ($typ!=2){
    //     //     $string = "<select multiple id=\"$inputName\" name=\"$inputName\" class=\"{$this->input_class}\" $validates>";
    //     // } else {
    //     //     $string = "<select id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" $validates>";

    //     // }

    //     // foreach ($this->enum as $key => $value) {

    //     //     $string .= '<option value="'.$key.'">'.$value.'</option>'."\n";
    //     // }
    //     // $string .= "</select>";
    //     if ($typ==2){
    //         $width = 'width:42%;text-align:left;padding-top:3px;margin-top:5px;';
    //     } else {
    //         $width = "";
    //     }
    //     if ($typ==2){
    //         $string = "<select id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" $validates>";
    //         foreach ($this->enum as $key => $value) {
    //             $string.= "<option ". (($key==$this->default)?'selected="selected"':'') ." value=\"$key\">$value</option>";
    //         }
    //         $string .= "</select>";
    //     } else {
    //         foreach ($this->enum as $key => $value) {
    //             if (in_array($key,$this->default)){
    //                 $plus = 'checked="checked"';
    //             } else {
    //                 $plus = "";
    //             }
    //             $string .= '<label class="checkbox-inline" style="'.$width.'"><input type="checkbox" name="'.$inputName.'[]" class="'.$inputName.'" id="'.$inputName.$key.'" value="'.$key.'" '.$plus.'/>'.$value."</label>";
    //         }
    //     }



    //     // $string .= "</div>";
    //     return $string;
    // }
    public function gen_value_from_front($input){
        $result = array();

        if ($input===false){
            return $result;
        }
        foreach ($input as $key => $value) {
            if (isset($value['_id']) && MongoId::isValid($value['_id'])){
                $value['_id'] = new MongoId($value['_id']);
            } else {
                $value['_id'] = new MongoId();
            }

            // if ($value['xinghaoId']=="0" || !MongoId::isValid($value['xinghaoId'])){
            //     //自动创建条目
            //     unset($value['xinghaoId']);
            //     $value['xinghaoId'] = (string)$this->dataModel->insert_sys_db($value);
            // }
            $result[] = $value;
        }

        return $result;
    }
    public function gen_value($input){


        return json_encode($input);
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
