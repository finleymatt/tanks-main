<?php
/**
 * file docblock; short description
 *
 * <p>long description</p>
 *
 * <b>IMPORTANT NOTE</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<style> 
	P {font-size:14px; margin-left:10px; margin-top:13px; margin-bottom:13px;}
	.sep_link {font-size:18px; padding-left:45px}
</style>
 
	<div class="main-navigation">Login</div>

	<?= (!empty($message) ? "<p class='alert'>{$message}</p>" : '') ?>
 
	<p style="margin-top:20px">Onestop Tanks uses SEP authentication. Please login into SEP. From there, you can select Onestop as the application to be redirected to.</p>
	<div class="sep_link"><a href="<?= $sep_login_url ?>">SEP Login</a></div>
 
	<p>If you don't have a SEP account, please navigate to:</p>
	<div class="sep_link"><a href="<?= $sep_register_url ?>">SEP Registration</a></div>
	<p>and register for an account.  During registration, be sure to select Onestop as an application to request access to.</p>
 
	<div style="margin:40px;">
		<ul>
		<li><a id="sep_overview_registration" class="pointer">SEP Registration Overview</a></li>
		<li><a id="sep_overview_login" class="pointer">SEP Login Overview</a></li>
		</ul>
	</div>
 
