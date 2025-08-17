<?php


Class Ust_ref_codes_Model extends Model
{
	public $table_name = 'USTX.UST_REF_CODES';
	public $pks = array('RV_LOW_VALUE', 'RV_DOMAIN');
	public $lookup_code = 'RV_LOW_VALUE';
	public $lookup_desc = 'RV_MEANING';

	// overridden to require parameter: $domain
	public function get_dropdown($domain, $dropdown_id=NULL, $dropdown_desc=NULL, $where=NULL, $bound_vars=array()) {
		$this->db->where(array('RV_DOMAIN' => $domain));
		return(parent::get_dropdown($dropdown_id, $dropdown_desc, $where, $bound_vars));
	}

	// overridden to allow for second parameter: $domain
	public function get_lookup_desc($domain, $code, $include_code=TRUE) {
		if ( (! $this->lookup_code) || (! $this->lookup_desc) ) {
			Session::instance()->set('error_message', 'Error occurred during UST_REF_CODES lookup.');
			return('');
		}
		else {
			$row = $this->get_row(array($code, $domain));
			return( ($include_code ? "({$code}) " : '') . $row[$this->lookup_desc] );
		}
	}
}
