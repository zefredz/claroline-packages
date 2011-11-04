
CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_version` (
  `version` 		CHAR(14)		NOT NULL,
  `created_at`          TIMESTAMP,
  PRIMARY KEY  (`version`)
) ENGINE=MyISAM;

INSERT INTO `__CL_MAIN__survey2_version` 
        (version) 
VALUES 
        ("00000000000002");
-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_question` 
    ADD COLUMN `author_id` INTEGER NULL, 
    ADD COLUMN `shared` TINYINT(1) NULL DEFAULT 1,
    ADD INDEX author_idx (`author_id`);

-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_participation` 
    DROP INDEX `surveyId`,
    ADD COLUMN `updated_at`          TIMESTAMP;

-- --------------------------------------------------------


