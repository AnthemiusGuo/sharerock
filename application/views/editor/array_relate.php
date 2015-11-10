<?
$editorData = $this->relateEditorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($editorData->editor_typ==1){
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
    $item_data['_id'] = $item->id;
    $item_data['relate_'.$editorData->tableName] = $item->id;
    $item_data['_show_name'] = $item->field_list['name']->value;
    $now_data[$item->field_list['_id']->toString()] = $item_data;
}
// var_dump($now_data);
?>
</ul>
<table class="table table-bordered">
    <tr>
        <td>
            <?=$editorData->gen_input($editorData->editor_typ)?>
        </td>
    <tr>
        <td><button type="button" class="btn btn-success" onclick="addSubLine(<?=$editorData->editor_typ?>,'<?=$inputName?>')">增加</button></td>
    </tr>
</table>

<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value="<?=$editorData->default?>"/>
<script>
table_item_vars.<?=$inputName?> = <?=json_encode($editorData->listFields)?>;
table_item_must_vars.<?=$inputName?> = <?=json_encode($editorData->mustFields)?>;
table_item_template.<?=$inputName?> = '<li class="list-group-item"><span><?=$editorData->templates?></span> <a href="javascript:void(0);" onclick="removeSubLine(\'<?=$inputName?>\',\'{_id}\')"><span class="glyphicon glyphicon-remove pull-right"></span></a></li>';
table_item_enums.<?=$inputName?> = <?=json_encode($editorData->enum)?>;
table_enumKey.<?=$inputName?> = "<?='relate_'.$editorData->tableName?>";
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
