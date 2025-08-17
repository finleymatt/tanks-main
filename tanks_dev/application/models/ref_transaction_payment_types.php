<?php


Class Ref_transaction_payment_types_Model extends Model
{
	public $table_name = 'USTX.REF_TRANSACTION_PAYMENT_TYPES';
	public $lookup_code = 'CODE';
	public $lookup_desc = 'DESCRIPTION';

	/**
	 * Method that returns Transaction payment types
	 * @access public
	 * @return transaction payment type list
	 */
	public function get_payment_types() {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/insp/getpaymenttypelist?flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);
		return $result['result']['out_cur'];
	}
}
