-- ran in eidd, eidt, eidq

CREATE OR REPLACE
FUNCTION USTX.get_payment_invoice_id(
	p_owner_id in number,
	p_fiscal_year in number,
	p_transaction_type in char,
	p_nov_number in number)
RETURN number
IS
-- ML 2013-01-11: Created this function
-- Original sql is from Oracle forms version of Onestop (ust_pay.fmb)
-- , in a post-query trigger of when user clicks on a invoice row.

result_invoice_id number;
  
BEGIN

if p_transaction_type = 'P' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'PA'
		and owner_id = p_owner_id;
elsif p_transaction_type = 'L' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'LA'
		and owner_id = p_owner_id;
elsif p_transaction_type = 'I' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'IA'
		and owner_id = p_owner_id;
elsif p_transaction_type = 'S' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'SCA'
		and owner_id = p_owner_id;
elsif p_transaction_type = 'J' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'ICA'
		and owner_id = p_owner_id;
elsif p_transaction_type = 'G' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'GWAA'
		and owner_id = p_owner_id;
elsif p_transaction_type = 'H' then
	select min(invoice_id) into result_invoice_id
	from ustx.transactions
	where fiscal_year = p_fiscal_year
		and transaction_code = 'HWEA'
		and owner_id = p_owner_id
		and inspection_id in 
			(select inspection_id from ustx.inspections where nov_number = p_nov_number);
end if;

return result_invoice_id;

END get_payment_invoice_id;
/

-- by USTX
GRANT EXECUTE ON ustx.get_payment_invoice_id TO ONESTOP_ROLE;
/