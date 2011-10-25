ALTER TABLE `__CL_MAIN__survey2_question` DROP `author_id`;

DROP TABLE IF EXISTS `__CL_MAIN__survey2_version`;

ALTER TABLE `__CL_MAIN__survey2_participation` 
    ADD UNIQUE KEY `surveyId` (`surveyId`,`userId`);
