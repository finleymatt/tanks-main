create or replace package      ustx.invoice
as
  procedure main (
      p_owner_id               in       number,
      p_invoice_date           in       date,
      p_due_date               in       date,
      p_fiscal_year            in       number,
      p_invoice_no             out      number);
   function generate (
      p_owner_id               in       number,
      p_invoice_date           in       date,
      p_due_date               in       date,
      p_fiscal_year            in       number)
      return number;
   function insert_invoice (
      p_owner_id               in       number,
      p_invoice_date           in       date,
      p_due_date               in       date)
      return number;
   function check_waiver(
      p_owner_id               in       number,
      p_fiscal_year            in       number,
      p_waiver_code            in       varchar2)
      return number;
   function count_facility_tanks(
      p_owner_id               in       number,
      p_facility_id            in       number,
      p_fiscal_year            in       number)
      return number;
   function count_new_tanks(
      p_owner_id               in       number,
      p_facility_id            in       number,
      p_fiscal_year            in       number,
      p_due_date               in       date)
      return number;
   function months_for_interest(
      p_fiscal_year            in       number)
      return number;
   function late_payments(
      p_owner_id               in       number,
      p_fiscal_year            in       number,
      p_current_fiscal_year    in       number)
      return number;
   procedure insert_log(
      p_process_id             in       number,
      p_log_text               in       varchar2);
   procedure transactions_query(
      p_owner_id               in       number,
      p_fiscal_year            in       number,
      p_invoice_due_date       in       date,
      p_tank_fee_invoiced      out      number,
      p_tank_fee_payments      out      number,
      p_tank_fee_waiver        out      number,
      p_late_fee_invoiced      out      number,
      p_late_fee_payments      out      number,
      p_late_fee_waiver        out      number,
      p_interest_invoiced      out      number,
      p_interest_payments      out      number,
      p_interest_waiver        out      number,
      p_payments_prior_due_date out     number);
   procedure insert_transaction(
      p_owner_id               in       number,
      p_invoice_id             in       number,
      p_transaction_code       in       varchar2,
      p_transaction_status     in       varchar2,
      p_transaction_date       in       date,
      p_fiscal_year            in       number,
      p_amount                 in       number);
   procedure insert_invoice_detail(
      p_invoice_id             in       number,
      p_fiscal_year            in       number,
      p_tank_count             in       number,
      p_tank_fee               in       number,
      p_tank_fee_invoiced      in       number,
      p_tank_fee_waiver        in       number,
      p_tank_fee_waiver_used   in       number,
      p_tank_fee_payment       in       number,
      p_tank_fee_balance       in       number,
      p_late_fee               in       number,
      p_late_fee_invoiced      in       number,
      p_late_fee_waiver        in       number,
      p_late_fee_waiver_used   in       number,
      p_late_fee_payment       in       number,
      p_late_fee_balance       in       number,
      p_interest               in       number,
      p_interest_invoiced      in       number,
      p_interest_months        in       number,
      p_interest_waiver        in       number,
      p_interest_waiver_used   in       number,
      p_interest_payment       in       number,
      p_interest_balance       in       number);
   procedure insert_inv_detail_facilities(
      p_invoice_id             in       number,
      p_fiscal_year            in       number,
      p_facility_id            in       number,
      p_tank_count             in       number);
   procedure delete_invoice(
      p_invoice_id             in       number);
   procedure waive_all (
      p_owner_id               in       number,
      p_reason                 in       varchar2,
      p_success                out      number);
	
end;
/


--------------------------------------------------------
--  DDL for Package Body INVOICE
--------------------------------------------------------

CREATE OR REPLACE PACKAGE BODY "USTX"."INVOICE" 
as
-- ML: Apr 2, 2012 - fixed interest and late fee overcharge when invoicing for past FY
-- ML: Apr 2, 2012 - added emergency generator tank exclusion for FY 2002-2007 logic
-- ML: May 18 2012 - imported delete_invoice procedure from ust_gen.fmb into this package.
-- ML: July 25 2012 - reverted to payments_prior_due_date in interest / late fee calculation.
-- ML: Mar 23 2017 - added waive_all procedure
debug_flag boolean := FALSE;
batch_flag boolean;
process_id number := 0;
invoice_count number;
sql_errm varchar2(2048);

procedure main (
	p_owner_id               in       number,
	p_invoice_date           in       date,
	p_due_date               in       date,
	p_fiscal_year            in       number,
	p_invoice_no             out      number)
is
	tank_count number;
	transaction_balance number;
begin
	select ustx.log_seq.nextval into process_id from dual;

	insert_log( process_id, 'New invoice begin ' || to_char(p_owner_id) || ' '
		|| to_char(p_invoice_date, 'mm-dd-yyyy') || ' '
		|| to_char(p_due_date, 'mm-dd-yyyy') || ' '
		|| to_char(p_fiscal_year) || ' ' );

	invoice_count := 0;
	
	-- generate invoice for one selected Owner *****************************************
	if p_owner_id <> 0 then
		batch_flag := FALSE;
		p_invoice_no := generate (p_owner_id, p_invoice_date, p_due_date, p_fiscal_year);
	
	-- generate invoice for all Owners - batch mode (p_owner_id = 0) *******************
	else
		batch_flag := TRUE;
		for owner_record in 
			( select id, owner_name from ustx.owners_mvw owners order by owner_name )
		loop
			-- see if we have a reason to print an invoice by checking tank count
			tank_count := 0;
			select count(*) into tank_count from ustx.tanks
			where owner_id = owner_record.id
				and facility_id in (
					select id
					from ustx.facilities_mvw
					where facility_id = tanks.facility_id
						and nvl(indian, 'N') = 'N'
				)
				and tank_status_code in ('1','2','3','6','11');
			-- check the transaction balance
			transaction_balance := 0;
			for transaction_record in (
				select transaction_code, amount
				from ustx.transactions
				where owner_id = owner_record.id
					and fiscal_year > 1978
					and fiscal_year <= p_fiscal_year
			)
			loop
				if transaction_record.transaction_code in ('IA','LA','PA','R') then
					transaction_balance := transaction_balance + transaction_record.amount;
				elsif transaction_record.transaction_code in ('IP','IW','LP','LW','PP','PW') then
					transaction_balance := transaction_balance - transaction_record.amount;
				end if;
			end loop;

			if tank_count > 0 or transaction_balance > 0 then
				p_invoice_no := generate (owner_record.id, p_invoice_date, p_due_date, p_fiscal_year);
			end if;
		end loop;
	end if;
	
	insert_log( process_id, 'New invoice finish invoices generated = ' || to_char(invoice_count)
		|| ' invoice number ' || to_char(p_invoice_no));
end main;

function generate (
	p_owner_id               in       number,
	p_invoice_date           in       date,
	p_due_date               in       date,
	p_fiscal_year            in       number)
	return number
is
	invoice_id number;
	fiscal_year_tank_count number;
	fiscal_year_tank_fee number;
	fiscal_year_late_fee number;
	fiscal_year_interest number;
	tank_fee_invoiced number;
	tank_fee_payments number;
	transaction_tank_fee_waiver number;
	late_fee_invoiced number;
	late_fee_payments number;
	transaction_late_fee_waiver number;
	interest_invoiced number;
	interest_payments number;
	transaction_interest_waiver number;
	payments_prior_due_date number;
	tank_fee_balance number;
	tank_fee_waiver number;
	interest_balance number;
	interest_waiver number;
	late_fee_balance number;
	late_fee_waiver number;
	months_delinquent number;
	balance_for_late_fee number;
	prior_balance_for_late_fee number;
	tank_count number;
	newly_installed_tank_count number;
	tank_fee_dif number;
	late_fee_dif number;
	months_delinquent_interest number;
	balance_for_interest number;
	interest_fee_dif number;
	delete_invoice_id number;
	late_payments_sum number;
begin
	-- loop for each Owner *************************************************************
	for owner_record in (
		-- get all unique owners that has a tank
		select id, owner_name
		from ustx.owners_mvw
		where id = p_owner_id
			and id in (
				select owner_id
				from ustx.tanks
				where facility_id in (select facility_id
					from ustx.facilities_mvw
					where nvl(indian, 'N') = 'N')
			)
		
		union
		-- get all unique owners that has a permit
		select id, owner_name
		from ustx.owners_mvw
		where id = p_owner_id
			and id in (
				select owner_id
				from ustx.permits
				where facility_id in (select facility_id
					from ustx.facilities_mvw
					where nvl(indian, 'N') = 'N')
		)
	)
	loop
		if debug_flag then
			insert_log( process_id, 'owner ' || to_char(owner_record.id, '99999') || '  ' || owner_record.owner_name );
		end if;
	
		invoice_id := insert_invoice( owner_record.id, p_invoice_date, p_due_date );
	
		prior_balance_for_late_fee := 0;
		
		-- loop for each prior FY, excluding selected FY ********************************
		for fiscal_year_record in (
			select fiscal_year, start_date, end_date, fee_per_tank, monthly_interest_rate, latefee_percentage, latefee_minimum, invoice_due_date
			from ustx.fiscal_years
			where fiscal_year < p_fiscal_year and fiscal_year > 1978
			order by fiscal_year
		)
		loop
			fiscal_year_tank_count := 0;
			fiscal_year_tank_fee := 0;
			fiscal_year_late_fee := 0;
			fiscal_year_interest := 0;
			tank_fee_invoiced := 0;
			tank_fee_payments := 0;
			transaction_tank_fee_waiver := 0;
			late_fee_invoiced := 0;
			late_fee_payments := 0;
			transaction_late_fee_waiver := 0;
			interest_invoiced := 0;
			interest_payments := 0;
			transaction_interest_waiver := 0;
			payments_prior_due_date := 0;
			tank_fee_balance :=0;
			tank_fee_waiver := 0;
			interest_balance := 0;
			interest_waiver := 0;
			late_fee_balance := 0;
			late_fee_waiver := 0;
			months_delinquent := 0;
			late_payments_sum := 0;
			
			-- get all transaction amounts for the looped FY
			transactions_query( owner_record.id, fiscal_year_record.fiscal_year, fiscal_year_record.invoice_due_date,
				tank_fee_invoiced, tank_fee_payments, transaction_tank_fee_waiver, late_fee_invoiced,
				late_fee_payments, transaction_late_fee_waiver, interest_invoiced, interest_payments,
				transaction_interest_waiver, payments_prior_due_date );
			
			-- tank fee calculation *****************************************************
			fiscal_year_tank_count := round(tank_fee_invoiced/fiscal_year_record.fee_per_tank,0);
			tank_fee_balance := tank_fee_invoiced - tank_fee_payments;
			if tank_fee_balance > 0 then
				tank_fee_balance := tank_fee_balance - transaction_tank_fee_waiver;
				if tank_fee_balance < 0 then
					tank_fee_balance := 0;
				end if;
			end if;
			
			-- use waivers from ustx.owner_waivers if exist *****************************
			if tank_fee_balance > 0 then
				tank_fee_waiver := check_waiver( owner_record.id, fiscal_year_record.fiscal_year, 'PW' );
				-- check to see if tank_fee_waiver is greater than the transaction waiver
				-- if so, 1) subtract difference from the balance 2) zero the balance if negative 3) create transaction with difference
				if tank_fee_waiver > transaction_tank_fee_waiver then
					tank_fee_waiver := tank_fee_waiver - transaction_tank_fee_waiver;
					tank_fee_balance := tank_fee_balance - tank_fee_waiver;  -- step 1)
					if tank_fee_balance < 0 then
						tank_fee_waiver := tank_fee_balance + tank_fee_waiver;
						tank_fee_balance := 0;  -- step 2)
					end if;
					insert_transaction( owner_record.id, invoice_id, 'PW', 'O', p_invoice_date, fiscal_year_record.fiscal_year, tank_fee_waiver);  -- step 3)
					transaction_tank_fee_waiver := transaction_tank_fee_waiver + tank_fee_waiver;
				end if;
			end if;

			-- interest calculation *****************************************************
			interest_balance := interest_invoiced - interest_payments;
			if interest_balance > 0 then
				interest_balance := interest_balance - transaction_interest_waiver;
				if interest_balance < 0 then
					interest_balance := 0;
				end if;
			end if;
			
			-- use waivers from ustx.owner_waivers if exist *****************************
			if interest_balance > 0 then
				interest_waiver := check_waiver( owner_record.id, fiscal_year_record.fiscal_year, 'IW' );
				-- check to see if interest_waiver is greater than the transaction waiver
				-- if so, 1) subtract difference from the balance 2) zero the balance if negative 3) create transaction with difference
				if interest_waiver > transaction_interest_waiver then
					interest_waiver := interest_waiver - transaction_interest_waiver;
					interest_balance := interest_balance - interest_waiver;
					if interest_balance < 0 then
						interest_waiver := interest_balance + interest_waiver;
						interest_balance := 0;
					end if;
					insert_transaction( owner_record.id, invoice_id, 'IW', 'O', p_invoice_date, fiscal_year_record.fiscal_year, interest_waiver);
					transaction_interest_waiver := transaction_interest_waiver + interest_waiver;
				end if;
			end if;

			-- late fee calculation *****************************************************
			late_fee_balance := late_fee_invoiced - late_fee_payments;
			if late_fee_balance > 0 then
				late_fee_balance := late_fee_balance - transaction_late_fee_waiver;
				if late_fee_balance < 0 then
					late_fee_balance := 0;
				end if;
			end if;
			if late_fee_balance > 0 then
				late_fee_waiver := check_waiver( owner_record.id, fiscal_year_record.fiscal_year, 'LW' );
				-- check to see if late_fee_waiver is greater than the transaction waiver
				-- if so, 1) subtract difference from the balance 2) zero the balance if negative 3) create transaction with difference
				if late_fee_waiver > transaction_late_fee_waiver then
					late_fee_waiver := late_fee_waiver - transaction_late_fee_waiver;
					late_fee_balance := late_fee_balance - late_fee_waiver;
					if late_fee_balance < 0 then
						late_fee_waiver := late_fee_balance + late_fee_waiver;
						late_fee_balance := 0;
					end if;
					insert_transaction( owner_record.id, invoice_id, 'LW', 'O', p_invoice_date, fiscal_year_record.fiscal_year, late_fee_waiver);
					transaction_late_fee_waiver := transaction_late_fee_waiver + late_fee_waiver;
				end if;
			end if;
		
			-- if fees exist, insert into ustx.invoice_detail with all the info calculated
			if tank_fee_balance + late_fee_balance + interest_balance <> 0 then
				insert_invoice_detail( invoice_id,
					fiscal_year_record.fiscal_year,
					fiscal_year_tank_count,
					fiscal_year_tank_fee,
					tank_fee_invoiced,
					transaction_tank_fee_waiver,
					transaction_tank_fee_waiver,
					tank_fee_payments,
					tank_fee_balance,
					fiscal_year_late_fee,
					late_fee_invoiced,
					transaction_late_fee_waiver,
					transaction_late_fee_waiver,
					late_fee_payments,
					late_fee_balance,
					fiscal_year_interest,
					interest_invoiced,
					months_delinquent,
					transaction_interest_waiver,
					transaction_interest_waiver,
					interest_payments,
					interest_balance );
			end if;
			
			-- special rule if selected fiscal years is > 2002 :
			-- sum balances from all prior years for computation of late fees
			-- if payments were made after due date for the fiscal year, then they need to be added back in
			if p_fiscal_year > 2002 then
				late_payments_sum := late_payments(owner_record.id, fiscal_year_record.fiscal_year, p_fiscal_year);
				if tank_fee_balance + late_fee_balance + interest_balance + late_payments_sum > 0 then
					prior_balance_for_late_fee := prior_balance_for_late_fee + tank_fee_balance + late_fee_balance + interest_balance + late_payments_sum;
				end if;
			end if;
			if debug_flag then
				insert_log( process_id, '1. Balance for Late Fee: ' || to_char(round(prior_balance_for_late_fee,2)) ||
					' Tank Fee Balance: ' || to_char(round(tank_fee_balance,2)) ||
					' Late Fee Balance: ' || to_char(round(late_fee_balance,2)) ||
					' Interest Balance: ' || to_char(round(interest_balance,2)) ||
					' Late Payments: '    || to_char(round(late_payments_sum,2)) ||
					' Fiscal Year: ' || to_char(fiscal_year_record.fiscal_year) );
			end if;
		end loop;  -- end of loop for prior FY transactions

		-- loop for selected FY *************************************************************
		for fiscal_year_record in (
			select fiscal_year, start_date, end_date, fee_per_tank, monthly_interest_rate, latefee_percentage, latefee_minimum, invoice_due_date
			from ustx.fiscal_years
			where fiscal_year = p_fiscal_year and fiscal_year > 1978
		)
		loop
			fiscal_year_tank_count := 0;
			fiscal_year_tank_fee := 0;
			fiscal_year_late_fee := 0;
			fiscal_year_interest := 0;
			tank_fee_invoiced := 0;
			tank_fee_payments := 0;
			transaction_tank_fee_waiver := 0;
			late_fee_invoiced := 0;
			late_fee_payments := 0;
			transaction_late_fee_waiver := 0;
			interest_invoiced := 0;
			interest_payments := 0;
			transaction_interest_waiver := 0;
			payments_prior_due_date := 0;
			tank_fee_balance :=0;
			tank_fee_waiver := 0;
			interest_balance := 0;
			interest_waiver := 0;
			late_fee_balance := 0;
			late_fee_waiver := 0;
			months_delinquent := 0;
			balance_for_late_fee := 0;
			tank_count := 0;
			newly_installed_tank_count := 0;
			tank_fee_dif := 0;
			late_fee_dif := 0;
			months_delinquent_interest := 0;
			balance_for_interest := 0;
			interest_fee_dif := 0;
			
			-- get all Facilities/Tanks for this Owner and find FY tank fee
			for facility in (
				select distinct fac.id, facility_name
				from ustx.tanks tan, ustx.facilities_mvw fac
				where tan.facility_id = fac.id
					and  nvl(fac.indian, 'N') = 'N'
					and (tan.facility_id in ( select facility_id
						from ustx.permits
						where owner_id = owner_record.id
							and fiscal_year = fiscal_year_record.fiscal_year
							and tanks > 0 )
					or tan.owner_id = owner_record.id )
			)
			loop
				tank_count := count_facility_tanks( owner_record.id, facility.id, fiscal_year_record.fiscal_year);
				fiscal_year_tank_count := fiscal_year_tank_count + tank_count;
				insert_inv_detail_facilities( invoice_id, fiscal_year_record.fiscal_year, facility.id, tank_count );
				newly_installed_tank_count := newly_installed_tank_count + count_new_tanks( owner_record.id, facility.id, fiscal_year_record.fiscal_year, p_due_date );
			end loop;  -- end of facility loop
			
			fiscal_year_tank_fee := (fiscal_year_tank_count * fiscal_year_record.fee_per_tank);

			transactions_query( owner_record.id,
				fiscal_year_record.fiscal_year,
				fiscal_year_record.invoice_due_date,
				tank_fee_invoiced,
				tank_fee_payments,
				transaction_tank_fee_waiver,
				late_fee_invoiced,
				late_fee_payments,
				transaction_late_fee_waiver,
				interest_invoiced,
				interest_payments,
				transaction_interest_waiver,
				payments_prior_due_date );
			
			if debug_flag then
				insert_log( process_id, '3. Fiscal Year: ' || to_char(fiscal_year_record.fiscal_year) ||
					' Tank Count: ' || to_char(fiscal_year_tank_count) ||
					' Tank Fee: ' || to_char(round(fiscal_year_tank_fee,2)) ||
					' Tank Fee Invoiced: ' || to_char(round(tank_fee_invoiced,2)) ||
					' Late Fee Invoiced: ' || to_char(round(late_fee_invoiced,2)) ||
					' Balance for Late Fee: ' || to_char(round(balance_for_late_fee,2)) );
			end if;

			-- calculate tank fee balance after payments and waivers
			tank_fee_balance := fiscal_year_tank_fee - tank_fee_payments;
			if tank_fee_balance > 0 then
				tank_fee_balance := tank_fee_balance - transaction_tank_fee_waiver;
				if tank_fee_balance < 0 then
					tank_fee_balance := 0;
				end if;
			end if;
		
			if tank_fee_balance > 0 then
				tank_fee_waiver := check_waiver( owner_record.id, fiscal_year_record.fiscal_year, 'PW' );
				-- check to see if tank_fee_waiver is greater than the transaction waiver
				-- if so, 1) subtract difference from the balance 2) zero the balance if negative 3) create transaction with difference
				if tank_fee_waiver > transaction_tank_fee_waiver then
					tank_fee_waiver := tank_fee_waiver - transaction_tank_fee_waiver;
					tank_fee_balance := tank_fee_balance - tank_fee_waiver;
					if tank_fee_balance < 0 then
						tank_fee_waiver := tank_fee_balance + tank_fee_waiver;
						tank_fee_balance := 0;
					end if;
					insert_transaction( owner_record.id, invoice_id, 'PW', 'O', p_invoice_date, fiscal_year_record.fiscal_year, tank_fee_waiver);
					transaction_tank_fee_waiver := transaction_tank_fee_waiver + tank_fee_waiver;
				end if;
			end if;

			if fiscal_year_tank_fee <> tank_fee_invoiced then
				tank_fee_dif := fiscal_year_tank_fee - tank_fee_invoiced;
				insert_transaction( owner_record.id, invoice_id, 'PA', 'O', p_invoice_date, fiscal_year_record.fiscal_year, tank_fee_dif);
			end if;

			-- compute interest only if selected FY < 2003 ******************************************
			if p_fiscal_year < 2003 then
				months_delinquent_interest := months_for_interest(fiscal_year_record.fiscal_year);
				balance_for_interest := fiscal_year_tank_fee
					- payments_prior_due_date
					- (newly_installed_tank_count * fiscal_year_record.fee_per_tank)
					- transaction_tank_fee_waiver;
				if balance_for_interest > 0 then
					fiscal_year_interest := round(balance_for_interest * (fiscal_year_record.monthly_interest_rate/100)
					* months_delinquent_interest,2);
					interest_balance := fiscal_year_interest - interest_payments;
					
					if interest_balance > 0 then
						interest_balance := interest_balance - transaction_interest_waiver;
						if interest_balance < 0 then
							interest_balance := 0;
						end if;
					end if;
					
					if interest_balance > 0 then
						interest_waiver := check_waiver( owner_record.id, fiscal_year_record.fiscal_year, 'IW' );
						-- check to see if interest_waiver is greater than the transaction waiver
						-- if so, 1) subtract difference from the balance 2) zero the balance if negative 3) create transaction with difference
						if interest_waiver > transaction_interest_waiver then
							interest_waiver := interest_waiver - transaction_interest_waiver;
							interest_balance := interest_balance - interest_waiver;
							if interest_balance < 0 then
								interest_waiver := interest_balance + interest_waiver;
								interest_balance := 0;
							end if;
							insert_transaction( owner_record.id, invoice_id, 'IW', 'O', p_invoice_date, fiscal_year_record.fiscal_year, interest_waiver);
							transaction_interest_waiver := transaction_interest_waiver + interest_waiver;
						end if;
					end if;
				end if;  -- end of interest computations for balance_for_interest > 0

				if fiscal_year_interest <> interest_invoiced then
					interest_fee_dif := fiscal_year_interest - interest_invoiced;
					insert_transaction( owner_record.id, invoice_id, 'IA', 'O', p_invoice_date, fiscal_year_record.fiscal_year, interest_fee_dif);
				end if;
			end if;  -- end of interest computations based on p_fiscal_year < 2003

			-- compute late fees *********************************************************
			months_delinquent := round(months_between(p_invoice_date, fiscal_year_record.invoice_due_date), 2);
			if months_delinquent < 0 then
				months_delinquent := 0;
			end if;
			
			if months_delinquent > 0 then
				-- it is possible that if an owner either overpaid prior to due date or paid for tank that
				-- later went exempt that a new install might not get a late fee
				balance_for_late_fee := fiscal_year_tank_fee
					- payments_prior_due_date
					- (newly_installed_tank_count * fiscal_year_record.fee_per_tank)
					- transaction_tank_fee_waiver;
				if balance_for_late_fee > 0 then
					balance_for_late_fee := balance_for_late_fee + prior_balance_for_late_fee;
				else
					balance_for_late_fee := prior_balance_for_late_fee;
				end if;
				
				if balance_for_late_fee > 0 then
					fiscal_year_late_fee := round(balance_for_late_fee * fiscal_year_record.latefee_percentage/100,2);
					if fiscal_year_late_fee < fiscal_year_record.latefee_minimum then
						fiscal_year_late_fee := fiscal_year_record.latefee_minimum;
					end if;
					late_fee_balance := fiscal_year_late_fee - late_fee_payments;
					if late_fee_balance > 0 then
						late_fee_balance := late_fee_balance - transaction_late_fee_waiver;
						if late_fee_balance < 0 then
							late_fee_balance := 0;
						end if;
					end if;

					if late_fee_balance > 0 then
						late_fee_waiver := check_waiver( owner_record.id, fiscal_year_record.fiscal_year, 'LW' );
						-- check to see if late_fee_waiver is greater than the transaction waiver
						-- if so, 1) subtract difference from the balance 2) zero the balance if negative 3) create transaction with difference
						if late_fee_waiver > transaction_late_fee_waiver then
							late_fee_waiver := late_fee_waiver - transaction_late_fee_waiver;
							late_fee_balance := late_fee_balance - late_fee_waiver;
							if late_fee_balance < 0 then
								late_fee_waiver := late_fee_balance + late_fee_waiver;
								late_fee_balance := 0;
							end if;
							insert_transaction( owner_record.id, invoice_id, 'LW', 'O', p_invoice_date, fiscal_year_record.fiscal_year, late_fee_waiver);
							transaction_late_fee_waiver := transaction_late_fee_waiver + late_fee_waiver;
						end if;
					end if;
				end if;  --end of late fee computations based on balance_for_late_fee > 0
			end if;  -- end of late fee computations based on months_delinquent > 0

			if fiscal_year_late_fee <> late_fee_invoiced then
				late_fee_dif := fiscal_year_late_fee - late_fee_invoiced;
				insert_transaction( owner_record.id, invoice_id, 'LA', 'O', p_invoice_date, fiscal_year_record.fiscal_year, late_fee_dif);
			end if;
			
			insert_invoice_detail( invoice_id,
				fiscal_year_record.fiscal_year,
				fiscal_year_tank_count,
				fiscal_year_tank_fee,
				tank_fee_invoiced,
				transaction_tank_fee_waiver,
				transaction_tank_fee_waiver,
				tank_fee_payments,
				tank_fee_balance,
				fiscal_year_late_fee,
				late_fee_invoiced,
				transaction_late_fee_waiver,
				transaction_late_fee_waiver,
				late_fee_payments,
				late_fee_balance,
				fiscal_year_interest,
				interest_invoiced,
				months_delinquent,
				transaction_interest_waiver,
				transaction_interest_waiver,
				interest_payments,
				interest_balance );
				
			if debug_flag then
				insert_log( process_id, ' 4. Late Fee: ' || to_char(round(fiscal_year_late_fee,2)) ||
					' Tank Fee Balance: ' || to_char(round(tank_fee_balance,2)) ||
					' Late Fee Balance: ' || to_char(round(late_fee_balance,2)) ||
					' Months: ' || to_char(months_delinquent) ||
					' Balance for Late Fee: ' || to_char(round(balance_for_late_fee,2)) );
			end if;
		end loop;  -- end of loop for selected FY transactions

		invoice_count := invoice_count + 1;
		commit;
	end loop;  -- end of Owner selection loop

	return invoice_id;
	
	exception
		when others then
		sql_errm := 'generate: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end generate;

function insert_invoice (
	p_owner_id               in       number,
	p_invoice_date           in       date,
	p_due_date               in       date)
	return number
is
	invoice_id number;
begin
	invoice_id := 0;
	select ustx.invoice_seq.nextval into invoice_id from dual;
	
	insert into ustx.invoices
		( id, owner_id, invoice_code, invoice_date, due_date, user_created, date_created, letter_date )
	values
		( invoice_id, p_owner_id, 'UST', p_invoice_date, p_due_date, 'INV_GEN', sysdate, sysdate + 7 );
	
	return invoice_id;

	exception
		when others then
		sql_errm := 'insert_invoice: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end insert_invoice;

function check_waiver(
	p_owner_id               in       number,
	p_fiscal_year            in       number,
	p_waiver_code            in       varchar2)
	return number
is
	waiver_amount number;
begin
	--nvl on amount so that if any are null others will be summed
	--nvl on sum in case nothing was selected and sum is null
	select nvl(sum(nvl(amount, 0)), 0)
	into waiver_amount
	from ustx.owner_waivers
	where owner_id = p_owner_id
		and fiscal_year = p_fiscal_year
		and waiver_code = p_waiver_code;
	
	return waiver_amount;
	
	exception
		when others then
		sql_errm := 'check_waiver: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end check_waiver;

-- ML: 04/02/2012 - modified this function to return tank counts that don't include emergency generator tanks (eg tanks)
-- Special case user requirement: eg tanks must not charged fees during FYs 2002 to 2007
-- This is accomplished ignoring eg tanks if p_fiscal_year happens to fall between 2002 to 2007
-- This function seems to count the number of tanks by first getting all the number of tanks today
-- then going back in time to p_fiscal_year by subtracting new tanks and adding removed tanks since the p_fiscal_year
function count_facility_tanks(
	p_owner_id               in       number,
	p_facility_id            in       number,
	p_fiscal_year            in       number)
	return number
is
	tank_base_count          number;
	permit_tank_count        number;
	permit_found             boolean;
	purchased_tank_count     number;
	sold_tank_count          number;
	removed_tank_count       number;
	filled_tank_count        number;
	installed_tank_count     number;
begin
	permit_tank_count := 0;
	tank_base_count := 0;
	purchased_tank_count := 0;
	sold_tank_count := 0;
	removed_tank_count := 0;
	filled_tank_count := 0;
	installed_tank_count := 0;
	permit_found := false;

	for permit_record in (
		select nvl(tanks,0) tanks
		from ustx.permits
		where owner_id = p_owner_id
			and facility_id = p_facility_id
			and fiscal_year = p_fiscal_year
	)
	loop
		permit_found := true;
		permit_tank_count := permit_record.tanks;
	end loop;

	-- get tank count snapshot as of today
	select count(*)
	into tank_base_count
	from ustx.tanks
	where facility_id = p_facility_id
		and owner_id = p_owner_id
		and tank_status_code in (1, 2, 3, 6, 11)
		and ( (p_fiscal_year < 2002) or (p_fiscal_year > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		);

	--  get purchased tank count in the future FY so we can exclude them
	select count(*)
	into purchased_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = p_facility_id
		and tanks.owner_id = p_owner_id
		and tank_status_code <> '12'
		and history_code in ('P','BP','LE')
		and fiscal_years.fiscal_year = p_fiscal_year
		and history_date >= fiscal_years.start_date
		and ( (p_fiscal_year < 2002) or (p_fiscal_year > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		);

	--  get sold tank count: code does not look at the tank owner id in tanks because if the owner sold the tank he is no longer the owner
	select count(*)
	into sold_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tank_history.owner_id = p_owner_id
		and tanks.facility_id = p_facility_id
		and tank_status_code <> '12'
		and history_code in ('S','BS','LO')
		and fiscal_years.fiscal_year = p_fiscal_year
		and history_date >= fiscal_years.start_date
		and ( (p_fiscal_year < 2002) or (p_fiscal_year > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		);

	--  get removed tank count
	select count(*)
	into removed_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = p_facility_id
		and tanks.owner_id = p_owner_id
		and tank_status_code <> '12'
		and history_code = 'R'
		and fiscal_years.fiscal_year = p_fiscal_year
		and history_date >= fiscal_years.start_date + 30
		and ( (p_fiscal_year < 2002) or (p_fiscal_year > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		);

	--  get filled tank count
	select count(*)
	into filled_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = p_facility_id
		and tanks.owner_id = p_owner_id
		and tank_status_code <> '12'
		and history_code = 'F'
		and fiscal_years.fiscal_year = p_fiscal_year
		and history_date >= fiscal_years.start_date + 30
		and ( (p_fiscal_year < 2002) or (p_fiscal_year > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		);
	
	--  get installed tank count
	select count(*)
	into installed_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.facility_id = p_facility_id
		and tanks.owner_id = p_owner_id
		and tank_status_code <> '12'
		and history_code = 'I'
		and fiscal_years.fiscal_year = p_fiscal_year
		and ((history_date > fiscal_years.end_date)
			or (tanks.tank_type = 'A' and history_date < to_date('07-01-2002','mm-dd-yyyy') and p_fiscal_year < 2003))  --ASTs not billed until fy 2003
		and ( (p_fiscal_year < 2002) or (p_fiscal_year > 2012) -- ML: only count non-eg tanks if selected FY is during this period
			or ((select count(*) from ustx.tank_details td where td.tank_id = tanks.id and td.tank_detail_code = 'U01') <= 0)
		);
	
	tank_base_count := (tank_base_count + sold_tank_count + removed_tank_count + filled_tank_count) - purchased_tank_count - installed_tank_count;

	--  create a new permit record to record what we have found...
	if not permit_found and tank_base_count <> 0 then
		insert into ustx.permits
			(owner_id, facility_id, fiscal_year, tanks)
		values
			(p_owner_id, p_facility_id, p_fiscal_year, tank_base_count);
	else
		if permit_tank_count <> tank_base_count then
			update ustx.permits
			set tanks = tank_base_count
			where owner_id = p_owner_id
				and facility_id = p_facility_id
				and fiscal_year = p_fiscal_year;
		end if;
	end if;

	return tank_base_count;

	exception
		when others then
		sql_errm := 'count_facility_tanks: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end count_facility_tanks;

function count_new_tanks(
	p_owner_id               in       number,
	p_facility_id            in       number,
	p_fiscal_year            in       number,
	p_due_date               in       date)
	return number
is
	newly_installed_tank_count  number;
begin
	newly_installed_tank_count := 0;
	select count(*)
	into newly_installed_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and facility_id = p_facility_id
		and tanks.owner_id = p_owner_id
		and tanks.owner_id = tank_history.owner_id
		and tank_status_code <> '12'
		and history_code = 'I'
		and history_date between p_due_date - 30 and p_due_date - 1
		and fiscal_years.fiscal_year = p_fiscal_year
		and history_date between fiscal_years.start_date and fiscal_years.end_date;

	return newly_installed_tank_count;

	exception
		when others then
		sql_errm := 'count_new_tanks: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end count_new_tanks;

function months_for_interest(
	p_fiscal_year            in       number)
	return number
is
	months_delinquent_interest number;
begin
	months_delinquent_interest := 0;

	if p_fiscal_year = 1979 then months_delinquent_interest := 144;
	elsif p_fiscal_year = 1980 then months_delinquent_interest := 132;
	elsif p_fiscal_year = 1993 then months_delinquent_interest := 120;
	elsif p_fiscal_year = 1994 then months_delinquent_interest := 108;
	elsif p_fiscal_year = 1995 then months_delinquent_interest := 96;
	elsif p_fiscal_year = 1996 then months_delinquent_interest := 84;
	elsif p_fiscal_year = 1997 then months_delinquent_interest := 72;
	elsif p_fiscal_year =1998 then months_delinquent_interest := 60;
	elsif p_fiscal_year = 1999 then months_delinquent_interest := 48;
	elsif p_fiscal_year = 2000 then months_delinquent_interest := 36;
	elsif p_fiscal_year = 2001 then months_delinquent_interest := 24;
	elsif p_fiscal_year = 2002 then months_delinquent_interest := 12;
	else months_delinquent_interest := 0;
	end if;
	return months_delinquent_interest;
end months_for_interest;

function late_payments(
	p_owner_id               in       number,
	p_fiscal_year            in       number,
	p_current_fiscal_year    in       number)
	return number
is
	late_payments_sum number;
begin
	--nvl on amount so that if any are zero others will be summed
	--nvl on sum in case nothing was selected and sum is null
	select nvl(sum(nvl(amount, 0)), 0)
	into late_payments_sum
	from ustx.transactions, ustx.fiscal_years
	where transactions.owner_id = p_owner_id
		and transactions.fiscal_year = p_fiscal_year
		and transactions.transaction_code in ('PP', 'LP', 'IP')
		and fiscal_years.fiscal_year = p_current_fiscal_year
		and transactions.transaction_date > fiscal_years.invoice_due_date;
	return late_payments_sum;
	
	exception
		when others then
		sql_errm := 'late_payments: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end late_payments;

procedure insert_log (
	p_process_id             in       number,
	p_log_text               in       varchar2)
is
begin
	insert into ustx.ust_log
		(process_id, log_timestamp, log_text)
	values
		(p_process_id, sysdate, p_log_text);
	commit;
	
	exception
		when others then
		sql_errm := substr(sqlerrm, 1, 2000);
		rollback;
		raise_application_error(-20100, 'insert_log: ' || sql_errm);
end insert_log;

procedure transactions_query(
	p_owner_id               in       number,
	p_fiscal_year            in       number,
	p_invoice_due_date       in       date,
	p_tank_fee_invoiced      out      number,
	p_tank_fee_payments      out      number,
	p_tank_fee_waiver        out      number,
	p_late_fee_invoiced      out      number,
	p_late_fee_payments      out      number,
	p_late_fee_waiver        out      number,
	p_interest_invoiced      out      number,
	p_interest_payments      out      number,
	p_interest_waiver        out      number,
	p_payments_prior_due_date out     number)
is
cursor select_transactions is
	select fiscal_year, transaction_code, nvl(amount,0) amount, transaction_date
	from ustx.transactions
	where owner_id = p_owner_id
		and fiscal_year = p_fiscal_year
	order by transaction_date;
begin
	p_tank_fee_invoiced := 0;
	p_tank_fee_payments := 0;
	p_tank_fee_waiver := 0;
	p_late_fee_invoiced := 0;
	p_late_fee_payments := 0;
	p_late_fee_waiver := 0;
	p_interest_invoiced := 0;
	p_interest_payments := 0;
	p_interest_waiver := 0;
	p_payments_prior_due_date := 0;

	for transaction_record in select_transactions
	loop
		if transaction_record.transaction_code = 'PA' then
			p_tank_fee_invoiced := p_tank_fee_invoiced + transaction_record.amount;
		elsif transaction_record.transaction_code = 'PP' then
			p_tank_fee_payments := p_tank_fee_payments + transaction_record.amount;
			if transaction_record.transaction_date <= p_invoice_due_date then
				p_payments_prior_due_date := p_payments_prior_due_date + transaction_record.amount;
			end if;
		elsif transaction_record.transaction_code = 'R' then
			p_tank_fee_payments := p_tank_fee_payments - transaction_record.amount;
			if transaction_record.transaction_date <= p_invoice_due_date then
				p_payments_prior_due_date := p_payments_prior_due_date - transaction_record.amount;
			end if;
		elsif transaction_record.transaction_code = 'PW' then
			p_tank_fee_waiver := p_tank_fee_waiver + transaction_record.amount;
		elsif transaction_record.transaction_code = 'LA' then
			p_late_fee_invoiced := p_late_fee_invoiced + transaction_record.amount;
		elsif transaction_record.transaction_code = 'LP' then
			p_late_fee_payments := p_late_fee_payments + transaction_record.amount;
		elsif transaction_record.transaction_code = 'LW' then
			p_late_fee_waiver := p_late_fee_waiver + transaction_record.amount;
		elsif transaction_record.transaction_code = 'IA' then
			p_interest_invoiced := p_interest_invoiced + transaction_record.amount;
		elsif transaction_record.transaction_code = 'IP' then
			p_interest_payments := p_interest_payments + transaction_record.amount;
		elsif transaction_record.transaction_code = 'IW' then
			p_interest_waiver := p_interest_waiver + transaction_record.amount;
		end if;
	end loop;

	exception
		when others then
		sql_errm := 'transactions_query: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end transactions_query;

procedure insert_transaction(
	p_owner_id               in       number,
	p_invoice_id             in       number,
	p_transaction_code       in       varchar2,
	p_transaction_status     in       varchar2,
	p_transaction_date       in       date,
	p_fiscal_year            in       number,
	p_amount                 in       number)
is
	transaction_id number;
begin
	transaction_id := 0;
	select ustx.transaction_seq.nextval into transaction_id from dual;
	
	insert into ustx.transactions
		(id, owner_id, invoice_id, transaction_code, transaction_status, transaction_date, fiscal_year, amount, user_created, date_created)
	values
		(transaction_id, p_owner_id, p_invoice_id, p_transaction_code, p_transaction_status, p_transaction_date, p_fiscal_year, p_amount, 'INV_GEN', sysdate);

	exception
		when others then
		sql_errm := 'insert_transaction: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end insert_transaction;

PROCEDURE insert_invoice_detail(
	p_invoice_id             in       number,
	p_fiscal_year            in       number,
	p_tank_count             in       number,
	p_tank_fee               in       number,
	p_tank_fee_invoiced      in       number,
	p_tank_fee_waiver        in       number,
	p_tank_fee_waiver_used   in       number,
	p_tank_fee_payment       in       number,
	p_tank_fee_balance       in       number,
	p_late_fee               in       number,
	p_late_fee_invoiced      in       number,
	p_late_fee_waiver        in       number,
	p_late_fee_waiver_used   in       number,
	p_late_fee_payment       in       number,
	p_late_fee_balance       in       number,
	p_interest               in       number,
	p_interest_invoiced      in       number,
	p_interest_months        in       number,
	p_interest_waiver        in       number,
	p_interest_waiver_used   in       number,
	p_interest_payment       in       number,
	p_interest_balance       in       number)
is
begin
	insert into ustx.invoice_detail
		(invoice_id, fiscal_year, tank_count, tank_fee, tank_fee_invoiced, tank_fee_waiver, tank_fee_waiver_used,
		tank_fee_payment, tank_fee_balance, late_fee, late_fee_invoiced, late_fee_waiver, late_fee_waiver_used,
		late_fee_payment, late_fee_balance, interest, interest_invoiced, interest_months, interest_waiver,
		interest_waiver_used, interest_payment, interest_balance)
	values
		(p_invoice_id, p_fiscal_year, p_tank_count, p_tank_fee, p_tank_fee_invoiced, p_tank_fee_waiver,
		p_tank_fee_waiver_used, p_tank_fee_payment, p_tank_fee_balance, p_late_fee,p_late_fee_invoiced,
		p_late_fee_waiver, p_late_fee_waiver_used, p_late_fee_payment, p_late_fee_balance, p_interest,
		p_interest_invoiced, p_interest_months, p_interest_waiver, p_interest_waiver_used,
		p_interest_payment, p_interest_balance);
	exception
		when others then
		sql_errm := 'insert_invoice_detail: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end insert_invoice_detail;

procedure insert_inv_detail_facilities(
	p_invoice_id             in       number,
	p_fiscal_year            in       number,
	p_facility_id            in       number,
	p_tank_count             in       number)
is
begin
	insert into ustx.invoice_detail_facilities
		(invoice_id, fiscal_year, facility_id, tank_count, tank_fee_waiver)
	values
		(p_invoice_id, p_fiscal_year, p_facility_id, p_tank_count, 0);
	exception
		when others then
		sql_errm := 'insert_inv_detail_facilities: ' || substr(sqlerrm, 1, 2000);
		rollback;
		insert_log( process_id, sql_errm);
		raise_application_error(-20100, sql_errm);
end insert_inv_detail_facilities;


procedure delete_invoice( p_invoice_id in number )
/*
 *   Deletes all the parts of an invoice and the actual invoice.
 *   
 *   Originally from Oracle Form ust_gen.fmb
 */
is
begin
	delete ustx.transactions where invoice_id = p_invoice_id
		and transaction_code not in ('PP','IP','LP');  -- delete non-payment transactions

	update ustx.transactions set invoice_id = 0
	where invoice_id = p_invoice_id
		and transaction_code in ('PP','IP','LP');  -- keep payment transactions, but change invoice_id to be 0

	delete ustx.invoice_detail_facilities where invoice_id = p_invoice_id;

	delete ustx.invoice_detail where invoice_id = p_invoice_id;

	delete ustx.invoices where id = p_invoice_id;

	commit;
	exception
		when others then
			sql_errm := 'delete_invoice error: ' || substr(sqlerrm, 1, 2000);
			rollback;
			insert_log(process_id, sql_errm);
			raise_application_error(-20100, sql_errm);
end delete_invoice;


procedure waive_all (
	p_owner_id in number,
	p_reason in varchar2,
	p_success out number)
/*
 *   Find and waive all outstanding fees for an owner
 */
is
	start_fy number;
	end_fy number;
	due_date date;
	inv_id number;
	process_id number;
begin
	select ustx.log_seq.nextval into process_id from dual;
	ustx.invoice.insert_log(process_id, 'New waive all begin for owner ' || to_char(p_owner_id));
	
	-- get earliest fy with balance -- determined from last run invoice
	SELECT min(INVD.fiscal_year) into start_fy
		FROM ustx.invoices I
			INNER JOIN ustx.invoice_detail INVD on I.id = INVD.invoice_id
		WHERE I.owner_id = p_owner_id
			and I.id = (select max(id) from ustx.invoices where owner_id = p_owner_id)
			and sum_balances > 0;
	
	SELECT fiscal_year into end_fy FROM ustx.fiscal_years WHERE CURRENT_DATE between start_date and end_date;
	
	if (start_fy is null) or (end_fy is null) then
		p_success := 0;
		return;
	end if;

	for fy in start_fy..end_fy loop
		Dbms_Output.Put_Line(fy);
		
		select end_date into due_date from ustx.fiscal_years where fiscal_year = fy - 1;
		if due_date is null then
			p_success := 0;
			return;
		end if;
			
		inv_id := ustx.invoice.generate(p_owner_id, CURRENT_DATE, due_date, fy);
		
		for invoice_detail_rec in 
			(select * from ustx.invoice_detail where invoice_id = inv_id and fiscal_year = fy)
		loop
			-- 3. create PW matching balances in new invoice. LW, IW not needed
			if invoice_detail_rec.tank_fee_balance > 0 then
				insert into ustx.owner_waivers (owner_id, waiver_code, fiscal_year, amount, waiver_comment, user_created, date_created)
				values (p_owner_id, 'PW', fy, invoice_detail_rec.tank_fee_balance, p_reason, 'WAIVE_ALL', CURRENT_DATE);
			end if;
		end loop;
		
		inv_id := ustx.invoice.generate(p_owner_id, CURRENT_DATE, due_date, fy);
	end loop;
	
	ustx.invoice.insert_log(process_id, 'New waive all finish for owner ' || to_char(p_owner_id));
	
	p_success := 1;
end waive_all;

end;
/
