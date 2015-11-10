<?php
include_once(APPPATH."models/record_model.php");
class Org_model extends Record_model {
    public function __construct() {
        parent::__construct('oOrg');
        $this->title_create = '创建门店';
        $this->deleteCtrl = 'amanagement';
        $this->deleteMethod = 'doDeleteOrg';
        $this->edit_link = 'amanagement/editOrg/';
        $this->short_info_link = $this->info_link = 'amanagement/infoOrg/';

        $this->field_list['_id'] = $this->load->field('Field_mongoid',"id","_id");
        $this->field_list['name'] = $this->load->field('Field_title',"门店名称","name",true);
        $this->field_list['logo'] = $this->load->field('Field_pic',"logo","logo");

        $this->field_list['status'] = $this->load->field('Field_enum',"状态","status");
        $this->field_list['status']->setEnum(array('正常','测试'));
        $this->field_list['score'] = $this->load->field('Field_float',"总体评分","score");
        $this->field_list['hj_score'] = $this->load->field('Field_float',"环境评分","hj_score");
        $this->field_list['kh_score'] = $this->load->field('Field_float',"客服评分","kh_score");
        $this->field_list['hj_images'] = $this->load->field('Field_array_pics',"环境图片","hj_images");

        $this->field_list['hj_images']->setimgCountLimit(5);



        $this->field_list['zhuanxiu_pinpai'] = $this->load->field('Field_array_pinpai',"支持品牌","zhuanxiu_pinpai");
        $this->field_list['zhuanxiu_pinpai']->add_where(WHERE_TYPE_WHERE,'inList',1);
        $this->field_list['projects'] = $this->load->field('Field_tag',"支持项目","projects");
        $this->field_list['projects']->setEnum(array(0=>'无法确定',10=>'维修类',20=>'保养类',30=>'美容装潢类',40=>'事故钣金喷漆'));

        $this->field_list['kefus'] = $this->load->field('Field_array_plain',"通知微信id","kefus");
$this->field_list['kefus']->tips = '请让门店人员先在微信端注册绑定，然后在 PC 后台用户订单管理内搜索该用户，可以查看到用户的微信 id，然后拷贝到这里，这边的格式是["A","B","C"]';
             // 'projects' =>array('美容装潢','故障维修','保养','轮胎','改装'),
        $this->field_list['beginTS'] = $this->load->field('Field_date',"成立时间","beginTS");
        $this->field_list['addresses'] = $this->load->field('Field_string',"地址","addresses");
        $this->field_list['phone'] = $this->load->field('Field_string',"电话","phone");
        $this->field_list['qq'] = $this->load->field('Field_string',"QQ","qq");
        $this->field_list['weixin'] = $this->load->field('Field_string',"微信","weixin");

        $this->field_list['desc'] = $this->load->field('Field_text',"门店介绍","desc");
        $this->field_list['supperUid'] = $this->load->field('Field_adminuserid',"店长","supperUid");
        $this->field_list['supperUid']->set_typ(10);

        $this->field_list['createUid'] = $this->load->field('Field_adminuserid',"创建人","createUid");
        $this->field_list['createTS'] = $this->load->field('Field_ts',"创建时间","createTS");
        $this->field_list['lastModifyUid'] = $this->load->field('Field_adminuserid',"最终编辑人","lastModifyUid");
        $this->field_list['lastModifyTS'] = $this->load->field('Field_ts',"最终编辑时间","lastModifyTS");
    }

    public function gen_list_html($templates){
        $msg = $this->load->view($templates, '', true);
    }
    public function gen_editor(){

    }

    public function buildInfoTitle(){
        return '门店 :'.$this->field_list['name']->gen_show_html();
    }

    public function buildChangeShowFields(){
            return array(
                    array('name','status'),
                    array('addresses'),
                    array('desc'),
                    array('phone','qq'),
                    array('score','null'),
                    array('kh_score','hj_score'),
                    array('zhuanxiu_pinpai'),
                    array('projects'),
                    array('kefus'),


                    array('supperUid'),

                );
    }

    public function buildDetailShowFields(){
        return array(
                    array('name','status'),
                    array('addresses'),
                    array('desc'),
                    array('phone','qq'),
                    array('score','null'),
                    array('kh_score','hj_score'),
                    array('zhuanxiu_pinpai'),
                    array('projects'),
                    array('supperUid'),

                );
    }

    public function do_delete_related($id){
        //用户表，清除店长

    }

    public function init_with_show_id($showId){
        $this->db->where(array('showid' => $showId));
        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
            $this->init_with_data($result['_id'],$result);
            return true;
        } else {
            return $this->init_with_id($showId);
        }
    }


}
?>
