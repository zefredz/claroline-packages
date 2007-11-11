<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 1.0 $Revision: 9 $
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSTAT
 *
 * @author Christophe Gesché <moosh@claroline.net>
 *
 */


function IPMSTAT_install()
{
    $tbl = claro_sql_get_tbl( array('stat_courses','stat_data_matrix'));

    return claro_sql_query("CREATE TABLE IF NOT EXISTs `" . $tbl ['stat_data_matrix'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `course_code` varchar(100) NOT NULL default '',
  `valueName` varchar(100) NOT NULL default '',
  `content` text,
  `pass` int(11) default '0',
  `dat_cr` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `dat_up` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `pass` (`pass`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; ")
   && claro_sql_query("CREATE TABLE IF NOT EXISTs `" . $tbl ['stat_courses'] . "` (
  `id` int(11) NOT NULL auto_increment,
  `course_code` varchar(100) NOT NULL default '',
  `content` text,
  `pass` int(11) default '0',
  `dat_cr` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `dat_up` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `pass` (`pass`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; ");

}

IPMSTAT_install();



?>