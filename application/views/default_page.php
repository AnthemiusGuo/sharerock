<?php include_once('common/header.php');
?>
<body class="page">
  <!-- BEGIN HEADER -->
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">如梦</a>
      </div>
      <div class="collapse navbar-collapse" id="main-nav">
          <?
          if ($this->is_logined){
            include_once('common/navbar-logined.php');
          } else {
            include_once('common/navbar-non-login.php');
          }
          ?>
      </div>
    </div>
  </nav>
  <!-- END HEADER -->
  <div class="clearfix">
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-9">
        <?php echo $contents; ?>
      </div>
      <div class="col-md-3">
        <?
        if ($this->is_logined){
          include_once('common/sidebar-logined.php');
        } else {
          include_once('common/sidebar-non-login.php');
        }
        ?>
      </div>
    </div>
  </div>
<script>
jQuery(document).ready(function() {
    $(".table-paged").quickPager({pageSize:10,holder:'#main_pager',struct:'tbody'});
    $('.tooltips').powerTip({offset:20});
    getEmailCount();
});
var relate_datas = {};
var relate_datas_result = [];

var table_item_vars = {};
var table_item_must_vars = {};
var table_item_template = {};
var table_all_data = {};

</script>
<!-- END JAVASCRIPTS -->
<?php include_once('common/footer.php')?>
