<?php


Class Financial_providers_Model extends Model
{
	public $table_name = 'USTX.FINANCIAL_PROVIDERS';
	public $lookup_code = 'CODE';
	public $lookup_desc = 'DESCRIPTION';

	/**
	 * Method that returns next financial provider code
	 * @access public
	 * @return next financial provider code
	 */
	public function get_next_code() {
		$largest_code = $this->db->query('SELECT MAX(TO_NUMBER(code)) as CODE FROM USTX.Financial_providers')->as_array();

		return $largest_code[0]['CODE'] + 1;
	}

	/**
	 * Method that returns the financial provider code
	 * @access public
	 * @return next financial provider code
	 */
	public function get_financial_provider_code($financial_provider_name) {
		$fin_prov = $this->db->query('
			SELECT CODE FROM USTX.financial_providers
			WHERE description = :DESCRIPTION'
		, array(':DESCRIPTION' => $financial_provider_name))->as_array();

		return $fin_prov['0']['CODE'];
	}

	/**
	 * Method that returns the financial provider name
	 * @access public
	 * @return next financial provider name
	 */
	public function get_financial_provider_name($financial_provider_code) {
		if(is_null($financial_provider_code) || empty($financial_provider_code)) {
			return '';
		} else {
			$fin_prov = $this->db->query('
				SELECT DESCRIPTION FROM USTX.financial_providers
				WHERE code = :CODE'
			, array(':CODE' => $financial_provider_code))->as_array();

			return $fin_prov['0']['DESCRIPTION'];
		}
	}

	/**
	 * Method that deletes financial providers
	 * @access public
	 * @return 
	 */
	public function delete($financial_provider_codes) {

		foreach($financial_provider_codes as $code) {
			$this->db->query('DELETE FROM USTX.financial_providers where CODE = :CODE', array(':CODE' => $code));
		}

		return TRUE;
	}
}
