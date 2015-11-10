<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<h4 class="text-center"><?=$this->title?></h4>
	</div>
	<div class="col-sm-6 col-md-6 col-lg-6">
		<form class="form-inline">
            <div class="input-group input-group-sm">
                <input type="text" id="related-search" name="related-search" class="form-control" placeholder="请输入<?=$this->quickSearchName?>" value="<?=$this->quickSearchValue?>">
                <div class="input-group-btn">
                    <a class="btn btn-primary" onclick="relatedSearch('<?=$this->controller_name?>','<?=$this->method_name?>')"> <span class="glyphicon glyphicon-search"></span> </a>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="relatedDirectAdd()"> <span class="glyphicon glyphicon-plus"></span></span>  
                    </a>
                </div>

            </div>

        </form>
		<div class="clearfix"><br/></div>

		<?php echo $contents; ?>
	</div>


	<div class="col-sm-6 col-md-6 col-lg-6">
		已选择：
		<div class="select_contents">
			<ul id="relate_box_choosed" class="list-search">
				
        		
			</ul>
		</div>
		<div class="btn_group pull-right">
			<a class="btn btn-sm btn-default" href="javascript:void(0);" onclick="hide_relate_box()">取消</a>
			<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="buildRelateResult()">确定</a>
		</div>
	</div>
		
</div>
<script>