/*
Module COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/
/* Création de la table qui stocke les associations entre documents compilatio et claroline */
CREATE TABLE IF NOT EXISTS `__CL_MAIN__compilatio_docs` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT,
    `submission_id` INT( 11 ) NOT NULL,
    `assignment_id` INT( 11 ) NOT NULL,
    `compilatio_id` VARCHAR( 32 ) NOT NULL,
    `course_code` VARCHAR( 40 ) NOT NULL,
    PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1;

/*Création de la table qui stocke les informations sur le serveur CAS pour compilatio */
 CREATE TABLE `__CL_MAIN__compilatio_auth_serv` (
    `id_auth_serv` VARCHAR( 32 ) NOT NULL,
    `host_auth_serv` VARCHAR( 100 ) NOT NULL,
    `port_auth_serv` INT NOT NULL,
    `version_auth_serv` INT NOT NULL DEFAULT '2',
    `uri_auth_serv` VARCHAR( 100 ) NOT NULL,
    PRIMARY KEY ( `id_auth_serv` )
) TYPE=MyISAM 