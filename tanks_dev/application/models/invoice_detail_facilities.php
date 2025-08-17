<?php


Class Invoice_detail_facilities_Model extends Model
{
	public $table_name = 'USTX.INVOICE_DETAIL_FACILITIES';

	// not needed becausing using facility_link() public $more_select = array("(select facility_name from ustx.facilities_mvw where id = INVOICE_DETAIL_FACILITIES.FACILITY_ID) FACILITY_NAME");
}
