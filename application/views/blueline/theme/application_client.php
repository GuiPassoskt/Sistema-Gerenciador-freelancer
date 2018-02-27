<?php 
/**
 * @file        Application View
 * @author      Luxsys <support@luxsys-apps.com>
 * @copyright   By Luxsys (http://www.luxsys-apps.com)
 * @version     2.2.0
 */

$act_uri = $this->uri->segment(1, 0);
$lastsec = $this->uri->total_segments();
$act_uri_submenu = $this->uri->segment($lastsec);
if(!$act_uri){ $act_uri = 'cdashboard'; }
if(is_numeric($act_uri_submenu)){ 
    $lastsec = $lastsec-1; 
    $act_uri_submenu = $this->uri->segment($lastsec);
}
 ?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <META Http-Equiv="Cache-Control" Content="no-cache">
    <META Http-Equiv="Pragma" Content="no-cache">
    <META Http-Equiv="Expires" Content="0">
    <link rel="SHORTCUT ICON" href="<?=base_url()?>assets/blueline/img/favicon.ico"/>
    <title><?=$core_settings->company;?></title> 

    <!-- Bootstrap core CSS and JS -->
    <link href="<?=base_url()?>assets/blueline/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?=base_url()?>assets/blueline/js/plugins/jquery-1.11.0.min.js"></script>


    <!-- Custom styles for this template -->
    <link href="<?=base_url()?>assets/blueline/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript">
  WebFontConfig = {
    google: { families: [ 'Open+Sans:400italic,400,300,600,700:latin' ] }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })(); </script>
    <!-- Plugins -->
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery-ui-1.10.3.custom.min.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/datepicker.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/bootstrap-timepicker.css"/>
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/colorpicker.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/refineslide.css"/>
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery-slider.css" />
    <!--<link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/checkbox-radio-switch.css" />-->
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/summernote.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/chosen.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/dataTables.bootstrap.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery.mCustomScrollbar.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/xcharts.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/nprogress.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/jquery-labelauty.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/easy-pie-chart-style.css" />
    

    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/blueline.css"/>
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/user.css"/> 
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/ionicons.min.css" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

<body>
<div id="mainwrapper">
 <a href="#" class="menu-trigger"><i class="fa fa-bars visible-xs"></i></a>
    <div class="side">
    <div class="sidebar-bg"></div>
        <div class="sidebar">
        <div class="navbar-header">
         
          <a class="navbar-brand" href="#"><img src="<?=base_url()?><?=$core_settings->logo;?>" alt="<?=$core_settings->company;?>"></a>
        </div>
          
          <ul class="nav nav-sidebar">
              <?php foreach ($menu as $key => $value) { ?>
               <?php 
               $icon = "";
               switch($value->icon){
                    case "icon-th":
                        $icon = "fa-dashboard";
                    break;
                    case "icon-inbox":
                        $icon = "fa-inbox";
                    break;
                    case "icon-briefcase":
                        $icon = "fa-lightbulb-o";
                    break;
                    case "icon-user":
                        $icon = "fa-users";
                    break;
                    case "icon-list-alt":
                        $icon = "fa-file-text-o";
                    break;
                    case "icon-calendar":
                        $icon = "fa-calendar";
                    break;
                    case "icon-file":
                        $icon = "fa-archive";
                    break;
                    case "icon-tag":
                        $icon = "fa-tag";
                    break;
                    case "icon-cog":
                        $icon = "fa-cog";
                    break;
                    default:
                        $icon = $value->icon;
                    break;
               }
               
               ?>
               <li id="<?=strtolower($value->name);?>" class="<?php if ($act_uri == strtolower($value->link)) {echo "active";}?>"><a href="<?=site_url($value->link);?>"><span class="menu-icon"><i class="fa <?=$icon;?>"></i></span><span class="nav-text"><?php echo $this->lang->line('application_'.$value->link);?></span>
                <?php if(strtolower($value->link) == "cmessages" && $messages_new[0]->amount != "0"){ ?><span class="notification-badge"><?=$messages_new[0]->amount;?></span><?php } ?>
                <?php if(strtolower($value->link) == "quotations" && $quotations_new[0]->amount != "0"){ ?><span class="notification-badge"><?=$quotations_new[0]->amount;?></span><?php } ?>
                <?php if(strtolower($value->link) == "cestimates" && $estimates_new[0]->amount != "0"){ ?><span class="notification-badge"><?=$estimates_new[0]->amount;?></span><?php } ?>

               </a> </li>
              <?php } ?>
          </ul>
            
    
          
        </div>
    </div>

    <div class="content-area">
      <div class="row mainnavbar">
      <div class="topbar">
      <?php 
                if($this->client->userpic != 'no-pic.png'){
                  $userimage = base_url()."files/media/".$this->client->userpic;
                }else{
                  $userimage = get_gravatar($this->client->email);
                }
                 ?>
      <span class="inline visible-xs"><a href="<?=site_url("agent");?>" data-toggle="mainmodal" title="<?=$this->lang->line('application_profile');?>"><img class="img-circle topbar-userpic" src="<?=$userimage;?>" height="21px"></a></span>
      <img class="img-circle topbar-userpic hidden-xs" src="<?=$userimage;?>" height="21px">
      
      <span class="hidden-xs"><?php echo character_limiter($this->client->firstname." ".$this->client->lastname, 25);?> </span>
      <span class="hidden-xs"><a href="<?=site_url("cmessages");?>" title="<?=$this->lang->line('application_messages');?>"><i class="fa fa-inbox"></i></a></span>
      <span class="hidden-xs"><a href="<?=site_url("agent");?>" data-toggle="mainmodal" title="<?=$this->lang->line('application_profile');?>"><i class="fa fa-cog"></i></a></span>
      <span class="btn-group">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                      <?php if(!empty($core_settings->language)){$default_language = $core_settings->language; }else{ $default_language = "english"; } ?>
                                      <img src="<?=base_url()?>assets/blueline/img/<?php if($this->input->cookie('language') != ""){echo $this->input->cookie('language');}else{echo $default_language;} ?>.png" style="margin-top:-49px" align="middle">
                                 
                                    </a>
                                     <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
                                        <?php if ($handle = opendir('application/language/')) {

                            while (false !== ($entry = readdir($handle))) {
                                if ($entry != "." && $entry != "..") {
                                  ?><li><a href="<?=base_url()?>agent/language/<?=$entry;?>"><img src="<?=base_url()?>assets/blueline/img/<?=$entry;?>.png" class="language-img"> <?=ucwords($entry);?></a></li><?php
                                }
                            }
                  
                            closedir($handle);
                            } 
                      ?>
                      
                                      </ul>
            </span>
      <span><a href="<?=site_url("logout");?>" title="<?=$this->lang->line('application_logout');?>"><i class="fa fa-sign-out"></i></a></span>
      </div>
          
        </div>
        
        
        
        
        <?=$yield?>
      
      
            

      

    </div>
    <!-- Notify -->
    <?php if($this->session->flashdata('message')) { $exp = explode(':', $this->session->flashdata('message'))?>
        <div class="notify <?=$exp[0]?>"><?=$exp[1]?></div>
    <?php } ?>

      
    <!-- Modal -->
    <div class="modal fade" id="mainModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="mainModalLabel" aria-hidden="true"></div>
    

 
    <!-- Bootstrap core JavaScript -->
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery-ui-1.10.3.custom.min.js"></script>
    
    <!-- Plugins -->
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/date-time/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/date-time/bootstrap-timepicker.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/bootstrap-colorpicker.min.js"></script>
    
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.knob.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.autosize-min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.inputlimiter.1.3.1.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.maskedinput.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.refineslide.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/summernote.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.nanoscroller.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jqBootstrapValidation.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/chart.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/nprogress.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery-labelauty.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/validator.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/timer.jquery.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/jquery.easypiechart.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.ui.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/blueline.js"></script>
    

    
 </div> <!-- Mainwrapper end -->   

      <script type="text/javascript" charset="utf-8">

                              

      $(document).ready(function(){

        $("form").validator();
        
   

        $("#menu li a, .submenu li a").removeClass("active");
        if("" == "<?php echo $act_uri_submenu; ?>"){$("#sidebar li a").first().addClass("active");}  
        <?php if($act_uri_submenu != "0"){ ?>$(".submenu li a#<?php echo $act_uri_submenu; ?>").parent().addClass("active");<?php } ?>
        $("#menu li#<?php echo $act_uri; ?>").addClass("active");

        //Datatables

        var dontSort = [];
                $('.data-sorting thead th').each( function () {
                    if ( $(this).hasClass( 'no_sort' )) {
                        dontSort.push( { "bSortable": false } );
                    } else {
                        dontSort.push( null );
                    }
                } );


        $('table.data').dataTable({
          "iDisplayLength": 25,
          stateSave: true,
          "bLengthChange": false,
          "aaSorting": [[ 0, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
        $('table.data-media').dataTable({
          "iDisplayLength": 8,
          stateSave: true,
          "bLengthChange": false,
          "bFilter": false, 
          "bInfo": false,
          "aaSorting": [[ 0, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": " ",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
        $('table.data-no-search').dataTable({
          "iDisplayLength": 8,
          stateSave: true,
          "bLengthChange": false,
          "bFilter": false, 
          "bInfo": false,
          "aaSorting": [[ 1, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": " ",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          },
          fnDrawCallback: function (settings) {
              $(this).parent().toggle(settings.fnRecordsDisplay() > 0);
              

          }
        });
        $('table.data-sorting').dataTable({
          "iDisplayLength": 25,
          "bLengthChange": false,
          "aoColumns": dontSort,
          "aaSorting": [[ 1, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>",
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });
        $('table.data-small').dataTable({
          "iDisplayLength": 5,
          "bLengthChange": false,
          "aaSorting": [[ 2, 'desc']],
          "oLanguage": {
          "sSearch": "",
            "sInfo": "<?=$this->lang->line('application_showing_from_to');?>", 
            "sInfoEmpty": "<?=$this->lang->line('application_showing_from_to_empty');?>",
            "sEmptyTable": "<?=$this->lang->line('application_no_data_yet');?>",
            "oPaginate": {
              "sNext": '<i class="fa fa-arrow-right"></i>',
              "sPrevious": '<i class="fa fa-arrow-left"></i>',
            }
          }
        });

      });
      
      
      </script>

 </body>
</html>
