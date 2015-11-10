<?php

class List_model extends CI_Model {
    public $name;
    public $record_list;
    public $quickSearchWhere;
    public $orderKey = array("_id"=>"desc");
    public $paged = false;
    public $perPage = 20;
    public $nowPage = 0;
    public $is_only_brief_fields = false;
    public $skip = 0;
    public $limit = 2000;
    public $op_limit = "";
    public $all_record_counts = 0;


    public function __construct($tableName = '') {

        parent::__construct();
        $CI =& get_instance();
        if (DB_TYPE=="MYSQL"){
            $this->db = $CI->db;
        } else {
            $this->db = $CI->cimongo;
        }
        $this->tableName = $tableName;
        $this->record_list = array();
        $this->whereData = array();
        $this->whereOrgId = null;
        $this->quickSearchWhere = array("name");
        $this->is_lightbox = true;
        $this->record_count = 0;
    }

    public function setOrgId($orgId) {
        $this->whereOrgId = $orgId;
        foreach ($this->dataModel as $key => $value) {
            $value->setOrgId($orgId);
        }
    }
    public function init($name,$dataModelName){
        $this->name = $name;
        $this->dataModelName = $dataModelName;

        $dataModel = new $dataModelName();

        $this->dataModel = $dataModel->field_list;

    }
    public function init_with_relate_id($relateField,$relateId){

    }

    public function purge_where(){
        $this->whereData = array();
    }
    public function add_where($typ,$name,$data){
        $this->whereData[] = array('typ'=>$typ,'name'=>$name,'data'=>$data);
    }
    public function build_where($typ,$name,$data){
        if (is_numeric($data)){
            $data = (float)$data;
        }

        switch ($typ) {
            case '=':
                $this->add_where(WHERE_TYPE_WHERE,$name,$data);
                break;
            case 'like':
                $this->add_where(WHERE_TYPE_LIKE,$name,$data);
                break;
            case '>':
                $this->add_where(WHERE_TYPE_WHERE_GT,$name,$data);
                break;
            case '<':
                $this->add_where(WHERE_TYPE_WHERE_LT,$name,$data);
                break;
            case '>=':
                $this->add_where(WHERE_TYPE_WHERE_GTE,$name,$data);
                break;
            case '<=':
                $this->add_where(WHERE_TYPE_WHERE_LTE,$name,$data);
                break;
            case '!=':
                $this->add_where(WHERE_TYPE_WHERE_NE,$name,$data);
                break;
            default:
                print("-----------------<br/>");
                var_dump($typ,$name,$data);
                print("-----------------<br/>");
                # code...
                break;
        }
    }


    public function add_quick_search_where($info) {
        $regex = new MongoRegex("/$info/iu");

        $array = array();
        if (count($this->quickSearchWhere)<=0){
            return;
        }
        foreach ($this->quickSearchWhere as $value) {
            if (count($this->quickSearchWhere)==1){
                $this->add_where(WHERE_TYPE_WHERE,$value,$regex);
            } else {
                $this->add_where(WHERE_TYPE_OR_WHERE,$value,$regex);
            }

        }

        // $this->db->where(array('$or'=>$array),true);
    }

    public function load_data_with_search($searchInfo){
        if ($searchInfo['t']=="no") {
            $this->load_data_with_where(0);
        } elseif ($searchInfo['t']=="quick"){

            $this->add_quick_search_where($searchInfo['i']);

            $this->load_data_with_where(0);
        } elseif ($searchInfo['t']=="full"){
            foreach ($searchInfo['i'] as $key => $value) {
                $this->build_where($value['e'],$key,$this->dataModel[$key]->gen_search_result_id($value['v']));
            };
            $this->load_data_with_where(0);
        }
    }

    public function build_where_with_search($searchInfo){
        if ($searchInfo['t']=="no") {
        } elseif ($searchInfo['t']=="quick"){

            $this->add_quick_search_where($searchInfo['i']);

        } elseif ($searchInfo['t']=="full"){
            foreach ($searchInfo['i'] as $key => $value) {
                $this->build_where($value['e'],$key,$this->dataModel[$key]->gen_search_result_id($value['v']));
            };
        }
    }

    public function load_data_with_in_array($field_name,$array){
        $this->add_where(WHERE_TYPE_IN,$field_name,$array);
        $this->load_data_with_where();
    }

    public function load_data_with_fullSearchMultiField($fields,$info){
        $regex = new MongoRegex("/$info/iu");

        $array = array();
        if (count($fields)<=0){
            return;
        }
        foreach ($fields as $value) {
            $array[] = array($value=>$regex);
        }

        $this->db->where(array('$or'=>$array),true);
        $this->load_data_with_where();

    }
    public function load_data_with_fullSearch($field_name,$where_array,$plus_where = array(),$limit = 5){
        $where_clause = array();


        if ($this->whereOrgId!==null && isset($this->dataModel['orgId'])){
            $where_clause['orgId'] = $this->whereOrgId;
        }
        if (count($plus_where)!=0){
            foreach ($plus_where as $key => $value) {
                $where_clause[$key] = $value;
            }
        }
        if (count($where_array)!=0){
            $where_clause['$or'] = array();
            foreach ($where_array as $value) {
                $value = (string) trim($value);
                $value = quotemeta($value);
                $where_clause['$or'][] = array($field_name => new MongoRegex("/$value/i"));
            }
        }

        $this->db->where($where_clause, TRUE);

        $this->db->order_by($this->orderKey);
        if ($this->limit>0){
            $this->db->limit($this->limit);
        }
        if ($this->skip>0){
            $this->db->skip($this->skip);
        }

        $query = $this->db->get($this->tableName);

        $num = $query->num_rows();
        $this->record_count = $num;
        if ($num > 0)
        {
            $this->_fill_data_with_query($query);
            return $num;
        } else {
            return 0;
        }


    }

    private function _fill_data_with_query($query){
        foreach ($query->result_array() as $row)
        {
            if (is_object($row['_id'])){
                $id = (string)$row['_id'];
            } else {
                $id = $row['_id'];
            }

            $this->record_list[$id] = new $this->dataModelName();
            $this->record_list[$id]->is_only_brief_fields = $this->is_only_brief_fields;
            $this->record_list[$id]->orgId = $this->whereOrgId;
            $this->record_list[$id]->init_with_data($row['_id'],$row);
        }
    }

    public function load_data_with_orignal_where($where_array=array(),$limit=1000){

        $this->db->where($where_array, TRUE);

        $this->db->order_by($this->orderKey);
        if ($this->limit>0){
            $this->db->limit($this->limit);
        }
        if ($this->skip>0){
            $this->db->skip($this->skip);
        }

        $query = $this->db->get($this->tableName);

        $num = $query->num_rows();
        $this->record_count = $num;
        if ($num > 0)
        {
            $this->_fill_data_with_query($query);
            return $num;
        } else {
            return 0;
        }


    }

    public function check_where($where_array){
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
                    $this->db->like($fieldName,$fieldData,'iu');
                    break;
            }
        }
    }

    public function load_data_with_where($where_array=0){
        //临时
        if ($where_array===0){
            $where_array = $this->whereData;
        }
        $this->check_where($where_array);

        if ($this->whereOrgId!==null && isset($this->dataModel['orgId'])){
            // $this->db->where(array('orgId'=>$this->whereOrgId));
        }

        $this->db->order_by($this->orderKey);


        if ($this->limit>0){
            $this->db->limit($this->limit);
        }
        if ($this->skip>0){
            $this->db->skip($this->skip);
        }
        if ($this->paged){
            $start = ($this->nowPage-1)*$this->perPage;
            $this->db->skip($start);
        }

        $query = $this->db->get($this->tableName);

        $this->all_record_counts = $this->db->count_all_results($this->tableName);
        $num = $query->num_rows();
        // var_dump($num);
        $this->record_count = $num;
        if ($num > 0)
        {
            $counter = 0;
            $this->_fill_data_with_query($query);
            return $num;
        } else {
            return 0;
        }


    }

    public function load_data($limit=0){
        $this->purge_where();
        $this->load_data_with_where(0);
    }

    public function load_data_with_foreign_key($keyName,$keyValue){
        $this->purge_where();
        $this->add_where(WHERE_TYPE_WHERE,$keyName,$keyValue);
        $this->load_data_with_where(0);
    }

    public function load_data_with_data($data,$dataModelName){
        foreach ($data as $row)
        {
            if (is_object($row['_id'])){
                $id = (string)$row['_id'];
            } else {
                $id = $row['_id'];
            }

            $this->record_list[$id] = new $dataModelName();
            $this->record_list[$id]->orgId = $this->whereOrgId;
            $this->record_list[$id]->init_with_data($row['_id'],$row);
        }
        $this->record_count = count($data);
    }

    public function gen_id_array(){
        return array_keys($this->record_list);
    }

    public function gen_value_array($field){
        $return = array();
        foreach ($this->record_list as $id=>$this_record)
        {
            $return[] = $this_record->field_list[$field]->value;
        }
        return $return;
    }

    public function update_db($data){


       $this->db->where(array('_id'=>$real_id))->update($this->tableName,$data);
       return true;
   }
}
?>
