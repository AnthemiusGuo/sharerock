<?
if ($this->need_plus!=""){
    include_once(APPPATH."views/".$this->need_plus.".php");
}
?>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped">
            <?php
            foreach ($this->showNeedFields as $key => $value) {
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
                    <td class="td_title"><?php echo $this->dataInfo->field_list[$v]->gen_show_name(); ?></td>
                    <td <?=($colspan==0)?'class="td_data"':'colspan="'.$colspan.'"'?> >
                            <?php echo $this->dataInfo->field_list[$v]->gen_show_html() ?>
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
    <div class="col-lg-12">

    </div>
    <?
    if ($this->dataInfo->has_changelog){
    ?>
    <div class="col-lg-12">
        <div class="panel panel-default calendar">
              <div class="panel-heading">
                <h3 class="panel-title">更新日志</h3>
              </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <?
                        $row_counter = 0;
                        foreach ($this->changelogList->listKeys as $key_names):
                            $row_counter++;
                        ?>
                            <th>
                                <?php
                                echo $this->changelogList->dataModel[$key_names]->gen_show_name();;
                                ?>
                            </th>
                        <?
                        endforeach;
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $now_begin_ts = 0;

                    foreach($this->changelogList->record_list as  $this_record):
                        $this_begin_ts = $this_record->field_list['beginTS']->formatTSAsDayBeginTS();
                        if ($this_begin_ts!=$now_begin_ts){
                            $now_begin_ts = $this_begin_ts;
                        ?>
                        <tr>
                            <th colspan="<?=$row_counter?>"><?=date("Y-m-d",$this_record->field_list['beginTS']->value)?></th>
                        </tr>
                        <?
                        }
                        $i++;
                        ?>
                        <tr>

                            <?
                            foreach ($this->changelogList->listKeys as $key_names):
                            ?>
                                <td>
                                    <?php
                                    if ($this_record->field_list[$key_names]->is_title):
                                        if ($this->changelogList->is_lightbox):
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
                        </tr>
                    <?php
                    endforeach; ?>

                </tbody>
            </table>
            <?php
            if (count($this->changelogList->record_list)==0):
            ?>
                <div class="no-data-large">
                    没有相关更新日志
                </div>
            <?
            endif;
            ?>
        </div>
    </div>
    <?
    }
    ?>
</div>
