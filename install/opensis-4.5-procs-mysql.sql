DROP FUNCTION IF EXISTS `SET_CLASS_RANK_MP`;
DELIMITER $$
CREATE FUNCTION `SET_CLASS_RANK_MP`(
	mp_id int
) RETURNS int(11)
BEGIN

DECLARE done INT DEFAULT 0;
DECLARE marking_period_id INT;
DECLARE student_id INT;
DECLARE rank NUMERIC;

declare cur1 cursor for
select
  mp.marking_period_id,
  sgm.student_id,
 (select count(*)+1 
   from STUDENT_MP_STATS sgm3
   where sgm3.cum_weighted_factor > sgm.cum_weighted_factor
     and sgm3.marking_period_id = mp.marking_period_id 
     and sgm3.student_id in (select distinct sgm2.student_id 
                            from STUDENT_MP_STATS sgm2, STUDENT_ENROLLMENT se2
                            where sgm2.student_id = se2.student_id 
                              and sgm2.marking_period_id = mp.marking_period_id 
               		      and se2.grade_id = se.grade_id
                              and se2.syear = se.syear)
  ) as rank
  from STUDENT_ENROLLMENT se, STUDENT_MP_STATS sgm, MARKING_PERIODS mp
  where se.student_id = sgm.student_id
    and sgm.marking_period_id = mp.marking_period_id
    and mp.marking_period_id = mp_id
    and se.syear = mp.syear
    and not sgm.cum_weighted_factor is null
  order by grade_id, rank;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

open cur1;
fetch cur1 into marking_period_id,student_id,rank;

while not done DO
	update STUDENT_MP_STATS
	  set
	    cum_rank = rank
	where STUDENT_MP_STATS.marking_period_id = marking_period_id
	  and STUDENT_MP_STATS.student_id = student_id;
	fetch cur1 into marking_period_id,student_id,rank;
END WHILE;
CLOSE cur1;

RETURN 1;
END$$
DELIMITER ;
DROP FUNCTION IF EXISTS `CALC_CUM_GPA_MP`;
DELIMITER $$
CREATE FUNCTION `CALC_CUM_GPA_MP`(
	mp_id int
) RETURNS int
BEGIN
 DECLARE done INT DEFAULT 0;
  DECLARE student_id INT;
  DECLARE weighted_factor NUMERIC;
  

  DECLARE cur1 CURSOR FOR
    SELECT sms.student_id, 
    (sum(sms.sum_weighted_factors)/sum(sms.count_weighted_factors)) as weighted_factor
    FROM MARKING_PERIODS mp
    INNER JOIN STUDENT_MP_STATS sms ON mp.marking_period_id=sms.marking_period_id
    AND mp.end_date <= mp.end_date
    AND mp.mp_type = mp.mp_type
    AND mp.school_id = mp.school_id
    WHERE mp.marking_period_id = mp_id
    GROUP BY sms.student_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur1;
  FETCH cur1 INTO student_id,weighted_factor;

  WHILE NOT done DO
    UPDATE STUDENT_MP_STATS
      SET cum_weighted_factor = weighted_factor
              WHERE student_id = student_id and marking_period_id = mp_id;
    FETCH cur1 INTO student_id,weighted_factor;
  END WHILE;
  CLOSE cur1;
  RETURN 1;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `CALC_GPA_MP`;
DELIMITER $$
CREATE FUNCTION `CALC_GPA_MP`(
	s_id int,
	mp_id int
) RETURNS int(11)
BEGIN
  

  SELECT COUNT(*) INTO @val FROM STUDENT_MP_STATS WHERE student_id = s_id and marking_period_id = mp_id;
  IF FOUND THEN
	select 
        sum(weighted_gp/gp_scale),
        count(*)
	INTO @sum_weighted_factors, @count_weighted_factors
        from STUDENT_REPORT_CARD_GRADES where student_id = s_id and marking_period_id = mp_id
	    and not gp_scale = 0 and not marking_period_id LIKE 'E%' 
		group by student_id, marking_period_id;

    UPDATE STUDENT_MP_STATS
      SET 
        sum_weighted_factors = @sum_weighted_factors, 
        count_weighted_factors = @count_weighted_factors
        WHERE student_id = s_id and marking_period_id = mp_id;
    RETURN 1;
  ELSE
    INSERT INTO STUDENT_MP_STATS (student_id, marking_period_id, sum_weighted_factors, count_weighted_factors, grade_level_short)
        select 
            srcg.student_id, srcg.marking_period_id,
            sum(weighted_gp/gp_scale) as sum_weighted_factors, 
            count(*) as count_weighted_factors,                        
            eg.short_name
        from STUDENT_REPORT_CARD_GRADES srcg 
	join MARKING_PERIODS mp on (mp.marking_period_id = srcg.marking_period_id) 
	join ENROLL_GRADE eg on (eg.student_id = srcg.student_id and eg.syear = mp.syear and eg.school_id = mp.school_id)
        where srcg.student_id = s_id and srcg.marking_period_id = mp_id and not srcg.gp_scale = 0 and not 
        srcg.marking_period_id LIKE 'E%' group by srcg.student_id, srcg.marking_period_id, eg.short_name;
    RETURN 0;
	End IF;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `CREDIT`;
DELIMITER $$


CREATE FUNCTION `CREDIT`(
	cp_id int,
	mp_id int
) RETURNS decimal(10,0)
BEGIN
  

select credits,marking_period_id into @credits,@marking_period_id from COURSE_PERIODS where course_period_id = cp_id;
select mp,mp_type into @mp,@mp_type from MARKING_PERIODS where marking_period_id = mp_id;

IF @marking_period_id = mp_id THEN
    return @credits;
ELSEIF @mp = 'FY' AND @mp_type = 'semester' THEN
    select count(*) into @val from MARKING_PERIODS where parent_id = @marking_period_id group by parent_id;
ELSEIF @mp = 'FY' and @mp_type = 'quarter' THEN
    select count(*) into @val from MARKING_PERIODS where grandparent_id = @marking_period_id group by grandparent_id;
ELSEIF @mp = 'SEM' and @mp_type = 'quarter' THEN
    select count(*) into @val from MARKING_PERIODS where parent_id = @marking_period_id group by parent_id;
ELSE
    RETURN 0;
END IF;
IF @val > 0 THEN
    return @credits/@val;
END IF;
return 0;

END$$
DELIMITER ;
