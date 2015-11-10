<?
$guzhangDataInfo = $this->xiangmuDataInfo;
?>
<div class="panel panel-default">
	<div class="panel-body">
	<!-- <button type="button" class="btn btn-primary" onclick="new_book_xiangmu('<?=$guzhangDataInfo->bookId?>')">新建</button> -->
	</div>
</div>
<?
foreach ($guzhangDataInfo->real_data as $key => $this_item) {
?>
	<div class="panel panel-default">
		<!-- Default panel contents -->
		<div class="panel-heading"><?=$this_item->field_list['name']->gen_show_value();?>

		<button type="button" class="btn btn-primary" onclick='reqDelete("acrm","doDelBookGuzhang","<?=$guzhangDataInfo->bookId?>/<?=$this_item->field_list['_id']->toString();?>")'>删除</button>
		</div>

	  	<!-- Table -->
    	<table class="table table-striped">
    		<tbody>
		        <tr>
		            <th class="td_title">
		                <?php echo $this_item->field_list['name']->gen_show_name(); ?>
		            </th>
		            <th class="td_data">
		                <?php echo $this_item->field_list['name']->gen_show_html() ?>
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
		                <?php echo $this_item->field_list['jiage']->gen_show_name(); ?>
		            </td>
		            <td colspan="3">
		                <?php echo $this_item->field_list['jiage']->gen_show_html() ?>
		            </td>
		        </tr>
		        <tr>
		            <td class="td_title">
		                <?php echo $this_item->field_list['pic']->gen_show_name(); ?>
		            </td>
		            <td colspan="3">
		                <?php echo $this_item->field_list['pic']->gen_show_html() ?>
		            </td>
		        </tr>
		    </tbody>
	  </table>
	</div>
<?
}
?>