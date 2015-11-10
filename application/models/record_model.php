 <?php
 class Record_model extends CI_Model {
    public $id;
    public $field_list;
    public $tableName;
    public $orgId;
    public $deleteCtrl = '';
    public $deleteMethod = '';
    public $edit_link = '';
    public $info_link = '';
    public $id_is_id = true;//id字段是mongoid对象还是字符串
    public $none_field_data = array();
    public $is_inited = false;
    public $data = array();
    public $is_only_brief_fields = false;
    public $brief_fields = array();
    public $has_changelog = false;


    public function __construct($tableName='') {

        parent::__construct();
        $CI =& get_instance();
        if (DB_TYPE=="MYSQL"){
            $this->db = $CI->db;
        } else {
            $this->db = $CI->cimongo;
        }
        // $this->db = $CI->cimongo;
        $this->title_create= $tableName;
        $this->tableName = $tableName;
        $this->field_list = array();
        $this->orgId = 0;
        $this->errData = '';
        $this->relateTableName = array();
        $this->relateIdName = 'null';

        $this->default_is_lightbox_or_page = true;
        $this->lastError = array('err'=>false,'errNo'=>0,'id'=>"",'msg'=>"");

    }
    public function init($id){
        $this->id = $id;

    }

    public function write_changelog($typ,$data,$changelog){
        //基类等继承
    }

    public function do_write_create_changelog($data,$changelog,$logDataNew){
        $logData = array();

        $logData['solution'] = ($changelog===false)?'':$changelog;
        if (isset($data['projectId'])){
            $logData['projectId'] = $data['projectId'];
        }
        if (isset($data['versionId'])){
            $logData['versionId'] = $data['versionId'];
        }
        if (isset($data['dueUser'])){
            $logData['dueUser'] = $data['dueUser'];
        }

        $logData['typ'] = 1;
        $logData['beginTS'] = time();
        $logData['_id'] = new MongoId();

        foreach ($logDataNew as $key => $value) {
            //覆盖默认值
            $logData[$key] = $value;
        }

        $this->db->insert('pChangelog', $logData);
    }

    public function do_write_update_changelog($data,$changelog,$logDataNew){
        $logData = array();

        $logData['solution'] = ($changelog===false)?'':$changelog;
        if (isset($this->field_list['projectId'])){
            $logData['projectId'] = $this->field_list['projectId']->value;
        }
        if (isset($this->field_list['versionId'])){
            $logData['versionId'] = $this->field_list['versionId']->value;
        }
        if (isset($this->field_list['dueUser'])){
            $logData['dueUser'] = $this->field_list['dueUser']->value;
        }

        $logData['typ'] = 1;
        $logData['beginTS'] = time();
        $logData['_id'] = new MongoId();

        foreach ($logDataNew as $key => $value) {
            //覆盖默认值
            $logData[$key] = $value;
        }
        $this->db->insert('pChangelog', $logData);
    }

    public function gen_url($key_names,$force_lightbox=false,$info_link=''){
        if ($info_link=='') {
            $info_link = $this->info_link;
        }

        if ($info_link==''){
            //报错
        }
        if ($this->default_is_lightbox_or_page) {
            return '<a href="javascript:void(0)" onclick="lightbox({url:\''. site_url($info_link.'/'.$this->id).'\'})">'.$this->field_list[$key_names]->gen_list_html().'</a>';
        } else {
            return '<a href="'. site_url($info_link.'/'.$this->id).'">'.$this->field_list[$key_names]->gen_list_html().'</a>';
        }
    }



    public function fetchArray(){
        $arrayRst = array();
        foreach ($this->field_list as $key => $value) {
            $arrayRst[$key] = $value->value;
        }
    }
    public function setRelatedOrgId($orgId){
        $this->orgId = $orgId;
        foreach ($this->field_list as $key => $value) {
            $value->setOrgId($orgId);
        }
    }
    public function gen_list_html($templates){

    }
    public function gen_editor(){

    }

    public function gen_editor_title(){
        if ($this->editor_typ==0){
            return $this->title_create.' 新建';
        }else if ($this->editor_typ==1){
            $plus = "";
            if (isset($this->field_list['name'])){
                $plus = $this->field_list['name']->gen_show_value();
            }
            return $this->title_create.' 编辑 '.$plus ;
        }
    }

    public function buildInfoTitle(){

    }

    public function check_data($data,$strict=true){
        $effect = 0;
        $this->error_field = "";
        foreach ($this->field_list as $key => $value) {
            if ($value->is_must_input){
                if (!isset($data[$key])){
                    if ($strict){
                        $this->error_field = $key;
                        return false;
                    }

                }  elseif ($value->check_data_input($data[$key])==false) {
                    $this->error_field = $key;
                    return false;
                }
            }
        }
        return true;
    }

    public function get_error_field(){
        if (isset($this->error_field)){
            return $this->error_field;
        } else {
            return "";
        }
    }

    public function checkNameExist($name){
        $this->db->select('*')
                    ->from($this->tableName)
                    ->where('name', $name);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return true;
        } else {
            return false;
        }
    }

    public function init_with_foreignId($foreignKey,$foreignId){

        $this->db->where(array($foreignKey => $foreignId));
        $this->checkWhere();

        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
            $this->init_with_data($result['_id'],$result);
            return true;
        } else {
            return false;
        }
    }

    public function init_with_where($whereArr){

        $this->db->where($whereArr);
        $this->checkWhere();

        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
            $this->init_with_data($result['_id'],$result);
            return true;
        } else {
            return false;
        }
    }

    public function reset(){
        foreach ($this->field_list as $key => $value) {
            $this->field_list[$key]->init("");
        }
    }

    public function init_with_id($id){
        if (!is_object($id) && $this->id_is_id){
            try{
                $real_id = new MongoId($id);
            } catch(Exception $e) {
                echo "========================error init_with_id===============================";
                var_dump($id,$e->getMessage(),$e->getTrace());
                echo "===============================end=======================================";
            }

        } else {
            $real_id = $id;
        }
        $this->db->where(array('_id' => $real_id));
        $this->checkWhere();

        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
            $this->init_with_data($result['_id'],$result);
            return true;
        } else {
            return false;
        }
    }

    public function init_with_data($id,$data,$isFullInit=true){
        if (!is_object($id)){
            $this->id = $id;
        } else {
            $this->id = $id->{'$id'};
        }

        $this->data = $data;

        foreach ($data as $key => $value) {
            $is_inited = false;
            if (isset($this->field_list[$key])){
                //简易版初始化，只初始化部分字段
                if ($this->is_only_brief_fields && !in_array($key,$this->brief_fields)){
                    $this->field_list[$key]->baseInit($value);
                    continue;
                }
                if ($isFullInit) {
                    $this->field_list[$key]->init($value);
                } else {
                    $this->field_list[$key]->baseInit($value);
                }
            }
        }
        foreach ($this->field_list as $key => $value) {
            if (!isset($this->data[$key])){
                $this->data[$key] = $value->value;
            }
        }
        if (isset($data['name'])){
            $this->name = $data['name'];
        }
        $this->is_inited = true;
    }

    public function init_with_part_data($data){
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;

            if (isset($this->field_list[$key])){
                $this->field_list[$key]->init($value);
            }
        }

        if (isset($data['name'])){
            $this->name = $data['name'];
        }
    }

    public function gen_op_view(){

    }
    public function gen_op_edit(){
        return '<a class="list_op tooltips" onclick="lightbox({size:\'m\',url:\''.site_url($this->edit_link).'/'.$this->id.'\'})" title="编辑"><span class="glyphicon glyphicon-edit"></span></a>';

    }
    public function gen_op_delete(){
        return '<a class="list_op tooltips" onclick=\'reqDelete("'.$this->deleteCtrl.'","'.$this->deleteMethod.'","'.$this->id.'")\' title="删除"><span class="glyphicon glyphicon-trash"></span></a>';

    }

    public function gen_op_pass(){
        return '<a class="list_op tooltips" onclick=\'reqOperator("'.$this->deleteCtrl.'","doPass","'.$this->id.'")\' title="删除"><span class="glyphicon glyphicon-trash"></span></a>';

    }

    public function get_list_ops($limits=''){
        $allow_ops = array();

        $allow_ops[] = 'edit';
        $allow_ops[] = 'delete';
        return $allow_ops;
    }

    public function get_info_ops($limits=''){
        return array('edit','delete');
    }

    public function gen_list_op($limits=''){
        $opList = $this->get_list_ops($limits);
        $strs = array();
        foreach ($opList as $op) {
            $func = "gen_op_".$op;
            $strs[] = $this->$func();
        }
        return implode(" | ", $strs);
    }

    public function insert_db($data){
        if (isset($this->field_list['_id']) && $this->field_list['_id']->typ == "Field_mongoid") {
            if (!isset($data['_id'])) {
                //补充_id 字段
                $data['_id'] = new MongoId();
                if (isset($this->field_list['showId']) && !isset($data['showId'])){
                    $data['showId'] = strtoupper(substr(md5((string)$data['_id']),-8));
                }
            }
        }
        $this->db->insert($this->tableName, $data);
        $id = $this->db->insert_id();
        $this->id = (string)$id;
        return $id;
    }

    public function delete_db($ids){
        $effect = 0;
        $idArray = explode('-',$ids);
        foreach ($idArray as $id) {
            $this->db->where(array('_id'=> new MongoId($id)))->delete($this->tableName);
            $effect += 1;
        }
        return $effect;
    }

    public function delete_db_where($where_array){
        $this->db->where($where_array)->delete($this->tableName);
    }

    public function push_db($id,$fieldName,$data){
        if (!is_object($id) && $this->id_is_id){
            $real_id = new MongoId($id);
        } else {
            $real_id = $id;
        }

        $this->db->where(array('_id'=>$real_id))
                ->push($fieldName, $data)
                ->update($this->tableName);
    }

    public function pull_db($id,$fieldName,$data){
        if (!is_object($id) && $this->id_is_id){
            $real_id = new MongoId($id);
        } else {
            $real_id = $id;
        }
        // pull('comments', array('comment_id'=>123))
        $this->db->where(array('_id'=>$real_id))
                ->pull($fieldName, $data)
                ->update($this->tableName);
    }

    public function pull_db_id($id,$fieldName,$sub_id){
        if (!is_object($id) && $this->id_is_id){
            $real_id = new MongoId($id);
        } else {
            $real_id = $id;
        }
        if (!is_object($sub_id) && $this->id_is_id){
            $real_sub_id = new MongoId($sub_id);
        } else {
            $real_sub_id = $sub_id;
        }

        $this->db->where(array('_id'=>$real_id))
                ->pull($fieldName, array('_id'=>$real_sub_id))
                ->update($this->tableName);
    }


    public function check_can_delete(){
        return true;
    }

    public function delete_related($ids){
        if ($this->relateIdName=='null'){
            return;
        }
        $effect = 0;
        $idArray = explode('-',$ids);
        foreach ($idArray as $id) {
            foreach ($this->relateTableName as $thisTableName){
                $this->db->where(array($this->relateIdName=> $id))->delete($thisTableName);
                $effect += 1;
            }
        }
        return $effect;
    }


     public function update_db($data,$id=null){
        if ($id==null){
            $id = $this->id;
        }
        if (!is_object($id) && $this->id_is_id){
            $real_id = new MongoId($id);
        } else {
            $real_id = $id;
        }

        $this->db->where(array('_id'=>$real_id))->update($this->tableName,$data);
        return true;
    }

    public function update_db_by_where($data,$where_array){

       $this->db->where($where_array)->update_batch($this->tableName,$data);
       return true;
   }


    public function genShowId($orgId,$typ){
         $this->db->select('*')
                    ->from('oMaxIds')
                    ->where('orgId', $orgId);

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
        }
        else
        {
            $this->db->insert('oMaxIds', array("orgId"=>$orgId));
            $result = array("orgId"=>$orgId);
        }
        //处理年份
        if (!isset($result['lastModifyTs'])){
            $result['lastModifyTs'] = 0;
        }

        $zeit  = time();
        $now_year = date('Y',$zeit);
        $last_modify_year = date('Y',$result['lastModifyTs']);

        if ($now_year > $last_modify_year) {
            $result[$typ] = 0;
        }

        if (!isset($result[$typ])){
            $update[$typ] = 1;
        } else {

            $update[$typ] = $result[$typ]+1;
        }
        $update["lastModifyTs"] = $zeit;
        $this->db->where('orgId', $orgId)->update('oMaxIds',$update);

        return $now_year . sprintf("%06d",$update[$typ]);

    }

    function checkImportDataBase($data,$cfg_field_lists){
        $errorData = array();
        foreach ($data as $key => $value) {
            # code...
            if (!isset($cfg_field_lists[$key])) {
                continue;
            }
            $rst = $this->field_list[$cfg_field_lists[$key]]->checkImportData($value);
            if ($rst<=0) {
                $errorData[$this->field_list[$cfg_field_lists[$key]]->gen_show_name()] = $value;
            }
        }
        return $errorData;
    }

    function checkIdBy($param){
        $this->db->select("id")
            ->from($this->tableName)
            ->where($param);
        // $this->checkWhere();

        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
            return $result["id"];
        } else {
            return -1;
        }
    }

    function checkWhere(){

    }
    public function buildChangeNeedFields($arr_plus = array()){
        $array = $arr_plus;
        foreach ($this->buildChangeShowFields() as $value) {
            foreach ($value as $v) {
                if ($v=='null'){
                    continue;
                }
                $array[] = $v;
            }
        }
        return $array;
    }

    public function setError($errNo,$msg,$id=""){
        $this->lastError['err'] = true;

        $this->lastError['errNo'] = $errNo;
        $this->lastError['id'] = $id;
        $this->lastError['msg'] = $msg;
    }

    public function getLastError(){
        if (!$this->lastError['err']){
            return array('errNo'=>0,'msg'=>'未知错误');
        }
        return array('errNo'=>$this->lastError['errNo'],'id'=>$this->lastError['id'],'msg'=>$this->lastError['msg']);
    }

}
?>
