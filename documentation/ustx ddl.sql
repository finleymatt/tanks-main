-- MINLEE generated it from USTX on EIDD because generating it from EIDP failed.
--------------------------------------------------------
--  File created - Saturday-June-20-2015   
--------------------------------------------------------
--------------------------------------------------------
--  DDL for Sequence AB_CERT_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "AB_CERT_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 1841 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence AB_OPERATOR_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "AB_OPERATOR_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 7 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence EMAIL_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "EMAIL_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 13 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence FIN_RESP_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "FIN_RESP_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 2066 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence INSPECTION_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "INSPECTION_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 32016 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence INVOICE_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "INVOICE_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 281178 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence LOG_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "LOG_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 13809 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence NOTICE_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "NOTICE_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 449 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence TANK_HIST_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "TANK_HIST_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 4 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence TANK_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "TANK_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 38236 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Sequence TRANSACTION_SEQ
--------------------------------------------------------

   CREATE SEQUENCE  "TRANSACTION_SEQ"  MINVALUE 1 MAXVALUE 999999999999999999999999999 INCREMENT BY 1 START WITH 409864 NOCACHE  NOORDER  NOCYCLE ;
--------------------------------------------------------
--  DDL for Table AB_CERT
--------------------------------------------------------

  CREATE TABLE "AB_CERT" 
   (	"ID" NUMBER(10,0), 
	"AB_OPERATOR_ID" NUMBER(10,0), 
	"CERT_LEVEL" VARCHAR2(50), 
	"CERT_NUM" VARCHAR2(50), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"REGISTRANT_ID" VARCHAR2(50), 
	"CERT_DATE" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table AB_OPERATOR
--------------------------------------------------------

  CREATE TABLE "AB_OPERATOR" 
   (	"ID" NUMBER(10,0), 
	"FACILITY_ID" NUMBER(10,0), 
	"FIRST_NAME" VARCHAR2(100), 
	"LAST_NAME" VARCHAR2(100), 
	"TITLE" VARCHAR2(100), 
	"PHONE" VARCHAR2(50), 
	"EMAIL" VARCHAR2(50), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table CITIES
--------------------------------------------------------

  CREATE TABLE "CITIES" 
   (	"CITY" VARCHAR2(25), 
	"COUNTY" VARCHAR2(25)
   ) ;
--------------------------------------------------------
--  DDL for Table COUNTIES
--------------------------------------------------------

  CREATE TABLE "COUNTIES" 
   (	"COUNTY" VARCHAR2(25), 
	"FIPS_CODE" VARCHAR2(2)
   ) ;
--------------------------------------------------------
--  DDL for Table CSED_SSN
--------------------------------------------------------

  CREATE TABLE "CSED_SSN" 
   (	"SSN" VARCHAR2(9)
   ) ;
--------------------------------------------------------
--  DDL for Table EMAILS
--------------------------------------------------------

  CREATE TABLE "EMAILS" 
   (	"ID" NUMBER(10,0), 
	"ENTITY_ID" NUMBER(10,0), 
	"ENTITY_TYPE" VARCHAR2(30), 
	"TITLE" VARCHAR2(100), 
	"FULLNAME" VARCHAR2(100), 
	"EMAIL" VARCHAR2(100), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"COMMENTS" LONG
   ) ;
--------------------------------------------------------
--  DDL for Table EXCEPTIONS
--------------------------------------------------------

  CREATE TABLE "EXCEPTIONS" 
   (	"ROW_ID" ROWID, 
	"OWNER" VARCHAR2(30), 
	"TABLE_NAME" VARCHAR2(30), 
	"CONSTRAINT" VARCHAR2(30)
   ) ;
--------------------------------------------------------
--  DDL for Table FACILITIES_MVW
--------------------------------------------------------

  CREATE TABLE "FACILITIES_MVW" 
   (	"ID" NUMBER, 
	"AI_ID" NUMBER(10,0), 
	"FACILITY_NAME" VARCHAR2(50), 
	"ADDRESS1" VARCHAR2(50), 
	"ADDRESS2" VARCHAR2(50), 
	"CITY" VARCHAR2(50), 
	"STATE" CHAR(2), 
	"ZIP" CHAR(9), 
	"OWNER_ID" NUMBER, 
	"INDIAN" CHAR(1), 
	"USER_MODIFIED" VARCHAR2(10), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table FACILITY_HISTORY
--------------------------------------------------------

  CREATE TABLE "FACILITY_HISTORY" 
   (	"FACILITY_ID" NUMBER(10,0), 
	"OWNER_ID" NUMBER(10,0), 
	"FACILITY_HISTORY_CODE" VARCHAR2(5), 
	"FACILITY_HISTORY_DATE" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table FACILITY_HISTORY_CODES
--------------------------------------------------------

  CREATE TABLE "FACILITY_HISTORY_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table FACILITY_INSPECTION_CONTACT
--------------------------------------------------------

  CREATE TABLE "FACILITY_INSPECTION_CONTACT" 
   (	"MASTER_AI_ID" NUMBER(10,0), 
	"FULL_NAME" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table FEE_SUMMARY
--------------------------------------------------------

  CREATE TABLE "FEE_SUMMARY" 
   (	"SUMMARY_DATE" DATE, 
	"OWNER_ID" NUMBER(8,0), 
	"FISCAL_YEAR" NUMBER(38,0), 
	"TRANSACTION_CODE" VARCHAR2(5), 
	"AMOUNT" NUMBER(8,2)
   ) ;
--------------------------------------------------------
--  DDL for Table FILL_MATERIAL
--------------------------------------------------------

  CREATE TABLE "FILL_MATERIAL" 
   (	"CODE" VARCHAR2(1), 
	"DESCRIPTION" VARCHAR2(30)
   ) ;
--------------------------------------------------------
--  DDL for Table FINANCIAL_METHODS
--------------------------------------------------------

  CREATE TABLE "FINANCIAL_METHODS" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table FINANCIAL_PROVIDERS
--------------------------------------------------------

  CREATE TABLE "FINANCIAL_PROVIDERS" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table FINANCIAL_RESPONSIBILITIES
--------------------------------------------------------

  CREATE TABLE "FINANCIAL_RESPONSIBILITIES" 
   (	"ID" NUMBER(8,0), 
	"OWNER_ID" NUMBER(8,0), 
	"FACILITY_ID" NUMBER(10,0), 
	"FIN_METH_CODE" VARCHAR2(5), 
	"FIN_PROV_CODE" VARCHAR2(5), 
	"POLICY_NUMBER" VARCHAR2(25), 
	"BEGIN_DATE" DATE, 
	"END_DATE" DATE, 
	"AMOUNT" NUMBER(10,2), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table FISCAL_YEARS
--------------------------------------------------------

  CREATE TABLE "FISCAL_YEARS" 
   (	"FISCAL_YEAR" NUMBER(38,0), 
	"START_DATE" DATE, 
	"END_DATE" DATE, 
	"FEE_PER_TANK" NUMBER(8,2), 
	"MONTHLY_INTEREST_RATE" NUMBER(4,2), 
	"LATEFEE_PERCENTAGE" NUMBER(4,2), 
	"INVOICE_DUE_DATE" DATE, 
	"LATEFEE_MINIMUM" NUMBER(4,2)
   ) ;
--------------------------------------------------------
--  DDL for Table HOLD_PERMITS
--------------------------------------------------------

  CREATE TABLE "HOLD_PERMITS" 
   (	"OWNER_ID" NUMBER(8,0), 
	"FACILITY_ID" NUMBER(8,0), 
	"FISCAL_YEAR" NUMBER(38,0), 
	"TANKS" NUMBER(5,0), 
	"DATE_PERMITTED" DATE, 
	"PERMIT_NUMBER" NUMBER(38,0), 
	"DATE_PRINTED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table INSPECTIONS
--------------------------------------------------------

  CREATE TABLE "INSPECTIONS" 
   (	"ID" NUMBER(8,0), 
	"FACILITY_ID" NUMBER(8,0), 
	"INSPECTION_CODE" VARCHAR2(5), 
	"DATE_INSPECTED" DATE, 
	"CASE_ID" VARCHAR2(7), 
	"NOV_NUMBER" NUMBER(5,0), 
	"COMPLIANCE_DATE" DATE, 
	"STAFF_CODE" VARCHAR2(5), 
	"COMPLIANCE_SUBMIT_DATE" DATE, 
	"COMPLIANCE_ORDER_ISSUE_DATE" DATE, 
	"CONFERENCE" VARCHAR2(1), 
	"CONFERENCE_COMMENTS" VARCHAR2(200)
   ) ;

   COMMENT ON COLUMN "INSPECTIONS"."COMPLIANCE_SUBMIT_DATE" IS 'Certificate of Compliance Submitted Date.';
   COMMENT ON COLUMN "INSPECTIONS"."COMPLIANCE_ORDER_ISSUE_DATE" IS 'Compliance Order Issued Date.';
--------------------------------------------------------
--  DDL for Table INSPECTION_CODES
--------------------------------------------------------

  CREATE TABLE "INSPECTION_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table INVOICES
--------------------------------------------------------

  CREATE TABLE "INVOICES" 
   (	"ID" NUMBER(8,0), 
	"OWNER_ID" NUMBER(8,0), 
	"INSPECTION_ID" NUMBER(8,0), 
	"INVOICE_CODE" VARCHAR2(5), 
	"INVOICE_DATE" DATE, 
	"INVOICE_STATUS" VARCHAR2(10), 
	"DUE_DATE" DATE, 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"NOV_NUMBER" VARCHAR2(10), 
	"NOV_GPA_FACILITY_ID" NUMBER(8,0), 
	"NOV_GPA_AMOUNT" NUMBER(10,2), 
	"NOV_GPA_FISCAL_YEAR" NUMBER(4,0), 
	"LETTER_DATE" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table INVOICE_CODES
--------------------------------------------------------

  CREATE TABLE "INVOICE_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50), 
	"INVOICE_TEXT" LONG, 
	"CUPON_FORMAT" VARCHAR2(2000)
   ) ;
--------------------------------------------------------
--  DDL for Table INVOICE_DETAIL
--------------------------------------------------------

  CREATE TABLE "INVOICE_DETAIL" 
   (	"INVOICE_ID" NUMBER(8,0), 
	"FISCAL_YEAR" NUMBER(4,0), 
	"TANK_COUNT" NUMBER(4,0), 
	"TANK_FEE" NUMBER(8,2), 
	"TANK_FEE_INVOICED" NUMBER(8,2), 
	"TANK_FEE_WAIVER" NUMBER(8,2), 
	"TANK_FEE_WAIVER_USED" NUMBER(8,2), 
	"TANK_FEE_PAYMENT" NUMBER(8,2), 
	"TANK_FEE_BALANCE" NUMBER(8,2), 
	"LATE_FEE" NUMBER(8,2), 
	"LATE_FEE_INVOICED" NUMBER(8,2), 
	"LATE_FEE_WAIVER" NUMBER(8,2), 
	"LATE_FEE_WAIVER_USED" NUMBER(8,2), 
	"LATE_FEE_PAYMENT" NUMBER(8,2), 
	"LATE_FEE_BALANCE" NUMBER(8,2), 
	"INTEREST" NUMBER(8,2), 
	"INTEREST_INVOICED" NUMBER(8,2), 
	"INTEREST_MONTHS" NUMBER(5,0), 
	"INTEREST_WAIVER" NUMBER(8,2), 
	"INTEREST_WAIVER_USED" NUMBER(8,2), 
	"INTEREST_PAYMENT" NUMBER(8,2), 
	"INTEREST_BALANCE" NUMBER(8,2), 
	"SUM_BALANCES" NUMBER(8,2)
   ) ;
--------------------------------------------------------
--  DDL for Table INVOICE_DETAIL_FACILITIES
--------------------------------------------------------

  CREATE TABLE "INVOICE_DETAIL_FACILITIES" 
   (	"INVOICE_ID" NUMBER(8,0), 
	"FISCAL_YEAR" NUMBER(4,0), 
	"FACILITY_ID" NUMBER(8,0), 
	"TANK_COUNT" NUMBER(4,0), 
	"TANK_FEE_WAIVER" NUMBER(8,2)
   ) ;
--------------------------------------------------------
--  DDL for Table NOTICES
--------------------------------------------------------

  CREATE TABLE "NOTICES" 
   (	"ID" NUMBER(8,0), 
	"OPERATOR_ID" VARCHAR2(10), 
	"NOTICE_CODE" VARCHAR2(5), 
	"NOTICE_DATE" DATE, 
	"NOTICE_STATUS" VARCHAR2(10), 
	"DUE_DATE" DATE, 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"LETTER_DATE" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table NOTICE_DETAIL_FACILITIES
--------------------------------------------------------

  CREATE TABLE "NOTICE_DETAIL_FACILITIES" 
   (	"NOTICE_ID" NUMBER(10,0), 
	"FISCAL_YEAR" NUMBER(4,0), 
	"FACILITY_ID" NUMBER(10,0), 
	"OWNER_ID" NUMBER(10,0), 
	"TANK_COUNT" NUMBER(4,0)
   ) ;
--------------------------------------------------------
--  DDL for Table OPERATORS_MVW
--------------------------------------------------------

  CREATE TABLE "OPERATORS_MVW" 
   (	"ID" VARCHAR2(10), 
	"OPERATOR_NAME" VARCHAR2(50), 
	"ADDRESS1" VARCHAR2(50), 
	"ADDRESS2" VARCHAR2(50), 
	"CITY" VARCHAR2(25), 
	"STATE" VARCHAR2(2), 
	"ZIP" VARCHAR2(10), 
	"PHONE_NUMBER" VARCHAR2(20), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table OPERATORS_MVW_PROD
--------------------------------------------------------

  CREATE TABLE "OPERATORS_MVW_PROD" 
   (	"ID" VARCHAR2(10), 
	"OPERATOR_NAME" VARCHAR2(50), 
	"ADDRESS1" VARCHAR2(50), 
	"ADDRESS2" VARCHAR2(50), 
	"CITY" VARCHAR2(25), 
	"STATE" VARCHAR2(2), 
	"ZIP" VARCHAR2(10), 
	"PHONE_NUMBER" VARCHAR2(20), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table OWNERS_MVW
--------------------------------------------------------

  CREATE TABLE "OWNERS_MVW" 
   (	"ID" NUMBER, 
	"PER_ID" NUMBER, 
	"ORG_ID" NUMBER, 
	"OWNER_NAME" VARCHAR2(50), 
	"ADDRESS1" VARCHAR2(50), 
	"ADDRESS2" VARCHAR2(50), 
	"CITY" VARCHAR2(25), 
	"STATE" VARCHAR2(2), 
	"ZIP" VARCHAR2(10), 
	"PHONE_NUMBER" VARCHAR2(20), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table OWNER_CODES
--------------------------------------------------------

  CREATE TABLE "OWNER_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table OWNER_COMMENTS
--------------------------------------------------------

  CREATE TABLE "OWNER_COMMENTS" 
   (	"OWNER_ID" NUMBER(8,0), 
	"COMMENT_DATE" DATE, 
	"COMMENTS" LONG, 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table OWNER_WAIVERS
--------------------------------------------------------

  CREATE TABLE "OWNER_WAIVERS" 
   (	"OWNER_ID" NUMBER(8,0), 
	"WAIVER_CODE" VARCHAR2(2), 
	"FISCAL_YEAR" NUMBER(38,0), 
	"AMOUNT" NUMBER(8,2), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"FACILITY_ID" NUMBER(8,0), 
	"TANK_ID" NUMBER(8,0), 
	"WAIVER_COMMENT" VARCHAR2(2000)
   ) ;
--------------------------------------------------------
--  DDL for Table PENALTIES
--------------------------------------------------------

  CREATE TABLE "PENALTIES" 
   (	"INSPECTION_ID" NUMBER(8,0), 
	"PENALTY_CODE" VARCHAR2(15), 
	"USTR_NUMBER" VARCHAR2(15), 
	"PENALTY_OCCURANCE" NUMBER(38,0), 
	"DATE_CORRECTED" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table PENALTY_CODES
--------------------------------------------------------

  CREATE TABLE "PENALTY_CODES" 
   (	"CODE" VARCHAR2(15), 
	"DESCRIPTION" VARCHAR2(150), 
	"AMOUNT" NUMBER(5,0), 
	"END_DATE" DATE, 
	"DP_CATEGORY" VARCHAR2(10), 
	"SOC_CATEGORY" VARCHAR2(10), 
	"PENALTY_LEVEL" VARCHAR2(10), 
	"IS_SOC" CHAR(1), 
	"TANK_TYPE" CHAR(1)
   ) ;
--------------------------------------------------------
--  DDL for Table PERMITS
--------------------------------------------------------

  CREATE TABLE "PERMITS" 
   (	"OWNER_ID" NUMBER(8,0), 
	"FACILITY_ID" NUMBER(8,0), 
	"FISCAL_YEAR" NUMBER(38,0), 
	"TANKS" NUMBER(5,0), 
	"DATE_PERMITTED" DATE, 
	"PERMIT_NUMBER" NUMBER(38,0), 
	"DATE_PRINTED" DATE, 
	"AST_COUNT" NUMBER(2,0), 
	"UST_COUNT" NUMBER(2,0)
   ) ;

   COMMENT ON COLUMN "PERMITS"."TANKS" IS 'Number of Tanks.';
--------------------------------------------------------
--  DDL for Table QA_SETUP
--------------------------------------------------------

  CREATE TABLE "QA_SETUP" 
   (	"OWNER" VARCHAR2(30), 
	"TABLE_NAME" VARCHAR2(30), 
	"COLUMN_ID" NUMBER, 
	"COLUMN_ORDER" NUMBER, 
	"DISPLAY_WIDTH" NUMBER(3,0), 
	"ORDER_BY" VARCHAR2(2)
   ) ;
--------------------------------------------------------
--  DDL for Table ROLES
--------------------------------------------------------

  CREATE TABLE "ROLES" 
   (	"CODE" VARCHAR2(15), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table STAFF
--------------------------------------------------------

  CREATE TABLE "STAFF" 
   (	"CODE" VARCHAR2(5), 
	"EMPLOYEE_ID" NUMBER(38,0), 
	"RESTRICTED" VARCHAR2(1), 
	"FIRST_NAME" VARCHAR2(20), 
	"LAST_NAME" VARCHAR2(25), 
	"LOGIN_ID" VARCHAR2(30), 
	"STAFF_TYPE" CHAR(1), 
	"SEP_LOGIN_ID" VARCHAR2(30)
   ) ;
--------------------------------------------------------
--  DDL for Table STAFF_ROLES
--------------------------------------------------------

  CREATE TABLE "STAFF_ROLES" 
   (	"STAFF_CODE" VARCHAR2(5), 
	"ROLE_CODE" VARCHAR2(15)
   ) ;
--------------------------------------------------------
--  DDL for Table TANKS
--------------------------------------------------------

  CREATE TABLE "TANKS" 
   (	"ID" NUMBER(8,0), 
	"FACILITY_ID" NUMBER(8,0), 
	"REGISTRATION_NUMBER" VARCHAR2(15), 
	"TANK_STATUS_CODE" VARCHAR2(5), 
	"CAPACITY" NUMBER(6,0), 
	"MEETS_1988_REQ" VARCHAR2(1), 
	"QUANTITY_REMAINING" NUMBER(6,0), 
	"FILL_MATERIAL" VARCHAR2(1), 
	"HS_MIXTURE" VARCHAR2(1), 
	"HS_NAME" VARCHAR2(25), 
	"HS_NUMBER" NUMBER(10,0), 
	"COMMENTS" VARCHAR2(200), 
	"TANK_TYPE" VARCHAR2(1), 
	"OWNER_ID" NUMBER(10,0), 
	"MEETS_2011_REQ" VARCHAR2(1), 
	"MOVE_2_DUP" VARCHAR2(1), 
	"OPERATOR_ID" VARCHAR2(10)
   ) ;

   COMMENT ON COLUMN "TANKS"."HS_MIXTURE" IS 'Hazardous Substance Mixture.';
   COMMENT ON COLUMN "TANKS"."HS_NAME" IS 'Hazardous Substance Name.';
   COMMENT ON COLUMN "TANKS"."HS_NUMBER" IS 'Hazardous Substance Number.';
--------------------------------------------------------
--  DDL for Table TANK_DETAILS
--------------------------------------------------------

  CREATE TABLE "TANK_DETAILS" 
   (	"TANK_ID" NUMBER(8,0), 
	"TANK_DETAIL_CODE" VARCHAR2(5)
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_DETAIL_CODES
--------------------------------------------------------

  CREATE TABLE "TANK_DETAIL_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50), 
	"TANK_INFO_CODE" VARCHAR2(5)
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_FILL_MATERIAL_CODES
--------------------------------------------------------

  CREATE TABLE "TANK_FILL_MATERIAL_CODES" 
   (	"CODE" VARCHAR2(1), 
	"DESCRIPTION" VARCHAR2(30)
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_HISTORY
--------------------------------------------------------

  CREATE TABLE "TANK_HISTORY" 
   (	"TANK_ID" NUMBER(8,0), 
	"HISTORY_DATE" DATE, 
	"OWNER_ID" NUMBER(8,0), 
	"HISTORY_CODE" VARCHAR2(5)
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_HISTORY_CODES
--------------------------------------------------------

  CREATE TABLE "TANK_HISTORY_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_INFO_CODES
--------------------------------------------------------

  CREATE TABLE "TANK_INFO_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_OPERATOR_HISTORY
--------------------------------------------------------

  CREATE TABLE "TANK_OPERATOR_HISTORY" 
   (	"TANK_ID" NUMBER(10,0), 
	"OPERATOR_ID" VARCHAR2(10), 
	"START_DATE" DATE, 
	"END_DATE" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table TANK_STATUS_CODES
--------------------------------------------------------

  CREATE TABLE "TANK_STATUS_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table TRANSACTIONS
--------------------------------------------------------

  CREATE TABLE "TRANSACTIONS" 
   (	"ID" NUMBER(8,0), 
	"OWNER_ID" NUMBER(8,0), 
	"INSPECTION_ID" NUMBER(8,0), 
	"INVOICE_ID" NUMBER(8,0), 
	"TRANSACTION_CODE" VARCHAR2(5), 
	"TRANSACTION_STATUS" VARCHAR2(6), 
	"TRANSACTION_DATE" DATE, 
	"FISCAL_YEAR" NUMBER(38,0), 
	"AMOUNT" NUMBER(8,2), 
	"CHECK_NUMBER" VARCHAR2(25), 
	"NAME_ON_CHECK" VARCHAR2(50), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"OPERATOR_ID" VARCHAR2(10), 
	"OPERATOR_PAYMENT" VARCHAR2(1), 
	"COMMENTS" VARCHAR2(240), 
	"DEPOSIT_DATE" DATE
   ) ;
--------------------------------------------------------
--  DDL for Table TRANSACTION_CODES
--------------------------------------------------------

  CREATE TABLE "TRANSACTION_CODES" 
   (	"CODE" VARCHAR2(5), 
	"DESCRIPTION" VARCHAR2(50)
   ) ;
--------------------------------------------------------
--  DDL for Table UST_CONTROL
--------------------------------------------------------

  CREATE TABLE "UST_CONTROL" 
   (	"PARAMETER" VARCHAR2(40), 
	"PARAMETER_TEXT" VARCHAR2(200), 
	"PARAMETER_VALUE" NUMBER
   ) ;
--------------------------------------------------------
--  DDL for Table UST_FACILITIES
--------------------------------------------------------

  CREATE TABLE "UST_FACILITIES" 
   (	"ID" NUMBER(8,0), 
	"OLD_ID" NUMBER(8,0), 
	"OWNER_ID" NUMBER(8,0), 
	"INDIAN" VARCHAR2(1), 
	"FEDERAL" VARCHAR2(1), 
	"SETTLEMENT" VARCHAR2(1), 
	"DATE_RECEIVED" DATE, 
	"FACILITY_NAME" VARCHAR2(50), 
	"ADDRESS1" VARCHAR2(50), 
	"ADDRESS2" VARCHAR2(50), 
	"CITY" VARCHAR2(25), 
	"STATE" VARCHAR2(2), 
	"ZIP" VARCHAR2(10), 
	"CONTACT_LEGAL_ENTITY_ID" NUMBER(8,0), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"AI_ID" NUMBER(10,0)
   ) ;

   COMMENT ON COLUMN "UST_FACILITIES"."DATE_RECEIVED" IS 'Date the application for registration was received.';
--------------------------------------------------------
--  DDL for Table UST_LOG
--------------------------------------------------------

  CREATE TABLE "UST_LOG" 
   (	"PROCESS_ID" NUMBER, 
	"LOG_TIMESTAMP" DATE, 
	"LOG_TEXT" VARCHAR2(2000)
   ) ;
--------------------------------------------------------
--  DDL for Table UST_OWNERS
--------------------------------------------------------

  CREATE TABLE "UST_OWNERS" 
   (	"ID" NUMBER(8,0), 
	"OLD_ID" NUMBER(8,0), 
	"OWNER_CODE" VARCHAR2(5), 
	"FEDERAL_ID" VARCHAR2(15), 
	"OWNER_NAME" VARCHAR2(50), 
	"ADDRESS1" VARCHAR2(50), 
	"ADDRESS2" VARCHAR2(50), 
	"CITY" VARCHAR2(25), 
	"STATE" VARCHAR2(2), 
	"ZIP" VARCHAR2(10), 
	"PHONE_NUMBER" VARCHAR2(20), 
	"USER_CREATED" VARCHAR2(30), 
	"DATE_CREATED" DATE, 
	"USER_MODIFIED" VARCHAR2(30), 
	"DATE_MODIFIED" DATE, 
	"ORG_ID" NUMBER(10,0), 
	"PER_ID" NUMBER(10,0)
   ) ;
--------------------------------------------------------
--  DDL for Table UST_REF_CODES
--------------------------------------------------------

  CREATE TABLE "UST_REF_CODES" 
   (	"RV_LOW_VALUE" VARCHAR2(240), 
	"RV_HIGH_VALUE" VARCHAR2(240), 
	"RV_ABBREVIATION" VARCHAR2(240), 
	"RV_DOMAIN" VARCHAR2(100), 
	"RV_MEANING" VARCHAR2(240), 
	"RV_TYPE" VARCHAR2(10)
   ) ;
--------------------------------------------------------
--  DDL for Table UST_ROLE_PRIVS
--------------------------------------------------------

  CREATE TABLE "UST_ROLE_PRIVS" 
   (	"TABLENAME" VARCHAR2(50), 
	"ROLENAME" VARCHAR2(15), 
	"SECURITY" VARCHAR2(10)
   ) ;
























































--------------------------------------------------------
--  DDL for Index CITY_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "CITY_PK" ON "CITIES" ("CITY") 
  ;
--------------------------------------------------------
--  DDL for Index COUNTY_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "COUNTY_PK" ON "COUNTIES" ("COUNTY") 
  ;
--------------------------------------------------------
--  DDL for Index FACILITIES_MVW_IDX1
--------------------------------------------------------

  CREATE INDEX "FACILITIES_MVW_IDX1" ON "FACILITIES_MVW" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index FACILITIES_MVW_IDX2
--------------------------------------------------------

  CREATE INDEX "FACILITIES_MVW_IDX2" ON "FACILITIES_MVW" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index FACILITIES_MVW_IDX3
--------------------------------------------------------

  CREATE INDEX "FACILITIES_MVW_IDX3" ON "FACILITIES_MVW" ("FACILITY_NAME") 
  ;
--------------------------------------------------------
--  DDL for Index FACILITY_OWNER_FK_I
--------------------------------------------------------

  CREATE INDEX "FACILITY_OWNER_FK_I" ON "UST_FACILITIES" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index FACILITY_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "FACILITY_PK" ON "UST_FACILITIES" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index FAC_INSP_CONTACT_AI
--------------------------------------------------------

  CREATE INDEX "FAC_INSP_CONTACT_AI" ON "FACILITY_INSPECTION_CONTACT" ("MASTER_AI_ID") 
  ;
--------------------------------------------------------
--  DDL for Index FIN_METH_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "FIN_METH_PK" ON "FINANCIAL_METHODS" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index FIN_PROV_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "FIN_PROV_PK" ON "FINANCIAL_PROVIDERS" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index FIN_RESP_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "FIN_RESP_PK" ON "FINANCIAL_RESPONSIBILITIES" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index FISCAL_YEARS_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "FISCAL_YEARS_PK" ON "FISCAL_YEARS" ("FISCAL_YEAR") 
  ;
--------------------------------------------------------
--  DDL for Index INSPECTION_FACILITY_FK_I
--------------------------------------------------------

  CREATE INDEX "INSPECTION_FACILITY_FK_I" ON "INSPECTIONS" ("FACILITY_ID") 
  ;
--------------------------------------------------------
--  DDL for Index INSPECTION_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "INSPECTION_PK" ON "INSPECTIONS" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index INSP_CODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "INSP_CODE_PK" ON "INSPECTION_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index INVOICE_DETAIL_FEE_IDX
--------------------------------------------------------

  CREATE INDEX "INVOICE_DETAIL_FEE_IDX" ON "INVOICE_DETAIL" ("INVOICE_ID", "TANK_FEE_BALANCE", "LATE_FEE_BALANCE", "INTEREST_BALANCE") 
  ;
--------------------------------------------------------
--  DDL for Index INVOICE_DETAIL_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "INVOICE_DETAIL_PK" ON "INVOICE_DETAIL" ("INVOICE_ID", "FISCAL_YEAR") 
  ;
--------------------------------------------------------
--  DDL for Index INV_CODE_FK_I
--------------------------------------------------------

  CREATE INDEX "INV_CODE_FK_I" ON "INVOICES" ("INVOICE_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index INV_COD_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "INV_COD_PK" ON "INVOICE_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index INV_INSPECTION_FK_I
--------------------------------------------------------

  CREATE INDEX "INV_INSPECTION_FK_I" ON "INVOICES" ("INSPECTION_ID") 
  ;
--------------------------------------------------------
--  DDL for Index INV_OPERATOR_FK_I
--------------------------------------------------------

  CREATE INDEX "INV_OPERATOR_FK_I" ON "NOTICES" ("OPERATOR_ID") 
  ;
--------------------------------------------------------
--  DDL for Index INV_OWNER_FK_I
--------------------------------------------------------

  CREATE INDEX "INV_OWNER_FK_I" ON "INVOICES" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index INV_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "INV_PK" ON "INVOICES" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index NOTI_INV_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "NOTI_INV_PK" ON "NOTICES" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index OWNERS_MVW_IDX1
--------------------------------------------------------

  CREATE INDEX "OWNERS_MVW_IDX1" ON "OWNERS_MVW" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index OWNERS_MVW_IDX2
--------------------------------------------------------

  CREATE INDEX "OWNERS_MVW_IDX2" ON "OWNERS_MVW" ("OWNER_NAME") 
  ;
--------------------------------------------------------
--  DDL for Index OWNER_CODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "OWNER_CODE_PK" ON "OWNER_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index OWNER_COMM_OWNER_FK_I
--------------------------------------------------------

  CREATE INDEX "OWNER_COMM_OWNER_FK_I" ON "OWNER_COMMENTS" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index OWNER_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "OWNER_PK" ON "UST_OWNERS" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index OWNER_WAIVER_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "OWNER_WAIVER_PK" ON "OWNER_WAIVERS" ("OWNER_ID", "FISCAL_YEAR", "WAIVER_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index OWN_UK
--------------------------------------------------------

  CREATE UNIQUE INDEX "OWN_UK" ON "UST_OWNERS" ("OWNER_NAME") 
  ;
--------------------------------------------------------
--  DDL for Index OWN_WAIV_OWNER_FK_I
--------------------------------------------------------

  CREATE INDEX "OWN_WAIV_OWNER_FK_I" ON "OWNER_WAIVERS" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index PENALTY_INSPECTION_FK_I
--------------------------------------------------------

  CREATE INDEX "PENALTY_INSPECTION_FK_I" ON "PENALTIES" ("INSPECTION_ID") 
  ;
--------------------------------------------------------
--  DDL for Index PENALTY_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "PENALTY_PK" ON "PENALTIES" ("INSPECTION_ID", "PENALTY_CODE", "USTR_NUMBER") 
  ;
--------------------------------------------------------
--  DDL for Index PENAL_CODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "PENAL_CODE_PK" ON "PENALTY_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index PERMIT_FACILITY_FK_I
--------------------------------------------------------

  CREATE INDEX "PERMIT_FACILITY_FK_I" ON "PERMITS" ("FACILITY_ID") 
  ;
--------------------------------------------------------
--  DDL for Index PERMIT_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "PERMIT_PK" ON "PERMITS" ("OWNER_ID", "FACILITY_ID", "FISCAL_YEAR") 
  ;
--------------------------------------------------------
--  DDL for Index ROLE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "ROLE_PK" ON "ROLES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index STAFF_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "STAFF_PK" ON "STAFF" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index STAFF_ROLE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "STAFF_ROLE_PK" ON "STAFF_ROLES" ("ROLE_CODE", "STAFF_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index SUM_BALANCES_IDX
--------------------------------------------------------

  CREATE INDEX "SUM_BALANCES_IDX" ON "INVOICE_DETAIL" ("INVOICE_ID", "FISCAL_YEAR", "SUM_BALANCES") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_DCODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_DCODE_PK" ON "TANK_DETAIL_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_DETAIL_INFO_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_DETAIL_INFO_FK_I" ON "TANK_DETAIL_CODES" ("TANK_INFO_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_DETAI_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_DETAI_PK" ON "TANK_DETAILS" ("TANK_ID", "TANK_DETAIL_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_DETAI_TANK_DCODE_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_DETAI_TANK_DCODE_FK_I" ON "TANK_DETAILS" ("TANK_DETAIL_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_DETAI_TANK_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_DETAI_TANK_FK_I" ON "TANK_DETAILS" ("TANK_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_FACILITY_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_FACILITY_FK_I" ON "TANKS" ("FACILITY_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_HIST_OWNER_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_HIST_OWNER_FK_I" ON "TANK_HISTORY" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_HIST_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_HIST_PK" ON "TANK_HISTORY" ("HISTORY_DATE", "HISTORY_CODE", "OWNER_ID", "TANK_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_HIST_TANK_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_HIST_TANK_FK_I" ON "TANK_HISTORY" ("TANK_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_HIST_THIST_CODE_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_HIST_THIST_CODE_FK_I" ON "TANK_HISTORY" ("HISTORY_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_ICODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_ICODE_PK" ON "TANK_INFO_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_OPR_HIST_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_OPR_HIST_PK" ON "TANK_OPERATOR_HISTORY" ("TANK_ID", "OPERATOR_ID", "START_DATE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_PK" ON "TANKS" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_SCODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TANK_SCODE_PK" ON "TANK_STATUS_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TANK_STATUS_CODE_FK_I
--------------------------------------------------------

  CREATE INDEX "TANK_STATUS_CODE_FK_I" ON "TANKS" ("TANK_STATUS_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index THIST_CODE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "THIST_CODE_PK" ON "TANK_HISTORY_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TRANS_CODE_FK_I
--------------------------------------------------------

  CREATE INDEX "TRANS_CODE_FK_I" ON "TRANSACTIONS" ("TRANSACTION_CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TRANS_COD_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TRANS_COD_PK" ON "TRANSACTION_CODES" ("CODE") 
  ;
--------------------------------------------------------
--  DDL for Index TRANS_INV_FK_I
--------------------------------------------------------

  CREATE INDEX "TRANS_INV_FK_I" ON "TRANSACTIONS" ("INVOICE_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TRANS_OWNER_FK_I
--------------------------------------------------------

  CREATE INDEX "TRANS_OWNER_FK_I" ON "TRANSACTIONS" ("OWNER_ID") 
  ;
--------------------------------------------------------
--  DDL for Index TRANS_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "TRANS_PK" ON "TRANSACTIONS" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index USTX_AB_CERT_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "USTX_AB_CERT_PK" ON "AB_CERT" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index USTX_E_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "USTX_E_PK" ON "EMAILS" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index USTX_TRAINEE_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "USTX_TRAINEE_PK" ON "AB_OPERATOR" ("ID") 
  ;
























































--------------------------------------------------------
--  DDL for Trigger BIUR_INVOICE_DETAIL
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "BIUR_INVOICE_DETAIL" 
BEFORE INSERT OR UPDATE
ON USTX.INVOICE_DETAIL
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW
begin
 :new.sum_balances  := nvl(:new.tank_fee_balance,0) +
                     nvl(:new.late_fee_balance,0) +
                     nvl(:new.interest_balance,0);
end;

/
ALTER TRIGGER "BIUR_INVOICE_DETAIL" ENABLE;
--------------------------------------------------------
--  DDL for Trigger INSERT_FACILITIES
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "INSERT_FACILITIES" 
BEFORE INSERT
ON USTX.UST_FACILITIES
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW
begin
    if :new.owner_id is not null then
      begin
        insert into os_legal_entity_location_assoc
               ( legal_entity_id, location_id, owner_application)
        values ( :new.owner_id, :new.id, 'UST' );
      exception
        when others then
          null;
      end;
    end if;
--
    if :new.contact_legal_entity_id is not null then
      begin
        insert into os_legal_entity_location_assoc
               ( legal_entity_id, location_id, owner_application )
        values (:new.contact_legal_entity_id, :new.id,'UST');
      exception
        when others then
          null;
      end;
      begin
        update os_legal_entities set parent_le_id = :new.owner_id
        where  id = :new.contact_legal_entity_id;
      exception
        when others then
          null;
      end;
    end if;
  end;
/
ALTER TRIGGER "INSERT_FACILITIES" DISABLE;
--------------------------------------------------------
--  DDL for Trigger TBIUD_EMAILS
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "TBIUD_EMAILS" 
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
ALTER TRIGGER "TBIUD_EMAILS" ENABLE;
--------------------------------------------------------
--  DDL for Trigger TBIUD_STAFF
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "TBIUD_STAFF" 
before insert or update
on ustx.staff
for each row
begin
  :new.login_id := upper(:new.login_id);
end ;
/
ALTER TRIGGER "TBIUD_STAFF" ENABLE;
--------------------------------------------------------
--  DDL for Trigger UPDATE_FACILITIES
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "UPDATE_FACILITIES" 
BEFORE UPDATE
ON USTX.UST_FACILITIES
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW
begin
    if :new.owner_id != :old.owner_id then
      begin
        delete os_legal_entity_location_assoc
        where  legal_entity_id = :old.owner_id
        and    location_id = :old.id
        and    owner_application = 'UST';
      exception
        when others then
          null;
      end;
      begin
        insert into os_legal_entity_location_assoc
               ( legal_entity_id, location_id, owner_application )
        values ( :new.owner_id, :new.id, 'UST' );
      exception
        when others then
          null;
      end;
    end if;
--
    if :new.contact_legal_entity_id != :old.contact_legal_entity_id then
      begin
        delete os_legal_entity_location_assoc
        where  legal_entity_id = :old.contact_legal_entity_id
        and    location_id = :old.id
        and    owner_application = 'UST';
      exception
        when others then
          null;
      end;
--
      begin
        insert into os_legal_entity_location_assoc
               ( legal_entity_id, location_id, owner_application )
        values (:new.contact_legal_entity_id, :new.id,'UST');
      exception
        when others then
          null;
      end;
      begin
        update os_legal_entities set parent_le_id = :new.owner_id
        where  id = :new.contact_legal_entity_id;
      exception
        when others then
          null;
      end;
    end if;
--
end;

/
ALTER TRIGGER "UPDATE_FACILITIES" DISABLE;
--------------------------------------------------------
--  DDL for View CITIES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "CITIES_VIEW" ("CITY", "FIPS_CODE") AS 
  select substr(upper(municipality_desc),1,50) city,
parish_or_county_code fips_code
from tempo.mtb_municipality
where inactive_flag = 'N'
with read only
;
--------------------------------------------------------
--  DDL for View COUNTIES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "COUNTIES_VIEW" ("COUNTY", "FIPS_CODE") AS 
  select upper(parish_or_county_desc) county,
fips_code
from mtb_parish_county
where fips_code is not null
and inactive_flag = 'N'
with read only
;
--------------------------------------------------------
--  DDL for View FACILITIES_MVW_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "FACILITIES_MVW_VIEW" ("ID", "AI_ID", "FACILITY_NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "OWNER_ID", "INDIAN", "USER_MODIFIED", "DATE_MODIFIED") AS 
  SELECT "ID","AI_ID","FACILITY_NAME","ADDRESS1","ADDRESS2","CITY","STATE","ZIP","OWNER_ID","INDIAN","USER_MODIFIED","DATE_MODIFIED"
      FROM USTX.FACILITIES_MVW;
--------------------------------------------------------
--  DDL for View FACILITY_TANK_HISTORY_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "FACILITY_TANK_HISTORY_VIEW" ("HISTORY_DATE", "HISTORY_CODE", "TANK_ID", "TANK_STATUS_CODE", "CAPACITY", "MEETS_1988_REQ", "QUANTITY_REMAINING", "FILL_MATERIAL", "HS_MIXTURE", "HS_NAME", "HS_NUMBER", "COMMENTS", "FACILITY_ID", "OWNER_ID", "NAME", "ADDRESS1", "ADDRESS2", "CITY", "COUNTY_CODE", "DISTRICT", "STATE", "ZIP", "PHONE_NUMBER", "INDIAN", "OWNER_NAME") AS 
  select history_date,
         history_code,
         tanks.id tank_id,
         tank_status_code,
         capacity,
         meets_1988_req,
         quantity_remaining,
         fill_material,
         hs_mixture,
         hs_name,
         hs_number,
         tanks.comments,
         locations.id,
         facilities.owner_id,
         locations.name,
         locations.address1,
         locations.address2,
         locations.city,
         locations.county,
         locations.district,
	     locations.state,
	     locations.zip,
         null phone_number,
         facilities.indian,
	     owners.owner_name
from tank_history, 
	 tanks, 
	 ust_locations_mvw locations,
	 owners_mvw owners, 
	 facilities_mvw facilities
where tank_history.tank_id = tanks.id
and facilities.owner_id = owners.id
and tanks.facility_id = locations.id
and locations.id = facilities.id
with read only
;
--------------------------------------------------------
--  DDL for View INSPECTIONS_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "INSPECTIONS_VIEW" ("ID", "FACILITY_ID", "INSPECTION_CODE", "DATE_INSPECTED", "CASE_ID", "NOV_NUMBER", "COMPLIANCE_DATE", "STAFF_CODE", "COMPLIANCE_SUBMIT_DATE", "COMPLIANCE_ORDER_ISSUE_DATE", "CONFERENCE", "CONFERENCE_COMMENTS") AS 
  SELECT "ID","FACILITY_ID","INSPECTION_CODE","DATE_INSPECTED","CASE_ID","NOV_NUMBER","COMPLIANCE_DATE","STAFF_CODE","COMPLIANCE_SUBMIT_DATE","COMPLIANCE_ORDER_ISSUE_DATE","CONFERENCE","CONFERENCE_COMMENTS"
      FROM USTX.INSPECTIONS;
--------------------------------------------------------
--  DDL for View INSPECTION_CODES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "INSPECTION_CODES_VIEW" ("CODE", "DESCRIPTION") AS 
  SELECT "CODE","DESCRIPTION"
      FROM USTX.INSPECTION_CODES;
--------------------------------------------------------
--  DDL for View LUST_SL_CON_AMEND_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "LUST_SL_CON_AMEND_VIEW" ("SC_ID", "SUM_SL_CON_AMEND_AMOUNT") AS 
  ( select 
    a.id sc_id, 
    sum(nvl(b.amount,0)) sum_sl_con_amend_amount
  from 
    lust.lust_sl_contracts a, 
    lust.lust_sl_contract_amendments b
  where 
     a.id = b.sc_id (+)
  group by a.id);
--------------------------------------------------------
--  DDL for View NOTICES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "NOTICES_VIEW" ("ID", "OPERATOR_ID", "NOTICE_CODE", "NOTICE_DATE", "NOTICE_STATUS", "DUE_DATE", "USER_CREATED", "DATE_CREATED", "USER_MODIFIED", "DATE_MODIFIED", "LETTER_DATE") AS 
  SELECT "ID","OPERATOR_ID","NOTICE_CODE","NOTICE_DATE","NOTICE_STATUS","DUE_DATE","USER_CREATED","DATE_CREATED","USER_MODIFIED","DATE_MODIFIED","LETTER_DATE"
      FROM USTX.NOTICES;
--------------------------------------------------------
--  DDL for View NOTICE_DETAIL_FAC_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "NOTICE_DETAIL_FAC_VIEW" ("NOTICE_ID", "FISCAL_YEAR", "FACILITY_ID", "OWNER_ID", "TANK_COUNT") AS 
  SELECT "NOTICE_ID","FISCAL_YEAR","FACILITY_ID","OWNER_ID","TANK_COUNT"
      FROM USTX.NOTICE_DETAIL_FACILITIES;
--------------------------------------------------------
--  DDL for View OPERATORS_MVW_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "OPERATORS_MVW_VIEW" ("ID", "OPERATOR_NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "PHONE_NUMBER", "DATE_CREATED", "USER_MODIFIED", "DATE_MODIFIED") AS 
  SELECT "ID","OPERATOR_NAME","ADDRESS1","ADDRESS2","CITY","STATE","ZIP","PHONE_NUMBER","DATE_CREATED","USER_MODIFIED","DATE_MODIFIED"
      FROM USTX.OPERATORS_MVW;
--------------------------------------------------------
--  DDL for View OWNERS_MVW_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "OWNERS_MVW_VIEW" ("ID", "PER_ID", "ORG_ID", "OWNER_NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "PHONE_NUMBER", "DATE_CREATED", "USER_MODIFIED", "DATE_MODIFIED") AS 
  SELECT "ID","PER_ID","ORG_ID","OWNER_NAME","ADDRESS1","ADDRESS2","CITY","STATE","ZIP","PHONE_NUMBER","DATE_CREATED","USER_MODIFIED","DATE_MODIFIED"
      FROM USTX.OWNERS_MVW;
--------------------------------------------------------
--  DDL for View OWNER_CODES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "OWNER_CODES_VIEW" ("CODE", "DESCRIPTION") AS 
  SELECT "CODE","DESCRIPTION"
      FROM USTX.OWNER_CODES;
--------------------------------------------------------
--  DDL for View OWNER_PAYMENTS_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "OWNER_PAYMENTS_VIEW" ("OWNER_ID", "FISCAL_YEAR", "INSPECTION_ID", "NOV_NUMBER", "TRANSACTION_TYPE", "AMOUNT") AS 
  select a.owner_id, a.fiscal_year,
         c.id inspection_id, c.nov_number,
         decode(transaction_code,'ICA','J','ICP','J',substr(transaction_code,1,1)) transaction_type,
--         nvl(invoice_id,0) invoice_id,
         sum(decode(transaction_code,
                    'PA', amount, 'PW', -amount, 'PP', -amount,
                    'LA', amount, 'LW', -amount, 'LP', -amount,
                    'IA', amount, 'IW', -amount, 'IP', -amount,
                    'ICA', amount, 'ICP', -amount,
                    'SCA', amount, 'SCP', -amount,
                    'GWAA', amount, 'GWAP', -amount,
                    'HWEA', amount, 'HWEP', -amount)) amount
    from transactions a, inspections c
    where a.inspection_id = c.id (+)
    group by a.owner_id, a.fiscal_year, c.id, c.nov_number,
         decode(transaction_code,'ICA','J','ICP','J',substr(transaction_code,1,1))
--, nvl(invoice_id,0)                                                           
;
--------------------------------------------------------
--  DDL for View OWNER_TRANSACTIONS_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "OWNER_TRANSACTIONS_VIEW" ("OWNER_ID", "FISCAL_YEAR", "PRINCIPAL_ASSESSMENT", "PRINCIPAL_PAYMENT", "PRINCIPAL_WAIVER", "LATE_FEE_ASSESSMENT", "LATE_FEE_PAYMENT", "LATE_FEE_WAIVER", "INTEREST_ASSESSMENT", "INTEREST_PAYMENT", "INTEREST_WAIVER", "SCI_CERT_ASSESSMENT", "SCI_CERT_PAYMENT", "INSTALLER_CERT_ASSESSMENT", "INSTALLER_CERT_PAYMENT", "GWA_ASSESSMENT", "GWA_PAYMENT", "HWE_ASSESSMENT", "HWE_PAYMENT", "REFUND") AS 
  select transactions.owner_id owner_id, 
transactions.fiscal_year fiscal_year, 
         sum(decode(transaction_code, 'PA', nvl 
(transactions.amount,0), 0)) principal_assessment, 
         sum(decode(transaction_code, 'PP', nvl 
(transactions.amount,0), 0)) principal_payment, 
         sum(decode(transaction_code, 'PW', nvl 
(transactions.amount,0), 0)) principal_waiver, 
         sum(decode(transaction_code, 'LA', nvl 
(transactions.amount,0), 0)) late_fee_assessment, 
         sum(decode(transaction_code, 'LP', nvl 
(transactions.amount,0), 0)) late_fee_payment, 
         sum(decode(transaction_code, 'LW', nvl 
(transactions.amount,0), 0)) late_fee_waiver, 
         sum(decode(transaction_code, 'IA', nvl 
(transactions.amount,0), 0)) interest_assessment, 
         sum(decode(transaction_code, 'IP', nvl 
(transactions.amount,0), 0)) interest_payment, 
         sum(decode(transaction_code, 'IW', nvl 
(transactions.amount,0), 0)) interest_waiver, 
         sum(decode(transaction_code, 'SCA', nvl 
(transactions.amount,0),0)) sci_cert_assessment, 
         sum(decode(transaction_code, 'SCP', nvl 
(transactions.amount,0),0)) sci_cert_payment, 
         sum(decode(transaction_code, 'ICA', nvl 
(transactions.amount,0),0)) installer_cert_assessment, 
         sum(decode(transaction_code, 'ICP', nvl 
(transactions.amount,0),0)) installer_cert_payment, 
         sum(decode(transaction_code, 'GWAA', nvl 
(transactions.amount,0),0)) gwa_assessment, 
         sum(decode(transaction_code, 'GWAP', nvl 
(transactions.amount,0),0)) gwa_payment, 
         sum(decode(transaction_code, 'HWEA', nvl 
(transactions.amount,0),0)) hwe_assessment, 
         sum(decode(transaction_code, 'HWEP', nvl 
(transactions.amount,0),0)) hwe_payment, 
         sum(decode(transaction_code, 'R', nvl 
(transactions.amount,0),0)) refund 
    from ustx.transactions 
    group by transactions.owner_id, transactions.fiscal_year 
with read only
;
--------------------------------------------------------
--  DDL for View PENALTIES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "PENALTIES_VIEW" ("INSPECTION_ID", "PENALTY_CODE", "USTR_NUMBER", "PENALTY_OCCURANCE", "DATE_CORRECTED") AS 
  SELECT "INSPECTION_ID","PENALTY_CODE","USTR_NUMBER","PENALTY_OCCURANCE","DATE_CORRECTED"
      FROM USTX.PENALTIES;
--------------------------------------------------------
--  DDL for View PENALTY_CODES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "PENALTY_CODES_VIEW" ("CODE", "DESCRIPTION", "AMOUNT") AS 
  SELECT "CODE","DESCRIPTION","AMOUNT"
      FROM USTX.PENALTY_CODES;
--------------------------------------------------------
--  DDL for View TANKS_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANKS_VIEW" ("ID", "FACILITY_ID", "REGISTRATION_NUMBER", "TANK_STATUS_CODE", "CAPACITY", "MEETS_1988_REQ", "QUANTITY_REMAINING", "FILL_MATERIAL", "HS_MIXTURE", "HS_NAME", "HS_NUMBER", "COMMENTS", "TANK_TYPE", "OWNER_ID", "MEETS_2011_REQ", "MOVE_2_DUP", "OPERATOR_ID") AS 
  SELECT "ID","FACILITY_ID","REGISTRATION_NUMBER","TANK_STATUS_CODE","CAPACITY","MEETS_1988_REQ","QUANTITY_REMAINING","FILL_MATERIAL","HS_MIXTURE","HS_NAME","HS_NUMBER","COMMENTS","TANK_TYPE","OWNER_ID","MEETS_2011_REQ","MOVE_2_DUP","OPERATOR_ID"
      FROM USTX.TANKS;
--------------------------------------------------------
--  DDL for View TANK_DETAILS_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANK_DETAILS_VIEW" ("TANK_ID", "TANK_DETAIL_CODE", "CODE_DESC", "TANK_INFO_CODE", "INFO_DESC") AS 
  select
tank_details.tank_id,
tank_details.tank_detail_code,
tank_detail_codes.description code_desc,
tank_detail_codes.tank_info_code,
tank_info_codes.description info_desc
from ustx.tank_details,
ustx.tank_detail_codes,
ustx.tank_info_codes
where tank_details.tank_detail_code = tank_detail_codes.code
and tank_detail_codes.tank_info_code = tank_info_codes.code;
--------------------------------------------------------
--  DDL for View TANK_DETAILS_VW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANK_DETAILS_VW" ("TANK_ID", "TANK_DETAIL_CODE") AS 
  SELECT "TANK_ID","TANK_DETAIL_CODE"
      FROM USTX.TANK_DETAILS;
--------------------------------------------------------
--  DDL for View TANK_DETAIL_CODES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANK_DETAIL_CODES_VIEW" ("CODE", "DESCRIPTION", "TANK_INFO_CODE") AS 
  SELECT "CODE","DESCRIPTION","TANK_INFO_CODE"
      FROM USTX.TANK_DETAIL_CODES;
--------------------------------------------------------
--  DDL for View TANK_HISTORY_CODES_VW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANK_HISTORY_CODES_VW" ("CODE", "DESCRIPTION") AS 
  SELECT "CODE","DESCRIPTION"
      FROM USTX.TANK_HISTORY_CODES;
--------------------------------------------------------
--  DDL for View TANK_HISTORY_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANK_HISTORY_VIEW" ("TANK_ID", "FACILITY_ID", "DATE_INSTALLED", "DATE_REMOVED") AS 
  select a.id tank_id, a.facility_id facility_id,
min(b.history_date) date_installed, min(c.history_date) date_removed
from tanks a,
(select tank_id, history_date from ustx.tank_history
 where history_code = 'I') b,
(select tank_id, history_date from ustx.tank_history
 where history_code in ('R','F')) c
where a.id = b.tank_id (+)
and a.id = c.tank_id (+)
group by a.id, a.facility_id
;
--------------------------------------------------------
--  DDL for View TANK_HISTORY_VW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "TANK_HISTORY_VW" ("TANK_ID", "HISTORY_DATE", "OWNER_ID", "HISTORY_CODE") AS 
  SELECT "TANK_ID","HISTORY_DATE","OWNER_ID","HISTORY_CODE"
      FROM USTX.TANK_HISTORY;
--------------------------------------------------------
--  DDL for View UST_FACILITIES_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "UST_FACILITIES_VIEW" ("ID", "OLD_ID", "OWNER_ID", "INDIAN", "FEDERAL", "SETTLEMENT", "DATE_RECEIVED", "FACILITY_NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "CONTACT_LEGAL_ENTITY_ID", "USER_CREATED", "DATE_CREATED", "USER_MODIFIED", "DATE_MODIFIED", "AI_ID") AS 
  SELECT "ID","OLD_ID","OWNER_ID","INDIAN","FEDERAL","SETTLEMENT","DATE_RECEIVED","FACILITY_NAME","ADDRESS1","ADDRESS2","CITY","STATE","ZIP","CONTACT_LEGAL_ENTITY_ID","USER_CREATED","DATE_CREATED","USER_MODIFIED","DATE_MODIFIED","AI_ID"
      FROM USTX.UST_FACILITIES;
--------------------------------------------------------
--  DDL for View UST_LOCATIONS_MVW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "UST_LOCATIONS_MVW" ("ID", "AI_ID", "NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "COUNTY", "DISTRICT", "LAT_DEGREES", "LAT_MINUTES", "LAT_SECONDS", "LON_DEGREES", "LON_MINUTES", "LON_SECONDS", "LAT_DEGREES_DECIMAL", "LON_DEGREES_DECIMAL", "LL_DATE", "LL_METHOD", "LL_DESCRIPTION", "LL_DATUM", "LL_ACCURACY", "USER_MODIFIED", "DATE_MODIFIED") AS 
  select to_number(trim(si.subject_item_designation))                   id,
       si.master_ai_id                                                ai_id,
       upper(substr(si.subject_item_desc, 1, 50))                     name,
       upper(substr(si_l.physical_address_line_1, 1, 50))             address1,
       upper(substr(si_l.physical_address_line_2, 1, 50))             address2,
       upper(substr(si_l.physical_address_municipality, 1, 50))       city,
       si_l.physical_address_state_code                               state,
       si_l.physical_address_zip                                      zip,
       (select
          upper(substr(m_pc.parish_or_county_desc, 1, 25))
        from
          tempo.mtb_parish_county m_pc
        where
          m_pc.parish_or_county_code = si_lgw.parish_or_county_code ) county,
       ltrim(si_l.region_code, '0')                                   district,
       si_ll.latitude_degrees                                         lat_degrees,
       si_ll.latitude_minutes                                         lat_minutes,
       si_ll.latitude_seconds                                         lat_seconds,
       si_ll.longitude_degrees                                        lon_degrees,
       si_ll.longitude_minutes                                        lon_minutes,
       si_ll.longitude_seconds                                        lon_seconds,
       si_ll.latitude_dec_degrees                                     lat_degrees_decimal,
       si_ll.longitude_dec_degrees                                    lon_degrees_decimal,
       si_ll.collected_date                                           ll_date,
       (select
          m_cs.coordinate_system_desc
        from
          tempo.mtb_coord_system m_cs
        where
          m_cs.coordinate_system_code = si_ll.method_code)            ll_method,
       (select
          m_rp.ref_point_desc
        from
          tempo.mtb_reference_point m_rp
        where
          m_rp.ref_point_code = si_ll.reference_point_code)           ll_description,
       (select m_co.coordinate_org_desc
          from tempo.mtb_coord_org m_co
         where m_co.coordinate_org_code = si_ll.datum_code)           ll_datum,
       si_ll.accuracy_desc                                            ll_accuracy,
       si.user_last_updt                                              user_modified,
       si.tmsp_last_updt                                              date_modified
  from tempo.subject_item             si,
       tempo.subj_item_loc_lat_long   si_ll,
       tempo.subj_item_location       si_l,
       tempo.subj_item_loc_gov_within si_lgw
 where si.subject_item_type_code = 'GPTK'
   and si.subject_item_category_code = 'GPTF'
   and si.int_doc_id = 0
   -- Be sure the si.subject_item_designation is numeric or null
   and translate(si.subject_item_designation,chr(0)||'0123456789',chr(0))is null
   and si_l.subject_item_id(+) = si.subject_item_id
   and si_l.subject_item_category_code(+) = si.subject_item_category_code
   and si_l.int_doc_id(+) = si.int_doc_id
   and si_l.master_ai_id(+) = si.master_ai_id
   and si_ll.int_doc_id(+) = si_l.int_doc_id
   and si_ll.subject_item_id(+) = si_l.subject_item_id
   and si_ll.subject_item_category_code(+) = si_l.subject_item_category_code
   and si_ll.master_ai_id(+) = si_l.master_ai_id
   and si_lgw.subject_item_id(+) = si.subject_item_id
   and si_lgw.subject_item_category_code(+) = si.subject_item_category_code
   and si_lgw.int_doc_id(+) = si.int_doc_id
   and si_lgw.master_ai_id(+) = si.master_ai_id
;
--------------------------------------------------------
--  DDL for View UST_LOCATIONS_MVW_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "UST_LOCATIONS_MVW_VIEW" ("ID", "AI_ID", "NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "COUNTY", "DISTRICT", "LAT_DEGREES", "LAT_MINUTES", "LAT_SECONDS", "LON_DEGREES", "LON_MINUTES", "LON_SECONDS", "LAT_DEGREES_DECIMAL", "LON_DEGREES_DECIMAL", "LL_DATE", "LL_METHOD", "LL_DESCRIPTION", "LL_DATUM", "LL_ACCURACY", "USER_MODIFIED", "DATE_MODIFIED") AS 
  SELECT "ID","AI_ID","NAME","ADDRESS1","ADDRESS2","CITY","STATE","ZIP","COUNTY","DISTRICT","LAT_DEGREES","LAT_MINUTES","LAT_SECONDS","LON_DEGREES","LON_MINUTES","LON_SECONDS","LAT_DEGREES_DECIMAL","LON_DEGREES_DECIMAL","LL_DATE","LL_METHOD","LL_DESCRIPTION","LL_DATUM","LL_ACCURACY","USER_MODIFIED","DATE_MODIFIED"
      FROM UST_LOCATIONS_MVW;
--------------------------------------------------------
--  DDL for View UST_OWNERS_VIEW
--------------------------------------------------------

  CREATE OR REPLACE VIEW "UST_OWNERS_VIEW" ("ID", "OLD_ID", "OWNER_CODE", "FEDERAL_ID", "OWNER_NAME", "ADDRESS1", "ADDRESS2", "CITY", "STATE", "ZIP", "PHONE_NUMBER", "USER_CREATED", "DATE_CREATED", "USER_MODIFIED", "DATE_MODIFIED", "ORG_ID", "PER_ID") AS 
  SELECT "ID","OLD_ID","OWNER_CODE","FEDERAL_ID","OWNER_NAME","ADDRESS1","ADDRESS2","CITY","STATE","ZIP","PHONE_NUMBER","USER_CREATED","DATE_CREATED","USER_MODIFIED","DATE_MODIFIED","ORG_ID","PER_ID"
      FROM USTX.UST_OWNERS;
--------------------------------------------------------
--  DDL for Function FUNC_GET_TANK_SUBSTANCE
--------------------------------------------------------

  CREATE OR REPLACE FUNCTION "FUNC_GET_TANK_SUBSTANCE" 
(

 p_tank_id    in   number
)
 return varchar2 is
ls_return  varchar2(420);
ld_rec_count     number;
cursor tank_details (lp_tank_id number) is
  select t2.description
  from ustx.tank_details t1,
       ustx.tank_detail_codes t2
  where t1.tank_id = lp_tank_id
  and substr(t1.tank_detail_code, 1, 1) = 'B'
  and t1.tank_detail_code = t2.code
  order by t2.description;
BEGIN
  ld_rec_count := 0;
  ls_return := '';
  for c_rec in tank_details (p_tank_id)
  loop
     if ld_rec_count = 0 then
       ls_return := c_rec.description;
     else
       ls_return := ls_return || ', ' || c_rec.description;
     end if;
     ld_rec_count := ld_rec_count + 1;
  end loop;
return ls_return;
END func_get_tank_substance;

/

--------------------------------------------------------
--  DDL for Function GET_PAYMENT_INVOICE_ID
--------------------------------------------------------

  CREATE OR REPLACE FUNCTION "GET_PAYMENT_INVOICE_ID" (
	p_owner_id in number,
	p_fiscal_year in number,
	p_transaction_type in char,
	p_nov_number in number)
RETURN number
IS

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

--------------------------------------------------------
--  DDL for Function IS_NUMBER
--------------------------------------------------------

  CREATE OR REPLACE FUNCTION "IS_NUMBER" (p_string IN VARCHAR2)
   RETURN INT
IS
   v_new_num NUMBER;
BEGIN
	if (p_string is null) then
		return 0;
	else
		v_new_num := TO_NUMBER(p_string);
		return 1;
	end if;
EXCEPTION
WHEN VALUE_ERROR THEN
   return 0;
END is_number;

/

--------------------------------------------------------
--  DDL for Function ONESTOP_NEXT_DATE
--------------------------------------------------------

  CREATE OR REPLACE FUNCTION "ONESTOP_NEXT_DATE" (p_date in date)
return date
is
l_day char(1);
l_hour char(2);
l_next_date date;
begin
l_day := substr(to_char(p_date, 'DY'), 1, 1);
l_hour := to_char(p_date, 'HH24');
if l_day = 'S' then
  l_next_date := trunc(next_day(p_date, 'MON'))+10.25/24;
elsif l_hour < '10' then
  l_next_date := trunc(p_date)+10.25/24;  
elsif l_hour < '14' then
  l_next_date := trunc(p_date)+14.25/24;
elsif l_hour < '18' then
  l_next_date := trunc(p_date)+18.25/24;
elsif l_day = 'F' then
  l_next_date := trunc(next_day(p_date, 'MON'))+10.25/24;
else
  l_next_date := trunc(p_date+1)+10.25/24;
end if;  
return l_next_date;
end onestop_next_date;

/

--------------------------------------------------------
--  DDL for Package GPA_INVOICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "GPA_INVOICE" 
AS
	function insert_gpa_invoice(
		p_invoice_date in date,
		p_due_date in date,
		p_nov_gpa_fiscal_year in number,
		p_nov_gpa_amount in number,
		p_nov_gpa_facility_id in number,
		p_staff_code in varchar) return number;
END;

/

--------------------------------------------------------
--  DDL for Package INVOICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "INVOICE" 
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
end;

/

--------------------------------------------------------
--  DDL for Package MVIEW_REFRESH
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "MVIEW_REFRESH" 
AS
   PROCEDURE main; 
   PROCEDURE write_log (
      p_pkg_proc               IN       VARCHAR2,
      p_description            IN       VARCHAR2);
END;

/

--------------------------------------------------------
--  DDL for Package NOTICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "NOTICE" 
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

--------------------------------------------------------
--  DDL for Package PERMIT
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "PERMIT" 
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

--------------------------------------------------------
--  DDL for Package REPORTR
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "REPORTR" AS
	/***************************************************************************
	Author  : Min Lee
	Created : November 29, 2010
	Purpose : Functions in this package are used by Onestop reports running inside CAF app
	*****************************************************************************/

	-- define the output cursor
	TYPE cursor_type IS REF CURSOR;

	FUNCTION get_active_lust_inspection(last_inspection_date IN DATE) RETURN cursor_type;
END REPORTR;

/

--------------------------------------------------------
--  DDL for Package UST_INVOICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE "UST_INVOICE" 
as
  function adjust_address_lines (
      line_1                  in varchar2 default null,
      line_2                  in varchar2 default null,
      out_line                in number)
      return varchar2;
  function adjusted_invoice_code (
      owner_id                in number)
      return varchar2;
end ust_invoice;

/

--------------------------------------------------------
--  DDL for Package Body GPA_INVOICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "GPA_INVOICE" 
AS
/****************************************************************************
  Creates GPA invoice for a specified facility. (Ground Water Protection Act)
  This procedure was put together by SQL gathered from ust_gpa_invoice.rdf
  For some reason, transaction_code gets set to 'OPEN' instead of 'O' as is in
  regular invoice.  This discrepancy is kept in the migration to this function.
  
  Jun 6 2013	Min Lee		Imported into DB from Oracle Form function.
*****************************************************************************/
function insert_gpa_invoice(
	p_invoice_date in date,
	p_due_date in date,
	p_nov_gpa_fiscal_year in number,
	p_nov_gpa_amount in number,
	p_nov_gpa_facility_id in number,
	p_staff_code in varchar) return number
is
	t_invoice_id number := 0;
	t_owner_id	number := 0;

pragma autonomous_transaction;
begin

	select ustx.invoice_seq.nextval into t_invoice_id from dual;
	
	select owner_id into t_owner_id from ustx.facilities_mvw where id = p_nov_gpa_facility_id;

	insert into ustx.invoices (id, owner_id, invoice_code, invoice_date,
		due_date, nov_gpa_facility_id, nov_gpa_amount,
		nov_gpa_fiscal_year, user_created, date_created)
	values (t_invoice_id, t_owner_id, 'GPA', p_invoice_date, p_due_date,
		p_nov_gpa_facility_id, p_nov_gpa_amount, p_nov_gpa_fiscal_year,
		p_staff_code, sysdate);

	insert into ustx.transactions
		( id, owner_id, invoice_id, transaction_code, transaction_date,
		fiscal_year, amount, transaction_status, check_number,
		user_created, date_created )
	select ustx.transaction_seq.nextval, t_owner_id, t_invoice_id,
		'GWAA', p_invoice_date, p_nov_gpa_fiscal_year,
		p_nov_gpa_amount, 'OPEN', to_char(p_nov_gpa_facility_id),
		p_staff_code, sysdate
		from dual;

	commit;

	return t_invoice_id;

exception
	when others then
		rollback;
		return 0;
end insert_gpa_invoice;

END;

/

--------------------------------------------------------
--  DDL for Package Body INVOICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "INVOICE" 
as
-- ML: Apr 2, 2012 - fixed interest and late fee overcharge when invoicing for past FY
-- ML: Apr 2, 2012 - added emergency generator tank exclusion for FY 2002-2007 logic
-- ML: May 18 2012 - imported delete_invoice procedure from ust_gen.fmb into this package.
-- ML: July 25 2012 - reverted to payments_prior_due_date in interest / late fee calculation.
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

end;

/

--------------------------------------------------------
--  DDL for Package Body MVIEW_REFRESH
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "MVIEW_REFRESH" 
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

/

--------------------------------------------------------
--  DDL for Package Body NOTICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "NOTICE" 
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

--------------------------------------------------------
--  DDL for Package Body PERMIT
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "PERMIT" 
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

--------------------------------------------------------
--  DDL for Package Body REPORTR
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "REPORTR" AS

	/***************************************************************************
	FUNCTION GET_ACTIVE_LUST_INSPECTION
	Description: Returns all info related to facilities that are active LUST sites
					that have not changed ownership
	Parameters:  last_inspection_date:  Last compliance inspection performed on the facility
	Modification History:
	Developer           Date          Description
	----------------    ----------    -----------------------------------------
	Min Lee        12/1/2010    Original script
	*****************************************************************************/
	FUNCTION get_active_lust_inspection(last_inspection_date IN DATE) RETURN cursor_type
	IS
		l_cursor cursor_type; 
	BEGIN
		OPEN l_cursor FOR
			SELECT O.owner_name, R.responsible_party, R.id RID, F.id FID, F.facility_name, F.address1, F.address2, F.city, CT.county, F.state, F.zip,
				I.last_inspected_date,
				( select max(I2.staff_code) from ustx.inspections I2 where (I2.facility_id=I.facility_id) and (I2.inspection_code=1) and (I2.date_inspected=I.last_inspected_date) group by I2.staff_code ) STAFF_CODE
			FROM ustx.facilities_mvw F,
				ustx.owners_mvw O, LUST.lust_releases_mvw R,
				USTX.cities CT,
				(
					select facility_id, max(date_inspected) last_inspected_date
					from ustx.inspections
					where (inspection_code = 1) -- 1 = COMPLIANCE
					group by facility_id
					having max(date_inspected) < last_inspection_date
				) I
			WHERE
				F.owner_id = O.id
				AND R.facility_id = F.id
				AND F.city = CT.city
				AND (select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE in ('A', 'U')) and (t.tank_status_code in (1, 2))) > 0
				AND (F.id = I.facility_id)
			ORDER BY F.ID;
		
		return l_cursor;
	END get_active_lust_inspection;

END REPORTR;

/

--------------------------------------------------------
--  DDL for Package Body UST_INVOICE
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE BODY "UST_INVOICE" 
AS

function adjust_address_lines( line_1 in varchar2 default null,
                               line_2 in varchar2 default null,
                               out_line in number )
return varchar2
/*
 * Figures out which lines to print/select to eliminate blanks in the address.
 *
 * Output: an address line
 */
is
begin
	if out_line = 1 then
		if line_1 is not null then
			return line_1;
		else
			return line_2;
		end if;
	else
		if line_1 is not null then
			return line_2;
		else
			return null;
		end if;
	end if;

end adjust_address_lines;

/****************************************************************************
 2013-10-25: ML modified adjusted_invoice_code function to make use of end_date.
*****************************************************************************/
function adjusted_invoice_code( owner_id in number )
return varchar2
/*
 * Figure out what invoice code should be used for this owner.
 *
 * Output: UST if regular invoice.
 *         USTBR if FIN_METH_CODE = 0 in FINANCIAL_RESPONSIBILITIES table.
 */
is
	this_invoice_code ustx.invoice_codes.code%type := 'UST';
begin
	for financial_record in ( select 'USTBR' invoice_code
		from ustx.financial_responsibilities
			where owner_id = adjusted_invoice_code.owner_id
				and fin_meth_code = '0'
				and nvl(end_date, sysdate) >= sysdate )
	loop
		this_invoice_code := financial_record.invoice_code;
	end loop;

	return this_invoice_code;
end adjusted_invoice_code;

END;

/

--------------------------------------------------------
--  DDL for Procedure D_INSPECTION
--------------------------------------------------------
set define off;

  CREATE OR REPLACE PROCEDURE "D_INSPECTION" ( p_inspection_id in number ) as
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

