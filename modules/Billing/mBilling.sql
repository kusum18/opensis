
--
-- Name: billing_fees; Type: TABLE; ; Tablespace: 
--

CREATE TABLE billing_fees (
    student_id numeric NOT NULL,
    assigned_date date,
    due_date date,
    comments character varying(255),
    id numeric,
    title character varying(255),
    amount numeric,
    school_id numeric,
    syear numeric,
    waived_fee_id numeric,
    old_id numeric
);




--
-- Name: billing_fees_categories; Type: TABLE; ; Tablespace: 
--

CREATE TABLE billing_fees_categories (
    id numeric NOT NULL,
    syear numeric NOT NULL,
    school_id numeric NOT NULL,
    title character varying(255)
)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;



--
-- Name: billing_fees_old; Type: TABLE; ; Tablespace: 
--

CREATE TABLE billing_fees_old (
    id numeric NOT NULL,
    category_id numeric NOT NULL,
    syear numeric NOT NULL,
    school_id numeric NOT NULL,
    title character varying(255),
    amount numeric,
    apply_all char,
    grade_id numeric,
    course_id numeric,
    course_weight character varying(10),
    activity_id numeric,
    student_field_id numeric,
    student_field_value character varying(255),
    student_id numeric,
    waived_fee_id numeric
)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;






--
-- Name: billing_payments; Type: TABLE; ; Tablespace: 
--

CREATE TABLE billing_payments (
    id numeric NOT NULL,
    syear numeric NOT NULL,
    school_id numeric NOT NULL,
    student_id numeric NOT NULL,
    amount numeric NOT NULL,
    payment_date date,
    comments character varying(255),
    refunded_payment_id numeric,
    lunch_payment character varying(1)
)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;






insert into  profile_exceptions (profile_id, modname, can_use, can_edit) values
("1",	"Billing/StudentFees.php",	"N",	"N"),
("1",	"Billing/StudentPayments.php",	"N",	"N"),
("1",	"Billing/MassAssignFees.php",	"N",	"N"),
("1",	"Billing/MassAssignPayments.php",	"N",	"N"),
("1",	"Billing/StudentBalances.php"	,"N",	"N"),
("1",	"Billing/DailyTransactions.php",	"N",	"N"),
("1",	"Billing/Statements.php",	"N",	"N"),
("1",	"Billing/Fees.php",	"N"	,"N");




--
-- Name: billing_fees_categories_pkey; Type: INDEX; ; Tablespace: 
--

CREATE UNIQUE INDEX billing_fees_categories_pkey USING btree ON billing_fees_categories  (id);


--
-- Name: billing_fees_ind1; Type: INDEX; ; Tablespace: 
--

CREATE INDEX billing_fees_ind1 USING btree ON billing_fees_old  (category_id);


--
-- Name: billing_fees_ind2; Type: INDEX; ; Tablespace: 
--

CREATE INDEX billing_fees_ind2 USING btree ON billing_fees_old  (amount);


--
-- Name: billing_fees_ind3; Type: INDEX; ; Tablespace: 
--

CREATE INDEX billing_fees_ind3 USING btree ON billing_fees_old (waived_fee_id);


--
-- Name: billing_fees_pkey; Type: INDEX; ; Tablespace: 
--

CREATE UNIQUE INDEX billing_fees_pkey USING btree ON billing_fees_old (id);


--
-- Name: billing_payments_ind1; Type: INDEX; ; Tablespace: 
--

CREATE INDEX billing_payments_ind1 USING btree ON billing_payments (student_id);


--
-- Name: billing_payments_ind2; Type: INDEX; ; Tablespace: 
--

CREATE INDEX billing_payments_ind2 USING btree ON billing_payments (amount);


--
-- Name: billing_payments_ind3; Type: INDEX; ; Tablespace: 
--

CREATE INDEX billing_payments_ind3 USING btree ON billing_payments (refunded_payment_id);


--
-- Name: billing_payments_pkey; Type: INDEX; ; Tablespace: 
--

CREATE UNIQUE INDEX billing_payments_pkey USING btree ON billing_payments (id);

