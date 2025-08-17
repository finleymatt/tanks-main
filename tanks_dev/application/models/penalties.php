<?php


Class Penalties_Model extends Model
{
	public $table_name = 'USTX.PENALTIES';
	public $pks = array('INSPECTION_ID', 'PENALTY_CODE', 'TANK_ID');
	public $parent_pks = array('Inspections' => array('INSPECTION_ID'));

	public $date_fields = array('LCC_DATE', 'NOV_DATE', 'NOD_DATE', 'NOIRT_DATE', 'REDTAG_PLACED_DATE', 'DATE_CORRECTED');


	public function get_entered($inspection_id) {
		return($this->get_list('INSPECTION_ID = :INSPECTION_ID', NULL, array(':INSPECTION_ID' => $inspection_id)));
	}

	public function insert($parent_ids, $data) {
		$data['inspection_id'] =  $parent_ids[0]; // needed in validation also
		$this->db->set('penalty_code', "'{$data['penalty_code']}'", FALSE); // set as str
		$this->db->set('ustr_number', "'{$data['penalty_code']}'", FALSE); // same as penalty_code
		return(parent::insert($parent_ids, $data));
	}
	protected function _validate_rules($vdata) {
		if (! empty($vdata['inspection_id'])) { // if insert
			$vdata->add_rules('penalty_code', 'required');
			$vdata->add_callbacks('penalty_code', array($this, 'is_unique_code'));
		}
		return($vdata);
	}

	/**
 	 * Validation function used to make sure no more than one penalty code
 	 * exists per inspection.
 	 * This function should only be used during insert operations.
 	 */
	public function is_unique_code($validation, $field) {
		$rows = $this->get_list(array('penalty_code' => "'{$validation[$field]}'",
			'inspection_id' => $validation['inspection_id']));

		if (count($rows))
			$validation->add_error($field, 'Entered Penalty Code already exists for this Inspection.');
	}

	/**
	 * Method that returns Penalties with Penalty Level equals to 'A' and 'B'
	 * @access public
	 * @param array $inspection_ids. The inspection id list
	 * @return facility penalty list with Penalty Level equals to 'A' and 'B'
	 */
	public function get_facility_penalties($inspection_ids) {
		if(empty($inspection_ids)) return array();

		$inspection_id_string = '(' . implode(",", $inspection_ids) . ')';
		$rows = $this->db->query('
			SELECT I.NOV_NUMBER, P.INSPECTION_ID, P.PENALTY_CODE, P.TANK_ID, P.DATE_CORRECTED, P.NOV_DATE, P.NOD_DATE, P.NOIRT_DATE, 
			P.REDTAG_PLACED_DATE, P.REDTAG_REMOVED_DATE, PC.DP_CATEGORY, PC.SOC_CATEGORY, PC.PENALTY_LEVEL, PC.USTR FROM USTX.PENALTIES P
			JOIN USTX.INSPECTIONS I
			ON P.INSPECTION_ID = I.ID
			JOIN USTX.PENALTY_CODES PC
			ON P.PENALTY_CODE = PC.CODE
			AND P.INSPECTION_ID IN ' . $inspection_id_string
			. " AND PC.PENALTY_LEVEL IN ('A', 'B')
			AND (P.DATE_CORRECTED = '01-JAN-68' OR P.DATE_CORRECTED IS NULL)")->as_array(); // empty date entered into DB as 1968/01/01
		return $rows;
	}

	/**
	 * Method that returns Penalty Details
	 * @access public
	 * @param $inspection_id, $penalty_code
	 * @return penalty detail list
	 */
	public function get_penalty_details($inspection_id, $penalty_code) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/getpen?n_inparm_inspection_id=' . $inspection_id . '&vc_inparm_penalty_code=' . url::kohana_decode($penalty_code) . '&vc_inparm_ustr_number=' . '600'
		. '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		return $result['result']['out_cur'];
	}

	/**
	 * Method edit a certain penalty
	 * @access public
	 * @params $inspection_id, $penalty_code, $tankid_str, $occurrence_str, $lcc_str, $nov_str, $nod_str, $lcav_str, $ntrf_str, $noirt_str, $redtag_placed_str, $redtag_removed_str, $correted_str
	 * @return result
	 */
	 public function add_penalty($inspection_id, $penalty_code, $tankid_str, $occurrence_str, $lcc_str, $nov_str, $nod_str, $lcav_str, $ntrf_str, $noirt_str, $redtag_placed_str, $redtag_removed_str, $correted_str) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$update_userid = Session::instance()->get('SEPuserID');
		// UDAPI
		$url = $domain . 'data/insp/postpenalties?n_inparm_inspection_id=' . $inspection_id . '&vc_inparm_penalty_code=' . url::kohana_decode($penalty_code) . '&vc_inparm_ustr_number=' . '600'
		. '&n_inparm_penalty_occurance=[' . $occurrence_str . ']&n_inparm_tank_id=[' . $tankid_str . ']&dt_inparm_date_corrected=[' . $correted_str . ']&dt_inparm_lcc_date=[' . $lcc_str . ']&dt_inparm_nov_date=['
		. $nov_str . ']&dt_inparm_lcav_date=[' . $lcav_str . ']&dt_inparm_nod_date=[' . $nod_str . ']&dt_inparm_noirt_date=[' . $noirt_str . ']&dt_inparm_redtag_placed_date=[' . $redtag_placed_str
		. ']&dt_inparm_ntrf_date=[' . $ntrf_str . ']&dt_inparm_redtag_removed_date=[' . $redtag_removed_str . ']&vc_inparm_update_userid=' . $update_userid
		. '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

		// UDS, used temporarily only when UDAPI is not working
		/*$uds_domain = substr_replace($domain, 'service', 8, 5);
		$url = $uds_domain . 'uds/oracle/USTX/ONESTOP/proc_postPenalties?n_inparm_inspection_id=' . $inspection_id . '&vc_inparm_penalty_code=' . url::kohana_decode($penalty_code) . '&vc_inparm_ustr_number=' . '600'
		. '&n_inparm_penalty_occurance=[' . $occurrence_str . ']&n_inparm_tank_id=[' . $tankid_str . ']&dt_inparm_date_corrected=[' . $correted_str . ']&dt_inparm_lcc_date=[' . $lcc_str  . ']&dt_inparm_nov_date=['
		. $nov_str . ']&dt_inparm_lcav_date=[' . $lcav_str . ']&dt_inparm_nod_date=[' . $nod_str . ']&dt_inparm_noirt_date=[' . $noirt_str . ']&dt_inparm_redtag_placed_date=[' . $redtag_placed_str
		. ']&dt_inparm_ntrf_date=[' . $ntrf_str . ']&dt_inparm_redtag_removed_date=[' . $redtag_removed_str . ']&vc_inparm_update_userid=' . $update_userid
		. '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';*/

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
	 * Method edit a certain penalty
	 * @access public
	 * @params $inspection_id, $penalty_code, $tank_id
	 * @return result
	 */
	 public function edit_penalty($inspection_id, $penalty_code, $tankid_str, $occurrence_str, $lcc_str, $nov_str, $nod_str, $lcav_str, $ntrf_str, $noirt_str, $redtag_placed_str, $redtag_removed_str, $correted_str) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$update_userid = Session::instance()->get('SEPuserID');
		// UDAPI
		$url = $domain . 'data/insp/putpenalties?n_inparm_inspection_id=' . $inspection_id . '&vc_inparm_penalty_code=' . url::kohana_decode($penalty_code) . '&vc_inparm_ustr_number=' . '600'
		. '&n_inparm_penalty_occurance=[' . $occurrence_str . ']&n_inparm_tank_id=[' . $tankid_str . ']&dt_inparm_date_corrected=[' . $correted_str . ']&dt_inparm_lcc_date=[' . $lcc_str . ']&dt_inparm_nov_date=['
		. $nov_str . ']&dt_inparm_lcav_date=[' . $lcav_str . ']&dt_inparm_nod_date=[' . $nod_str . ']&dt_inparm_noirt_date=[' . $noirt_str . ']&dt_inparm_redtag_placed_date=[' . $redtag_placed_str 
		. ']&dt_inparm_ntrf_date=[' . $ntrf_str . ']&dt_inparm_redtag_removed_date=[' . $redtag_removed_str . ']&vc_inparm_update_userid=' . $update_userid
		. '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

		// UDS, used temporarily only when UDAPI is not working
		/*$uds_domain = substr_replace($domain, 'service', 8, 5);
		$url = $uds_domain . 'uds/oracle/USTX/ONESTOP/proc_putPenalties?n_inparm_inspection_id=' . $inspection_id . '&vc_inparm_penalty_code=' . url::kohana_decode($penalty_code) . '&vc_inparm_ustr_number='  . '600'
		. '&n_inparm_penalty_occurance=[' . $occurrence_str . ']&n_inparm_tank_id=[' . $tankid_str . ']&dt_inparm_date_corrected=[' . $correted_str . ']&dt_inparm_lcc_date=[' . $lcc_str  . ']&dt_inparm_nov_date=['
		. $nov_str . ']&dt_inparm_lcav_date=[' . $lcav_str . ']&dt_inparm_nod_date=[' . $nod_str . ']&dt_inparm_noirt_date=[' . $noirt_str . ']&dt_inparm_redtag_placed_date=[' . $redtag_placed_str
		. ']&dt_inparm_ntrf_date=[' . $ntrf_str . ']&dt_inparm_redtag_removed_date=[' . $redtag_removed_str . ']&vc_inparm_update_userid=' . $update_userid
		. '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';*/

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
	 * Method delete a certain penalty
	 * @access public
	 * @params $inspection_id, $penalty_code, $tank_id
	 * @return result
	 */
	 public function delete_penalty($inspection_id, $penalty_code, $tank_id) {
	 	$domain = str_replace('tanks.', '', url::fullpath(''));
		$update_userid = Session::instance()->get('SEPuserID');

		$url = $domain . 'data/insp/delpenalties?n_inparm_inspection_id=' . $inspection_id . '&vc_inparm_penalty_code=' . url::kohana_decode($penalty_code) 
		. '&vc_inparm_ustr_number=' . '600' . '&n_inparm_tank_id=[' . $tank_id 	. ']&vc_inparm_update_userid=' . $update_userid . '&c_inparm_commit_flag=N';

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
}
