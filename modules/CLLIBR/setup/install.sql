/**
 * $Id$
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_resource`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    creation_date DATETIME,
    resource_type VARCHAR(32) NOT NULL,
    resource_name VARCHAR(128) NOT NULL,
    title VARCHAR(256) NOT NULL,
    description TEXT,
    PRIMARY KEY( id ),
    FULLTEXT KEY title (title),
    FULLTEXT KEY description (description)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_metadata`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    resource_id INT(11) NOT NULL,
    name VARCHAR(128),
    value TEXT,
    PRIMARY KEY( id ),
    FULLTEXT KEY metadata (name,value)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_library`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(128),
    is_public BOOLEAN DEFAULT FALSE,
    PRIMARY KEY( id )
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_librarian`(
    user_id  INT(11),
    library_id INT(11),
    PRIMARY KEY( user_id, library_id )
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_collection`(
    resource_id INT(11) NOT NULL,
    type ENUM('catalogue','bibliography','bookmark'),
    ref_id VARCHAR(16) NOT NULL,
    PRIMARY KEY( resource_id, type, ref_id )
) ENGINE=MyISAM;
