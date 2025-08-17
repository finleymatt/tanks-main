-- 2016-02-25 create phone field and make all optional
-- ran on eidd, eidt

ALTER TABLE USTX.EMAILS
MODIFY EMAIL NULL;

ALTER TABLE USTX.EMAILS
ADD PHONE Varchar2(25) NULL;
