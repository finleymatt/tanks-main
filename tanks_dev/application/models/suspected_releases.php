<?php


Class Suspected_releases_Model extends Model
{
	public $table_name = 'USTX.SUSPECTED_RELEASE';
	public $pks = array('ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));
	public $more_select = array("to_char(DATE_DISCOVERED, 'MM/DD/YYYY') DATE_DISCOVERED_FMT",
				"to_char(DATE_REPORTED, 'MM/DD/YYYY') DATE_REPORTED_FMT",
				"to_char(SCSR_LETTER_MAILED_DATE, 'MM/DD/YYYY') SCSR_LETTER_MAILED_DATE_FMT",
				"to_char(SEVEN_DAY_REPORT_SUBMIT_DATE, 'MM/DD/YYYY') REPORT_SUBMIT_DATE_FMT",
				"to_char(SYSTEM_TEST_DATE, 'MM/DD/YYYY') SYSTEM_TEST_DATE_FMT",
				"to_char(CLOSED_DATE, 'MM/DD/YYYY') CLOSED_DATE_FMT",
				"to_char(NFA_LETTER_DATE, 'MM/DD/YYYY') NFA_LETTER_DATE_FMT",
				"to_char(APPROVED_ALT_REPORT_DATE, 'MM/DD/YYYY') APPROVED_ALT_REPORT_DATE_FMT",
				"to_char(CONFIRMED_DATE, 'MM/DD/YYYY') CONFIRMED_DATE_FMT",
				"to_char(REFERRED_DATE, 'MM/DD/YYYY') REFERRED_DATE_FMT");

	/**
	 *  Method that returns the suspected release list of a facility
	 *  @access public
	 *  @return suspected releases
	 */
	public function get_suspected_release_list($facility_id, $suspected_release_id = 0 , $ai_id = 0, $tank_id = 0) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/getsuspectedrelease?n_inparm_suspected_release_id=' . $suspected_release_id . '&n_inparm_facility_id=' . $facility_id
		. '&n_inparm_ai_id=' . $ai_id . '&n_inparm_tank_id=' . $tank_id . '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

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
	 * Method that returns single suspected release
	 * @access public
	 * @return single suspected release
	 */
	public function get_suspected_release($suspected_release_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/getsuspectedrelease?n_inparm_suspected_release_id=' . $suspected_release_id
		. '&n_inparm_facility_id=0&n_inparm_ai_id=0&n_inparm_tank_id=0&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

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
	 * Method that get the tanks in a suspected release
	 * @access public
	 * @return tanks in a suspected release
	 */
	public function get_suspected_release_tank($release_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/getsuspectedreleasetank?n_inparm_suspected_release_id=' . $release_id 
		. '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$tanks = json_decode($results, true);
		if(is_null($tanks) || $tanks['flag_outvar'] == 'N') {
			return array();
		} else {
			return $tanks['result']['out_cur'];
		}
	}

	/**
	 * Method that adds suspected release(s) to a facility
	 * @access public
	 * @params $suspected_releases
	 * @return result
	 */
	public function add_suspected_releases($facility_id, $suspected_release) {
		$domain = str_replace('tanks.', '', url::fullpath(''));

		$user_id = Session::instance()->get('SEPuserID');
		$tank_ids = $suspected_release['tanks'];
		$date_discovered = date::format_date($suspected_release['date_discovered']);
		$date_reported = date::format_date($suspected_release['date_reported']);
		$source_id = $suspected_release['source'];
		$cause_desc = urlencode($suspected_release['cause']);
		$date_letter_mailed = date::format_date($suspected_release['date_mailed']);
		$date_seven_day_report_submit = date::format_date($suspected_release['date_seven_day_report_submit']);
		$date_system_test = date::format_date($suspected_release['date_system_test']);
		$date_closed = date::format_date($suspected_release['date_closed']);
		$date_nfa_letter = date::format_date($suspected_release['date_nfa_letter']);
		$date_approved = date::format_date($suspected_release['date_approved']);
		$date_confirmed = date::format_date($suspected_release['date_confirmed']);
		$date_referred = date::format_date($suspected_release['date_referred']);
		$comments = urlencode($suspected_release['comments']);
		//$seven_day_report_due_date = strtoupper(date("d-M-y", strtotime('+7 days', strtotime($date_discovered))));
		$tempo_ai_id = $this->get_tempo_ai_id($facility_id);

		$suspected_release_url = $domain . 'data/tank/postsuspectedrelease?n_inparm_facility_id=' . $facility_id . '&n_inparm_tempo_ai_id=' . $tempo_ai_id 
		. '&dt_inparm_date_discovered=' . $date_discovered . '&dt_inparm_date_reported=' . $date_reported . '&n_inparm_sr_source_id=' . $source_id . '&vc_inparm_cause_desc=' 
		. $cause_desc . '&dt_inparm_date_letter_mailed=' . $date_letter_mailed . '&dt_inparm_date_7day_report=' . $date_seven_day_report_submit . '&dt_inparm_date_system_test=' 
		. $date_system_test . '&dt_inparm_date_closed=' . $date_closed . '&dt_inparm_date_nfa_letter=' . $date_nfa_letter . '&dt_inparm_date_approved_report=' . $date_approved 
		. '&dt_inparm_date_confirmed=' . $date_confirmed . '&dt_inparm_date_referred=' . $date_referred . '&vc_inparm_comments=' . $comments . '&vc_inparm_update_userid='
		. $user_id . '&c_inparm_commit_flag=Y&id_outvar=[length=10,type=int,value=]&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $suspected_release_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$suspected_release_result = curl_exec($ch);

		curl_close($ch);
		$add_suspected_release = json_decode($suspected_release_result, true);

		if($add_suspected_release['flag_outvar'] == 'Y') {
			foreach($tank_ids as $tank_id) {
				$suspected_release_tank_url = $domain . 'data/tank/postsuspectedreleasetank?n_inparm_suspected_release_id=' 
				. $add_suspected_release['id_outvar'] . '&n_inparm_tank_id=' . $tank_id . '&vc_inparm_update_userid=' . $user_id 
				. '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $suspected_release_tank_url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 60);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

				$add_suspected_release_tank_result = curl_exec($ch);
				curl_close($ch);
			}
		} else {
			echo 'Add suspected releases failed, please contact application';
		}
		return $add_suspected_release_tank_result;
	}

	/**
	 * Method that updates suspected release
	 * @access public
	 * @return updated test results
	 */
	public function update_suspected_release($facility_id, $suspected_release_id, $suspected_release) {
		$user_id = Session::instance()->get('SEPuserID');
		$tank_ids_before_input = array();
		$tanks_before_input = $this->get_suspected_release_tank($suspected_release_id);
		foreach($tanks_before_input as $tank) {
			array_push($tank_ids_before_input, $tank['TANK_ID']);
		}

		$tank_ids_input = $suspected_release['tanks'];
		$date_discovered = date::format_date($suspected_release['date_discovered']);
		$date_reported = date::format_date($suspected_release['date_reported']);
		$source_id = $suspected_release['source'];
		$cause_desc = urlencode($suspected_release['cause']);
		$date_letter_mailed = date::format_date($suspected_release['date_mailed']);
		$date_seven_day_report_submit = date::format_date($suspected_release['date_seven_day_report_submit']);
		$date_system_test = date::format_date($suspected_release['date_system_test']);
		$date_closed = date::format_date($suspected_release['date_closed']);
		$date_nfa_letter = date::format_date($suspected_release['date_nfa_letter']);
		$date_approved = date::format_date($suspected_release['date_approved']);
		$date_confirmed = date::format_date($suspected_release['date_confirmed']);
		$date_referred = date::format_date($suspected_release['date_referred']);
		$comments = urlencode($suspected_release['comments']);
		$tempo_ai_id = $this->get_tempo_ai_id($facility_id);	
		//$seven_day_report_due_date = strtoupper(date("d-M-y", strtotime('+7 days', strtotime($date_discovered))));

		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/putsuspectedrelease?n_inparm_suspected_release_id=' . $suspected_release_id . '&n_inparm_facility_id=' . $facility_id . '&n_inparm_tempo_ai_id='
		. $tempo_ai_id . '&dt_inparm_date_discovered=' . $date_discovered . '&dt_inparm_date_reported=' . $date_reported . '&n_inparm_sr_source_id=' . $source_id 
		. '&vc_inparm_cause_desc=' . $cause_desc . '&dt_inparm_date_letter_mailed=' . $date_letter_mailed . '&dt_inparm_date_7day_report=' . $date_seven_day_report_submit
		. '&dt_inparm_date_system_test=' . $date_system_test . '&dt_inparm_date_closed=' . $date_closed . '&dt_inparm_date_nfa_letter=' . $date_nfa_letter 
		. '&dt_inparm_date_approved_report=' . $date_approved . '&dt_inparm_date_confirmed=' . $date_confirmed . '&dt_inparm_date_referred=' . $date_referred . '&vc_inparm_comments='
		. $comments . '&vc_inparm_update_userid=' . $user_id . '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		$update_suspected_release = json_decode($result, true);
		if($update_suspected_release['flag_outvar'] == 'Y') {
			// insert tanks to suspected release using suspected release tank POST method
			foreach($tank_ids_input as $tank_id) {
				if(!in_array($tank_id, $tank_ids_before_input)) {
					$add_suspected_release_url = $domain . 'data/tank/postsuspectedreleasetank?n_inparm_suspected_release_id=' 
					. $suspected_release_id . '&n_inparm_tank_id=' . $tank_id . '&vc_inparm_update_userid=' . $user_id 
					. '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $add_suspected_release_url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_TIMEOUT, 60);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					$result = curl_exec($ch);
					curl_close($ch);
				}
			}
			// delete tanks from suspected release using suspected release tank DELETE method
			foreach($tank_ids_before_input as $tank_id) {
				if(!in_array($tank_id, $tank_ids_input)) {
					$delete_suspected_release_tank_url = $domain . 'data/tank/delsuspectedreleasetank?n_inparm_suspected_release_id=' . $suspected_release_id 
					. '&n_inparm_tank_id=' . $tank_id . '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $delete_suspected_release_tank_url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_TIMEOUT, 60);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					$result = curl_exec($ch);
					curl_close($ch);
				}
			}
		} else {
			echo 'Update suspected releases failed, please contact application';
		}
		return $update_suspected_release;
	}

	/**
	 * Method that deletes a suspected release
	 * @access public
	 * @return success or fail
	 */
	public function delete_suspected_release($suspected_release_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/delsuspectedrelease?n_inparm_suspected_release_id=' . $suspected_release_id 
		. '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * Method that returns suspected release cause list
	 * @access public
	 * @return suspected release cause list
	 */
	public function get_suspected_release_cause_list($cause_id='') {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/getsuspectedreleasecause?n_parm_id=' . $cause_id . '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		foreach($result['result']['out_cur'] as $cause) {
			$cause_list[$cause['CAUSE_ID']] = $cause['CAUSE_DESCRIPTION'];
		}

		return $cause_list;
	}

	/**
	 * Method that returns suspected release source list
	 * @access public
	 * @return suspected release source list
	 */
	public function get_suspected_release_source_list($source_id='') {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/getsuspectedreleasesource?n_parm_id=' . $source_id . '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		foreach($result['result']['out_cur'] as $source) {
			$source_list[$source['SOURCE_ID']] = $source['SOURCE_DESCRIPTION'];
		}
		return $source_list;
	}

	/**
	 * Method that gets TEMPO AI ID
	 * @access public
	 * @return suspected release source list
	 */
	public function get_tempo_ai_id($facility_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'tex-data/facility/list/Y?bureau=PSTB&FacID=' . $facility_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		$facility_info = json_decode($result, true);

		$tempo_ai_id = $facility_info['result'][0]['TEMPO_AI_ID'];
		return $tempo_ai_id;
	}
}
