-- 2014-11-23 ML: Create A/B Operator tracking in Onestop as requested by Antonette.

/**************************************************
 AB Operator section
***************************************************/
CREATE TABLE USTX.AB_OPERATOR (
	ID Number(10,0) not null,
	FACILITY_ID Number(8,0) not null,
	FIRST_NAME Varchar2(100) not null,
	LAST_NAME Varchar2(100) not null,
	TITLE Varchar2(100) null,
	PHONE Varchar2(50) null,
	EMAIL Varchar2(50) null,
	USER_CREATED Varchar2(30) not null,
	DATE_CREATED Date not null,
	USER_MODIFIED Varchar2(30),
	DATE_MODIFIED Date,
	CONSTRAINT USTX_AB_OPERATOR_PK PRIMARY KEY (ID)
);

CREATE SEQUENCE USTX.AB_OPERATOR_SEQ
	START WITH 1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 999999999999999999999999999
	NOCACHE
	NOORDER
	NOCYCLE;
/

GRANT SELECT ON USTX.AB_OPERATOR_SEQ to ONESTOP_ROLE;
GRANT SELECT, INSERT, UPDATE, DELETE ON USTX.AB_OPERATOR to ONESTOP_ROLE;
/

-- UST_PI has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_PI', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_PI', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_PI', 'DELETE');

-- UST_FIN has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_FIN', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_FIN', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_FIN', 'DELETE');

-- UST_IM has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_IM', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_IM', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_IM', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'UST_IM', 'DELETE');

-- PST_SELECT_ONLY can only select
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_OPERATOR', 'PST_SELECT_ONLY', 'SELECT');

commit;
/

/**************************************************
 AB Certificate section
***************************************************/
CREATE TABLE USTX.AB_CERT (
	ID Number(10,0) not null,
	AB_OPERATOR_ID Number(10,0) not null,
	CERT_LEVEL Varchar2(50) not null,
	CERT_NUM Varchar2(50) null,
	CERT_DATE Date not null,
	REGISTRANT_ID Varchar2(50) null,
	USER_CREATED Varchar2(30) not null,
	DATE_CREATED Date not null,
	USER_MODIFIED Varchar2(30),
	DATE_MODIFIED Date,
	CONSTRAINT USTX_AB_CERT_PK PRIMARY KEY (ID)
);

CREATE SEQUENCE USTX.AB_CERT_SEQ
	START WITH 1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 999999999999999999999999999
	NOCACHE
	NOORDER
	NOCYCLE;
/

GRANT SELECT ON USTX.AB_CERT_SEQ to ONESTOP_ROLE;
GRANT SELECT, INSERT, UPDATE, DELETE ON USTX.AB_CERT to ONESTOP_ROLE;
/

-- UST_PI has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_PI', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_PI', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_PI', 'DELETE');

-- UST_FIN has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_FIN', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_FIN', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_FIN', 'DELETE');

-- UST_IM has full permissions
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_IM', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_IM', 'UPDATE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_IM', 'INSERT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'UST_IM', 'DELETE');

-- PST_SELECT_ONLY can only select
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.AB_CERT', 'PST_SELECT_ONLY', 'SELECT');

commit;
/


/**************************************************
 ABC Letter Tank Status section
***************************************************/
CREATE TABLE USTX.AB_TANK_STATUS (
	ID Number(10,0) not null,
	FACILITY_ID Number(8,0) not null,
	/* not used yet */
	TANK_ID Number(8,0) null,
	TANK_STATUS_CODE VARCHAR2(5) null,
	/* if tank status = TOS */
	TANK_LAST_USED Date null,
	TANK_STATUS_NOTE VARCHAR2(200) null,
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
