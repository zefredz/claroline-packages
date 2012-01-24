
UPDATE `__CL_MAIN__survey2_version` 
    SET `version` = "00000000000003";

-- --------------------------------------------------------

ALTER TABLE `__CL_MAIN__survey2_answer` 
    ADD COLUMN `predefined` VARCHAR(32) NULL;


ALTER TABLE `__CL_MAIN__survey2_question` 
    MODIFY COLUMN `type` ENUM( 'OPEN', 'MCSA', 'MCMA', 'ARRAY', 'LIKERT' ) NOT NULL DEFAULT 'MCSA';
