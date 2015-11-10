<?php
include_once(APPPATH."models/fields/fields.php");
class Field_ts extends Fields {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_ts";

    }
    public function init($value){
        $value = (int)$value;
        parent::init($value);
    }
    public function setDefault($default){
        if ($default<=86400) {
            $this->default = ' - ';
        } else {
            if (is_numeric($default)) {
                $this->default = date("Y-m-d H:i",$default);
            } else {
                $this->default = $default;
            }


        }
    }
    public function isEmpty(){
        if ($this->value<=86400) {
            return true;
        }
        return false;
    }
    public function gen_list_html(){
        if ($this->value<=86400){
            return "-";
        } else {
            return date("y-m-d H:i",$this->value);
        }
    }
    public function gen_js_value(){
        return date("y-m-d H:i",$this->value);
    }
    public function gen_show_html(){
        if ($this->value<=86400){
            return "-";
        } else {
            return date("Y-m-d H:i",$this->value);
        }

    }
    public function gen_show_value(){
        if ($this->value<=86400){
            return "-";
        } else {
            return date("Y-m-d H:i",$this->value);
        }

    }
    public function gen_show_hour(){
        if ($this->value<=86400){
            return "-";
        } else {
            return date("H:i",$this->value);
        }
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
                    $(\"#{$inputName}\").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: true,
        todayBtn: true,language:'zh-CN',minuteStep:30});

                });
              </script>";

        return $str;
        //appendDtpicker({\"inline\": false,\"locale\": \"cn\",\"minuteInterval\": 30,\"calendarMouseScroll\": false});
    }
    public function gen_search_result_id($value){
        return $this->gen_value($value);
    }

    public function check_data_input($input)
    {
        if ((int)($input) == 0 ) {
            return false;
        }
        return parent::check_data_input($input);
    }

    public function gen_value($input){
        //Y-m-d H:i
        //2008-12-04 12:00
        if (trim($input) == "-" || trim($input) == "") {
            return 0;
        }
        $year      =   substr($input,0,4);
        $month       =   substr($input,5,2);
        $day        =  substr($input,8,2);

        $hour   =   substr($input,11,2);
        $min   =   substr($input,14,2);

        $ts = mktime($hour,$min,0,$month,$day,$year);
        if  ($ts<0){
            return 0;
        }
        return $ts;
    }

    function formatTSAsDayBeginTS(){
        $ts = $this->value;

        $m = date('m',$ts);
        $d = date('d',$ts);
        $y = date('Y',$ts);
        return mktime(0, 0, 0, $m, $d, $y);
    }
    function formatTSAsDayEndTS(){
        $ts = $this->value;

        $m = date('m',$ts);
        $d = date('d',$ts);
        $y = date('Y',$ts);
        return mktime(23,59, 59, $m, $d, $y);
    }

}
?>
