-- extend space allowed for rolename to match code in ustx.roles
ALTER TABLE ustx.ust_role_privs MODIFY ( ROLENAME varchar2(15) );

-- extend space allowed for tablename
ALTER TABLE ustx.ust_role_privs MODIFY ( TABLENAME varchar2(50) );
/

-- clear table
delete from ustx.ust_role_privs;
/

-- UST_FIN ===================================================

-- select privs
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.CITIES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.COUNTIES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITIES_MVW', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_INSPECTION_CONTACT', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_INSPECTION_CONTACT', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_INSPECTION_CONTACT', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_INSPECTION_CONTACT', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FILL_MATERIAL', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_METHODS', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_PROVIDERS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_PROVIDERS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_PROVIDERS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_PROVIDERS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_RESPONSIBILITIES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_RESPONSIBILITIES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_RESPONSIBILITIES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_RESPONSIBILITIES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FISCAL_YEARS', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.HOLD_PERMITS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.HOLD_PERMITS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.HOLD_PERMITS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.HOLD_PERMITS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTION_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_CODES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_CODES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_CODES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_CODES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL_FACILITIES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL_FACILITIES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL_FACILITIES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL_FACILITIES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICE_DETAIL_FACILITIES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICE_DETAIL_FACILITIES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICE_DETAIL_FACILITIES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICE_DETAIL_FACILITIES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OPERATORS_MVW', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OPERATORS_MVW', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OPERATORS_MVW', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OPERATORS_MVW', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_COMMENTS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_COMMENTS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_COMMENTS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_COMMENTS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_WAIVERS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_WAIVERS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_WAIVERS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_WAIVERS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNERS_MVW', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTIES', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTIES', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTIES', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTIES', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTY_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PERMITS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PERMITS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PERMITS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PERMITS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.ROLES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.STAFF', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.STAFF_ROLES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAIL_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_FILL_MATERIAL_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_INFO_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_OPERATOR_HISTORY', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_OPERATOR_HISTORY', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_OPERATOR_HISTORY', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_OPERATOR_HISTORY', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_STATUS_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANKS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANKS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANKS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANKS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTION_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTIONS', 'UST_FIN', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTIONS', 'UST_FIN', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTIONS', 'UST_FIN', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTIONS', 'UST_FIN', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_FACILITIES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_OWNERS', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_REF_CODES', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_ROLE_PRIVS', 'UST_FIN', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('LUST.CG_REF_CODES', 'UST_FIN', 'SELECT');

-- UST_PI ===================================================

-- select privs
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.CITIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.COUNTIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITIES_MVW', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_INSPECTION_CONTACT', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FILL_MATERIAL', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_METHODS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_PROVIDERS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_RESPONSIBILITIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FISCAL_YEARS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.HOLD_PERMITS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTION_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_PI', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_PI', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_PI', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'UST_PI', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL_FACILITIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICE_DETAIL_FACILITIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OPERATORS_MVW', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_COMMENTS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_WAIVERS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNERS_MVW', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTY_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PERMITS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.ROLES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.STAFF', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.STAFF_ROLES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAIL_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_PI', 'SELECT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_PI', 'UPDATE');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_PI', 'INSERT');
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'UST_PI', 'DELETE');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_FILL_MATERIAL_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_INFO_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_OPERATOR_HISTORY', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_STATUS_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANKS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTION_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTIONS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_FACILITIES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_OWNERS', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_REF_CODES', 'UST_PI', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_ROLE_PRIVS', 'UST_PI', 'SELECT');


-- PST_SELECT_ONLY ===================================================

-- select privs
insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.CITIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.COUNTIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITIES_MVW', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_HISTORY_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FACILITY_INSPECTION_CONTACT', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FILL_MATERIAL', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_METHODS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_PROVIDERS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FINANCIAL_RESPONSIBILITIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.FISCAL_YEARS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.HOLD_PERMITS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTION_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INSPECTIONS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICE_DETAIL_FACILITIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.INVOICES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICE_DETAIL_FACILITIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.NOTICES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OPERATORS_MVW', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_COMMENTS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNER_WAIVERS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.OWNERS_MVW', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PENALTY_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.PERMITS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.ROLES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.STAFF', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.STAFF_ROLES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAIL_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_DETAILS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_FILL_MATERIAL_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_HISTORY_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_INFO_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_OPERATOR_HISTORY', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANK_STATUS_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TANKS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTION_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.TRANSACTIONS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_FACILITIES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_OWNERS', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_REF_CODES', 'PST_SELECT_ONLY', 'SELECT');

insert into ustx.ust_role_privs (tablename, rolename, security)
values ('USTX.UST_ROLE_PRIVS', 'PST_SELECT_ONLY', 'SELECT');


commit;
/

