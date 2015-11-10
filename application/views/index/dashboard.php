<div>
<?php
include_once(APPPATH."views/common/bread.php");
?>
</div>
<?php echo link_tag(static_url('css/fullcalendar.css')); ?>
<?php echo link_script(static_url('js/fullcalendar.js')); ?>
<?php
include_once("dashboardHelper.php");
?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default calendar">
              <div class="panel-heading">
                <h3 class="panel-title">日程</h3>
                <div>
                    <label class="checkbox-inline">
                        <input type="checkbox" id="cldMyTask" value="1" onchange="reloadCalender()" checked="checked"/>我的工作
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" id="cldMyPushTask" value="1"  onchange="reloadCalender()" checked="checked"/>我派发的工作
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" id="cldMyDev" value="1"  onchange="reloadCalender()" checked="checked"/>我的开发项
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" id="cldMyInterview" value="1"  onchange="reloadCalender()" checked="checked"/>我的面试
                    </label>

                </div>
              </div>
              <div class="panel-body">
                <div id="calendar" >
                </div>
              </div>
        </div>
        <script>
        var date = new Date();
                    var d = date.getDate();
                    var m = date.getMonth();
                    var y = date.getFullYear();
        var h = {
                                left: 'title',
                                center: '',
                                right: 'prev,next,today,month,basicWeek'
                            };

            function reloadCalender(){
                var data = {
                    cldMyTask:$("#cldMyTask").prop('checked'),
                    cldMyPushTask:$("#cldMyPushTask").prop('checked'),
                    cldMyDev:$("#cldMyDev").prop('checked'),
                    cldMyInterview:$("#cldMyInterview").prop('checked'),
                };

                $("#calendar").fullCalendar("destroy");
                $("#calendar").fullCalendar({ //re-initialize the calendar
                                header: h,
                                firstDay: 1,
                                defaultView:"basicWeek",
                                weekMode:'liquid',
                                allDayText:'全天事件',
                                axisFormat:'HH(:mm)',
                                slotMinutes: 60,
                                editable: false,
                                droppable: false,
                                events:req_url_template.str_supplant({ctrller:'index',action:'calList/?'+http_build_query(data)}),
                                eventMouseover:function( event, jsEvent, view ) { },
                                eventClick: function(calEvent, jsEvent, view) {
                                    console.log(calEvent);
                                    lightbox({size:'m',url:'<?=site_url('calendar/edit').'/'?>'+ calEvent.typ+'/'+calEvent.id})


                                    // change the border color just for fun
                                    $(this).css('border-color', 'red');

                                }
                            });
            }
            $(function(){
                reloadCalender();
            });
        </script>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default calendar">
            <div class="panel-heading">
            <h3 class="panel-title">常用操作区</h3>
            </div>
            <div class="panel-body">
                <a href="javascript:void(0)" class="btn btn-primary btn-sqaure" onclick="lightbox({size:'m',url:'<?=site_url("/operate/create/crate/")?>'})">
                    <span class="glyphicon glyphicon-wrench"></span> <span class="sqaure-desc">请求开机箱</span>
                </a>
                <a href="javascript:void(0)" class="btn btn-info btn-sqaure" onclick="lightbox({size:'m',url:'<?=site_url("/art/create/needs/")?>'})">
                    <span class="glyphicon glyphicon-picture"></span>  <span class="sqaure-desc">发美术需求</span>
                </a>
                <a href="javascript:void(0)" class="btn btn-warning btn-sqaure" onclick="lightbox({size:'m',url:'<?=site_url("/admin/editMyself/")?>'})">
                    <span class="glyphicon glyphicon-pencil"></span>  <span class="sqaure-desc">编辑个人信息</span>
                </a>
                <a href="javascript:void(0)" class="btn btn-success btn-sqaure" onclick="lightbox({size:'m',url:'<?=site_url("/task/create/task/")?>'})">
                    <span class="glyphicon glyphicon-list"></span>  <span class="sqaure-desc">发工作需求</span>
                </a>

                <?
                if ($this->userInfo->field_list['isManager']->value>=2){
                ?>
                <a href="javascript:void(0)" class="btn btn-danger  btn-sqaure" onclick="lightbox({size:'m',url:'<?=site_url("/index/sendRTX/")?>'})">
                    <span class="glyphicon glyphicon-comment"></span>  <span class="sqaure-desc">发RTX通知</span>
                </a>
                <?
                }
                ?>
                <?
                if ($this->userInfo->field_list['typ']->in_array(array(2,3,4,7))){
                ?>
                <a href="http://172.18.1.4:8082/" target="_blank" class="btn btn-danger  btn-sqaure">
                    <span class="glyphicon glyphicon-cloud"></span>  <span class="sqaure-desc">知识分享</span>
                </a>
                <?
                }
                ?>
            </div>
        </div>
        <div class="panel panel-default calendar">
            <div class="panel-heading">
            <h3 class="panel-title">新功能Tips</h3>
            </div>
            <ul class="list-group">
                <li class="list-group-item text-danger"><span class="glyphicon glyphicon-bullhorn "></span> 可以在任务列表点击眼睛图标主动关注某些任务</li>
                <li class="list-group-item">我的工作 页增加状态筛选</li>
                <li class="list-group-item">工作增加了关注人，可以选择关注人，状态变化时也会发送rtx通知。在我的工作页面，增加了我关注的任务。</li>

                <?
                if ($this->userInfo->field_list['isManager']->value>=1){
                ?>
                <li class="list-group-item">管理周会内容上线，以任务卡的方式组织的关于这个任务在上周到本周末的进展和安排</li>
                <li class="list-group-item">经理及以上可以手动发RTX通知，在上面，后续将提供按部门发通知功能，这样方便大家召集开会等</li>
                <li class="list-group-item">经理及以上可以发起招聘需求，候选人面试管理也放在系统了，后续面试预约也会放在日历里面</li>

                <?
                }
                ?>
                <li class="list-group-item">任务在更新状态后，会发送RTX通知，请将自己系统的登录名改为rtx登录名。更多通知内容需求请各位告知</li>

                <li class="list-group-item">我的工作 可以委托给他人，点小飞机图标即可</li>
                <li class="list-group-item">编辑个人信息，可以改密码，另外自己看看自己的内网机机箱号，填进去，这样发开机箱申请时候可以自动填入，找钥匙比较快</li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">我派发的工作
                    <a href="javascript:void(0)" class="pull-right" onclick="lightbox({size:'m',url:'<?=site_url($this->task_create_link)?>'})">
                        <small><span class="glyphicon glyphicon-file"></span> 新建</small></a></h3>
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <?
                        foreach ($this->pushTaskList->keyList as $key_names):
                        ?>
                            <th>
                                <?php
                                echo $this->pushTaskList->dataModel[$key_names]->gen_show_name();;
                                ?>
                            </th>
                        <?
                        endforeach;
                        ?>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;

                    foreach($this->pushTaskList->record_list as  $this_record):
                        $i++;
                        ?>
                        <tr>

                            <?
                            foreach ($this->pushTaskList->keyList as $key_names):
                            ?>
                                <td>
                                    <?php
                                    if ($this_record->field_list[$key_names]->is_title):
                                        if ($this->pushTaskList->is_lightbox):
                                            echo '<a href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})">'.$this_record->field_list[$key_names]->gen_list_html().'</a>';
                                        else :
                                            echo '<a href="'.site_url($this_record->info_link.$this_record->id).'">'.$this_record->field_list[$key_names]->gen_list_html().'</a>';
                                        endif;
                                    else :
                                        echo $this_record->field_list[$key_names]->gen_list_html();

                                    endif;
                                    ?>
                                </td>
                            <?
                            endforeach;
                            ?>
                            <td>
                                <?
                                if ($this->pushTaskList->is_lightbox):
                                    echo '<a class="list_op tooltips" href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})"><span class="glyphicon glyphicon-search"></span></a>';
                                else :
                                    echo '<a  class="list_op tooltips" href="'.site_url($this_record->info_link.$this_record->id).'"><span class="glyphicon glyphicon-search"></span></a>';
                                endif;
                                ?>
                                 |
                                <?php
                                if ($this->pushTaskList->canEdit) {
                                    echo $this_record->gen_list_op($this->pushTaskList->op_limit);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    endforeach; ?>

                </tbody>
            </table>
            <?php
            if (count($this->pushTaskList->record_list)==0):
            ?>
                <div class="no-data-large">
                    没有相关记录
                </div>
            <?
            endif;
            ?>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">我的工作</h3>
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <?
                        foreach ($this->dueTaskList->keyList as $key_names):
                        ?>
                            <th>
                                <?php
                                echo $this->dueTaskList->dataModel[$key_names]->gen_show_name();;
                                ?>
                            </th>
                        <?
                        endforeach;
                        ?>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;

                    foreach($this->dueTaskList->record_list as  $this_record):
                        $i++;
                        ?>
                        <tr>

                            <?
                            foreach ($this->dueTaskList->keyList as $key_names):
                            ?>
                                <td>
                                    <?php
                                    if ($this_record->field_list[$key_names]->is_title):
                                        if ($this->dueTaskList->is_lightbox):
                                            echo '<a href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})">'.$this_record->field_list[$key_names]->gen_list_html().'</a>';
                                        else :
                                            echo '<a href="'.site_url($this_record->info_link.$this_record->id).'">'.$this_record->field_list[$key_names]->gen_list_html().'</a>';
                                        endif;
                                    else :
                                        echo $this_record->field_list[$key_names]->gen_list_html();

                                    endif;
                                    ?>
                                </td>
                            <?
                            endforeach;
                            ?>
                            <td>
                                <?
                                if ($this->dueTaskList->is_lightbox):
                                    echo '<a class="list_op tooltips" href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})"><span class="glyphicon glyphicon-search"></span></a>';
                                else :
                                    echo '<a  class="list_op tooltips" href="'.site_url($this_record->info_link.$this_record->id).'"><span class="glyphicon glyphicon-search"></span></a>';
                                endif;
                                ?>
                                 |
                                <?php
                                if ($this->dueTaskList->canEdit) {
                                    echo $this_record->gen_list_op($this->dueTaskList->op_limit);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    endforeach; ?>

                </tbody>
            </table>
            <?php
            if (count($this->dueTaskList->record_list)==0):
            ?>
                <div class="no-data-large">
                    没有相关记录
                </div>
            <?
            endif;
            ?>
        </div>
    </div>
</div>
