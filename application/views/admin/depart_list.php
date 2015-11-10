<ul class="nav nav-tabs">
    
    <li role="presentation" class="<?=($this->departId=="null")?'active':''?>">
        <a href="<?=site_url($this->controller_name.'/'.$this->method_name.'/null')?>">未分配</a>
    </li>
    <?
    foreach ($this->departList->record_list as $this_record) {
    ?>
    <li role="presentation" class="<?=($this->departId==$this_record->id)?'active':''?>">
        <a href="<?=site_url($this->controller_name.'/'.$this->method_name.'/'.$this_record->id)?>"><?=$this_record->name?>
        </a>
    </li>
    <?
    }
    ?>
</ul>
