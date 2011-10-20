
CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_version` (
  `version` 		CHAR(14)		NOT NULL,
  `created_at`          TIMESTAMP,
  PRIMARY KEY  (`version`)
) ;

-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_question` 
    ADD COLUMN `author_id` INTEGER NULL, 
    ADD INDEX author_idx (`author_id`);
