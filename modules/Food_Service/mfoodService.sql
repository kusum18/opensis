--
CREATE TABLE FOOD_SERVICE_ACCOUNTS (
    account_id numeric NOT NULL,
    balance numeric(9,2) NOT NULL,
    transaction_id numeric
);

--

CREATE TABLE FOOD_SERVICE_CATEGORIES (
    category_id numeric NOT NULL,
    school_id numeric NOT NULL,
    menu_id numeric NOT NULL,
    title character varying(25),
    sort_order numeric
);

CREATE TABLE food_service_categories_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_food_service_categories_seq`;
DELIMITER $$
CREATE FUNCTION fn_food_service_categories_seq () RETURNS INT
BEGIN
  INSERT INTO food_service_categories_seq VALUES(NULL);
  DELETE FROM food_service_categories_seq;
  RETURN LAST_INSERT_ID();
END$$
DELIMITER ;
ALTER TABLE food_service_categories_seq AUTO_INCREMENT=1;

--

CREATE TABLE FOOD_SERVICE_ITEMS (
    item_id numeric NOT NULL,
    school_id numeric NOT NULL,
    short_name character varying(25),
    sort_order numeric,
    description character varying(25),
    icon character varying(50),
    price numeric(9,2) NOT NULL,
    price_reduced numeric(9,2),
    price_free numeric(9,2),
    price_staff numeric(9,2) NOT NULL
);

CREATE TABLE food_service_items_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_food_service_items_seq`;
DELIMITER $$
CREATE FUNCTION fn_food_service_items_seq () RETURNS INT
BEGIN
  INSERT INTO food_service_items_seq VALUES(NULL);
  DELETE FROM food_service_items_seq;
  RETURN LAST_INSERT_ID();
END$$
DELIMITER ;
ALTER TABLE food_service_items_seq AUTO_INCREMENT=1;

--

CREATE TABLE FOOD_SERVICE_MENU_ITEMS (
    menu_item_id numeric NOT NULL,
    school_id numeric NOT NULL,
    menu_id numeric NOT NULL,
    item_id numeric NOT NULL,
    category_id numeric,
    sort_order numeric,
    does_count character varying(1)
);

CREATE TABLE food_service_menu_items_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_food_service_menu_items_seq`;
DELIMITER $$
CREATE FUNCTION fn_food_service_menu_items_seq () RETURNS INT
BEGIN
  INSERT INTO food_service_menu_items_seq VALUES(NULL);
  DELETE FROM food_service_menu_items_seq;
  RETURN LAST_INSERT_ID();
END$$
DELIMITER ;
ALTER TABLE food_service_menu_items_seq AUTO_INCREMENT=1;

--

CREATE TABLE FOOD_SERVICE_MENUS (
    menu_id numeric NOT NULL,
    school_id numeric NOT NULL,
    title character varying(25) NOT NULL,
    sort_order numeric
);

CREATE TABLE food_service_menus_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_food_service_menus_seq`;
DELIMITER $$
CREATE FUNCTION fn_food_service_menus_seq () RETURNS INT
BEGIN
  INSERT INTO food_service_menus_seq VALUES(NULL);
  DELETE FROM food_service_menus_seq;
  RETURN LAST_INSERT_ID();
END$$
DELIMITER ;
ALTER TABLE food_service_menus_seq AUTO_INCREMENT=1;

--

CREATE TABLE FOOD_SERVICE_STAFF_ACCOUNTS (
    staff_id numeric NOT NULL,
    status character varying(25),
    barcode character varying(50),
    balance numeric(9,2) NOT NULL,
    transaction_id numeric
);

--

CREATE TABLE FOOD_SERVICE_STAFF_TRANSACTION_ITEMS (
    item_id numeric NOT NULL,
    transaction_id numeric NOT NULL,
    amount numeric(9,2),
    short_name character varying(25),
    description character varying(50)
);

--

CREATE TABLE FOOD_SERVICE_STAFF_TRANSACTIONS (
    transaction_id numeric NOT NULL,
    staff_id numeric NOT NULL,
    school_id numeric,
    syear numeric(4,0),
    balance numeric(9,2),
    timestamp timestamp(0) ,
    short_name character varying(25),
    description character varying(50),
    seller_id numeric
);

CREATE TABLE food_service_staff_transactions_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_food_service_staff_transactions_seq`;
DELIMITER $$
CREATE FUNCTION fn_food_service_staff_transactions_seq () RETURNS INT
BEGIN
  INSERT INTO food_service_staff_transactions_seq VALUES(NULL);
  DELETE FROM food_service_staff_transactions_seq;
  RETURN LAST_INSERT_ID();
END$$
DELIMITER ;

ALTER TABLE food_service_staff_transactions_seq AUTO_INCREMENT=1;

--

CREATE TABLE FOOD_SERVICE_STUDENT_ACCOUNTS (
    student_id numeric NOT NULL,
    account_id numeric NOT NULL,
    discount character varying(25),
    status character varying(25),
    barcode character varying(50)
);

--

CREATE TABLE FOOD_SERVICE_TRANSACTION_ITEMS (
    item_id numeric NOT NULL,
    transaction_id numeric NOT NULL,
    amount numeric(9,2),
    discount character varying(25),
    short_name character varying(25),
    description character varying(50)
);

--

CREATE TABLE FOOD_SERVICE_TRANSACTIONS (
    transaction_id numeric NOT NULL,
    account_id numeric NOT NULL,
    student_id numeric,
    school_id numeric,
    syear numeric(4,0),
    discount character varying(25),
    balance numeric(9,2),
    timestamp timestamp(0) ,
    short_name character varying(25),
    description character varying(50),
    seller_id numeric
);

CREATE TABLE food_service_transactions_seq (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_food_service_transactions_seq`;
DELIMITER $$
CREATE FUNCTION fn_food_service_transactions_seq () RETURNS INT
BEGIN
  INSERT INTO food_service_transactions_seq VALUES(NULL);
  DELETE FROM food_service_transactions_seq;
  RETURN LAST_INSERT_ID();
END$$
DELIMITER ;
ALTER TABLE food_service_transactions_seq AUTO_INCREMENT=1;

--

ALTER TABLE FOOD_SERVICE_ACCOUNTS
    ADD CONSTRAINT food_service_accounts_pkey PRIMARY KEY (account_id);


ALTER TABLE FOOD_SERVICE_CATEGORIES
    ADD CONSTRAINT food_service_categories_pkey PRIMARY KEY (category_id);


ALTER TABLE FOOD_SERVICE_ITEMS
    ADD CONSTRAINT food_service_items_pkey PRIMARY KEY (item_id);


ALTER TABLE FOOD_SERVICE_MENU_ITEMS
    ADD CONSTRAINT food_service_menu_items_pkey PRIMARY KEY (menu_item_id);


ALTER TABLE FOOD_SERVICE_MENUS
    ADD CONSTRAINT food_service_menus_pkey PRIMARY KEY (menu_id);


ALTER TABLE FOOD_SERVICE_STAFF_ACCOUNTS
    ADD CONSTRAINT food_service_staff_accounts_pkey PRIMARY KEY (staff_id);


ALTER TABLE FOOD_SERVICE_STAFF_TRANSACTION_ITEMS
    ADD CONSTRAINT food_service_staff_transaction_items_pkey PRIMARY KEY (item_id, transaction_id);


ALTER TABLE FOOD_SERVICE_STAFF_TRANSACTIONS
    ADD CONSTRAINT food_service_staff_transactions_pkey PRIMARY KEY (transaction_id);


ALTER TABLE FOOD_SERVICE_STUDENT_ACCOUNTS
    ADD CONSTRAINT food_service_student_accounts_pkey PRIMARY KEY (student_id);


ALTER TABLE FOOD_SERVICE_TRANSACTION_ITEMS
    ADD CONSTRAINT food_service_transaction_items_pkey PRIMARY KEY (item_id, transaction_id);


ALTER TABLE FOOD_SERVICE_TRANSACTIONS
    ADD CONSTRAINT food_service_transactions_pkey PRIMARY KEY (transaction_id);

--

CREATE UNIQUE INDEX food_service_categories_title  USING btree ON FOOD_SERVICE_CATEGORIES (school_id, menu_id, title);


CREATE UNIQUE INDEX food_service_items_short_name  USING btree ON FOOD_SERVICE_ITEMS (school_id, short_name);


CREATE UNIQUE INDEX food_service_menus_title  USING btree ON FOOD_SERVICE_MENUS (school_id, title);


CREATE INDEX food_service_staff_transaction_items_ind1  USING btree ON FOOD_SERVICE_STAFF_TRANSACTION_ITEMS(transaction_id);


CREATE INDEX food_service_transaction_items_ind1  USING btree ON FOOD_SERVICE_TRANSACTION_ITEMS(transaction_id);


CREATE UNIQUE INDEX staff_barcode  USING btree ON FOOD_SERVICE_STAFF_ACCOUNTS (barcode);


CREATE UNIQUE INDEX students_barcode  USING btree ON FOOD_SERVICE_STUDENT_ACCOUNTS (barcode);
