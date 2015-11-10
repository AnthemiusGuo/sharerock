<?
$editorData = $this->piceditorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($editorData->editor_typ==1){
    $editorData->default = $editorData->value;
}
?>
<?
$key = $editorData->name;
$value = $editorData->default;
?>
<div class="box-camera" id="holder_pic_<?=$key?>">
    <div class="real_pic" id="real_pic_<?=$key?>">
        <?
        if ($value!=""){
        ?>
        <img src="<?=static_url('uploads/'.$value)?>"/>
        <?
        }
        ?>
    </div>
    <span class="glyphicon glyphicon-camera"></span>
    <input type="file" id="input_pic_<?=$key?>" name="input_<?=$key?>" accept="image/*" capture="camera">
    <script>
        setAjaxUpload({
            fileDom:"#input_pic_<?=$key?>",
            url:'<?=site_url($editorData->uploadUrl.'/'.$key)?>',
            successCallback:function(e,data){
                json = data.result;
                $("#real_pic_<?=$key?>").html('<img src="'+json.data.url+'"/>');
                $("#<?=$inputName?>").val(json.data.link);
            }
        });
    </script>
</div>
<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value="<?=$editorData->default?>"/>
