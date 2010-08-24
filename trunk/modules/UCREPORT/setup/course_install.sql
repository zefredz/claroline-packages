/**
 * $Id$
 * Student Report for Claroline
 *
 * @version     UCREPORT 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_COURSE__report_reports`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(127),
    datas TEXT,
    publication_date DATETIME,
    visibility ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
    PRIMARY KEY( id )
);