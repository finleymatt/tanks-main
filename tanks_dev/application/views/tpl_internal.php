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
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?= url::external_file_path('include/jquery/jquery-1.6.2.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::external_file_path('include/jquery/jquery-ui-1.9.2.custom.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::external_file_path('include/jquery/jquery.validationEngine.js') ?>"></script>

<!-- limits textarea's maxlength for non-HTML5 browsers -->
<script type="text/javascript" src="<?= url::external_file_path('include/jquery/textlimiter.js') ?>"></script>
<!-- used by jquery frames to save frame selection state -->
<script type="text/javascript" src="<?= url::external_file_path('include/jquery/jquery.cookie.js') ?>"></script>
<script type="text/javascript" src="<?= url::external_file_path('include/jquery/jquery_onestop_startup.js') ?>"></script>
<!-- datatable js -->
<script type="text/javascript" src="<?= url::external_file_path('include/datatable/js/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::external_file_path('include/datatable/js/custom_funcs.js') ?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<link rel="stylesheet" type="text/css" href="<?= url::external_file_path('include/jquery/css/redmond/jquery-ui-1.9.2.custom.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::external_file_path('include/datatable/css/demo_table_jui.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::external_file_path("include/css/devreso/{$GLOBAL_INI['instance']['environment']}.css") ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::external_file_path('include/css/style.css') ?>" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

<script type="text/javascript">
window.name = 'main';

$(window).load(function() {
	$('.fade-out').fadeOut('slow');
	$('.fade-in').fadeIn(2000);
});
</script>
</head>

<body id="inner_page">

<div id="inner_header"><!-- start of top header -->

<div style="float:left; font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif; font-size:17px;">
	<a href="<?= url::fullpath('/') ?>"><img src="<?= url::fullpath('images/nmed_logo_small.png') ?>" style="vertical-align:top" alt="NM Environment Dept"></a> PSTB Onestop Tanks
</div>
<div style="float:right">
	<div>
		Logged in as <?= $session->get('SEPuserID') ?>.
		<a href="<?= url::fullpath('/login/logout') ?>">Logout</a>
	</div>
	<div style="margin-top:5px">
		Recently viewed:
		<ul><?php
		$history = $session->get('history', array());
		foreach ($history as $obj_info) {
			$url = url::fullpath("/{$obj_info['type']}/view/{$obj_info['id']}");
			$short_name = text::limit_chars($obj_info['name'], 15, '.');
			echo "<li><a href='{$url}'>({$obj_info['id']}) {$short_name} [{$obj_info['type']}]</a></li>";
		}
		?></ul>
	</div>
</div>

<?php
// site navigation menu -----------------------------------------------
// flattens heirarchy to only 2 for correct display
//$sessionId = $this->input->get('sessionId');
$nav_menu = array(
	//'home' => array('title' => 'Main Menu', 'url' => url::fullpath('/')),
	'tex' => array('title' => 'Tex', 'url' => '#', 'children' => array(
		'owner_search' => array('title' => 'Owner Search', 'url' => url::fullpath('/tex-ui/organizations/search_org/')),
		'new_owner' => array('title' => 'New Owner', 'url' => url::fullpath('/tex-ui/organizations/organization_details/')),
		'facility_search' => array('title' => 'Facility Search', 'url' => url::fullpath('/tex-ui/')),
		'new_facility' => array('title' => 'New Facility', 'url' => url::fullpath('/tex-ui/facility/facility_info/'))
	)),
	'owner' => array('title' => 'Owner', 'url' => url::fullpath('/owner/'), 'children' => array(
		'owner_comment' => array('hidden' => TRUE)
	)),
	'operator' => array('title' => 'Operator', 'url' => url::fullpath('/operator/')),
	'facility' => array('title' => 'Facility', 'url' => url::fullpath('/facility/'), 'children' => array(
		'facility_history' => array('hidden' => TRUE),
		'inspection' => array('hidden' => TRUE),
		'penalty' => array('hidden' => TRUE),
		'permit' => array('hidden' => TRUE),
		'tank' => array('hidden' => TRUE),
		'tank_detail' => array('hidden' => TRUE)
	)),
	'reports' => array('title' => 'Reports', 'url' => url::fullpath('/reports/')),
	'violation' => array('title' => 'Violation', 'url' => url::fullpath('/violation/')), 
	//'FCO' => array('title' => 'FCO', 'url' => url::fullpath('/fco/')),
);
$admin_menu = array(
	'fees' => array('title' => 'Fees &amp; Letters', 'url' => '#', 'children' => array(
		'invoice' => array('title' => 'Tank Owner Invoice', 'url' => url::fullpath('/invoice/')),
		'notice' => array('title' => 'Tank Operator Notice', 'url' => url::fullpath('/notice/')),
		// not tracked in onestop anymore -- 'nov_invoice' => array('title' => 'NOV Invoice', 'url' => '#'),
		'payment' => array('title' => 'Tank Owner Payment', 'url' => url::fullpath('/transaction/payment_add/')),
		'gpa_invoice' => array('title' => 'GPA Invoice', 'url' => url::fullpath('/invoice/gpa_menu')),
		'certificate' => array('title' => 'Registration Certificate', 'url' => url::fullpath('/permit/')),
		'abop_letters' => array('title' => 'A/B Op Letters', 'url' => url::fullpath('/ab_operator/'))
	)),
	'admin' => array('title' => 'Admin', 'url' => '#', 'children' => array(
		//'lookup' => array('title' => 'Change Lookups', 'url' => url::fullpath('#')),
		'invoice_codes' => array('title' => 'Manage Invoice Text', 'url' => url::fullpath('/invoice_codes/')),
		'penalty_codes' => array('title' => 'Penalty Codes', 'url' => url::fullpath('/penalty_codes/')),
		'users' => array('title' => 'User Admin', 'url' => url::fullpath('/users/'))
	))
);

if (Sam::instance()->has_priv('USTX.INVOICES', 'INSERT')) 
	$nav_menu = array_merge($nav_menu, $admin_menu);

   ?>

 

<div style="float:left; margin-top:40px; margin-left:30px;">
	<?= html::nav_menu($nav_menu, $nav_id) ?>
</div>

</div><!-- end of top header bar -->
<br clear=all />

<!-- display owner or facility info here -->

<? if (!isset($skip_bread_crumbs) || (! $skip_bread_crumbs)): ?>
	<div style="margin-bottom:10px"><?= html::breadcrumbs($this->uri->argument_array()) ?></div>
<? endif; ?>

<?= (($error_message = $session->get_once('error_message')) ? "<div class='ui-state-error ui-corner-all' style='padding:0.7em;'><span class='ui-icon ui-icon-alert' style='float:left; margin-right:0.3em;'></span> {$error_message}</div>" : '') ?>

<?= (($info_message = $session->get_once('info_message')) ? "<div class='ui-state-highlight ui-corner-all' style='padding:0.7em; margin-bottom:15px'><span class='ui-icon ui-icon-info' style='float:left; margin-right:0.3em;'></span> {$info_message}</div>" : '') ?>

<div style="min-height:350px">
<?= $content ?>
</div>

<br clear="all" />
<div class="copyright">
	<center>&copy; 2012 New Mexico Environment Department. All Rights Reserved.</center>
</div>

</body>
</html>
