<?
$editorData = $this->carTypeEditorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($typ==1){
    $editorData->default = $editorData->value;
}

?>
<div id="selector_<?=$inputName?>">
</div>
<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value="<?=$editorData->default?>"/>
<script>
getAllSpeedList("<?=$inputName?>");
</script>