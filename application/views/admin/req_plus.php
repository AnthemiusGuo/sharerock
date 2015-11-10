<div class="row">
<div class="col-lg-12 list-title-op ">
    <div class="input-group input-group-sm">
      <span class="input-group-addon">状态</span>
      <select id="filter_status" name="filter_status" class="form-control">
        
      <?
        foreach ($this->dataModel->field_list['status']->enum as $key => $value) {
      ?>
          <option value="<?=$key?>" <?=($this->status==$key)?'selected':''?> ><?=$value?></option>
      <?
        }
      ?>
      </select>
      <span class="input-group-addon">从</span>
      <input id="filter_beginTS" type="text" class="form-control" placeholder="<?=$this->from?>" value="<?=$this->from?>">
      <span class="input-group-addon">到</span>
      <input id="filter_endTS" type="text" class="form-control" placeholder="<?=$this->to?>" value="<?=$this->to?>">
      <span class="input-group-btn">
        <button class="btn btn-primary  btn-sm" type="button" onclick="search_req()">查询</button>
        <button class="btn btn-warning  btn-sm" type="button" onclick="reset_req()">重置</button>
      </span>
    </div>
    <script type="text/javascript">
        $(function(){
          $("#filter_beginTS").datetimepicker({"autoclose": true,"language": "zh-CN","calendarMouseScroll": false,"dateOnly":true,format: 'yyyy-mm-dd',startView:'year',minView:'month'});
          $("#filter_endTS").datetimepicker({"autoclose": true,"language": "zh-CN","calendarMouseScroll": false,"dateOnly":true,'format' : 'yyyy-mm-dd',startView:'year',minView:'month'});
        });
        function search_req(){
          var url = req_url_template.str_supplant({ctrller:'aadmin',action:'req'});
          url= url+'/'+$("#filter_status").val()+'/'+$("#filter_beginTS").val()+'/'+$("#filter_endTS").val();
          window.location.href=url;
        }
        function reset_req(){
          var url = req_url_template.str_supplant({ctrller:'aadmin',action:'req'});
          window.location.href=url;
        }
    </script>
</div>
<div class="col-lg-12 list-title-op ">
    故事点总计：<?=$this->totalStoryPoints?>
</div>
</div>
