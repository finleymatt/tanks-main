<?php
/**
 * Pre-Invoice Tank Counts
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
$permits = new Permits_Model();

if (!$owner_id || !$fy)
	exit('Required fields not entered.');

$before_style = array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FF5566BB') ) );
$after_style = array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FF55BB66') ) );
$alert_style = array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FFBB5566') ) );

$report_sql = "
SELECT F.ID FACILITY_ID, F.FACILITY_NAME,

	(select count(*)
	from ustx.tanks
	where facility_id = F.id
		and owner_id = :owner_id
		and tank_status_code in (1, 2, 3, 6, 11)
		and ( (:fy < 2002) or (:fy > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		)) tank_base_count,
  
	(select count(*)
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = F.id
		and tanks.owner_id = :owner_id
		and tank_status_code <> '12'
		and history_code in ('P','BP','LE')
		and fiscal_years.fiscal_year = :fy
		and history_date >= fiscal_years.start_date
		and ( (:fy < 2002) or (:fy > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		)) purchased_tank_count,
  
	(select count(*)
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tank_history.owner_id = :owner_id
		and tanks.facility_id = F.id
		and tank_status_code <> '12'
		and history_code in ('S','BS','LO')
		and fiscal_years.fiscal_year = :fy
		and history_date >= fiscal_years.start_date
		and ( (:fy < 2002) or (:fy > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		)) sold_tank_count,
  
	(select count(*)
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = F.id
		and tanks.owner_id = :owner_id
		and tank_status_code <> '12'
		and history_code = 'R'
		and fiscal_years.fiscal_year = :fy
		and history_date >= fiscal_years.start_date + 30
		and ( (:fy < 2002) or (:fy > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		)) removed_tank_count,
  
	(select count(*)
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = F.id
		and tanks.owner_id = :owner_id
		and tank_status_code <> '12'
		and history_code = 'F'
		and fiscal_years.fiscal_year = :fy
		and history_date >= fiscal_years.start_date + 30
		and ( (:fy < 2002) or (:fy > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		)) filled_tank_count,

	(select count(*)
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = F.id
		and tanks.owner_id = :owner_id
		and tank_status_code <> '12'
		and history_code = 'I'
		and fiscal_years.fiscal_year = :fy
		and ((history_date > fiscal_years.end_date)
			or (tanks.tank_type = 'A' and history_date < to_date('07-01-2002','mm-dd-yyyy') and :fy < 2003))  --ASTs not billed until fy 2003
		and ((:fy < 2002) or (:fy > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		)) installed_tank_count

FROM
	USTX.FACILITIES_MVW F

WHERE
	-- original query from invoice package: finds FID based on permit table unioned
	-- with FID that has current tanks. so it grabs more than it needs in case
	F.ID IN (select distinct fac.id
		from ustx.tanks tan, ustx.facilities_mvw fac
		where tan.facility_id = fac.id
		and nvl(fac.indian, 'N') = 'N'
		and (tan.facility_id in (
			select facility_id from ustx.permits
			where owner_id = :owner_id
				and fiscal_year = :fy
				and tanks > 0 )
			or tan.owner_id = :owner_id))
";

$rs_arr = $db->query($report_sql, array(':owner_id' => $owner_id, ':fy' => $fy))->as_array();

$owners_mvw = new Owners_mvw_Model();
$owner_row = $owners_mvw->get_row($owner_id);
$report = new Report($output_format, 'Pre-Invoice Tank Counts', "Owner: ({$owner_id}) {$owner_row['OWNER_NAME']} \nFY: {$fy}");

// labels -----------------------------------------------
$report->setLabelRow(array(
	0 => array('colspan' => 2),
	2 => array(
		'value' => 'Calc before Invoice',
		'colspan' => 3,
		'style' => $before_style
	),
	5 => array(
		'value' => 'Calc for Invoice',
		'colspan' => 7,
		'style' => $after_style
	),
));
$report->setLabelRow( array('FID', 'Facility Name', 'Permit', 'Past 2', 'Past 1', 'Total', 'Base', 'Sold', 'Removed', 'Filled', 'Purchased', 'Installed'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-2, $report->row_num-1);

// main body --------------------------------------------
if (count($rs_arr)) {
	$group_row_start = $report->row_num;

	foreach ($rs_arr as $row) {
		$permit_row = $permits->get_row(array('owner_id' => $owner_id, 'facility_id' => $row['FACILITY_ID'], 'fiscal_year' => $fy));
		$permit_tank = (isset($permit_row['TANKS']) ? $permit_row['TANKS'] : '');

		// query for past tank counts - using ustx.invoice_detail_facilities
		$past_rs_arr = $db->query('SELECT DF.TANK_COUNT
			FROM ustx.invoices I, ustx.invoice_detail_facilities DF
			WHERE I.id = DF.invoice_id
				AND owner_id = :owner_id
				AND DF.fiscal_year = :fy
				AND DF.facility_id = :facility_id
				AND rownum <= 2
			ORDER BY invoice_id DESC',
			array(':owner_id' => $owner_id, ':facility_id' => $row['FACILITY_ID'], ':fy' => $fy))->as_array();

		$past_1 = (isset($past_rs_arr[0]['TANK_COUNT']) ? $past_rs_arr[0]['TANK_COUNT'] : '');
		$past_2 = (isset($past_rs_arr[1]['TANK_COUNT']) ? $past_rs_arr[1]['TANK_COUNT'] : '');
		$pre_total = $row['TANK_BASE_COUNT'] + $row['SOLD_TANK_COUNT'] + $row['REMOVED_TANK_COUNT'] + $row['FILLED_TANK_COUNT'] - $row['PURCHASED_TANK_COUNT'] - $row['INSTALLED_TANK_COUNT'];

		$style = (has_alert($pre_total, $permit_tank, $past_2, $past_1) ? $alert_style : $before_style);
		$report->setRow(array(
			$row['FACILITY_ID'],
			$row['FACILITY_NAME'],
			array('value' => $permit_tank, 'style' => $style),
			array('value' => $past_2, 'style' => $style),
			array('value' => $past_1, 'style' => $style),
			array('value' => "=G{$report->row_num}+H{$report->row_num}+I{$report->row_num}+J{$report->row_num}-K{$report->row_num}-L{$report->row_num}", 'style' => $after_style),
			array('value' => $row['TANK_BASE_COUNT'], 'style' => $after_style),
			array('value' => $row['SOLD_TANK_COUNT'], 'style' => $after_style),
			array('value' => $row['REMOVED_TANK_COUNT'], 'style' => $after_style),
			array('value' => $row['FILLED_TANK_COUNT'], 'style' => $after_style),
			array('value' => $row['PURCHASED_TANK_COUNT'], 'style' => $after_style),
			array('value' => $row['INSTALLED_TANK_COUNT'], 'style' => $after_style)
		));
	}

	// totals summary row ---------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow( array(
		1 => 'Totals:',
		"=SUM(C{$group_row_start}:C{$group_row_end})",
		"=SUM(D{$group_row_start}:D{$group_row_end})",
		"=SUM(E{$group_row_start}:E{$group_row_end})",
		"=SUM(F{$group_row_start}:F{$group_row_end})"
	), FALSE, Report::$STYLE_TOTAL);
}

$report->setRow( array(
	array('value' => "Calc before Invoice tank count sources:\n--Permit is from permits table which gets updated with new tank count everytime invoice is run.\n--Past 2 is from invoice generated 2 invoices prior.\n--Past 1 is from invoice generated 1 invoice prior.",
		'colspan' => 10, 'style' => array_merge(Report::$STYLE_NOTE, array('alignment' => array('wrap' => TRUE))))
	), FALSE );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(60);

$report->setRow( array(
	array('value' => 'Invoice\'s tank count calculation: Total Count = Base + Sold + Removed + Filled - Purchased - Installed ', 'colspan' => 10, 'style' => Report::$STYLE_NOTE)
	), FALSE );
$report->setColumnSize(array(10, 30, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10));
$flag = $report->output('preinvoice_tank_counts');

// local function =========================================================
// returns TRUE only if $pre_total has a nonzero value and none of the pre values match it
function has_alert($pre_total, $permit_tank, $past_2, $past_1) {
	if (!is_numeric($pre_total) || empty($pre_total))
		return(FALSE);
	return(($pre_total != $permit_tank) && ($pre_total != $past_2) && ($pre_total != $past_1));
}
?>
