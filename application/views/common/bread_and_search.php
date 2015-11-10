<ul class='breadcrumb breadcrumb-with-search'>
    <li><a href='#'><span class='glyphicon glyphicon-home'></span> Home</a></li>
    <li><a href='#'><span class='glyphicon <?=$this->menus[$this->show_controller_name]['icon']?>'></span> <?=$this->menus[$this->show_controller_name]['name']?></a></li>
    <li class='active'><a href='<?=site_url($this->controller_name.'/'.$this->method_name)?>'><span class='glyphicon glyphicon-circle-arrow-right'></span>
        <?=$this->menus[$this->show_controller_name]['menu_array'][$this->show_method_name]['name']?></a></li>
    <?=(isset($this->searchInfo) && $this->searchInfo['t']=="quick")?'<li class="avtive"><span class="glyphicon glyphicon-search"></span> 快捷搜索</li>':'';?>
    <?=(isset($this->searchInfo) && $this->searchInfo['t']=="full")?'<li class="avtive"><span class="glyphicon glyphicon-search"></span> 高级搜索</li>':'';?>
    <li class="pull-right search-aera-inline">
        <div class="">
        <form class="form-inline">
            <div class="input-group input-group-sm">
                <span class="input-group-addon">快捷搜索</span>
                <input type="text" id="quick_search" class="form-control input-sm" placeholder="请输入<?=(isset($this->quickSearchName)?$this->quickSearchName:'名称');?>" value="<?=(isset($this->quickSearchValue)?$this->quickSearchValue:'');?>">
                <div class="input-group-btn">
                    <a class="btn btn-primary btn-sm" id="btnQuickSearch" onclick="quicksearch('<?=$this->controller_name?>','<?=$this->method_name?>')"><span class="glyphicon glyphicon-search"></span></a>
                    <a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="toggle_search_box()">
                            更多 <span class="caret"></span>
                    </a>
                </div>

            </div>
            <div class="clearfix"></div>

        </form>
    </div>
    </li>
</ul>
<?php
$search_hidden = "hidden";
if (isset($this->searchInfo) && ($this->searchInfo['t']=="quick" || $this->searchInfo['t']=="full")){
    $search_hidden = '';
}
?>
<div id="search-box-main" class="<?=$search_hidden?> search-box">
    <form role="form" id="searchForm">
    <!-- <ul  class="list-group"> -->
    <table class="table search-table">
        <?php
        $counter = 0;
        foreach ($this->listInfo->build_search_infos() as $key_names) :

            if ($counter %2 ==0){
        ?>
        <tr>
        <?
            }

        ?>

            <?php
            $searchDefault = array("e"=>"=","v"=>null);
            if ($this->searchInfo['t']=="full"){
                $searchDefault['e'] = $this->searchInfo['i'][$key_names]['e'];
                $searchDefault['v'] = $this->searchInfo['i'][$key_names]['v'];
            }
            ?>
            <td width="30px">
                <input id="searchChk_<?=$key_names?>" name="searchChk_<?=$key_names?>" type="checkbox" value="" <?=($searchDefault['v']==null)?'':'checked="checked"'?>>
            </td>
            <td width="100px">
                <?php
                echo $this->listInfo->dataModel[$key_names]->gen_show_name();
                ?>
            </td>
            <td style="width:70px" class="mini_search_ele">
            <?php


            echo $this->listInfo->dataModel[$key_names]->gen_search_element($searchDefault['e']);
            ?>
            </td>
            <td style="width:100px">
            <?php
            echo $this->listInfo->dataModel[$key_names]->gen_search_editor($searchDefault['v']);
            ?>
            </td>
        <?
            if ($counter %2 ==1){
        ?>
            </tr>
        <?
            }
            $counter++;
        endforeach
        ?>
        <tr>
            <td colspan="7">
                <?php
                if (isset($this->searchInfo) && $this->searchInfo['t']=="quick"):
                ?>
                        <span class="glyphicon glyphicon-search"></span> 快捷搜索 : <?=(isset($this->quickSearchName)?$this->quickSearchName:'名称/编号');?> 包含 <?=(isset($this->quickSearchValue)?$this->quickSearchValue:'');?>;
                        <a href='<?=site_url($this->controller_name.'/'.$this->method_name)?>'><span class='glyphicon glyphicon-circle-arrow-right'></span> 返回<?=$this->Menus->show_menus[$this->controller_name]['menu_array'][$this->method_name]['name']?></a>
                <?php
                endif;
                if (isset($this->searchInfo) && $this->searchInfo['t']=="full"):
                ?>
                    <span class="glyphicon glyphicon-search"></span> 高级搜索 :
                    <?php
                    foreach ($this->searchInfo['i'] as $key => $value) {
                        echo  $this->listInfo->dataModel[$key]->gen_show_name();
                        echo " : ";
                        echo $this->listInfo->dataModel[$key]->gen_search_result_show($value['v']);
                        echo " ; ";
                    };
                    ?>
                    <a href='<?=site_url($this->controller_name.'/'.$this->method_name)?>'><span class='glyphicon glyphicon-circle-arrow-right'></span> 返回<?=$this->Menus->show_menus[$this->controller_name]['menu_array'][$this->method_name]['name']?></a>
                <?php
                endif;
                ?>

            </td>
            <td>
                <a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="toggle_search_box()">取消</a>
                <a class="btn btn-primary btn-sm" href="javascript:void(0);" onclick="fullsearch('<?=$this->controller_name?>','<?=$this->method_name?>',reqSearchFields,searchFormValidator)">搜索</a></td>
        </tr>

    </table>
    </form>
</div>

<script type="text/javascript">
    $("#quick_search").keyup(function(event){
        if(event.keyCode == 13){
            $("#btnQuickSearch").click();
        }
    });
    var searchFormValidator = $("#searchForm").validate();
    var reqSearchFields = [];
    <?php
    foreach ($this->listInfo->build_search_infos() as $key_names) {
        echo 'reqSearchFields.push({name:"'.$key_names.'",type:"'.$this->listInfo->dataModel[$key_names]->typ.'"});';
    }
    ?>
</script>
