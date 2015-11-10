<?php include_once('common/header.php');
?>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12"><?php echo $contents; ?></div>
        </div>
    </div>
<script>
jQuery(document).ready(function() {
    $(".table-paged").quickPager({pageSize:10,holder:'#main_pager',struct:'tbody'});
    $('.tooltips').powerTip({offset:20});
});
</script>
<!-- END JAVASCRIPTS -->
<?php include_once('common/footer.php')?>
