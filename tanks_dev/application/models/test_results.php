<?php

Class Test_results_Model extends Model
{
	public $table_name = 'USTX.TEST_RESULTS';
	public $pks = array('ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));
	public $more_select = array("to_char(TEST_DATE, 'MM/DD/YYYY') TEST_DATE_FMT",
		"to_char(TEST_SUBMITTED_DATE, 'MM/DD/YYYY') TEST_SUBMITTED_DATE_FMT");

	/**
	 *  Method that returns the test results
	 *  @access public
	 *  @return test results
	 */	
	public function get_test_result_list($facility_id, $test_result_id = 0 , $tank_id = 0) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/gettestresults?n_inparm_test_results_id=' . $test_result_id . '&n_inparm_facility_id=' . $facility_id . '&n_inparm_tank_id=' . $tank_id 
		. '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=256,type=chr,value=]&out_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
	
		if(is_null($result) || $result['flag_outvar'] == 'N') {
			return array();
		} else {
			return $result['result']['out_cur'];
		}
	}

	/**
	 * Method that returns single test result
	 * @access public
	 * @return single test result
	 */
	public function get_test_result($test_result_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/gettestresults?n_inparm_test_results_id=' . $test_result_id 
		. '&n_inparm_facility_id=0&n_inparm_tank_id=0&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=256,type=chr,value=]&out_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		if(is_null($result) || $result['flag_outvar'] == 'N') {
			return array();
		} else {
			return $result['result']['out_cur'];
		}
	 }

	 /**
	  * Method that updates test results
	  * @access public
	  * @return updated test results
	  */
	 public function update_test_results($test_result_id, $test_results) {
		$user_id = Session::instance()->get('SEPuserID');
		$facility_id = $this->get_test_result($test_result_id)[0]['FACILITY_ID'];
		$tank_id = $this->get_test_result($test_result_id)[0]['TANK_ID'];
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$tester_id = $test_results['tester_id'];
		$test_company_id = $test_results['test_company_id'];
		$inspection_id = $test_results['violation_issued'];
		$test_date = date::format_date($test_results['test_date']);
		$test_submitted_date = date::format_date($test_results['test_submitted_date']);
		$inspector = urlencode($test_results['inspector']);
		$original_test_id = $test_results['original_test_id'];
		$alld = isset($test_results['alld']) ? $test_results['alld'] : '';
		$sensor = isset($test_results['sensor']) ? $test_results['sensor'] : '';
		$line_tightness = isset($test_results['line_tightness']) ? $test_results['line_tightness'] : '';
		$tank_tightness = isset($test_results['tank_tightness']) ? $test_results['tank_tightness'] : '';
		$atg = isset($test_results['atg']) ? $test_results['atg'] : '';
		$overfill = isset($test_results['overfill']) ? $test_results['overfill'] : '';
		$corrosion = isset($test_results['corrosion']) ? $test_results['corrosion'] : '';
		$spill = isset($test_results['spill']) ? $test_results['spill'] : '';
		$sump = isset($test_results['sump']) ? $test_results['sump'] : '';
		$comments = urlencode($test_results['comments']);

		$url = $domain . 'data/tank/puttestresults?n_inparm_test_results_id=' . $test_result_id . '&n_inparm_facility_id=' . $facility_id . '&n_inparm_tank_id=' . $tank_id
		. '&n_inparm_tester_id=' . $tester_id . '&n_inparm_testing_company_id=' . $test_company_id . '&dt_inparm_test_date=' . $test_date . '&dt_inparm_test_submitted_date='
		. $test_submitted_date. '&vc_inspector_name=' . $inspector . '&c_inparm_annual_alld_func_fl=' . $alld . '&c_inparm_annual_sens_func_fl=' . $sensor 
		. '&c_inparm_annual_line_tight_fl=' . $line_tightness . '&c_inparm_annual_tank_tight_fl=' . $tank_tightness . '&c_inparm_annual_agt_test_fl=' . $atg
		. '&c_inparm_year3_overfill_fl=' . $overfill . '&c_inparm_year3_corrprot_fl=' . $corrosion . '&c_inparm_year3_spillcont_fl=' . $spill . '&c_inparm_year3_sumpcont_fl='
		. $sump . '&n_inparm_inspection_id=' . $inspection_id . '&n_inparm_retest_test_result_id=' . $original_test_id . '&vc_inparm_comments=' . $comments . '&vc_inparm_update_userid='
		. $user_id . '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	 }

	/**
	 * Method that add test results
	 * @access public
	 * @params $test_results
	 * @return result
	 */
	public function add_test_results($facility_id, $test_results) {
		$user_id = Session::instance()->get('SEPuserID');
		$test_submitted_date = null;
		$violation = null;
		$original_test_id = null;
		$test_comment = null;
		$domain = str_replace('tanks.', '', url::fullpath(''));

		foreach($test_results as $test_result) {
			// Reset below values to null in case of neither pass or fail is seleced
			$alld = null;
			$sensor = null;
			$line_tightness = null;
			$tank_tightness = null;
			$atg = null;
			$overfill = null;
			$corrosion = null;
			$spill = null;
			$sump = null;
			foreach($test_result as $key => $value) {
				$field = substr($key, 0, strrpos($key, "_"));
				$tank_id = substr($key, strrpos($key, "_")+1);

				if($field == 'tester') $tester = urlencode($value);
				else if($field == 'testing_company') $testing_company = urlencode($value);
				else if($field == 'test_date') $test_date = date::format_date($value);
				else if($field == 'test_submitted_date') $test_submitted_date = date::format_date($value);
				else if($field == 'violation_inspection_id') $violation = $value;
				else if($field == 'inspector') $inspector = urlencode($value);
				else if($field == 'original_test_id') $original_test_id = $value;
				else if($field == 'alld') $alld = $value;
				else if($field == 'sensor') $sensor = $value;
				else if($field == 'line_tightness') $line_tightness = $value;
				else if($field == 'tank_tightness') $tank_tightness = $value;
				else if($field == 'atg') $atg = $value;
				else if($field == 'overfill') $overfill = $value;
				else if($field == 'corrosion') $corrosion = $value;
				else if($field == 'spill') $spill = $value;
				else if($field == 'sump') $sump = $value;
				else if($field == 'test_comment') $test_comment = urlencode($value);

			}


			$url = $domain . 'data/tank/posttestresults?n_inparm_facility_id=' . $facility_id . '&n_inparm_tank_id=' . $tank_id . '&n_inparm_tester_id=' . $tester
			. '&n_inparm_testing_company_id=' . $testing_company . '&dt_inparm_test_date=' . $test_date . '&dt_inparm_test_submitted_date=' . $test_submitted_date
			. '&vc_inspector_name=' . $inspector . '&c_inparm_annual_alld_func_fl=' . $alld . '&c_inparm_annual_sens_func_fl=' . $sensor
			. '&c_inparm_annual_line_tight_fl=' . $line_tightness . '&c_inparm_annual_tank_tight_fl=' . $tank_tightness . '&c_inparm_annual_agt_test_fl=' . $atg
			. '&c_inparm_year3_overfill_fl=' . $overfill . '&c_inparm_year3_corrprot_fl=' . $corrosion . '&c_inparm_year3_spillcont_fl=' . $spill
			. '&c_inparm_year3_sumpcont_fl=' . $sump . '&n_inparm_inspection_id=' . $violation . '&n_inparm_retest_test_result_id=' . $original_test_id
			. '&vc_inparm_comments=' . $test_comment . '&vc_inparm_update_userid=' . $user_id . '&c_inparm_commit_flag=Y&tester_id_outvar=[length=10,type=int,value=]'
			. '&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result = curl_exec($ch);

			curl_close($ch);
		}

		return $result;	
	}

	public function delete_test_result($test_result_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/deltestresults?n_inparm_test_results_id=' . $test_result_id . '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * Method that retrieve inspection view page link using NOV/LCC Number
	 * @access public
	 * @params $nov_lcc
	 * @return $result
	 */
	public function get_nov_lcc_number($inspection_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/facility/getinspection?n_inparm_inspection_id=' . $inspection_id
		. '&n_inparm_facility_id=0&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		return $result['result']['out_cur'][0]['NOV_NUMBER'];
	}

	/**
	 * Method that returns inspector list
	 * @access public
	 * @return tester list
	 */
	public function get_inspector_list() {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/getinspectors?c_inparm_active_only_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&count_outvar=[length=10,type=int,value=]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		foreach($result['result']['out_cur'] as $inspector) {
			$inspector_list[$inspector['FULL_NAME']] = $inspector['FIRST_NAME'] . ' ' . $inspector['LAST_NAME'];
		}
		asort($inspector_list);
		return $inspector_list;
	}


}
