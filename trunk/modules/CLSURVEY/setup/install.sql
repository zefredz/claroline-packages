# $Id$
#
# CLSURVEY
#
# version 1.0.0
#
# copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
# license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
#
# package CLSURVEY
#
# Contact : Christophe Gesché <moosh@claroline.net>
# Credit  : Philippe Dekimpe <dkp@ecam.be>
# General-contact Claro Team <cvs@claroline.net>
# --------------------------------------------------------#
# Structure de la table `survey_list`
#

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey_list` (
  `id_survey` int(11) NOT NULL auto_increment,
  `cid` varchar(40) NOT NULL default '0',
  `title` varchar(200) NOT NULL default '',
  `description` text,
  `visibility` varchar(4) NOT NULL default 'SHOW',
  `date_created` date NOT NULL default '0000-00-00',
  `rank` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_survey`),
  INDEX (`rank` )
) ;

# --------------------------------------------------------#
# Structure de la table `question_list`
#

CREATE TABLE IF NOT EXISTS `__CL_MAIN__question_list` (
  `id_question` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL  default '',
  `description` text,
  `type` varchar(10) NOT NULL  default 'radio',
  `option` text,
  `cid` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id_question`),
  INDEX (`cid`)
)  ;

# --------------------------------------------------------
#
# Structure de la table `survey_answer`
#

CREATE TABLE IF NOT EXISTS __CL_MAIN__survey_answer (
  `id_answer` int(11) NOT NULL auto_increment,
  `id_survey` int(11) NOT NULL default '0',
  `id_question` int(11) NOT NULL default '0',
  `cid` varchar(40) NOT NULL default '',
  `answer` varchar(255) NOT NULL default '',
   PRIMARY KEY  (`id_answer`),
   INDEX ( `id_survey` , `id_question` , `cid` )
) ;

# --------------------------------------------------------
#--
#-- Structure de la table `cl_survey_question`
#--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey_question` (
  `id_survey` int(11) NOT NULL default '0',
  `id_question` int(11) NOT NULL default '0',
  `rank` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_survey`,`id_question`),
  INDEX (`rank`)
)  ;

# --------------------------------------------------------
#--
#-- Structure de la table `cl_survey_user`
#--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey_user` (
  `id_survey` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_survey`, `id_user`)
)  ;
