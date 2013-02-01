/**
 * $Id$
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_resource`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    submitter_id INT(11),
    creation_date DATETIME,
    storage_type ENUM('file','url') NOT NULL DEFAULT 'file',
    resource_type VARCHAR(32) NOT NULL,
    resource_name VARCHAR(128) NOT NULL,
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_metadata`(
    resource_id INT(11) NOT NULL,
    metadata_name VARCHAR(128) NOT NULL,
    metadata_value TEXT NOT NULL,
    FULLTEXT KEY metadata (metadata_name,metadata_value),
    KEY(resource_id,metadata_name)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_library`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(128) NOT NULL,
    status ENUM('public','restricted','private') NOT NULL DEFAULT 'private',
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_librarian`(
    user_id  INT(11) NOT NULL,
    library_id INT(11) NOT NULL,
    PRIMARY KEY(user_id,library_id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_course_library`(
    course_id VARCHAR(16) NOT NULL,
    library_id INT(11) NOT NULL,
    title VARCHAR(128) NOT NULL,
    PRIMARY KEY(course_id,library_id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_collection`(
    resource_id INT(11) NOT NULL,
    collection_type ENUM('catalogue','bibliography','bookmark') NOT NULL,
    ref_id VARCHAR(16) NOT NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    PRIMARY KEY(resource_id,collection_type,ref_id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__library_user_note`(
    user_id INT(11) NOT NULL,
    resource_id INT(11) NOT NULL,
    content TEXT,
    PRIMARY KEY(user_id,resource_id)
) ENGINE=MyISAM;