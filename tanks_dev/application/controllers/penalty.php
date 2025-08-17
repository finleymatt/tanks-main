<?php

class Penalty_Controller extends Template_Controller {

	public $name = 'penalty';
	public $model_name = 'Penalties';
	public $prev_name = 'inspection';

	public $template = 'tpl_internal';  // default template for all reports

	public function __construct()
	{
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	///////////////////////////////////////////////////////////////////////////
	// following 5 methods overridden to workaround kohana's converting of '_' to '.'
	///////////////////////////////////////////////////////////////////////////

	public function view($inspection_id, $penalty_code = null, $tank_id = null) {
		return(parent::view($inspection_id, url::kohana_decode($penalty_code), $tank_id));
	}

	public function add($inspection_id) {
		return(parent::add($inspection_id));
	}

	public function edit($inspection_id, $penalty_code = null, $tank_id = null) {
		/*$ids = array($inspection_id, url::kohana_decode($penalty_code), $tank_id);

		$view = new View('penalty_edit');
		$view->penalties = new Penalties_Model();
		$view->is_add = FALSE;
		$view->action = parent::_edit_action_url($ids);
		$view->row = $view->penalties->get_row($ids);
		
		$view->inspection = new Inspections_Model();
		$facility_id = $view->inspection->get_value("SELECT FACILITY_ID AS VAL FROM ustx.inspections I WHERE I.ID = :INSPECTION_ID", array(':INSPECTION_ID' => $inspection_id));
		$view->tanks = new Tanks_Model();
		$tank_rows = $view->tanks->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $facility_id));

		if (! $view->row) {
			$this->_go_message(parent::_error_url(), "No penalty with selected ID exists.");
		}

		$this->template->content = $view;*/
		return(parent::edit($inspection_id, url::kohana_decode($penalty_code), $tank_id));
	}

	public function add_action($inspection_id) {
		$penalty_code = $this->input->post('penalty_code');
		$input_arr = $this->input->post();
		$tank_id_arr = [];
		$occurrence_arr = [];
		$date_corrected_arr = [];
		$lcc_date_arr = [];
		$nov_date_arr = [];
		$lcav_date_arr = [];
		$nod_date_arr = [];
		$noirt_date_arr = [];
		$redtag_placement_date_arr = [];
		$ntrf_date_arr = [];
		$redtag_removed_date_arr = [];

		// add all tank ids into an array
		foreach($input_arr as $key => $value) {
			if(substr($key, 0, 8) == 'checkbox') {
				$tank_id = substr($key, strrpos($key, "_")+1);
				array_push($tank_id_arr,  $tank_id);
			}
		}

		// add disabled fields of removed tanks to input array so it can pass UDAPI
		foreach($tank_id_arr as $tank_id) {
			if(!array_key_exists('occurrence_' . $tank_id, $input_arr)) {
				$input_arr['occurrence_' . $tank_id] = '';
				$input_arr['lcc_date_' . $tank_id] = '';
				$input_arr['nov_date_' . $tank_id] = '';
				$input_arr['nod_date_' . $tank_id] = '';
				$input_arr['lcav_date_' . $tank_id] = '';
				$input_arr['ntrf_date_' . $tank_id] = '';
				$input_arr['noirt_date_' . $tank_id] = '';
				$input_arr['red_tag_placement_date_' . $tank_id] = '';
				$input_arr['red_tag_removal_date_' . $tank_id] = '';
			}
		}

		// data processing for empty fields that UDAPI cannot handle as of now
		foreach($tank_id_arr as $tank_id) {
			foreach($input_arr as $key => $value) {
				$field = substr($key, 0, strrpos($key, "_"));
				$tank = substr($key, strrpos($key, "_")+1);

				if(substr($key, 0, 10) == 'occurrence' && $tank_id == $tank) array_push($occurrence_arr, empty($value) ? '99999' : $value);
				else if($field == 'lcc_date' && $tank_id == $tank) array_push($lcc_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'nov_date' && $tank_id == $tank) array_push($nov_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'nod_date' && $tank_id == $tank) array_push($nod_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'lcav_date' && $tank_id == $tank) array_push($lcav_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'ntrf_date' && $tank_id == $tank) array_push($ntrf_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'noirt_date' && $tank_id == $tank) array_push($noirt_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'red_tag_placement_date' && $tank_id == $tank) array_push($redtag_placement_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'red_tag_removal_date' && $tank_id == $tank) array_push($redtag_removed_date_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if($field == 'date_corrected' && $tank_id == $tank) array_push($date_corrected_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
			}
		}


		$tankid_str = implode(",", $tank_id_arr);
		$occurrence_str = implode(",", $occurrence_arr);
		$lcc_str =  implode(",", $lcc_date_arr);
		$nov_str =  implode(",", $nov_date_arr);
		$nod_str =  implode(",", $nod_date_arr);
		$lcav_str =  implode(",", $lcav_date_arr);
		$ntrf_str =  implode(",", $ntrf_date_arr);
		$noirt_str =  implode(",", $noirt_date_arr);
		$redtag_placed_str =  implode(",", $redtag_placement_date_arr);
		$redtag_removed_str =  implode(",", $redtag_removed_date_arr);
		$correted_str =  implode(",", $date_corrected_arr);

		$model = new Penalties_Model();
		$add_penalty = $model->add_penalty($inspection_id, $penalty_code, $tankid_str, $occurrence_str, $lcc_str, $nov_str, $nod_str, $lcav_str, $ntrf_str, $noirt_str, $redtag_placed_str, $redtag_removed_str, $correted_str);
		$result = json_decode($add_penalty, true);	

		$parent_url = $this->_parent_url($inspection_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully created {$this->name}.", 'info_message');
		}else {
			$this->_go_message($parent_url, "Error occurred while creating penalty.");
		}
	}

	public function edit_action($inspection_id, $penalty_code = null) {
		// tanks with this inspection and penalty
		$existing_tanks = [];
		$input_arr = $this->input->post();

		$input_tanks = [];
		$tank_id_edited_arr = [];
		$tank_id_added_arr= [];
		$occurrence_edited_arr = [];
		$occurrence_added_arr = [];
		$date_corrected_edited_arr = [];
		$date_corrected_added_arr = [];
		$lcc_date_edited_arr = [];
		$lcc_date_added_arr = [];
		$nov_date_edited_arr = [];
		$nov_date_added_arr = [];
		$lcav_date_edited_arr = [];
		$lcav_date_added_arr = [];
		$nod_date_edited_arr = [];
		$nod_date_added_arr = [];
		$noirt_date_edited_arr = [];
		$noirt_date_added_arr = [];
		$redtag_placement_date_edited_arr = [];
		$redtag_placement_date_added_arr = [];
		$ntrf_date_edited_arr = [];
		$ntrf_date_added_arr = [];
		$redtag_removed_date_edited_arr = [];
		$redtag_removed_date_added_arr = [];

		$model = new Penalties_Model();
		$penalty_details = $model->get_penalty_details($inspection_id, $penalty_code);		

		// gather all the tank IDs with penalty
		foreach($penalty_details as $key_out => $tank) {
			foreach($tank as $key_in => $value) {
				if($key_in == 'TANK_ID') array_push($existing_tanks,  $value);
			}
		}

		foreach($input_arr as $key => $value) {
			// gether all the input tanks, check which tanks need to be added, edited
			if(substr($key, 0, 8) == 'checkbox') {
				$tank_id = substr($key, strrpos($key, "_")+1);
				array_push($input_tanks, $tank_id);
				if(in_array($tank_id, $existing_tanks)) array_push($tank_id_edited_arr, $tank_id);
				else array_push($tank_id_added_arr,  $tank_id);	
			}
			$tank_id = substr($key, strrpos($key, "_")+1);
			//  
			if(in_array($tank_id, $existing_tanks)) {
				if(substr($key, 0, 10) == 'occurrence') array_push($occurrence_edited_arr, empty($value) ? '99999' : $value);
				else if(substr($key, 0, 8) == 'lcc_date') array_push($lcc_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 8) == 'nov_date') array_push($nov_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 8) == 'nod_date') array_push($nod_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 9) == 'lcav_date') array_push($lcav_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 9) == 'ntrf_date') array_push($ntrf_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 10) == 'noirt_date') array_push($noirt_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 17) == 'red_tag_placement') array_push($redtag_placement_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 15) == 'red_tag_removal') array_push($redtag_removed_date_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 14) == 'date_corrected') array_push($date_corrected_edited_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
			} else {
				if(substr($key, 0, 10) == 'occurrence') array_push($occurrence_added_arr, empty($value) ? '99999' : $value);
				else if(substr($key, 0, 8) == 'lcc_date') array_push($lcc_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 8) == 'nov_date') array_push($nov_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 8) == 'nod_date') array_push($nod_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 9) == 'lcav_date') array_push($lcav_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 9) == 'ntrf_date') array_push($ntrf_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 10) == 'noirt_date') array_push($noirt_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 17) == 'red_tag_placement') array_push($redtag_placement_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 15) == 'red_tag_removal') array_push($redtag_removed_date_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
				else if(substr($key, 0, 14) == 'date_corrected') array_push($date_corrected_added_arr, empty($value) ? '01-JAN-68' : date::format_date($value));
			}
		}

		$occurrence_edited_str = implode(",", $occurrence_edited_arr);
		$occurrence_added_str = implode(",", $occurrence_added_arr);
		$lcc_edited_str =  implode(",", $lcc_date_edited_arr);
		$lcc_added_str =  implode(",", $lcc_date_added_arr);
		$nov_edited_str =  implode(",", $nov_date_edited_arr);
		$nov_added_str =  implode(",", $nov_date_added_arr);
		$nod_edited_str =  implode(",", $nod_date_edited_arr);
		$nod_added_str =  implode(",", $nod_date_added_arr);
		$lcav_edited_str =  implode(",", $lcav_date_edited_arr);
		$lcav_added_str =  implode(",", $lcav_date_added_arr);
		$ntrf_edited_str =  implode(",", $ntrf_date_edited_arr);
		$ntrf_added_str =  implode(",", $ntrf_date_added_arr);
		$noirt_edited_str =  implode(",", $noirt_date_edited_arr);
		$noirt_added_str =  implode(",", $noirt_date_added_arr);
		$redtag_placed_edited_str =  implode(",", $redtag_placement_date_edited_arr);
		$redtag_placed_added_str =  implode(",", $redtag_placement_date_added_arr);
		$redtag_removed_edited_str =  implode(",", $redtag_removed_date_edited_arr);
		$redtag_removed_added_str =  implode(",", $redtag_removed_date_added_arr);
		$correted_edited_str =  implode(",", $date_corrected_edited_arr);
		$correted_added_str =  implode(",", $date_corrected_added_arr);

		$deleted_tanks_arr = array_diff($existing_tanks,$input_tanks);
		if(!empty($deleted_tanks_arr) &&  !is_null($deleted_tanks_arr[0])) { //delete penalties
			$deleted_tanks_str = implode(",", $deleted_tanks_arr);
			$delete_penalty = $model->delete_penalty($inspection_id, $penalty_code, $deleted_tanks_str);
			$delete_result = json_decode($delete_penalty, true);
		}
		if(!empty($tank_id_edited_arr)) { // edit existing penalties
			$tankid_edited_str = implode(",", $tank_id_edited_arr);
			$edit_penalty = $model->edit_penalty($inspection_id, $penalty_code, $tankid_edited_str, $occurrence_edited_str, $lcc_edited_str, $nov_edited_str, 
					$nod_edited_str, $lcav_edited_str, $ntrf_edited_str, $noirt_edited_str, $redtag_placed_edited_str, $redtag_removed_edited_str, $correted_edited_str);
			$edit_result = json_decode($edit_penalty, true);
		}
		if(!empty($tank_id_added_arr)) { // add new penaties
			$tankid_added_str = implode(",", $tank_id_added_arr);
			$add_penalty = $model->add_penalty($inspection_id, $penalty_code, $tankid_added_str, $occurrence_added_str, $lcc_added_str, $nov_added_str,
					$nod_added_str, $lcav_added_str, $ntrf_added_str, $noirt_added_str, $redtag_placed_added_str, $redtag_removed_added_str, $correted_added_str);
			$add_result = json_decode($add_penalty, true);
		}

		//$ids = array($inspection_id);
		$parent_url = $this->_parent_url($inspection_id);

		if((empty($tank_id_edited_arr) || $edit_result['success_flag_outvar'] == 'Y') 
		&& (empty($tank_id_added_arr) || $add_result['success_flag_outvar'] == 'Y')
		&& ((empty($deleted_tanks_arr) || is_null($deleted_tanks_arr[0])) || $delete_result['flag_outvar'] == 'Y')
		) {
			$this->_go_message($parent_url, "Successfully edited {$this->name}.", 'info_message');
		}else {
			$this->_go_message($parent_url, "Error occurred while editing penalty.");
		}

	}

	public function delete($inspection_id, $penalty_code = null, $tank_id = null) {
		$ids = array($inspection_id, url::kohana_decode($penalty_code), $tank_id);

		$view = new View('generic_delete');
		$view->object_name = ucfirst($this->name);
		$view->delete_url = $this->_delete_action_url($ids);

		// get rows that are about to be deleted
		$where = text::where_pk(array('INSPECTION_ID', 'PENALTY_CODE', 'TANK_ID'), $ids);
		$view->penalties = new Penalties_Model();
		$penalty_rows = $view->penalties->get_list($where);

		// temporarily change '01-JAN/68' to empty string, will fix after UDAPI can accept empty array
		$penalty_rows[0]['PENALTY_OCCURANCE'] = $penalty_rows[0]['PENALTY_OCCURANCE'] == '99999' ? '' : $penalty_rows[0]['PENALTY_OCCURANCE'];
		$penalty_rows[0]['DATE_CORRECTED'] = $penalty_rows[0]['DATE_CORRECTED'] == '01-JAN-68' ? '' : $penalty_rows[0]['DATE_CORRECTED'];
		$penalty_rows[0]['LCC_DATE'] = $penalty_rows[0]['LCC_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['LCC_DATE'];
		$penalty_rows[0]['NOV_DATE'] = $penalty_rows[0]['NOV_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['NOV_DATE'];
		$penalty_rows[0]['NOD_DATE'] = $penalty_rows[0]['NOD_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['NOD_DATE'];
		$penalty_rows[0]['NOIRT_DATE'] = $penalty_rows[0]['NOIRT_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['NOIRT_DATE'];
		$penalty_rows[0]['REDTAG_PLACED_DATE'] = $penalty_rows[0]['REDTAG_PLACED_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['REDTAG_PLACED_DATE'];
		$penalty_rows[0]['LCAV_DATE'] = $penalty_rows[0]['LCAV_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['LCAV_DATE'];
		$penalty_rows[0]['NTRF_DATE'] = $penalty_rows[0]['NTRF_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['NTRF_DATE'];
		$penalty_rows[0]['REDTAG_REMOVED_DATE'] = $penalty_rows[0]['REDTAG_REMOVED_DATE'] == '01-JAN-68' ? '' : $penalty_rows[0]['REDTAG_REMOVED_DATE'];
		$penalty_rows[0]['LCC_DATE_FMT'] = $penalty_rows[0]['LCC_DATE_FMT'] == '01/01/1968' ? '' : $penalty_rows[0]['LCC_DATE_FMT'];
		$penalty_rows[0]['NOV_DATE_FMT'] = $penalty_rows[0]['NOV_DATE_FMT'] == '01/01/1968' ? '' : $penalty_rows[0]['NOV_DATE_FMT'];
		$penalty_rows[0]['NOD_DATE_FMT'] = $penalty_rows[0]['NOD_DATE_FMT'] == '01/01/1968' ? '' : $penalty_rows[0]['NOD_DATE_FMT'];
		$penalty_rows[0]['NOIRT_DATE_FMT'] = $penalty_rows[0]['NOIRT_DATE_FMT'] == '01/01/1968' ? '' : $penalty_rows[0]['NOIRT_DATE_FMT'];
		$penalty_rows[0]['REDTAG_PLACED_DATE_FMT'] = $penalty_rows[0]['REDTAG_PLACED_DATE_FMT'] == '01/01/1968' ? '' : $penalty_rows[0]['REDTAG_PLACED_DATE_FMT'];
		$penalty_rows[0]['DATE_CORRECTED_FMT'] = $penalty_rows[0]['DATE_CORRECTED_FMT'] == '01/01/1968' ? '' : $penalty_rows[0]['DATE_CORRECTED_FMT'];
		$view->rows = $penalty_rows;

		if (method_exists($this, '_delete_pre_render'))
			$this->_delete_pre_render();
		$this->template->content = $view;
	}

	public function delete_action($inspection_id = null, $penalty_code = null, $tank_id = null) {
		// if the tank id doesn't belong to the facility
		$inspection = new Inspections_Model();
		$facility_id = $inspection->get_value("SELECT FACILITY_ID AS VAL FROM ustx.inspections I WHERE I.ID = :INSPECTION_ID", array(':INSPECTION_ID' => $inspection_id));
		$tanks = new Tanks_Model();
		$tank_rows = $tanks->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $facility_id));
		$tank_id_arr = array();
		foreach($tank_rows as $tank_row) {
			array_push($tank_id_arr, $tank_row['ID']);
		}

		// if the penalty doesn't have a tank or the tank id doesn't belong to the facility
		if($tank_id == 0 || !in_array($tank_id, $tank_id_arr)) {
			return(parent::delete_action($inspection_id, url::kohana_decode($penalty_code), $tank_id));
		}

		// if the penalty has tanks
		$ids = array($inspection_id, url::kohana_decode($penalty_code), $tank_id);
		$parent_url = $this->_parent_url(NULL, $ids);

		$model = new Penalties_Model();
		$where = text::where_pk(array('INSPECTION_ID', 'PENALTY_CODE', 'TANK_ID'), $ids);

		$rows = $model->get_list($where);

		if ((! $model->check_priv('DELETE')) || count($rows) > 1) {
			$this->_go_message($parent_url, "Error occurred while deleting {$this->name}.");
		}
		
		$delete_penalty = $model->delete_penalty($inspection_id, $penalty_code, $tank_id);
		$result = json_decode($delete_penalty, true);
		if($result['flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		}else {
			$this->_go_message($parent_url, "Error occurred while deleting penalty.");
		}
	}

}
