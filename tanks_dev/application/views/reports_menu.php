<?php
/**
 * Report Menu
 *
 * This report menu displays all menus in one html page using html_report_form function.
 *
 * @package Onestop
 * @subpackage views
 *
*/
?>
<h1>Reports</h1>

<style>
	FORM { float:left; margin:10px; }
	.report_group { margin-left:auto; margin-right:auto; height:27px; width:100%; clear:both; background-color:#DDDDDD; text-align:center; margin-top:35px; padding-top:6px; font-size:18px; font-weight:bold; color:#775533; }
	.accordion { width: 1050px; }
	.report_desc { float:left; width:370px; margin:15px; clear:right }
	.report_desc UL { margin-left:30px; }
	.report_desc BR { margin-bottom:10px }
</style>
<script>
	$(function() {
		$( ".accordion" ).accordion({
			autoHeight: false,
			collapsible: true,
			active: false
		});
	});
	alert(date[2]);
</script>

<?php
// Inspection Reports =========================================================
echo('<div class="report_group ui-corner-all">Inspection Reports</div>');
echo('<div class="accordion fade-in">');
echo html_report_head('Facility Summary', array(
	html_report_form('/reports/facility_summary', array('facility_id'))),
	'Used by Inspectors');

echo html_report_head('Field Inspection', array(
	html_report_form('/reports/field_inspection', array('facility_id',
		'output_format' => array('label' => 'Output Type', 'html' => '
			<input name="output_format" type="Radio" checked="checked" value="excel" />Excel')))),
	'Form filled out by inspectors during their inspection of a facility. Inspectors are expected to Update or enter new data directly in this Excel file.');

echo html_report_head('Thirty Months No Compliance', array(
	html_report_form('/reports/thirty_month_no_comp', array('before_date', 'county_all', 'tank_type'))),
	'This report lists facilities that have not been inspected for more than 914 days.<br />Report output includes facility information, tank count, assigned inspector, and date inspected.<br />Only facilities with the last date of inspection older than 914 days are shown in this report.<br />Parameters:<br /><ul><li>Prior to Date - Only the facilities with their <b>last inspection</b> performed before this date will be included.</li><li>County - Results will be limited to the county selected.</li></ul>');

echo html_report_head('Eighteen Months No Compliance', array(
	html_report_form('/reports/eighteen_month_no_comp', array('before_date', 'county_all'))),
	'This report lists UST facilities that have not been inspected for more than 548 days.<br />Report output includes facility information, tank count, assigned inspector, and date inspected.<br />Only facilities with the last date of inspection older than 548 days are shown in this report.<br />Parameters:<br /><ul><li>Prior to Date - Only the facilities with their <b>last inspection</b> performed before this date will be included.</li><li>County - Results will be limited to the county selected.</li></ul>');

echo html_report_head('Inspections Review', array(
	html_report_form('/reports/inspections_review', array('start_date', 'end_date', 'inspector_id', 'tank_type'))),
	'This report shows all inspections performed for the dates you selected.');


echo html_report_head('Tank Inspection Dates (Facilities Not Inspected in Last 12 Months)', array(
	html_report_form('/reports/tank_inspection_dates', array('before_date', 'county_all', 'tank_type'))),
	'Previous name: Facilities Not Inspected in Last 12 Months.<br />This report is used for monitoring inspection dates and for maintaining the 3 year inspection frequency requirement.<br />Report output includes facility information, tank count, assigned inspector, and date inspected.<br />Parameters:<br /><ul><li>Prior to Date - Only the facilities with their <b>last inspection</b> performed before this date will be included.</li><li>County - Results will be limited to the county selected.</li></ul>');

echo html_report_head('Last Two Compliance Inspections (EPA report)', array(
	html_report_form('/reports/tank_inspection_2_dates', array('start_date', 'end_date', 'tank_type'))),
	'Report for EPA.');

echo html_report_head('All Storage Tanks Status By County', array(
	html_report_form('/reports/all_storage_tanks', array('county_all',
		'get_active_only' => array('label' => 'Active Tanks Only?', 'html' => '
			<input name="get_active_only" type="checkbox" />')))),
	'This report provides a history of storage tanks registered in the Bureau and is planned to be posted on the web. This report consists of a large amount of data so it is categorized on a county basis.<br />Report output includes facility and owner information, tank type and tank status (in use, removed, exempt, TOS).<br />Posted on the web at 6 month intervals.<br />Selecting &quot;Active Tanks Only?&quot; limits results to facilities with at least one tank in In Use, TOS, or Exempt status.');

echo html_report_head('Facilities with Active Tanks by County', array(
	html_report_form('/reports/active_storage_tanks')),
	'This report is posted on the web at 6 month intervals and consists of active storage tanks in use.<br />Displays county, AST count, UST count, and facility address.');

echo html_report_head('Active Facility LUST Compliance', array(
	html_report_form('/reports/lust_compliance', array('before_date'))),
	'This report tracks active LUST sites. Report includes owner name, facility, release information (identifies RP), last inspection date, and inspector name.<br />Tom Gray and Kal Martin reviews this report to compare if Owner Name is the same as Responsible Party.  If facility is out of compliance, it could potentially affect compliance determination if applicable.');


echo html_report_head('Tanks', array(
        html_report_form('/reports/all_tanks_details',  array('tank_type'))),
	        'Report of all the tanks with tank install date, removal date, contents, whether it is federally regulated, storage capacity, etc');

echo html_report_head('Tank Fee Compliance', array(
	html_report_form('/reports/tank_fee_compliance', array('facility_id'))),
	'Report shows tank fee history on a facility basis. Report shows history of tank fee invoice information. Used by Tank fee program.');

echo html_report_head('Tank and Owner Statistics', array(
	html_report_form('/reports/tank_owner_stat', array('start_date', 'end_date'))),
	'Report of owners with tanks, active facilities with tanks, # of ASTs, # of USTs, delinquent owners, delinquent fee balance, tanks removed in a requested time frame.<br />Used by tank fee program.');

echo html_report_head('Tanks Installed', array(
	html_report_form('/reports/tanks_installed', array('fy'))),
	'Report of tanks installed for a specified FY. Data shows associated owner and facility of each tank.');

echo html_report_head('Tanks Removed', array(
	html_report_form('/reports/tanks_removed', array('fy'))),
	'Report of tanks removed for a specified FY. Data shows associated owner and facility of each tank.');

echo html_report_head('Facilities in NOV Violation - Delivery Prohibition', array(
	html_report_form('/reports/nov_report', array('start_date', 'end_date', 'tank_type'))),
	'Report of outstanding NOVs issued for SOC violations for levels <b>A and B only</b> that potentially identify facilities as ineligible for delivery.<br />Report includes penalty citation, SOC category, facility, owner name, date of inspection, and tank type.<br />Corrected violations are not included.');

echo html_report_head('Facilities in NOV Violation - All Violations', array(
	html_report_form('/reports/nov_report_all', array('start_date', 'end_date', 'tank_type'))),
	'Same report as above with listing of all violations for levels A B and C.<br />Corrected violations are not included.');

echo html_report_head('Facilities in NOV Violation - Financial Violations', array(
	html_report_form('/reports/nov_report_financial', array('start_date', 'end_date', 'tank_type'))),
	'Same report as above with listing of all violations that contains key word "Financial" for levels A B and C.<br />Corrected violations are not included.');

echo html_report_head('SOC Performance', array(
	html_report_form('/reports/soc_performance', array('start_date', 'end_date'))));

echo html_report_head('SOC Compliance Statistics', array(
	html_report_form('/reports/soc_compliance_stat', array('start_date', 'end_date'))),
	'Provides count and percentages of facilities in SOC RP and RD compliance. These numbers are used for EPA&#39;s UST4, UST5, and UST6 data requests.<br />(Not yet received approval from Kal about the report outputs.)');

echo html_report_head('TCR Compliance Statistics', array(
	html_report_form('/reports/tcr_compliance_stat', array('start_date', 'end_date'))),
	'Provides count and percentages of facilities in TCR compliance.');

echo html_report_head('Non-Fuel Tanks', array(
	html_report_form('/reports/nonfuel_tanks')),
	'List of all lube oil and non-fuel tanks.  Tanks with no Content code are also included.');

// report finished but not yet reviewed by Bertha who asked for it ------
echo html_report_head('Facilities with Emergency Generator Tanks', array(
	html_report_form('/reports/facilities_eg_tanks')),
	'List of all facilities with at least one emergency generator tank.<br />The EG tank must be either in "CURRENTLY IN USE" or "NO DATA" status.<br />This report was requested by Bertha on 5/9/2013.');

echo html_report_head('Facilities Count', array(
	html_report_form('/reports/facilities_count', array('tank_type', array('label'=>'TOS only?', 'html'=>'<input type="checkbox" name="tos_only" />')))
	),
	'Lists and counts all facilities that have at least one tank with status:
	<ul><li>CURRENTLY IN USE</li><li>TEMPORARILY OUT OF USE</li><li>NO DATA</li><li>EXEMPT</li></ul>
	This report was requested by Dana on 11/1/2013.');

echo html_report_head('Facilities with A/B/C Operators', array(
	html_report_form('/reports/facilities_abc_op', array(
		'cert_level' => array('label' => 'Certificate Level', 'html' => '
			<input name="cert_level" type="radio" value="A/B" checked="checked" />A/B
			<input name="cert_level" type="radio" value="C" />C
			<input name="cert_level" type="radio" value="Both" />Both
		')))),
	'List of all facilities and designated A/B/C Operators if information exist.');

echo html_report_head('DP Statistics', array(
        html_report_form('/reports/dp_stat', array('start_date', 'end_date', 'tank_type'))),
	'Report of delivery prohibition, the number of NOVs, NODs, NOIRTs, NRTPIDs, COCs and LCAVs in a requested time frame.');

echo html_report_head('DP Master', array(
	html_report_form('/reports/dp_master', array('start_date', 'end_date'))),
	'Report of facilities with violations in a requested time frame.');

echo html_report_head('Violations Per Inspector', array(
	html_report_form('/reports/inspector_report', array('inspector_id', 'start_date', 'end_date'))),
	'Report for inspectors in a requested time frame.');

echo html_report_head('Emails', array(
	html_report_form('/reports/emails_review', array('contact_type', 'entity_type'))),
	'Report for facility and owner emails.');

echo html_report_head('Insurance Review', array(
	html_report_form('/reports/insurance_review')),
	'Report for latest insurance of facilities and facilities withouth insurances.');

echo html_report_head('Suspected Release', array(
	html_report_form('/reports/suspected_release', array('release_status', 'start_date', 'end_date'))),
	'Report for suspected relase for a requested time frame.');

$tank_detail_codes = array_keys(Model::instance('Tank_detail_codes')->get_dropdown());
// make tank detail codes both key and value for the array using array_combine function
$tank_detail_codes_dropdown = array_combine($tank_detail_codes, $tank_detail_codes);
echo html_report_head('Facility and Tank Detail', array(
	html_report_form('/reports/facility_tank_detail', array(
	array('label' => 'Tank Detail Code',
	'html' => form::dropdown(array('name' => 'tank_detail_codes[]', 'multiple' => 'multiple', 'size' => 15), $tank_detail_codes_dropdown, 'none', 'class="validate[required]"') . ' use CTRL to select multiple codes')))),
	'Report for facility and tank details.');

echo html_report_head('Inspections by Certified Installer', array(
	html_report_form('/reports/inspections_by_certified_installer', array('certified_installer_id', 'start_date', 'end_date'))),
	'Report for inspections by certified installer for a requested time frame.');

echo html_report_head('Quarterly Performance Measures', array(
	html_report_form('/reports/quarterly_performance_measures', array('year', 'quarter'))),
	'Report to get the quarterly performance measures for the Prevention Inspection Program');


echo('</div><!-- end of accordion -->');

// Financial Reports ==========================================================
echo('<div class="report_group ui-corner-all">Financial Reports</div>');
echo('<div class="accordion fade-in">');
/***** not used and has error: Undefined offset: 0
echo html_report_head('Pre-Invoice Tank Counts', array(
	html_report_form('/reports/preinvoice_tank_counts', array('owner_id', 'fy'))),
	'Displays tank counts that will be used in generating invoice. Use this report when you encounter unexpected invoice fee calculations. The cause may be tank count miscalculation.');
******/

echo html_report_head('Accounts Aging', array(
	html_report_form('/reports/accounts_aging', array(array('label' => 'Days after invoice', 'html' => "<input type='text' name='days' id='days' size='5' class='validate[required,custom[integer]]'>"),
		'output_format' => array('label' => 'Output Type', 'html' => '
			<input name="output_format" type="Radio" value="html" checked="checked" />HTML
			<input name="output_format" type="Radio" value="excel" />Excel')))),
	'Report lists owners who have not paid for the specified number of days after their last invoice was generated.');

echo html_report_head('Outstanding Liabilities for Active Tanks', array(
	html_report_form('/reports/outstanding_liab_active', array('fy', 'include_prior_years'))),
	'Owners with tank fees not yet paid. Only retrieves owners that has an active tank in the unpaid invoice.');

echo html_report_head('Outstanding Liabilities for Inactive Tanks', array(
	html_report_form('/reports/outstanding_liab_inactive', array('fy', 'include_prior_years'))),
	'Owners with tank fees not yet paid. Only retrieves owners that has no active tanks in the unpaid invoice.');

echo html_report_head('Delinquent Owners', array(
	html_report_form('/reports/delinquent_owners', array('fy', 'include_prior_years'))),
	'');

echo html_report_head('Delinquent Owners -- Excluding Federal', array(
	html_report_form('/reports/delinquent_owners_no_fed')),
	'');

/***** not completed ****
echo  html_report_head('Current FY Tank Fees', array(
	html_report_form('/reports/current_fy_tank_fees', array('start_date', 'fy'))),
	'Report to get list of owners with balances owed for only the current FY.  Used in Sept.');
*************************/

echo html_report_head('Fee Summary', array(
	html_report_form('reports/fee_summary', array(
		array('label' => 'Trx Code', 'html' => form::dropdown('transaction_code', array_merge(Model::instance('Transaction_codes')->get_dropdown(), array('UST'=>'UST')), NULL, 'class="validate[required]" style="font-size:10px"')),
	                'start_date', 'end_date'), 'Default'),
	html_report_form('reports/fee_summary_fy', array(
                array('label' => 'Trx Code', 'html' => form::dropdown('transaction_code', array_merge(Model::instance('Transaction_codes')->get_dropdown(), array('UST'=>'UST')), NULL, 'class="validate[required]" style="font-size:10px"')),
			'start_date', 'end_date'), 'Group by FY')),
	'Previously named, "Daily/Monthly Fee Summary Report."<br />Transaction Codes:<ul>
	<li>GWAP = Ground Water Protection Act Fees</li>
	<li>HWEP = Hazardous Waste Emergency(NOV)
	<li>UST = Includes PP, IP, and LP transactions</li></ul>');

$fy_arr = Model::instance('Fiscal_years')->get_dropdown(); $fy_arr[''] = 'All FYs';
echo html_report_head('Owner Tank Fee Transaction History', array(
	html_report_form('/reports/owner_tank_fee_history', array('owner_id', 
		array('label' => 'FY', 'html' => form::dropdown('fy', $fy_arr))
	))),
	'Shows Owner\'s transactions for the FY(s) selected.');

echo html_report_head('Owner Balance and Tanks', array(
	html_report_form('/reports/owner_balance_tanks', array(
		array('label' => 'Tank Status',
			'html' => form::dropdown(array('name' => 'tank_status_codes[]', 'multiple' => 'multiple', 'size' => 7), Model::instance('Tank_status_codes')->get_dropdown(), 'none', 'class="validate[required]"') . ' CTRL selects multiple'),
		array('label' => 'Balance Type',
			'html' => form::dropdown('balance_type', array('all'=>'All', 'credit'=>'Credit', 'debit'=>'Debit', 'non-zero'=>'Non-zero')))
	))),
	'Displays fee balances for owners with tanks.');

echo html_report_head('Tank Fee Billing Exceptions', array(
	html_report_form('/reports/tank_fee_billing_exceptions', array(
		array('label' => 'Invoice Date', 'html' => "<input type='text' name='invoice_date' id='invoice_date' size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy")
	))),
	'Displays info in two parts:
		<li>-Facilities with Tanks Installed Midyear</li>
		<li>-Owners Making Partial Payments</li>');

echo('</div><!-- end of accordion -->');

// GoNM Reports ===============================================================
echo('<div class="report_group ui-corner-all">GoNM Reports</div>');
echo('<div class="accordion fade-in">');

$inspector_arr = Model::instance('Staff')->get_dropdown_inspector('LAST_NAME');
$inspector_arr[''] = 'All Inspectors';
echo html_report_head('Facility Score', array(
	html_report_form('/reports/facility_score', array(
		array('label' => 'Inspector', 'html' => form::dropdown('inspector_lname', $inspector_arr))
	))),
	'Lists facility criteria score along with related info for all facilities. If inspector is selected, only the inspector\'s facilities will be included in the list. This report is useful for finding which facility has high-risk and needs to be examined.');
?>

<?php

function html_report_head($title, $report, $desc=NULL) {
	$report_html = implode((is_array($report) ? $report : array($report)), ' ');
	$desc_html = ($desc ? "<div class='report_desc'>{$desc}</div>" : '');

	return("<h3><a href='#'>" . html::h($title) . "</a></h3>
		<div>{$report_html} {$desc_html}</div>\n");
}

function html_report_form($action, $fields=array(), $title=NULL) {
	$action = url::fullpath($action);
	if (! array_key_exists('output_format', $fields)) // if not specified, add
		$fields[] = 'output_format';

	$counties = Model::instance('Counties');
	$fiscal_years = Model::instance('Fiscal_years');
	$staff = Model::instance('Staff');
	$contact_types_dropdown =  Model::instance('Ref_contact_type')->get_dropdown();
	$contact_types_dropdown['All'] = "All";
//	$tank_detail_codes = array_keys(Model::instance('Tank_detail_codes')->get_dropdown());
	// make tank detail codes both key and value for the array using array_combine function
//	$tank_detail_codes_dropdown = array_combine($tank_detail_codes, $tank_detail_codes);

	// target='report': IE opens html in current window otherwise
	$html = "<form action='{$action}' target='report' class='small_form validate_form' encType='multipart/form-data' method='post'>
			<fieldset class='ui-widget ui-widget-content ui-corner-all'>";

	$html .= ($title ? "<legend class='ui-widget ui-widget-header ui-corner-all'>" . html::h($title) . '</legend>' : '');

	foreach($fields as $field_name => $field_value) {
		if (is_array($field_value)) {
			$html .= "<label>{$field_value['label']}:</label>{$field_value['html']}<br clear='all' />";
		}
		else {
			$id = basename($action) . "_{$field_name}";
			switch($field_value) {
				case 'before_date' : $html .= "
					<label>Prior to Date:</label>
					<input type='text' name='before_date' id='{$id}' size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />";
					break;
				case 'start_date' : $html .= "
					<label>Begin Date:</label>
					<input type='text' name='start_date' id='{$id}' size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />";
					break;
				case 'end_date' : $html .= "
					<label>End Date:</label>
					<input type='text' name='end_date' id='{$id}' size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />";
					break;
				case 'fy' : $html .= "
					<label>Fiscal Year:</label>
					". form::dropdown('fy', $fiscal_years->get_dropdown(), NULL, 'class="validate[required]"') ."
					<br clear='all' />";
					break;
				case 'year' : $html .= "
					<label>Year:</label>
					<input name='year' id='year' type='number' class='validate[required]' min='1900' max='2100'/><br clear='all' />";
					break;
				case 'include_prior_years': $html .= "
					<label>Include Prior Years?:</label>
					<input name='include_prior_years' id='{$id}' type='checkbox' value='TRUE' /><br clear='all' />";
					break;
				case 'inspector_id' : $html .= "
					<label>Inspector:</label>
					". form::dropdown('inspector_id', $staff->get_dropdown_inspector('code'), NULL) ."
					<br clear='all' />";
					break;
				case 'owner_id' : $html .= "
					<label>Owner ID:</label>
					<input name='owner_id' type='text' id='{$id}' size='10' class='validate[required,custom[integer]]' /><br clear='all' />";
					break;
				case 'facility_id' : $html .= "
					<label>Facility ID:</label>
					<input name='facility_id' type='text' id='{$id}' size='10' class='validate[required,custom[integer]]' /><br clear='all' />";
					break;
				case 'tank_type' : $html .= "
					<label>Tank Type:</label>
					<input name='tank_types[]' id='{$id}' type='checkbox' value='". Tanks_Model::TANK_TYPE_AST ."' class='validate[required]' checked='checked' />AST
					<input name='tank_types[]' id='{$id}' type='checkbox' value='". Tanks_Model::TANK_TYPE_UST ."' class='validate[required]' checked='checked' />UST
					<br clear='all' />";
					break;
				case 'novs' : $html .= "
					<label>NOV Type:</label>
					<input name='nov_type' id='{$id}' type='radio' value='SOC' checked='checked' />SOC
					<input name='nov_type' id='{$id}' type='radio' value='DP' />Delivery Prohibition
					<br clear='all' />";
					break;
				case 'county' : $html .= "
					<label>County:</label>
					". form::dropdown('county', $counties->get_dropdown(), NULL, 'class="validate[required]"') ."
					<br clear='all' />";
					break;
				case 'county_all' : // same as county but with "all" option
					$counties_all = $counties->get_dropdown();
					$counties_all[''] = 'All Counties';
					$html .= "<label>County:</label>
					". form::dropdown('county', $counties_all, NULL) ."
					<br clear='all' />";
					break;
				case 'entity_type' : $html .= '
					<label>Entity Type:</label>
					<input name="entity_type" type="Radio" value="facility" checked="checked"/>Facility
					<input name="entity_type" type="Radio" value="owner" />Owner<br />';
					break;
				case 'contact_type' : $html .= "
					<label>Contact Type:</label>
					". form::dropdown('contact_type', $contact_types_dropdown, NULL, 'class="validate[required]"') ."
					<br clear='all' />";
					break;
				case 'release_status' : $html .= '
					<label>Status:</label>
					<select id="release_status" name="release_status">
						<option value="All" />All</option>
						<option value="Closed" />Closed</option>
						<option value="Referred" />Referred</option>
						<option value="Confirmed" />Confirmed</option>
						<option value="Open" />Open</option>
					</select><br clear="all" />';
					break;
				case 'certified_installer_id' : $html .= "
					<label>Certified Installer:</label>
					". form::dropdown('certified_installer_id', Model::instance('Certified_installers')->get_dropdown_certified_installer(), NULL) ."
					<br clear='all' />";
					break;
				case 'quarter' : $html .= '
					<label>Quarter:</label>
					<select id="quarter" name="quarter">
						<option value="1st" />First Quarter</option>
						<option value="2nd" />Second Quarter</option>
						<option value="3rd" />Third Quarter</option>
						<option value="4th" />Fourth Quarter</option>
					</select><br clear="all" />';
					break;
				case 'output_format' : $html .= '
					<label>Output Type:</label>
					<input name="output_format" type="Radio" value="html" />HTML
					<input name="output_format" type="Radio" value="excel2007" checked="checked" />Excel
					<input name="output_format" type="Radio" value="pdf" />PDF
					<input name="output_format" type="Radio" value="csv" />CSV<br />';
					break;
			}
		}
	}
	
	$html .= '<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
		</fieldset>
		</form>';
	
	return($html);
}

function get_options_sql($sql) {
	return('');
}
?>
