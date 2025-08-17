-- 2014-03-30 ML: Created Email management feature in Onestop as requested by Antonette.
-- This table will store emails for either Facility, Owner, Operator.
CREATE TABLE USTX.EMAILS (
	ID Number(10,0) not null,
	ENTITY_ID Number(10,0) not null,
	ENTITY_TYPE Varchar2(30) not null,
	TITLE Varchar2(100),
	FULLNAME Varchar2(100),
	EMAIL Varchar2(100) not null,
	COMMENTS Long,
	USER_CREATED Varchar2(30) not null,
	DATE_CREATED Date not null,
	USER_MODIFIED Varchar2(30),
	DATE_MODIFIED Date,
	CONSTRAINT USTX_E_PK PRIMARY KEY (ID)
);
	
CREATE SEQUENCE USTX.EMAIL_SEQ
	START WITH 1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 999999999999999999999999999
	NOCACHE
	NOORDER
	NOCYCLE;
/

CREATE OR REPLACE TRIGGER USTX.TBIUD_EMAILS
BEFORE insert or update on USTX.EMAILS
for each row
declare
tmpid number;
BEGIN
	if inserting then
		if :new.ID is null then
			tmpid := 0;
			select USTX.EMAIL_SEQ.nextval into tmpid from dual;
			:new.ID := tmpid;
		end if;
		
		if :new.USER_CREATED is null then
			:new.USER_CREATED := user;
		end if;
		
		:new.DATE_CREATED := sysdate;

	elsif updating then
		if :new.USER_MODIFIED is null then
			:new.USER_MODIFIED := user;
		end if;
		
		:new.DATE_MODIFIED := sysdate;
	end if;
END;
/

GRANT SELECT ON USTX.EMAIL_SEQ to ONESTOP_ROLE;
GRANT SELECT, INSERT, UPDATE, DELETE ON USTX.EMAILS to ONESTOP_ROLE;
/

-- UST_PI has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_PI', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_PI', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_PI', 'DELETE');

-- UST_FIN has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_FIN', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_FIN', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'UST_FIN', 'DELETE');

-- PST_SELECT_ONLY can only select
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.EMAILS', 'PST_SELECT_ONLY', 'SELECT');

commit;

/
