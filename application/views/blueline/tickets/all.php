	<div class="col-sm-13  col-md-12 main">  
    
    <div class="row tile-row">
      <div class="col-md-3 col-xs-6 tile"><div class="icon-frame"><i class="ion-ios-pricetags"></i> </div><h1><?php if(isset($tickets_assigned_to_me)){echo $tickets_assigned_to_me;} ?> <span class="hidden-xs"><?=$this->lang->line('application_tickets');?></span></h1><h2 class="hidden-xs"><?=$this->lang->line('application_assigned_to_me');?></h2></div>
      <div class="col-md-3 col-xs-6 tile"><div class="icon-frame secondary"><i class="ion-ios-albums"></i> </div><h1><?php if(isset($tickets_in_my_queue)){echo $tickets_in_my_queue;} ?> <span class="hidden-xs"><?=$this->lang->line('application_tickets');?></span></h1><h2 class="hidden-xs"><?=$this->lang->line('application_in_my_queue');?></h2></div>
      <div class="col-md-6 col-xs-12 tile">
      <div style="width:97%; height: 93px;">
      <canvas id="tileChart" class="hidden-xs" width="auto" height="50"></canvas>
      </div>
      </div>
    
    </div>  
    <div class="row"> 
			<a href="<?=base_url()?>tickets/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_new_ticket');?></a>
			<div class="btn-group pull-right-responsive margin-right-3">
	          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
	            <?=$this->lang->line('application_queue');?> <span class="caret"></span>
	          </button>
			<ul class="dropdown-menu pull-right" role="menu">
				<?php foreach ($queues as $value):?>
	                <li><a id="" href="<?=base_url()?>tickets/queues/<?=$value->id?>"><?=$value->name?></a></li>
	            <?php endforeach;?>
	            
			</ul>
			</div>
			<div class="btn-group pull-right-responsive margin-right-3">
	          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
	            <?php $last_uri = $this->uri->segment($this->uri->total_segments()); if($last_uri != "tickets"){echo $this->lang->line('application_'.$last_uri);}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
	          </button>
			<ul class="dropdown-menu pull-right" role="menu">
				<?php foreach ($submenu as $name=>$value):?>
	                <li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
	            <?php endforeach;?>
	            
			</ul>
			</div>
			<div class="btn-group pull-right-responsive margin-right-3 hidden-xs">
	          <button id="bulk-button" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
	             <?=$this->lang->line('application_bulk_actions');?> <span class="caret"></span>
	          </button>
			<ul class="dropdown-menu pull-right bulk-dropdown" role="menu">
				
	                <li data-action="close"><a id="" href="#"><?=$this->lang->line('application_close');?></a></li>

	            
			</ul>
			<form action="<?=base_url()?>tickets/bulk/" method="POST" id="bulk-form">
			  <input type="hidden" name="list" id="list-data"/>
			</form>
			</div>
		</div>
	<div class="row">
		<div class="table-head"><?=$this->lang->line('application_tickets');?></div>
		<div class="table-div">
		<table class="data-sorting table" id="tickets" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th class="hidden-xs no_sort simplecheckbox" style="width:16px"><input class="checkbox-nolabel" type="checkbox" id="checkAll" name="selectall" value=""></th>
			<th class="hidden-xs" style="width:70px"><?=$this->lang->line('application_ticket_id');?></th>
			<th style="width:50px"><?=$this->lang->line('application_status');?></th>
			<th class="hidden-xs no_sort" style="width:5px; padding-right: 5px;"><i class="fa fa-star-o"></i></th>
			<th><?=$this->lang->line('application_subject');?></th>
			
			<th class="hidden-xs"><?=$this->lang->line('application_queue');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_client');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_owner');?></th>
		</thead>
		<?php foreach ($ticket as $value):?>
			<?php $lable = FALSE; if($value->status == "new"){ $lable = "label-important"; }elseif($value->status == "open"){$lable = "label-warning";}elseif($value->status == "closed" || $value->status == "inprogress"){$lable = "label-success";}elseif($value->status == "reopened"){$lable = "label-warning";} ?>
		<tr id="<?=$value->id;?>" >
			<td class="hidden-xs noclick simplecheckbox" style="width:16px"> <input class="checkbox-nolabel bulk-box" type="checkbox" name="bulk[]" value="<?=$value->id?>"></td>
			<td  class="hidden-xs" style="width:70px"><?=$value->reference;?></td>
			<td style="width:50px"><span class="label <?php echo $lable; ?>"><?=$this->lang->line('application_ticket_status_'.$value->status);?></span></td>
			<?php if(isset($value->user->id)){$user_id = $value->user->id; }else{ $user_id = FALSE; }?>
			<td  class="hidden-xs" style="width:15px"><?php if($value->updated == "1" && $user_id == $this->user->id){?><i class="fa fa-star"></i><?php }else{?> <i class="fa fa-star-o" style="opacity: 0.2;"></i><?php } ?></td>
			<td><?=$value->subject;?></td>
			<td class="hidden-xs"><span><?php if(isset($value->queue->name)){ echo $value->queue->name;}?></span></td>
			<td class="hidden-xs"><?php if(!isset($value->company->name)){echo '<span class="label">'.$this->lang->line('application_no_client_assigned').'</span>'; }else{ echo '<span class="label label-info">'.$value->company->name.'</span>'; }?></td>
			<td class="hidden-xs"><?php if(!isset($value->user->firstname)){echo '<span class="label">'.$this->lang->line('application_not_assigned').'</span>'; }else{ echo '<span class="label label-info">'.$value->user->firstname.' '.$value->user->lastname.'</span>'; }?></td>

		</tr>

		<?php endforeach;?>
	 	</table>
	 	
	 	</div>
	</div>
	</div>

<script>
$(document).ready(function(){ 


//chartjs

var ctx = $("#tileChart").get(0).getContext("2d");

<?php
                                $days = array(); 
                                $data = "";
                                $this_week_days = array(
                                  date("Y-m-d",strtotime('monday this week')),
                                  date("Y-m-d",strtotime('tuesday this week')),
                                    date("Y-m-d",strtotime('wednesday this week')),
                                      date("Y-m-d",strtotime('thursday this week')),
                                        date("Y-m-d",strtotime('friday this week')),
                                          date("Y-m-d",strtotime('saturday this week')),
                                            date("Y-m-d",strtotime('sunday this week')));

                                $labels = '"'.date("m-d",strtotime('monday this week')).'","'.
                                  date("m-d",strtotime('tuesday this week')).'","'.
                                    date("m-d",strtotime('wednesday this week')).'","'.
                                      date("m-d",strtotime('thursday this week')).'","'.
                                        date("m-d",strtotime('friday this week')).'","'.
                                          date("m-d",strtotime('saturday this week')).'","'.
                                            date("m-d",strtotime('sunday this week')).'"';
                                foreach ($tickets_opened_this_week as $value) {
                                  $days[$value->date_formatted] = $value->amount;
                                }
                                foreach ($this_week_days as $selected_day) {
                                  $y = 0;
                                    if(isset($days[$selected_day])){ $y = $days[$selected_day];}
                                      $data .= $y.",";
                                      $selday = $selected_day;
                                     } ?>

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
            data: [<?=$data;?>]
        }
        
    ]
};

var options = {

    scaleShowVerticalLines: false,

    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",

    tooltipTemplate: " <%= value %> new tickets"

};
 var tileChart = new Chart(ctx).Line(data, options);

});
</script>
	