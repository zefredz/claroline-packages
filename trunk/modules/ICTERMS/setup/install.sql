CREATE TABLE IF NOT EXISTS `__CL_MAIN__icterms_acceptances` (
    user_id INT(11), -- USER ID
    terms_acceptance_timestamp INT, -- UNIX TIMESTAMP
    UNIQUE KEY user_id (user_id)
);