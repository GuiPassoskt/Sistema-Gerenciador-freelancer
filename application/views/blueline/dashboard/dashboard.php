<?php 
if(isset($update)){
if($this->user->admin == "1" && $update){
 ?>
<div class="newsbox"><a href="<?=base_url()?>settings/updates"><?=$this->lang->line('application_update_available');?> <?=$update?> <i class="fa fa-download"></i> </a></div>
<?php } }?>
<?php if($this->user->admin == "1"){ ?> 
<div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4">
              <div class="stdpad-small red">
                    <div class="icon"><i class="ion-ios-lightbulb-outline"></i></div>
                    <div class="stats"> 
                    <div class="number"><?=$projects_open;?><small> / <?=$projects_all;?></small></div> <div class="text"><?=$this->lang->line('application_open_projects');?></div>
                    
                    </div>
             </div>

            </div>
        
            <div class="col-xs-12 col-sm-12 col-md-4">
             
              <div class="stdpad-small orange">
                  <div class="icon"><i class="ion-ios-paper-outline"></i></div>
                    <div class="stats"> 
                     <div class="number"><?=$invoices_open;?><small> / <?=$invoices_all;?></small></div> <div class="text"><?=$this->lang->line('application_open_invoices');?></div>
                    
                    </div>
                  
                </div>
                            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-4">
             
                <div class="stdpad-small blue">
                    
                    <div class="icon"><i class="ion-ios-analytics-outline"></i></div>
                    <div class="stats two"> 
                    <div class="number"><?php if(empty($payments[0]->summary)){echo display_money(0, $core_settings->currency, 0);}else{echo display_money($payments[0]->summary, $core_settings->currency, 0); }?></div> <div class="text"><?=$this->lang->line('application_'.$month);?> <?=$this->lang->line('application_payments');?></div> 
                    <div class="number"><?php if(empty($paymentsoutstanding[0]->summary)){echo display_money(0, $core_settings->currency, 0);}else{echo display_money($paymentsoutstanding[0]->summary, $core_settings->currency, 0); } ?></div> <div class="text"><?=$this->lang->line('application_outstanding_payments');?></div>
                    
                    </div>
                       </div>
                        </div>
            
            
</div>
<?php } ?>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4">
              <div class="stdpad"><div class="table-head"><?=$this->lang->line('application_events');?><small> (<?=$eventcount;?>)</small></div>
                    <ul class="eventlist">
                            <?php $count = 0;
                            foreach ($events as $value):  $count = $count+1; ?>            
                                    <li>
                                       <p class="truncate"><?=$value;?></p>  
                                    </li>
                            <?php endforeach;?>
                            <?php if($count == 0) { ?>
                                    <li> <p class="truncate"><?=$this->lang->line('application_no_events_yet');?></p></li>
                            <?php } ?>
                    </ul>
             </div>

            </div>
        
            <div class="col-xs-12 col-sm-12 col-md-4">
            <?php if(isset($tasks)){ ?> 
              <div class="stdpad">
                  <div class="table-head"><?=$this->lang->line('application_my_open_tasks');?></div>
                  <div id="main-nano-wrapper" class="nano">
    <div class="nano-content"><ul id="jp-container" class="todo jp-container">
                         <?php $count = 0;
                                foreach ($tasks as $value):  $count = $count+1; ?>
                                    <li class="<?=$value->status;?>">
                                      <span class="lbl-"> 
                                        <p class="truncate"><input name="form-field-checkbox" type="checkbox" class="checkbox-nolabel task-check" data-link="<?=base_url()?>projects/tasks/<?=$value->project_id;?>/check/<?=$value->id;?>" <?=$value->status;?>/>
                                   <a href="<?=base_url()?>projects/view/<?=$value->project_id;?>"><?=$value->name;?></a></p></span> 
                                             <span class="pull-right"><img class="img-circle list-profile-img" width="21px" height="21px" src="<?php 
                                                if($this->user->userpic != 'no-pic.png'){
                                                  echo base_url()."files/media/".$this->user->userpic;
                                                }else{
                                                  echo get_gravatar($this->user->email);
                                                }
                                                 ?>">
                                             </span>
                                         
                                    </li>
                                <?php endforeach;?>
                                
                                <?php if($count == 0) { ?>
                                    <li class="notask"><?=$this->lang->line('application_no_tasks_yet');?></li>
                                    
                                <?php } ?>

                  </ul></div></div>
                </div>
                <?php } ?>
            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-4">
            <?php if(isset($message)){ ?> 
                <div class="stdpad">
                    <div class="table-head"><?=$this->lang->line('application_recent_messages');?></div>

                        <ul class="dash-messages">
                            <?php foreach ($message as $value):?>          
                                <li style="display: list-item;">
                                    <a href="<?=base_url()?>messages">
                                      <img class="userpic img-circle" src="
                                        <?php 
                                          if($value->userpic_u){
                                            if($value->userpic_u != 'no-pic.png'){
                                              echo base_url()."files/media/".$value->userpic_u;
                                            }else{
                                              echo get_gravatar($value->email_u);
                                            }
                                            
                                          }else{
                                            if($value->userpic_c != 'no-pic.png'){
                                              echo base_url()."files/media/".$value->userpic_c;
                                            }else{
                                              echo get_gravatar($value->email_c);
                                            }
                                          }
                                          ?>
                                        ">
                                    <h5><?php if(isset($value->sender_u)){echo $value->sender_u;}else{ echo $value->sender_c; } ?> <small><?php echo time_ago($value->time); ?></small></h5>
                                    <p class="truncate" style="width:80%"><span> <?php if($value->status == "New"){ echo '<span class="new"><i class="fa fa-circle-o"></i></span>';}?> <?=$value->subject;?></span></p>
                                    </a>
                                </li>
                            <?php endforeach;?>
                            <?php if(empty($message)) { ?>
                                <li style="padding: 10px 0 0 0; height: 24px;"><?=$this->lang->line('application_no_messages');?></li>
                            <?php } ?>
                        </ul><br/>
                       </div>
            <?php } ?>
            </div>
            
            
        </div>
<?php if($this->user->admin == "1"){ ?>        
    <div class="row">
          <div class="col-xs-12 col-sm-12 ">

          <div class="dashboard-chart">
            <div class="table-head"><?=$this->lang->line('application_statistics');?> 
            
            <div class="btn-group pull-right">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <?=$year;?> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="<?=base_url()?>dashboard/filter/<?=date("Y");?>"><?=date("Y");?></a></li>

                    <li><a href="<?=base_url()?>dashboard/filter/<?=date("Y")-1;?>"><?=date("Y")-1;?></a></li>
                    <li><a href="<?=base_url()?>dashboard/filter/<?=date("Y")-2;?>"><?=date("Y")-2;?></a></li>
                    <li><a href="<?=base_url()?>dashboard/filter/<?=date("Y")-3;?>"><?=date("Y")-3;?></a></li>
                    <li><a href="<?=base_url()?>dashboard/filter/<?=date("Y")-4;?>"><?=date("Y")-4;?></a></li>
                    <li><a href="<?=base_url()?>dashboard/filter/<?=date("Y")-5;?>"><?=date("Y")-5;?></a></li>
                  </ul>
            </div>
            </div>
            
            <div class="padding-30" style="width:94%;">
      <canvas id="tileChart" class="hidden-xs" width="auto" height="50"></canvas>
      </div>
          </div>
          </div>
    </div>

<?php } ?>    
         
 

      <?php 
      $line1 = '';
      $labels = '';
      for ($i = 01; $i <= 12; $i++) {

        $num = "0";
        foreach ($stats as $value):
        $act_month = explode("-", $value->paid_date); 
        if($act_month[1] == $i){  
          $num = sprintf("%02.2d", $value->summary); 
        }
        endforeach; 
          $i = sprintf("%02.2d", $i);
          $labels .= '"'.$year.'-'.$i.'"';
          $line1 .= $num;
          if($i != "12"){ $line1 .= ","; $labels .= ",";}
        } 
        
       
        ?>



  <script type="text/javascript">
    $(document).ready(function(){


//chartjs

var ctx = $("#tileChart").get(0).getContext("2d");

<?php
                                $days = array(); 
                                $data = "";
                                
                               ?>

var data = {
    labels: [<?=$labels?>],
    datasets: [
        {
            label: "First dataset",
            fillColor: "rgba(50, 211, 218,0.2)",
            strokeColor: " #33C3DA",
            pointColor: "#33D2DA",
            pointStrokeColor: "rgba(50, 211, 218,1)",
            pointHighlightFill: "rgba(50, 211, 218,0.9)",
            pointHighlightStroke: "rgba(50, 211, 218,1)",
            data: [<?=$line1?>]
        }
        
    ]
};

var options = {

    scaleShowVerticalLines: false,

    tooltipTemplate: '<?=$this->lang->line("application_received");?>: <%=value%> '

};
 var tileChart = new Chart(ctx).Line(data, options);




function tick(){
  $('ul.dash-messages li:first').slideUp('slow', function () { $(this).appendTo($('ul.dash-messages')).fadeIn('slow'); });
}
<?php if(count($message) > 2){ ?>
setInterval(function(){ tick() }, 5000);
<?php } ?>
$('ul.eventlist li').click(function(){
  $('ul.eventlist li:first').slideUp('slow', function () { $(this).appendTo($('ul.eventlist')).fadeIn('slow'); });
});



    });
    </script>




 