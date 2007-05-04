# $Id$
#
# NETQUIZ
#
# version 1.0
#
# copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
# license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
#
# package NETQUIZ
#
# Contact : Grégory KOCH <gregk84@gate71.be>
# General-contact Claro Team <cvs@claroline.net>


-- --------------------------------------------------------

--
-- Structure de la table `nq_participants`
-- 

CREATE TABLE `nq_participants` (
  `IDParticipant` int(10) unsigned NOT NULL auto_increment,
  `Prenom` varchar(45) NOT NULL default '',
  `Nom` varchar(45) NOT NULL default '',
  `Groupe` varchar(45) NOT NULL default '',
  `Matricule` varchar(45) NOT NULL default '',
  `Courriel` varchar(45) NOT NULL default '',
  `Coordonnees` longtext,
  `ParticipationDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Final` int(11) NOT NULL default '0',
  `IDQuiz` int(11) NOT NULL default '0',
  `Actif` int(11) NOT NULL default '1',
  PRIMARY KEY  (`IDParticipant`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_participations`
-- 

CREATE TABLE `nq_participations` (
  `IDParticipant` int(10) unsigned NOT NULL default '0',
  `IDQuestion` int(10) unsigned NOT NULL default '0',
  `Pointage` float NOT NULL default '0',
  `PointageAuto` float NOT NULL default '0',
  `ReponseHTML` longtext NOT NULL,
  PRIMARY KEY  (`IDParticipant`,`IDQuestion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_questions`
-- 

CREATE TABLE `nq_questions` (
  `IDQuestion` int(10) unsigned NOT NULL auto_increment,
  `QuestionName` longtext NOT NULL,
  `QuestionType` varchar(45) NOT NULL default '',
  `QuestionTypeTD` varchar(45) NOT NULL default '',
  `Ponderation` float unsigned NOT NULL default '0',
  `EnonceHTML` longtext NOT NULL,
  `ReponseHTML` longtext,
  `ReponseXML` longtext NOT NULL,
  `IDQuiz` int(10) unsigned NOT NULL default '0',
  `NoQuestion` int(10) unsigned NOT NULL default '0',
  `Active` int(11) NOT NULL default '1',
  PRIMARY KEY  (`IDQuestion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_quizs`
-- 

CREATE TABLE `nq_quizs` (
  `IDQuiz` int(10) unsigned NOT NULL auto_increment,
  `QuizIdent` varchar(45) NOT NULL default '',
  `QuizVersion` varchar(45) NOT NULL default '',
  `QuizName` varchar(45) NOT NULL default '',
  `NbQuestions` int(10) unsigned NOT NULL default '0',
  `VersionDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Password` varchar(45) NOT NULL default '',
  `Title` longtext,
  `Auteur` longtext,
  `Actif` int(11) NOT NULL default '0',
  PRIMARY KEY  (`IDQuiz`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_users`
-- 

CREATE TABLE `nq_users` (
  `IDUser` int(10) unsigned NOT NULL auto_increment,
  `Username` varchar(45) NOT NULL default '',
  `LoginPassword` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`IDUser`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
