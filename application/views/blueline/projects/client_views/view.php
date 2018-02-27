<div class="row">
              <div class="col-xs-12 col-sm-12">

  <div class="row tile-row tile-view">
      <div class="col-md-1 col-xs-3">
      <div class="percentage easyPieChart" data-percent="<?=$project->progress;?>"><span><?=$project->progress;?>%</span></div>
        
      </div>
      <div class="col-md-11 col-xs-9 smallscreen"> 
        <h1><span class="nobold">#<?=$project->reference;?></span> - <?=$project->name;?></h1>
         <p class="truncate description"><?=$project->description;?></p>
      </div>
    
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active hidden-xs"><a href="#projectdetails-tab" aria-controls="projectdetails-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_project_details');?></a></li>
        <li role="presentation" class="hidden-xs"><a href="#tasks-tab" aria-controls="tasks-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_tasks');?></a></li>
        <li role="presentation" class="hidden-xs"><a href="#media-tab" aria-controls="media-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_media');?></a></li>
        <li role="presentation" class="hidden-xs"><a href="#notes-tab" aria-controls="notes-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_notes');?></a></li>
       <?php if($invoice_access) { ?>
        <li role="presentation" class="hidden-xs"><a href="#invoices-tab" aria-controls="invoices-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_invoices');?></a></li>
       <?php } ?>
        <li role="presentation" class="hidden-xs"><a href="#activities-tab" aria-controls="activities-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_activities');?></a></li>
        
        <li role="presentation" class="dropdown visible-xs">
            <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents" aria-expanded="false"><?=$this->lang->line('application_overview');?> <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
              <li role="presentation" class="active"><a href="#projectdetails-tab" aria-controls="projectdetails-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_project_details');?></a></li>
              <li role="presentation"><a href="#tasks-tab" aria-controls="tasks-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_tasks');?></a></li>
              <li role="presentation"><a href="#media-tab" aria-controls="media-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_media');?></a></li>
              <li role="presentation"><a href="#notes-tab" aria-controls="notes-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_notes');?></a></li>
             <?php if($invoice_access) { ?>
              <li role="presentation"><a href="#invoices-tab" aria-controls="invoices-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_invoices');?></a></li>
             <?php } ?>
              <li role="presentation"><a href="#activities-tab" aria-controls="activities-tab" role="tab" data-toggle="tab"><?=$this->lang->line('application_activities');?></a></li>
            </ul>
        </li>

        
        

        
      </ul>


    </div> 


              </div>
          </div>
   <div class="tab-content"> 

<div class="row tab-pane fade in active" role="tabpanel" id="projectdetails-tab">

              <div class="col-xs-12 col-sm-9">
            <div class="table-head"><?=$this->lang->line('application_project_details');?></div>

                <div class="subcont">
                  <ul class="details col-xs-12 col-sm-12 col-md-6">
                    <li><span><?=$this->lang->line('application_project_id');?>:</span> <?=$project->reference;?></li>
                    <li><span><?=$this->lang->line('application_category');?>:</span> <?=$project->category;?></li>
                    <li><span><?=$this->lang->line('application_client');?>:</span> <?php if(!isset($project->company->name)){ ?> <a href="#" class="label label-default"><?php echo $this->lang->line('application_no_client_assigned'); }else{ ?><a class="label label-success" href="#"><?php echo $project->company->name;} ?></a></li>
                    <li><span><?=$this->lang->line('application_assigned_to');?>:</span> <?php foreach ($project->project_has_workers as $workers):?> <a class="label label-info" style="padding: 2px 5px 3px;"><?php echo $workers->user->firstname." ".$workers->user->lastname;?></a><?php endforeach;?> </li>
        
                  </ul>
                  <ul class="details col-xs-12 col-sm-12 col-md-6"><span class="visible-xs divider"></span>
                    <li><span><?=$this->lang->line('application_start_date');?>:</span> <?php  $unix = human_to_unix($project->start.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
                    <li><span><?=$this->lang->line('application_deadline');?>:</span> <?php  $unix = human_to_unix($project->end.' 00:00'); echo date($core_settings->date_format, $unix);?></li>
                    <li><span><?=$this->lang->line('application_time_spent');?>:</span> <?=$time_spent;?> </li>
                    <li><span><?=$this->lang->line('application_created_on');?>:</span> <?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $project->datetime); ?></li>
                  </ul>
                  <br clear="both">
                </div>


               </div>


               <div class="col-xs-12 col-sm-3">
            <div class="table-head"><?=$this->lang->line('application_activities');?></div>
            <div class="subcont" > 

<ul id="comments-ul-short" class="comments">
<?php $i = 0; foreach ($project->project_has_activities as $value):?>
                      <?php 
                      $writer = FALSE;
                      
                      if ($value->user_id != 0) { 
                          $writer = $value->user->firstname." ".$value->user->lastname;
                          $image = get_user_pic($value->user->userpic, $value->user->email);
                          }else{
                          $writer = $value->client->firstname." ".$value->client->lastname;
                          $image = get_user_pic($value->client->userpic, $value->client->email);
                                
                      }?>
                      <li class="comment-item">
                      <div class="comment-pic">
                        <?php if ($writer != FALSE) {  ?>
                        <img class="img-circle tt" title="<?=$writer?>"  src="<?=$image?>">
                        <?php }else{?> <i class="fa fa-rocket"></i> <?php } ?>
                      </div>
                      <div class="comment-content">
                          <h5><?=$value->subject;?></h5>
                            <p><small class="text-muted"><span class="datetime"> <i class="glyphicon glyphicon-time"></i> <?php  echo time_ago($value->datetime, true);/*date($core_settings->date_format.' '.$core_settings->date_time_format, $value->datetime);*/ ?></span></small></p>
                            <p><?=character_limiter(strip_tags($value->message), 25);?></p>
                      </div>
                      </li>
  <?php $i = $i+1; 
  if($i == 5) break;
  endforeach;

  if($i == 0){ ?>
  <li class="comment-item">
                      <div class="comment-pic">
                        
                      </div>
                      <div class="comment-content">
                          
                          <p><?=$this->lang->line('application_no_data_yet');?></p>
                      </div>
                      </li>
  <?php } ?>
         </ul>            



</div>
</div>



            </div>


  <div class="row tab-pane fade" role="tabpanel" id="tasks-tab">
     <div class="col-xs-12 col-sm-12">
            <div class="table-head"><?=$this->lang->line('application_tasks');?></div>
  

                <div class="subcont no-padding min-height-410">
                  <ul id="task-list" class="todo sortlist">
                      <?php 
        $count = 0;
        foreach ($project->project_has_tasks as $value):  $count = $count+1; if($value->public != 0){ ?>

            <li class="<?=$value->status;?> priority<?=$value->priority;?>"><a href="#" class=""></a>
              
              <input name="form-field-checkbox" class="checkbox-nolabel task-check" disabled="disabled" type="checkbox" <?php if($value->status == "done"){echo "checked";}?>/>
              <span class="lbl"> <p class="truncate name"><?=$value->name;?></p></span>
              <span class="pull-right">
                                  <?php if ($value->user_id != 0) {  ?><img class="img-circle list-profile-img tt"  title="<?=$value->user->firstname;?> <?=$value->user->lastname;?>"  src="<?php 
                if($value->user->userpic != 'no-pic.png'){
                  echo base_url()."files/media/".$value->user->userpic;
                }else{
                  echo get_gravatar($value->user->email);
                }
                 ?>"><?php } ?>
                                   
                                  </span>
                    <div class="todo-details">
                    <div class="row">
                        <div class="col-sm-3">
                        <ul class="details">
                            <li><span><?=$this->lang->line('application_priority');?>:</span> <?php switch($value->priority){case "0": echo $this->lang->line('application_no_priority'); break; case "1": echo $this->lang->line('application_low_priority'); break; case "2": echo $this->lang->line('application_med_priority'); break; case "3": echo $this->lang->line('application_high_priority'); break;};?></li>
                            <?php if($value->value != 0){ ?><li><span><?=$this->lang->line('application_value');?>:</span> <?=$value->value;?></li><?php } ?>
                            <?php if($value->due_date != ""){ ?><li><span><?=$this->lang->line('application_due_date');?>:</span> <?php  $unix = human_to_unix($value->due_date.' 00:00'); echo date($core_settings->date_format, $unix);?></li><?php } ?>
                            <li><span><?=$this->lang->line('application_assigned_to');?>:</span> <?php if(isset($value->user->lastname)){ echo $value->user->firstname." ".$value->user->lastname;}else{$this->lang->line('application_not_assigned');}?> </li>

                         </ul>
                        
                        </div>
                        <div class="col-sm-9"><h3><?=$this->lang->line('application_description');?></h3> <p><?=$value->description;?></p></div>
                        
                    </div>
                    </div>
              
          </li>
         <?php } endforeach;?>
         <?php if($count == 0) { ?>
          <li class="notask"><?=$this->lang->line('application_no_tasks_yet');?></li>
         <?php } ?>

                       
         
                         </ul>
                </div>
               </div>
</div>
<div class="row tab-pane fade" role="tabpanel" id="media-tab">
<div class="col-xs-12 col-sm-12">
 <div class="table-head"><?=$this->lang->line('application_media');?> <span class=" pull-right"><a href="<?=base_url()?>cprojects/media/<?=$project->id;?>/add" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_add_media');?></a></span></div>
<div class="table-div min-height-410">
 <table id="media" class="table data-media" rel="<?=base_url()?>cprojects/media/<?=$project->id;?>" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
                    <th  class="hidden"></th>
          <th><?=$this->lang->line('application_name');?></th>
          <th class="hidden-xs"><?=$this->lang->line('application_filename');?></th>
          <th class="hidden-xs"><?=$this->lang->line('application_phase');?></th>
          <th class="hidden-xs"><i class="fa fa-download"></i></th>
          </tr></thead>
        
        <tbody>
        <?php foreach ($project->project_has_files as $value):?>

        <tr id="<?=$value->id;?>">
          <td class="hidden"><?=human_to_unix($value->date);?></td>
          <td onclick=""><?=$value->name;?></td>
          <td class="hidden-xs truncate" style="max-width: 80px;"><?=$value->filename;?></td>
          <td class="hidden-xs"><?=$value->phase;?></td>
          <td class="hidden-xs"><span class="label label-info tt" title="<?=$this->lang->line('application_download_counter');?>" ><?=$value->download_counter;?></span></td>
          
        </tr>

        <?php endforeach;?>
        
        
        
        </tbody></table>
        <?php if(!$project->project_has_files) { ?>
        <div class="no-files">  
            <i class="fa fa-cloud-upload"></i><br>
            No files have been uploaded yet!
        </div>
         <?php } ?>
        </div>
</div>
</div>
<div class="row tab-pane fade" role="tabpanel" id="notes-tab">
<div class="col-xs-12 col-sm-12">
<?php $attributes = array('class' => 'note-form', 'id' => '_notes');
    echo form_open(base_url()."projects/notes/".$project->id, $attributes); ?>
 <div class="table-head"><?=$this->lang->line('application_notes');?> <span class=" pull-right"><a id="send" name="send" class="btn btn-primary button-loader"><?=$this->lang->line('application_save');?></a></span><span id="changed" class="pull-right label label-warning"><?=$this->lang->line('application_unsaved');?></span></div>

  <textarea class="input-block-level summernote-note" name="note" id="textfield" ><?=$project->note;?></textarea>
</form>
</div>

</div>

<?php if($invoice_access) { ?>
<div class="row tab-pane fade" role="tabpanel" id="invoices-tab">
 <div class="col-xs-12 col-sm-12">
 <div class="table-head"><?=$this->lang->line('application_invoices');?> <span class=" pull-right"></span></div>
<div class="table-div">
 <table class="data table" id="invoices" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
    <thead>
      <th width="70px" class="hidden-xs"><?=$this->lang->line('application_invoice_id');?></th>
      <th><?=$this->lang->line('application_client');?></th>
      <th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
      <th class="hidden-xs"><?=$this->lang->line('application_due_date');?></th>
      <th><?=$this->lang->line('application_status');?></th>
    </thead>
    <?php foreach ($project_has_invoices as $value):?>

    <tr id="<?=$value->id;?>" >
      <td class="hidden-xs" onclick=""><?=$value->reference;?></td>
      <td onclick=""><span class="label label-info"><?php if(isset($value->company->name)){echo $value->company->name; }?></span></td>
      <td class="hidden-xs"><span><?php $unix = human_to_unix($value->issue_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span></td>
      <td class="hidden-xs"><span class="label <?php if($value->status == "Paid"){echo 'label-success';} if($value->due_date <= date('Y-m-d') && $value->status != "Paid"){ echo 'label-important tt" title="'.$this->lang->line('application_overdue'); } ?>"><?php $unix = human_to_unix($value->due_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span> <span class="hidden"><?=$unix;?></span></td>
      <td onclick=""><span class="label <?php $unix = human_to_unix($value->sent_date.' 00:00'); if($value->status == "Paid"){echo 'label-success';}elseif($value->status == "Sent"){ echo 'label-warning tt" title="'.date($core_settings->date_format, $unix);} ?>"><?=$this->lang->line('application_'.$value->status);?></span></td>
    </tr>

    <?php endforeach;?>
    </table>
        <?php if(!$project_has_invoices) { ?>
        <div class="no-files">  
            <i class="fa fa-file-text"></i><br>
            
            <?=$this->lang->line('application_no_invoices_yet');?>
        </div>
         <?php } ?>
        </div>
  </div>             


</div>
<?php } ?>



<div class="row tab-pane fade" role="tabpanel" id="activities-tab">
<div class="col-xs-12 col-sm-12">
            <div class="table-head"><?=$this->lang->line('application_activities');?>
            <span class=" pull-right"><a class="btn btn-primary open-comment-box"><?=$this->lang->line('application_new_comment');?></a></span>
            </div>
            <div class="subcont" > 

<ul id="comments-ul" class="comments">
                      <li class="comment-item add-comment">
                      <?php   
                                $attributes = array('class' => 'ajaxform', 'id' => 'replyform', 'data-reload' => 'comments-ul');
                                echo form_open('cprojects/activity/'.$project->id.'/add', $attributes); 
                                ?>
                      <div class="comment-pic">
                        <img class="img-circle tt" title="<?=$this->client->firstname?> <?=$this->client->lastname?>"  src="<?=get_user_pic($this->client->userpic, $this->client->email);?>">
                      
                      </div>
                      <div class="comment-content">
                          <h5><input type="text" name="subject" class="form-control" id="subject" placeholder="<?=$this->lang->line('application_subject');?>..." required/></h5>
                            <p><small class="text-muted"><span class="comment-writer"><?=$this->client->firstname?> <?=$this->client->lastname?></span> <span class="datetime"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, time()); ?></span></small></p>
                            <p><textarea class="input-block-level summernote" id="reply" name="message" placeholder="<?=$this->lang->line('application_write_message');?>..." required/></textarea></p>
                            <button id="send" name="send" class="btn btn-primary button-loader"><?=$this->lang->line('application_send');?></button>
                            <button id="cancel" name="cancel" class="btn btn-danger open-comment-box"><?=$this->lang->line('application_close');?></button>
                               
                      </div>
                       </form>
                      </li>
<?php foreach ($project->project_has_activities as $value):?>
                      <?php 
                      $writer = FALSE;
                      if ($value->user_id != 0) { 
                          $writer = $value->user->firstname." ".$value->user->lastname;
                          $image = get_user_pic($value->user->userpic, $value->user->email);
                          }else{
                          $writer = $value->client->firstname." ".$value->client->lastname;
                          $image = get_user_pic($value->client->userpic, $value->client->email);
                                
                      }?>
                      <li class="comment-item">
                      <div class="comment-pic">
                        <?php if ($writer != FALSE) {  ?>
                        <img class="img-circle tt" title="<?=$writer?>"  src="<?=$image?>">
                        <?php }else{?> <i class="fa fa-rocket"></i> <?php } ?>
                      </div>
                      <div class="comment-content">
                          <h5><?=$value->subject;?></h5>
                            <p><small class="text-muted"><span class="comment-writer"><?=$writer?></span> <span class="datetime"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $value->datetime); ?></span></small></p>
                            <p><?=$value->message;?></p>
                      </div>
                      </li>
  <?php endforeach;?>
                      <li class="comment-item">
                        <div class="comment-pic"><i class="fa fa-bolt"></i></div>
                          <div class="comment-content">
                          <h5><?=$this->lang->line('application_project_created');?></h5>
                            <p><small class="text-muted"><?php  echo date($core_settings->date_format.' '.$core_settings->date_time_format, $project->datetime); ?></small></p>
                            <p><?=$this->lang->line('application_project_has_been_created');?></p>
                          </div>
                      </li>  
         </ul>            




</div>
</div>
</div>
<style type="text/css">

.circular-bar{
  text-align: center;

  margin:10px 20px;
}
  .circular-bar-content{
    margin-bottom: 70px;
    margin-top: -100px;
    text-align: center;
  }
    .circular-bar-content strong{
      display: block;
      font-weight: 400;
      @include font-size(18,24);
    }
    .circular-bar-content label, .circular-bar-content span{
      display: block;
      font-weight: 400;
      font-size: 18px;
      color: #505458;
      @include font-size(15,20);
    }


</style>
 <script type="text/javascript">  
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
                "thickness":.13,                 
                'dynamicDraw': true,                
                "displayInput":false,

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
  
  

