CREATE TABLE IF NOT EXISTS `__CL_MAIN__ICSURVEW_survey`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title TEXT NOT NULL,
    is_active TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__ICSURVEW_question`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    survey_id INT(11) NOT NULL,
    question TEXT NOT NULL,
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__ICSURVEW_choice`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    question_id INT(11) NOT NULL,
    choice TEXT NOT NULL,
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__ICSURVEW_answer`(
    id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    course_id VARCHAR(16) NOT NULL,
    question_id INT(11) NOT NULL,
    choice_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY course_question (course_id,question_id)
) ENGINE=MyISAM;