<?php
include_once(APPPATH."models/fields/field_relate_simple_id.php");
class Field_cross_relate extends Field_relate_simple_id {
    public $where = array();

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_cross_relate";

        $this->valueSetted = false;
        $this->showValue = ' - ';
        $this->enum = array();
        $this->needOrgId = 1;
        $this->whereOrgId = '';
        $this->relate_id_is_id = true;
        $this->value_checked = 0;
        $this->relate_typ = 0;
        $this->relate_id = '';

        $this->whereData = array();
        $this->tableName = array();
        $this->valueField = array();
        $this->showField = array();
    }

    public function baseInit($value){
        parent::init($value);
    }

    public function setEnum($arr){
        foreach ($arr as $key => $value) {
            $this->tableName[$key] = $value['tableName'];
            $this->valueField[$key] = $value['valueField'];
            $this->showField[$key] = $value['showField'];
            if (isset($value['whereData'])){
                $this->whereData[$key] = $value['whereData'];
            }
        }
    }

    public function checkCache($typ,$id){
        global $cache_relate_id;
        $id = (string)$id;
        if (!isset($cache_relate_id[$typ])){
            $cache_relate_id[$typ] = array();
        }
        if (!isset($cache_relate_id[$typ][$id])){
            return null;
        } else {
            return $cache_relate_id[$typ][$id];
        }
    }

    public function setCache($typ,$id,$value){
        global $cache_relate_id;
        $id = (string)$id;
        if (!isset($cache_relate_id[$typ])){
            $cache_relate_id[$typ] = array();
        }
        $cache_relate_id[$typ][$id] = $value;
    }

    public function checkWhere($typ){
        if (!isset( $this->whereData[$typ])){
            return;
        }
        $where_array = $this->whereData[$typ];
        foreach ($where_array as $key => $value) {
            $typ = $value['typ'];
            $fieldName = $value['name'];
            $fieldData = $value['data'];

            switch ($typ) {
                case WHERE_TYPE_WHERE:
                    $this->db->where(array($fieldName=>$fieldData));
                    break;
                case WHERE_TYPE_OR_WHERE:
                    $this->db->or_where(array($fieldName=>$fieldData));
                    break;
                case WHERE_TYPE_WHERE_GT:
                    $this->db->where_gt($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_WHERE_GTE:
                    $this->db->where_gte($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_WHERE_LT:
                    $this->db->where_lt($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_WHERE_LTE:
                    $this->db->where_lte($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_WHERE_NE:
                    $this->db->where_ne($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_IN:
                    $this->db->where_in($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_LIKE:
                    $this->db->like($key, $value['data'],'iu');
                    break;
            }
        }

    }

    public function init($value){

        if (!is_array($value) || !isset($value['t']) || !isset($value['v']) ){
            $this->showValue = ' - ';

            return;
        }

        $this->relate_typ = $value['t'];
        $this->relate_id = $value['v'];

        $tableName = $this->tableName[$this->relate_typ];
        $valueField = $this->valueField[$this->relate_typ];
        $showField = $this->showField[$this->relate_typ];



        $this->valueSetted = true;

        if (!is_object($value['v']) && $this->relate_id_is_id){
            $real_value = new MongoId($value['v']);
        } else {
            $real_value = $value['v'];
        }

        $cache_rst = $this->checkCache($tableName,$value['v']);
        if ($cache_rst!=null){
            $this->showValue = $cache_rst;
            $this->value_checked = 1;
            return;
        }


        $this->db->select(array($valueField,$showField))
            ->where(array($valueField => $real_value))
            ->limit(1);
        $this->checkWhere($this->relate_typ);
        $query = $this->db->get($tableName);
        if ($query->num_rows() > 0)
        {

            $result = $query->row_array();
            if (isset($result[$showField])){
                $this->showValue = $result[$showField];
                $this->setCache($tableName,$result[$valueField],$this->showValue);
            }
            $this->value_checked = 1;

        } else {
            $this->showValue = '[未知(id:'.$value.')]';
            $this->value_checked = -1;
        }
    }

    public function genEnum(){

        $tableName = $this->tableName[$this->relate_typ];
        $valueField = $this->valueField[$this->relate_typ];
        $showField = $this->showField[$this->relate_typ];

        $this->db->select("{$valueField},{$showField}");
        $this->checkWhere();

        $this->db->order_by(array($showField => 'ASC'))->limit(50);
        $query = $this->db->get($tableName);

        $this->enum[0] = ' - ';
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $this->enum["".$row[$valueField]] = $row[$showField];
            }

        }

    }

    public function gen_editor($typ=0){
        $inputName = $this->build_input_name($typ);
        $validates = $this->build_validator();
        $this->genEnum();
        if ($typ==1){
            $this->default = $this->value;
        }

        $editor = "<select id=\"{$inputName}\" name=\"{$inputName}\" class=\"{$this->input_class}\" value=\"{$this->default}\" $validates>";

        foreach ($this->enum as $key => $value) {
            $editor.= "<option ". (($key==$this->default)?'selected="selected"':'') ." value=\"$key\">$value</option>";
        }
        $editor .= "</select>";
        return $editor;
    }
    public function gen_search_result_show($value){
        return $this->enum[$value];
    }
    public function check_data_input($input)
    {
        if ($input===0){
            return false;
        }
        return parent::check_data_input($input);
    }

    public function isEmpty(){
        if ($this->value==0 || $this->value==""){
            return true;
        }
        //检查数据有效性，看情况再加
        return false;
    }
}
?>
