$.ajaxSetup ({
    // Disable caching of AJAX responses
    cache: false
});

// Support for AJAX loaded modal window.
// Focuses on first input textbox after it loads the window. 
function modalfunc(){
$('[data-toggle="mainmodal"]').bind('click',function(e) {
  NProgress.start();
  e.preventDefault();
  var url = $(this).attr('href');
 
  if (url.indexOf('#') === 0) {
    $('#mainModal').modal('open');
  } else {
    $.get(url, function(data) { 
                        $('#mainModal').modal();
                        $('#mainModal').html(data);

                        
    }).success(function() { NProgress.done();  });
  }
}); 
}
modalfunc();
//Ajax loaded content
$(document).on("click", '.ajax', function (e) {
  e.preventDefault();
  NProgress.start();

  $(".message-list ul.list-striped li").removeClass('active');
  $(this).parent().addClass('active');
  
  //$("html, body").animate({ scrollTop: 0 }, 600);
  var url = $(this).attr('href');
  if (url.indexOf('#') === 0) {
    
  } else {
    $.get(url, function(data) { 
                        $('#ajax_content').html(data);
                        $(".message_content:gt(1)").hide();
                        $('#ajax_content').velocity("transition.fadeIn");
    }).success(function() { 
            $(".message_content:gt(1)").velocity("transition.fadeIn");  
            
            $(".scroll-content").mCustomScrollbar({theme:"dark-2"}); 
            NProgress.done(); 
        });
  }
}); 

//Ajax background load
  $(document).on("click", '.ajax-silent', function (e) {
  e.preventDefault();
  NProgress.start();
  var url = $(this).attr('href');
  
    $.get(url, function(data) { 
                        
    }).success(function() { $('.message-list ul li a').first().click(); NProgress.done(); });
  
}); 

          //button loaded on click
        $(document).on("click", '.button-loader', function (e) {
          var value = $( this ).text();
            $(this).html('<i class="fa fa-spinner fa-spin"></i> '+ value);
        });


  //Ajax background load
  $(document).on("change", '.description-setter', function (e) {
  
  var itemid = $(this).val();
  var description = $("#item"+itemid).html();
  $("#description").val(description);

  
}); 

  //Ajax background load
  $(document).on("change", '.task-check', function (e) {
  e.preventDefault();
  NProgress.start();
  var url = $(this).data('link');
  
    $.get(url, function(data) { 
                        
    }).success(function() { NProgress.done(); });
  
}); 

//message list delete item
$(document).on("click", '.message-list-delete', function (e) {

  $(this).parent().fadeTo("slow", 0.01, function(){ //fade
             $(this).slideUp("fast", function() { //slide up
                 $(this).remove(); //then remove from the DOM
             });
         });
});  




//message reply

$(document).on("click", '#reply', function (e) {
 
 $("#reply").velocity({'height': '240px'}, {queue: false, complete: function(){ 
    $('#reply').wysihtml5({"size": 'small'});
    $('.reply #send').fadeIn('slow');

    } });


}); 
$(".nano").nanoScroller();



//Ajax reply form submit
  $(document).on("click", '.ajaxform #send', function (e) {

    var content = $('textarea[name="message"]').html($('#reply').code());
    var url = $(this).closest('form').attr('action'); 
    var active = $(this);
    var formData = new FormData($(this).closest('form')[0]);
     
    if($('textarea[name="message"]').val() === ""){
      $('.comment-content .note-editable').css("border-top", "2px solid #D43F3A");

      var value = $('.button-loader').html().replace('<i class="fa fa-spinner fa-spin"></i> ', "");
      $('.button-loader').html(value);
    }
    else{
    $.ajax({
           type: "POST",
           url: url,
           mimeType: "multipart/form-data",
           contentType: false,
           cache: false,
           processData: false,
           data: formData,
           success: function(data)
           {

               $('#message-list li.active').click().click();
              
               $(".ajaxform #send").html('<i class="fa fa-check-circle-o"></i>');
              
                  $('.message-content-reply, #timeline-comment').slideUp('slow').velocity(
                    { opacity: 0 },
                    { queue: false, duration: 'slow' }
                  );
                 $(".note-editable").html("");
                 var reload = active.closest('form').data('reload');
                 if(reload) {
                     $('#'+reload).load(document.URL + ' #'+reload, function() {
                         $('#'+reload+' ul li:nth-child(2) .timeline-panel').addClass("highlight");
                         $('#'+reload+' ul li:nth-child(2) .timeline-panel').delay("5000").removeClass("highlight");
                         
                        summernote();
                     }); 
                     
                 }
                 
                
           },
           error: function(data)
           {
            
               $('#message-list li.active').click().click();
               
               $(".ajaxform #send").html('<i class="fa fa-check-circle-o"></i>');
              
                  $('.message-content-reply, #timeline-comment').slideUp('slow').velocity(
                    { opacity: 0 },
                    { queue: false, duration: 'slow' }
                  );
                 $(".note-editable").html("");
                 var reload = active.closest('form').data('reload');
                 if(reload) {
                     $('#'+reload).load(document.URL + ' #'+reload, function() {
                         $('#'+reload+' ul li:nth-child(2) .timeline-panel').addClass("highlight");
                         $('#'+reload+' ul li:nth-child(2) .timeline-panel').delay("5000").removeClass("highlight");
                         
                        summernote();
                     }); 
                     
                 }
                 
                
           }
         }); }

    return false;
});

//ajax page section reload
$(document).on("click", '.section-reload #send', function (e) {
    e.preventDefault();
    NProgress.start();
    $('#tasks-tab').load(document.URL +  ' #tasks-tab');
    
      NProgress.done();
     
});



  $(document).on("click", '.dynamic-form .send', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var content = $('textarea.summernote-modal').html($('#mainModal .note-editable').code());
    var url = $(this).closest('form').attr('action'); 
    var active = $(this);
    var data = new FormData($(this).closest('form')[0]);
   
    $.ajax({
           type: "POST",
           url: url,
           mimeType: "multipart/form-data",
           contentType: false,
           cache: false,
           processData: false, 
           data: data,
           success: function(data, textStatus, jqXHR)
           {
 if(typeof data.error === 'undefined')
            {
                
            }
            else
            {
               
                console.log('ERRORS: ' + data.error);
            }
                 var reload = active.closest('form').data('reload');
                 if(reload) {
                     $('#'+reload).velocity("transition.slideDownOut");
                     $('#'+reload).load(document.URL + ' #'+reload, function(data) {
                        
                         $('#'+reload + ' .checkbox-nolabel').labelauty({ label: false });
                         modalfunc();
                         $('#'+reload).velocity("transition.slideUpIn"); 
                         keepmodal = active.data('keepmodal');
                         if(keepmodal === undefined){
                            $('#mainModal').modal('hide');
                          }else{
                            active.closest('form')[0].reset();
                            $("#mainModal .note-editable").html("");
                          }
                          var value = active.text().replace('<i class="fa fa-spinner fa-spin"></i> ', '');
                          active.html(value);
                        
                     }); 
                     
                 }
                  
           },
           error: function(formData)
           {
              
              var reload = active.closest('form').data('reload');
                 if(reload) {
                     $('#'+reload).velocity("transition.slideDownOut");
                     $('#'+reload).load(document.URL + ' #'+reload, function(data) {
                        
                         $('#'+reload + ' .checkbox-nolabel').labelauty({ label: false });
                         $('#'+reload).velocity("transition.slideUpIn"); 
                         keepmodal = active.data('keepmodal');
                         if(keepmodal === undefined){
                            $('#mainModal').modal('hide');
                          }else{
                            active.closest('form')[0].reset();
                            $("#mainModal .note-editable").html("");
                          }
                          var value = active.text().replace('<i class="fa fa-spinner fa-spin"></i> ', '');
                          active.html(value);
                        
                     }); 
                     
                 }
                
           }
         });

    return false;
});


//Project Notes
$(document).on("click", '.note-form #send', function (e) {
var button = this;
var content = $('textarea[name="note"]').html($('#textfield').code());
    var url = $(this).closest('form').attr('action'); 
    var note = $(this).closest('form').serialize();

    $.ajax({
           type: "POST",
           url: url,
           data: note,
           success: function(data)
           {
            var value = $( button ).text();
            var str = value.replace('<i class="fa fa-spinner fa-spin"></i> ', "");
            $(button).html(str);
             $('#changed').velocity("transition.fadeOut"); 
           },
           error: function(data)
           {
            
            var value = $( button ).text();
            var str = value.replace('<i class="fa fa-spinner fa-spin"></i> ', "");
            $(button).html(str);
             $('#changed').velocity("transition.fadeOut");
           }
         });

    return false;
  
}); 
$(document).on("focus", '#_notes .note-editable', function (e) {
$('#changed').velocity("transition.fadeIn");
}); 
$(document).on("click", '#_notes .addtemplate', function (e) {
$('#changed').velocity("transition.fadeIn");
}); 



$('.to_modal').click(function(e) {
    e.preventDefault();
    var href = $(e.target).attr('href');
    if (href.indexOf('#') == 0) {
        $(href).modal('open');
    } else {
        $.get(href, function(data) {
            $('<div class="modal fade" >' + data + '</div>').modal();
        });
    }
});





//Clickable rows
	$(document).on("click", 'table#projects td, table#clients td, table#invoices td, table#cprojects td, table#cinvoices td, table#estimates td, table#cestimates td, table#quotations td, table#messages td, table#cmessages td, table#subscriptions td, table#csubscriptions td, table#tickets td, table#ctickets td', function (e) {
	    
      var id = $(this).parent().attr("id");
	    if(id && !$(this).hasClass("noclick")){
	   		var site = $(this).closest('table').attr("rel")+$(this).closest('table').attr("id");
	    	if(!$(this).hasClass('option')){window.location = site+"/view/"+id;}
	    } 
  	});
      $(document).on("click", 'table#media td', function (e) {
	    var id = $(this).parent().attr("id");
	    if(id){
	    	var site = $(this).closest('table').attr("rel");
	    	if(!$(this).hasClass('option')){window.location = site+"/view/"+id;}
	    }
    });
      $(document).on("click", 'table#custom_quotations_requests td', function (e) {
      var id = $(this).parent().attr("id");
      if(id){
        var site = $(this).closest('table').attr("rel");
        if(!$(this).hasClass('option')){window.location = "quotations/cview/"+id;}
      }
    });
      $(document).on("click", 'table#quotation_form td', function (e) {
      var id = $(this).parent().attr("id");
      if(id){
        var site = $(this).closest('table').attr("rel");
        if(!$(this).hasClass('option')){window.location = "formbuilder/"+id;}
      }
    });



    
      /* -------------- Summernote WYSIWYG Editor ------------- */
         function summernote(){
              $('.summernote').summernote({
                height:"200px",
                shortcuts: false,
                toolbar: [
                  ['style', ['style']], // no style button
                  ['style', ['bold', 'italic', 'underline', 'clear']],
                  ['fontsize', ['fontsize']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['height', ['height']],
                  ['insert', []], //for Custom Templates
                ]
              });
              var postForm = function() {
                var content = $('textarea[name="content"]').html($('#textfield').code());
              }
         }
         summernote();
          $('.summernote-note').summernote({
            height:"360px",
            shortcuts: false,
            toolbar: [
              ['insert', []], //for Custom Templates
              ['style', ['style']], // no style button
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['fontsize', ['fontsize']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['height', ['height']],
              
            ]
          });
          var postForm = function() {
            var content = $('textarea[name="note"]').html($('#textfield').code());
          }
        
          $('.summernote-big').summernote({
            height:"450px",
            shortcuts: false,
            toolbar: [
              ['insert', []], //for Custom Templates
              ['style', ['style']], // no style button
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['fontsize', ['fontsize']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['height', ['height']],
              
            ]
          });

        

      /* -------------- Summernote WYSIWYG Editor ------------- */


      //Custom select plugin
      $(".chosen-select").chosen({disable_search_threshold: 4, width: "100%"});


      //notify 
      
      $('.notify').velocity({
            opacity: 1,
            right: "10px",
          }, 800, function() {
            $('.notify').delay( 3000 ).fadeOut();
          });
      

      // List striped
      $("ul.striped li:even").addClass("listevenitem");

      //Custom Scrollbar
      $(".scroll-content").mCustomScrollbar({theme:"dark-2"});
      $(".scroll-content-2").mCustomScrollbar({theme:"dark-2"});
      
      //Form validation
      $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();

      $('.use-tooltip').tooltip();
       $('.tt').tooltip();
       
      $('.po').popover({html:true});
      
        //change comma to point
        $(document).on("change", '.comma-to-point', function (e) {
          var str = $(this).val().replace(",", ".");
          $(this).val(str);
        });

       $(document).on("click", '.po-close', function (e) {
          $('.po').popover('hide');
      });
      $(document).on("click", '.po-delete', function (e) {
          $(this).closest('tr').velocity("transition.slideRightOut");
      });
       
       $('.date-picker').datepicker();
        $('#timepicker1').timepicker({
          minuteStep: 1,
          showSeconds: true,
          showMeridian: false
        });
        
        /* 
        $('.colorpicker').colorpicker();
        $('.colorpicker input').click(function() {
          $(this).parents('.colorpicker').colorpicker('show');
        })
        */

        // Checkbox Plugin

        $(".checkbox").labelauty();
        $(".checkbox-nolabel").labelauty({ label: false });

        //Checkbox for slider enable/disable
        $( ".lbl" ).click(function(){
          var isDisabled = $( "#slider-range" ).slider( "option", "disabled" );
          if(isDisabled){
            $( "#slider-range" ).slider( "option", "disabled", false );
          }else{
            $( "#slider-range" ).slider( "option", "disabled", true );
          }
          
        });


        //slider config
        $( "#slider-range" ).slider({
          range: "min",
          min: 0,
          max: 100,
          value: 1,
          slide: function( event, ui ) {
            $( "#progress-amount" ).html( ui.value );
            $( "#progress" ).val( ui.value );
          }
        });

        //upload button
        $(document).on("change", '#uploadBtn', function (e) {
          var value = $( this ).val().replace(/\\/g, '/').replace(/.*\//, '');
            $("#uploadFile").val(value);
        });
        $(document).on("change", '#uploadBtn2', function (e) {
          var value = $( this ).val().replace(/\\/g, '/').replace(/.*\//, '');
            $("#uploadFile2").val(value);
        });

        //field disable switcher
        $(document).on("change", '.switcher', function (e) {
          var fieldID = $(this).data('switcher');
          
          if($(this).val() == "" || $(this).val() == "0"){
            $('#'+fieldID).attr("disabled", true);
            $('#'+fieldID).val('0');
            $('#'+fieldID).trigger("chosen:updated");

          }else{
            $('#'+fieldID).removeAttr("disabled");
            $('#'+fieldID).trigger("chosen:updated");
          }
        });

        //client -> project choser
        $(document).on("change", '.getProjects', function (e) {
          var fieldID = $(this).data('destination');
          var selectedValue = $(this).val();
          
          if(selectedValue == "" || selectedValue == "0"){
            $('#'+fieldID+' optgroup').attr("disabled", true);
            $('#'+fieldID).val('0');
            $('#'+fieldID).trigger("chosen:updated");

          }else{
            $('#'+fieldID+' optgroup').attr("disabled", true);
            $('#'+fieldID).val('0');
            $('#optID_'+selectedValue).removeAttr("disabled");
            $('#'+fieldID).trigger("chosen:updated");
          }
        });


        
        //on todo-checkbox click
         $(document).on("click", '.todo-checkbox', function (e) {
             
           var url = $(this).data('link');
           //$(this).attr("checked");
           if($(this).closest('li').hasClass("done")){
               $(this).closest('li').removeClass("done");
           }else{
               $(this).closest('li').addClass("done");
           }
            $.get(url, function(data) { 
                                
            }).success(function() {  });
             
        
        });
        //on todo click
         $(document).on("click", '.todo li p.name', function (e) {
           
                $(this).closest("li").toggleClass( "slidedown" );
        
        });

        //message reply slide down
        $(document).on("click", '.message-reply-button', function (e) {
        //button loaded on click
        $(document).on("click", '.button-loader', function (e) {
          var value = $( this ).text();
            $(this).html('<i class="fa fa-spinner fa-spin"></i> '+ value);
        });
        $('.summernote-ajax').summernote({
            height:"200px",
            shortcuts: false,
            toolbar: [
              //['style', ['style']], // no style button
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['fontsize', ['fontsize']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['height', ['height']],
              ['insert', []], //for Custom Templates
            ]
          });
          $(".message-content-reply").slideDown('slow').velocity(
            { opacity: 1 },
            { queue: false, duration: 'slow' }
          );
        })

        //Timeline Comment field slide down
        $(document).on("click", '.open-comment-box', function (e) {
          $(".add-comment").slideToggle('slow').velocity(
            { opacity: 1 },
            { queue: false, duration: 'slow' }
          );

        });

        //Mobile Menu
        $(document).on("click", '.menu-trigger', function (e) {
          $(".side").addClass( 'menu-action');
          $(".sidebar-bg").addClass( 'show');
          /*$(".sidebar, .navbar-header").addClass( 'show');*/


        });
        $(document).on("click", '.content-area', function (e) {
          $(".side").removeClass( 'menu-action');
          $(".sidebar-bg").removeClass( 'show');
          /*$(".sidebar, .navbar-header").removeClass( 'show');*/

        });

        //check all checkboxes
        $("#checkAll").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $('#checkAll, .bulk-box').click(function(){
        if($('.bulk-box:checked').length){
          $("#bulk-button").addClass("btn-success");
        }else{
          $("#bulk-button").removeClass("btn-success");
        }
      });
      $(".bulk-dropdown li").click(function(){
        NProgress.start();
        var values = $('input:checkbox:checked.bulk-box').map(function () {
          return this.value;
        }).get();
        $('#list-data').val(values);
        var action = $("#bulk-form").attr('action');
        $("#bulk-form").attr('action', action+$(this).data("action"));
        $('#bulk-form').submit();
        
      });

      //bulk action setter
$(document).on("click", '.bulk-dropdown ul li a', function (e) {
  var action = $("#bulk-form").attr('action');
  $("#bulk-form").attr('action', action+$(this).data("action"));

});

      //fade in
$(document).on("click", '#fadein', function (e) {
$(".fadein").toggleClass("slide");


});

$(document).on("click", '.sortListTrigger', function (e) {
sortList();

});
function sortList() { var mylist = $('ul.sortlist');
  var listitems = mylist.children('li').get();
  listitems.sort(function(a, b) {
     var compA = $(a).attr("class").split(' ').toString().toUpperCase();
     var compB = $(b).attr("class").split(' ').toString().toUpperCase();
     return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
  })
  $.each(listitems, function(idx, itm) { mylist.append(itm); });
}
        
        

function startTimer(starttime) {
  $("#timer").timer({
  action: 'start', 
  seconds: starttime
  });
}

$(function() {
    $('.easyPieChart').easyPieChart({
        barColor: function (percent) {
       return (percent < 100 ? '#11A7DB' : percent = 100 ? '#5cb85c' : '#cb3935');
    },
        trackColor: '#E5E9EC',
        scaleColor: false,
        size:55

    });

});
//ChartJs Config
Chart.defaults.global = {
    // Boolean - Whether to animate the chart
    animation: true,

    // Number - Number of animation steps
    animationSteps: 60,

    // String - Animation easing effect
    // Possible effects are:
    // [easeInOutQuart, linear, easeOutBounce, easeInBack, easeInOutQuad,
    //  easeOutQuart, easeOutQuad, easeInOutBounce, easeOutSine, easeInOutCubic,
    //  easeInExpo, easeInOutBack, easeInCirc, easeInOutElastic, easeOutBack,
    //  easeInQuad, easeInOutExpo, easeInQuart, easeOutQuint, easeInOutCirc,
    //  easeInSine, easeOutExpo, easeOutCirc, easeOutCubic, easeInQuint,
    //  easeInElastic, easeInOutSine, easeInOutQuint, easeInBounce,
    //  easeOutElastic, easeInCubic]
    animationEasing: "easeOutQuart",

    // Boolean - If we should show the scale at all
    showScale: true,

    // Boolean - If we want to override with a hard coded scale
    scaleOverride: false,

    // ** Required if scaleOverride is true **
    // Number - The number of steps in a hard coded scale
    scaleSteps: null,
    // Number - The value jump in the hard coded scale
    scaleStepWidth: null,
    // Number - The scale starting value
    scaleStartValue: null,

    // String - Colour of the scale line
    scaleLineColor: "rgba(0,0,0,.1)",

    // Number - Pixel width of the scale line
    scaleLineWidth: 1,

    // Boolean - Whether to show labels on the scale
    scaleShowLabels: true,

    // Interpolated JS string - can access value
    scaleLabel: "<%=value%>",

    // Boolean - Whether the scale should stick to integers, not floats even if drawing space is there
    scaleIntegersOnly: true,

    // Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
    scaleBeginAtZero: false,

    // String - Scale label font declaration for the scale label
    scaleFontFamily: "'Open Sans', Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

    // Number - Scale label font size in pixels
    scaleFontSize: 10,

    // String - Scale label font weight style
    scaleFontStyle: "400",

    // String - Scale label font colour
    scaleFontColor: "#979797",

    // Boolean - whether or not the chart should be responsive and resize when the browser does.
    responsive: true,

    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: true,

    // Boolean - Determines whether to draw tooltips on the canvas or not
    showTooltips: true,

    // Function - Determines whether to execute the customTooltips function instead of drawing the built in tooltips (See [Advanced - External Tooltips](#advanced-usage-custom-tooltips))
    customTooltips: false,

    // Array - Array of string names to attach tooltip events
    tooltipEvents: ["mousemove", "touchstart", "touchmove"],

    // String - Tooltip background colour
    tooltipFillColor: "rgba(0,0,0,0.6)",

    // String - Tooltip label font declaration for the scale label
    tooltipFontFamily: "'Open Sans', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

    // Number - Tooltip label font size in pixels
    tooltipFontSize: 11,

    // String - Tooltip font weight style
    tooltipFontStyle: "600",

    // String - Tooltip label font colour
    tooltipFontColor: "#fff",

    // String - Tooltip title font declaration for the scale label
    tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

    // Number - Tooltip title font size in pixels
    tooltipTitleFontSize: 11,

    // String - Tooltip title font weight style
    tooltipTitleFontStyle: "bold",

    // String - Tooltip title font colour
    tooltipTitleFontColor: "#fff",

    // Number - pixel width of padding around tooltip text
    tooltipYPadding: 4,

    // Number - pixel width of padding around tooltip text
    tooltipXPadding: 4,

    // Number - Size of the caret on the tooltip
    tooltipCaretSize: 4,

    // Number - Pixel radius of the tooltip border
    tooltipCornerRadius: 3,

    // Number - Pixel offset from point x to tooltip edge
    tooltipXOffset: 10,

    // String - Template string for single tooltips
    tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",

    // String - Template string for multiple tooltips
    multiTooltipTemplate: "<%= value %>",

    // Function - Will fire on animation progression.
    onAnimationProgress: function(){},

    // Function - Will fire on animation completion.
    onAnimationComplete: function(){}
};









