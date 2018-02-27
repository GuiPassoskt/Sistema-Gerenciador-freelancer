	<div class="col-sm-12  col-md-12 main">  
    <div class="row tile-row">
      <div class="col-md-3 col-xs-6 tile"><div class="icon-frame"><i class="ion-ios-bell"></i> </div><h1> <?php if(isset($invoices_due_this_month)){echo $invoices_due_this_month;} ?> <span class="hidden-xs"><?=$this->lang->line('application_invoices');?></span></h1><h2 class="hidden-xs"><?=$this->lang->line('application_due_this_month');?></h2></div>
      <div class="col-md-3 col-xs-6 tile"><div class="icon-frame secondary"><i class="ion-ios-analytics"></i> </div><h1> <?php if(isset($invoices_paid_this_month)){echo $invoices_paid_this_month;} ?> <span class="hidden-xs"><?=$this->lang->line('application_invoices');?></span></h1><h2 class="hidden-xs"><?=$this->lang->line('application_paid_this_month');?></h2></div>
      <div class="col-md-6 col-xs-12 tile">
      <div style="width:97%; height: 93px;">
      <canvas id="tileChart" class="hidden-xs" width="auto" height="50"></canvas>
      </div>
      </div>
    
    </div>   
     <div class="row">
      <a href="<?=base_url()?>invoices/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_invoice');?></a>
      <div class="btn-group pull-right-responsive margin-right-3">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php $last_uri = $this->uri->segment($this->uri->total_segments()); if($last_uri != "invoices"){echo $this->lang->line('application_'.$last_uri);}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right" role="menu">
            <?php foreach ($submenu as $name=>$value):?>
	                <li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
	            <?php endforeach;?>
          </ul>
      </div>
    </div>  
      <div class="row">

         <div class="table-head"><?=$this->lang->line('application_invoices');?></div>
         <div class="table-div">
		<table class="data table" id="invoices" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th width="70px" class="hidden-xs"><?=$this->lang->line('application_invoice_id');?></th>
			<th ><?=$this->lang->line('application_client');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_due_date');?></th>
      <th class="hidden-xs"><?=$this->lang->line('application_value');?></th>
			<th><?=$this->lang->line('application_status');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>
		<?php foreach ($invoices as $value):?>

		<tr id="<?=$value->id;?>" >
			<td class="hidden-xs"><?=$value->reference;?></td>
			<td><span class="label label-info"><?php if(isset($value->company->name)){echo $value->company->name; }?></span></td>
			<td class="hidden-xs"><span><?php $unix = human_to_unix($value->issue_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span></td>
			<td class="hidden-xs"><span class="label <?php if($value->status == "Paid"){echo 'label-success';} if($value->due_date <= date('Y-m-d') && $value->status != "Paid"){ echo 'label-important tt" title="'.$this->lang->line('application_overdue'); } ?>"><?php $unix = human_to_unix($value->due_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span> <span class="hidden"><?=$unix;?></span></td>
			<td class="hidden-xs"><?php if(isset($value->sum)){echo display_money($value->sum, $value->currency);} ?> </td>
      <td><span class="label <?php $unix = human_to_unix($value->sent_date.' 00:00'); if($value->status == "Paid"){echo 'label-success';}elseif($value->status == "Sent"){ echo 'label-warning tt" title="'.date($core_settings->date_format, $unix);} ?>"><?=$this->lang->line('application_'.$value->status);?></span></td>
		
			<td class="option" width="8%">
				        <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>invoices/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-times"></i></button>
				        <a href="<?=base_url()?>invoices/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-cog"></i></a>
			</td>
		</tr>

		<?php endforeach;?>
	 	</table>
            </div>

      </div>

<script>
$(document).ready(function(){ 


//chartjs

var ctx = $("#tileChart").get(0).getContext("2d");

<?php
                                $days = array(); 
                                $data = "";
                                $data2 = "";
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
                                
                                //First Dataset            
                                foreach ($invoices_paid_this_month_graph as $value) {
                                  $days[$value->date_formatted] = $value->amount;
                                }
                                foreach ($this_week_days as $selected_day) {
                                  $y = 0;
                                    if(isset($days[$selected_day])){ $y = $days[$selected_day];}
                                      $data .= $y.",";
                                      $selday = $selected_day;
                                     } 

                                //Second Dataset
                                foreach ($invoices_due_this_month_graph as $value) {
                                  $days[$value->date_formatted] = $value->amount;
                                }
                                foreach ($this_week_days as $selected_day2) {
                                  $y = 0;
                                    if(isset($days[$selected_day2])){ $y = $days[$selected_day2];}
                                      $data2 .= $y.",";
                                      $selday2 = $selected_day2;
                                     }


                                     ?>

var data = {
    labels: [<?=$labels?>],
    datasets: [
        {
            label: "First dataset",
            fillColor: "rgba(50, 211, 218,0.2)",
            strokeColor: "#33C3DA",
            pointColor: "#33D2DA",
            pointStrokeColor: "#fff",
            pointHighlightFill: "rgba(50, 211, 218,0.6)",
            pointHighlightStroke: "rgba(50, 211, 218,1)",
            data: [<?=$data;?>]
        },
        {
            label: "Second dataset",
            fillColor: "rgba(237,85,100,0.2)",
            strokeColor: "rgba(237,85,100,1)",
            pointColor: "rgba(237,85,100,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "rgba(237,85,100,1)",
            pointHighlightStroke: "rgba(237,85,100,0.9)",
            data: [<?=$data2;?>]
        }
        
    ]
};
legentvar = new Array("<?=$this->lang->line('application_paid');?>", "<?=$this->lang->line('application_due');?>");
var options = {

    scaleShowVerticalLines: false,

    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",

    tooltipTemplate: "<?=$this->lang->line('application_paid');?> <%= value %>"

};
 var tileChart = new Chart(ctx).Line(data, options);


});
</script>