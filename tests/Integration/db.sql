-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `einexttest`;
CREATE DATABASE `einexttest` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci */;
USE `einexttest`;

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


DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationId` int(10) unsigned DEFAULT NULL,
  `eventId` int(10) unsigned NOT NULL DEFAULT '0',
  `createdUserId` int(11) unsigned NOT NULL,
  `finishedUserId` int(11) unsigned DEFAULT NULL,
  `preAppointmentId` int(11) unsigned DEFAULT NULL,
  `nextAppointmentId` int(11) unsigned DEFAULT NULL,
  `result` tinyint(3) unsigned DEFAULT NULL,
  `when` datetime NOT NULL,
  `erinnernAm` datetime DEFAULT NULL,
  `nachgehenAm` datetime DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `finished_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `verkauftAm` datetime DEFAULT NULL,
  `verkauftVon` int(10) unsigned DEFAULT NULL,
  `seller` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_anrede` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_vorname` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_nachname` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `preisinfo` text COLLATE utf8mb4_german2_ci,
  `assignedUserId` int(11) unsigned DEFAULT NULL,
  `typ` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `eventId` (`eventId`),
  KEY `createdUserId` (`createdUserId`),
  KEY `finishedUserId` (`finishedUserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `bundeslaender`;
CREATE TABLE `bundeslaender` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_german2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
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
  `bundeslandid` int(10) unsigned DEFAULT NULL,
  `phoneNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobilePhoneNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homepage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agentFromId` int(10) unsigned DEFAULT NULL,
  `agentLastChangeId` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `revision` int(11) DEFAULT NULL,
  `locationCategoryId` int(10) unsigned DEFAULT NULL,
  `markierung` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mondayId` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `werbeaktion` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manuellErstellt` tinyint(4) NOT NULL DEFAULT '0',
  `said_whatsapp` tinyint(4) DEFAULT NULL,
  `ist_alte_homepage` tinyint(4) NOT NULL DEFAULT '0',
  `homepage_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci,
  `customerstate` tinyint(4) NOT NULL DEFAULT '0',
  `canlogin` tinyint(4) NOT NULL DEFAULT '1',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `registerlink` text CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci,
  `domain` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ftphost` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ftpusername` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ftppassword` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ftpdirectoryhtml` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_anrede` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_vorname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_nachname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `wiederkontaktAm` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SCHOOLSCHOOL_AFID` (`agentFromId`),
  KEY `SCHOOLSCHOOL_ALCID` (`agentLastChangeId`),
  KEY `school_s_i_c_d` (`id`,`deleted_at`),
  KEY `CL_LCID` (`locationCategoryId`),
  KEY `CAM_L_DAT` (`deleted_at`),
  KEY `CAM_L_DATID` (`deleted_at`,`id`),
  KEY `CL_PFED` (`phoneNumber`(191),`fax`(191),`email`(191),`deleted_at`),
  KEY `CAM_L_TITLE` (`deleted_at`,`title`(191)),
  KEY `state` (`bundeslandid`),
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
  `showAfter` datetime DEFAULT NULL,
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
  `wiedervorlage` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `shownAt` timestamp NULL DEFAULT NULL,
  `ansprechpartner` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erlaubnis_anrufen` tinyint(4) DEFAULT NULL,
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


DROP TABLE IF EXISTS `counter_tasks`;
CREATE TABLE `counter_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `counter_task_events`;
CREATE TABLE `counter_task_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `counterTaskId` int(11) unsigned NOT NULL,
  `mainTaskId` int(11) unsigned DEFAULT NULL,
  `locationEventAppointmentId` int(11) unsigned DEFAULT NULL,
  `doneBy` int(11) unsigned DEFAULT NULL,
  `done` tinyint(4) NOT NULL DEFAULT '0',
  `finishedAt` datetime DEFAULT NULL,
  `dueAt` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `customer_infodata`;
CREATE TABLE `customer_infodata` (
  `customerid` int(10) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `value` text COLLATE utf8mb4_german2_ci,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customerid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `customer_tokens`;
CREATE TABLE `customer_tokens` (
  `customerid` int(10) unsigned NOT NULL,
  `token` varchar(512) COLLATE utf8mb4_german2_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_block`;
CREATE TABLE `duty_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_german2_ci NOT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_follow_ups`;
CREATE TABLE `duty_follow_ups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dutyRowTemplId` int(11) NOT NULL,
  `dutyTaskTemplId` int(11) NOT NULL,
  `followUpDutyTaskTemplId` int(11) DEFAULT NULL,
  `followUpDutyRowTemplId` int(11) DEFAULT NULL,
  `interactionMsg` varchar(80) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `followUpInteractionTypeId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_duty_follow_ups_duty_row_templ_id` (`dutyRowTemplId`),
  KEY `fk_duty_follow_ups_duty_task_templ_id` (`dutyTaskTemplId`),
  KEY `fk_duty_follow_ups_follow_up_duty_row_templ_id` (`followUpDutyRowTemplId`),
  KEY `fk_duty_follow_ups_follow_up_duty_task_templ_id` (`followUpDutyTaskTemplId`),
  KEY `fk_duty_follow_ups_duty_interaction_type_id` (`followUpInteractionTypeId`),
  CONSTRAINT `fk_duty_follow_ups_duty_interaction_type_id` FOREIGN KEY (`followUpInteractionTypeId`) REFERENCES `duty_follow_up_interaction_types` (`id`),
  CONSTRAINT `fk_duty_follow_ups_duty_row_templ_id` FOREIGN KEY (`dutyRowTemplId`) REFERENCES `duty_row_templates` (`id`),
  CONSTRAINT `fk_duty_follow_ups_duty_task_templ_id` FOREIGN KEY (`dutyTaskTemplId`) REFERENCES `duty_task_templates` (`id`),
  CONSTRAINT `fk_duty_follow_ups_follow_up_duty_row_templ_id` FOREIGN KEY (`followUpDutyRowTemplId`) REFERENCES `duty_row_templates` (`id`),
  CONSTRAINT `fk_duty_follow_ups_follow_up_duty_task_templ_id` FOREIGN KEY (`followUpDutyTaskTemplId`) REFERENCES `duty_task_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_follow_up_interaction_types`;
CREATE TABLE `duty_follow_up_interaction_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_german2_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_rows`;
CREATE TABLE `duty_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dutyRowId` int(11) NOT NULL,
  `appointmentId` int(10) unsigned DEFAULT NULL,
  `eventAppointmentId` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_duty_rows_location_appointments` (`appointmentId`),
  KEY `fk_duty_rows_location_event_appointments` (`eventAppointmentId`),
  KEY `fk_duty_rows_row` (`dutyRowId`),
  CONSTRAINT `fk_duty_rows_location_appointments` FOREIGN KEY (`appointmentId`) REFERENCES `x_location_appointments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_duty_rows_location_event_appointments` FOREIGN KEY (`eventAppointmentId`) REFERENCES `x_location_event_appointments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_duty_rows_row` FOREIGN KEY (`dutyRowId`) REFERENCES `duty_row_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_rows_tasks`;
CREATE TABLE `duty_rows_tasks` (
  `dutyRowId` int(11) NOT NULL,
  `dutyTaskId` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `fk_duty_rows_tasks_duty_row_id` (`dutyRowId`),
  KEY `fk_duty_rows_tasks_duty_task_id` (`dutyTaskId`),
  CONSTRAINT `fk_duty_rows_tasks_duty_row_id` FOREIGN KEY (`dutyRowId`) REFERENCES `duty_rows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_duty_rows_tasks_duty_task_id` FOREIGN KEY (`dutyTaskId`) REFERENCES `duty_task_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_rows_templ_tasks_templ`;
CREATE TABLE `duty_rows_templ_tasks_templ` (
  `dutyTaskId` int(11) NOT NULL,
  `dutyRowId` int(11) NOT NULL,
  `proceeding_task` int(11) DEFAULT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dutyTaskId`,`dutyRowId`),
  KEY `fk_rows_tasks_row_id` (`dutyRowId`),
  KEY `fk_rows_tasks_proceeding_task_id` (`proceeding_task`),
  CONSTRAINT `fk_rows_tasks_duty_id` FOREIGN KEY (`dutyTaskId`) REFERENCES `duty_task_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rows_tasks_proceeding_task_id` FOREIGN KEY (`proceeding_task`) REFERENCES `duty_task_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rows_tasks_row_id` FOREIGN KEY (`dutyRowId`) REFERENCES `duty_row_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_rows_templ_trigger`;
CREATE TABLE `duty_rows_templ_trigger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dutyRowTemplId` int(11) NOT NULL,
  `dutyTriggerId` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dutyRowTemplId` (`dutyRowTemplId`,`dutyTriggerId`),
  KEY `fk_rows_templ_triggers_duty_trigger_id` (`dutyTriggerId`),
  CONSTRAINT `fk_rows_templ_triggers_duty_row_templ_id` FOREIGN KEY (`dutyRowTemplId`) REFERENCES `duty_row_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rows_templ_triggers_duty_trigger_id` FOREIGN KEY (`dutyTriggerId`) REFERENCES `duty_triggers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_row_templates`;
CREATE TABLE `duty_row_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dutyBlockId` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_german2_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_duty_row_duty_block_id` (`dutyBlockId`),
  CONSTRAINT `fk_duty_row_duty_block_id` FOREIGN KEY (`dutyBlockId`) REFERENCES `duty_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `duty_task_templates`;
CREATE TABLE `duty_task_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `duty_triggers`;
CREATE TABLE `duty_triggers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8mb4_german2_ci NOT NULL,
  `initial` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `generic_tasks`;
CREATE TABLE `generic_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(11) unsigned DEFAULT NULL,
  `title` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `businessType` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `finishedAt` datetime DEFAULT NULL,
  `finishedBy` int(11) unsigned DEFAULT NULL,
  `done` tinyint(4) DEFAULT '0',
  `hasSubTasks` tinyint(4) DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `location_emails`;
CREATE TABLE `location_emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationid` int(10) unsigned NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `typ` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `location_event_notes`;
CREATE TABLE `location_event_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `eventId` int(10) unsigned NOT NULL,
  `note` text COLLATE utf8mb4_german2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `eventId` (`eventId`),
  CONSTRAINT `location_event_notes_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  CONSTRAINT `location_event_notes_ibfk_2` FOREIGN KEY (`eventId`) REFERENCES `campaign_location_events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `location_event_tracks`;
CREATE TABLE `location_event_tracks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(10) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `trackedAt` datetime DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_german2_ci NOT NULL,
  `result` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `notice` text COLLATE utf8mb4_german2_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `appointmentAt` datetime DEFAULT NULL,
  `showAgainAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eventId` (`eventId`),
  KEY `userId` (`userId`),
  CONSTRAINT `location_event_tracks_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `campaign_location_events` (`id`),
  CONSTRAINT `location_event_tracks_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `location_notes`;
CREATE TABLE `location_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationId` int(10) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `when` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `note` text COLLATE utf8mb4_german2_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `locationId` (`locationId`),
  KEY `userId` (`userId`),
  CONSTRAINT `location_notes_ibfk_1` FOREIGN KEY (`locationId`) REFERENCES `campaign_locations` (`id`),
  CONSTRAINT `location_notes_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `location_phonenumbers`;
CREATE TABLE `location_phonenumbers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationid` int(10) unsigned NOT NULL,
  `phonenumber` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `menuitems`;
CREATE TABLE `menuitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuid` int(11) DEFAULT NULL,
  `titel` varchar(60) COLLATE utf8mb4_german2_ci NOT NULL,
  `routerlink` varchar(100) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `nummer` int(11) NOT NULL,
  `schluessel` varchar(10) COLLATE utf8mb4_german2_ci NOT NULL,
  `sichtbar` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


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


DROP TABLE IF EXISTS `monitors`;
CREATE TABLE `monitors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(4) NOT NULL DEFAULT '-1',
  `message` text COLLATE utf8mb4_german2_ci,
  `realm` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `referenceid` int(10) unsigned NOT NULL,
  `lastupdate` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `realm` (`realm`),
  KEY `referenceid` (`referenceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `monitor_details`;
CREATE TABLE `monitor_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `monitorid` int(10) unsigned NOT NULL,
  `state` tinyint(4) NOT NULL,
  `message` text COLLATE utf8mb4_german2_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `monitorid` (`monitorid`),
  CONSTRAINT `monitor_details_ibfk_1` FOREIGN KEY (`monitorid`) REFERENCES `monitors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `possible_sales_amount`;
CREATE TABLE `possible_sales_amount` (
  `hour` tinyint(4) NOT NULL,
  `minute` tinyint(4) NOT NULL,
  `weekday` tinyint(4) NOT NULL,
  `monday` date DEFAULT NULL,
  `default` tinyint(4) NOT NULL,
  `amount` tinyint(4) NOT NULL,
  KEY `hour` (`hour`),
  KEY `minute` (`minute`),
  KEY `weekday` (`weekday`),
  KEY `monday` (`monday`),
  KEY `default` (`default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `unternehmensimport`;
CREATE TABLE `unternehmensimport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `name2` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `bundesland` varchar(30) COLLATE utf8mb4_german2_ci NOT NULL,
  `land` varchar(50) COLLATE utf8mb4_german2_ci NOT NULL,
  `branche` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `strasse` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `plz` varchar(10) COLLATE utf8mb4_german2_ci NOT NULL,
  `ort` varchar(100) COLLATE utf8mb4_german2_ci NOT NULL,
  `oeffnungszeiten` varchar(200) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `webseite` varchar(100) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `telefonnummer` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `inhaber` varchar(100) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `hash` varchar(128) COLLATE utf8mb4_german2_ci NOT NULL,
  `hashversion` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `erzeuger` varchar(30) COLLATE utf8mb4_german2_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `locationId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `hashversion` (`hashversion`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


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
  `last_action_at` datetime DEFAULT NULL,
  `narev_id` int(11) DEFAULT NULL,
  `narev_token` varchar(384) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `user_absences`;
CREATE TABLE `user_absences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `from` date NOT NULL,
  `to` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `am` tinyint(4) NOT NULL DEFAULT '0',
  `pm` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `user_absences_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `x_location_appointments`;
CREATE TABLE `x_location_appointments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `createdUserId` int(11) unsigned NOT NULL,
  `finishedUserId` int(11) unsigned DEFAULT NULL,
  `locationId` int(11) unsigned NOT NULL,
  `preAppointmentId` int(11) unsigned DEFAULT NULL,
  `nextAppointmentId` int(11) unsigned DEFAULT NULL,
  `result` tinyint(3) unsigned DEFAULT NULL,
  `when` datetime NOT NULL,
  `erinnernAm` datetime DEFAULT NULL,
  `nachgehenAm` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `seller` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_anrede` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_vorname` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_nachname` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `assignedUserId` int(11) unsigned DEFAULT NULL,
  `typ` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdUserId` (`createdUserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


DROP TABLE IF EXISTS `x_location_event_appointments`;
CREATE TABLE `x_location_event_appointments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(10) unsigned NOT NULL,
  `createdUserId` int(11) unsigned NOT NULL,
  `finishedUserId` int(11) unsigned DEFAULT NULL,
  `preAppointmentId` int(11) unsigned DEFAULT NULL,
  `nextAppointmentId` int(11) unsigned DEFAULT NULL,
  `result` tinyint(3) unsigned DEFAULT NULL,
  `when` datetime NOT NULL,
  `erinnernAm` datetime DEFAULT NULL,
  `nachgehenAm` datetime DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `finished_at` timestamp NULL DEFAULT NULL,
  `seller` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_anrede` varchar(30) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_vorname` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `ansprechpartner_nachname` varchar(50) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `preisinfo` text COLLATE utf8mb4_german2_ci,
  `assignedUserId` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eventId` (`eventId`),
  KEY `createdUserId` (`createdUserId`),
  KEY `finishedUserId` (`finishedUserId`),
  CONSTRAINT `x_location_event_appointments_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `campaign_location_events` (`id`),
  CONSTRAINT `x_location_event_appointments_ibfk_2` FOREIGN KEY (`createdUserId`) REFERENCES `users` (`id`),
  CONSTRAINT `x_location_event_appointments_ibfk_3` FOREIGN KEY (`finishedUserId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;


-- 2019-11-23 09:19:28