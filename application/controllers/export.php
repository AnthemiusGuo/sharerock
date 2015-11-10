<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aexport extends P_Controller {
    function __construct() {
        parent::__construct(true,'a');
        $this->load->library('pagination');
    }

    function index($store='all',$from='',$to=''){
        $this->store = $store;
        if ($from==''){
            $this->from = date('Y-m-01');
        } else {
			$this->from = $from;
		}
        if ($to=='') {
			$this->to = date('Y-m-d');
		} else {
			$this->to = $to;
        }

        $this->admin_load_menus();
        $this->load_org_info();

        $this->quickSearchName = "订单号/姓名/电话";
        $this->buildSearch();

        $this->load->model('lists/Org_list',"allOrgList");
        $this->allOrgList->load_data();


        $this->template->load('adefault_page', 'aexport/index');
    }

    function share_peijian() {

        $this->load->model('lists/Common_list',"listInfo");

        $this->listInfo->setInfo('sPeijian','Peijian_list','Peijian_model');
        $this->listInfo->orderKey = array('typ'=>'asc');
        $this->listInfo->load_data();


        $fileName = BASEPATH."../wwwroot/templates/template_peijian.xlsx";
        $exportName = "exports/peijian-".date('Y')."-".date('m')."-".date('d').'-'.substr(md5($searchInfo.time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = '配件导出表 '.date('Y')."-".date('m')."-".date('d');

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
            $this_pinpai = '通用';
            if (!$this_record->field_list['chepinpai_tongyong']->toBool()){
                $showNames = array();
                foreach ($this_record->field_list['chepinpais']->value as $key => $value) {
                    $showNames[] = $pinpais[$value];
                }
                $this_pinpai = implode('，', $showNames);
            }
            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['_id']->toString());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['showId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_pinpai);
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['typ']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['name']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['pinpai']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['biaoshi']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['xinghao']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['baoxiu']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['jiage']->gen_show_value());
            $objWorksheet->getCell('K'.$i)->setValue($this_record->field_list['gongshi']->gen_show_value());
            $objWorksheet->getCell('L'.$i)->setValue($this_record->field_list['changyong']->gen_show_value());
            $objWorksheet->getCell('M'.$i)->setValue($this_record->field_list['jinjia']->gen_show_value());

            $i++;
        }

        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    function share_service() {

        $this->load->model('lists/Common_list',"pinpaiList");

        $this->pinpaiList->setInfo('sCarPinpai','Carpinpai_list','Carpinpai_model');
        $this->pinpaiList->load_data();

        $pinpais = array();

        foreach ($this->pinpaiList->record_list as $key => $this_record) {
            $pinpais[$this_record->id] = $this_record->field_list['name']->gen_show_value();
        }


        $this->load->model('lists/Common_list',"listInfo");

        $this->listInfo->setInfo('sService','Service_list','Service_model');
        $this->listInfo->orderKey = array('typ'=>'asc','changyong'=>'desc');
        $this->listInfo->load_data();


        $fileName = BASEPATH."../wwwroot/templates/template_service.xlsx";
        $exportName = "exports/服务详情-".date('Y')."-".date('m')."-".date('d').'-'.substr(md5($searchInfo.time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();


        $title = '服务导出表 '.date('Y')."-".date('m')."-".date('d');

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
            $this_pinpai = '通用';
            if (!$this_record->field_list['chepinpai_tongyong']->toBool()){
                $showNames = array();
                foreach ($this_record->field_list['chepinpais']->value as $key => $value) {
                    $showNames[] = $pinpais[$value];
                }
                $this_pinpai = implode('，', $showNames);
            }
            //A 数据库 ID，新建配件填 新建 两个字	B显示 ID，手填	C汽车品牌
            //D类别	E服务名称	标识	价格	是否常用	成本	删除(是/否)

            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['_id']->toString());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['showId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_pinpai);
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['typ']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['name']->gen_show_value());

            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['biaoshi']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['jiage']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['changyong']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['chengben']->gen_show_value());

            $i++;
        }

        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    function store_peijian() {

        $this->load->model('lists/Org_list',"allOrgList");
        $this->allOrgList->load_data();


        $this->load->model('lists/Common_list',"listInfo");

        $this->listInfo->setInfo('sPeijian','Peijian_list','Peijian_model');
        $this->listInfo->orderKey = array('typ'=>'asc');
        $this->listInfo->limit = 2000;
        $this->listInfo->load_data();


        $fileName = BASEPATH."../wwwroot/templates/template_peijian_store.xlsx";
        $exportName = "exports/配件-数量统计-".date('Y')."-".date('m')."-".date('d').'-'.substr(md5($searchInfo.time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = '配件数量/成本统计表 '.date('Y')."-".date('m')."-".date('d');

        $objWorksheet->getCell('A1')->setValue($title);

        $start_store = 73;//'I';
        foreach ($this->allOrgList->record_list as $this_store){
            $this_col = chr($start_store);

            $objWorksheet->getCell($this_col.'2')->setValue($this_store->field_list['name']->gen_show_value());
            $start_store++;
            //新增成本导出，多空一行
            $this_col = chr($start_store);

            $objWorksheet->getCell($this_col.'2')->setValue($this_store->field_list['name']->gen_show_value().'成本');
            $start_store++;

        }

        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
            //
            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['_id']->toString());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['showId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_record->field_list['typ']->gen_show_value());
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['name']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['pinpai']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['biaoshi']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['xinghao']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['jinjia']->gen_show_value());


            $counter_instore = $this_record->field_list['counter_instore']->value;
            $chengben_instore = $this_record->field_list['chengben_instore']->value;

            $start_store = 73;//'I';
            foreach ($this->allOrgList->record_list as $this_store){
                $this_col = chr($start_store);
                $this_id = $this_store->id;
                if (isset($counter_instore[$this_id])) {
                    $objWorksheet->getCell($this_col.$i)->setValue($counter_instore[$this_id]);
                } else {
                    $objWorksheet->getCell($this_col.$i)->setValue('0');
                }

                $start_store++;

                $this_col = chr($start_store);
                if (isset($chengben_instore[$this_id])) {
                    $objWorksheet->getCell($this_col.$i)->setValue($chengben_instore[$this_id]);
                } else {
                    $objWorksheet->getCell($this_col.$i)->setValue('0');
                }

                $start_store++;
            }
            $i++;
        }
        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    function uploads($typ){
        switch ($typ) {
            case 'xiangqing':
                $rst = $this->_upload('input_peijian_xiangqing');
                break;
            case 'qingdian':
                $rst = $this->_upload('input_peijian_qingdian');
                break;
            case 'service':
                    $rst = $this->_upload('input_service_xiangqing');
                    break;
            default:
                # code...
                break;
        }
        if ( $rst['rstno']==-1)
        {
            //上传失败
            $jsonRst = -1;
            $jsonData = array();
            $jsonData['err']['msg'] =$rst['message'];
            echo $this->exportData($jsonData,$jsonRst);
            exit;
        }
        $jsonRst = 1;
        $jsonData = array();
        $jsonData['link'] = $rst['link'];
        echo $this->exportData($jsonData,$jsonRst);

    }

    function imports($typ,$file){

        $this->infoTitle = "导入数据".$file;
        $this->typ = $typ;
        $this->fileName = $file;
        $file = urldecode($file);

        $fileName = BASEPATH."../wwwroot/misc/auploads/".$file;
        $this->load->library("excel");
        $this->excel->init($fileName);
        $this->excel->maxColumn = 'N';
        $this->excel->titleLine = 2;
        $this->excel->getAllData();
        $this->dataChecker = array();
        $this->allCounter = count($this->excel->excel_data)-1;

        $this->lineCheckerError = array();
        $this->lineCheckerInsert = array();
        $this->lineCheckerUpdate = array();
        $this->lineCheckerDelete = array();


        $this->canImport = true;
        if ($typ=="xiangqing" || $typ=="service"){
            switch ($typ) {
                case 'xiangqing':
                    $this->load->model('records/peijian_model',"dataModel");
                    break;
                case 'service':
                    $this->load->model('records/service_model',"dataModel");
                    break;
                default:
                    # code...
                    break;
            }

            foreach ($this->excel->excel_data as $lineId => $line) {
                if ($lineId<2) {
                    continue;
                }
                if (trim($line[0])==""){
                    continue;
                }
                $lineChecker = $this->dataModel->checkImportData($line);
                if ($lineChecker['result']=='error'){
                    $this->lineCheckerError[$lineId+1 ] = $lineChecker['data'];
                    $this->canImport = false;
                } else if ($lineChecker['result']=='insert'){
                    $this->lineCheckerInsert[$lineId+1 ] = $lineChecker['data'];
                } else if ($lineChecker['result']=='update'){
                    $this->lineCheckerUpdate[$lineId+1 ] = $lineChecker['data'];
                } else if ($lineChecker['result']=='delete'){
                    $this->lineCheckerDelete[$lineId+1 ] = $lineChecker['data'];
                }

            }

        } else if ($typ=="qingdian"){

            $this->load->model('lists/Org_list',"allOrgList");
            $this->allOrgList->load_data();

            $allStores = array();
            $allStores2 = array();

            foreach ($this->allOrgList->record_list as $key => $value) {
                $allStores[$value->field_list['name']->value]=$key;
                $allStores2[$key] = $value->field_list['name']->value;
            }
            $this->load->model('records/peijian_model',"dataModel");
            $myStores = array();
            foreach ($this->excel->excel_data as $lineId => $line) {

                if ($lineId<1) {
                    continue;
                }
                if ($lineId==1) {
                    for ($i=8;$i<count($line);$i=$i+2){
                        if (isset($allStores[$line[$i]])){
                            $myStores[$i] = $allStores[$line[$i]];
                        } else {
                            $this->lineCheckerError[$lineId+1 ] = array('msg'=>'店名不存在','info'=>array('name'=>$line[$i],'col'=>$i));
                            $this->canImport = false;
                            break;
                        }

                    }
                    continue;
                }
                $lineChecker = $this->dataModel->checkImportQingdian($line,$myStores,$allStores2);
                if ($lineChecker['result']=='error'){
                    $this->lineCheckerError[$lineId+1 ] = $lineChecker['data'];
                    $this->canImport = false;
                } else if ($lineChecker['result']=='insert'){
                    $this->lineCheckerInsert[$lineId+1 ] = $lineChecker['data'];
                } else if ($lineChecker['result']=='update'){
                    $this->lineCheckerUpdate[$lineId+1 ] = $lineChecker['data'];
                }


            }

        }
        if (count($this->lineCheckerInsert) + count($this->lineCheckerUpdate) + count($this->lineCheckerDelete)==0) {
            $this->canImport = false;
        }

        $this->template->load('adefault_lightbox_import', 'aexport/importData');
    }

    function doRealImport(){
        $typ = $this->input->post('typ');
        $file = $this->input->post('file');
        $updates = $this->input->post('updates');
        $inserts  = $this->input->post('inserts');
        $deletes = $this->input->post('deletes');

        if ($updates===false){
            $updates = array();
        }
        if ($inserts===false){
            $inserts = array();
        }
        if ($deletes===false){
            $deletes = array();
        }
        $this->typ = $typ;
        $this->fileName = $file;
        $file = urldecode($file);

        $fileName = BASEPATH."../wwwroot/misc/auploads/".$file;
        $this->load->library("excel");
        $this->excel->init($fileName);
        $this->excel->maxColumn = 'N';
        $this->excel->titleLine = 2;
        $this->excel->getAllData();
        $this->dataChecker = array();
        $this->allCounter = count($this->excel->excel_data)-1;

        $jsonData = array('insert'=>0,'update'=>0,'deleted'=>0,'goto_url'=>site_url('aconfig/peijian'));
        if ($typ=="xiangqing" || $typ=="service"){
            switch ($typ) {
                case 'xiangqing':
                    $this->load->model('records/peijian_model',"dataModel");
                    break;
                case 'service':
                    $this->load->model('records/service_model',"dataModel");
                    break;
                default:
                    # code...
                    break;
            }
            foreach ($this->excel->excel_data as $lineId => $line) {
                if ($lineId<2) {
                    continue;
                }
                if (!in_array($lineId+1,$updates) && !in_array($lineId+1,$inserts) &&!array($lineId+1,$deletes)){
                    continue;
                }
                if (in_array($lineId+1,$updates)){
                    $lineChecker = $this->dataModel->doImportData($line,'update');
                } else if (in_array($lineId+1,$inserts)){
                    $lineChecker = $this->dataModel->doImportData($line,'insert');
                } else if (in_array($lineId+1,$deletes)){
                    $lineChecker = $this->dataModel->doImportData($line,'delete');
                }

                if ($lineChecker['result']=='insert'){
                    $jsonData['insert']++;
                } else if ($lineChecker['result']=='update'){
                    $jsonData['update']++;
                } else if ($lineChecker['result']=='delete'){
                    $jsonData['deleted']++;
                }
            }
        }else if ($typ=="qingdian"){
            $this->load->model('lists/Org_list',"allOrgList");
            $this->allOrgList->load_data();

            $allStores = array();
            $myStores = array();

            foreach ($this->allOrgList->record_list as $key => $value) {
                $allStores[$value->field_list['name']->value]=$key;
            }

            $this->load->model('records/peijian_model',"dataModel");
            foreach ($this->excel->excel_data as $lineId => $line) {
                if ($lineId<1) {
                    continue;
                }
                if ($lineId==1) {
                    for ($i=8;$i<count($line);$i=$i+2){
                        if (isset($allStores[$line[$i]])){
                            $myStores[$i] = $allStores[$line[$i]];
                        } else {
                            $this->lineCheckerError[$lineId+1 ] = array('msg'=>'店名不存在','info'=>array('name'=>$line[$i],'col'=>$i));
                            $this->canImport = false;
                            break;
                        }

                    }
                    continue;
                }
                if (!in_array($lineId+1,$updates) && !in_array($lineId+1,$inserts)){
                    continue;
                }

                $lineChecker = $this->dataModel->doImportCouter($line,$myStores);
                if ($lineChecker['result']=='insert'){
                    $jsonData['insert']++;
                } else if ($lineChecker['result']=='update'){
                    $jsonData['update']++;
                }
            }
        }

        echo $this->exportData($jsonData,1);
    }

    function _upload($domId)
    {
        $config['upload_path'] = './misc/auploads/';
        $config['allowed_types'] ='xlsx';

        $config['max_size'] = '10000';
        $config['overwrite'] = true;
        $this->load->library('upload', $config);

        $info = $this->upload->data();
        if ( ! $this->upload->do_upload($domId))
        {

            $jsonData = array();
            $jsonData['rstno'] = -1;
            $jsonData['message'] = $this->upload->display_errors("","");
            return $jsonData;
        }
        else

        {
            $info = $this->upload->data();
            $jsonData = array();
            $jsonData['rstno'] = 1;
            $jsonData['link'] = $info['file_name'];
            $jsonData['url'] = static_url("/auploads/".$info['file_name']);
            return $jsonData;
        }
    }

    function share_orders_finish($store='all',$from='',$to=''){
        $this->store = $store;
        $this->from = $from;
        $this->to = $to;
        $this->load_org_info();

        $this->quickSearchName = "订单号/姓名/电话";
        $this->buildSearch();


        $this->load->model('lists/Book_list',"listInfo");
        $this->listInfo->is_only_brief_fields = true;

        if ($this->store!='all' || $from!='' || $to!=''){
            if ($this->store!='all'){
                $this->listInfo->add_where(WHERE_TYPE_WHERE,'orgId',$this->store);
            }
            if ($from!=''){
                $beginTS = $this->utility->getTSFromDateString($from);
                $this->listInfo->add_where(WHERE_TYPE_WHERE_GTE,'realTS',$beginTS);
            }
            if ($to!=''){
                $endTS = $this->utility->getTSFromDateString($to)+86400-1;
                $this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'realTS',$endTS);
            }

            $this->listInfo->add_where(WHERE_TYPE_IN, 'status', array(60,100));

        } else {


        }

        $this->templateFile = "template_order_finish.xlsx";
        $this->exportFilePrefix = "订单-已完成-";

        $this->listInfo->load_data_with_where();
        $fileName = BASEPATH."../wwwroot/templates/".$this->templateFile ;
        $exportName = "exports/".$this->exportFilePrefix.date('Y')."-".date('m')."-".date('d').'-'.substr(md5(time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = '订单已完成 导出表 '.date('Y')."-".date('m')."-".date('d').' 时间区间:'.$from.'-'.$to;

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
            // 订单号 门店  客户  手机号 车牌号 服务内容    状态  活动订单    是否异常    预约到店时间  实际到店时间  金额  技师  总体打分    接待人员服务满意度   技师专业度满意度    门店环境及整洁度    点评


            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['showId']->gen_show_value());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['orgId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_record->field_list['crmId']->gen_show_value());
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['phone']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['chepaihao']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['bookdesc']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['status']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['is_active']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['passFail']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['bookTS']->gen_show_value());
            $objWorksheet->getCell('K'.$i)->setValue(date('Y-m-d h:i',$this_record->field_list['realTS']->gen_show_value()));
            $objWorksheet->getCell('L'.$i)->setValue($this_record->field_list['totalPrice']->gen_show_value());
            $objWorksheet->getCell('M'.$i)->setValue($this_record->field_list['payMethod']->gen_show_value());

            $objWorksheet->getCell('N'.$i)->setValue($this_record->field_list['jishi']->gen_show_value());
            $objWorksheet->getCell('O'.$i)->setValue($this_record->field_list['score']->gen_show_value());
            $objWorksheet->getCell('P'.$i)->setValue($this_record->field_list['kh_score']->gen_show_value());
            $objWorksheet->getCell('Q'.$i)->setValue($this_record->field_list['jishi_score']->gen_show_value());
            $objWorksheet->getCell('R'.$i)->setValue($this_record->field_list['hj_score']->gen_show_value());
            $objWorksheet->getCell('S'.$i)->setValue($this_record->field_list['score_cont']->gen_show_value());


            $i++;
        }
        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    function share_orders_nopay($store='all',$from='',$to=''){
        $this->store = $store;
        $this->from = $from;
        $this->to = $to;
        $this->load_org_info();

        $this->quickSearchName = "订单号/姓名/电话";
        $this->buildSearch();


        $this->load->model('lists/Book_list',"listInfo");

        $this->listInfo->is_only_brief_fields = true;


        if ($this->store!='all' || $from!='' || $to!=''){
            if ($this->store!='all'){
                $this->listInfo->add_where(WHERE_TYPE_WHERE,'orgId',$this->store);
            }
            if ($from!=''){
                $beginTS = $this->utility->getTSFromDateString($from);
                $this->listInfo->add_where(WHERE_TYPE_WHERE_GTE,'realTS',$beginTS);
            }
            if ($to!=''){
                $endTS = $this->utility->getTSFromDateString($to)+86400-1;
                $this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'realTS',$endTS);
            }

            $this->listInfo->add_where(WHERE_TYPE_IN, 'status', array(50));

        } else {


        }

        $this->templateFile = "template_order_nopay.xlsx";
        $this->exportFilePrefix = "订单-应收账款-";

        $this->listInfo->load_data_with_where();

        $fileName = BASEPATH."../wwwroot/templates/".$this->templateFile ;
        $exportName = "exports/".$this->exportFilePrefix.date('Y')."-".date('m')."-".date('d').'-'.substr(md5(time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = '订单应收账款 导出表 '.date('Y')."-".date('m')."-".date('d').' 时间区间:'.$from.'-'.$to;

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
            // 订单号 门店  客户  手机号 车牌号 服务内容    状态  活动订单    是否异常    预约到店时间  实际到店时间  金额  技师  总体打分    接待人员服务满意度   技师专业度满意度    门店环境及整洁度    点评


            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['showId']->gen_show_value());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['orgId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_record->field_list['crmId']->gen_show_value());
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['phone']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['chepaihao']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['bookdesc']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['status']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['is_active']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['passFail']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['bookTS']->gen_show_value());
            $objWorksheet->getCell('K'.$i)->setValue(date('Y-m-d h:i',$this_record->field_list['realTS']->gen_show_value()));
            $objWorksheet->getCell('L'.$i)->setValue($this_record->field_list['totalPrice']->gen_show_value());
            $objWorksheet->getCell('M'.$i)->setValue($this_record->field_list['jishi']->gen_show_value());


            $i++;
        }

        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    function share_orders_jishi($store='all',$from='',$to=''){
        $this->store = $store;
        $this->from = $from;
        $this->to = $to;
        $this->load_org_info();

        $this->quickSearchName = "订单号/姓名/电话";
        $this->buildSearch();


        $this->load->model('lists/Book_list',"listInfo");

        $this->listInfo->is_only_brief_fields = true;

        if ($this->store!='all' || $from!='' || $to!=''){
            if ($this->store!='all'){
                $this->listInfo->add_where(WHERE_TYPE_WHERE,'orgId',$this->store);
            }
            if ($from!=''){
                $beginTS = $this->utility->getTSFromDateString($from);
                $this->listInfo->add_where(WHERE_TYPE_WHERE_GTE,'realTS',$beginTS);
            }
            if ($to!=''){
                $endTS = $this->utility->getTSFromDateString($to)+86400-1;
                $this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'realTS',$endTS);
            }

            $this->listInfo->add_where(WHERE_TYPE_IN, 'status', array(60,100));

        } else {


        }

        $this->templateFile = "template_order_jishi.xlsx";
        $this->exportFilePrefix = "订单-技师-";

        $this->listInfo->load_data_with_where();

        $fileName = BASEPATH."../wwwroot/templates/".$this->templateFile ;
        $exportName = "exports/".$this->exportFilePrefix.date('Y')."-".date('m')."-".date('d').'-'.substr(md5($store.$from.$to.time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = '订单导出表 '.date('Y')."-".date('m')."-".date('d').' 时间区间:'.$from.'-'.$to;

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        $allRecords = array();
        $allJishis = array();
        foreach ($this->listInfo->record_list as $this_record) {
            $storeId = $this_record->field_list['orgId']->value;
            $jishis = $this_record->field_list['jishi']->real_data;

            $this_record->field_list['totalPrice']->value = $this_record->field_list['totalPrice']->value/count($jishis);
            if (!isset($allRecords[$storeId])){
                $allRecords[$storeId] = array();
            }
            foreach ($jishis as $jishiModel) {
                $this_jishi = $jishiModel->id;
                if (!isset($allRecords[$storeId][$this_jishi])){
                    $allRecords[$storeId][$this_jishi] = array();
                }
                $allRecords[$storeId][$this_jishi][] = $this_record;
                if (!isset($allJishis[$this_jishi])){
                    $allJishis[$this_jishi] = $jishiModel->field_list['name']->value;
                }

            }



        }
            // 订单号 门店  客户  手机号 车牌号 服务内容    状态  活动订单    是否异常    预约到店时间  实际到店时间  金额  技师  总体打分    接待人员服务满意度   技师专业度满意度    门店环境及整洁度    点评

        foreach ($allRecords as $thisStore) {
            foreach ($thisStore as $this_jishi => $jishiOrders) {

                foreach ($jishiOrders as $this_record) {

            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['showId']->gen_show_value());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['orgId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($allJishis[$this_jishi] );

            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['crmId']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['phone']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['chepaihao']->gen_show_value());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['bookdesc']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['status']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['is_active']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['passFail']->gen_show_value());
            $objWorksheet->getCell('K'.$i)->setValue($this_record->field_list['bookTS']->gen_show_value());
            $objWorksheet->getCell('L'.$i)->setValue(date('Y-m-d h:i',$this_record->field_list['realTS']->gen_show_value()));
            $objWorksheet->getCell('M'.$i)->setValue($this_record->field_list['totalPrice']->gen_show_value());
            $objWorksheet->getCell('N'.$i)->setValue($this_record->field_list['jishi']->gen_show_value());
            $objWorksheet->getCell('O'.$i)->setValue($this_record->field_list['score']->gen_show_value());
            $objWorksheet->getCell('P'.$i)->setValue($this_record->field_list['kh_score']->gen_show_value());
            $objWorksheet->getCell('Q'.$i)->setValue($this_record->field_list['jishi_score']->gen_show_value());
            $objWorksheet->getCell('R'.$i)->setValue($this_record->field_list['hj_score']->gen_show_value());
            $objWorksheet->getCell('S'.$i)->setValue($this_record->field_list['score_cont']->gen_show_value());


            $i++;

                }
            }
        }

        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    function export_peijian_ruku($store='all',$from='',$to=''){
        $this->store = $store;
        $this->from = $from;
        $this->to = $to;
        $this->load_org_info();

        $this->load->model('lists/Org_list',"allOrgList");
        $this->allOrgList->load_data();

        $storeName = '全部';
        if ($store!='all' && isset($this->allOrgList->record_list[$store])){
            $storeName = $this->allOrgList->record_list[$store]->field_list['name']->value;
        }

        $this->load->model('lists/Peijianflow_list',"listInfo");
        $this->listInfo->is_only_brief_fields = true;

        if ($this->store!='' || $from!='' || $to!=''){
            if ($this->store!='all'){
                $this->listInfo->add_where(WHERE_TYPE_WHERE,'orgId',$this->store);
            }
            if ($from!=''){
                $beginTS = $this->utility->getTSFromDateString($from);
                $this->listInfo->add_where(WHERE_TYPE_WHERE_GTE,'beginTS',$beginTS);
            }
            if ($to!=''){
                $endTS = $this->utility->getTSFromDateString($to)+86400-1;
                $this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'beginTS',$endTS);
            }
        }
        /*
        $this->field_list['typ']->setEnum(array(0=>'其他',
                                          1=>"入库",
                                          2=>"出库",
                                          3=>"快速入库",
                                          5=>"出库回库",
                                          6=>"退货",
        ));
        */
        $this->listInfo->add_where(WHERE_TYPE_IN,'typ',array(1,3,6,7));


        $this->listInfo->load_data_with_where();

        $this->templateFile = "template_ruku.xlsx";
        $this->exportFilePrefix = "配件流水-入库-".$storeName.'-';

        $fileName = BASEPATH."../wwwroot/templates/".$this->templateFile ;
        $exportName = "exports/".$this->exportFilePrefix.date('Y')."-".date('m')."-".date('d').'-'.substr(md5(time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = $this->exportFilePrefix . date('Y')."-".date('m')."-".date('d').' 时间区间:'.$from.'-'.$to;

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
//'bookShowId','peijianId','peijiantyp','peijianming','orgId','beginTS','typ','counter','chengben','uid'
//门店	配件数据库ID	配件类型	配件名	数量	时间	操作人	行为	成本	订单号


            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['orgId']->gen_show_value());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['peijianId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_record->field_list['peijiantyp']->gen_show_value());
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['peijianming']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['counter']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['beginTS']->gen_show_html());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['uid']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['typ']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['chengben']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['bookShowId']->gen_show_value());



            $i++;
        }
        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }

    public function export_peijian_use($store='all',$from='',$to=''){
        $this->store = $store;
        $this->from = $from;
        $this->to = $to;
        $this->load_org_info();

        $this->load->model('lists/Org_list',"allOrgList");
        $this->allOrgList->load_data();

        $storeName = '全部';
        if ($store!='all' && isset($this->allOrgList->record_list[$store])){
            $storeName = $this->allOrgList->record_list[$store]->field_list['name']->value;
        }

        $this->load->model('lists/Peijianflow_list',"listInfo");
        $this->listInfo->is_only_brief_fields = true;

        if ($this->store!='' || $from!='' || $to!=''){
            if ($this->store!='all'){
                $this->listInfo->add_where(WHERE_TYPE_WHERE,'orgId',$this->store);
            }
            if ($from!=''){
                $beginTS = $this->utility->getTSFromDateString($from);
                $this->listInfo->add_where(WHERE_TYPE_WHERE_GTE,'beginTS',$beginTS);
            }
            if ($to!=''){
                $endTS = $this->utility->getTSFromDateString($to)+86400-1;
                $this->listInfo->add_where(WHERE_TYPE_WHERE_LT,'beginTS',$endTS);
            }
        }
        /*
        $this->field_list['typ']->setEnum(array(0=>'其他',
                                          1=>"入库",
                                          2=>"出库",
                                          3=>"快速入库",
                                          5=>"出库回库",
                                          6=>"退货",
        ));
        */
        $this->listInfo->add_where(WHERE_TYPE_IN,'typ',array(0,2,5));

        $this->listInfo->load_data_with_where();

        $this->templateFile = "template_peijianxiaohao.xlsx";
        $this->exportFilePrefix = "配件流水-消耗-".$storeName.'-';

        $fileName = BASEPATH."../wwwroot/templates/".$this->templateFile ;
        $exportName = "exports/".$this->exportFilePrefix.date('Y')."-".date('m')."-".date('d').'-'.substr(md5(time()),2,6).'.xlsx';
        $exportFileName = BASEPATH."../wwwroot/misc/".$exportName;
        $this->load->library("excel");
        $this->excel->init($fileName);

        $objWorksheet = $this->excel->excel->getActiveSheet();
        // 汽车品牌    类别  配件名称    品牌  标识  型号  保修  卖价  是否常用件   进价

        $title = $this->exportFilePrefix . date('Y')."-".date('m')."-".date('d').' 时间区间:'.$from.'-'.$to;

        $objWorksheet->getCell('A1')->setValue($title);
        $i = 3;
        foreach ($this->listInfo->record_list as $this_record) {
//'bookShowId','peijianId','peijiantyp','peijianming','orgId','beginTS','typ','counter','chengben','uid'
//门店	配件数据库ID	配件类型	配件名	数量	时间	操作人	行为	成本	订单号


            $objWorksheet->getCell('A'.$i)->setValue($this_record->field_list['orgId']->gen_show_value());
            $objWorksheet->getCell('B'.$i)->setValue($this_record->field_list['peijianId']->gen_show_value());
            $objWorksheet->getCell('C'.$i)->setValue($this_record->field_list['peijiantyp']->gen_show_value());
            $objWorksheet->getCell('D'.$i)->setValue($this_record->field_list['peijianming']->gen_show_value());
            $objWorksheet->getCell('E'.$i)->setValue($this_record->field_list['counter']->gen_show_value());
            $objWorksheet->getCell('F'.$i)->setValue($this_record->field_list['beginTS']->gen_show_html());
            $objWorksheet->getCell('G'.$i)->setValue($this_record->field_list['uid']->gen_show_value());
            $objWorksheet->getCell('H'.$i)->setValue($this_record->field_list['typ']->gen_show_value());
            $objWorksheet->getCell('I'.$i)->setValue($this_record->field_list['chengben']->gen_show_value());
            $objWorksheet->getCell('J'.$i)->setValue($this_record->field_list['bookShowId']->gen_show_value());



            $i++;
        }
        $objWriter = $this->excel->initWriter();
        $objWriter->save($exportFileName);

        header('Location: '.static_url($exportName));
    }
}
