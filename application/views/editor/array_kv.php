<?
$editorData = $this->kvEditorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($editorData->editor_typ==1){
    $editorData->default = $editorData->value;
}
?>
<table class="table table-bordered">
    <?php
    foreach ($editorData->allKeys as $key => $value) {
    ?>
    <tr>
        <td class="td_title"><?=$value?></td>
        <td class="td_data">
            <input  autocomplete="on" id="<?=$inputName.'_'.$key?>"  name="<?=$inputName.'_'.$key?>" class="<?=$editorData->input_class?>" type="text" value="<?=isset($editorData->default[$key])?$editorData->default[$key]:'0'?>" onchange="refresh_kv_table('<?=$inputName?>')"/>
        </td>
    </tr>
    <?
    }
    ?>
</table>
<script>
table_item_vars.<?=$inputName?> = <?=json_encode($editorData->allKeys)?>;
</script>
<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value='<?=json_encode($editorData->default)?>'/>
