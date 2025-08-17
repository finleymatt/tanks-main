<?php
global $GLOBAL_INI;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Onestop Tanks Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="NMED PSTB Onestop Tanks" />
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery-1.6.2.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::fullpath('include/jquery/jquery-ui-1.8.16.custom.min.js') ?>"></script>
<script type="text/javascript" src="<?= url::fullpath('include/jquery/sep_overview.js') ?>"></script>

<link rel="stylesheet" type="text/css" href="<?= url::fullpath("include/css/devreso/{$GLOBAL_INI['instance']['environment']}.css") ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::fullpath('include/css/style.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= url::fullpath('include/jquery/css/redmond/jquery-ui-1.8.16.custom.css') ?>" />
</head>

<body id="outer_page">
<div class="struct-glove">
        <div class="organization" style="height:450px;">

	        <div class="insignia"><img src="<?= url::fullpath('images/nmedlogo3.gif') ?>" width="307" height="93" alt="NM Environment Dept" /></div>
        	<div class="usefulness">Onestop Tanks</div>

		<?= $content ?>
	</div>
</div>

<div class="copyright">
	<center>&copy; 2011 New Mexico Environment Department. All Rights Reserved.</center>
</div>
</body>
</html>
