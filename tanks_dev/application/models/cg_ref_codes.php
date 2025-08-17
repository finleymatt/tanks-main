<?php


Class Cg_ref_codes_Model extends Model
{
	public $table_name = 'LUST.CG_REF_CODES';
	public $pks = array('RV_DOMAIN', 'RV_LOW_VALUE');
/********
	public function get_row($rv_domain, $rv_low_value)
	{
		$rows = $this->db->query('SELECT * FROM LUST.CG_REF_CODES WHERE (RV_LOW_VALUE = :RV_LOW_VALUE) and (rv_domain = :RV_DOMAIN)', array(':RV_DOMAIN' => $rv_domain, ':RV_LOW_VALUE' => $rv_low_value))->as_array();
		if (count($rows))
			return($rows[0]);
		else
			return(NULL);
	}
*********/

}
