<?php
/**
 * Tank and Owner Statistics
 * imported from Oracle Report, "Tank and Owner Statistics"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Dates are not in valid format.');

$report_sql = "
SELECT * FROM

-- owners with active tanks
(select count(*) owners_w_active_tanks
from ustx.owners_mvw
where id in (select owner_id
	from ustx.tanks
	where id in (select tank_id from ustx.tank_history where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I')
		and tank_status_code <> '12'
		and id not in (select tank_id from ustx.tank_history where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F')))
) owners,

-- facilities with active tanks
(select count(*) facilities_w_active_tanks
from ustx.facilities_mvw
where
	id in (
		select facility_id from ustx.tanks
		where id in (select tank_id from ustx.tank_history where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I')
			and tank_status_code <> '12'
			and id not in (select tank_id from ustx.tank_history where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F'))
	)
) facilities,

-- facilities with active UST tanks
(select count(*) facilities_w_active_tanks_ust
from ustx.facilities_mvw
where
	id in (
		select facility_id from ustx.tanks
		where id in (select tank_id from ustx.tank_history where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I')
			and tank_status_code <> '12'
			and id not in (select tank_id from ustx.tank_history where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F'))
			and tank_type = 'U'
	)
) facilities_w_active_tanks_ust,

-- facilities with active AST tanks
(select count(*) facilities_w_active_tanks_ast
from ustx.facilities_mvw
where
	id in (
		select facility_id from ustx.tanks
		where id in (select tank_id from ustx.tank_history where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I')
			and tank_status_code <> '12'
			and id not in (select tank_id from ustx.tank_history where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F'))
			and tank_type = 'A'
	)
) facilities_w_active_tanks_ast,

-- active USTs
(select count(*) active_usts
from ustx.tanks
where
	id in (
		select id from ustx.tanks
		where id in (select tank_id from ustx.tank_history where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I')
			and tank_status_code <> '12'
			and id not in (select tank_id from ustx.tank_history where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F'))
	)
	and tank_type = 'U'
) usts,

-- active ASTs
(select count(*) active_asts
from ustx.tanks
where
	id in (
		select id from ustx.tanks
		where id in (select tank_id from ustx.tank_history where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I')
			and tank_status_code <> '12'
			and id not in (select tank_id from ustx.tank_history where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F'))
	)
	and tank_type = 'A'
) asts,

-- operators with active tanks
(select count(distinct operators.id) operators_w_active_tanks
from ustx.operators_mvw operators, ustx.owners_mvw owners, ustx.tanks
where operators.id = tanks.operator_id
	and owners.id = tanks.owner_id
	and substr(operators.id,2) <> decode(owners.org_id,null,owners.per_id,owners.org_id)
	and (operators.id,owners.id,tanks.id) in (
		select t1.operator_id, t1.owner_id, t1.id
		from ustx.tanks t1
		where id in (
			select id
			from ustx.tanks
			where id in (
				select tank_id
				from ustx.tank_history
				where history_date <= TO_DATE(:end_date, 'mm/dd/yyyy') and history_code = 'I'
			)
			and tank_status_code <> '12'
			and id not in (
				select tank_id
				from ustx.tank_history
				where history_date < TO_DATE(:end_date, 'mm/dd/yyyy') and history_code in ('R','F')
			)
		)
	)
) operators,

-- delinquent owners     
(select count(*) delinquent_owners
from ustx.owners_mvw
where
	id in (
		select owner_id
		from ustx.transactions
		where fiscal_year > 1978
			and transaction_date <= TO_DATE(:end_date, 'mm/dd/yyyy')
			and instr(transaction_code, 'H') = 0
			and instr(transaction_code, 'G') = 0
		group by owner_id
		having 
			sum(decode(transaction_code, 'PP', amount*-1, 'LP', amount*-1, 
				'WP', amount*-1, 'IP', amount*-1, 'PW', amount*-1, 'LW', amount*-1, 
				'IW', amount*-1, amount)
			) > 0
	)
) delinquent_owners,

-- outstanding fees
(select
	sum(decode(transaction_code, 'PP', amount*-1, 'LP', amount*-1, 
		'WP', amount*-1, 'IP', amount*-1, 'PW', amount*-1, 'LW', amount*-1, 
		'IW', amount*-1, amount)
	) outstanding_fees
from ustx.transactions
where fiscal_year > 1978
	and transaction_date <= TO_DATE(:end_date, 'mm/dd/yyyy')
	and instr(transaction_code, 'H') = 0
	and instr(transaction_code, 'G') = 0
) outstanding_fees,

-- over 5
(select
	sum(decode(transaction_code, 'PP', amount*-1, 'LP', amount*-1, 
		'WP', amount*-1, 'IP', amount*-1, 'PW', amount*-1, 'LW', amount*-1, 
		'IW', amount*-1, amount)
	) outstanding_fees_over_5_yrs
from ustx.transactions
where transactions.fiscal_year > 1978
	and transactions.transaction_date < add_months(TO_DATE(:end_date, 'mm/dd/yyyy'), -60)
	and instr(transaction_code, 'H') = 0
	and instr(transaction_code, 'G') = 0
) over_5,

-- tanks closed
(select count(*) tanks_closed
from ustx.tanks, ustx.tank_history
where tanks.id = tank_history.tank_id
	and history_code in ('R','F')
	and history_date between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
) tanks_closed
";

$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();
if (count($rs_arr)) {
	$caf_report = new Report($output_format, 'Tank and Owner Statistics', "dates: {$start_date} - {$end_date}");
	
	// main body --------------------------------------------
	foreach ($rs_arr as $row) {
		$caf_report->setRow(array(
			array('value' => 'Owners With Active Tanks'),
			array('value' => $row['OWNERS_W_ACTIVE_TANKS'])
		));
		$caf_report->setRow(array(
			array('value' => 'Operators With Active Tanks'),
			array('value' => $row['OPERATORS_W_ACTIVE_TANKS'])
		));
		$caf_report->setRow(array(
			array('value' => 'Facilities With Active Tanks'),
			array('value' => $row['FACILITIES_W_ACTIVE_TANKS'])
		));
		$caf_report->setRow(array(
			array('value' => 'Facilities With Active Tanks - UST'),
			array('value' => $row['FACILITIES_W_ACTIVE_TANKS_UST'])
		));
		$caf_report->setRow(array(
			array('value' => 'Facilities With Active Tanks - AST'),
			array('value' => $row['FACILITIES_W_ACTIVE_TANKS_AST'])
		));
		$caf_report->setRow(array(
			array('value' => 'Active USTs'),
			array('value' => $row['ACTIVE_USTS'])
		));
		$caf_report->setRow(array(
			array('value' => 'Active ASTs'),
			array('value' => $row['ACTIVE_ASTS'])
		));
		$caf_report->setRow(array(
			array('value' => 'Delinquent Owners'),
			array('value' => $row['DELINQUENT_OWNERS'])
		));
		$caf_report->setRow(array(
			array('value' => 'Outstanding Fees'),
			array('value' => $row['OUTSTANDING_FEES'], 'style' => Report::$STYLE_MONEY)
		));
		$caf_report->setRow(array(
			array('value' => 'Fees Outstanding Over 5 Yrs'),
			array('value' => $row['OUTSTANDING_FEES_OVER_5_YRS'], 'style' => Report::$STYLE_MONEY)
		));
		$caf_report->setRow(array(
			array('value' => "Tanks Closed Between {$start_date} and {$end_date}"),
			array('value' => $row['TANKS_CLOSED'])
		));
	}
	
	$caf_report->setColumnSize(array(40, 20));
	$flag = $caf_report->output('tank_owner_stat');
}

?>
