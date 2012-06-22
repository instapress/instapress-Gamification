-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 22, 2012 at 09:50 AM
-- Server version: 5.0.77
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `instacheckin_gamification`
--

-- --------------------------------------------------------

--
-- Table structure for table `gamification_activity_type`
--

CREATE TABLE IF NOT EXISTS `gamification_activity_type` (
  `activityTypeId` int(10) unsigned NOT NULL auto_increment,
  `publicationId` int(10) unsigned NOT NULL,
  `activityName` varchar(255) collate utf8_unicode_ci NOT NULL,
  `activityOwnership` varchar(10) collate utf8_unicode_ci NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `activityVerb` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityCommonObject` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'YES',
  `activityCommonVerb` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'YES',
  `activityText` varchar(250) collate utf8_unicode_ci NOT NULL,
  `activityPoints` int(10) unsigned NOT NULL,
  `activityPointsType` enum('+ve','-ve') collate utf8_unicode_ci NOT NULL default '+ve',
  `userEligibilityPoints` int(11) NOT NULL,
  `pointIndicator` enum('self','other','both') collate utf8_unicode_ci NOT NULL default 'self',
  `activityTypeFrom` enum('user','moderator','admin') collate utf8_unicode_ci NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `clientId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`activityTypeId`),
  KEY `publicationId` (`publicationId`),
  KEY `userEligibilityPoints` (`userEligibilityPoints`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=127 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_badge`
--

CREATE TABLE IF NOT EXISTS `gamification_badge` (
  `badgeId` int(10) unsigned NOT NULL auto_increment,
  `publicationId` int(10) unsigned NOT NULL,
  `badgeLevelType` enum('1','2','3','4','5') collate utf8_unicode_ci NOT NULL,
  `badgeName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `badgeDesc` text collate utf8_unicode_ci NOT NULL,
  `activityOwnership` varchar(10) collate utf8_unicode_ci NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `activityVerb` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityCommonObject` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `activityCommonVerb` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `multipleBadges` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'NO',
  `activityCount` int(10) unsigned NOT NULL,
  `imageUrl` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`badgeId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=103 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_collectible`
--

CREATE TABLE IF NOT EXISTS `gamification_collectible` (
  `collectibleId` int(10) unsigned NOT NULL auto_increment,
  `publicationId` int(10) unsigned NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `contentType` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityCount` int(10) unsigned NOT NULL,
  `collectibleName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `collectibleDescription` text collate utf8_unicode_ci NOT NULL,
  `collectibleImageUrl` varchar(255) collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`collectibleId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_content_type`
--

CREATE TABLE IF NOT EXISTS `gamification_content_type` (
  `gamificationContentTypeId` int(10) unsigned NOT NULL auto_increment,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `contentType` varchar(50) collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`gamificationContentTypeId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_friend_activity_stream`
--

CREATE TABLE IF NOT EXISTS `gamification_friend_activity_stream` (
  `activityStreamId` int(10) unsigned NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL,
  `friendId` int(10) unsigned NOT NULL,
  `publicationId` smallint(5) unsigned NOT NULL,
  `activityId` bigint(20) unsigned NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `activityVerb` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityText` text collate utf8_unicode_ci NOT NULL,
  `comman` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'NO',
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`activityStreamId`),
  KEY `userId` (`userId`),
  KEY `friendId` (`friendId`),
  KEY `publicationId` (`publicationId`),
  KEY `comman` (`comman`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=123091 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_moderator_type`
--

CREATE TABLE IF NOT EXISTS `gamification_moderator_type` (
  `moderatorTypeId` int(10) unsigned NOT NULL auto_increment,
  `moderatorNo` int(11) NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `moderatorName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `unlockingPoints` int(10) unsigned NOT NULL,
  `imageUrl` varchar(255) collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`moderatorTypeId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_notification`
--

CREATE TABLE IF NOT EXISTS `gamification_notification` (
  `notificationId` int(10) unsigned NOT NULL auto_increment,
  `notificationTypeId` int(10) unsigned NOT NULL,
  `notificationType` enum('badge','moderator','collectible','level') collate utf8_unicode_ci NOT NULL,
  `notificationStatus` enum('read','unread','deleted') collate utf8_unicode_ci NOT NULL,
  `notificationText` varchar(255) collate utf8_unicode_ci NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`notificationId`),
  KEY `clientId` (`clientId`),
  KEY `publicationId` (`publicationId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4254 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_object_master`
--

CREATE TABLE IF NOT EXISTS `gamification_object_master` (
  `gamificationObjectMasterId` int(10) unsigned NOT NULL auto_increment,
  `gamificationObjectName` varchar(25) collate utf8_unicode_ci NOT NULL,
  `commonObject` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'YES',
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`gamificationObjectMasterId`),
  UNIQUE KEY `gamificationObjectName` (`gamificationObjectName`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_ownership_master`
--

CREATE TABLE IF NOT EXISTS `gamification_ownership_master` (
  `gamificationOwnershipMasterId` int(10) unsigned NOT NULL auto_increment,
  `gamificationOwnershipName` varchar(10) collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `createdBy` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`gamificationOwnershipMasterId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_publication`
--

CREATE TABLE IF NOT EXISTS `gamification_publication` (
  `publicationId` smallint(5) unsigned NOT NULL auto_increment,
  `clientId` smallint(5) unsigned NOT NULL,
  `publicationName` varchar(150) collate utf8_unicode_ci NOT NULL,
  `publicationDescription` varchar(255) collate utf8_unicode_ci NOT NULL,
  `publicationActiveStatus` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `publicationPublishedStory` mediumint(8) unsigned NOT NULL,
  `publicationStartTime` datetime NOT NULL,
  `publicationUrl` varchar(255) collate utf8_unicode_ci NOT NULL,
  `sectionId` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL,
  `gaProfileName` varchar(100) collate utf8_unicode_ci NOT NULL,
  `webPropertyIdentity` varchar(25) collate utf8_unicode_ci NOT NULL,
  `webProfileId` bigint(20) unsigned NOT NULL,
  `statCounterCode` text collate utf8_unicode_ci NOT NULL,
  `qualityAnalysis` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `buyerId` int(10) unsigned NOT NULL,
  `createdUserId` int(10) unsigned NOT NULL,
  `createdTime` datetime NOT NULL,
  PRIMARY KEY  (`publicationId`),
  KEY `cid_pn` (`clientId`,`publicationName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=509 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_url`
--

CREATE TABLE IF NOT EXISTS `gamification_url` (
  `urlId` mediumint(8) unsigned NOT NULL auto_increment,
  `urlSlug` varchar(150) collate utf8_unicode_ci NOT NULL,
  `phpFilePath` varchar(100) collate utf8_unicode_ci NOT NULL,
  `phpFile` varchar(100) collate utf8_unicode_ci NOT NULL,
  `createdUserId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `urlPermissions` int(11) NOT NULL,
  PRIMARY KEY  (`urlId`),
  UNIQUE KEY `cstshc` (`urlSlug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_activity_rel`
--

CREATE TABLE IF NOT EXISTS `gamification_user_activity_rel` (
  `userActivityId` int(10) unsigned NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL,
  `activityOwnership` varchar(10) collate utf8_unicode_ci NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `activityVerb` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityCommonObject` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `activityCommonVerb` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `activityText` text collate utf8_unicode_ci NOT NULL,
  `comman` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'YES',
  `clientId` int(10) unsigned NOT NULL,
  `contentId` int(10) unsigned NOT NULL,
  `contentType` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityPoints` int(10) unsigned NOT NULL,
  `activityPointsType` enum('+ve','-ve') collate utf8_unicode_ci NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`userActivityId`),
  KEY `userId` (`userId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19554 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_badge_rel`
--

CREATE TABLE IF NOT EXISTS `gamification_user_badge_rel` (
  `userBadgeRelId` int(11) NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL,
  `badgeId` int(10) unsigned NOT NULL,
  `badgeLevelType` int(10) unsigned NOT NULL COMMENT '1,2,3,4,5',
  `activityOwnership` varchar(10) collate utf8_unicode_ci NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `activityVerb` varchar(50) collate utf8_unicode_ci NOT NULL,
  `activityCommonObject` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `activityCommonVerb` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `higestLevel` enum('YES','NO') collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`userBadgeRelId`),
  KEY `userId` (`userId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=147 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_collectible_rel`
--

CREATE TABLE IF NOT EXISTS `gamification_user_collectible_rel` (
  `gamificationUserCollectibleRelId` int(10) unsigned NOT NULL auto_increment,
  `publicationId` int(10) unsigned NOT NULL,
  `collectibleId` int(11) NOT NULL,
  `activityObject` varchar(25) collate utf8_unicode_ci NOT NULL,
  `contentType` varchar(50) collate utf8_unicode_ci NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`gamificationUserCollectibleRelId`),
  KEY `publicationId` (`publicationId`),
  KEY `userId` (`userId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_level_rel`
--

CREATE TABLE IF NOT EXISTS `gamification_user_level_rel` (
  `userLevelRelId` int(11) NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL,
  `userLevelNo` int(10) unsigned NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `clientId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`userLevelRelId`),
  KEY `userId` (`userId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3179 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_level_type`
--

CREATE TABLE IF NOT EXISTS `gamification_user_level_type` (
  `userLevelTypeId` int(10) unsigned NOT NULL auto_increment,
  `userLevelNo` int(11) NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `userLevelName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `unlockingPoints` int(10) unsigned NOT NULL,
  `imageUrl` varchar(255) collate utf8_unicode_ci NOT NULL,
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`userLevelTypeId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`),
  KEY `userLevelNo` (`userLevelNo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_moderation_rel`
--

CREATE TABLE IF NOT EXISTS `gamification_user_moderation_rel` (
  `userModerationRelId` int(11) NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL,
  `moderatorNo` int(10) unsigned NOT NULL,
  `publicationId` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `clientId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`userModerationRelId`),
  KEY `userId` (`userId`),
  KEY `publicationId` (`publicationId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_user_publication_status`
--

CREATE TABLE IF NOT EXISTS `gamification_user_publication_status` (
  `userPublicationStatusId` int(11) NOT NULL auto_increment,
  `publicationId` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `status` enum('banned','deleted','active') collate utf8_unicode_ci NOT NULL,
  `totalPositivePoints` int(10) unsigned NOT NULL,
  `totalNegativePoints` int(10) unsigned NOT NULL,
  `lastActivityTime` timestamp NOT NULL default '0000-00-00 00:00:00',
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `clientId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`userPublicationStatusId`),
  KEY `publicationId` (`publicationId`),
  KEY `userId` (`userId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=423 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamification_verb_master`
--

CREATE TABLE IF NOT EXISTS `gamification_verb_master` (
  `gamificationVerbMasterId` int(10) unsigned NOT NULL auto_increment,
  `gamificationVerbName` varchar(50) collate utf8_unicode_ci NOT NULL,
  `commonVerb` enum('YES','NO') collate utf8_unicode_ci NOT NULL default 'YES',
  `clientId` int(10) unsigned NOT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  `createdTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`gamificationVerbMasterId`),
  UNIQUE KEY `gamificationVerbName` (`gamificationVerbName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=37 ;
