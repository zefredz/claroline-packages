/**
 * $Id$
 * Online Help Form
 *
 * @version     ICHELP 0.8 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_MAIN__ichelp_log`(
    ticketId VARCHAR(64) NOT NULL,
    userId INT(11),
    courseId VARCHAR(64),
    submissionDate DATETIME,
    userAgent TEXT,
    urlOrigin TEXT,
    userInfos TEXT,
    issueDescription TEXT,
    shortDescription VARCHAR(255),
    mailSent BOOLEAN NOT NULL DEFAULT FALSE,
    autoMailSent BOOLEAN NOT NULL DEFAULT FALSE,
    status ENUM('pending','solved','closed') NOT NULL DEFAULT 'pending',
    PRIMARY KEY(ticketId)
) ENGINE=MyISAM;