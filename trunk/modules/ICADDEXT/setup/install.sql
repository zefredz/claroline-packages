CREATE TABLE IF NOT EXISTS `__CL_MAIN__ICADDEXT_user_added`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    actif tinyINT(1) NOT NULL DEFAULT 0,
    mail_envoye tinyINT(1) NOT NULL DEFAULT 0,
    user_id INT(11) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(32) NOT NULL,
    date_naissance VARCHAR(64) NOT NULL,
    institution VARCHAR(64) DEFAULT NULL,
    annee_etude VARCHAR(32) DEFAULT NULL,
    officialCode VARCHAR(32) NOT NULL,
    date_ajout DATETIME NOT NULL,
    remarques TEXT,
    PRIMARY KEY(id),
    UNIQUE KEY offical_code(officialCode)
) ENGINE=MyISAM;