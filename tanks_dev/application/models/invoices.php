<?php

Class Invoices_Model extends Model
{
	public $table_name = 'USTX.INVOICES';
	public $pks = array('ID');
	public $parent_pks = array('Owners_mvw' => array('OWNER_ID'));

	public function delete($ids) {
		if (! $this->check_priv('DELETE')) return(FALSE);
		$user_id = Session::instance()->get('UserID');

		return($this->db->procedure_call("BEGIN ustx.invoice.delete_invoice(:p_invoice_id, :p_user_id); END;", array(':p_invoice_id' => $ids[0], ':p_user_id' => $user_id)));  // procedure handles errors itself
	}

	public function generate($owner_id, $fy, $invoice_date, $due_date) {
		if (! $this->check_priv('INSERT')) return(FALSE);
		$user_id = Session::instance()->get('UserID');

		return($this->batch('invoice', array('gen', $owner_id, $fy, $invoice_date, $due_date, $user_id, 'none')));
	}

	public function print_report($owner_id, $fy, $invoice_date, $due_date, $print_opt) {
		$user_id = Session::instance()->get('UserID');
		return($this->batch('invoice', array('print', $owner_id, $fy, $invoice_date, $due_date, $user_id, $print_opt)));
	}

	public function gen_print($owner_id, $fy, $invoice_date, $due_date, $print_opt) {
		if (! $this->check_priv('INSERT')) return(FALSE);
		$user_id = Session::instance()->get('UserID');

		return($this->batch('invoice', array('gen-print', $owner_id, $fy, $invoice_date, $due_date, $user_id, $print_opt)));
	}

	public function is_gpa($invoice_id) {
		$row = $this->get_row($invoice_id);
		return($row['INVOICE_CODE'] == 'GPA');
	}

	public function get_gpa_invoices($owner_id) {
		return($this->get_list(array('invoice_code'=>'GPA', 'owner_id'=>$owner_id)));
	}

	public function gpa_insert($data) {
		$rows = $this->db->query("SELECT ustx.gpa_invoice.insert_gpa_invoice(:p_invoice_date, :p_due_date, :p_nov_gpa_fiscal_year, :p_nov_gpa_amount, :p_nov_gpa_facility_id, :p_staff_code) invoice_id FROM DUAL",
			array(':p_invoice_date' => Model::str_date_db($data['invoice_date']),
				':p_due_date' => Model::str_date_db($data['due_date']),
				':p_nov_gpa_fiscal_year' => $data['nov_gpa_fiscal_year'],
				':p_nov_gpa_amount' => $data['nov_gpa_amount'],
				':p_nov_gpa_facility_id' => $data['nov_gpa_facility_id'],
				':p_staff_code' => Session::instance()->get('UserID')))->as_array();
		return($rows[0]['INVOICE_ID']);  // 0 if error
	}

}
