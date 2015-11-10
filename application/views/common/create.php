<div class="row">
    <div class="col-lg-12">
        <?
        if ($this->dataInfo->is_inited){
        ?>
        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="lightbox({size:'m',url:'<?=site_url($this->dataInfo->short_info_link).'/'.$this->dataInfo->id?>'})">
            <span class="glyphicon glyphicon-search"></span> 查看
        </a>
        <?
        }
        ?>
    </div>
    <div class="col-lg-12">
        <table class="table">
            <?php
            foreach ($this->modifyNeedFields as $key => $value) {
            ?>
            <tr>
                <?php
                $colspan = 0;
                if (count($value)==1){
                    $colspan = 3;
                }
                foreach ($value as $k => $v) {
                    if ($v=="null") {
                ?>
                    <td class="td_title"></td><td class="td_data"></td>
                <?
                    } else {
                ?>
                    <td class="td_title"><?php echo $this->dataInfo->field_list[$v]->gen_editor_show_name(); ?></td>
                    <td <?=($colspan==0)?'class="td_data"':'colspan="'.$colspan.'"'?> >
                            <?php
                            if ($this->dataInfo->field_list[$v]->is_hidden_input){
                                echo $this->dataInfo->field_list[$v]->gen_hidden_editor($this->editor_typ);
                                echo $this->dataInfo->field_list[$v]->gen_show_value();
                            } else {
                                echo $this->dataInfo->field_list[$v]->gen_editor($this->editor_typ);
                            }
                             ?>
                            <?php if ($this->dataInfo->field_list[$v]->tips!=''){ ?>
                            <p  class="help-block"><?=$this->dataInfo->field_list[$v]->tips?></p>
                            <?php } ?>
                    </td>
                <?
                    }
                ?>

                <?
                }
                ?>
            </tr>
            <?
            }
            ?>

        </table>
    </div>
    <div class="clearfix"></div>
</div>
