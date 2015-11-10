<!-- Modal -->
<div class="modal-header logo-small">
    <h4 class="modal-title" id="myModalLabel"><?php echo $this->infoTitle ?></h4>
</div>
<div class="modal-body">
        <?php echo $contents; ?>
</div>        
<div class="modal-footer">
    <?
    if ($this->canImport) {
    ?>
    <button type="button" class="btn btn-default" onclick="$.fancybox.close();">取消</button>
    <button type="button" class="btn btn-primary" onclick="reqImport('aexport','doRealImport','<?=$this->typ?>','<?=$this->fileName?>')">保存</button>
    <?
    }
    ?>
</div>
<script>
    var allInserts = <?=json_encode(array_keys($this->lineCheckerInsert))?>;
    var allUpdates = <?=json_encode(array_keys($this->lineCheckerUpdate))?>;
    var allDeletes = <?=json_encode(array_keys($this->lineCheckerDelete))?>;

</script>