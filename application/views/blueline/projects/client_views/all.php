<div class="col-sm-13  col-md-12 main">    
     <div class="row">
        <div class="btn-group pull-right margin-right-3">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php $last_uri = $this->uri->segment($this->uri->total_segments()); if($last_uri != "cprojects"){echo $this->lang->line('application_'.$last_uri);}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right" role="menu">
            <?php foreach ($submenu as $name=>$value):?>
                  <li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
              <?php endforeach;?>
          </ul>
      </div>
    </div>  
      <div class="row">

         <div class="table-head"><?=$this->lang->line('application_projects');?></div>
         <div class="table-div">
         <table class="data table" id="cprojects" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
                <thead>
                  <tr>
                      <th width="20px" class="hidden-xs"><?=$this->lang->line('application_project_id');?></th>
                      <th class="hidden-xs" width="19px" class="no-sort sorting"></th>
                      <th><?=$this->lang->line('application_name');?></th>
                      <th class="hidden-xs"><?=$this->lang->line('application_client');?></th>
                      <th class="hidden-xs"><?=$this->lang->line('application_deadline');?></th>
                      <th class="hidden-xs"><?=$this->lang->line('application_assign_to');?></th>
                  </tr></thead>
                
                <tbody>
                <?php foreach ($project as $value):

          ?>
                <tr id="<?=$value->id;?>">
                  <td class="hidden-xs"><?=$value->reference;?></td>
                  <td class="hidden-xs">

                    <div class="circular-bar tt" title="<?=$value->progress;?>%">
                      <input type="hidden" class="dial" data-fgColor="<?php if($value->progress== "100"){ ?>#43AC6E<?php }else{ ?>#11A7DB<?php } ?>" data-width="19" data-height="19" data-bgColor="#e6eaed"  value="<?=$value->progress;?>" >

                      </div>
       
                </td>
                  <td onclick=""><?=$value->name;?></td>
                  <td class="hidden-xs"><a class="label label-info"><?php if(!isset($value->company->name)){echo $this->lang->line('application_no_client_assigned'); }else{ echo $value->company->name; }?></a></td>
                  <td class="hidden-xs"><span class="hidden-xs label label-success <?php if($value->end <= date('Y-m-d') && $value->progress != 100){ echo 'label-important tt" title="'.$this->lang->line('application_overdue'); } ?>"><?php $unix = human_to_unix($value->end.' 00:00');echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span></td>
                  <td class="hidden-xs">
                    <?php foreach ($value->project_has_workers as $workers):?> 
                    <?php
                     
                          $image = get_user_pic($workers->user->userpic, $workers->user->email);
                         
                                
                      ?>
                      <img class="img-circle tt" src="<?=$image;?>" title="<?php echo $workers->user->firstname.' '.$workers->user->lastname;?>" height="19px"><span class="hidden"><?php echo $workers->user->firstname.' '.$workers->user->lastname;?></span>
                    
                    <?php endforeach;?>
                  </td>
               
                </tr>
          
            <?php endforeach;?>
                
               

              </tbody>
            </table>
            </div>

      </div>
<script>
$(document).ready(function(){ 


$('.dial').each(function () { 

          var elm = $(this);
          var color = elm.attr("data-fgColor");  
          var perc = elm.attr("value");  
 
          elm.knob({ 
               'value': 0, 
                'min':0,
                'max':100,
                "skin":"tron",
                "readOnly":true,
                "thickness":.25,                 
                'dynamicDraw': true,                
                "displayInput":false
          });

          $({value: 0}).animate({ value: perc }, {
              duration: 1000,
              easing: 'swing',
              progress: function () {                  elm.val(Math.ceil(this.value)).trigger('change')
              }
          });

          //circular progress bar color
          $(this).append(function() {
              elm.parent().parent().find('.circular-bar-content').css('color',color);
              elm.parent().parent().find('.circular-bar-content label').text(perc+'%');
          });

          });
   
 


                       
});
</script>
  