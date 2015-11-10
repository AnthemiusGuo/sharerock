<?php
include_once(APPPATH."models/fields/field_array.php");
class Field_array_hash extends Field_array {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array_hash";
        $this->field_typ = "Null_model";
        $this->hash_info = array('name'=>'名称','info'=>'信息');
        $this->value = array();
        $this->datas = array();
    }
    public function gen_show_html(){
        $_html = '<table class="table table-bordered"><thead>
                <tr>';
        foreach ($this->hash_info as $key => $value) {
            $_html .= '<th>'.$value.'</th>';
        }
        $_html .= '<th>操作</th>
                </tr>
            </thead><tbody>';

        foreach ($this->value as $item) {
            $_html .= '<tr>';
            foreach ($this->hash_info as $key => $value) {
                $_html .= '<td>'.$item[$key].'</td>';
            }
            $_html .= '</tr>';

        }
        $_html .= '</tbody></table>';
        return $_html;
    }
    public function gen_value($input){
        $input = (int)$input;

        return $input;
    }
    public function gen_editor($typ=0){
        $inputName = $this->build_input_name($typ);
        $validates = $this->build_validator();
        if ($typ==1){
            $this->default = $this->value;
        }
        return "<input id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" placeholder=\"{$this->placeholder}\" type=\"text\" value=\"{$this->default}\" $validates /> ";
    }
    public function check_data_input($input)
    {
        if ($input==0){
            return false;
        }
        return parent::check_data_input($input);
    }
}
?>
