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
<div class="panel panel-default">
    <div class="panel-heading">
        文件名：<span id="file_name_<?=$key?>"><?=$value?></span>
    </div>
    <div class="panel-body">
        <?
        if ($value!=""){
        ?>
        <a href="<?=static_url('duploads/'.$value)?>" target="_blank">文件下载：<?=$value?></a>
        <div>
            *注意!!新文件上传将替换旧有文件！
        </div>
        <?
        }
        ?>
    </div>
    <div class="panel-footer">
        <a href="javascript:void(0);" class="btn btn-danger ajax_input_holder">
        <span class="glyphicon glyphicon-paperclip"></span> 上传
        <input type="file" id="input_doc_<?=$key?>" name="input_<?=$key?>"  class="ajax_input">
        </a>
        <script>
            setAjaxUpload({
                fileDom:"#input_doc_<?=$key?>",
                url:'<?=site_url($editorData->uploadUrl.'/'.$key)?>',
                successCallback:function(e,data){
                    json = data.result;
                    $("#file_name_<?=$key?>").html(json.data.link);
                    $("#<?=$inputName?>").val(json.data.link);

                }
            });
        </script>

    </div>
</div>
<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value="<?=$editorData->default?>"/>
