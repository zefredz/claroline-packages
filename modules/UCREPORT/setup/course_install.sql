/**
 * $Id$
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/* Report tool table */
CREATE TABLE IF NOT EXISTS `__CL_COURSE__report_report`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(127),
    datas TEXT,
    publication_date DATETIME,
    confidentiality ENUM('PUBLIC','PRIVATE') NOT NULL DEFAULT 'PRIVATE',
    visibility ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
    PRIMARY KEY(id)
) ENGINE=MyISAM;


/* Integrated Examination tool tables */
CREATE TABLE IF NOT EXISTS `__CL_COURSE__examination_score`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    session_id INT(11),
    user_id INT(11),
    score TINYINT,
    comment TEXT,
    PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__examination_session`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255),
    max_score TINYINT NOT NULL DEFAULT 20,
    publication_date DATETIME,
    confidentiality ENUM('PUBLIC','PRIVATE') NOT NULL DEFAULT 'PRIVATE',
    visibility ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE ',
    PRIMARY KEY(id)
) ENGINE=MyISAM;