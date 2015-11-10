<?php
include_once(APPPATH."models/record_model.php");
class Department_model extends Record_model {
    public function __construct() {
        parent::__construct('gDepartment');
        $this->uname = '';
        $this->uid = 0;

        $this->deleteCtrl = 'admin';
        $this->deleteMethod = 'doDel/department/';
        $this->edit_link = 'admin/edit/department/';
        $this->short_info_link = $this->info_link = 'admin/info/department/';


        $this->field_list['_id'] = $this->load->field('Field_mongoid',"id","_id");
        $this->field_list['name'] = $this->load->field('Field_string',"部门名","name",true);

        $this->field_list['manager'] = $this->load->field('Field_userid',"头目","manager");
        $this->field_list['manager']->add_where(WHERE_TYPE_WHERE_GTE,'isManager',1);
    }

    public function gen_list_html($templates){
        $msg = $this->load->view($templates, '', true);
    }
    public function gen_editor(){

    }

    public function buildInfoTitle(){
        return '部门 :'.$this->field_list['name']->gen_show_html();
    }

    public function buildChangeShowFields(){
            return array(
                    array('name'),
                    array('manager'),

                );
    }

    public function buildDetailShowFields(){
        return array(
                    array('name'),
                    array('manager'),

                );
    }

    public function do_delete_related($id){

    }

    public function get_list_ops(){
        $allow_ops = parent::get_list_ops();

        return $allow_ops;
    }

    public function inc_counter(){
        $this->db->where(array('_id'=>new MongoId($this->id)))->increment($this->tableName,array('hitCounter'=>1));
    }


}
?>
