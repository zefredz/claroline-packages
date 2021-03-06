/* $Id$ */

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.2.2 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */
 
CREATE TABLE IF NOT EXISTS `__CL_COURSE__poll_polls`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title TEXT,
    question VARCHAR(255),
    poll_options VARCHAR(255),
    status ENUM('open','closed') NOT NULL DEFAULT 'open',
    visibility ENUM('visible','invisible') NOT NULL DEFAULT 'invisible',
    PRIMARY KEY( id )
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__poll_choices`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    poll_id INT(11) NOT NULL,
    label  VARCHAR(127),
    PRIMARY KEY( id )
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__poll_votes`(
    poll_id INT(11) NOT NULL,
    choice_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    vote ENUM('checked','notchecked') NOT NULL DEFAULT 'notchecked',
    primary KEY ( choice_id , user_id )
) ENGINE=MyISAM;
