DROP TABLE IF EXISTS `__CL_MAIN__survey2_version`;

-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_participation` 
    ADD UNIQUE KEY `surveyId` (`surveyId`,`userId`);
-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_question` 
    DROP COLUMN `author_id`,
    DROP COLUMN `shared`,
    DROP INDEX author_idx;

-- --------------------------------------------------------
