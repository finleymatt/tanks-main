-- ran on eidd, xeidd, xeidt, xeidq
/**************************************************
 2017-03-02 ML: Create DP tracking.
 Modify penalties to be tracked at tank level
***************************************************/
ALTER TABLE USTX.PENALTIES
ADD (
	TANK_ID NUMBER(8,0),
	LCC_DATE DATE,
	NOV_DATE DATE,
	NOD_DATE DATE,
	NOIRT_DATE DATE,
	REDTAG_PLACED_DATE DATE
);
/
