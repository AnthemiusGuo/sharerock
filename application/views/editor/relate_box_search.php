<?
$inputName = $this->editorData->build_input_name($this->editorData->editor_typ);
$validates = $this->editorData->build_validator();
?>
<input id="<?=$inputName?>" name="<?=$inputName?>" class="form-control input-sm" placeholder="" type="text" value="<?=$this->editorData->default?>" >