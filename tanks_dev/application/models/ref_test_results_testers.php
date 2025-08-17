<?php


Class Ref_test_results_testers_Model extends Model
{
	public $table_name = 'USTX.TEST_RESULTS_TESTERS';
	public $pks = array('ID');
	public $lookup_code = 'ID';
	public $lookup_desc = 'LAST_NAME';

	/**
	 * Method that returns tester list
	 * @access public
	 * @return tester list
	 */
	public function get_tester_list() {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/gettestresultstesterlist?c_inparm_active_only_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		foreach($result['result']['out_cur'] as $tester) {
			$tester_name = $tester['TESTER_NAME'];
			$first_name = substr($tester_name, strrpos($tester_name, ",") + 2);
			$last_name = substr($tester_name, 0, strrpos($tester_name, ","));
			$tester_list[$tester['TESTER_ID']] = $first_name . ' ' . $last_name; 
		}
		asort($tester_list);
		return $tester_list;
	}

	/**
	 * Method that return test company id if it exists, add testing company and returns id if not exists
	 * @access public
	 * @return test company id
	 */
	public function get_tester_id($tester) {
		$first_name = substr($tester, 0, strrpos($tester, ' '));
		$last_name = substr($tester, strrpos($tester, ' ') + 1);

		$tester_row = $this->db->query('
		SELECT * FROM USTX.test_results_testers
		WHERE FIRST_NAME = :FIRST_NAME
		AND LAST_NAME = :LAST_NAME'
		, array(':FIRST_NAME' => $first_name, ':LAST_NAME' => $last_name))->as_array();

		if(!empty($tester_row)) {
			return $tester_row[0]['ID'];
		} else {
			$user_id = Session::instance()->get('SEPuserID');
			$domain = str_replace('tanks.', '', url::fullpath(''));
			$url = $domain . 'data/tank/posttestresultstester?vc_inparm_first_name=' . $first_name . '&vc_inparm_last_name=' . $last_name . '&vc_inparm_update_userid=' . $user_id
			. '&c_inparm_commit_flag=Y&tester_id_outvar=[length=10,type=int,value=]&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result = curl_exec($ch);
			curl_close($ch);

			$add_tester = json_decode($result, true);
			if($add_tester['success_flag_outvar'] == 'Y') {
				return $add_tester['tester_id_outvar'];
			} else {
				echo $add_tester['result_msg_outvar'];
			}
		}
	}

	/**
	 * Method that add a test result tester 
	 * @access public
	 * @params $tester_name
	 * @return result
	 */
	public function add_tester($tester_name) {
		$first_name = urlencode($tester_name['first_name']);
		$last_name = urlencode($tester_name['last_name']);
		$user_id = Session::instance()->get('SEPuserID');
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/posttestresultstester?vc_inparm_first_name=' . $first_name . '&vc_inparm_last_name=' . $last_name . '&vc_inparm_update_userid=' . $user_id
		. '&c_inparm_commit_flag=Y&tester_id_outvar=[length=10,type=int,value=]&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

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

	/**
	 * Method that delete a test result tester
	 * @access public
	 * @params $tester_id
	 * @return result
	 */
	public function delete_testers($tester_ids) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		foreach($tester_ids as $tester_id) {
			$url = $domain . 'data/tank/deltestresultstester?n_inparm_id=' . $tester_id
			. '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

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
