<?php
include_once(APPPATH."models/fields/field_ts.php");
class Field_hour extends Field_ts {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_ts";

    }

    public function gen_editor($typ=0){
        if ($typ==1){
            if ($this->value<=86400) {
                $this->default = ' - ';
            } else {
                $this->default = date("Y-m-d H:i",$this->value);
            }

        } else {
            if ($this->default<=86400) {
                $this->default = ' - ';
            } else {
                if (is_numeric($this->default)){
                    $this->default = date("Y-m-d H:i",$this->default);
                }
            }
        }
        $inputName = $this->build_input_name($typ);
        $validates = $this->build_validator();
        $str = "<input id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" placeholder=\"{$this->placeholder}\" type=\"text\" value=\"{$this->default}\" $validates/>
              <script type=\"text/javascript\">
                $(function(){
                    $(\"#{$inputName}\").datetimepicker({format: 'yyyy-mm-dd hh:00',autoclose: true,
        todayBtn: true,language:'zh-CN',minuteStep:60,minView:'day'});

                });
              </script>";

        return $str;
    }

}
?>
