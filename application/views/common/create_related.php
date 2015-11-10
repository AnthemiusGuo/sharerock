<div class="row">
    <div class="col-lg-12">
        <?php
        if (isset($this->related_field) && $this->related_field!=''){
            echo $this->dataInfo->field_list[$this->related_field]->gen_hidden_editor($this->editor_typ,$this->related_id);
        }
        ?>

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
                            if ($this->related_field==$v){
                                echo '<span>'.$this->dataInfo->field_list[$this->related_field]->gen_show_html().'</span>';
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
    <?
    if ($this->dataInfo->has_changelog){
    ?>

    <div class="col-lg-12">
    <span class="field_name">更新速记：</span>
    <textarea id="changelog" rows="4" name="changelog" class="form-control"></textarea>
    </div>
    <?
    }
    ?>
    <div class="clearfix"></div>
    <div class="col-lg-12">

    </div>
</div>
