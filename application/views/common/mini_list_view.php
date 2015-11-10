<?php
$thisListInfo = $this->dataInfo;
?>
<div class="row">
    
    <div class="col-lg-12">
        <table class="table table-striped simplePagerContainer">
            <thead>
                <tr>
                    <?
                    foreach ($thisListInfo->build_list_titles() as $key_names):
                    ?>
                        <th>
                            <?php
                            echo $thisListInfo->dataModel[$key_names]->gen_show_name();;
                            ?>
                        </th>
                    <?
                    endforeach;
                    ?>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody class="table-paged">
                <?php
                $i = 1;
                foreach($thisListInfo->record_list as  $this_record): ?>
                    <tr>

                        <?
                        foreach ($thisListInfo->build_list_titles() as $key_names):
                        ?>
                            <td>
                                <?php
                                if ($this_record->field_list[$key_names]->is_title):
                                    if ($thisListInfo->is_lightbox):
                                        echo '<a href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})">'.$this_record->field_list[$key_names]->gen_list_html().'</a>';
                                    else :
                                        echo '<a href="'.site_url($this_record->info_link.$this_record->id).'">'.$this_record->field_list[$key_names]->gen_list_html().'</a>';
                                    endif;
                                elseif ($this_record->field_list[$key_names]->typ=="Field_text"):
                                    echo $this_record->field_list[$key_names]->gen_list_html();
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
                            if ($thisListInfo->is_lightbox):
                                echo '<a class="list_op tooltips" href="javascript:void(0)" onclick="lightbox({size:\'m\',url:\''. site_url($this_record->info_link.$this_record->id).'\'})"><span class="glyphicon glyphicon-search"></span></a>';
                            else :
                                echo '<a  class="list_op tooltips" href="'.site_url($this_record->info_link.$this_record->id).'"><span class="glyphicon glyphicon-search"></span></a>';
                            endif;
                            ?>
                             |
                            <?php
                            if ($this->canEdit) {
                                echo $this_record->gen_list_op();
                            }
                            ?>
                        </td>
                    </tr>
                <?php $i++;
                endforeach; ?>

            </tbody>
        </table>

        <div id="main_pager">

        </div>
        <?php
        if (count($thisListInfo->record_list)==0):
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
