<?php
include_once(APPPATH."models/fields/field_mongoid.php");
class Field_relate_simple_id extends Field_mongoid {
    public $where = array();

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_related_id";
        $this->tableName = '';
        $this->valueField = '';
        $this->showField = '';
        $this->whereData = array();
        $this->valueSetted = false;
        $this->showValue = ' - ';
        $this->enum = array();
        $this->needOrgId = 1;
        $this->whereOrgId = '';
        $this->relate_id_is_id = true;
        $this->value_checked = 0;
        $this->is_link = true;
        $this->relateToProject = 0;//1 ==; 2 in array
        $this->projectFieldName = 'projectId';
    }

    public function baseInit($value){
        parent::init($value);

    }

    public function checkCache($id){
        global $cache_relate_id;
        $id = (string)$id;
        if (!isset($cache_relate_id[$this->tableName])){
            $cache_relate_id[$this->tableName] = array();
        }
        if (!isset($cache_relate_id[$this->tableName][$id])){
            return null;
        } else {
            return $cache_relate_id[$this->tableName][$id];
        }
    }

    public function setCache($id,$value){
        global $cache_relate_id;
        $id = (string)$id;
        if (!isset($cache_relate_id[$this->tableName])){
            $cache_relate_id[$this->tableName] = array();
        }
        $cache_relate_id[$this->tableName][$id] = $value;
    }

    public function setWhere($arr){
        $this->whereData = $arr;
    }

    public function init($value){

        parent::init($value);


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


        $this->db->select(array($this->valueField,$this->showField))
            ->where(array($this->valueField => $real_value))
            ->limit(1);

        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {

            $result = $query->row_array();
            if (isset($result[$this->showField])){
                $this->showValue = $result[$this->showField];
                $this->setCache($value,$this->showValue);
            }
            $this->value_checked = 1;

        } else {
            $this->showValue = '[未知(id:'.$value.')]';
            $this->value_checked = -1;
        }
    }
    public function gen_list_html(){
        return $this->gen_show_html();
    }
    public function set_relate_db($tableName,$valueField,$showField){
        $this->tableName = $tableName;
        $this->valueField = $valueField;
        $this->showField = $showField;
    }

    public function add_where($typ,$name,$data){
        $this->whereData[] = array('typ'=>$typ,'name'=>$name,'data'=>$data);
    }

    public function checkWhere(){
        $where_array = $this->whereData;
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

        if ($this->relateToProject==1 && !$this->CI->currentProjectId->isEmpty()){
            $this->db->where(array($this->projectFieldName =>$this->CI->currentProjectId->value));
        } elseif ($this->relateToProject==2){
            $this->db->where_in($this->projectFieldName,$this->CI->userInfo->field_list['projects']->value);
        }
    }
    public function genEnum(){
        $this->db->select("{$this->valueField},{$this->showField}");
        $this->db->removeWhere();
        $this->checkWhere();
        $this->db->order_by(array($this->showField => 'ASC'))->limit(50);
        $query = $this->db->get($this->tableName);

        $this->enum[0] = ' - ';
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $this->enum["".$row[$this->valueField]] = $row[$this->showField];
            }

        }
        if ($this->is_fullInited){
            $this->enum["".$this->value] = $this->showValue;
        }

    }
    public function gen_search_element($default="="){
        return "<input type='hidden' name='searchEle_{$this->name}' id='searchEle_{$this->name}' value='='>=";
    }
    public function gen_show_html(){
        $str = $this->showValue;

        if ($this->is_link){
            $url = site_url($this->url.'/'.$this->value);
            if ($this->is_link_lightbox){
                $str = '<a href="javascript:void(0);" onclick="lightbox({size:\'m\',url:\''.$url.'\'})">'.$str;
            } else {
                $str = '<a href="'.$url.'">'.$str;
            }
            $str .= '</a>';

        }
        return $str;
    }
    public function gen_show_value(){
        return $this->showValue;
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
        if($this->is_must_input){
            if ($input==="0"){
                return false;
            }
        }
        return parent::check_data_input($input);
    }
    public function build_validator(){
        if ($this->is_must_input){
        }
        $validater .= Fields::build_validator();
        return $validater;
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
