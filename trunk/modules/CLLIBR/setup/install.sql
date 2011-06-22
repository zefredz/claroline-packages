/**
 * $Id$
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_resource`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    creation_date DATETIME,
    storage_type ENUM('file','link') NOT NULL DEFAULT 'file',
    resource_type VARCHAR(32) NOT NULL,
    resource_name VARCHAR(128) NOT NULL,
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_metadata`(
    resource_id INT(11) NOT NULL,
    metadata_name VARCHAR(128),
    metadata_type ENUM('short','long') NOT NULL DEFAULT 'short',
    metadata_value TEXT,
    FULLTEXT KEY metadata (metadata_name,metadata_value),
    KEY(resource_id,metadata_name)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_library`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(128),
    status ENUM('public','restricted','private') NOT NULL DEFAULT 'private',
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_librarian`(
    user_id  INT(11),
    library_id INT(11),
    PRIMARY KEY(user_id,library_id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_collection`(
    resource_id INT(11) NOT NULL,
    collection_type ENUM('catalogue','bibliography','bookmark') NOT NULL,
    ref_id VARCHAR(16) NOT NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    PRIMARY KEY(resource_id,collection_type,ref_id)
) ENGINE=MyISAM;
