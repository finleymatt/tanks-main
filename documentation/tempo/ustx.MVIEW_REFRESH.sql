create or replace
PACKAGE BODY      MVIEW_REFRESH
AS
l_errorcode 	     number;
l_errormsg	     varchar2(240);
l_description	     varchar2(240);
l_pkg_proc           varchar2(30) := 'ustx.mview_refresh';
l_rec_count          number;
PROCEDURE main
IS
BEGIN
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustx.mview_refresh.main begin';
  write_log (l_pkg_proc, l_description);
  lust.mview_refresh.main;
  l_rec_count := 0;
  select count(*) into l_rec_count
    from ustb.onestop_facilities1;
  if l_rec_count > 0 then
--  execute immediate 'truncate table ustx.facilities_mvw';
-- use delete in case of transaction rollback
  delete from ustx.facilities_mvw;
  insert into ustx.facilities_mvw
  select to_number(trim(id)) id,
    ai_id,
    facility_name,
    address1,
    address2,
    city,
    state,
    zip,
    owner_id,
    indian,
    user_modified,
    date_modified
  from ustb.onestop_facilities1;
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
     || ' ustx.facilities_mvw records inserted '
     || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);
  else
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
         || ' *error* ustb.onestop_facilities1 empty';
  write_log (l_pkg_proc, l_description);
  end if;

  l_rec_count := 0;
  select count(*) into l_rec_count
    from ustb.onestop_ust_locations1;
  if l_rec_count > 0 then
--  execute immediate 'truncate table ustx.ust_locations_mvw';
-- use delete in case of transaction rollback
  delete from ustx.ust_locations_mvw;
  insert into ustx.ust_locations_mvw
  select * from ustb.onestop_ust_locations1;
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
     || ' ustx.ust_locations_mvw records inserted '
     || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);
  else
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
         || ' *error* ustb.onestop_ust_locations1 empty';
  write_log (l_pkg_proc, l_description);
  end if;

  l_rec_count := 0;
  select count(*) into l_rec_count
    from ustb.onestop_owners1;
  if l_rec_count > 0 then
--  execute immediate 'truncate table ustx.owners_mvw';
-- use delete in case of transaction rollback
  delete from ustx.owners_mvw;
  insert into ustx.owners_mvw
  select id,
    per_id,
    org_id,
    owner_name,
    address1,
    address2,
    city,
    state,
    zip,
    phone_number,
    date_created,
    user_modified,
    date_modified
  from ustb.onestop_owners1;
/*
  union
  select id,
         per_id,
         org_id,
         owner_name,
         address1,
         address2,
         city,
         state,
         zip,
         phone_number,
         date_created,
         user_modified,
         date_modified
  from ustx.ust_owners t1
  where not exists
  (select 'x'
  from ustb.onestop_owners1 t2
  where t1.id = t2.id);
*/
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
     || ' ustx.owners_mvw records inserted '
     || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);
  else
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
         || ' *error* ustb.onestop_owners1 empty';
  write_log (l_pkg_proc, l_description);
  end if;

  l_rec_count := 0;
  select count(*) into l_rec_count
    from ustb.onestop_operators1;
  if l_rec_count > 0 then
--  execute immediate 'truncate table ustx.operators_mvw';
-- use delete in case of transaction rollback
  delete from ustx.operators_mvw;
  insert into ustx.operators_mvw
  select * from ustb.onestop_operators1;
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
     || ' ustx.operators_mvw records inserted '
     || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);
  else
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
         || ' *error* ustb.onestop_operators1 empty';
  write_log (l_pkg_proc, l_description);
  end if;

if to_char(sysdate,'hh24') >= '18' then
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustx.mview_refresh.main gather stats';
  write_log (l_pkg_proc, l_description);
-- Commented out by Alex Herndon 11/01/2006
--  dbms_stats.gather_schema_stats(ownname=> 'USTX' , cascade=> TRUE);
end if;

  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustx.mview_refresh.main end';
  write_log (l_pkg_proc, l_description);
  EXCEPTION
    WHEN OTHERS then
      l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustx.mview_refresh.main error';
      write_log (l_pkg_proc, l_description);
END main;
PROCEDURE write_log (
  p_pkg_proc               in   varchar2,
  p_description            in   varchar2)
IS
BEGIN
   l_ErrorCode := SQLCODE;
   l_ErrorMsg  := SUBSTR(SQLERRM,1,240);
   insert into lust.log_table (pkg_proc, code, message, description)
   values (p_pkg_proc, l_errorcode, l_errormsg, p_description);
   commit;
END write_log;
END;