<?
$editorData = $this->editorData;
$inputName = $editorData->build_input_name($editorData->editor_typ);
$validates = $editorData->build_validator();
if ($typ==1){
    $editorData->default = $editorData->value;
}
?>
<?
foreach ($editorData->real_data as $key => $this_item) {
?>
	<div class="panel panel-default">
	  <!-- Default panel contents -->
	  <div class="panel-heading"><?=$this_item->field_list['typ']->gen_show_value();?></div>
	  <div class="panel-body">
	    <p>...</p>
	  </div>

	  <!-- Table -->
	  <table class="table">
	    
	  </table>
	</div>
<?
}
?>