<!-- Modal -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header logo-small">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><? echo $this->infoTitle; ?>
            <!--<div class="btn-group pull-right margin-right-20">
                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span>编辑</button>
                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span>删除</button>
            </div>-->
            </h4>
            
        </div>
        <div class="modal-body">
            <?php echo $contents; ?>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
$('.tooltips').powerTip({offset:20});
</script>