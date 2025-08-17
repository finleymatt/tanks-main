-- used by new kohana-based Onestop

CREATE OR REPLACE
PACKAGE ustx.notice
AS
	function generate_notice( p_operator_id in varchar2,
		p_notice_code in varchar2,
		p_notice_date in date,
		p_fy in number) return number;

	function count_facility_tanks( owner_id in number,
		operator_id in varchar2,
		facility_id in number,
		fiscal_year in number ) return number;

	function delete_notice( notice_id in number ) return number;
END;
/

CREATE OR REPLACE
PACKAGE BODY ustx.notice
AS
/****************************************************************************
  Generate notices for a specified fiscal year in DB.

  Mar 10 2005	Asbury		Initial implementation.
  Apr 30 2013	Min Lee		Imported into DB from Oracle Form function.
*****************************************************************************/
function generate_notice( p_operator_id in varchar2,
	p_notice_code in varchar2,
	p_notice_date in date,
	p_fy in number)
return number
is
	notice_id	number := 0;
	tank_count	number := 0;

pragma autonomous_transaction;
begin

	/*** create actual notice record ***/
	select ustx.notice_seq.nextval into notice_id from dual;

	insert into ustx.notices ( id, operator_id, notice_code, notice_date, user_created, date_created )
	values ( notice_id, p_operator_id, p_notice_code, p_notice_date, 'NOTI_GEN', sysdate );

	/*** create owner/facility/tank count info record in insert_noti_detail_facilities ***/
	for owner_record in
	(select distinct ow.id owner_id
		from ustx.owners_mvw ow, ustx.transactions tr
		where ow.id = tr.owner_id
			and instr(tr.transaction_code, 'H')=0
			and instr(tr.transaction_code, 'G')=0
			and tr.fiscal_year >= 1979
			and ow.id in (select tanks.owner_id
				from ustx.tanks, ustx.facilities_mvw facilities, ustx.owners_mvw owners
				where tanks.facility_id = facilities.id
					and tank_status_code in (1,2,3,6,11)
					and tanks.owner_id = owners.id
					and tanks.operator_id = p_operator_id
					and facilities.indian is null
					and owners.id = tanks.owner_id
					and substr(tanks.operator_id,2) <> decode(owners.org_id, null, owners.per_id, owners.org_id))
		group by ow.id
		having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
			'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
			'LW', amount*-1, 'IW', amount*-1, amount)) > 0)
	loop
		for facility_record in
		( select distinct fac.id, facility_name
			from ustx.tanks tan, ustx.facilities_mvw fac
			where tan.facility_id = fac.id
				and (tan.operator_id = p_operator_id
				and tan.owner_id = owner_record.owner_id)
				and fac.indian is null )
		loop
			tank_count := count_facility_tanks(owner_record.owner_id, p_operator_id, facility_record.id, p_fy);

			if tank_count > 0 then
				insert into ustx.notice_detail_facilities
					(notice_id, fiscal_year, owner_id, facility_id, tank_count)
				values (notice_id, p_fy, owner_record.owner_id, facility_record.id, tank_count);
			end if;
		end loop;
	end loop;
  
	commit;

	return notice_id;

exception
	when others then
		rollback;
		return 0;
end generate_notice;



/****************************************************************************
  Find the number of tanks for a specified facility and fiscal year.

  Output:	number of tanks (number)
  
  Mar 10 2005	Asbury		Initial implementation.
  Apr 30 2013	Min Lee		Imported into DB from Oracle Form function.
*****************************************************************************/
function count_facility_tanks( owner_id in number,
	operator_id in varchar2,
	facility_id in number,
	fiscal_year in number )
return number
is
	tank_base_count          number := 0;
	purchased_tank_count     number := 0;
	sold_tank_count          number := 0;
	tank_changes             number := 0;
	removed_tank_count       number := 0;
	filled_tank_count        number := 0;
	installed_tank_count     number := 0;

begin

	/*** base count is the permit count for the specified fiscal year. ***/
	tank_base_count := 0;
	select count(*)
	into tank_base_count
	from ustx.tanks
	where facility_id = count_facility_tanks.facility_id
		and owner_id = count_facility_tanks.owner_id
		and operator_id = count_facility_tanks.operator_id
		and tank_status_code in (1,2,3,6,11);

	/***  get purchased tank count ***/
	select count(*)
	into purchased_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.operator_id = count_facility_tanks.operator_id
		and facility_id = count_facility_tanks.facility_id
		and tanks.owner_id = count_facility_tanks.owner_id
		and tanks.owner_id = tank_history.owner_id
		and tank_status_code <> '12'
		and history_code in ('P','BP','LE')
		and fiscal_years.fiscal_year = count_facility_tanks.fiscal_year
		and history_date between fiscal_years.start_date and fiscal_years.end_date;

	/*** get sold tank count ***/
	select count(*)
	into sold_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.operator_id = count_facility_tanks.operator_id
		and facility_id = count_facility_tanks.facility_id
		and tanks.owner_id = count_facility_tanks.owner_id
		and tanks.owner_id = tank_history.owner_id
		and tank_status_code <> '12'
		and history_code in ('S','BS','LO')
		and fiscal_years.fiscal_year = count_facility_tanks.fiscal_year
		and history_date between fiscal_years.start_date and fiscal_years.end_date;

	/*** get removed tank count ***/
	select count(*)
	into removed_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and tanks.operator_id = count_facility_tanks.operator_id
		and facility_id = count_facility_tanks.facility_id
		and tanks.owner_id = count_facility_tanks.owner_id
		and tanks.owner_id = tank_history.owner_id
		and tank_status_code <> '12'
		and history_code = 'R'
		and fiscal_years.fiscal_year = count_facility_tanks.fiscal_year
		and history_date between fiscal_years.start_date+30 and fiscal_years.end_date;

	/*** get filled tank count ***/
	select nvl(count(*),0)
	into filled_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and facility_id = count_facility_tanks.facility_id
		and tanks.owner_id = count_facility_tanks.owner_id
		and tanks.operator_id = count_facility_tanks.operator_id
		and tanks.owner_id = tank_history.owner_id
		and tank_status_code <> '12'
		and history_code = 'F'
		and fiscal_years.fiscal_year = count_facility_tanks.fiscal_year
		and history_date between fiscal_years.start_date+30 and fiscal_years.end_date;

	/*** get installed tank count ***/
	select nvl(count(*),0)
	into installed_tank_count
	from ustx.tank_history, ustx.tanks, ustx.fiscal_years
	where tanks.id = tank_history.tank_id
		and tanks.owner_id = tank_history.owner_id
		and facility_id = count_facility_tanks.facility_id
		and tanks.owner_id = count_facility_tanks.owner_id
		and tanks.operator_id = count_facility_tanks.operator_id
		and tanks.owner_id = tank_history.owner_id
		and tank_status_code <> '12'
		and history_code = 'I'
		and fiscal_years.fiscal_year = count_facility_tanks.fiscal_year
		and (history_date > fiscal_years.end_date-29 or sysdate - history_date < 30);

	tank_base_count := tank_base_count + sold_tank_count + removed_tank_count + filled_tank_count - purchased_tank_count - installed_tank_count;

	return tank_base_count;
end count_facility_tanks;


/****************************************************************************
  Delete a notice and its related record in notice_detail_facilities.
  
  Mar 10 2005	Asbury		Initial implementation.
  Apr 30 2013	Min Lee		Imported into DB from Oracle Form proc.
*****************************************************************************/
function delete_notice( notice_id in number )
return number
is
pragma autonomous_transaction;
begin
	delete ustx.notice_detail_facilities
	where notice_id = delete_notice.notice_id;

	delete ustx.notices
	where id = delete_notice.notice_id;
	
	commit;
	return 1;
exception
	when others then
		rollback;
		return 0;
end delete_notice;

END;
/

-- by USTX
GRANT EXECUTE ON ustx.notice TO ONESTOP_ROLE;
/