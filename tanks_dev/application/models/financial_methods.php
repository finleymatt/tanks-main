<?php


Class Financial_methods_Model extends Model
{
	public $table_name = 'USTX.FINANCIAL_METHODS';
	public $lookup_code = 'CODE';
	public $lookup_desc = 'DESCRIPTION';

	/**
	  * Method that returns the financial method code
	  * @access public
	  * @return next financial method code
	  */
	 public function get_financial_method_code($financial_method) {
		$fin_method = $this->db->query('
			SELECT CODE FROM USTX.financial_methods
			WHERE description = :DESCRIPTION'
		, array(':DESCRIPTION' => $financial_method))->as_array();

		return $fin_method['0']['CODE'];
	 }

	 /**
	  * Method that returns the financial method
	  * @access public
	  * @return next financial method
	  */
	 public function get_financial_method($financial_method_code) {
	 	if(is_null($financial_method_code) || empty($financial_method_code)) {
			return '';
		} else {
			$fin_method = $this->db->query('
				SELECT DESCRIPTION FROM USTX.financial_methods
				WHERE code = :CODE'
			, array(':CODE' => $financial_method_code))->as_array();
	
			return $fin_method['0']['DESCRIPTION'];
		}
	 }
}
