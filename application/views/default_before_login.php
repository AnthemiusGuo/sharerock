<?php include_once('common/header.php')?>
<?php echo link_tag(static_url('css/login2.css')); ?>
<body class="<?=$this->pageClass ?>">
<div class="container-fluid">
	<div class="row">
	  	<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12"></div>
	  	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"><div class="logo">
			<a href="<?=site_url()?>">
					后台登录
			</a>
		</div></div>
	  	<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12"></div>
	</div>
	<div class="row">
	  	<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12"></div>
	  	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 login_contents">
			<?php echo $contents; ?>
		</div>
	  	<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12"></div>
	</div>
</div>
<?php include_once('common/footer.php')?>
