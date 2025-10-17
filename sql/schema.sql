-- CHWR Database Schema
-- Cobourg Homeless Warming Room Web Application

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: chwr_db

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('Client','Outreach Worker','Service Provider','Administrator') NOT NULL DEFAULT 'Client',
  `security_question` varchar(255) NOT NULL,
  `security_answer_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `profile_avatar` varchar(255) DEFAULT NULL,
  `theme_preference` varchar(20) DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remember_me_tokens`
--

CREATE TABLE IF NOT EXISTS `remember_me_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token_id`),
  KEY `idx_token` (`token`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_data` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_last_activity` (`last_activity`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE IF NOT EXISTS `assessments` (
  `assessment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` enum('Not Started','In Progress','Completed','Reviewed') NOT NULL DEFAULT 'Not Started',
  `overall_score` decimal(5,2) DEFAULT NULL,
  `traffic_light` enum('Green','Yellow','Red') DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assessment_id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_sections`
--

CREATE TABLE IF NOT EXISTS `assessment_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `section_code` char(1) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `section_score` decimal(5,2) DEFAULT NULL,
  `section_status` enum('Not Started','In Progress','Completed') NOT NULL DEFAULT 'Not Started',
  `traffic_light` enum('Green','Yellow','Red') DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`section_id`),
  KEY `idx_assessment_id` (`assessment_id`),
  KEY `idx_section_code` (`section_code`),
  FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`assessment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_responses`
--

CREATE TABLE IF NOT EXISTS `assessment_responses` (
  `response_id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `section_code` char(1) NOT NULL,
  `question_id` varchar(50) NOT NULL,
  `question_text` text NOT NULL,
  `response_value` text,
  `response_type` enum('multiple_choice','multi_select','text','number','date') NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `flag_level` enum('None','Low','Medium','High','Critical') DEFAULT 'None',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`response_id`),
  KEY `idx_assessment_id` (`assessment_id`),
  KEY `idx_section_code` (`section_code`),
  KEY `idx_question_id` (`question_id`),
  FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`assessment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `category` varchar(50) DEFAULT NULL,
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `status` enum('Pending','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `due_date` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_assigned_by` (`assigned_by`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `thread_id` varchar(64) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message_body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `idx_sender_id` (`sender_id`),
  KEY `idx_recipient_id` (`recipient_id`),
  KEY `idx_thread_id` (`thread_id`),
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipient_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `appointment_date` timestamp NOT NULL,
  `duration_minutes` int(11) DEFAULT 30,
  `status` enum('Scheduled','Confirmed','Completed','Cancelled','No Show') NOT NULL DEFAULT 'Scheduled',
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`appointment_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_staff_id` (`staff_id`),
  KEY `idx_appointment_date` (`appointment_date`),
  FOREIGN KEY (`client_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`staff_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE IF NOT EXISTS `referrals` (
  `referral_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `referred_by` int(11) NOT NULL,
  `service_provider_id` int(11) DEFAULT NULL,
  `service_type` varchar(100) NOT NULL,
  `description` text,
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `status` enum('Pending','Accepted','Declined','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `outcome` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`referral_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_referred_by` (`referred_by`),
  KEY `idx_service_provider_id` (`service_provider_id`),
  FOREIGN KEY (`client_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`referred_by`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_provider_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_notes`
--

CREATE TABLE IF NOT EXISTS `case_notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `note_content` text NOT NULL,
  `is_sensitive` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `attachments` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`note_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_author_id` (`author_id`),
  FOREIGN KEY (`client_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consent_documents`
--

CREATE TABLE IF NOT EXISTS `consent_documents` (
  `consent_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_content` text NOT NULL,
  `is_signed` tinyint(1) NOT NULL DEFAULT 0,
  `signed_at` timestamp NULL DEFAULT NULL,
  `signature_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consent_id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wellness_checkins`
--

CREATE TABLE IF NOT EXISTS `wellness_checkins` (
  `checkin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mood_rating` int(1) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`checkin_id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
