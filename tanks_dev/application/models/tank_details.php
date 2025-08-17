<?php


Class Tank_details_Model extends Model
{
	public $table_name = 'USTX.TANK_DETAILS';
	public $pks = array('TANK_ID', 'TANK_DETAIL_CODE');
	public $parent_pks = array('Tanks' => array('TANK_ID'));

	// rules for auto-selecting tank detail codes per GoNM's request
	public static $GONM_RULES = array(
		'triggers' => array(
			'A04' => array('set'=>'C19', 'message'=>'(A04) FIBERGLASS REINFORCED PLASTIC automatically sets (C19) N/A - Tank'),
			'A26' => array('set'=>'C19', 'message'=>'(A26) STI P3 automatically sets (C19) N/A - Tank'),
			'A30' => array('set'=>'C19', 'message'=>'(A30) ACT100 STEEL FIBERGLASS CLAD automatically sets (C19) N/A - Tank'),
			'A31' => array('set'=>'C19', 'message'=>'(A31) NON-ACT100 STEEL FBRGLS CLAD automatically sets (C19) N/A - Tank'),
			'A06' => array('set'=>'S16', 'message'=>'(A06) DOUBLE WALLED automatically sets (S16) NOT APPLICABLE - TANK'),
			'F09' => array('set'=>'S17', 'message'=>'(F09) DOUBLE WALLED PIPING automatically sets (S17) NOT APPLICABLE - PIPING'),
			'F03' => array('set'=>'C20', 'message'=>'(F03) FIBERGLASS REINFORCED PLASTIC automatically sets (C20) N/A - Piping'),
			'F10' => array('set'=>'C20', 'message'=>'(F10) FLEXIBLE PIPING automatically sets (C20) N/A - Piping')
		),
		'exclusions' => array(
			array('A01', 'A02'),
			array('A01', 'A04'),
			array('A01', 'A05'),
			array('A01', 'A30'),
			array('A02', 'A04')
		)
	);

	/**
 	 * Overridden to include tank_info_code
 	 */
	public function get_list($where=NULL, $orderby=NULL, $bound_vars=array()) {
		$this->db->select(array('TD.TANK_ID', 'TD.TANK_DETAIL_CODE', 'TDC.DESCRIPTION as TANK_DETAIL_DESC', 'TDC.TANK_INFO_CODE', 'TIC.DESCRIPTION as TANK_INFO_DESC', 'TD.USER_CREATED', 'TD.DATE_CREATED'));
		$this->db->from(array("{$this->table_name} TD", 'USTX.TANK_DETAIL_CODES TDC', 'USTX.TANK_INFO_CODES TIC'));
		$this->db->where('TD.TANK_DETAIL_CODE = TDC.CODE and TDC.TANK_INFO_CODE = TIC.CODE');

		if ($where) $this->db->where($where);
		if ($orderby) $this->db->orderby($orderby);
		return($this->db->query(NULL, $bound_vars)->as_array());
	}
	
	/*
	 * Insert tank detail 
	 */
	public function insert($parent_ids, $data) {
		$this->db->set('tank_id', $parent_ids[0]);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));	
	}

	/*
	 * Delete tank detail
	 */
	public function delete_tank_detail($tank_id, $detail_code) {
		$this->db->query('DELETE FROM USTX.tank_details where TANK_ID = :TANK_ID AND TANK_DETAIL_CODE = :CODE', array(':TANK_ID' => $tank_id, ':CODE' => $detail_code));
		return TRUE;
	}

	/*
	 * Get tank detail list by tank ID
	 */
	public function get_tank_detail_list($tank_id) {
		$tank_details = $this->db->query('
			SELECT * FROM USTX.TANK_DETAILS WHERE TANK_ID = :tank_id'
			, array(':tank_id' => $tank_id))->as_array();
		return $tank_details;
	}

	/*
	 * Get tank detail by tank ID and tank detail code
	 */
	public function get_tank_detail($tank_id, $detail_code) {
		$this->db->select("{$this->table_name}.*");
		foreach ($this->more_select as $select)
			$this->db->select($select);
		$this->db->from($this->table_name);
		$this->db->where(array('tank_id' => $tank_id, 'tank_detail_code' => $detail_code));
		$tank_detail = $this->db->query(NULL, array())->as_array();

		return $tank_detail[0];
	}

	/*
	 * Update tank details
	 */
	public function update_all($tank_id, $inputs) {
		if (! $this->check_priv('DELETE')) return(FALSE);
		if (! $this->check_priv('INSERT')) return(FALSE);
		if (! is_numeric($tank_id)) return(FALSE);

		// consolidate all tank detail codes into one array -----------
		$tank_detail_codes = array();
		foreach($inputs as $input)
			array_push($tank_detail_codes, $input);

		$tank_detail_list = $this->get_tank_detail_list($tank_id);
		$tank_detail_codes_existed = array_column($tank_detail_list, 'TANK_DETAIL_CODE');
		$tank_detail_codes_insert = array_diff($tank_detail_codes, $tank_detail_codes_existed);
		$tank_detail_codes_delete = array_diff($tank_detail_codes_existed, $tank_detail_codes);	

		// delete tank detail
		foreach($tank_detail_codes_delete as $code) {
			$this->delete_tank_detail($tank_id, $code);
		}

		// insert tank detail
		foreach($tank_detail_codes_insert as $code) {
			$this->insert(array($tank_id), array('tank_detail_code' => $code));
		}

		return(TRUE);
	}

	public function set_all($tank_id, $tank_detail_codes) {
		if (! $this->check_priv('DELETE')) return(FALSE);
		if (! $this->check_priv('INSERT')) return(FALSE);
		if (! is_numeric($tank_id)) return(FALSE);

		// custom command to delete all rows for this tank in one call
		$this->db->where(array('tank_id' => $tank_id));
		$this->db->delete($this->table_name);
		
		// insert tank details one by one
                foreach($tank_detail_codes as $code)
			$this->insert(array($tank_id), array('tank_detail_code' => $code));

		return(TRUE);
	}
}
