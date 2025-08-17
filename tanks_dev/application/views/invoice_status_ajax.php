<?php
/**
 * Displays Invoice status bar to be used in AJAX calls
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

$reset_link = "(<a href='". url::fullpath('/invoice/reset_status') . "' onclick=\"return confirm('Are you sure you want to reset batch status? Use this feature only when you know the batch has been 
running unusually long, possibly due to an error, and you need to run another batch 
instead.');\">reset status?</a>)";

// if last was batch generation ----------------------------------------------
if ($latest_status['type'] == 'gen') {
	if ($latest_status['row']['FINISH_TIMESTAMP']) {
		$batch_message = "Invoice generation finished successully on 
{$latest_status['row']['FINISH_TIMESTAMP']} ";
	}
	else {
		$batch_message = "Invoice generation started on {$latest_status['row']['BEGIN_TIMESTAMP']} 
{$reset_link} ";
	}
}

// if last status was batch print --------------------------------------------
elseif ($latest_status['type'] == 'print') {
	if ($latest_status['row']['FINISH_TIMESTAMP']) {
		if ($latest_status['row']['COUNT']) { // invoice count
			$file_name = '/download/' . url::invoice_filename($latest_status['row']['OWNER_ID'], $latest_status['row']['INVOICE_DATE']) ;
			$last_invoice_file = url::fullpath($file_name);
		}
		else
			$last_invoice_file = NULL;

		if ($last_invoice_file) {
			$file_link = "<a href='{$last_invoice_file}'>". basename($last_invoice_file) .'</a>';
			// if multiple invoices, display zip link too
			if ($latest_status['row']['COUNT'] > 1) {
				$zip_file = url::fullpath('/download/invoices.zip');
				$file_link .= " | <a href='{$zip_file}'>". basename($zip_file) .'</a>';
			}
		}
		else
			$file_link = '<b>no file - invoice had no data to print</b>';

		$batch_message = "({$file_link}) Invoice print finished successully on 
{$latest_status['row']['FINISH_TIMESTAMP']} ";
	}
	else {
		$batch_message = "Invoice print started on {$latest_status['row']['BEGIN_TIMESTAMP']} 
{$reset_link} ";
	}
}

if (isset($batch_message)) {
	$batch_message .= "with criterias:<br />
		Owner ID(". ($latest_status['row']['OWNER_ID'] ? $latest_status['row']['OWNER_ID'] : 'all owners') 
."),
		Invoice Date({$latest_status['row']['INVOICE_DATE']}),
		Due Date({$latest_status['row']['DUE_DATE']}),
		FY({$latest_status['row']['FY']})";
	echo("<div style='padding:0.7em; margin-bottom:10px; border:1px solid grey; background-color:". 
($latest_status['row']['FINISH_TIMESTAMP'] ? 'white' : '#50AA50') . "'><span class='ui-icon ui-icon-info' 
style='float:left; margin-right:0.3em;'></span><div><span style='font-weight:bold'>Invoice 
operation status</span></div>{$batch_message}</div>");
}

?>
