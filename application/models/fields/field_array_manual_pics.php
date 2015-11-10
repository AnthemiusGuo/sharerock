<?php
include_once(APPPATH."models/fields/field_array_hash.php");
include_once(APPPATH."models/records/manualpics_model.php");

class Field_array_manual_pics extends Field_array_hash {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array";
        $this->real_data = array();
        $this->bookId = '';
        $this->value = array();
        $this->real_data = array();
        $this->count = 0;
    }

    public function setbookId($id){
        $this->bookId = $id;
    }
    public function init($value){
        $this->value = $value;
        foreach ($value as $key => $this_item) {
            $id = (string)$this_item['_id'];
            $this->real_data[$id] = new Manualpics_model();
            $this->real_data[$id]->init_with_data($this_item['_id'],$this_item);
        }
        $this->count = count($this->real_data);

    }

    public function gen_list_html(){
        return "";
    }
    public function gen_show_html(){
        $this->CI->guzhangDataInfo = $this;
        $editor = $this->CI->load->view('editor/guzhang_show', '', true);
        return $editor;
    }

    public function setDefault($default){
        $this->default = json_decode($default,true);
        if ($this->default==false){
            $this->default = array();
        }
    }
    public function gen_editor($typ=0){
        if ($typ==1){
            $this->default = $this->showValue;
        }
        $this->editor_typ = $typ;
        $this->CI->editorData = $this;
        $editor = $this->CI->load->view('editor/18yujian', '', true);

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
