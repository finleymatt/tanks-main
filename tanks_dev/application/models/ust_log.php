<?php


Class Ust_log_Model extends Model
{
	public $table_name = 'USTX.UST_LOG';
	public $pks = array('PROCESS_ID', 'LOG_TEXT');


	public function is_batch_running() {
		$latest_invoice = $this->latest_invoice();
		$latest_permit = $this->permit_print_status();

		// still running, if either of the finish_timestamp is blank
		return(!isset($latest_invoice['row']['FINISH_TIMESTAMP'])
			|| !isset($latest_permit['FINISH_TIMESTAMP']));
	}

	public function latest_invoice() {
		$gen_row = $this->invoice_gen_status();
		$print_row = $this->invoice_print_status();

		if ($gen_row['PROCESS_ID'] > $print_row['PROCESS_ID'])
			return(array('type' => 'gen', 'row' => $gen_row));
		else
			return(array('type' => 'print', 'row' => $print_row));
	}

	public function invoice_gen_status() {
		$begin_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where log_text like 'New invoice begi%')
				AND log_text like 'New invoice begi%'")->as_array();

		if (! count($begin_rows)) return(NULL);

		$finished_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where process_id = {$begin_rows[0]['PROCESS_ID']})
				AND log_text like 'New invoice finish%'")->as_array();

		$result = $begin_rows[0];
		$result['BEGIN_TIMESTAMP'] = (count($begin_rows) ? $begin_rows[0]['LOG_TIMESTAMP'] : NULL);
		$result['FINISH_TIMESTAMP'] = (count($finished_rows) ? $finished_rows[0]['LOG_TIMESTAMP'] : NULL);

		list(, , , $result['OWNER_ID'], $result['INVOICE_DATE'], $result['DUE_DATE'], $result['FY']) = explode(' ', $begin_rows[0]['LOG_TEXT']);

		return($result);
	}

	/**
	 * Uses messy string parsing of the DB logs in an attempt to leave the logging
	 * process intact.
	 **/
	public function invoice_print_status() {
		$begin_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where log_text like 'Batch invoice print start%')
				AND log_text like 'Batch invoice print start%'")->as_array();

		if (! count($begin_rows)) return(NULL);

		$finished_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where process_id = {$begin_rows[0]['PROCESS_ID']})
				AND log_text like 'Batch invoice print finish%'")->as_array();

		$result = $begin_rows[0];
		$result['BEGIN_TIMESTAMP'] = (count($begin_rows) ? $begin_rows[0]['LOG_TIMESTAMP'] : NULL);
		if (count($finished_rows)) {
			$result['FINISH_TIMESTAMP'] = $finished_rows[0]['LOG_TIMESTAMP'];
			$finished_row = explode(' ', $finished_rows[0]['LOG_TEXT']);
			$result['COUNT'] = $finished_row[8]; // 8 => # of printed invoices
			$result['INVOICE_ID'] = $finished_row[9];
		}
		else {
			$result['FINISH_TIMESTAMP'] = NULL;
			$result['COUNT'] = 0;
			$result['INVOICE_ID'] = 0;
		}

		// log text: "Batch invoice print start owner_id invoice_date due_date fy"
		list(, , , , $result['OWNER_ID'], $result['INVOICE_DATE'], $result['DUE_DATE'], $result['FY']) = explode(' ', $begin_rows[0]['LOG_TEXT']);

		return($result);
	}


	/**
	 * Get latest permit print status.
	 * For permits, only print status is needed because print auto-generates
	 * new permits if necessary.
	 **/
	public function permit_print_status() {
		$begin_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where log_text like 'New_cert_start%')
				AND log_text like 'New_cert_start%'")->as_array();
		if (! count($begin_rows)) return(NULL);

		$finished_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where process_id = {$begin_rows[0]['PROCESS_ID']})
				AND log_text like 'New_cert_finish%'")->as_array();

		$result = $begin_rows[0];
		$result['BEGIN_TIMESTAMP'] = (count($begin_rows) ? $begin_rows[0]['LOG_TIMESTAMP'] : NULL);
		if (count($finished_rows)) {
			$result['FINISH_TIMESTAMP'] = $finished_rows[0]['LOG_TIMESTAMP'];
			$finished_row = explode(' ', $finished_rows[0]['LOG_TEXT']);
			$result['COUNT'] = $finished_row[5]; // 5 => # of printed certificates
		}
		else {
			$result['FINISH_TIMESTAMP'] = NULL;
			$result['COUNT'] = 0;
		}

		list(, $result['OWNER_ID'], $result['FACILITY_ID'], $result['DATE_PERMITTED'], $result['FY']) = explode(' ', $begin_rows[0]['LOG_TEXT']);

		return($result);
	}

	/**
	 * Get latest A/B Operator Letters print status.
	 **/
	public function ab_operator_print_status() {
		$begin_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where log_text like 'New_abop_start%')
				AND log_text like 'New_abop_start%'")->as_array();
		if (! count($begin_rows)) return(NULL);

		$finished_rows = $this->db->query("SELECT * FROM {$this->table_name}
			WHERE process_id = (select max(process_id) from {$this->table_name}
				where process_id = {$begin_rows[0]['PROCESS_ID']})
				AND log_text like 'New_abop_finish%'")->as_array();

		$result = $begin_rows[0];
		$result['BEGIN_TIMESTAMP'] = (count($begin_rows) ? $begin_rows[0]['LOG_TIMESTAMP'] : NULL);
		if (count($finished_rows)) {
			$result['FINISH_TIMESTAMP'] = $finished_rows[0]['LOG_TIMESTAMP'];
			$finished_row = explode(' ', $finished_rows[0]['LOG_TEXT']);
			$result['COUNT'] = $finished_row[1]; // 1 => ind of printed letters
		}
		else {
			$result['FINISH_TIMESTAMP'] = NULL;
			$result['COUNT'] = 0;
		}

		return($result);
	}

	public function reset_invoice() {
		return($this->db->query("DELETE FROM ustx.ust_log
			WHERE process_id =
				(select max(process_id) from ustx.ust_log
				where log_text like 'Batch invoice print start%')"));
	}

	public function reset_permit() {
		return($this->db->query("DELETE FROM ustx.ust_log
			WHERE process_id =
				(select max(process_id) from ustx.ust_log
				where log_text like 'New_cert_start%')"));
	}

	public function reset_ab_operator() {
		return($this->db->query("DELETE FROM ustx.ust_log
			WHERE process_id =
				(select max(process_id) from ustx.ust_log
				where log_text like 'New_abop_start%')"));
	}
}
