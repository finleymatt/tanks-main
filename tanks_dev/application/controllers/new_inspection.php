<?php

//use \CloudConvert\Api;
use Api2Pdf\Api2Pdf;

class Inspection_Controller extends Template_Controller {

	public $name = 'inspection';
	public $model_name = 'Inspections';
	public $prev_name = 'facility';
	public $template = 'tpl_internal';  // default template for all reports

	public function __construct()
	{
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($id=NULL)
	{
		$view = new View('inspection_view');

		$view->model = new Inspections_Model();
		$view->row = $view->model->get_row($id);
		if (! $view->row)
                	$this->_go_message($this->_error_url(), 'No Inspection with selected ID exists.');

		$view->penalties = new Penalties_Model();
		$penalty_rows = $view->penalties->get_list('INSPECTION_ID = :INSPECTION_ID', NULL, array(':INSPECTION_ID' => $id));

		foreach($penalty_rows as $key => $penalty_row) {
			$penalty_level = $view->penalties->get_penalty_details($id, $penalty_row['PENALTY_CODE'])[0]['PENALTY_LEVEL'];
			$penalty_rows[$key]['PENALTY_LEVEL'] = $penalty_level;
		}
		$view->penalty_rows = $penalty_rows;

		$this->template->content = $view;
	}

	public function download($inspection_id=NULL)
	{
		require_once Kohana::find_file('vendor', 'phpoffice/phpword/bootstrap', TRUE);
		require __DIR__ . '/../vendor/autoload.php';
	
		// cloudconvert API key
		//$api = new Api("u1bkObywmSHbxMTereRb6KcaYy4vMVlNhiUGJodtq3hayChjocZ9bqZCQPLlyZy0");
		$letter_name = $this->uri->segment(4);
		$download_format = $this->uri->segment(5);
		$template_name = ($letter_name == 'RTPIDA' || $letter_name == 'RTPIDB') ? 'RTPID' : $letter_name;

		$governor = Kohana::config('names.governor');
		$lt_governor = Kohana::config('names.lt_governor');
		$cabinet_secretary = Kohana::config('names.cabinet_secretary');
		$deputy_secretary = Kohana::config('names.deputy_secretary');

		$view = new View('inspection_view');
		$view->inspection_id = $inspection_id;
		$view->inspections = new Inspections_Model();
		$view->inspection_row = $view->inspections->get_row($view->inspection_id);

		$view->facilities_mvw = new Facilities_mvw_Model();
		$view->facility_row = $view->facilities_mvw->get_row($view->inspection_row['FACILITY_ID']);
		$facility_name = text::capitalize_first_letters($view->facility_row['FACILITY_NAME']);
		$facility_address = text::capitalize_first_letters($view->facility_row['ADDRESS1']);
		$facility_city = text::capitalize_first_letters($view->facility_row['CITY']);

		$view->owners_mvw = new Owners_mvw_Model();
		$view->owner_row = $view->owners_mvw->get_row($view->facility_row['OWNER_ID']);
		$date_corrected = date::reverse_format_date($view->inspection_row['DATE_INSPECTED']);
		$owner_name = text::capitalize_first_letters($view->owner_row['OWNER_NAME']);
		$owner_address = text::capitalize_first_letters($view->owner_row['ADDRESS1']);
		$owner_city = text::capitalize_first_letters($view->owner_row['CITY']);

		$view->penalties = new Penalties_Model();
		// only dispaly penalties with level A & B
		$inspection_id_arr = array($inspection_id);
		$view->penalty_rows = $view->penalties->get_facility_penalties($inspection_id_arr);

		// Consolidate tanks in one cell on letter to show only one row per violation 
		$penalty_codes = array();
		$penalties = array();
		$penalties_a = array();
		$penalties_b = array();
		foreach($view->penalty_rows as $penalty_row) {
			if(!in_array($penalty_row['PENALTY_CODE'], $penalty_codes)) {
				array_push($penalty_codes, $penalty_row['PENALTY_CODE']);
				array_push($penalties, $penalty_row);
			} else {
				$key = array_search($penalty_row['PENALTY_CODE'], $penalty_codes);

				$penalties[$key]['TANK_ID'] .= ', '. $penalty_row['TANK_ID'];
			}
		}
		foreach($penalties as $penalty) {
			if($penalty['PENALTY_LEVEL'] == 'A') {
				array_push($penalties_a, $penalty);
			} else if($penalty['PENALTY_LEVEL'] == 'B') {
				array_push($penalties_b, $penalty);
			}
		}
			
		// under the same insepction, all penalties have the same NOV and NOD dates
		$nod_date = array_key_exists('0', $view->penalty_rows) ? date::reverse_format_date($view->penalty_rows[0]['NOD_DATE']) : '';
		$nov_date = array_key_exists('0', $view->penalty_rows) ? date::reverse_format_date($view->penalty_rows[0]['NOV_DATE']) : '';
		$noirt_date = array_key_exists('0', $view->penalty_rows) ? date::reverse_format_date($view->penalty_rows[0]['NOIRT_DATE']) : '';

		$view->tanks = new Tanks_Model();
		$view->tank_rows = $view->tanks->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->inspection_row['FACILITY_ID']));

		$view->operators_mvw = new Operators_mvw_Model();
		$view->operator_id = $view->tank_rows[0]['OPERATOR_ID'];
		$view->operator_row = $view->operators_mvw->get_row($view->operator_id);
		$operator_name = text::capitalize_first_letters($view->operator_row['OPERATOR_NAME']);
		$operator_address = text::capitalize_first_letters($view->operator_row['ADDRESS1']);
		$operator_city = text::capitalize_first_letters($view->operator_row['CITY']);
		
		$view->model = new Staff_Model();
		$view->staff_row = $view->model->get_row($view->inspection_row['STAFF_CODE']);
		$inspector_name = $view->staff_row['FIRST_NAME'] . ' ' . $view->staff_row['LAST_NAME'];
		$sep_login_id = $view->staff_row['SEP_LOGIN_ID'];
		$phone_number = $view->model->get_staff_phone_number($sep_login_id);

		$word_directory = '/home/env/report_templates/';

		if (!file_exists($word_directory)) {
			mkdir($word_directory, 0777, true);
		}
		$document = new \PhpOffice\PhpWord\TemplateProcessor($word_directory . $template_name  . '_template.docx');

		if($letter_name == 'NOD' || $letter_name == 'NOIRTB' || $letter_name == 'RTPIDB') {
			$document->cloneRow('Violation_Description', count($penalties_b));
		
			foreach($penalties_b as $key => $penalty) {
				$row_no = $key + 1;
				$violation_code_description = Model::instance('Penalty_codes')->get_lookup_desc($penalty['PENALTY_CODE'], TRUE);
				$position = strpos($violation_code_description, ' ');
				$violation_description = substr($violation_code_description, $position + 1);
				$document->setValue('Violation_Code#' . $row_no, $penalty['PENALTY_CODE']);
				$document->setValue('Tank_ID#' . $row_no, $penalty['TANK_ID']);
				$document->setValue('Violation_Description#' . $row_no, $violation_description);
			}
		} else if ($letter_name == 'NOIRTA' || $letter_name == 'RTPIDA') {
			$document->cloneRow('Violation_Description', count($penalties_a));
			
			foreach($penalties_a as $key => $penalty) {
				$row_no = $key + 1;
				$violation_code_description = Model::instance('Penalty_codes')->get_lookup_desc($penalty['PENALTY_CODE'], TRUE);
				$position = strpos($violation_code_description, ' ');
				$violation_description = substr($violation_code_description, $position + 1);
				$document->setValue('Violation_Code#' . $row_no, $penalty['PENALTY_CODE']);
				$document->setValue('Tank_ID#' . $row_no, $penalty['TANK_ID']);
				$document->setValue('Violation_Description#' . $row_no, $violation_description);
			}
		}
		$document->setValue(
			array(
				'Facility_ID', 'Facility_Name', 'Facility_Address', 'Facility_City', 'Facility_Zip',
				'Owner_ID', 'Owner_Name', 'Owner_Address', 'Owner_City', 'Owner_State', 'Owner_Zip',
				'Operator_ID', 'Operator_Name', 'Operator_Address', 'Operator_City', 'Operator_State', 'Operator_Zip',
				'NOV_Number', 'Date_NOV_Issued', 'Date_NOD_Issued', 'Date_NOIRT_Issued', 'Date_of_Inspection', 'Inspector', 'Inspector_Phone',
				'Governor', 'Lt. Governor', 'Cabinet Secretary', 'Deputy Secretary'
			),
			array(
				$view->facility_row['ID'], $facility_name, $facility_address, $facility_city, $view->facility_row['ZIP'], 
				$view->owner_row['ID'], $owner_name, $owner_address, $owner_city, $view->owner_row['STATE'], $view->owner_row['ZIP'],
				$view->operator_id, $operator_name, $operator_address, $operator_city, $view->operator_row['STATE'], $view->operator_row['ZIP'],
				$view->inspection_row['NOV_NUMBER'], $nov_date, $nod_date, $noirt_date, $date_corrected, $inspector_name, $phone_number, $governor, $lt_governor, $cabinet_secretary, $deputy_secretary
			)
		);

		// generate a temporary word file
		$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
		$sourceFilePath = "/home/env/report_templates/temp.docx";
		$document->saveAs($sourceFilePath);

		if($download_format == 'WORD'){
			$word_name = $letter_name . '_Form_' . $view->inspection_id . '_' . time() . '.docx';
			// generate word document
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/msword");
			header("Content-Transfer-Encoding: Binary");
			header("Content-length: ".filesize($sourceFilePath));
			header("Content-Disposition: attachment; filename=" . $word_name);

			readfile('/home/env/report_templates/temp.docx');
			unlink('/home/env/report_templates/temp.docx');

			$this->template->content = '';
		} else {
			// generate pdf document
			/********** zamzar ********************/
			/*$endpoint = "https://sandbox.zamzar.com/v1/jobs";	
			$apiKey = "618be4a17f14a0ecdf92f71628ddb46565ac2537";	
			$targetFormat = "pdf";

			if(function_exists('curl_file_create')) {
				$sourceFile = curl_file_create($sourceFilePath);
			} else {
				$sourceFile = '@' . realpath($sourceFilePath);
			}

				$postData = array(
				"source_file" => $sourceFile,
				"target_format" => $targetFormat
			);

			$ch = curl_init(); // Init curl
			curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // Enable the @ prefix for uploading files
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
			curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
			$body = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($body, true);

			$jobID = $response['id'];
			$endpoint = "https://sandbox.zamzar.com/v1/jobs/$jobID";

			do{
				sleep(1);
				$ch = curl_init(); // Init curl
				curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
				curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
				$body = curl_exec($ch);
				curl_close($ch);
				$job = json_decode($body, true);
			}
			while($job['status'] !== 'successful');
		
			$fileID = $job['target_files'][0]['id'];*/


			$url = str_replace('tanks.waste', 'service', url::fullpath('')) . 'files/temp.docx';	
			$output_file = $letter_name . '_Form_' . $view->inspection_id . '_' . time() . '.pdf';
			$conversion = url::convert_to_pdf($url, $output_file);
		
			if($conversion["Success"] == true) {
				$pdf_url = $conversion['FileUrl'];
				header("Location: " . $pdf_url);
			} else {
				$error = $conversion['Error'];
				echo $error;
			}

			unlink('/home/env/report_templates/temp.docx');	
			/*$file_name = $letter_name . '_Form_' . $view->inspection_id . '_' . time() . '.pdf';	
			$localFilename = $word_directory . $file_name;
			$endpoint = "https://sandbox.zamzar.com/v1/files/$fileID/content";
			$ch = curl_init(); // Init curl
			curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
			curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			$fh = fopen($localFilename, "wb");
			curl_setopt($ch, CURLOPT_FILE, $fh);

			$body = curl_exec($ch);
			curl_close($ch);*/
		
			/*header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/pdf");
			header("Content-Transfer-Encoding: Binary");
			header("Content-length: ".filesize($localFilename));
			header("Content-Disposition: attachment; filename=" . $file_name);

			readfile($word_directory . $file_name);
			unlink($word_directory . 'temp.docx');
			unlink($localFilename);

			echo "File downloaded\n";*/
			$this->template->content = '';
		}
	}

	protected function delete_pre_render() {
		$this->view->note = 'Deleting an Inspection also deletes all its associated penalties, transactions, and invoices.';
	}
}
