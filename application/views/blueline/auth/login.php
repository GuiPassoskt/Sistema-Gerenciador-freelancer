<?php $attributes = array('class' => 'form-signin', 'role'=> 'form', 'id' => 'login'); ?>
<?=form_open('login', $attributes)?>
        <div class="logo"><img src="<?=base_url()?><?=$core_settings->invoice_logo;?>" alt="<?=$core_settings->company;?>"></div>
        <?php if($error == "true") { $message = explode(':', $message)?>
            <div id="error">
              <?=$message[1]?>
            </div>
        <?php } ?>
        
          <div class="form-group">
            <label for="username"><?=$this->lang->line('application_username');?></label>
            <input type="username" class="form-control" id="username" name="username" />
          </div>
          <div class="form-group">
            <label for="password"><?=$this->lang->line('application_password');?></label>
            <input type="password" class="form-control" id="password" name="password" />
          </div>

          <input type="submit" class="btn btn-primary fadeoutOnClick" value="<?=$this->lang->line('application_login');?>" />
          <div class="forgotpassword"><a href="<?=site_url("forgotpass");?>"><?=$this->lang->line('application_forgot_password');?></a></div>

          <?php if($core_settings->registration == 1){ ?><div class="small"><small><?=$this->lang->line('application_you_dont_have_an_account');?></small></div><hr/><a href="<?=site_url("register");?>" class="btn btn-success"><?=$this->lang->line('application_create_account');?></a> <?php } ?>
        
<?=form_close()?>

