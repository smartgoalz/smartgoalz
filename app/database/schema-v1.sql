CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `due_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `difficulty` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `is_completed` int(1) NOT NULL,
  `completion_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `task_total` int(11) NOT NULL,
  `task_completed` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `journals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entry` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `id_3` (`id`),
  KEY `journals_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `monitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `minimum` varchar(15) NOT NULL,
  `maximum` varchar(15) NOT NULL,
  `minimum_threshold` varchar(15) NOT NULL,
  `maximum_threshold` varchar(15) NOT NULL,
  `is_lower_better` int(1) NOT NULL,
  `type` enum('CHAR','INT','FLOAT','BOOL') NOT NULL,
  `units` varchar(255) NOT NULL,
  `frequency` enum('DAILY','WEEKLY','MONTHLY','QUATERLY','YEARLY') NOT NULL DEFAULT 'DAILY',
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `monitorvalues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monitor_id` int(11) NOT NULL,
  `value` varchar(15) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `monitor_id` (`monitor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL,
  `pin_dashboard` int(1) NOT NULL,
  `pin_top` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `due_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_completed` int(1) NOT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `timewatch_count` int(11) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `timetables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `days` set('SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `timewatches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stop_time` timestamp NULL DEFAULT NULL,
  `is_active` int(1) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `minutes_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateformat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_verified` tinyint(1) NOT NULL,
  `email_verified` tinyint(1) NOT NULL,
  `email_token` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `status` int(11) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_password_key` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_password_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL,
  `database_version` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `activities`
  ADD CONSTRAINT `fk_activities_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `goals`
  ADD CONSTRAINT `fk_goals_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_goals_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `journals`
  ADD CONSTRAINT `journals_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `monitors`
  ADD CONSTRAINT `fk_monitors_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `monitorvalues`
  ADD CONSTRAINT `fk_monitorvalues_monitor_id` FOREIGN KEY (`monitor_id`) REFERENCES `monitors` (`id`);

ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_goal_id` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`);

ALTER TABLE `timetables`
  ADD CONSTRAINT `fk_timetables_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`);

ALTER TABLE `timewatches`
  ADD CONSTRAINT `fk_timewatches_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `fk_timewatches_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
