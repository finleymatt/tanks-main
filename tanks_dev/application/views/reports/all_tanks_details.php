<?php
/**
 * All Storage Tanks Status
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/
ini_set('max_execution_time', 1600); // 4 minutes

$db = Database::instance();

#print_r($tank_types);

$tank_types[] = 'B';  // add Both
#$tank_type_list = implode( ',', array_map(create_function('$val', 'return("\'{$val}\'");'), $tank_types) );
$tank_type_list = implode( ',', array_map(function($val) { return("'{$val}'");}, $tank_types) );

$report_sql = "select distinct T.id TID, (CASE WHEN T.tank_type='A' THEN 'AST' WHEN T.tank_type='U' THEN 'UST' END) as tank_type, F.id as facility_id, F.facility_name, F.address1, F.address2, F.city, F.zip, con.content, TSC.description as status ,T.capacity,
        h.install_date,
        h.removed_date,
        (CASE 
         WHEN T.tank_type='A' then 'No' 
         WHEN T.tank_type='U' and T.tank_status_code in (1,2) then 'Yes'
         ELSE 'No'
        END) as is_federally_regulated
        FROM
USTX.tanks T left join (select
h.tank_id,
max(CASE WHEN h.history_code = 'I' then history_date END) as install_date,
max(CASE WHEN h.history_code = 'R' then history_date END) as removed_date
from tank_history h, tanks t where h.tank_id = t.id and  tank_type in ( $tank_type_list )  group by h.tank_id order by h.tank_id ) h on T.id=h.tank_id
left join 
(select
t.id, LISTAGG(description, ', ') 
WITHIN GROUP (ORDER BY id) as content
FROM  tanks t ,tank_details td, tank_detail_codes tdc
WHERE t.id = td.TANK_ID  and td.TANK_DETAIL_CODE = tdc.code and tdc.tank_info_code = 'B' and  t.tank_type in ( $tank_type_list ) 
GROUP by t.id ) con on t.id = con.id 
left join USTX.facilities_mvw F on T.facility_id= F.id
left join USTX.tank_status_codes TSC on T.tank_status_code=TSC.code
WHERE  T.tank_type in ( $tank_type_list ) 
order by T.id";


if (in_array('A', $tank_types)) $tank_type_names[] = 'AST';
if (in_array('U', $tank_types)) $tank_type_names[] = 'UST';

$rs_arr = $db->query($report_sql);

#echo $report_sql;

if (count($rs_arr)) {
	$report =  new Report($output_format, 'All Tank Report', "\nTank Types: " . implode(', ', $tank_type_names));
	// labels -----------------------------------------------
	$status_header_style = array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FF3355AA') ) );
	$report->setLabelRow( array('Tank ID','Tank Type','FACILITY ID','Facility', 'Street', 'Street 2', 'City','Zip','Status','Federally Regulated','Capacity','Contents','Tank Install Date', 'Tank Removal Date'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-2, $report->row_num-1);
	
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['TID']),
			array('value' => $row['TANK_TYPE']),
			array('value' => $row['FACILITY_ID']),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $row['ADDRESS1']),
			array('value' => $row['ADDRESS2']),
			array('value' => $row['CITY']),
			array('value' => $row['ZIP']),
			array('value' => $row['STATUS']),
			array('value' => $row['IS_FEDERALLY_REGULATED']),
			array('value' => $row['CAPACITY']),
			array('value' => $row['CONTENT']),
			array('value' => $row['INSTALL_DATE']),
			array('value' => $row['REMOVED_DATE'])
		));
	}

	// tank totals ------------------------------------------------
	//$group_row_end = $report->row_num - 1;

	$report->setColumnSize(array(8,12, 12, 35, 35, 25, 15, 15, 8, 8, 10,30,15, 15 ));
	$flag = $report->output('all_tanks_details');
}

