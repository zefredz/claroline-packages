CREATE TABLE IF NOT EXISTS `__CL_MAIN__ICSURVEW_log`(
    user_id INT(11) NOT NULL,
    course_id VARCHAR(16) NOT NULL,
    question_id INT(11) NOT NULL,
    choice_id INT(11) NOT NULL,
    PRIMARY KEY(user_id,course_id,question_id)
) ENGINE=MyISAM;