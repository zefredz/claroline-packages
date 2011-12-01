
CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_survey` (
  `id`                  INTEGER                                         NOT NULL    auto_increment,
  `courseId`            VARCHAR(40)                                     NOT NULL,
  `title`               VARCHAR(255)                                    NOT NULL,
  `description`         TEXT                                            NOT NULL,
  `is_anonymous`        TINYINT(1)                                      NOT NULL    DEFAULT 0,
  `is_visible`          TINYINT(1)                                      NOT NULL    DEFAULT 0,
  `resultsVisibility`   ENUM('VISIBLE','INVISIBLE','VISIBLE_AT_END')    NOT NULL    DEFAULT 'INVISIBLE',
  `startDate`           DATETIME                                        NULL,
  `endDate`             DATETIME                                        NULL,
  `rank`                INTEGER                                         NULL,
  `maxCommentSize`      INTEGER                                         NOT NULL    DEFAULT 200,
  `allow_change_answers` TINYINT(1)                                     NULL DEFAULT 1,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_question` (
  `id`                  INTEGER                                         NOT NULL    auto_increment,
  `text`                VARCHAR(255)                                    NOT NULL,
  `type`                ENUM('OPEN','MCSA','MCMA', 'ARRAY')             NOT NULL    DEFAULT 'MCSA',
  `author_id`           INTEGER                                         NULL,
  `shared` TINYINT(1)   NULL                                            DEFAULT 1,
  INDEX author_idx (`author_id`),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_survey_line` (
  `id`                  INTEGER                                         NOT NULL auto_increment,
  `surveyId`            INTEGER                                         NOT NULL,
  `rank`                INTEGER                                         NULL,
  PRIMARY KEY  (`id`)
  -- , UNIQUE(`surveyId`, `rank`) -- removed for allowing swapping lines
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_survey_line_separator` (
  `id`                  INTEGER                                         NOT NULL,
  `title`               VARCHAR(255)                                    NOT NULL,
  `description`             TEXT                                        NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_survey_line_question` (
  `id`                  INTEGER                                         NOT NULL,
  `questionId`          INTEGER                                         NOT NULL,
  `maxCommentSize`      INTEGER                                         NOT NULL    DEFAULT 200,
  `required`            TINYINT(1)                                      NULL DEFAULT  1,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_choice` (
  `id`                  INTEGER                                         NOT NULL auto_increment,
  `questionId`          INTEGER                                         NOT NULL,
  `text`                TEXT                                            NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_option` (
  `id`                  INTEGER                                         NOT NULL auto_increment,
  `choiceId`            INTEGER                                         NOT NULL,
  `text`                TEXT                                            NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_answer` (
  `id`                  INTEGER                                         NOT NULL auto_increment,
  `surveyLineId`        INTEGER                                         NOT NULL,
  `participationId`     INTEGER                                         NOT NULL,
  `comment`             VARCHAR(200)                                    NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`surveyLineId`, `participationId`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_answer_item` (
  `id`                  INTEGER                                         NOT NULL auto_increment,
  `answerId`            INTEGER                                         NOT NULL,
  `choiceId`            INTEGER                                         NOT NULL,
  `optionId`            INTEGER                                         NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_participation` (
    `id`                INTEGER                                         NOT NULL auto_increment,
    `surveyId`          INTEGER                                         NOT NULL,
    `userId`            INTEGER                                         NOT NULL,
    `updated_at`        TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE  (`surveyId`,`userId`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_version` (
  `version`         CHAR(14)        NOT NULL,
  `created_at`          TIMESTAMP,
  PRIMARY KEY  (`version`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

INSERT INTO `__CL_MAIN__survey2_version` 
        (version) 
VALUES 
        ("00000000000002");
