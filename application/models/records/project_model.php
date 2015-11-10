<?php
include_once(APPPATH."models/record_model.php");
class Project_model extends Record_model {
    public function __construct() {
        parent::__construct('sProject');
        $this->title_create = '项目';
        $this->deleteCtrl = 'project';
        $this->deleteMethod = 'doDel/project';
        $this->edit_link = 'project/edit/project/';
        $this->short_info_link = $this->info_link = 'project/info/project/';
//服务简述（解决方法）	问题描述（故障详细描述）	服务建议	一级现象	二级现象	有无故障码	故障码代码
//	故障码内容	配件ID

        $this->field_list['_id'] = $this->load->field('Field_mongoid',"id","_id");

        $this->field_list['name'] = $this->load->field('Field_title',"项目","name",true);
        $this->field_list['desc'] = $this->load->field('Field_text',"描述","desc");

    }

    public function gen_list_html($templates){
        $msg = $this->load->view($templates, '', true);
    }
    public function gen_editor(){

    }

    public function buildInfoTitle(){
        return '项目 :'.$this->field_list['name']->gen_show_html();
    }

    public function buildChangeShowFields(){
            return array(
                    array('name'),
                    array('desc'),

                );
    }

    public function buildDetailShowFields(){
        return array(
                    array('name'),
                    array('desc'),

                );
    }

    public function do_delete_related($id){
        //用户表，清除店长

    }


    public function inc_counter(){
        $this->db->where(array('_id'=>new MongoId($this->id)))->increment($this->tableName,array('hitCounter'=>1));
    }

}
?>
