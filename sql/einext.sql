-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `apitokens`;
CREATE TABLE `apitokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) unsigned DEFAULT NULL,
  `token` varchar(200) COLLATE utf8mb4_german2_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `apitokens_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `campaign_locations`;
CREATE TABLE `campaign_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phoneNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobilePhoneNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agentFromId` int(10) unsigned DEFAULT NULL,
  `agentLastChangeId` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `revision` int(11) DEFAULT NULL,
  `locationCategoryId` int(10) unsigned DEFAULT NULL,
  `markierung` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SCHOOLSCHOOL_AFID` (`agentFromId`),
  KEY `SCHOOLSCHOOL_ALCID` (`agentLastChangeId`),
  KEY `school_s_i_c_d` (`id`,`deleted_at`),
  KEY `CL_LCID` (`locationCategoryId`),
  KEY `CAM_L_DAT` (`deleted_at`),
  KEY `CAM_L_DATID` (`deleted_at`,`id`),
  KEY `CL_PFED` (`phoneNumber`(191),`fax`(191),`email`(191),`deleted_at`),
  KEY `CAM_L_TITLE` (`deleted_at`,`title`(191)),
  KEY `state` (`state`(191)),
  CONSTRAINT `CL_LCID` FOREIGN KEY (`locationCategoryId`) REFERENCES `campaign_location_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `SCHOOLSCHOOL_AFID` FOREIGN KEY (`agentFromId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `SCHOOLSCHOOL_ALCID` FOREIGN KEY (`agentLastChangeId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `campaign_location_categories`;
CREATE TABLE `campaign_location_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `agentFromId` int(10) unsigned DEFAULT NULL,
  `agentLastChangeId` int(10) unsigned DEFAULT NULL,
  `revision` int(10) unsigned DEFAULT NULL,
  `stacktraceLastChange` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `CLC_AFID` (`agentFromId`),
  KEY `CLC_ALCID` (`agentLastChangeId`),
  CONSTRAINT `CLC_AFID` FOREIGN KEY (`agentFromId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CLC_ALCID` FOREIGN KEY (`agentLastChangeId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `campaign_location_events`;
CREATE TABLE `campaign_location_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lockedUserId` int(10) unsigned DEFAULT NULL,
  `ursprungseventId` int(10) unsigned DEFAULT NULL,
  `showAfter` timestamp NULL DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `showAfterTriedCount` int(11) NOT NULL DEFAULT '1',
  `agentId` int(10) unsigned DEFAULT NULL,
  `agentLastSeen` timestamp NULL DEFAULT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `failed` tinyint(1) NOT NULL DEFAULT '0',
  `result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `schoolId` int(10) unsigned DEFAULT NULL,
  `agentFromId` int(10) unsigned DEFAULT NULL,
  `agentLastChangeId` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `revision` int(11) DEFAULT NULL,
  `finishedTimestamp` timestamp NULL DEFAULT NULL,
  `finishedAgentId` int(10) unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `markierung` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notiz` text COLLATE utf8mb4_unicode_ci,
  `arbeitskategorie` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `shownAt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_events_agentid_foreign` (`agentId`),
  KEY `schoolId` (`schoolId`),
  KEY `SEE_AFID` (`agentFromId`),
  KEY `SEE_ALCID` (`agentLastChangeId`),
  KEY `SCHOOL_E_D_R` (`deleted_at`,`result`(191)),
  KEY `CLE_FINISHED_AGENT` (`finishedAgentId`),
  KEY `CLE_FFR` (`finishedTimestamp`,`finishedAgentId`,`result`(191)),
  KEY `timestamp` (`timestamp`),
  KEY `done` (`done`),
  KEY `lockedUserId` (`lockedUserId`),
  KEY `arbeitskategorie` (`arbeitskategorie`),
  CONSTRAINT `CLE_FINISHED_AGENT` FOREIGN KEY (`finishedAgentId`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `SEE_AFID` FOREIGN KEY (`agentFromId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `SEE_ALCID` FOREIGN KEY (`agentLastChangeId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `schoolId` FOREIGN KEY (`schoolId`) REFERENCES `campaign_locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `school_events_agentid_foreign` FOREIGN KEY (`agentId`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `campaign_location_event_matcher_rules`;
CREATE TABLE `campaign_location_event_matcher_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoryId` int(10) unsigned NOT NULL,
  `wd0_hour_start` tinyint(4) DEFAULT NULL,
  `wd1_hour_start` tinyint(4) DEFAULT NULL,
  `wd2_hour_start` tinyint(4) DEFAULT NULL,
  `wd3_hour_start` tinyint(4) DEFAULT NULL,
  `wd4_hour_start` tinyint(4) DEFAULT NULL,
  `wd5_hour_start` tinyint(4) DEFAULT NULL,
  `wd6_hour_start` tinyint(4) DEFAULT NULL,
  `wd0_hour_end` tinyint(4) DEFAULT NULL,
  `wd1_hour_end` tinyint(4) DEFAULT NULL,
  `wd2_hour_end` tinyint(4) DEFAULT NULL,
  `wd3_hour_end` tinyint(4) DEFAULT NULL,
  `wd4_hour_end` tinyint(4) DEFAULT NULL,
  `wd5_hour_end` tinyint(4) DEFAULT NULL,
  `wd6_hour_end` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `menuitems`;
CREATE TABLE `menuitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuid` int(11) DEFAULT NULL,
  `titel` varchar(60) COLLATE utf8mb4_german2_ci NOT NULL,
  `routerlink` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `nummer` int(11) NOT NULL,
  `schluessel` varchar(10) COLLATE utf8mb4_german2_ci NOT NULL,
  `sichtbar` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

INSERT INTO `menuitems` (`id`, `menuid`, `titel`, `routerlink`, `nummer`, `schluessel`, `sichtbar`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4,	13,	'Anrufen',	'/branches/call',	1,	'b1',	1,	NULL,	NULL,	NULL),
(5,	13,	'Zurückrufen',	'/branches/callback',	1,	'b2',	1,	NULL,	NULL,	NULL),
(6,	14,	'Branchen freischalten und ändern',	NULL,	10,	'',	1,	NULL,	NULL,	NULL),
(7,	14,	'Regeln für Anrufzeiten',	NULL,	20,	'',	1,	NULL,	NULL,	NULL),
(8,	14,	'Rückrufereignisse freischalten',	NULL,	30,	'',	1,	NULL,	NULL,	NULL),
(9,	14,	'Kategorien zusammenführen',	NULL,	40,	'',	1,	NULL,	NULL,	NULL),
(10,	14,	'Unternehmen nach Mustern suchen',	NULL,	50,	'',	1,	NULL,	NULL,	NULL),
(11,	15,	'Branchen - Statistik',	NULL,	10,	'',	1,	NULL,	NULL,	NULL),
(12,	15,	'Branchen - Statistik - Pausen',	NULL,	20,	'',	1,	NULL,	NULL,	NULL),
(13,	15,	'Quote der Kategorien',	NULL,	30,	'',	1,	NULL,	NULL,	NULL),
(14,	15,	'Historie der Branchenfreischaltung',	NULL,	40,	'',	1,	NULL,	NULL,	NULL),
(15,	15,	'Aufkommende Ereignisse',	NULL,	50,	'',	1,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(30) COLLATE utf8mb4_german2_ci NOT NULL,
  `nummer` int(11) NOT NULL,
  `schluessel` varchar(10) COLLATE utf8mb4_german2_ci NOT NULL,
  `sichtbar` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

INSERT INTO `menus` (`id`, `titel`, `nummer`, `schluessel`, `sichtbar`, `created_at`, `updated_at`, `deleted_at`) VALUES
(13,	'Branchen anrufen',	2,	'b',	1,	NULL,	NULL,	NULL),
(14,	'Branchen verwalten',	3,	'c',	1,	NULL,	NULL,	NULL),
(15,	'Branchen auswerten',	4,	'd',	1,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) COLLATE utf8mb4_german2_ci NOT NULL,
  `password` varchar(200) COLLATE utf8mb4_german2_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_german2_ci,
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

INSERT INTO `users` (`id`, `username`, `password`, `permissions`, `admin`, `updated_at`, `created_at`, `deleted_at`) VALUES
(1,	'admin',	'',	NULL,	1,	'2018-12-16 15:14:33',	'0000-00-00 00:00:00',	NULL),
(12,	'Stephan',	'hodor',	'a,b,c,d,e',	0,	'2018-12-16 17:57:18',	'2018-12-16 15:33:34',	'2018-12-16 17:57:18'),
(13,	'Judas',	'test',	'a,b,c,d,e',	0,	'2018-12-16 17:57:17',	'2018-12-16 15:35:49',	'2018-12-16 17:57:17'),
(14,	'Sonja',	'',	'',	0,	'2018-12-16 17:57:15',	'2018-12-16 17:57:11',	'2018-12-16 17:57:15'),
(15,	'Isaac der Hodensack',	'',	'',	0,	'2018-12-16 18:14:05',	'2018-12-16 17:57:54',	'2018-12-16 18:14:05'),
(16,	'Negerbuah',	'',	'',	0,	'2018-12-16 18:14:00',	'2018-12-16 18:00:15',	'2018-12-16 18:14:00'),
(17,	'Hans',	'',	'',	0,	'2018-12-16 18:14:00',	'2018-12-16 18:13:53',	'2018-12-16 18:14:00'),
(18,	'hodor',	'test',	'',	0,	'2018-12-17 22:05:19',	'2018-12-17 19:05:25',	'2018-12-17 22:05:19'),
(19,	'neger',	'1',	'2',	0,	'2018-12-17 22:05:18',	'2018-12-17 19:23:05',	'2018-12-17 22:05:18'),
(20,	'wameling',	'848484',	'',	1,	'2018-12-17 22:05:24',	'2018-12-17 22:05:24',	NULL);

-- 2019-02-13 21:26:31
