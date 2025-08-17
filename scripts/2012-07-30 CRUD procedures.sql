-- ran in eidd, eidt, eidq

create or replace procedure ustx.d_inspection( p_inspection_id in number ) as
/*
 *   Delete an invoice and its associated penalties.
 */
begin
	-- delete any invoices referring to this inspection
	for invoice_record in (
		select id from ustx.invoices where inspection_id = p_inspection_id
	)
	loop
		ustx.invoice.delete_invoice(invoice_record.id);
	end loop;

	-- delete any transactions, penalties referring to this inspection
	delete ustx.transactions where inspection_id = p_inspection_id;
	delete ustx.penalties where inspection_id = p_inspection_id;
	
	delete ustx.inspections where id = p_inspection_id;
	
	commit;
	exception
		when others then
			rollback;
			raise_application_error(-20100, 'd_inspection error: ' || substr(sqlerrm, 1, 2000));
end;
/

GRANT EXECUTE ON USTX.D_INSPECTION TO ONESTOP_USER;
/