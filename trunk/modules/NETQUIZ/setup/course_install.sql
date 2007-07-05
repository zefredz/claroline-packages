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

CREATE TABLE IF NOT EXISTS `__CL_COURSE__nq_participants` (
  `IDParticipant` int(10) unsigned NOT NULL auto_increment,
  `currentUserId` int(11) NOT NULL,
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
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_participations`
-- 

CREATE TABLE IF NOT EXISTS `__CL_COURSE__nq_participations` (
  `IDParticipant` int(10) unsigned NOT NULL default '0',
  `IDQuestion` int(10) unsigned NOT NULL default '0',
  `Pointage` float NOT NULL default '0',
  `PointageAuto` float NOT NULL default '0',
  `ReponseHTML` longtext NOT NULL,
  PRIMARY KEY  (`IDParticipant`,`IDQuestion`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_questions`
-- 

CREATE TABLE IF NOT EXISTS `__CL_COURSE__nq_questions` (
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
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `nq_quizs`
-- 

CREATE TABLE IF NOT EXISTS `__CL_COURSE__nq_quizs` (
  `IDQuiz` int(10) unsigned NOT NULL auto_increment,
  `RepQuizId` varchar(45) NOT NULL,
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
) ENGINE=MyISAM;
