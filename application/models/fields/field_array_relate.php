<?php
include_once(APPPATH."models/fields/fields.php");

class Field_array_relate extends Fields {

    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array_relate";
        $this->tableName = '';
        $this->valueField = '';
        $this->showField = '';
        $this->whereData = array();
        $this->valueSetted = false;
        $this->showValue = ' - ';
        $this->enum = array();
        $this->needOrgId = 1;
        $this->relate_id_is_id = true;
        $this->value_checked = 0;
        $this->value = array();
        $this->id_lists = array();
        $this->relate_id_is_id = true;

        $this->linked_lists = null;
    }
    public function init($value){
        if (!is_array($value)){
            return;
        }
        $this->value = $value;
        //预处理 id 列表
        foreach ($this->value as $key => $value) {
            if (!is_object($value) && $this->relate_id_is_id){
                $real_value = new MongoId($value);
            } else {
                $real_value = $value;
            }
            $this->id_lists[] = $real_value;
        }

        if (count($this->id_lists)==0){
            return;
        }
        //查询
        $this->db->select(array($this->valueField,$this->showField))
            ->where_in($this->valueField,$this->id_lists);
        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                if (is_object($row[$this->valueField])){
                    $id = (string)$row[$this->valueField];
                } else {
                    $id = $row[$this->valueField];
                }

                $this->enum[$id] = $row[$this->showField];
            }

            $this->value_checked = 1;
        } else {
            $this->value_checked = -1;
        }
    }

    public function set_relate_db($tableName,$valueField,$showField){
        $this->tableName = $tableName;
        $this->valueField = $valueField;
        $this->showField = $showField;
        $this->listFields = array('relate_'.$this->tableName);
        $this->mustFields = array('relate_'.$this->tableName);

    }

    public function gen_search_result_show($value){
        return $this->enum[$value];
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
                case WHERE_TYPE_NOT_IN:
                    $this->db->where_not_in($fieldName,$fieldData);
                    break;
                case WHERE_TYPE_LIKE:
                    $this->db->like($key, $value['data'],'iu');
                    break;
            }
        }
        if ($this->whereOrgId>0 && $this->needOrgId==1){
            $this->db->where('orgId', $this->whereOrgId);
        } elseif ($this->whereOrgId>0 && $this->needOrgId==2){
            $this->db->where_in('orgId',array($this->whereOrgId,0));
        }
    }

    public function setEnum(){
        $this->db->select("{$this->valueField},{$this->showField}");
        $this->checkWhere();

        $this->db->order_by(array($this->showField => 'ASC'))->limit(50);
        $query = $this->db->get($this->tableName);

        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $this->enum["".$row[$this->valueField]] = $row[$this->showField];
            }

        }

    }

    public function gen_input($typ){
        switch ($typ) {
            case 0:
                $prefix = "creator_";
                break;
            case 1:
                $prefix = "modify_";
                break;
            case 2:
                $prefix = "search_";
                break;
            default:
                $prefix ="";
                # code...
                break;
        }
        $inputName = $prefix .'relate_'.$this->tableName;

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
    public function gen_search_element($default="like"){
        return "<input type='hidden' name='searchEle_{$this->name}' id='searchEle_{$this->name}' value='like'>包含";
    }

    public function gen_list_html(){
        $string ='<ul class="list-group">';
        foreach ($this->value as $key => $value) {
            if (isset($this->enum[$value])){
                $real_value = $this->enum[$value];
            } else {
                $real_value = "未知";
            }
            $string .= '<li class="list-group-item">'.$real_value.'</li>';
        }
        $string .='</ul>';
        return $string;
    }
    public function gen_show_value(){
        $real_value = array();
        foreach ($this->value as $key => $value) {
            if (isset($this->enum[$value])){
                $real_value[] = $this->enum[$value];
            } else {
                $real_value[] = "未知";
            }
        }
        return implode(',', $real_value);
    }
    public function gen_show_html(){
        $string ='<ul class="list-group">';
        foreach ($this->value as $key => $value) {
            if (isset($this->enum[$value])){
                $real_value = $this->enum[$value];
            } else {
                $real_value = "未知";
            }
            $string .= '<li class="list-group-item">'.$real_value.'</li>';
        }
        $string .='</ul>';
        return $string;
    }



    public function setDefault($default){
        $this->default = $default;
        if ($this->default==false){
            $this->default = array();
        }
    }
    public function gen_editor($typ=0){
        $inputName = $this->build_input_name($typ);
        $validates = $this->build_validator();
        $this->setEnum();
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

    public function gen_value($input){

        $real_input = json_decode($input,true);
        $data = array();
        $inputName = 'relate_'.$this->tableName;
        foreach ($real_input as $key => $value) {

            $data[] = $value[$inputName];
        }
        return $data;
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
