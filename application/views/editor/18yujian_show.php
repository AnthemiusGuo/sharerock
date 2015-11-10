<?
$yujanDataInfo = $this->yujanDataInfo;
?>
<?
foreach ($yujanDataInfo->real_data as $key => $this_item) {
?>
	<div class="panel panel-default">
		<!-- Default panel contents -->
		<div class="panel-heading"><?=$this_item->field_list['typ']->gen_show_value();?></div>
		<!-- <div class="panel-body">
			<button type="button" class="btn btn-primary" onclick="edit_book_jichujiance('<?=$yujanDataInfo->bookId?>','<?=$key?>')">编辑</button>
		</div> -->

	  	<!-- Table -->
    	<table class="table table-striped">
    		<tbody>
		        <tr>
		            <th class="td_title">
		                <?php echo $this_item->field_list['typ']->gen_show_name(); ?>
		            </th>
		            <th class="td_data">
		                <?php echo $this_item->field_list['typ']->gen_show_html() ?>
		            </th>
		            <th class="td_title">
		                <?php echo $this_item->field_list['xtyp']->gen_show_name(); ?>
		            </th>
		            <th class="td_data">
		                <?php echo $this_item->field_list['xtyp']->gen_show_html() ?>
		            </th>

		        </tr>
		        <tr>
		        	
		            <td class="td_title">
		                <?php echo $this_item->field_list['result']->gen_show_name(); ?>
		            </td>
		            <td  class="td_data">
		                <?php echo $this_item->field_list['result']->gen_show_html() ?>
		            </td>
		             <td class="td_title">
		                <?php echo $this_item->field_list['endTS']->gen_show_name(); ?>
		            </td>
		            <td  class="td_data">
		                <?php echo $this_item->field_list['endTS']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>

		            <td class="td_title">
		                <?php echo $this_item->field_list['suggest']->gen_show_name(); ?>
		            </td>
		            <td colspan="3">
		                <?php echo $this_item->field_list['suggest']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['peijians']->gen_show_name(); ?>
		            </td>
		            <td colspan="3">
		                <?php echo $this_item->field_list['peijians']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['peijianfei']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['peijianfei']->gen_show_html() ?>
		            </td>
		            <td class="td_title">
		                <?php echo $this_item->field_list['gongshifei']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['gongshifei']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['youhui']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['youhui']->gen_show_html() ?>
		            </td>
		            <td class="td_title">
		                <?php echo $this_item->field_list['jiage']->gen_show_name(); ?>
		            </td>
		            <td class="td_data">
		                <?php echo $this_item->field_list['jiage']->gen_show_html() ?>
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