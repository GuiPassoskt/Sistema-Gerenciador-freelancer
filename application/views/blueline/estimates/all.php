	<div class="col-sm-12  col-md-12 main">  
    
     <div class="row">
      <a href="<?=base_url()?>estimates/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_estimate');?></a>
      <div class="btn-group pull-right-responsive margin-right-3">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php $last_uri = $this->uri->segment($this->uri->total_segments()); if($last_uri != "estimates"){echo $this->lang->line('application_'.$last_uri);}else{echo $this->lang->line('application_all');} ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right" role="menu">
            <?php foreach ($submenu as $name=>$value):?>
	                <li><a id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$name?></a></li>
	            <?php endforeach;?>
          </ul>
      </div>
    </div>  
      <div class="row">

         <div class="table-head"><?=$this->lang->line('application_estimates');?></div>
         <div class="table-div">
		<table class="data table" id="estimates" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
		<thead>
			<th width="70px" class="hidden-xs"><?=$this->lang->line('application_estimate_id');?></th>
			<th ><?=$this->lang->line('application_client');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_issue_date');?></th>
			<th class="hidden-xs"><?=$this->lang->line('application_total');?></th>
			<th><?=$this->lang->line('application_status');?></th>
			<th><?=$this->lang->line('application_action');?></th>
		</thead>
		<?php foreach ($estimates as $value):
        $change_date = "";
        switch($value->estimate_status){
          case "Open": $label = "label-default"; break;
          case "Accepted": $label = "label-success"; $change_date = 'title="'.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          case "Sent": $label = "label-warning"; $change_date = 'title="'.date($core_settings->date_format, human_to_unix($value->estimate_sent.' 00:00')).'"'; break; 
          case "Declined": $label = "label-important"; $change_date = 'title="'.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          case "Invoiced": $label = "label-chilled"; $change_date = 'title="'.$this->lang->line('application_Accepted').' '.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          case "Revised": $label = "label-warning"; $change_date = 'title="'.$this->lang->line('application_Revised').' '.date($core_settings->date_format, human_to_unix($value->estimate_accepted_date.' 00:00')).'"'; break;
          

          default: $label = "label-default"; break;
        } ?>
		<tr id="<?=$value->id;?>" >
			<td class="hidden-xs"><?=$core_settings->estimate_prefix;?><?=$value->reference;?></td>
			<td><span class="label label-info"><?php if(isset($value->company->name)){echo $value->company->name; }?></span></td>
			<td class="hidden-xs"><span><?php $unix = human_to_unix($value->issue_date.' 00:00'); echo '<span class="hidden">'.$unix.'</span> '; echo date($core_settings->date_format, $unix);?></span></td>
			<td class="hidden-xs"><?=display_money(sprintf("%01.2f", round($value->sum, 2)));?></td>
			<td><span class="label  <?=$label?> tt" <?=$change_date;?>><?=$this->lang->line('application_'.$value->estimate_status);?></span></td>
		
			<td class="option" width="8%">
				        <button type="button" class="btn-option delete po" data-toggle="popover" data-placement="left" data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?=base_url()?>estimates/delete/<?=$value->id;?>'><?=$this->lang->line('application_yes_im_sure');?></a> <button class='btn po-close'><?=$this->lang->line('application_no');?></button> <input type='hidden' name='td-id' class='id' value='<?=$value->id;?>'>" data-original-title="<b><?=$this->lang->line('application_really_delete');?></b>"><i class="fa fa-times"></i></button>
				        <a href="<?=base_url()?>estimates/update/<?=$value->id;?>" class="btn-option" data-toggle="mainmodal"><i class="fa fa-cog"></i></a>
			</td>
		</tr>

		<?php endforeach;?>
	 	</table>
            </div>

      </div>

<script>
$(document).ready(function(){ 
                        //xChart 
                          var tt = document.createElement('div'),
                            leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                            topOffset = -32;
                          tt.className = 'ex-tooltip';
                          document.body.appendChild(tt);

                          var data = {
                            "xScale": "time",
                            "yScale": "linear",
                            "yMin": 0,
                            
                            "main": [
                              {
                                "className": ".invoice_chart_opened",
                                "data": [
                                <?php
                                $days = array(); 
                                $this_week_days = array(
                                  date("Y-m-d",strtotime('monday this week')),
                                  date("Y-m-d",strtotime('tuesday this week')),
                                    date("Y-m-d",strtotime('wednesday this week')),
                                      date("Y-m-d",strtotime('thursday this week')),
                                        date("Y-m-d",strtotime('friday this week')),
                                          date("Y-m-d",strtotime('saturday this week')),
                                            date("Y-m-d",strtotime('sunday this week')));
                                foreach ($estimates_paid_this_month_graph as $value) {
                                  $days[$value->date_formatted] = $value->amount;

                                  //$days[$value->date_day."_date"] = $value->date_formatted;
                                }
                                foreach ($this_week_days as $selected_day) {
                                  $y = 0;
                                    if(isset($days[$selected_day])){ $y = $days[$selected_day];}
                                      ?>{
                                    
                                    "x": "<?php echo $selected_day; ?>",
                                    "y": <?php echo $y; ?>
                                  },
                                  <?php } ?>
          
                                 
                                ]}, 
                              {
                                "className": ".invoice_chart_closed",
                                "data": [

                                <?php
                                $days = array(); 
                                $this_week_days = array(
                                  date("Y-m-d",strtotime('monday this week')),
                                  date("Y-m-d",strtotime('tuesday this week')),
                                    date("Y-m-d",strtotime('wednesday this week')),
                                      date("Y-m-d",strtotime('thursday this week')),
                                        date("Y-m-d",strtotime('friday this week')),
                                          date("Y-m-d",strtotime('saturday this week')),
                                            date("Y-m-d",strtotime('sunday this week')));
                                foreach ($estimates_due_this_month_graph as $value) {
                                  $days[$value->date_formatted] = $value->amount;

                                  //$days[$value->date_day."_date"] = $value->date_formatted;
                                }
                                foreach ($this_week_days as $selected_day) {
                                  $y = 0;
                                    if(isset($days[$selected_day])){ $y = $days[$selected_day];}
                                      ?>{
                                    
                                    "x": "<?php echo $selected_day; ?>",
                                    "y": <?php echo $y; ?>
                                  },
                                  <?php } ?>
                                  
                                ] 



                              }
                            ]
                          };
                          var opts = {
                            "dataFormatX": function (x) { return d3.time.format('%Y-%m-%d').parse(x); },
                            "tickFormatX": function (x) { return d3.time.format('%a')(x); },
                            "mouseover": function (d, i) {
                              var pos = $(this).offset();
                              var lineclass = $(this).parent().attr("class");
                              lineclass = lineclass.split(" ");
                              
                              if( lineclass[2] == "invoice_chart_closed"){
                                var linename = "<?=$this->lang->line('application_due');?>";
                              }else{
                                var linename = "<?=$this->lang->line('application_paid');?>";
                              }
                              $(tt).text(d.y + ' estimates ' +linename)
                                .css({top: topOffset + pos.top, left: pos.left + leftOffset})
                                .show();
                            },
                            "mouseout": function (x) {
                              $(tt).hide();
                            },
                            "tickHintY": 4,
                            "paddingLeft":20,
                             
                          };
                          if($("#invoice_line_chart").length != 0) {
                          var myChart = new xChart('line-dotted', data, '#invoice_line_chart', opts);
                          }
                          //xChart End
});
</script>