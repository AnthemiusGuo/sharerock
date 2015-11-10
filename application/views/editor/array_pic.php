<?
$editorData = $this->arrayPiceditorData;
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
    <div class="col-md-6">
        <div class="box-camera" id="holder_pic_<?=$key."_".$id?>">
            <div class="real_pic" id="real_pic_<?=$key."_".$id?>">
                <?
                if ($value!=""){
                ?>
                <img src="<?=static_url($editorData->uploadDir.'/'.$value)?>"/>
                <?
                }
                ?>
            </div>
            <span class="glyphicon glyphicon-camera"></span>
            <input type="file" id="input_pic_<?=$key."_".$id?>" name="input_<?=$key."_".$id?>" accept="image/*" capture="camera">
            <script>
                setAjaxUpload({
                    fileDom:"#input_pic_<?=$key."_".$id?>",
                    url:'<?=site_url($editorData->uploadUrl.'/'.$key."/".$id)?>',
                    successCallback:function(e,data){
                        json = data.result;
                        $("#real_pic_<?=$key."_".$id?>").html('<img src="'+json.data.url+'"/>');

                        now_<?=$key?>_value[<?=$id?>] = json.data.link;

                        $("#<?=$inputName?>").val(JSON.stringify(now_<?=$key?>_value));
                    }
                });
            </script>
        </div>
    </div>
    <?
    }
    ?>
    <?
    for ($id=count($values);$id<$editorData->imgCountLimit;$id++) {

    ?>
    <div class="col-md-6 hide" id="holder_col_<?=$key."_".$id?>">
        <div class="box-camera" id="holder_pic_<?=$key."_".$id?>">
            <div class="real_pic" id="real_pic_<?=$key."_".$id?>">

            </div>
            <span class="glyphicon glyphicon-camera"></span>
            <input type="file" id="input_pic_<?=$key."_".$id?>" name="input_<?=$key."_".$id?>" accept="image/*" capture="camera">
            <script>
                setAjaxUpload({
                    fileDom:"#input_pic_<?=$key."_".$id?>",
                    url:'<?=site_url($editorData->uploadUrl.'/'.$key."/".$id)?>',
                    successCallback:function(e,data){
                        json = data.result;
                        $("#real_pic_<?=$key."_".$id?>").html('<img src="'+json.data.url+'"/>');
                        now_<?=$key?>_value[<?=$id?>] = json.data.link;
                        $("#<?=$inputName?>").val(JSON.stringify(now_<?=$key?>_value));
                    }
                });
            </script>
        </div>
    </div>
    <?
    }
    ?>
    <div class="col-md-3" id="holder_col_<?=$key."_adder"?>">
        <div class="box-camera" id="holder_pic_0" onclick="add_<?=$key?>_img_upload_holder()">

            <span class="glyphicon glyphicon-plus"></span>
            <script>
                var now_<?=$key?>_counter = <?=count($values)?>;
                var max_<?=$key?>_counter = <?=$editorData->imgCountLimit?>;
                var now_<?=$key?>_value = <?=json_encode($values)?>;

                function add_<?=$key?>_img_upload_holder(){
                    $("#holder_col_<?=$key?>_"+now_<?=$key?>_counter).removeClass("hide");
                    now_<?=$key?>_counter ++;
                    if (now_<?=$key?>_counter>=max_<?=$key?>_counter){
                        $("#holder_col_<?=$key."_adder"?>").addClass("hide");
                    }

                }
            </script>
        </div>
    </div>
</div>
<input id="<?=$inputName?>" name="<?=$inputName?>" type="hidden" value='<?=json_encode($editorData->default)?>'/>
