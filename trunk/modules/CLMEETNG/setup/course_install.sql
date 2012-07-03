/* $Id$ */

/**
 * Online Meetings
 *
 * @version     CLMEETNG 0.1.0 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLMEETNG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

CREATE TABLE IF NOT EXISTS `__CL_COURSE__CLMEETNG_meeting`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    /*session_id VARCHAR(32) NOT NULL,*/
    creator_id INT(11) NOT NULL,
    group_id int(11),
    title VARCHAR(127) NOT NULL,
    description TEXT,
    date_from DATETIME NOT NULL,
    date_to DATETIME NOT NULL,
    creation_date DATETIME NOT NULL,
    modification_date DATETIME NOT NULL,
    meeting_type INT(1) NOT NULL DEFAULT 1, /* 1 = Conference, 2 = Audience, 3 = Restricted, 4 = Interview, 5 = custom */
    meeting_lang INT(2) NOT NULL DEFAULT 1, /* 1 = english, 4 = french (for complete list, see CLMEETNG::$langList) */
    max_user INT(2) NOT NULL DEFAULT 5,
    room_id INT(11) NOT NULL,
    room_recording_id INT(11) NOT NULL,
    is_moderated BOOLEAN NOT NULL DEFAULT TRUE,
    is_recording_allowed BOOLEAN NOT NULL DEFAULT TRUE,
    is_open BOOLEAN NOT NULL DEFAULT TRUE,
    is_visible BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY( id )
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__CLMEETNG_user`(
    user_id INT(11) NOT NULL,
    username VARCHAR(32) NOT NULL,
    password VARCHAR(32) NOT NULL,
    PRIMARY KEY( user_id )
) ENGINE=MyISAM;
