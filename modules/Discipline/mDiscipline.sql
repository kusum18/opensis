--
-- Structure for table: 'DISCIPLINE_CATAGORIES'
--

DROP TABLE IF EXISTS DISCIPLINE_CATAGORIES;
CREATE TABLE DISCIPLINE_CATAGORIES
(
    ID INT NOT NULL KEY,
    SYEAR NUMERIC(4),
    SCHOOL_ID NUMERIC,
    TITLE VARCHAR(255),
    SORT_ORDER NUMERIC,
    TYPE VARCHAR(30),
    OPTIONS VARCHAR(10000)
);
--
-- Structure for table: 'discipline_categories_seq' 
-- used for autoincrementing after porting from PostGres Do not alter unless you 
-- know what you are doing
--
DROP TABLE IF EXISTS discipline_categories_seq;
CREATE TABLE discipline_categories_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);

DROP FUNCTION IF EXISTS fn_discipline_categories_seq;
DELIMITER $$
CREATE FUNCTION fn_discipline_categories_seq () RETURNS INT
BEGIN
    INSERT INTO discipline_categories_seq VALUES(NULL);
    DELETE FROM discipline_categories_seq;
    RETURN LAST_INSERT_ID();
END $$

DELIMITER ;

--
-- Structure for table: 'DISCIPLINE_CATAGORIES'
--

DROP TABLE IF EXISTS DISCIPLINE_REFERRALS;
CREATE TABLE DISCIPLINE_REFERRALS
(
    ID INT NOT NULL KEY,
    SYEAR NUMERIC(4),
    SCHOOL_ID NUMERIC,
    STUDENT_ID NUMERIC,
    ENTRY_DATE DATE,
    STAFF_ID NUMERIC,
    CATEGORY_1 varchar(1000),
	CATEGORY_2 varchar(1000),
	CATEGORY_3 varchar(1),
	CATEGORY_4 varchar(1000),
	CATEGORY_5 varchar(1000),
	CATEGORY_6 varchar(5000)
    
);

--
-- Structure for table: 'discipline_referrals_seq' 
--

DROP TABLE IF EXISTS discipline_referrals_seq;
CREATE TABLE discipline_referrals_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);

DROP FUNCTION IF EXISTS fn_discipline_referrals_seq;
DELIMITER $$
CREATE FUNCTION fn_discipline_referrals_seq () RETURNS INT
BEGIN
    INSERT INTO discipline_referrals_seq VALUES(NULL);
    DELETE FROM discipline_referrals_seq;
    RETURN LAST_INSERT_ID();
END $$
DELIMITER ;

--
-- Structure for table: 'discipline_fields'
--
DROP TABLE IF EXISTS DISCIPLINE_FIELDS;
CREATE TABLE DISCIPLINE_FIELDS (
ID INTEGER NOT NULL KEY,
TITLE varchar(255) NOT NULL,
SHORT_NAME varchar(20),
DATA_TYPE varchar(30) NOT NULL,
COLUMN_NAME varchar(255) NOT NULL

);

--
-- Structure for sequence: 'discipline_fields_seq'
--
DROP TABLE IF EXISTS discipline_fields_seq;
CREATE TABLE discipline_fields_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);

DROP FUNCTION IF EXISTS fn_discipline_fields_seq;
DELIMITER $$
CREATE FUNCTION fn_discipline_fields_seq() RETURNS INT
BEGIN
    INSERT INTO discipline_fields_seq VALUES(NULL);
    DELETE FROM discipline_referrals_seq;
    RETURN LAST_INSERT_ID();
END $$
DELIMITER ;

--
-- Creating data for 'discipline_fields' uncomment data for testing
--
-- INSERT INTO DISCIPLINE_FIELDS VALUES ('1','Violation',NULL,'multiple_checkbox','CATEGORY_1');
-- INSERT INTO DISCIPLINE_FIELDS VALUES ('2','Detention Assigned',NULL,'multiple_radio','CATEGORY_2');
-- INSERT INTO DISCIPLINE_FIELDS VALUES ('3','Parents Contacted By Teacher',NULL,'checkbox','CATEGORY_3');
-- INSERT INTO DISCIPLINE_FIELDS VALUES ('4','Parent Contacted by Administrator',NULL,'text','CATEGORY_4');
-- INSERT INTO DISCIPLINE_FIELDS VALUES ('5','Suspensions (Office Only)',NULL,'multiple_checkbox','CATEGORY_5');
-- INSERT INTO DISCIPLINE_FIELDS VALUES ('6','Comments',NULL,'textarea','CATEGORY_6');

--
-- Creating indices for 'discipline_fields'
--

CREATE UNIQUE INDEX discipline_fields_pkey USING btree  ON discipline_fields(ID) ;
--
-- Structure for table: 'discipline_field_usage'
--
DROP TABLE IF EXISTS DISCIPLINE_FIELD_USAGE;
CREATE TABLE DISCIPLINE_FIELD_USAGE (
ID INTEGER NOT NULL,
DISCIPLINE_FIELD_ID numeric NOT NULL,
SYEAR numeric NOT NULL,
SCHOOL_ID numeric NOT NULL,
TITLE varchar(255),
SELECT_OPTIONS varchar(10000),
SORT_ORDER numeric
);

--
-- Structure for sequence: 'discipline_fields_usage_seq'
--


DROP TABLE IF EXISTS discipline_field_usage_seq;
CREATE TABLE discipline_field_usage_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);

DROP FUNCTION IF EXISTS fn_discipline_field_usage_seq;
DELIMITER $$
CREATE FUNCTION fn_discipline_field_usage_seq () RETURNS INT
BEGIN
    INSERT INTO discipline_field_usage_seq VALUES(NULL);
    DELETE FROM discipline_field_usage_seq;
    RETURN LAST_INSERT_ID();
END $$
DELIMITER ;

-- INSERT INTO DISCIPLINE_FIELD_USAGE VALUES ('3','3','2008','1','Parents Contacted By Teacher',NULL,'4');
-- INSERT INTO DISCIPLINE_FIELD_USAGE VALUES ('4','4','2008','1','Parent Contacted by Administrator',NULL,'5');
-- INSERT INTO DISCIPLINE_FIELD_USAGE VALUES ('6','6','2008','1','Comments',NULL,'6');
-- INSERT INTO DISCIPLINE_FIELD_USAGE VALUES ('1','1','2008','1','Violation','Skipping Class 

--
-- Creating indices for Modify profile exceptions***if reenstalling Module comment out following steps
--

INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/MakeReferral.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/Referrals.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/CategoryBreakdown.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/CategoryBreakdownTime.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/StudentFieldBreakdown.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/ReferralLog.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/DisciplineForm.php', 'Y', 'Y');
INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES (1, 'Discipline/ReferalForm.php', 'Y', 'Y');
