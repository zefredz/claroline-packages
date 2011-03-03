/**
 * $Id$
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_resource`(
    uid VARCHAR(32) NOT NULL,
    creation_date DATETIME,
    mime_type VARCHAR(128) NOT NULL DEFAULT 'text/html',
    resource_name VARCHAR(128) NOT NULL,
    PRIMARY KEY( uid )
);

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_metadata`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    resource_uid VARCHAR(32) NOT NULL,
    name VARCHAR(128),
    value TEXT,
    PRIMARY KEY( id )
);

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_library`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(127),
    is_public BOOLEAN DEFAULT FALSE,
    PRIMARY KEY( id )
);

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_librarian`(
    user_id  INT(11),
    library_id INT(11),
    PRIMARY KEY( user_id, library_id )
);

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_resource_set`(
    resource_uid VARCHAR(32) NOT NULL,
    type ENUM('catalogue','bibliography','bookmark'),
    ref_id VARCHAR(16) NOT NULL,
    PRIMARY KEY( resource_uid, type )
);