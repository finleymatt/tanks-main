<?php

Class Retraining_Model extends Model
{
	public $table_name = 'USTX.RETRAINING';
	public $pks = array('RETRAINING_ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));

	public function get_facility_id($retraining_id) {
		$rows = $this->db->query('
			SELECT facility_id
			FROM ustx.retraining
			WHERE retraining_id = :RETRAINING_ID'
		, array(':RETRAINING_ID' => $retraining_id))->as_array();
		
		return $rows[0]['FACILITY_ID'];
	}

	public function get_retrainings_by_facility($facility_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/getretrainingsummary?facility_id=' . $facility_id . '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur';

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

	public function get_retraining_detail($facility_id, $retraining_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/getretrainingdetail?n_inparm_retraining_id=' . $retraining_id . '&facility_id=' . $facility_id . '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur&out2_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);
		
		$result = json_decode($results, true);
		return $result['result'];

	}

	public function add_retraining($facility_id, $input_arr) {
		$inspection_date = empty($input_arr['date_inspected']) ? '' : date::format_date($input_arr['date_inspected']);
		$notify_date =  empty($input_arr['notified_date']) ? '' : date::format_date($input_arr['notified_date']);
		$item_trained = implode(",", $input_arr['items_trained']);
		$complete_date = empty($input_arr['complete_date']) ? '' : date::format_date($input_arr['complete_date']);
		$onsite = (isset($input_arr['onsite']) && $input_arr['onsite'] == '1') ? '1' : '0' ;
		$ab_operator_id = $input_arr['ab_operator'];
		$cert_number = $input_arr['cert_number'];
		$cert_expire_date = empty($input_arr['expire_date']) ? '' : date::format_date($input_arr['expire_date']);
		$staff_code = $input_arr['staff_code'];
		$staff_name = '';

		$domain = str_replace('tanks.', '', url::fullpath(''));
		$update_userid = Session::instance()->get('SEPuserID');
		$url = $domain . 'data/insp/postretraining?n_inparm_retraining_id=0&facility_id=' . $facility_id . '&dt_inparm_inspection_date=' . $inspection_date
			. '&n_inparm_aboperator_id=' . $ab_operator_id . '&vc_inparm_certnumber=' . $cert_number . '&dt_inparm_certexpire_date=' . $cert_expire_date
			. '&dt_inparm_notify_date=' . $notify_date . '&n_inparm_onsite_fl=' . $onsite . '&dt_inparm_complete_date=' . $complete_date
			. '&vc_inparm_train_staff_code=' . $staff_code . '&vc_inparm_train_staff_name=' . $staff_name . '&vc_inparm_train_item_list=[' . $item_trained
			. ']&vc_inparm_update_userid=' . $update_userid . '&c_inparm_commit_flag=Y&retraining_id_outvar=[length=10,type=int,value=]&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

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
	
	public function edit_retraining($facility_id, $retraining_id, $input_arr) {
		$inspection_date = empty($input_arr['date_inspected']) ? '' : date::format_date($input_arr['date_inspected']);
		$notify_date =  empty($input_arr['notified_date']) ? '' : date::format_date($input_arr['notified_date']);
		$item_trained = implode(",", $input_arr['items_trained']);
		$complete_date = empty($input_arr['complete_date']) ? '' : date::format_date($input_arr['complete_date']);
		$onsite = (isset($input_arr['onsite']) && $input_arr['onsite'] == '1') ? '1' : '0' ;
		$ab_operator_id = $input_arr['ab_operator'];
		$cert_number = $input_arr['cert_number'];
		$cert_expire_date = empty($input_arr['expire_date']) ? '' : date::format_date($input_arr['expire_date']);
		$staff_code = $input_arr['staff_code'];
		$staff_name = '';
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$update_userid = Session::instance()->get('SEPuserID');

		$url = $domain . 'data/insp/putretraining?n_inparm_retraining_id=' . $retraining_id . '&facility_id=' . $facility_id . '&dt_inparm_inspection_date=' . $inspection_date
			. '&n_inparm_aboperator_id=' . $ab_operator_id . '&vc_inparm_certnumber=' . $cert_number . '&dt_inparm_certexpire_date=' . $cert_expire_date
			. '&dt_inparm_notify_date=' . $notify_date . '&n_inparm_onsite_fl=' . $onsite . '&dt_inparm_complete_date=' . $complete_date
			. '&vc_inparm_train_staff_code=' . $staff_code . '&vc_inparm_train_staff_name=' . $staff_name . '&vc_inparm_train_item_list=[' . $item_trained
			. ']&vc_inparm_update_userid=' . $update_userid . '&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

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

	public function delete($retraining_id) {
		$retraining_id = $retraining_id[0];
		$retraining = Model::instance('Retraining')->get_list('RETRAINING_ID = :RETRAINING_ID', NULL, array(':RETRAINING_ID' => $retraining_id));
		$facility_id = $retraining[0]['FACILITY_ID'];
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/delretraining?n_inparm_retraining_id=' . $retraining_id . '&facility_id=' . $facility_id  . '&vc_inparm_update_userid=mark.morell&c_inparm_commit_flag=Y&success_flag_outvar=[length=1,type=chr,value=]&result_msg_outvar=[length=500,type=chr,value=]';

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
