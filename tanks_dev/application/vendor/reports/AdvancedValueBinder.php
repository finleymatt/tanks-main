<?php
/**
Same as original PHPExcel_Cell_AdvancedValueBinder class with the exception of
not setting any styles for determined formats
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
}

/** PHPExcel_Cell */
require_once PHPEXCEL_ROOT . 'PHPExcel/Cell.php';

/** PHPExcel_Cell_IValueBinder */
require_once PHPEXCEL_ROOT . 'PHPExcel/Cell/IValueBinder.php';

/** PHPExcel_Cell_DefaultValueBinder */
require_once PHPEXCEL_ROOT . 'PHPExcel/Cell/DefaultValueBinder.php';

/** PHPExcel_Style_NumberFormat */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/NumberFormat.php';

/** PHPExcel_Shared_Date */
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/Date.php';

/** PHPExcel_Shared_String */
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/String.php';


/**
 * PHPExcel_Cell_AdvancedValueBinder
 *
 * @category   PHPExcel
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell_AdvancedValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
	/**
	 * Bind value to a cell
	 *
	 * @param PHPExcel_Cell $cell	Cell to bind value to
	 * @param mixed $value			Value to bind in cell
	 * @return boolean
	 */
	public function bindValue(PHPExcel_Cell $cell, $value = null)
	{
		// sanitize UTF-8 strings
		if (is_string($value)) {
			$value = PHPExcel_Shared_String::SanitizeUTF8($value);
		}

		// Find out data type
		$dataType = parent::dataTypeForValue($value);
		
		// Style logic - strings
		if ($dataType === PHPExcel_Cell_DataType::TYPE_STRING && !$value instanceof PHPExcel_RichText) {
			// Check for percentage
			if (preg_match('/^\-?[0-9]*\.?[0-9]*\s?\%$/', $value)) {
				// Convert value to number
				$cell->setValueExplicit( (float)str_replace('%', '', $value) / 100, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				
				// Set style
				//$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE );
				
				return true;
			}
			
			// Check for time e.g. '9:45', '09:45'
			if (preg_match('/^(\d|[0-1]\d|2[0-3]):[0-5]\d$/', $value)) {
				list($h, $m) = explode(':', $value);
				$days = $h / 24 + $m / 1440;
				
				// Convert value to number
				$cell->setValueExplicit($days, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				
				// Set style
				//$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
				
				return true;
			}
			
			// Check for date
			if (strtotime($value) !== false) {
				// make sure we have UTC for the sake of strtotime
				$saveTimeZone = date_default_timezone_get();
				date_default_timezone_set('UTC');
				
				// Convert value to Excel date
				$cell->setValueExplicit( PHPExcel_Shared_Date::PHPToExcel(strtotime($value)), PHPExcel_Cell_DataType::TYPE_NUMERIC);
				
				// Set style
				//$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2 );
				
				// restore original value for timezone
				date_default_timezone_set($saveTimeZone);
				
				return true;
			}
		}

		// Not bound yet? Use parent...
		return parent::bindValue($cell, $value);
	}
}
