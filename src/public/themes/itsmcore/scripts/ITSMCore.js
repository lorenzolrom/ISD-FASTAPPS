/**
* Open a popup window
*/
function ITSMCore_openSmallWindow(href)
{
	window.open(href, "_blank", "scrollbars-yes, menubar=no, height=480, width=500, resizable=no, toolbar=no, status=no");
}

/**
* Add listeners to popup links
*/
function ITSMCore_addPopupListeners()
{
	$('.itsmcore-small-popup-link').unbind('click');
	
	$('.itsmcore-small-popup-link').click(function(e){
		e.preventDefault();
		ITSMCore_openSmallWindow($(this).attr('href'));
	});
	
	$('.itsmcore-small-popup-link-td').find('a').unbind('click');
	$('.itsmcore-small-popup-link-td').find('a').click(function(e){
		e.preventDefault();
		ITSMCore_openSmallWindow($(this).attr('href'));
	});
	$(".results-button").click(function(){ITSMCore_addPopupListeners()});
}

// Add listeners
$(document).ready(function(){
	ITSMCore_addPopupListeners();
});