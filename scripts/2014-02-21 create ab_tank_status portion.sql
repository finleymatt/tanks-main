--ran in eidd, eidt
/**************************************************
 ABC Letter Tank Status section
***************************************************/
CREATE TABLE USTX.AB_TANK_STATUS (
	ID Number(10,0) not null,
	FACILITY_ID Number(8,0) null,
	/* not used yet */
	TANK_ID Number(8,0) null,
	TANK_STATUS_CODE VARCHAR2(5),
	/* if tank status = TOS */
	TANK_LAST_USED Date,
	TANK_STATUS_NOTE VARCHAR2(200),
	USER_CREATED Varchar2(30) not null,
	DATE_CREATED Date not null,
	USER_MODIFIED Varchar2(30),
	DATE_MODIFIED Date,
	CONSTRAINT USTX_AB_TANK_STATUS_PK PRIMARY KEY (ID)
);

CREATE SEQUENCE USTX.AB_TANK_STATUS_SEQ
	START WITH 1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 999999999999999999999999999
	NOCACHE
	NOORDER
	NOCYCLE;
/

GRANT SELECT ON USTX.AB_TANK_STATUS_SEQ to ONESTOP_ROLE;
GRANT SELECT, INSERT, UPDATE, DELETE ON USTX.AB_TANK_STATUS to ONESTOP_ROLE;
/

-- UST_PI has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_PI', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_PI', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_PI', 'DELETE');

-- UST_FIN has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_FIN', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_FIN', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_FIN', 'DELETE');

-- UST_IM has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_IM', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_IM', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_IM', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'UST_IM', 'DELETE');

-- PST_SELECT_ONLY can only select
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_TANK_STATUS', 'PST_SELECT_ONLY', 'SELECT');

commit;
/
