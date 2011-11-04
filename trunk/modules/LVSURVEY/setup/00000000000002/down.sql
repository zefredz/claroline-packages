

DELETE FROM `__CL_MAIN__survey2_answer_item`
WHERE `answerId` IN 
    (   SELECT A.`id` 
        FROM `__CL_MAIN__survey2_answer` A 
        JOIN `__CL_MAIN__survey2_participation` P
            ON A.`participationId` = P.`id`
        WHERE P.`userId` = 0
    );


DELETE FROM `__CL_MAIN__survey2_answer`
WHERE `participationId` IN 
    (   SELECT `id` 
        FROM `__CL_MAIN__survey2_participation` 
        WHERE `userId` = 0
    );


DELETE FROM `__CL_MAIN__survey2_participation`
WHERE `userId` = 0;


ALTER TABLE `__CL_MAIN__survey2_participation` 
    ADD UNIQUE KEY `surveyId` (`surveyId`,`userId`),
    DROP COLUMN `updated_at`; 
-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_question` 
    DROP COLUMN `author_id`,
    DROP COLUMN `shared`,
    DROP INDEX author_idx;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `__CL_MAIN__survey2_version`;

-- --------------------------------------------------------