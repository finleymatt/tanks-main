<?php


Class Fiscal_years_Model extends Model
{
	public $table_name = 'USTX.FISCAL_YEARS';
	public $lookup_code = 'FISCAL_YEAR';
	public $lookup_desc = 'FISCAL_YEAR';


	// return array of fy: interest rate
	public function get_int_rates() {
		$rows = $this->db->query("
			SELECT fiscal_year, ((monthly_interest_rate / 100) * (144 - ((rownum-1)*12))) interest_rate
			FROM ustx.fiscal_years
			WHERE fiscal_year >= 1979
				and fiscal_year <= to_char(sysdate, 'YYYY') + 2
			ORDER BY fiscal_year ASC")->as_array();

		$result = array();
		foreach($rows as $row)
			$result[$row['FISCAL_YEAR']] = $row['INTEREST_RATE'];

		return($result);
	}
}
