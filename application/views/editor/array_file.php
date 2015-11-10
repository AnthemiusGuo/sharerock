<?
$editorData = $this->arrayFileeditorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($editorData->editor_typ==1){
    $editorData->default = $editorData->value;
}
?>
<?
$key = $editorData->name;
$values = $editorData->default;
?>
<div class="row">
    <?

    foreach ($values as $id=>$value) {
    ?>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                文件名：<span id="file_name_<?=$key."_".$id?>"><?=$value?></span>
            </div>
            <div class="panel-body">
                <?
                if ($value!=""){
                ?>
                <a href="<?=static_url($editorData->uploadDir.'/'.$value)?>" target="_blank">文件下载：<?=$value?></a>
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
                <input type="file" id="input_doc_<?=$key."_".$id?>" name="input_<?=$key."_".$id?>"  class="ajax_input">
                </a>
                <script>
                    setAjaxUpload({
                        fileDom:"#input_doc_<?=$key."_".$id?>",
                        url:'<?=site_url($editorData->uploadUrl.'/'.$key."/".$id)?>',
                        successCallback:function(e,data){
                            json = data.result;
                            $("#file_name_<?=$key."_".$id?>").html(json.data.link);
                            now_<?=$key?>_value[<?=$id?>] = json.data.link;
                            $("#<?=$inputName?>").val(JSON.stringify(now_<?=$key?>_value));

                        }
                    });
                </script>

            </div>
        </div>
    </div>
    <?
    }
    ?>
    <?
    for ($id=count($values);$id<$editorData->imgCountLimit;$id++) {

    ?>
    <div class="col-md-4 hide" id="holder_col_<?=$key."_".$id?>">
        <div class="panel panel-default">
            <div class="panel-heading">
                文件名：<span id="file_name_<?=$key."_".$id?>"></span>
            </div>

            <div class="panel-footer">
                <a href="javascript:void(0);" class="btn btn-danger ajax_input_holder">
                <span class="glyphicon glyphicon-paperclip"></span> 上传
                <input type="file" id="input_doc_<?=$key."_".$id?>" name="input_<?=$key."_".$id?>"  class="ajax_input">
                </a>
                <script>
                    setAjaxUpload({
                        fileDom:"#input_doc_<?=$key."_".$id?>",
                        url:'<?=site_url($editorData->uploadUrl.'/'.$key."/".$id)?>',
                        successCallback:function(e,data){
                            json = data.result;
                            $("#file_name_<?=$key."_".$id?>").html(json.data.link);
                            now_<?=$key?>_value[<?=$id?>] = json.data.link;
                            $("#<?=$inputName?>").val(JSON.stringify(now_<?=$key?>_value));

                        }
                    });
                </script>

            </div>
        </div>
    </div>
    <?
    }
    ?>
    <div class="col-md-4" id="holder_col_<?=$key."_adder"?>">
        <a href="javascript:void(0);" class="btn btn-info ajax_input_holder" onclick="add_<?=$key?>_file_upload_holder()"><span class="glyphicon glyphicon-plus"></span> 增加一条</a>
            <script>
                var now_<?=$key?>_counter = <?=count($values)?>;
                var max_<?=$key?>_counter = <?=$editorData->imgCountLimit?>;
                var now_<?=$key?>_value = <?=json_encode($values)?>;
                function add_<?=$key?>_file_upload_holder(){
                    $("#holder_col_<?=$key?>_"+now_<?=$key?>_counter).removeClass("hide");
                    now_<?=$key?>_counter ++;
                    if (now_<?=$key?>_counter>=max_<?=$key?>_counter){
                        $("#holder_col_<?=$key."_adder"?>").addClass("hide");
                    }

                }
            </script>
    </div>
</div>
<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value='<?=json_encode($editorData->default)?>'/>
