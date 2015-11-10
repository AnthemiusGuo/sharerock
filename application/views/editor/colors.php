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
foreach ($editorData->datas as $item) {
    $now_data_counter ++;
    $now_data[$item->field_list['_id']->toString()] = array('_id'=>$item->field_list['_id']->toString(),
                                                            'colorName'=>$item->field_list['colorName']->gen_show_value(),
                                                            'subprice'=>$item->field_list['subprice']->value);
}
// var_dump($now_data);
?>
</ul>
<table class="table table-bordered">
    <tr>
        <td class="td_title"><?=$editorData->dataModel->field_list['colorName']->gen_editor_show_name()?></td>
        <td class="td_data"><?=$editorData->dataModel->field_list['colorName']->gen_editor($editorData->editor_typ,false)?></td>
        <td class="td_title">
        <?=$editorData->dataModel->field_list['subprice']->gen_editor_show_name()?></td>
        <td class="td_data"><?=$editorData->dataModel->field_list['subprice']->gen_editor($editorData->editor_typ,false)?></td>
    </tr>
    <tr>
        <td colspan="4"><button type="button" class="btn btn-success" onclick="addSubLine(<?=$editorData->editor_typ?>,'<?=$inputName?>')">增加</button></td>
    </tr>
</table>

<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value="<?=$editorData->default?>"/>
<script>
var table_item_vars = <?=json_encode($editorData->listFields)?>;
var table_item_must_vars = {colorName:true,subprice:true};
var table_item_template = '<li class="list-group-item"><span>{colorName}&nbsp;&nbsp;(参考售价{subprice} ￥ / 米|件) <a href="javascript:void(0);" onclick="removeSubLine(\'<?=$inputName?>\',\'{_id}\')"><span class="glyphicon glyphicon-remove pull-right"></span></a></li>';
<?
if ($now_data_counter<=0){
?>
var table_all_data = {};
<?
} else {
?>
var table_all_data = <?=json_encode($now_data)?>;
<?
}
if ($editorData->editor_typ==0){
?>
var price_id_pre = 'creator_';
<?
} else {
?>
var price_id_pre = 'modify_';
<?
}
?>
resetTable('<?=$inputName?>');
</script>
