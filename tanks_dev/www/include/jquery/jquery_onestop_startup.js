///////////////////////////////////////////////
// start up jquery JS for all CAF needs
///////////////////////////////////////////////

$(function() {
	// sets up datepicker control for all elements set to "datepicker" class
	$(".datepicker").datepicker( { changeYear: true, yearRange: '1997:2018' } );

	// sets up form validation 
	$(".validate_form").validationEngine({validationEventTrigger: 'submit'});

	// hover effect for buttons
	$('.ui-state-default').hover(
		function(){ $(this).addClass('ui-state-hover'); }, 
		function(){ $(this).removeClass('ui-state-hover'); }
	);
	$('.ui-state-default').click(function(){
		$(this).toggleClass('ui-state-active');
	});

	// apply jquery ui tabs for all elements with class, ui-tabs
	$(".ui-tabs").tabs({
		cookie: { expires: 1 }  // save tab state for 1 day
	});

	// apply jquery tooltip
	$(document).tooltip();

	// drop-down navigation menu in tpl_internal.php
	$('.nav li').hover(
		function () {
			//show its submenu
			$('ul', this).stop(true, true).slideDown(100);

		}, 
		function () {
			//hide its submenu
			$('ul', this).stop(true, true).slideUp(100);			
		}
	);

	// apply popup feature for links with popup class
	$('.popup').click(function(event) {
		event.preventDefault();
		window.open($(this).find("a").attr("href"), "popupWindow", "width=560,height=300,scrollbars=yes");
	});
});

// not a jquery function but here to reduce includes
function print_div(div_id) {
	// not using jquery here because outerHTML equivalent doesnt exist
	var div = document.getElementById(div_id);
	var popupWin = window.open('', '_blank', 'width=480,height=400');
	popupWin.document.open();
	popupWin.document.write('<html><head><title>Onestop Tanks</title><style>.no_print {display:none}</style></head><body onload="window.print()">' + div.outerHTML + '</html>');
	popupWin.document.close();
}

