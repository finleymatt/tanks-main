<?php
global $GLOBAL_INI;
$sam = Sam::instance();
$session = Session::instance();
$nav_id = (isset($nav_id) ? $nav_id : '');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>PSTB Onestop</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="NMED PSTB Onestop" />
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery-1.6.2.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery-ui-1.9.2.custom.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery.validationEngine.js') ?>"></script>
<!-- limits textarea's maxlength for non-HTML5 browsers -->
<script type="text/javascript" src="<?= url::fullpath('include/jquery/textlimiter.js') ?>"></script>
<!-- used by jquery frames to save frame selection state -->
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery.cookie.js') ?>"></script>
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery_onestop_startup.js') ?>"></script>
<!-- datatable js -->
<script type="text/javascript" src="<?= url::fullpath('include/datatable/js/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::fullpath('include/datatable/js/custom_funcs.js') ?>"></script>

<link rel="stylesheet" type="text/css" href="<?= url::fullpath('include/jquery/css/redmond/jquery-ui-1.9.2.custom.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::fullpath('include/datatable/css/demo_table_jui.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::fullpath("include/css/devreso/{$GLOBAL_INI['instance']['environment']}.css") ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::fullpath('include/css/style.css') ?>" />

<script type="text/javascript">
$(function() {
	$(".edit_form").submit(function(event) {
		//alert( "Handler for .submit() called." );
		//event.preventDefault();
		window.close();
	});
});
</script>
</head>

<body id="inner_page">

<?= (($error_message = $session->get_once('error_message')) ? "<div class='ui-state-error ui-corner-all' style='padding:0.7em;'><span class='ui-icon ui-icon-alert' style='float:left; margin-right:0.3em;'></span> {$error_message}</div>" : '') ?>

<?= (($info_message = $session->get_once('info_message')) ? "<div class='ui-state-highlight ui-corner-all' style='padding:0.7em;'><span class='ui-icon ui-icon-info' style='float:left; margin-right:0.3em;'></span> {$info_message}</div>" : '') ?>

<div style="min-height:200px">
<?= $content ?>
</div>

</body>
</html>
