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
<?php
foreach($insurance_rows as $key => $value) {
	$insurance_rows[$key]['stats_active_tanks'] = $stats_active_tanks;
	$insurance_rows[$key]['stats_tos_tanks'] = $stats_tos_tanks;
}
?>
<h1>Owner View</h1>
<table class="horiz_table ui-widget ui-corner-all left_float" style="margin-bottom:30px; margin-right:50px">
	<caption><div class="left_float">Owner</div></caption>
	<?= html::horiz_table_tr('ID', $owner_id) ?>
	<?= html::horiz_table_tr('Person ID', $row['PER_ID']) ?>
	<?= html::horiz_table_tr('Organization ID', $row['ORG_ID']) ?>
	<?= html::horiz_table_tr('Name', $row['OWNER_NAME'], FALSE) ?>
	<?= html::horiz_table_tr('Mailing Address 1', $row['ADDRESS1']) ?>
	<?= html::horiz_table_tr('Address', $row['ADDRESS2']) ?>
	<?= html::horiz_table_tr('City', $row['CITY']) ?>
	<?= html::horiz_table_tr('State', $row['STATE']) ?>
	<?= html::horiz_table_tr('Zip', $row['ZIP']) ?>
	<?= html::horiz_table_tr('Phone', $row['PHONE_NUMBER']) ?>
	<?= html::table_foot_info($row) ?>
</table>

<table class="horiz_table ui-widget ui-corner-all left_float" style="margin-bottom:30px; margin-right:50px">
	<caption><div class="left_float">Facilities Summary</div></caption>
	<?= html::horiz_table_tr('Facilities Count', count($facility_rows)) ?>
	<?= html::horiz_table_tr('<span title="facilities with at least one active tank">Active Facilities Count</span>', $stats_active_facs) ?>
	<?= html::horiz_table_tr('<span title="facilities with no active tanks">Inactive Facilities Count</span>', $stats_inactive_facs) ?>
	<?= html::horiz_table_tr('<span title="facilities with at least one AST">AST Facilities Count</span>', $stats_ast_facs) ?>
	<?= html::horiz_table_tr('<span title="facilities with at least one UST">UST Facilities Count</span>', $stats_ast_facs) ?>
	<?= html::horiz_table_tr('Tanks Count', $stats_total_tanks) ?>
	<?= html::horiz_table_tr('Active Tanks Count', $stats_active_tanks) ?>
	<?= html::horiz_table_tr('<span title="number of Temporary Out of Service Tanks">TOS Tanks Count</span>', $stats_tos_tanks) ?>
</table>

<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px;">
	<caption><div class="left_float">Finances Summary</div></caption>
	<?= html::horiz_table_tr('<span>Owed Balance</span>', display_owed_balance($owner_id, $balance_summary), FALSE) ?>
</table>

<br clear="all" />

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all fade-in">
	<ul>
		<li><a href="#tabs-email">Contacts</a></li>
		<li><a href="#tabs-insurance">Insurance</a></li>
		<li><a href="#tabs-waiver">Waivers</a></li>
		<li><a href="#tabs-transaction">Transactions</a></li>
		<li><a href="#tabs-comment">Comments</a></li>
		<li><a href="#tabs-facility">Facilities</a></li>
		<li><a href="#tabs-tank">Tanks</a></li>
		<li><a href="#tabs-invoice">Invoices</a></li>
	</ul>

	<!-- -------------- Email ------------------ -->

	<div id="tabs-email">
	<?= Controller::_instance('Email')->_add_button(array($owner_id, 'owner'), 'add new Contact') ?>
	<table id="email_tabular" class="display">
		<thead>
			<tr><th>EMAIL</th><th>PHONE</th><th>TITLE</th><th>FULL NAME</th><th>CONTACT TYPE</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>COMMENTS</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($email_rows, 'display_email_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Insurance ------------------ -->
	<div id="tabs-insurance">
	<?= Controller::_instance('Insurance')->_add_button($owner_id, 'add new Insurance') ?>
	<table id="insurance_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>METHOD</th><th>PROVIDER</th><th>POLICY NUMBER</th><th>PER OCCURRENCE AMOUNT</th><th>ANNUAL AGGREGATE AMOUNT</th><th>EFFECTIVE DATE</th><th>EXPIRATION DATE</th><th>EXPIRE IN 30 DAYS</th><th>EXPIRED</th><th># of TANKS COVERED</th><th>Non-Compliance Reminder Letter Send Date</th><th>Owner Response to Letter(Y/N)</th><th>Attachment</th><th>Upload</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($insurance_rows, 'display_insurance_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Waiver ------------------ -->
	<div id="tabs-waiver">
	<?= Controller::_instance('Waiver')->_add_button($owner_id, 'add new Waiver') ?>
	<table id="waiver_tabular" class="display">
		<thead>
			<tr><th>CODE</th><th>FY</th><th>AMOUNT</th><th width="150px">COMMENT</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($waiver_rows, 'display_waiver_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Transaction ------------------ -->
	<div id="tabs-transaction">
	<div style='margin-top:20px; padding-bottom:20px'><?= Controller::_instance('Transaction')->_add_button($owner_id, 'add new Transaction') ?></div>
	<table id="transaction_tabular" class="display">
		<thead>
			<tr><th>FY</th><th>TRX DATE</th><th>TYPE</th><th>AMOUNT</th><th>PAYMENT TYPE</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($transaction_rows, 'display_transaction_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Comment ------------------ -->
	<div id="tabs-comment">
	<?= Controller::_instance('Owner_comments')->_add_button($owner_id, 'add new Comment') ?>
	<table id="comment_tabular" class="display">
		<thead>
			<tr><th>DATE</th><th width="340px">COMMENT</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th width="135">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($comment_rows, 'display_comment_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Facility ------------------ -->
	<div id="tabs-facility">
	<table id="facility_tabular" class="display">
		<thead>
			<tr><th>ID</th><th width="200px">NAME</th><th>IN USE TANKS</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($facility_rows, 'display_facility_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Tank ------------------- -->
	<div id="tabs-tank">
	<table id="tank_tabular" class="display">
		<thead>
			<tr><th>ID</th><th width="200px">OWNER</th><th width="200px">FACILITY</th><th>TYPE</th><th>STATUS</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_rows, 'display_tank_row'); ?>
		</tbody>
	</table>
	</div>


	<!-- -------------- Invoice ------------------ -->
	<div id="tabs-invoice">
	<table id="invoice_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>CODE</th><th>INVOICE DATE</th><th>DUE DATE</th><th>CREATED BY</th><th>CREATED DATE</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($invoice_rows, 'display_invoice_row'); ?>
		</tbody>
	</table>
	</div>

</div> <!-- end of tabs -->

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		email_tabular_obj = $('#email_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[7], "bSearchable": false, "bSortable":false }
			 ]
		});

		insurance_tabular_obj = $('#insurance_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[4], "fnRender":currency_format, "bUseRendered":false },
				{ "aTargets":[5], "fnRender":currency_format, "bUseRendered":false },
				{ "aTargets":[6], "bSearchable": false, "bSortable":false }
			 ]
		});

		waiver_tabular_obj = $('#waiver_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[2], "fnRender":currency_format, "bUseRendered":false },				
				{ "aTargets":[4], "bSortable":false, "bSearchable":false }
			],
			"aaSorting": [[ 1, "desc" ]]
		});

		transaction_tabular_obj = $('#transaction_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[3], "fnRender":currency_format, "bUseRendered":false },
				{ "aTargets":[1], "sType":"oracle_date" },
				{ "aTargets":[5], "bSortable":false, "bSearchable":false }
			],
			"aaSorting": [[ 1, "desc" ]]
		});

		comment_tabular_obj = $('#comment_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[0,2], "sType":"oracle_date" },
				{ "aTargets":[4], "bSortable":false, "bSearchable":false }
			],
			"aaSorting": [[ 0, "desc" ]],
		});

		facility_tabular_obj = $('#facility_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[3], "bSortable":false, "bSearchable":false }
			]
		});

		tank_tabular_obj = $('#tank_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aaSorting": [[ 0, "desc" ]],
		});

		invoice_tabular_obj = $('#invoice_tabular').dataTable({
			"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
			"aoColumnDefs": [
				{ "aTargets":[2,3,4], "sType":"oracle_date" }
			],
			"aaSorting": [[ 0, "desc" ]]
		});

		$('#owed_balance').click(function () {
			if ($('#owed_balance_detail').is(':hidden'))
				$('#owed_balance_detail').slideDown('slow');
			else
				$('#owed_balance_detail').hide();
		});
	});
</script>

<?php

function display_email_row($result, $row) {
	$edit_button = Controller::_instance('Email')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Email')->_delete_button($row['ID']);
	$contact_type = Model::instance('Ref_contact_type')->get_lookup_desc($row['CONTACT_TYPE_ID'], FALSE);
	$email = html::mailto($row['EMAIL']);
	//$user_created_initials = Model::instance('Staff')->get_code($row['USER_CREATED']);

	$result .= "<tr>
		<td>{$email}</td>
		<td>{$row['PHONE']}</td>
		<td>{$row['TITLE']}</td>
		<td>{$row['FULLNAME']}</td>
		<td>{$contact_type}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$row['COMMENTS']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_insurance_row($result, $row) {
	$view_button = Controller::_instance('Insurance')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Insurance')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Insurance')->_delete_button($row['ID']);
	$fin_meth = Model::instance('Financial_methods')->get_financial_method($row['FIN_METH_CODE']);
	$fin_prov = Model::instance('Financial_providers')->get_financial_provider_name($row['FIN_PROV_CODE']);
	$insurance_id = $row['ID'];
	$upload_id = $row['upload_id'];
	$owner_response_flag = (isset($row['OWNER_RESPONSE_FLAG']) && $row['OWNER_RESPONSE_FLAG'] = 'Y') ? 'Y' : 'N';
	$today = date("Y-m-d");
	$thirty_days_later = date("Y-m-d", strtotime('+30 days'));
	$expiration_date = date("Y-m-d", strtotime($row['END_DATE_FMT']));

	// calculate if an insurance expires or will expire in 30 days
	if(!is_null($row['END_DATE'])) {
		$is_expired = ($expiration_date < $today) ? 'Yes' : 'No';
		$is_expired_in_30_days = ($expiration_date >= $today && $expiration_date <= $thirty_days_later) ? 'Yes' : 'No';
	} else {
		$is_expired = '';
		$is_expired_in_30_days = '';
	}

	$number_of_tanks_covered_color = '';
	$number_of_tanks_covered_bold = '';
	// calculate if an insurance covers all the Active and TOS(Temporary Out of Service) tanks, then color code
	if($row['COVERED_TANKS_COUNT'] == $row['stats_active_tanks'] + $row['stats_tos_tanks']) {
		$number_of_tanks_covered_color = 'green';
		$number_of_tanks_covered_bold = 'bold';
	} else if ($row['COVERED_TANKS_COUNT'] < $row['stats_active_tanks'] + $row['stats_tos_tanks']) {
		$number_of_tanks_covered_color = 'red';
		$number_of_tanks_covered_bold = 'bold';
	}

	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$fin_meth}</td>
		<td>{$fin_prov}</td>
		<td>{$row['POLICY_NUMBER']}</td>
		<td class='align-center'>{$row['PER_OCCURRENCE_AMOUNT']}</td>
		<td class='align-center'>{$row['ANNUAL_AGGREGATE_AMOUNT']}</td>		
		<td>{$row['BEGIN_DATE_FMT']}</td>
		<td>{$row['END_DATE_FMT']}</td>
		<td>{$is_expired_in_30_days}</td>
		<td>{$is_expired}</td>
		<td style='color:{$number_of_tanks_covered_color}; font-weight:{$number_of_tanks_covered_bold}'>{$row['COVERED_TANKS_COUNT']}</td>
		<td>{$row['REMINDER_DATE_FMT']}</td>
		<td>{$owner_response_flag}</td>
		<td class='align-center'>";
		if($upload_id !== '') {
			$result .= '<a href="' . $row['file_path'] . '"><i class="far fa-file-pdf fa-2x pdf_icon"></i></a>';
			$result .= '<form action="' . $row['delete_action'] . '" method="post" onsubmit="return confirm(\'Are you sure you want to remove this document?\');">';
			$result .= '<input type="submit" value="Remove" name="remove">';
			$result .= '<input type="hidden" name="form_id" value="' . $insurance_id . '">';
			$result .= '<input type="hidden" name="form_code" value="Insurance">';
			$result .= '<input type="hidden" name="upload_id" value="' . $upload_id. '">';
			$result .= '</form>';
		}
	$result .= "</td>
		<td>
		<form action='" . $row['upload_action'] . "' method='post' enctype='multipart/form-data'>
		<input type='file' name='fileToUpload[]' id='fileToUpload' multiple required>
		<input type='submit' value='Upload' name='submit'>
		<input type='hidden' name='app_topic' value='Insurance'>
		<input type='hidden' id='upload_form_name_" . $insurance_id . "' name='form_name' value='Insurance'>
		<input type='hidden' id='upload_name' name='upload_name' value='InsuranceAttachment'>
		<input type='hidden' name='id' value='" . $insurance_id . "'>
		<input type='hidden' name='upload_id' value='" . $upload_id . "'>
		</form>
		</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_waiver_row($result, $row) {
	// multiple keyed PK: owner_id, fiscal_year, waiver_code
	// edit form must not allow 3 keys to be modified
	$view_button = Controller::_instance('Waiver')->_view_button(array($row['OWNER_ID'], $row['FISCAL_YEAR'], $row['WAIVER_CODE']));
	$edit_button = Controller::_instance('Waiver')->_edit_button(array($row['OWNER_ID'], $row['FISCAL_YEAR'], $row['WAIVER_CODE']));
	$delete_button = Controller::_instance('Waiver')->_delete_button(array($row['OWNER_ID'], $row['FISCAL_YEAR'], $row['WAIVER_CODE']));
	$waiver = Model::instance('Ust_ref_codes')->get_lookup_desc_by_domain('OWNER_WAIVERS.WAIVER_CODE', $row['WAIVER_CODE']);

	$result .= "<tr>
		<td>{$waiver}</td>
		<td>{$row['FISCAL_YEAR']}</td>
		<td>{$row['AMOUNT']}</td>
		<td>{$row['WAIVER_COMMENT']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_transaction_row($result, $row) {
	$view_button = Controller::_instance('Transaction')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Transaction')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Transaction')->_delete_button($row['ID']);
	$trx_type = Model::instance('Transaction_codes')->get_lookup_desc($row['TRANSACTION_CODE']);
	$payment_type = Model::instance('Ref_transaction_payment_types')->get_lookup_desc($row['PAYMENT_TYPE_CODE']);

	$result .= "<tr>
		<td>{$row['FISCAL_YEAR']}</td>
		<td>{$row['TRANSACTION_DATE']}</td>
		<td>{$trx_type}</td>
		<td>{$row['AMOUNT']}</td>
		<td>{$payment_type}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_comment_row($result, $row) {
	$view_button = Controller::_instance('Owner_comments')->_view_button(array($row['OWNER_ID'], $row['ID']));
	$edit_button = Controller::_instance('Owner_comments')->_edit_button(array($row['ID']));
	$delete_button = Controller::_instance('Owner_comments')->_delete_button(array($row['OWNER_ID'], $row['ID']));
	$result .= "<tr>
		<td>{$row['COMMENT_DATE']}</td>
		<td>{$row['COMMENTS']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_facility_row($result, $row) {
	$tank_rows = Model::instance('Tanks')->get_list('FACILITY_ID=:FACILITY_ID and TANK_STATUS_CODE IN (1,2)', NULL, array(':FACILITY_ID' => $row['ID'])); // 1=in use, 2=TOS
	$tank_count = count($tank_rows);
	$view_button = Controller::_instance('Facility')->_view_button($row['ID']);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['FACILITY_NAME']}</td>
		<td>{$tank_count}</td>
		<td>{$view_button}</td>
		</tr>";
	return($result);
}

function display_tank_row($result, $row) {
	$status = Model::instance('Tank_status_codes')->get_lookup_desc($row['TANK_STATUS_CODE'], FALSE);
	$owner_link = html::owner_link($row['OWNER_ID']);
	$facility_link = html::facility_link($row['FACILITY_ID']);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$owner_link}</td>
		<td>{$facility_link}</td>
		<td>{$row['TANK_TYPE']}</td>
		<td>{$status}</td>";
	return($result);
}

function display_invoice_row($result, $row) {
	$view_button = Controller::_instance('Invoice')->_view_button($row['ID']);
	$delete_button = Controller::_instance('Invoice')->_delete_button($row['ID']);
	$print_button = Controller::_instance('Invoice')->_print_button($row['ID']);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['INVOICE_CODE']}</td>
		<td>{$row['INVOICE_DATE']}</td>
		<td>{$row['DUE_DATE']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$delete_button} {$print_button}</td>
		</tr>";
	return($result);
}

function display_owed_balance($owner_id, $balance_summary) {
	// return quick balance only as Antonette requested
	return(format::currency($balance_summary['QUICK_BALANCE']));

	if (isset($balance_summary['BALANCE'])) {
		$balance_total = $balance_summary['BALANCE'] - array_sum(array_column($balance_summary['PAYMENTS'], 'AMOUNT'));

		$html = "<div id='owed_balance'>" . format::currency($balance_total) . "</div>";
		$html .= "<div id='owed_balance_detail' style='display:none;'>(<a href='" . Controller::_instance('Invoice')->_view_url($balance_summary['INVOICE_ID']) . "'>invoice</a>)";

		if ($balance_summary['INVOICE_ID'] != $balance_summary['GEN_INVOICE_ID'])
			$html .= "<div class='ui-state-highlight ui-corner-all' style='padding:0.5em; margin-top:6px' title='Amount shown may not be accurate because past FY invoice(s) have been generated since this amount was determined. To find accurate amount, generate invoices for the subsequent FYs up to the current FY.'><span class='ui-icon ui-icon-alert' style='float:left; margin-right:0.3em;'></span> may not be accurate</div>";

		// show payments since last invoice generation
		if (count($balance_summary['PAYMENTS']))
			$html .= "<hr style='border:1px solid black' />Invoice Amount: " . format::currency($balance_summary['BALANCE']);
		foreach($balance_summary['PAYMENTS'] as $payment)
			$html .= "<br />- " . format::currency($payment['AMOUNT']) . " ({$payment['TRANSACTION_CODE']} on {$payment['TRANSACTION_DATE']}<span title='If this payment was late and late fee should be assessed, they are not reflected here.'>*</span>)";
	}
	else {
		$html = "<div id='owed_balance'>" . format::currency($balance_summary['QUICK_BALANCE']) . "</div>
			<div id='owed_balance_detail' style='display:none;'>Amount shown may not be accurate because current FY invoice does not exist</div>";
	}

	$html .= "</div>";

	return($html);
}
?>
