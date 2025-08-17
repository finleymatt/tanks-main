<?php


Class Owners_mvw_Model extends Model
{
	public $table_name = 'USTX.OWNERS_MVW';
	public $pks = array('ID');
	public $lookup_code = 'ID';
	public $lookup_desc = 'OWNER_NAME';


	public function search($fields) {
		$this->db->from($this->table_name);
		$bound_vars = array();
		if ($fields['OWNER_NAME']) {
			$this->db->where($this->_search_syntax('OWNER_NAME'));
			$bound_vars['OWNER_NAME'] = $fields['OWNER_NAME'];
		}
		if ($fields['STREET']) {
			$this->db->where("({$this->_search_syntax('ADDRESS1')} or {$this->_search_syntax('ADDRESS2')})");
			$bound_vars['ADDRESS1'] = $fields['STREET'];
			$bound_vars['ADDRESS2'] = $fields['STREET'];
		}
		if ($fields['CITY']) {
			$this->db->where($this->_search_syntax('CITY'));
			$bound_vars['CITY'] = $fields['CITY'];
		}
		if ($fields['ZIP']) {
			$this->db->where($this->_search_syntax('ZIP'));
			$bound_vars['ZIP'] = $fields['ZIP'];
		}
		if ($fields['STATE']) {
			$this->db->where($this->_search_syntax('STATE'));
			$bound_vars['STATE'] = $fields['STATE'];
		}

		return($this->db->query(NULL, $bound_vars)->as_array());  //auto-generate sql
	}

	/**
 	 * Returns balance owed by the owner specified
 	 */
	public function get_balance_quick($owner_id) {
		return($this->get_value('
			SELECT sum(greatest(principal_assessment - principal_waiver - principal_payment, 0) +
				greatest(late_fee_assessment - late_fee_waiver - late_fee_payment, 0) +
				greatest(interest_assessment - interest_waiver - interest_payment, 0) + refund) VAL
			FROM ustx.owner_transactions_view
			WHERE fiscal_year > 1978
				and owner_id = :OWNER_ID', array(':OWNER_ID' => $owner_id)));
	}

	/**
 	 * Returns balance owed by the owner specified using last FY invoice.
	 * Return value includes other info for determining validity of balance.
	 * If INVOICE_ID and GEN_INVOICE_ID don't match, then balance is not valid.
 	 */
	public function get_balance_summary($owner_id) {
		// balance from the invoice with the most recent FY
		$bal_row = $this->db->query_row("
			SELECT max(invoice_id) INVOICE_ID, max(fiscal_year) INV_FY, sum(sum_balances) BALANCE
			FROM ustx.invoice_detail
			WHERE invoice_id in (select max(I.id) -- use max to get only one
				from ustx.invoices I
					inner join ustx.invoice_detail INVD
						on I.id = INVD.invoice_id
				where I.owner_id = :OWNER_ID
					-- current FY and next FY just in case
					and INVD.fiscal_year between to_char(sysdate, 'YYYY') and (to_char(sysdate, 'YYYY') + 1))"
			, array(':OWNER_ID' => $owner_id));

		// invoice ID from the most recently run invoice
		$fy_row = $this->db->query_row('
			SELECT max(id) INVOICE_ID FROM ustx.invoices
			WHERE owner_id = :OWNER_ID'
			, array(':OWNER_ID' => $owner_id));

		$bal_row['GEN_INVOICE_ID'] = (count($fy_row) ? $fy_row['INVOICE_ID'] : NULL);

		// payments that have been received since the invoice generation
		$bal_row['PAYMENTS'] = $this->db->query("
			SELECT amount, transaction_code, transaction_date
			FROM ustx.transactions
			WHERE owner_id = :OWNER_ID
				AND date_created > (select date_created from ustx.invoices where id = :INVOICE_ID)
				AND transaction_code in ('PP', 'IP', 'LP')"
			, array(':OWNER_ID' => $owner_id, ':INVOICE_ID' => $bal_row['INVOICE_ID']))->as_array();

		$bal_row['QUICK_BALANCE'] = $this->get_balance_quick($owner_id);

		return($bal_row);
	}

	/**
	 * Returns array of balance info calculated from the last invoice generated
	 */
	public function get_balance_inv($owner_id) {
		$balance_summary = $this->get_balance_summary($owner_id);

		return($this->db->query_row("
			SELECT sum(tank_fee_balance) TANK_BALANCE,
				sum(late_fee_balance) LATE_BALANCE,
				sum(interest_balance) INT_BALANCE
			FROM ustx.invoice_detail WHERE invoice_id = :INVOICE_ID"
			, array(':INVOICE_ID' => $balance_summary['INVOICE_ID'])));
	}

	public function is_valid_id($validation, $field) {
		if ($validation[$field]) {
			if (! $this->get_row($validation[$field]))
				$validation->add_error($field, 'Entered Owner ID is incorrect');
		}
	}

	/**
	 * Performs Waive All action, which will create waivers for all FYs
	 * as needed, and then generate invoices.
	 * Returns waivers created.
	 */
	public function waive_all($owner_id, $reason) {
		$success = NULL;
		$this->db->procedure_call("BEGIN ustx.invoice.waive_all(:p_owner_id, :p_reason, :p_success); END;", array(':p_owner_id' => $owner_id, ':p_reason' => $reason, ':p_success' => $success));

		return(Model::instance('Owner_waivers')->get_list(
			"owner_id = :owner_id and user_created = 'WAIVE_ALL'
				and TO_CHAR(date_created) = TO_CHAR(CURRENT_DATE)",
			'fiscal_year', array(':owner_id' => $owner_id)));
	}
}
