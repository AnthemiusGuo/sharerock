<?
$guzhangDataInfo = $this->peijianDataInfo;
?>

<?
foreach ($guzhangDataInfo->real_data as $key => $this_item) {
?>
	<div class="panel panel-default">
		<!-- Default panel contents -->
		<div class="panel-heading"><?=$this_item->field_list['name']->gen_show_value();?></div>
		<div class="panel-body">


		</div>

	  	<!-- Table -->
    	<table class="table table-striped">
    		<tbody>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['name']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['name']->gen_show_html() ?>
		            </td>
		            <td class="td_title">
		                <?php echo $this_item->field_list['pinpai']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['pinpai']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['xinghao']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['xinghao']->gen_show_html() ?>
		            </td>
		            <td class="td_title">
		                <?php echo $this_item->field_list['biaoshi']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['biaoshi']->gen_show_html() ?>
		            </td>
		        </tr>
				<tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['counter']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['counter']->gen_show_html() ?>
		            </td>
		            <td class="td_title">
		                <?php echo $this_item->field_list['baoxiu']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['baoxiu']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['pics']->gen_show_name(); ?>
		            </td>
		            <td colspan="3">
		                <?php echo $this_item->field_list['pics']->gen_show_html() ?>
		            </td>
		        </tr>
		    </tbody>
	  </table>
	</div>
<?
}
?>
