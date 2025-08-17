<?php
/**
 * Displays Permit/Certificate status bar to be used in AJAX calls
 *
 * Used for Jquery's .load() feature.
 *
 * <b>When using this view, template must be set to tpl_blank</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

if ($latest_status) {
	if ($latest_status['FINISH_TIMESTAMP']) {
		if ($latest_status['COUNT']) // print count
			$last_file = url::fullpath('/download/all_abop_letters.pdf');

		$file_link = (isset($last_file) ? "<a href='{$last_file}'>". basename($last_file) .'</a>' : '<b>no file</b>');
		$batch_message = "({$file_link}) Letter print finished on {$latest_status['FINISH_TIMESTAMP']} ";
		$box_color = 'white';
	}
	else {
		$clear_status_url = url::fullpath('/ab_operator/reset_status');
		$batch_message = "Letter print started on {$latest_status['BEGIN_TIMESTAMP']} (<a href='{$clear_status_url}' onclick=\"return confirm('Are you sure you want to reset batch status? Use this feature only when you know the batch has been running unusually long, possibly due to an error, and you need to run another batch instead.');\">reset status?</a>) ";
		$box_color = '#50AA50';
	}

	echo("<div style='padding:0.7em; margin-bottom:10px; border:1px solid grey; background-color:{$box_color}'><span class='ui-icon ui-icon-info' style='float:left; margin-right:0.3em;'></span><div><span style='font-weight:bold'>A/B Letter batch status</span></div>{$batch_message}</div>");
}
?>
