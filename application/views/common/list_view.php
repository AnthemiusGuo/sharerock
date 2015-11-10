<?php
if ($this->hasSearch){
    include_once(APPPATH."views/common/bread_and_search.php");
} else {
    include_once(APPPATH."views/common/bread.php");
}

?>
<?
if ($this->need_plus!=""){
    include_once(APPPATH."views/".$this->need_plus.".php");
}
?>
<div class="row">
    <?php
    if ($this->canEdit && $this->canCreate):
    ?>
    <div class="col-lg-12 list-title-op">
        <?
        if ($this->createAsLightbox){
        ?>
        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="lightbox({size:'m',url:'<?=site_url($this->create_link)?>'})"><span class="glyphicon glyphicon-file"></span> 新建</a>

        <?
        } else {
        ?>
        <a href="<?=site_url($this->create_link)?>" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-file"></span> 新建</a>
        <?
        }
        ?>

    </div>
    <?
    endif;
    ?>
    
    <div class="col-lg-12">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <?
                    foreach ($this->listInfo->build_list_titles() as $key_names):
                    ?>
                        <th>
                            <?php
                            echo $this->listInfo->dataModel[$key_names]->gen_show_name();;
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

                foreach($this->listInfo->record_list as  $this_record):
                    $i++;
                    ?>
                    <tr>

                        <?
                        foreach ($this->listInfo->build_list_titles() as $key_names):
                        ?>
                            <td>
                                <?php
                                if ($this_record->field_list[$key_names]->is_title):
                                    if ($this->listInfo->is_lightbox):
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
                            if ($this->listInfo->is_lightbox):
                                echo '<a class="list_op tooltips" href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})"><span class="glyphicon glyphicon-search"></span></a>';
                            else :
                                echo '<a  class="list_op tooltips" href="'.site_url($this_record->info_link.$this_record->id).'"><span class="glyphicon glyphicon-search"></span></a>';
                            endif;
                            ?>
                             |
                            <?php
                            if ($this->canEdit) {
                                echo $this_record->gen_list_op($this->listInfo->op_limit);
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                endforeach; ?>

            </tbody>
        </table>

        <nav class="center-block">
            <?php echo $this->pagination->create_links(); ?>
        </nav>
        <?php
        if (count($this->listInfo->record_list)==0):
        ?>
            <div class="no-data-large">
                没有相关记录
            </div>
        <?
        endif;
        ?>
    </div>
</div>
<script>


</script>
