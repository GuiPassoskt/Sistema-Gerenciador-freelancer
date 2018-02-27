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
		<div class="table-head"><?=$this->lang->line('application_bank_transfer');?> <?=$this->lang->line('application_settings');?></div>
		<div class="table-div">
		<?php   
		$attributes = array('class' => '', 'id' => 'banktransfer');
		echo form_open_multipart($form_action, $attributes); 
		?>
<div class="form-group">
<br>

            <input name="bank_transfer" type="checkbox" class="checkbox" style="width:100%;" data-labelauty="<?=$this->lang->line('application_bank_transfer_active');?>" value="1" <?php if($settings->bank_transfer == "1"){ ?> checked="checked" <?php } ?>>

 </div>
<div class="form-group"><label><?=$this->lang->line('application_bank_transfer_details');?></label>
			<textarea name="bank_transfer_text" class="form-control summernote"><?=$settings->bank_transfer_text;?></textarea>
</div>


<div class="form-group">

			 <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
</div>
	 	 
		<?php echo form_close(); ?>
	
</div>
	</div>