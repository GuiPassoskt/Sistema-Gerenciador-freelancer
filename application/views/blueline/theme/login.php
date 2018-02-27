<?php 
/**
 * @file        Login View
 * @author      Luxsys <support@luxsys-apps.com>
 * @copyright   By Luxsys (http://www.luxsys-apps.com)
 * @license     http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version     2.2.0
 * @link        http://pear.php.net/package/PackageName
 */
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <META Http-Equiv="Cache-Control" Content="no-cache">
    <META Http-Equiv="Pragma" Content="no-cache">
    <META Http-Equiv="Expires" Content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?=$core_settings->company;?></title>
    
    <link href="<?=base_url()?>assets/blueline/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/blueline/css/blueline.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/blueline/css/user.css" rel="stylesheet" /> 
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
     <link rel="SHORTCUT ICON" href="<?=base_url()?>assets/blueline/img/favicon.ico"/>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body class="login">
    <div class="container-fluid">
      <div class="row">
        <?=$yield?>
      </div>
    </div>
     <!-- Notify -->
    <?php if($this->session->flashdata('message')) { $exp = explode(':', $this->session->flashdata('message'))?>
        <div class="notify <?=$exp[0]?>"><?=$exp[1]?></div>
    <?php } ?>
    <script src="<?=base_url()?>assets/blueline/js/plugins/jquery-1.11.0.min.js"></script>
    <script src="<?=base_url()?>assets/blueline/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.ui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/validator.min.js"></script>
    <script type="text/javascript">
            $(document).ready(function(){
              $("form").validator();
            //$(".form-signin").delay(400).addClass("slidein");
            $(".form-signin").velocity("transition.flipYIn", { delay: 400});
           
                <?php if($error == "true") { ?>
                    $("#error").velocity("transition.slideDownIn", { delay: 900});  
                <?php } ?>

                //notify 
            $('.notify').velocity({
                  opacity: 1,
                  right: "10px",
                }, 800, function() {
                  $('.notify').delay( 3000 ).fadeOut();
                });


             });

            
        </script> 

  </body>
</html>
