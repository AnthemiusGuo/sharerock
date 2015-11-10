<?
$editorData = $this->editorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($typ==1){
    $editorData->default = $editorData->value;
}
?>
<ul class="list-group"  id="table_<?=$inputName?>">
<?
$now_data = array();
$now_data_counter = 0;
foreach ($editorData->real_data as $item) {
    $now_data_counter ++;
    $item_data = array();
    foreach ($editorData->listFields as $key) {
        $item_data[$key] = $item->field_list[$key]->gen_show_value();
    }
    $now_data[$item->field_list['_id']->toString()] = $item_data;
}
// var_dump($now_data);
?>
</ul>
<table class="table table-bordered">
    <?php
            foreach ($editorData->showListFields as $key => $value) {
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
                    <td class="td_title"><?=$editorData->dataModel->field_list[$v]->gen_editor_show_name()?></td>
                    <td <?=($colspan==0)?'class="td_data"':'colspan="'.$colspan.'"'?> >
                            <?=$editorData->dataModel->field_list[$v]->gen_editor($editorData->editor_typ,false)?>
                            <?php if ($this->dataInfo->field_list[$v]->tips!=''){ ?>
                            <p  class="help-block"><?=$editorData->dataModel->field_list[$v]->tips?></p>
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
    <tr>
        <td colspan="4"><button type="button" class="btn btn-success" onclick="addSubLine(<?=$editorData->editor_typ?>,'<?=$inputName?>')">增加</button></td>
    </tr>
</table>

<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value="<?=$editorData->default?>"/>
<script>
table_item_vars.<?=$inputName?> = <?=json_encode($editorData->listFields)?>;
table_item_must_vars.<?=$inputName?> = <?=json_encode($editorData->mustFields)?>;
table_item_template.<?=$inputName?> = '<li class="list-group-item"><span><?=$editorData->templates?></span> <a href="javascript:void(0);" onclick="removeSubLine(\'<?=$inputName?>\',\'{_id}\')"><span class="glyphicon glyphicon-remove pull-right"></span></a></li>';
<?
if ($now_data_counter<=0){
?>
table_all_data.<?=$inputName?> = {};
<?
} else {
?>
table_all_data.<?=$inputName?> = <?=json_encode($now_data)?>;
<?
}
?>
resetTable('<?=$inputName?>');
</script>
