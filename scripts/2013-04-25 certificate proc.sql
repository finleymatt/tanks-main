-- used by new kohana-based Onestop

CREATE OR REPLACE
PACKAGE ustx.permit
AS
	procedure insert_single (
		p_owner_id				in		number,
		p_facility_id			in		number,
		p_fiscal_year			in		number,
		p_date_permitted		in		date);

	procedure refresh_all (
		p_fiscal_year			in		number);
		
	function get_permit_number (
		p_fiscal_year			in		number,
		p_owner_id				in		number := NULL,
		p_facility_id			in		number := NULL) return number;
END;
/


CREATE OR REPLACE
PACKAGE BODY ustx.permit
AS
/****************************************************************************
  For permit/certificate generation with a specified owner and facility.
  This code imported from Oracle Form application version of Onestop and
  then simplified.
  ML: 3/8/2013 - imported from Oracle Form application
*****************************************************************************/
procedure insert_single (
	p_owner_id				in		number,
	p_facility_id			in		number,
	p_fiscal_year			in		number,
	p_date_permitted		in		date)
is
	fac_permit_number number;
	permits_rowid varchar2(20);
	tank_count number := 0;
	
begin
	/** get existing or new permit/certificate number **/
	fac_permit_number := get_permit_number(p_fiscal_year, p_owner_id, p_facility_id);
	
	permits_rowid := null;
	for permits_record in
		( select rowid
			from ustx.permits
			where owner_id = p_owner_id
				and facility_id = p_facility_id
				and fiscal_year = p_fiscal_year )
	loop
		permits_rowid := permits_record.rowid;
	end loop;
	
	/** if permit already exists, update only **/
	if permits_rowid is not null then
		begin
			update ustx.permits
			set permit_number = fac_permit_number,
				date_permitted = p_date_permitted
			where rowid = permits_rowid;
		end;
	
	/** permit does not exist, so insert new **/
	else
		/** calc latest tank count **/
		tank_count := 0;
		for tank_history_record in
		( select history_code
			from ustx.tank_history
			where tank_id in (select id
				from ustx.tanks
				where facility_id = p_facility_id
					and owner_id = p_owner_id) )
		loop
			if tank_history_record.history_code = 'I' then
				tank_count := tank_count + 1;
			elsif tank_history_record.history_code in ('F','R') then
				tank_count := tank_count - 1;
			end if;
		end loop;

		/** insert permit **/
		begin
			insert into ustx.permits
				( owner_id, facility_id, fiscal_year,
				tanks, date_permitted, permit_number )
			values ( p_owner_id, p_facility_id, p_fiscal_year,
				tank_count, sysdate, fac_permit_number );
		end;
	end if;
end insert_single;

procedure refresh_all (
	p_fiscal_year			in		number)
is
	new_permit_number number;
	permits_rowid varchar2(20);
	tank_count number := 0;
	transaction_balance number := 0;
	current_fy_balance number := 0;
	has_trans boolean := FALSE;

begin
	/** get new permit/certificate number to start from **/
	new_permit_number := get_permit_number(p_fiscal_year);
	
	/* get all owner/facility where: has least 1 tank, fy=selected, and date_permitted=null */
	for owner_facility_record in
		(select distinct owners.id owner_id, facilities.id facility_id, owners.owner_name owner_name
		from ustx.owners_mvw owners, ustx.facilities_mvw facilities, ustx.tanks
		where owners.id = tanks.owner_id
			and tanks.facility_id = facilities.id
			and facilities.id in (select facility_id from ustx.tanks
				where ((tank_type = 'U' and meets_1988_req = 'Y') or (tank_type = 'A')))
			and (owners.id, facilities.id) in
				(select owner_id, facility_id from ustx.permits
				where fiscal_year = p_fiscal_year and date_permitted is null)
		order by owner_name, facilities.id)
	loop
		/**  count tanks.  max used to get least one row **/
		select nvl(max(tanks), 0)
		into tank_count
		from ustx.permits
		where owner_id = owner_facility_record.owner_id
			and facility_id = owner_facility_record.facility_id
			and fiscal_year = p_fiscal_year;
		
		/**  calculate transaction_balance - for all FYs **/
		transaction_balance := 0;
		for transaction_record in 
			( select transaction_code, amount
			from ustx.transactions
			where owner_id = owner_facility_record.owner_id
				and fiscal_year > 1978
				and fiscal_year <= p_fiscal_year )
		loop
			if transaction_record.transaction_code in ('IA','LA','PA') then
				transaction_balance := transaction_balance + transaction_record.amount;
			elsif transaction_record.transaction_code in ('IP','IW','LP','LW','PP','PW') then
				transaction_balance := transaction_balance - transaction_record.amount;
			end if;
		end loop;
		
		/**  calculate current_fy_balance - for selected FY only **/		
		current_fy_balance :=0;
		has_trans := FALSE;  /* must have at least one transaction */
		for current_fy_record in 
			( select transaction_code, amount
			from ustx.transactions
			where owner_id = owner_facility_record.owner_id
				and fiscal_year = p_fiscal_year)
		loop
			has_trans := TRUE;
			if current_fy_record.transaction_code in ('IA','LA','PA') then
				current_fy_balance := current_fy_balance + current_fy_record.amount;
			elsif current_fy_record.transaction_code in ('IP','IW','LP','LW','PP','PW','R') then
				current_fy_balance := current_fy_balance - current_fy_record.amount;
			end if;
		end loop;
		
		/** if no fees due, update the permit record **/
		if (transaction_balance <= 0) and (current_fy_balance <= 0) and (has_trans = TRUE) then
			new_permit_number := new_permit_number + 1;
			update ustx.permits
			set date_permitted = sysdate, permit_number = new_permit_number
			where owner_id = owner_facility_record.owner_id
				and facility_id = owner_facility_record.facility_id
				and fiscal_year = p_fiscal_year;
		end if;
	end loop;
end refresh_all;


/****************************************************************************
  Returns existing permit_number if one exists for FY, owner, facility combo.
  Otherwise, uses weird numbering scheme where each FY starts off at 1001.
*****************************************************************************/
function get_permit_number (
	p_fiscal_year			in		number,
	p_owner_id				in		number := NULL,
	p_facility_id			in		number := NULL) return number
is
	ret_permit_number number := null;
begin
	if (p_owner_id is not null) and (p_facility_id is not null) then
		select max(permit_number) /* max used to get at least one row */
		into ret_permit_number
		from ustx.permits
		where fiscal_year = p_fiscal_year
			and owner_id = p_owner_id
			and facility_id = p_facility_id;
	end if;
	
	/****************************************************************
	 If permit_number isn't found, use next incremented value per FY.
	 If first one for FY, then start off at 1001.
	*****************************************************************/
	if ret_permit_number is null then
		select nvl(max(permit_number),1000) + 1
		into ret_permit_number
		from ustx.permits
		where fiscal_year = p_fiscal_year;
	end if;
	
	return ret_permit_number;
end get_permit_number;	
	
END;
/

-- by USTX
GRANT EXECUTE ON ustx.permit TO ONESTOP_ROLE;
/