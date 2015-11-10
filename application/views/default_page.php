<?php include_once('common/header.php');
?>
<body class="page">
    <!-- BEGIN HEADER -->
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=site_url('index/index')?>">
              叶游工作管理</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><?
            if (isset($this->myOrgInfo)){
                echo $this->myOrgInfo->field_list['name']->gen_show_value().' - ';
            }?>
            <?=$this->userInfo->field_list['name']->gen_show_value()?> </a></li>
            <li><a href="javascript:void(0)" onclick="lightbox({size:'m',url:'<?=site_url('email/index')?>'})">收件箱 <span id="emailcount" class="badge"></span></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    当前项目<?=$this->userInfo->field_list['projectId']->gen_show_value()?> <span class="caret"></span>
                </a>
                  <ul class="dropdown-menu">
                      <?
                      foreach ($this->userInfo->field_list['projectIds']->real_data as $key => $this_project) {
                      ?>
                      <li><a class="list_op tooltips" onclick="reqOperator('project','doChangeProject','<?=$this_project->id?>')" title="确认"><span class="glyphicon glyphicon-log-out"></span><?=$this_project->field_list['name']->value?></a></li>
                      <?
                      }
                      ?>

                  </ul>
            </li>
            <li><a href="<?=site_url('index/doLogout')?>">
            <span class="glyphicon glyphicon-log-out"></span>退出
            </a></li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- END HEADER -->
    <div class="clearfix">
    </div>
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <ul id="nav-sidebar" class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <?php
                foreach ($this->menus as $menu_name=>$menu_info):
                ?>
                    <li class="main-nav <?php echo ($this->show_controller_name==$menu_name)?"active open":"" ?>" id="nav-side-title-<?php echo $menu_name;?>">
                        <a href="#" onclick="nav_sidebar_collapse('<?php echo $menu_name;?>')">
                        <span class="glyphicon <?php echo $menu_info["icon"]?>"></span>
                        <span class="title"><?php echo $menu_info['name'];?></span>
                        <span class="showing_icon glyphicon glyphicon-chevron-down pull-right <?php echo ($this->show_controller_name==$menu_name)?"show":"hidden" ?>"></span>
                        <?php echo ($this->controller_name==$menu_name)?'<span class="selected"></span>':"" ?>
                        </a>

                        <ul class="nav sub-nav <?php echo ($this->show_controller_name==$menu_name)?"show":"hidden" ?>" id="nav-side-list-<?php echo $menu_name;?>">
                            <?php
                            foreach ($menu_info["menu_array"] as $sub_menu_name=>$sub_menu_info):
                            ?>
                            <li class="<?php echo ($this->show_controller_name==$menu_name && $sub_menu_name==$this->show_method_name)?'active':'' ?>">
                                <a href="<?php echo ("href"==$sub_menu_info['method'])?$sub_menu_info['href']:'javascript:void(0);' ?>" <?php echo ("onclick"==$sub_menu_info['method'])?'onclick="'.$sub_menu_info['onclick'].'"':'' ?> >
                                <span class="glyphicon <?php echo ($this->show_controller_name==$menu_name && $sub_menu_name==$this->show_method_name)?'glyphicon-chevron-right':'glyphicon-minus' ?>"></span>
                                <?php echo $sub_menu_info['name'] ?></a>
                                </li>
                            <?
                            endforeach;
                            ?>
                        </ul>
                    </li>
                <?
                endforeach;
                ?>

            </ul>
        </div>
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content" style="min-height:1227px">
        <?php echo $contents; ?>
        </div>
    </div>
    <!-- END CONTENT -->
<script>
jQuery(document).ready(function() {
    $(".table-paged").quickPager({pageSize:10,holder:'#main_pager',struct:'tbody'});
    $('.tooltips').powerTip({offset:20});
    getEmailCount();
});
var relate_datas = {};
var relate_datas_result = [];

var table_item_vars = {};
var table_item_must_vars = {};
var table_item_template = {};
var table_all_data = {};

</script>
<!-- END JAVASCRIPTS -->
<?php include_once('common/footer.php')?>
