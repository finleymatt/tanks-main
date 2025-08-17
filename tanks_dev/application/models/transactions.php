<?php

Class Transactions_Model extends Model
{
	public $table_name = 'USTX.TRANSACTIONS';
	public $pks = array('ID');
	public $parent_pks = array('Owners_mvw' => array('OWNER_ID'));

	public $more_select = array("to_char(TRANSACTION_DATE, 'MM/DD/YYYY') TRANSACTION_DATE_FMT",
		"to_char(DEPOSIT_DATE, 'MM/DD/YYYY') DEPOSIT_DATE_FMT");


	public function get_payable_invoices($owner_id) {
		return($this->db->query("
			SELECT ustx.owner_payments_view.*,
				decode(transaction_type,
					'P','PP',
					'L','LP',
					'I','IP',
					'S','SCP',
					'J','ICP',
					'H','HWEP',
					'G','GWAP',
					NULL) payment_code,
				ustx.get_payment_invoice_id(owner_id, fiscal_year, transaction_type, nov_number) invoice_id
			FROM ustx.owner_payments_view
			WHERE owner_id = :owner_id and amount != 0
			ORDER BY fiscal_year DESC,
				decode(transaction_type, 'L','A' ,'I','B', 'P','C', 'J','D', 'S','E', 'H','F', 'G','G', 'Z') ASC"
			, array(':owner_id' => $owner_id))->as_array());
	}

	public function update($ids, $data) {
		$this->db->set('transaction_date', Model::sql_date_db($data['transaction_date']), FALSE);
		$this->db->set('deposit_date', Model::sql_date_db($data['deposit_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.transaction_seq.NEXTVAL', FALSE);
		$this->db->set('owner_id', $parent_ids[0]);
		$this->db->set('operator_id', "'{$data['operator_id']}'", FALSE); // set as str
		$this->db->set('check_number', "'{$data['check_number']}'", FALSE); // set as str
		$this->db->set('transaction_date', Model::sql_date_db($data['transaction_date']));
		$this->db->set('deposit_date', Model::sql_date_db($data['deposit_date']));
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	public function payment_insert($owner_id, $data) {
		// check if paid is over specified check amount ----------
		if (round(array_sum($data['inv_paid']), 2) > $data['amount'])
			exit('Error: Amounts totaled is greater than the check amount.');

		$row_data['check_number'] = $data['check_number'];
		$row_data['name_on_check'] = $data['name_on_check'];
		$row_data['transaction_date'] = $data['transaction_date'];
		$row_data['deposit_date'] = $data['deposit_date'];
		$row_data['payment_type_code'] = $data['payment_type_code'];
		$row_data['operator_id'] = trim($data['operator_id']);
		$row_data['operator_payment'] = ($data['operator_id'] ? 'Y' : 'N');
		$row_data['comments'] = $data['comments'];

		// loop payments made to invoices ------------------------
		foreach($data['inv_paid'] as $j => $paid) {
			if (! $paid) continue;

			$row_data['invoice_id'] = $data['inv_invoice_id'][$j];
			$row_data['inspection_id'] = $data['inv_inspection_id'][$j];
			$row_data['transaction_code'] = $data['inv_payment_code'][$j];
			$row_data['transaction_status'] = ((floatval($paid) == floatval($data['inv_amount'][$j])) ? 'C' : 'O');
			$row_data['fiscal_year'] = $data['inv_fiscal_year'][$j];
			$row_data['amount'] = $paid;

			if (! $this->insert(array($owner_id), $row_data))
				return(FALSE);
		}
		return(TRUE);
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('transaction_code', 'required');
		$vdata->add_rules('transaction_date', 'required');
		$vdata->add_rules('fiscal_year', 'required');
		$vdata->add_rules('amount', 'required', 'numeric');
		if (! empty($vdata['operator_id']))
			$vdata->add_callbacks('operator_id', array(Model::instance('Operators_mvw'), 'is_valid_id'));
		return($vdata);
	}
}
