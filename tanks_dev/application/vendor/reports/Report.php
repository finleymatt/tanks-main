<?php
require_once('PHPExcel.php');
require_once('PHPExcel/IOFactory.php');
//require_once 'AdvancedValueBinder.php';  PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() ); // auto conversion of dates, percents...

ini_set('memory_limit', '256M');  // default insider setting: 16MB

if (class_exists('url'))
	define('URL_PATH', url::site());  // only needed if format is HTML


function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
	echo "  Fatal error on line $errline in file $errfile";
	echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
	echo "Aborting...<br />\n";
	exit(1);

	/* Don't execute PHP internal error handler */
	return true;
}
$old_error_handler = set_error_handler("myErrorHandler");


class Report extends PHPExcel {
	public $output_type = NULL;
	public $row_num = 1;
	
	public static $STYLE_LABEL = array(
		'font' => array(
			'bold' => true,
			'color' => array('argb' => 'FFFFFFFF')
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'wrap' => TRUE
		),
		'borders' => array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FF444444') // pink:'FFD07070'
		),
		'numberformat' => array(
			'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
		)
	);

	public static $STYLE_LABEL_2 = array(
		'font' => array(
			'bold' => true,
			'size' => 12
		),
		'alignment' => array(
			'wrap' => TRUE
		),
		'borders' => array(
		),
		'fill' => array(
		),
		'numberformat' => array(
			'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
		)
	);

	public static $STYLE_TOTAL = array(
		'font' => array('bold' => true),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		),
		'borders' => array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFD0A040')
		)
	);

	public static $STYLE_BORDER = array(
		'borders' => array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
	);

	public static $STYLE_TEXT = array(
		'numberformat' => array(
			'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
		)
	);

	public static $STYLE_MONEY = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		),
		'numberformat' => array(
			'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
		)
	);
	
	public static $STYLE_INTEGER = array(
		'numberformat' => array('code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER)
	);
	
	public static $STYLE_PERCENT = array(
		'numberformat' => array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE)
	);
	
	public static $STYLE_DATE = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		),
		'numberformat' => array(
			'code' => 'm/d/yyyy'
		)
	);
	
	public static $STYLE_NOTE = array(
		'font' => array('size' => 8),
		'color' => array('argb' => 'FF333333')
	);

	public static $STYLE_HIGHLIGHT_YELLOW = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFFFE303'))
	);

	public static $STYLE_HIGHLIGHT_PINK = array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFD05050'))
	);

	public static $STYLE_ROW_ODD = array(
		'borders' => array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
	);
	
	public static $STYLE_ROW_EVEN = array(
		'borders' => array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFD0D0D0')
		)
	);
	
	public static $STYLE_CENTER = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		)
	);

	public static $STYLE_LEFT = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
		)
	);

	public static $STYLE_RIGHT = array(
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		)
	);

	public function __construct($output_type, $title='Onestop Report', $subtitle='', $do_header=TRUE) {
		parent::__construct();
		$this->styles = array('LABEL' => Report::$STYLE_LABEL);
		
		$this->output_type = $output_type;

		$this->getProperties()->setCreator("Onestop Tanks")->setTitle($title);
		$this->setActiveSheetIndex(0);

		// set defaults for font, alignment, row height
		//$this->getDefaultStyle()->getFont()->setName('helvetica')->setSize(9);
		$this->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
		
		$this->getActiveSheet()->setShowGridLines(false);
		$this->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
		
		// logo and title at first row
		if ($do_header) {
			//$this->getActiveSheet()->mergeCells('A1:A2');  // give logo vert room in html/pdf
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('NMED Logo')->setCoordinates('A1');
			$objDrawing->setPath('./images/nmed_logo_med.gif');
			$objDrawing->setResizeProportional(true)->setHeight(80);  // PHPExcel bug: hundredth of inches in excel/pdf, pixels in html
			$objDrawing->setWorksheet($this->getActiveSheet());
		
			$this->getActiveSheet()->mergeCells('C1:H1');
			$this->getActiveSheet()->setCellValue('C1', $title);
			$this->getActiveSheet()->getStyle('C1')->getFont()->setBold(true)->setSize(12);
			$this->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
		
			$this->getActiveSheet()->mergeCells('C2:H2');
			$this->getActiveSheet()->setCellValue('C2', $subtitle);
			$this->getActiveSheet()->getStyle('C2')->getFont()->setBold(true)->setSize(11);
			$this->getActiveSheet()->getStyle('C2')->getAlignment()->setWrapText(true);  // to allow multiple lines
			$this->getActiveSheet()->getRowDimension(2)->setRowHeight(40);
		
			$this->row_num = 3;
		}
		else
			$this->row_num = 1;
		
		// print version footer - in Excel only
		$current_date_time = date('M j, Y');
		$this->getActiveSheet()->getHeaderFooter()->setOddHeader("&LNMED-PSTB &R{$title}");
		$this->getActiveSheet()->getHeaderFooter()->setOddFooter("&LReport generated on {$current_date_time} &RPage &P of &N");

		return($this);
	}

	public function setStyle($area, $style) {
		$this->styles[$area] = $style;
	}

	public function setGroup($rows, $group_infos=array(), $do_sort=FALSE) {
		/*********** sort not tested
		if ($do_sort) { // sort array for groups
			$cols = array();
			foreach ($rows as $row) {
				foreach ($group_infos as $group_idx => $group_info)
					$cols[$group_idx][]  = $row[$group_info['name']];
			}
			$flag = array_multisort($cols[0], SORT_ASC, $rows);
		}*****************************/
		
		$group_row_start = array();
		foreach ($rows as $row_idx => $row) {
			// group header ---------------------------------
			foreach($group_infos as $group_idx => $group_info) {
				if ($this->_isHeaderHere($group_info['name'], $rows, $row_idx)) {
					$group_row_start[$group_idx] = $this->row_num;

					if (isset($group_info['header_func']))
						$group_info['header_func']($this, $row, $this->_groupParams($group_row_start, $group_infos, $group_idx, $rows, $row_idx));
				}
			}
			
			// group row -------------------------------------
			foreach($group_infos as $group_idx => $group_info) {
				if (isset($group_info['row_func']))
					$group_info['row_func']($this, $row, $this->_groupParams($group_row_start, $group_infos, $group_idx, $rows, $row_idx));
			}
			
			// group footer ---------------------------------
			foreach(array_reverse($group_infos, true) as $group_idx => $group_info) {  // loop in reverse order
				if ($this->_isFooterHere($group_info['name'], $rows, $row_idx)) {
					if (isset($group_info['footer_func']))
						$group_info['footer_func']($this, $row, $this->_groupParams($group_row_start, $group_infos, $group_idx, $rows, $row_idx));
				}
			}
		}
		
		return($this);
	}
	
	public function setLabelRow($headers, $style=array()) {
		return($this->setRow($headers, FALSE, array_merge($this->styles['LABEL'], $style)));
	}
	
	public function setBlankRow() {
		$this->row_num++;
		return($this);
	}
	
	public function setRow($row=array(), $use_oddeven=TRUE, $style=array(), $height=NULL) {
		$column_min = 0;  // minimum col num that will stop overwrites
		foreach($row as $column => $field) {
			if (is_array($field))
				$column_used = (isset($field['colspan']) ? $field['colspan'] : 1);
			else
				$column_used = 1;
			if ($use_oddeven)
				$style = array_merge(oddeven($this->row_num, self::$STYLE_ROW_ODD, self::$STYLE_ROW_EVEN), $style);

			// if calc column_min is higher, use it instead to stop overwrite
			$this->setCell(max($column_min, $column), $this->row_num, $field, $style);
			$column_min += $column_used;
		}

		if ($height)
			$this->getActiveSheet()->getRowDimension($this->row_num)->setRowHeight($height);

		$this->row_num++;
		
		return($this);
	}
	
	public function setCell($column, $row, $field, $style=array()) {
		if (is_array($field)) {
			if (isset($field['colspan']) || isset($field['rowspan']))
				$this->getActiveSheet()->mergeCellsByColumnAndRow($column, $row,
					$column + (isset($field['colspan']) ? $field['colspan'] - 1 : 0),
					$row + (isset($field['rowspan']) ? $field['rowspan'] - 1 : 0));
			
			if (isset($field['value']))
				$this->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $field['value']);
			
			if (isset($field['conditional'])) {  // not available in Excel 5
				$conditional_styles = $this->getActiveSheet()->getStyleByColumnAndRow($column, $row)->getConditionalStyles();
				array_push($conditional_styles, $field['conditional']);
				$this->getActiveSheet()->getStyleByColumnAndRow($column, $row)->setConditionalStyles($conditional_styles);
			}
			
			if (isset($field['style']))
				$style = array_merge($style, $field['style']);
			
			for ($j=0; $j<(isset($field['colspan']) ? $field['colspan'] : 1); $j++)
				$this->getActiveSheet()->getStyleByColumnAndRow($column+$j, $row)->applyFromArray($style);
		}
		else {
			$this->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $field);
			$this->getActiveSheet()->getStyleByColumnAndRow($column, $row)->applyFromArray($style);
		}
		
		return($this);
	}
	
	/**
	 * Draws horizontal line.
	 * Uses different method depending on output_format
	 **/
	public function setLine($colspan) {
		if (($this->output_type == 'pdf') || ($this->output_type == 'html')) {
			$this->setRow(array(array('colspan' => $colspan, 'value' => '<hr />')), FALSE);
		}
		else { // excel formats
			$this->setRow(array(array('colspan' => $colspan, 'style' => 
				array('fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('argb' => 'FF444444'))
				)
			)), FALSE);

			$this->getActiveSheet()->getRowDimension($this->row_num-1)->setRowHeight(1);
		}
	}

	/**
	 * Draws dashed line.
	 * Used in PDF format, since TCPDF doesnt display borders correctly
	 **/
	public function setDashLine($colspan) {
		$this->setRow(array(array('colspan' => $colspan,
			'value' => str_repeat('- ', 70),
			'style' => Report::$STYLE_CENTER)), FALSE);
	}

	public function setPageBreak() {
		// tcpdf_old requires workaround, while new version is ok
		if ($this->output_type == 'pdf') {
			$this->setCell(PHPExcel_Cell::columnIndexFromString($this->getActiveSheet()->getHighestColumn())+1, $this->row_num - 1, '<tcpdf method="AddPage" />');  // last col + 1 gets newline cmd
		}
		else {
			$this->getActiveSheet()->setBreak("A{$this->row_num}", PHPExcel_Worksheet::BREAK_ROW);
			$this->row_num++;
		}
	}

	public function setColumnSize($widths=array()) {
		if (count($widths))
		{
			foreach($widths as $column => $width)
				$this->getActiveSheet()->getColumnDimensionByColumn($column)->setWidth($width);
		}
		else {
			$max_column = $this->getActiveSheet()->getHighestColumn();
			for($j=0; $j<$max_column; $j++)
				$this->getActiveSheet()->getColumnDimensionByColumn($j)->setAutoSize(true);			
		}
		
		return($this);
	}

	public function output($filename, $output_type=NULL) {
		if (! $output_type)
			$output_type = $this->output_type;
		
		switch(strtolower($output_type)) {
			case 'excel':
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel5');
				$objWriter->save('php://output');
				break;
			case 'excel2007':
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel2007');
				$objWriter->save('php://output');
				break;
			case 'pdf':
				//require_once('my_tcpdf.php'); // add page# in footer

				if (!PHPExcel_Settings::setPdfRenderer(
					PHPExcel_Settings::PDF_RENDERER_TCPDF,
					dirname(__FILE__).'/tcpdf_old/'
				)) die('Could not set TCPDF');

				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($this, 'PDF');
				$objWriter->save('php://output');
				break;
			case 'csv':
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($this, 'CSV');
				$objWriter->save('php://output');
				break;
			default:
				header('Content-Type: text/html');
				header('Content-Disposition: attachment;filename="' . $filename . '.html"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($this, 'HTML');
				$objWriter->setImagesRoot(URL_PATH);
				$objWriter->save('php://output');
		}
		
		return($this);
	}
	
	private function _isHeaderHere($group_name, $rows, $index) {
		return ( !isset($rows[$index-1][$group_name]) ||
			($rows[$index][$group_name] != $rows[$index-1][$group_name]) );
	}
	
	private function _isFooterHere($group_name, $rows, $index) {
		return ( !isset($rows[$index+1][$group_name]) ||
			($rows[$index][$group_name] != $rows[$index+1][$group_name]) );
	}
	
	private function _groupParams($group_row_start, $group_infos, $group_idx, $rows, $row_idx) {
		return(array(
			'group_row_start' => $group_row_start[$group_idx],
			'group_row_end' => ($this->_isFooterHere($group_infos[$group_idx]['name'], $rows, $row_idx) ? $this->row_num - 1 : NULL),  // this param only makes sense at footer
			'is_first_row' => (!isset($group_infos[$group_idx-1]) ||
				$this->_isHeaderHere($group_infos[$group_idx-1]['name'], $rows, $row_idx)),
			'is_last_row' => (!isset($group_infos[$group_idx-1]) ||
				$this->_isFooterHere($group_infos[$group_idx-1]['name'], $rows, $row_idx))
		));
	}
	
	private function _isInMerged($col, $row) {
		foreach($this->getActiveSheet()->getMergeCells() as $range) {
			if ($this->getActiveSheet()->getCellByColumnAndRow($col, $row)->isInRange($range))
				return($range);
		}
		
		return(FALSE);
	}
	
	public static function TO_DATE($date_str) {
		if (!$date_str) return('');
		
		$date_unix = strtotime($date_str);
		if ($date_unix)
			return(PHPExcel_Shared_Date::PHPToExcel($date_unix));
		else
			return($date_str);
			//exit("invalid date in call to Report::TO_DATE: {$date_str}");
	}
}


function oddeven($num, $odd, $even) {
	if ($num % 2 == 0)
		return($even);
	else
		return($odd);
}
?>
