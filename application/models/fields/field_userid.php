<?php
include_once(APPPATH."models/fields/field_relate_simple_id.php");

class Field_userid extends Field_relate_simple_id {
    public $isRelateToProjectId=false;
    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_userid";
        $this->set_relate_db('uUser','_id','name');
        $this->projectFieldName = 'projectIds';
    }

    public function set_typ($typ){

        $this->add_where(WHERE_TYPE_WHERE,'typ',$typ);
    }
    public function set_in_typ($typ){
        $this->add_where(WHERE_TYPE_IN,'typ',$typ);
    }

    public function gen_list_html(){
        return $this->showValue;
    }
    public function gen_show_html(){
        return $this->showValue;
    }
    public function gen_search_element($default="="){
        $editor = "<input type=\"hidden\" id=\"searchEle_{$this->name}\" name=\"search_{$this->name}\" class=\"form-control input-sm\" value=\"=\">";
        $editor .= "=";
        return $editor;
    }
    public function check_data_input($input)
    {
        if($this->is_must_input&&$input==="0"){
            return false;
        }
        if ($input===0){
            return false;
        }
        return parent::check_data_input($input);
    }

    public function init($value){
        $this->value = $value;
        if (is_numeric($value) && $value<=0){
            $this->showValue = '[系统]';
        } else {


            if ($value===0||$value==="0" || $value==="" || $value===null){
                $this->showValue = ' - ';

                return;
            }


            $this->valueSetted = true;

            if (!is_object($value) && $this->relate_id_is_id){
                $real_value = new MongoId($value);
            } else {
                $real_value = $value;
            }

            $cache_rst = $this->checkCache($value);
            if ($cache_rst!=null){
                $this->showValue = $cache_rst;
                $this->value_checked = 1;
                return;
            }


            $this->db->select(array($this->valueField,'name','loginName'))
                ->where(array($this->valueField => $real_value))
                ->limit(1);

            $query = $this->db->get($this->tableName);
            if ($query->num_rows() > 0)
            {

                $result = $query->row_array();
                if (isset($result[$this->showField])){
                    $this->showValue = $result[$this->showField].'('.$result['loginName'].')';
                    $this->setCache($value,$this->showValue);
                }
                $this->value_checked = 1;

            } else {
                $this->showValue = '[未知(id:'.$value.')]';
                $this->value_checked = -1;
            }
            parent::init($value);
            $this->userName = $this->showValue;
        }
    }

    public function genEnum(){
        $this->db->select("{$this->valueField},{$this->showField},loginName");
        $this->db->removeWhere();
        $this->checkWhere();
        $this->db->order_by(array($this->showField => 'ASC'))->limit(50);
        $query = $this->db->get($this->tableName);

        $this->enum[0] = ' - ';
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $this->enum["".$row[$this->valueField]] = $row[$this->showField].'('.$row['loginName'].')';
            }

        }
        if ($this->is_fullInited){
            $this->enum["".$this->value] = $this->showValue;
        }

    }
}
?>
