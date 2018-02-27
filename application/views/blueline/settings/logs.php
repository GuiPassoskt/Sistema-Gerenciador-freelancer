<div id="row">
	
		<div class="col-md-3">
			<div class="list-group">
				<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
				if($value == "settings/updates"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
				if($name == $breadcrumb){ $active = 'active';}?>
	               <a class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
	            <?php endforeach;?>
			</div>
		</div>


<div class="col-md-9">
<div class="row">
		<div class="span12 marginbottom20">
		<div class="table-head">Logs <span class="pull-right"><a href="<?=base_url()?>settings/logs" class="btn btn-success"><?=$this->lang->line('application_refresh');?></a> <a href="<?=base_url()?>settings/logs/clear" class="btn btn-primary"><?=$this->lang->line('application_clear');?></a></span></div>
		<div class="subcont">
		<ul class="details span12">
			<?php foreach ($logs as $value) { $value = str_replace("ERROR -", '', $value); ?>
				<?php 
					//highlight notice
					$value = highlight_phrase($value, "Severity: Notice", '<span class="label label-success">', '</span>');
					//highlight warning
					$value = highlight_phrase($value, "Severity: Warning", '<span class="label label-warning tt">', '</span>');
					//highlight error
					$value = highlight_phrase($value, "Severity: Error", '<span class="label label-important tt">', '</span>');
					if (strpos($value,'filemtime(): stat failed for') !== false) {
					    $value = mb_substr($value, 0, 20)." --> Full database backup has been created.";
					}
				?>
				<li style="font-size: 12px;"><?=$value;?></li>
			<?php } ?>
			<?php if(empty($logs)){echo "<li>No log entrys yet</li>";}?>
			
		</ul>
		<br clear="all">
		</div>
		</div>
		</div>
</div>