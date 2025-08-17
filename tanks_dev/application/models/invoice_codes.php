<?php


Class Invoice_codes_Model extends Model
{
	public $table_name = 'USTX.INVOICE_CODES';
	public $pks = array('CODE');
	public $lookup_code = 'CODE';
	public $lookup_desc = 'DESCRIPTION';

}
