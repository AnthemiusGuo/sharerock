<!-- Modal -->
        <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->title_create ?>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm pull-right" onclick="lightbox({size:'m',url:'<?=site_url($this->dataInfo->short_info_link).'/'.$this->dataInfo->id?>'})">
                        <span class="glyphicon glyphicon-search"></span> 查看
                    </a></h4>
            </div>
        </div>
        <div class="modal-body">
            <form role="form" id="editForm">
                <input type="hidden" id="modify_id" name="modify_id" value="<?=$this->dataInfo->field_list['_id']->toString()?>"/>
                <?php echo $contents; ?>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" onclick="$.fancybox.close();">取消</button>
            <button type="button" class="btn btn-primary" onclick="reqEdit('<?=$this->createUrlC?>','<?=$this->createUrlF?>',reqEditFields,editFormValidator)">保存</button>
        </div>
<script>
var editFormValidator = $("#editForm").validate();
var reqEditFields = [];
<?php
foreach ($this->createPostFields as $key => $value) {
    echo 'reqEditFields.push({name:"'.$value.'",type:"'.$this->dataInfo->field_list[$value]->typ.'"});';
}
?>
</script>
