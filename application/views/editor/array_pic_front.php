<?
$editorData = $this->arrayPiceditorData;
$inputName = $editorData->name;
$validates = $editorData->build_validator();
if ($editorData->editor_typ==1){
    $editorData->default = $editorData->value;
}
?>
<div class="row">
<script>
    var real_data_<?=$inputName?> = <?=json_encode($editorData->default)?>;
    function setUploadRst(key,value){
        real_data_<?=$inputName?>[key] = value;
    }
</script>
<?
foreach ($editorData->default as $key => $value) {
?>
	<div class="col-md-3 text_center">

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
            <span class="fa fa-camera"></span>
            <input type="file" id="input_pic_<?=$key?>" name="input_pic" accept="image/*" capture="camera">
            <script>
                setAjaxUpload({
                    fileDom:"#input_pic_<?=$key?>",
                    url:'<?=$editorData->uploadUrl.'/'.$key?>',
                    successCallback:function(json){
                        $("#real_pic_<?=$key?>").html('<img src="'+json.data.url+'"/>');
                        setUploadRst(json.data.key,json.data.link);
                    },
                    successCallbackOld:function(e,data){
                        json = data.result;
                        $("#real_pic_<?=$key?>").html('<img src="'+json.data.url+'"/>');
                        setUploadRst(json.data.key,json.data.link);
                    },
                });
            </script>
        </div>

    </div>
<?
}
?>
</div>
