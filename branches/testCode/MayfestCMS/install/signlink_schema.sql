-- phpMyAdmin SQL Dump
-- version 2.11.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 15, 2008 at 09:32 AM
-- Server version: 5.0.54
-- PHP Version: 5.2.6-pl7-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `signlinkcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE IF NOT EXISTS `forums` (
  `forum_id` mediumint(8) unsigned NOT NULL auto_increment,
  `subject` varchar(100) NOT NULL,
  `subject_alt` varchar(255) NOT NULL,
  `num_topics` mediumint(8) unsigned NOT NULL default '0',
  `num_posts` mediumint(8) unsigned NOT NULL default '0',
  `last_post` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`forum_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forums_posts`
--

CREATE TABLE IF NOT EXISTS `forums_posts` (
  `post_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `forum_id` mediumint(8) unsigned NOT NULL default '0',
  `login` varchar(20) NOT NULL default '',
  `last_comment` datetime NOT NULL default '0000-00-00 00:00:00',
  `num_comments` mediumint(8) unsigned NOT NULL default '0',
  `subject` varchar(100) NOT NULL default '',
  `subject_alt` varchar(255) NOT NULL default '',
  `msg` text NOT NULL,
  `msg_alt` varchar(255) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `locked` tinyint(4) NOT NULL default '0',
  `sticky` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forums_read`
--

CREATE TABLE IF NOT EXISTS `forums_read` (
  `post_id` mediumint(9) NOT NULL,
  `member_id` mediumint(9) NOT NULL,
  `forum_id` mediumint(9) NOT NULL,
  `parent_id` mediumint(9) NOT NULL,
  PRIMARY KEY  (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_views`
--

CREATE TABLE IF NOT EXISTS `forums_views` (
  `post_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `views` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `member_id` mediumint(9) NOT NULL auto_increment,
  `login` varchar(20) NOT NULL default '',
  `bl_pass` blob,
  `name` varchar(265) NOT NULL,
  `email` varchar(265) NOT NULL,
  `created_ts` timestamp not null default CURRENT_TIMESTAMP,
  `passresetexp_ts` timestamp NULL DEFAULT 0,
  `last_login_ts` timestamp NULL DEFAULT 0,
  `post_count` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

INSERT INTO `members` (`member_id`, `login`, `bl_pass`, `name`, `email`, 'created_ts', 'passresetexp_ts', 'last_login_ts') VALUES
(1, 'admin', 0x82aea95cb77f3c2eee4731302359a5a33ff1877f378dfba966e83d0a7fdcc83c, 'admin', 'admin@admin.org', DEFAULT, DEFAULT, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` mediumint(9) NOT NULL auto_increment,
  `parent_id` mediumint(9) NOT NULL default '0',
  `member_id` mediumint(9) NOT NULL,
  `title` varchar(150) NOT NULL default '',
  `title_alt` varchar(80) NOT NULL,
  `content` text NOT NULL,
  `content_alt` varchar(80) NOT NULL,
  `outline` text NOT NULL,
  `created` datetime NOT NULL,
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `links_to` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `name`, `value`) VALUES
(1, 'contact', 'admin@admin.org'),
(2, 'site_name', 'Signlink CMS'),
(3, 'max_upload_size', '5242880'),
(4, 'reg_user_only', 0),
(5, 'registration_closed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vlogs`
--

CREATE TABLE `vlogs` (
  `vlog_id` MEDIUMINT UNSIGNED NOT NULL auto_increment,
  `member_id` MEDIUMINT NOT NULL ,
  `title` VARCHAR( 255 ) NOT NULL ,
  `title_alt` VARCHAR( 255 ) NOT NULL ,
  `num_entries` MEDIUMINT NOT NULL ,
  `last_entry` DATETIME NOT NULL ,
  PRIMARY KEY ( `vlog_id` )
) ENGINE = InnoDB ;

-- --------------------------------------------------------

--
-- Table structure for table `vlogs_comments`
--
CREATE TABLE IF NOT EXISTS `vlogs_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` mediumint(9) NOT NULL,
  `vlog_id` mediumint(9) NOT NULL,
  `entry_id` mediumint(9) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `comment_alt` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vlogs_entries`
--

CREATE TABLE IF NOT EXISTS `vlogs_entries` (
  `entry_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `vlog_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `title_alt` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `content_alt` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_comments` mediumint(9) NOT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------
