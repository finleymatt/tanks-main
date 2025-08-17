$(document).ready(function() {
	// registration overview -------------------------------
	var $registration_dialog = $('<div></div>')
		.html("<h1>Step 1</h1>\
			<p>After navigating to the SEP registration page as linked from Onestop login page, you should see three entry fields as shown below.  Please enter your NMED email address and captcha code as shown.</p>\
			<img src='images/sep_overview/sep_overview_register_1.png'>\
			<p>After you submit this form, you should receive a system-generated email.</p><br />\
			<h1>Step 2</h1>\
			<p>When you receive an email from SEP system, it will contain a link to continue your SEP registration.  Launch the link, and you will see the form below:</p>\
			<img src='images/sep_overview/sep_overview_register_2.png'>\
			<p>Complete this form and submit.</p><br />\
			<h1>Step 3</h1>\
			<p>From this screen, select Onestop as the application you would like to have access to.</p>\
			<img src='images/sep_overview/sep_overview_register_3.png'>\
			<p>After this step, the Onestop admin will receive notification about your request and will either approve or decline your Onestop access privilege.</p><br />")
		.dialog({
			autoOpen: false,
			height: 460,
			width: 570,
			title: 'SEP account registration procedure:'
		});

	$('#sep_overview_registration').click(function() {
		$registration_dialog.dialog('open');
		// prevent the default action, e.g., following a link
		return false;
	});

	// login overview ---------------------------------------
	var $login_dialog = $('<div></div>')
		.html("<h1>Step 1</h1>\
			<img src='images/sep_overview/sep_overview_signin_1.png'>\
			<h1>Step 2</h1>\
			<img src='images/sep_overview/sep_overview_signin_2.png'>")
		.dialog({
			autoOpen: false,
			height: 460,
			width: 570,
			title: 'SEP login procedure:'
		});
	$('#sep_overview_login').click(function() {
		$login_dialog.dialog('open');
		// prevent the default action, e.g., following a link
		return false;
	});
});

