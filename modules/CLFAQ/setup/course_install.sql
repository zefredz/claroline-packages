# $Id$
#
# CLFAQ
#
# version 1.0
#
# copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
# license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
#
# package CLFAQ
#
# Contact : Grégory KOCH <gregk84@gate71.be>
# General-contact Claro Team <cvs@claroline.net>

# --------------------------------------------------------#
# Structure de la table `clfaq`
#

CREATE TABLE IF NOT EXISTS __CL_COURSE__clfaq (
  `clfaq_id` int(11) NOT NULL auto_increment,
  `clfaq_id_category` int(11) NOT NULL default '1',
  `clfaq_question` varchar(200) NOT NULL default '',
  `clfaq_answer` text NOT NULL default '',
  PRIMARY KEY  (`clfaq_id`)
);

# --------------------------------------------------------#
# Structure de la table `clfaq_category`
#

CREATE TABLE IF NOT EXISTS __CL_COURSE__clfaq_category (
  `clfaq_category_id` int(11) NOT NULL auto_increment,
  `clfaq_category` varchar(40) NOT NULL default '',
  `clfaq_category_description` text NOT NULL,
  PRIMARY KEY  (`clfaq_category_id`)
);