<?php


Class Ref_test_results_test_company_Model extends Model
{
	public $table_name = 'USTX.TEST_RESULTS_TEST_COMPANY';
	public $pks = array('ID');
	public $lookup_code = 'ID';
	public $lookup_desc = 'NAME';

	/**
	 * Method that returns tester list
	 * @access public
	 * @return tester list
	 */
	public function get_test_company_list() {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain .'data/insp/gettestresultstestingcompanylist?c_inparm_active_only_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		foreach($result['result']['out_cur'] as $testing_company) {
			$testing_company_list[$testing_company['TESTING_COMPANY_ID']] = $testing_company['TESTING_COMPANY_NAME'];
		}
		
		return $testing_company_list;
	}

	/**
	 * Method that return test company id if it exists, add testing company and returns id if not exists
	 * @access public
	 * @return test company id
	 */
	public function get_test_company_id($test_company) {
		// Decode special characters to search
		$test_company = urldecode($test_company);
		$test_company_row = $this->db->query('
		SELECT * FROM USTX.test_results_test_company
		WHERE NAME = :TEST_COMPANY'
		, array(':TEST_COMPANY' => $test_company))->as_array();

		if(!empty($test_company_row)) {
			return $test_company_row[0]['ID'];
		} else {
			$user_id = Session::instance()->get('SEPuserID');
			$domain = str_replace('tanks.', '', url::fullpath(''));
			$url = $domain . 'data/tank/posttestresultstestingcompany?vc_inparm_name=' . $test_company . '&vc_inparm_update_userid=' . $user_id . '&c_inparm_commit_flag=Y&testing_comp_id_outvar=[length=10,type=int,value=]&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result = curl_exec($ch);
			curl_close($ch);

			$add_test_company = json_decode($result, true);
			if($add_test_company['success_flag_outvar'] == 'Y') {
				return $add_test_company['testing_comp_id_outvar'];
			} else {
				echo $add_test_company['result_msg_outvar'];
			}
		}
	}

	/**
	 * Method that creates a new test company
	 * @access public
	 * @params $company_name
	 * @return result
	 */
	public function add_test_company($test_company) {
		$company = urlencode($test_company['company_name']);
		$user_id = Session::instance()->get('SEPuserID');
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/posttestresultstestingcompany?vc_inparm_name=' . $company . '&vc_inparm_update_userid=' . $user_id
		. '&c_inparm_commit_flag=Y&testing_comp_id_outvar=[length=10,type=int,value=]&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function delete_test_companies($test_company_ids) {

		//$user_id = Session::instance()->get('SEPuserID');
		$domain = str_replace('tanks.', '', url::fullpath(''));
		foreach($test_company_ids as $tester_id) {
			$url = $domain . 'data/tank/deltestresultstestingcompany?n_inparm_id=' . $tester_id
			.'&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		return $result;
	}
}
