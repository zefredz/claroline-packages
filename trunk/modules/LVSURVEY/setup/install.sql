-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 16, 2008 at 10:19 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `claroline`
--

-- --------------------------------------------------------

--
-- Table structure for table `cl_survey2_answer_choice`
--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_answer_choice` (
  `id` int(11) NOT NULL auto_increment,
  `surveyId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `userId` int(11) default NULL,
  `answer` int(11) NOT NULL,
  `comment` VARCHAR(200) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;


-- --------------------------------------------------------

--
-- Table structure for table `cl_survey2_choice`
--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_choice` (
  `id` int(11) NOT NULL auto_increment,
  `questionId` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `cl_survey2_question`
--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_question` (
  `id` int(11) NOT NULL auto_increment,
  `courseId` varchar(12) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('TEXT','MCSA','MCMA') NOT NULL default 'TEXT',
  `alignment` enum('VERTI','HORIZ') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `cl_survey2_rel_survey_question`
--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_rel_survey_question` (
  `id` int(11) NOT NULL auto_increment,
  `surveyId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `cl_survey2_rel_survey_user`
--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_rel_survey_user` (
  `surveyId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY  (`surveyId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cl_survey2_survey`
--

CREATE TABLE IF NOT EXISTS `__CL_MAIN__survey2_survey` (
  `id` int(10) NOT NULL auto_increment,
  `courseId` varchar(40) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `anonymous` enum('YES','NO') NOT NULL default 'NO',
  `visibility` enum('VISIBLE','INVISIBLE') NOT NULL default 'INVISIBLE',
  `resultsVisibility` enum('VISIBLE','INVISIBLE','VISIBLE_AT_END') NOT NULL default 'INVISIBLE',
  `startDate` datetime default NULL,
  `endDate` datetime default NULL,
  `rank` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Table for surveys' AUTO_INCREMENT=7 ;
