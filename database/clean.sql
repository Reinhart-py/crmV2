-- Sales CRM: clean starting database (structure + reference data only).
-- Made by database/make_clean_sql.sh. Load into an empty database, then run: php migration_runner.php
SET NAMES utf8mb4;
SET SESSION innodb_strict_mode = 0;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `activity` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  `location_status` varchar(50) DEFAULT 'unavailable',
  `location_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_ai_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'Draft',
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `voice` varchar(20) NOT NULL DEFAULT 'female',
  `voice_id` varchar(100) NOT NULL DEFAULT '',
  `disclose_ai` tinyint(1) NOT NULL DEFAULT 1,
  `goal` varchar(20) NOT NULL DEFAULT 'book_meeting',
  `pitch` text DEFAULT NULL,
  `opening` text DEFAULT NULL,
  `questions` text DEFAULT NULL,
  `objections` text DEFAULT NULL,
  `closing` text DEFAULT NULL,
  `transfer_number` varchar(30) NOT NULL DEFAULT '',
  `dnd_on_request` tinyint(1) NOT NULL DEFAULT 1,
  `max_minutes` int(11) NOT NULL DEFAULT 5,
  `call_from` char(5) NOT NULL DEFAULT '09:00',
  `call_to` char(5) NOT NULL DEFAULT '18:00',
  `call_days` varchar(20) NOT NULL DEFAULT '1,2,3,4,5',
  `daily_limit` int(11) NOT NULL DEFAULT 100,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_ai_calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `lead_id` int(11) NOT NULL DEFAULT 0,
  `owner_id` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(30) NOT NULL,
  `contact` varchar(150) NOT NULL DEFAULT '',
  `customer` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT 'Queued',
  `outcome` varchar(30) NOT NULL DEFAULT '',
  `provider` varchar(10) NOT NULL DEFAULT '',
  `provider_call_id` varchar(100) NOT NULL DEFAULT '',
  `duration` int(11) NOT NULL DEFAULT 0,
  `summary` text DEFAULT NULL,
  `transcript` mediumtext DEFAULT NULL,
  `sentiment` varchar(20) NOT NULL DEFAULT '',
  `next_action` varchar(255) NOT NULL DEFAULT '',
  `followup_date` date DEFAULT NULL,
  `recording_url` varchar(500) NOT NULL DEFAULT '',
  `error` varchar(255) NOT NULL DEFAULT '',
  `is_test` tinyint(1) NOT NULL DEFAULT 0,
  `handled_by` int(11) NOT NULL DEFAULT 0,
  `handled_at` datetime DEFAULT NULL,
  `queued_by` int(11) NOT NULL DEFAULT 0,
  `queued_at` datetime NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ai_status` (`status`),
  KEY `idx_ai_agent` (`agent_id`),
  KEY `idx_ai_campaign_lead` (`campaign_id`,`lead_id`),
  KEY `idx_ai_owner` (`owner_id`),
  KEY `idx_ai_provider_call` (`provider_call_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `in_time` time DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `accuracy` decimal(8,2) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  `clock_in_ip` varchar(50) DEFAULT NULL,
  `clock_out_ip` varchar(50) DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `total_hours` decimal(4,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'Present',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_att_date` (`user_id`,`attendance_date`),
  KEY `idx_att_date` (`attendance_date`),
  KEY `idx_att_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `award_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `reward_points` int(11) DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_boleads` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `lead_id` int(255) NOT NULL,
  `bo_cat` text DEFAULT NULL,
  `bo_date` varchar(255) DEFAULT NULL,
  `bo_status` varchar(255) DEFAULT NULL,
  `bo_ref` text DEFAULT NULL,
  `bo_remark` text DEFAULT NULL,
  `status` int(255) DEFAULT NULL,
  `created` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_call_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `call_id` varchar(100) DEFAULT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT '',
  `phone_number` varchar(50) NOT NULL,
  `recording_url` text DEFAULT NULL,
  `recording_file` varchar(255) DEFAULT NULL,
  `call_type` varchar(50) DEFAULT 'Outbound 3CX',
  `call_status` varchar(50) DEFAULT 'Initiated',
  `duration` int(11) DEFAULT 0,
  `disposition` varchar(100) DEFAULT '',
  `notes` text DEFAULT NULL,
  `transcript` longtext DEFAULT NULL,
  `ai_summary` text DEFAULT NULL,
  `ai_remarks` text DEFAULT NULL,
  `ai_status` varchar(50) DEFAULT NULL,
  `ai_sentiment` varchar(50) DEFAULT NULL,
  `ai_follow_up_date` date DEFAULT NULL,
  `ai_next_action` text DEFAULT NULL,
  `ai_language` varchar(50) DEFAULT NULL,
  `ai_requirements` text DEFAULT NULL,
  `ai_objections` text DEFAULT NULL,
  `ai_raw_json` longtext DEFAULT NULL,
  `ai_processing_status` varchar(50) DEFAULT 'pending',
  `ai_error_message` text DEFAULT NULL,
  `ai_processed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `is_internal` tinyint(1) DEFAULT 0,
  `call_category` varchar(50) DEFAULT 'new_call',
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_lead` (`lead_id`),
  KEY `idx_call_campaign` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_dialer_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_no` varchar(20) DEFAULT NULL,
  `objective` varchar(30) NOT NULL DEFAULT 'lead_generation',
  `priority` varchar(10) NOT NULL DEFAULT 'Normal',
  `country` int(11) NOT NULL DEFAULT 0,
  `state` int(11) NOT NULL DEFAULT 0,
  `city` varchar(150) DEFAULT NULL,
  `industry_category` varchar(150) DEFAULT NULL,
  `database_category` varchar(150) DEFAULT NULL,
  `data_source` varchar(150) DEFAULT NULL,
  `pipeline_type` varchar(20) NOT NULL DEFAULT 'database',
  `status_ids` varchar(255) DEFAULT NULL,
  `participants` text DEFAULT NULL,
  `daily_target` int(11) NOT NULL DEFAULT 0,
  `goal_type` varchar(20) NOT NULL DEFAULT '',
  `goal_target` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 3,
  `retry_hours` int(11) NOT NULL DEFAULT 24,
  `script` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `ai_agent_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_status` (`status`),
  KEY `idx_campaign_creator` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_dialer_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `queue_key` varchar(191) NOT NULL,
  `last_position` int(11) NOT NULL DEFAULT 0,
  `last_lead_id` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_queue` (`user_id`,`queue_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_dialer_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'idle',
  `current_lead_id` int(11) DEFAULT NULL,
  `current_call_log_id` int(11) DEFAULT NULL,
  `cooldown_seconds` int(11) DEFAULT 60,
  `wrapup_started_at` datetime DEFAULT NULL,
  `wrapup_ends_at` datetime DEFAULT NULL,
  `dialed_lead_ids` text DEFAULT NULL,
  `status_filter` varchar(50) DEFAULT 'all',
  `total_dialed` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` varchar(50) DEFAULT 'meeting',
  `visibility` varchar(20) NOT NULL DEFAULT 'public',
  `assigned_user_id` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT 0,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_no` varchar(50) NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_to` varchar(255) NOT NULL,
  `payment_mode` varchar(50) NOT NULL DEFAULT 'Bank Transfer',
  `approved_by` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_exp_date` (`expense_date`),
  KEY `idx_exp_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_followup` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `lead_id` int(255) NOT NULL,
  `lead_type` varchar(500) NOT NULL,
  `followup_date` varchar(500) NOT NULL,
  `followup_time` varchar(500) NOT NULL,
  `followup_remark` text NOT NULL,
  `followup_result` varchar(500) NOT NULL,
  `user_id` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fu_lead_id` (`lead_id`),
  KEY `idx_fu_user_id` (`user_id`),
  KEY `idx_fu_date` (`followup_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_appraisals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `cycle_name` varchar(100) NOT NULL,
  `rating` decimal(3,1) DEFAULT 0.0,
  `kpi_score` decimal(5,2) DEFAULT 0.00,
  `strengths` text DEFAULT NULL,
  `improvements` text DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Completed',
  `created_at` datetime DEFAULT current_timestamp(),
  `cycle_id` int(11) DEFAULT NULL,
  `stage` varchar(20) DEFAULT NULL,
  `self_text` text DEFAULT NULL,
  `self_rating` decimal(3,1) DEFAULT NULL,
  `scores` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `self_at` datetime DEFAULT NULL,
  `manager_at` datetime DEFAULT NULL,
  `ack_at` datetime DEFAULT NULL,
  `ack_note` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `cycle_id` (`cycle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_checklist_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `owner` varchar(12) NOT NULL DEFAULT 'HR',
  `due_date` date DEFAULT NULL,
  `done_by` int(11) DEFAULT NULL,
  `done_at` datetime DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `checklist_id` (`checklist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_checklist_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kind` varchar(12) NOT NULL,
  `title` varchar(150) NOT NULL,
  `owner` varchar(12) NOT NULL DEFAULT 'HR',
  `days_offset` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_checklists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kind` varchar(12) NOT NULL,
  `start_date` date NOT NULL,
  `status` varchar(8) NOT NULL DEFAULT 'Open',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `done_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `doc_number` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Valid',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `doc_type` (`doc_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_employee` (
  `user_id` int(11) NOT NULL,
  `grade` varchar(60) NOT NULL DEFAULT '',
  `contract_start` date DEFAULT NULL,
  `contract_end` date DEFAULT NULL,
  `notice_days` int(11) DEFAULT NULL,
  `annual_leave_days` int(11) DEFAULT NULL,
  `labour_card_no` varchar(60) NOT NULL DEFAULT '',
  `labour_card_expiry` date DEFAULT NULL,
  `mol_person_id` varchar(30) NOT NULL DEFAULT '',
  `insurance_provider` varchar(120) NOT NULL DEFAULT '',
  `insurance_no` varchar(60) NOT NULL DEFAULT '',
  `insurance_expiry` date DEFAULT NULL,
  `payment_method` varchar(40) NOT NULL DEFAULT '',
  `wps_routing_code` varchar(20) NOT NULL DEFAULT '',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_leave_adjust` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `days` decimal(5,1) NOT NULL,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_year` (`user_id`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_leave_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `days_per_year` decimal(5,1) NOT NULL DEFAULT 0.0,
  `pay` varchar(20) NOT NULL DEFAULT 'full',
  `count_mode` varchar(10) NOT NULL DEFAULT 'working',
  `accrual` varchar(20) NOT NULL DEFAULT '',
  `needs_document` tinyint(1) NOT NULL DEFAULT 0,
  `gender` varchar(10) NOT NULL DEFAULT 'any',
  `min_service_months` int(11) NOT NULL DEFAULT 0,
  `carry_max` decimal(5,1) NOT NULL DEFAULT 0.0,
  `note` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `addressed_to` varchar(150) NOT NULL DEFAULT '',
  `purpose` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT 'Requested',
  `ref_no` varchar(30) NOT NULL DEFAULT '',
  `body` text DEFAULT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `requested_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_lifecycle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `stage` varchar(50) DEFAULT 'Confirmed',
  `department` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `probation_end_date` date DEFAULT NULL,
  `confirmation_date` date DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT 'Permanent',
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`),
  KEY `stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_loan_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `run_id` int(11) DEFAULT NULL,
  `settlement_id` int(11) DEFAULT NULL,
  `month` char(7) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_id` (`loan_id`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kind` varchar(20) NOT NULL DEFAULT 'Loan',
  `amount` decimal(12,2) NOT NULL,
  `monthly` decimal(12,2) NOT NULL,
  `start_month` char(7) NOT NULL,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT 'Active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `closed_by` int(11) DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `close_note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_pay_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `month` char(7) NOT NULL,
  `kind` varchar(12) NOT NULL,
  `label` varchar(100) NOT NULL DEFAULT '',
  `hours` decimal(6,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `month_user` (`month`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_pay_runs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` char(7) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'Draft',
  `people` int(11) NOT NULL DEFAULT 0,
  `gross` decimal(14,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(14,2) NOT NULL DEFAULT 0.00,
  `net` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `paid_by` int(11) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `month` (`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_payroll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `month_year` varchar(20) NOT NULL,
  `basic_salary` decimal(10,2) DEFAULT 0.00,
  `housing_allowance` decimal(10,2) DEFAULT 0.00,
  `transport_allowance` decimal(10,2) DEFAULT 0.00,
  `other_allowance` decimal(10,2) DEFAULT 0.00,
  `gross_salary` decimal(10,2) DEFAULT 0.00,
  `tax_deduction` decimal(10,2) DEFAULT 0.00,
  `other_deductions` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) DEFAULT 0.00,
  `status` varchar(30) DEFAULT 'Pending',
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'Bank Transfer',
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `month_year` (`month_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_payslips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `run_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `info` text DEFAULT NULL,
  `earnings` text DEFAULT NULL,
  `deductions` text DEFAULT NULL,
  `days_in_month` int(11) NOT NULL DEFAULT 30,
  `days_paid` decimal(5,1) NOT NULL DEFAULT 0.0,
  `leave_days` decimal(5,1) NOT NULL DEFAULT 0.0,
  `fixed` decimal(12,2) NOT NULL DEFAULT 0.00,
  `variable` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `run_user` (`run_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_review_cycles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `period_from` date DEFAULT NULL,
  `period_to` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'Open',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `basic` decimal(12,2) NOT NULL DEFAULT 0.00,
  `housing` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transport` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other` decimal(12,2) NOT NULL DEFAULT 0.00,
  `extras` text DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reason` varchar(200) NOT NULL DEFAULT '',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_from` (`user_id`,`effective_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_settings` (
  `k` varchar(60) NOT NULL,
  `v` varchar(500) NOT NULL DEFAULT '',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_settlements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `last_day` date NOT NULL,
  `reason` varchar(40) NOT NULL DEFAULT '',
  `basic` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service_days` int(11) NOT NULL DEFAULT 0,
  `unpaid_days` decimal(6,1) NOT NULL DEFAULT 0.0,
  `gratuity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `leave_days` decimal(5,1) NOT NULL DEFAULT 0.0,
  `leave_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `salary_days` decimal(5,1) NOT NULL DEFAULT 0.0,
  `salary_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notice_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `loans` decimal(12,2) NOT NULL DEFAULT 0.00,
  `extra_lines` text DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `note` varchar(500) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT 'Draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_shift_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `shift_id` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `grace_period_mins` int(11) DEFAULT 15,
  `working_hours` decimal(4,2) DEFAULT 8.00,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `target_type` varchar(50) DEFAULT 'monthly',
  `period_month` varchar(20) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `target_amount` decimal(12,2) DEFAULT 0.00,
  `target_calls` int(11) DEFAULT 0,
  `target_leads` int(11) DEFAULT 0,
  `target_meetings` int(11) DEFAULT 0,
  `achieved_amount` decimal(12,2) DEFAULT 0.00,
  `achieved_calls` int(11) DEFAULT 0,
  `achieved_leads` int(11) DEFAULT 0,
  `achieved_meetings` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Active',
  `assigned_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `period_month` (`period_month`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_hr_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `summary` text DEFAULT NULL,
  `body` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_import_batch_leads` (
  `batch_id` int(10) unsigned NOT NULL,
  `lead_id` int(11) NOT NULL,
  PRIMARY KEY (`batch_id`,`lead_id`),
  KEY `lead_id` (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_import_batches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `total_rows` int(11) NOT NULL DEFAULT 0,
  `imported` int(11) NOT NULL DEFAULT 0,
  `skipped_dups` int(11) NOT NULL DEFAULT 0,
  `errors` int(11) NOT NULL DEFAULT 0,
  `undone_at` datetime DEFAULT NULL,
  `undone_by` int(11) DEFAULT NULL,
  `undo_removed` int(11) NOT NULL DEFAULT 0,
  `undo_kept` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_integration_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `api_key` text DEFAULT NULL,
  `api_secret` text DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `config_json` longtext DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_name` (`service_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `service_desc` text DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vat_rate` decimal(5,2) NOT NULL DEFAULT 5.00,
  `vat_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'Unpaid',
  `generated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_inv_status` (`status`),
  KEY `idx_inv_date` (`invoice_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_kam_client_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `executive_id` int(11) DEFAULT NULL,
  `executive_name` varchar(255) DEFAULT NULL,
  `v_man` varchar(255) DEFAULT NULL,
  `v_exe` varchar(255) DEFAULT NULL,
  `kam_data` longtext DEFAULT NULL,
  `schedule_data` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `collaborators` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_id` (`lead_id`),
  KEY `lead_id_2` (`lead_id`),
  KEY `client_name` (`client_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_kam_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT 'Once',
  `repeat_days` varchar(100) DEFAULT NULL,
  `repeat_day_month` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` varchar(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_name` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `matrix_data` longtext DEFAULT NULL,
  `sched_year` int(11) DEFAULT 2026,
  `status` varchar(50) DEFAULT 'Scheduled',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_lead_copies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `new_lead_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL DEFAULT 0,
  `to_user_id` int(11) NOT NULL,
  `copied_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_to` (`lead_id`,`to_user_id`),
  KEY `idx_new` (`new_lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_lead_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `transferred_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `to_user_id` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_leads` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `lead_id` varchar(211) NOT NULL,
  `vertical_id` varchar(255) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `qty` varchar(50) DEFAULT NULL,
  `mrr` varchar(50) DEFAULT NULL,
  `followup_date` varchar(255) DEFAULT NULL,
  `m_date` varchar(255) DEFAULT NULL,
  `m_time` varchar(255) DEFAULT NULL,
  `close_date` varchar(255) DEFAULT NULL,
  `status` int(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `poc` varchar(255) DEFAULT NULL,
  `contact1` varchar(255) DEFAULT NULL,
  `poc2` varchar(255) DEFAULT NULL,
  `contact2` varchar(255) DEFAULT NULL,
  `c_partner` varchar(255) DEFAULT NULL,
  `p_cat` varchar(255) DEFAULT NULL,
  `lead_cat` varchar(255) DEFAULT NULL,
  `a_number` varchar(255) DEFAULT NULL,
  `a_department` varchar(255) DEFAULT NULL,
  `r_amount` varchar(255) DEFAULT NULL,
  `r_date` varchar(255) DEFAULT NULL,
  `s_cat` varchar(255) DEFAULT NULL,
  `p_details` text DEFAULT NULL,
  `mrc` varchar(255) DEFAULT NULL,
  `revenue` varchar(255) DEFAULT NULL,
  `followup_time` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` int(255) DEFAULT NULL,
  `state` int(255) DEFAULT NULL,
  `lead_assign` int(255) DEFAULT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `created_by` int(255) DEFAULT NULL,
  `admin_status` int(255) DEFAULT NULL,
  `created` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `sub_date` varchar(255) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL,
  `agents` varchar(255) DEFAULT NULL,
  `c_code` varchar(20) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `acc_mgr` varchar(255) DEFAULT NULL,
  `p_name` varchar(255) DEFAULT NULL,
  `otc` varchar(255) DEFAULT NULL,
  `contact_details` text DEFAULT NULL,
  `location` text DEFAULT NULL,
  `meetings` text DEFAULT NULL,
  `reference` text DEFAULT NULL,
  `tl_number` varchar(255) DEFAULT NULL,
  `tl_authority` varchar(255) DEFAULT NULL,
  `tl_emirate` varchar(100) DEFAULT '',
  `tl_zone` varchar(100) DEFAULT '',
  `tl_issue_date` varchar(255) DEFAULT NULL,
  `tl_expire_date` varchar(255) DEFAULT NULL,
  `v_amount` float(10,2) DEFAULT 0.00,
  `v_remark` text DEFAULT NULL,
  `followup_by` varchar(255) DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `modified` varchar(255) DEFAULT NULL,
  `alt_mobile` varchar(100) DEFAULT NULL,
  `alt_contact` varchar(100) DEFAULT NULL,
  `business_category` varchar(150) DEFAULT NULL,
  `dnd_status` varchar(20) DEFAULT 'No',
  `dnd_check_date` varchar(30) DEFAULT NULL,
  `lost_reason` text DEFAULT NULL,
  `quote_status` varchar(100) DEFAULT NULL,
  `quote_mode` varchar(100) DEFAULT NULL,
  `quote_amount` decimal(15,2) DEFAULT NULL,
  `call_status` varchar(100) DEFAULT NULL,
  `industry_category` varchar(150) DEFAULT NULL,
  `corporate_tax_status` varchar(50) DEFAULT NULL,
  `vat_status` varchar(50) DEFAULT NULL,
  `data_source` varchar(150) DEFAULT NULL,
  `data_batch_referral_no` varchar(100) DEFAULT NULL,
  `database_category` varchar(100) DEFAULT NULL,
  `status_category` varchar(255) DEFAULT NULL,
  `status_remarks` text DEFAULT NULL,
  `company_status` varchar(20) DEFAULT 'Active',
  `vat_trn` varchar(100) DEFAULT NULL,
  `ct_trn` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_leads_assign` (`lead_assign`),
  KEY `idx_leads_created_by` (`created_by`),
  KEY `idx_leads_status` (`status`),
  KEY `idx_leads_contact1` (`contact1`),
  KEY `idx_leads_email` (`email`),
  KEY `idx_leads_p_cat` (`p_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total_days` decimal(5,1) NOT NULL DEFAULT 1.0,
  `reason` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Approved',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `action_note` varchar(255) DEFAULT NULL,
  `actioned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_leave_user` (`user_id`),
  KEY `idx_leave_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_lf_batches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(100) NOT NULL,
  `sent_by` int(11) NOT NULL DEFAULT 0,
  `sent_email` varchar(190) NOT NULL DEFAULT '',
  `engine` varchar(40) NOT NULL DEFAULT '',
  `search_query` varchar(255) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL DEFAULT 0,
  `n_new` int(11) NOT NULL DEFAULT 0,
  `n_possible` int(11) NOT NULL DEFAULT 0,
  `n_dup` int(11) NOT NULL DEFAULT 0,
  `state` varchar(12) NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `finished_by` int(11) DEFAULT NULL,
  `import_batch_id` int(11) DEFAULT NULL,
  `n_added` int(11) NOT NULL DEFAULT 0,
  `n_merged` int(11) NOT NULL DEFAULT 0,
  `n_forced` int(11) NOT NULL DEFAULT 0,
  `n_skipped` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery` (`delivery_id`),
  KEY `sent_by` (`sent_by`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_lf_rows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` int(10) unsigned NOT NULL,
  `line` int(11) NOT NULL DEFAULT 0,
  `category` varchar(10) NOT NULL DEFAULT 'new',
  `score` tinyint(4) NOT NULL DEFAULT 0,
  `match_lead_id` int(11) NOT NULL DEFAULT 0,
  `match_json` text DEFAULT NULL,
  `data_json` mediumtext DEFAULT NULL,
  `decision` varchar(10) NOT NULL DEFAULT '',
  `result_lead_id` int(11) NOT NULL DEFAULT 0,
  `note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `batch` (`batch_id`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_meetings` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `lead_id` varchar(255) DEFAULT NULL,
  `a1` varchar(255) DEFAULT NULL,
  `a2` varchar(255) DEFAULT NULL,
  `a3` varchar(255) DEFAULT NULL,
  `m_date` varchar(255) DEFAULT NULL,
  `m_time` varchar(255) DEFAULT NULL,
  `m_poc` varchar(255) DEFAULT NULL,
  `m_mobile` varchar(255) DEFAULT NULL,
  `m_address` varchar(255) NOT NULL,
  `m_remark` text NOT NULL,
  `created` varchar(255) DEFAULT NULL,
  `created_by` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_mkt_connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(20) NOT NULL DEFAULT 'meta',
  `name` varchar(100) NOT NULL DEFAULT '',
  `source_name` varchar(150) NOT NULL DEFAULT '',
  `ref_month` varchar(100) NOT NULL DEFAULT '',
  `config_json` text DEFAULT NULL,
  `secret_ref` varchar(60) NOT NULL DEFAULT '',
  `webhook_key` varchar(64) NOT NULL DEFAULT '',
  `verify_token` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mkt_conn_key` (`webhook_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_mkt_leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(20) NOT NULL DEFAULT 'meta',
  `connection_id` int(11) NOT NULL DEFAULT 0,
  `external_id` varchar(64) NOT NULL DEFAULT '',
  `form_id` varchar(64) NOT NULL DEFAULT '',
  `form_name` varchar(150) NOT NULL DEFAULT '',
  `ad_id` varchar(64) NOT NULL DEFAULT '',
  `ad_name` varchar(255) NOT NULL DEFAULT '',
  `campaign_name` varchar(255) NOT NULL DEFAULT '',
  `platform` varchar(20) NOT NULL DEFAULT '',
  `full_name` varchar(150) NOT NULL DEFAULT '',
  `phone` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `fields_json` text DEFAULT NULL,
  `lead_row_id` int(11) NOT NULL DEFAULT 0,
  `assigned_to` int(11) NOT NULL DEFAULT 0,
  `status` varchar(12) NOT NULL DEFAULT 'New',
  `error` varchar(255) NOT NULL DEFAULT '',
  `received_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mkt_ext` (`source`,`external_id`),
  KEY `idx_mkt_received` (`received_at`),
  KEY `idx_mkt_conn` (`connection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_mkt_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(40) NOT NULL,
  `detail` varchar(500) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_mkt_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connection_id` int(11) NOT NULL DEFAULT 0,
  `channels` varchar(10) NOT NULL DEFAULT 'fb',
  `message` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL DEFAULT '',
  `image_ref` varchar(100) NOT NULL DEFAULT '',
  `link` varchar(500) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT 'Draft',
  `auto` tinyint(1) NOT NULL DEFAULT 0,
  `scheduled_at` datetime DEFAULT NULL,
  `posted_at` datetime DEFAULT NULL,
  `fb_post_id` varchar(80) NOT NULL DEFAULT '',
  `ig_media_id` varchar(80) NOT NULL DEFAULT '',
  `error` varchar(255) NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mkt_post_status` (`status`,`scheduled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT 'activity',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT '',
  `icon` varchar(50) DEFAULT 'fa fa-bell',
  `color` varchar(20) DEFAULT '#0072f0',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`,`is_read`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_order_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `action_by` int(11) NOT NULL,
  `action_by_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `sales_ref_no` varchar(100) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT 0,
  `lead_id` int(11) DEFAULT NULL,
  `db_ref_no` varchar(100) DEFAULT NULL,
  `lead_ref_no` varchar(100) DEFAULT NULL,
  `vertical_code` varchar(50) DEFAULT NULL,
  `service_title` varchar(255) NOT NULL,
  `service_scope` text DEFAULT NULL,
  `pricing_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mrc` decimal(12,2) DEFAULT 0.00,
  `otc` decimal(12,2) DEFAULT 0.00,
  `mrr` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'AED',
  `billing_type` varchar(50) NOT NULL DEFAULT 'One-Time',
  `documents_status` longtext DEFAULT NULL,
  `assigned_dept` varchar(100) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `operational_instructions` text DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `followup_time` varchar(20) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Draft',
  `approval_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `approval_history` longtext DEFAULT NULL,
  `salesperson_original_data` longtext DEFAULT NULL,
  `sales_converted_at` datetime DEFAULT NULL,
  `sales_converted_by` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `idx_client` (`client_id`),
  KEY `idx_lead` (`lead_id`),
  KEY `idx_status` (`status`),
  KEY `idx_assigned` (`assigned_to`),
  KEY `idx_vertical` (`vertical_code`),
  KEY `idx_ord_approval` (`approval_status`),
  KEY `idx_ord_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(50) NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_mode` varchar(50) NOT NULL DEFAULT 'Bank Transfer',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reference_no` varchar(100) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pmt_date` (`payment_date`),
  KEY `idx_pmt_mode` (`payment_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_pipeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `pipeline_stage` varchar(50) NOT NULL DEFAULT 'database',
  `vertical_code` varchar(50) NOT NULL DEFAULT 'Telecom',
  `db_ref_no` varchar(100) DEFAULT NULL,
  `lead_ref_no` varchar(100) DEFAULT NULL,
  `sales_ref_no` varchar(100) DEFAULT NULL,
  `conversion_status` varchar(50) NOT NULL DEFAULT 'none',
  `conversion_reason` text DEFAULT NULL,
  `conversion_requested_by` int(11) DEFAULT NULL,
  `conversion_requested_at` datetime DEFAULT NULL,
  `conversion_reviewed_by` int(11) DEFAULT NULL,
  `conversion_reviewed_at` datetime DEFAULT NULL,
  `bo_status` varchar(50) NOT NULL DEFAULT 'none',
  `bo_checklist` longtext DEFAULT NULL,
  `bo_remarks` text DEFAULT NULL,
  `work_order_no` varchar(100) DEFAULT NULL,
  `work_order_status` varchar(50) NOT NULL DEFAULT 'none',
  `order_request_status` varchar(50) DEFAULT 'none',
  `order_requested_by` int(11) DEFAULT NULL,
  `order_requested_at` datetime DEFAULT NULL,
  `order_request_remarks` text DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `locked_reason` varchar(255) DEFAULT NULL,
  `locked_at` datetime DEFAULT NULL,
  `work_order_data` longtext DEFAULT NULL,
  `work_order_approved_by` int(11) DEFAULT NULL,
  `work_order_approved_at` datetime DEFAULT NULL,
  `onboarded_at` datetime DEFAULT NULL,
  `onboarded_by` int(11) DEFAULT NULL,
  `vertical_data` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `salesperson_snapshot` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_lead_id` (`lead_id`),
  KEY `idx_pipeline_stage` (`pipeline_stage`),
  KEY `idx_vertical_code` (`vertical_code`),
  KEY `idx_conversion_status` (`conversion_status`),
  KEY `idx_bo_status` (`bo_status`),
  KEY `idx_pipe_order_req` (`order_request_status`),
  KEY `idx_pipe_locked` (`is_locked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_pipeline_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `stage_from` varchar(50) NOT NULL,
  `stage_to` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `action_by` int(11) NOT NULL,
  `action_by_name` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_lead_id` (`lead_id`),
  KEY `idx_action_by` (`action_by`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_qoc_checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_key` varchar(191) NOT NULL,
  `is_checked` tinyint(1) NOT NULL DEFAULT 0,
  `checked_by` int(11) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `chk_ui` tinyint(1) NOT NULL DEFAULT 0,
  `chk_ux` tinyint(1) NOT NULL DEFAULT 0,
  `chk_func` tinyint(1) NOT NULL DEFAULT 0,
  `chk_nav` tinyint(1) NOT NULL DEFAULT 0,
  `chk_data` tinyint(1) NOT NULL DEFAULT 0,
  `chk_perm` tinyint(1) NOT NULL DEFAULT 0,
  `chk_err` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `node_key` (`node_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_record_changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rtype` varchar(20) NOT NULL,
  `rid` int(11) NOT NULL,
  `action` varchar(10) NOT NULL DEFAULT 'update',
  `field` varchar(100) NOT NULL DEFAULT '',
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(150) NOT NULL DEFAULT '',
  `page` varchar(190) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rec` (`rtype`,`rid`,`id`),
  KEY `who` (`user_id`,`created_at`),
  KEY `at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_reference_sequences` (
  `sequence_type` varchar(32) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `current_val` int(10) unsigned NOT NULL DEFAULT 0,
  `padding` int(11) NOT NULL DEFAULT 10,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`sequence_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_roles` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `role_type` varchar(12) NOT NULL DEFAULT 'department',
  `level` int(255) NOT NULL,
  `parent_id` int(255) NOT NULL,
  `role` int(255) NOT NULL DEFAULT 3,
  `status` int(255) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `deletable` int(255) NOT NULL DEFAULT 1,
  `permissions` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_service_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `service_title` varchar(255) NOT NULL,
  `vertical` varchar(100) DEFAULT NULL,
  `order_date` date NOT NULL,
  `target_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `technician_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sd_status` (`status`),
  KEY `idx_sd_date` (`order_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT 0,
  `user_data` mediumtext NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sources` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) NOT NULL,
  `active` int(255) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_status` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) NOT NULL,
  `active` int(255) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_suite_commission` (
  `order_id` int(11) NOT NULL,
  `external_ref` varchar(60) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(190) NOT NULL,
  `month` char(7) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(20) NOT NULL DEFAULT 'sent',
  `hr_message` varchar(255) NOT NULL DEFAULT '',
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `approved_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_suite_inbox` (
  `delivery_id` varchar(100) NOT NULL,
  `event` varchar(60) NOT NULL DEFAULT '',
  `result` varchar(255) NOT NULL DEFAULT '',
  `received_at` datetime NOT NULL,
  PRIMARY KEY (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_suite_leave` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(190) NOT NULL DEFAULT '',
  `full_name` varchar(150) NOT NULL DEFAULT '',
  `leave_type` varchar(60) NOT NULL DEFAULT '',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days` decimal(6,1) NOT NULL DEFAULT 0.0,
  `received_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `one` (`email`,`start_date`,`end_date`,`leave_type`),
  KEY `ud` (`user_id`,`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_suite_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `at` datetime NOT NULL,
  `dir` varchar(3) NOT NULL DEFAULT 'in',
  `event` varchar(60) NOT NULL DEFAULT '',
  `ok` tinyint(1) NOT NULL DEFAULT 1,
  `summary` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `at` (`at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_suite_staff` (
  `hr_id` varchar(64) NOT NULL,
  `emp_code` varchar(40) NOT NULL DEFAULT '',
  `full_name` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(190) NOT NULL DEFAULT '',
  `phone` varchar(40) NOT NULL DEFAULT '',
  `designation` varchar(150) NOT NULL DEFAULT '',
  `department` varchar(150) NOT NULL DEFAULT '',
  `hr_status` varchar(30) NOT NULL DEFAULT '',
  `join_date` varchar(20) NOT NULL DEFAULT '',
  `looks_sales` tinyint(1) NOT NULL DEFAULT 0,
  `state` varchar(20) NOT NULL DEFAULT 'pending',
  `note` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `exit_date` varchar(20) NOT NULL DEFAULT '',
  `exit_reason` varchar(120) NOT NULL DEFAULT '',
  `open_records` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`hr_id`),
  KEY `st` (`state`),
  KEY `uid` (`user_id`),
  KEY `em` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_support_agents` (
  `user_id` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 0,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_support_chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(20) DEFAULT NULL,
  `token` char(48) NOT NULL,
  `name` varchar(120) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `issue` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'waiting',
  `agent_id` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `emailed_at` datetime DEFAULT NULL,
  `email_error` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `reopened_at` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_last_read` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `status` (`status`),
  KEY `ip_created` (`ip`,`created_at`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_support_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `sender` varchar(10) NOT NULL,
  `agent_id` int(11) NOT NULL DEFAULT 0,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_support_reads` (
  `user_id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `last_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_system_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `scheduled_date` date NOT NULL,
  `completed_date` date DEFAULT NULL,
  `duration_mins` int(11) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'Completed',
  `executed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_maint_date` (`scheduled_date`),
  KEY `idx_maint_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` varchar(50) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `poc` varchar(255) DEFAULT NULL,
  `contact1` varchar(255) DEFAULT NULL,
  `alt_mobile` varchar(100) DEFAULT NULL,
  `alt_contact` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `a_number` varchar(255) DEFAULT NULL,
  `a_department` varchar(255) DEFAULT NULL,
  `r_amount` varchar(255) DEFAULT NULL,
  `r_date` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `business_category` varchar(150) DEFAULT NULL,
  `s_cat` varchar(255) DEFAULT NULL,
  `p_cat` varchar(255) DEFAULT NULL,
  `p_details` text DEFAULT NULL,
  `tl_number` varchar(255) DEFAULT NULL,
  `tl_authority` varchar(255) DEFAULT NULL,
  `tl_issue_date` varchar(255) DEFAULT NULL,
  `tl_expire_date` varchar(255) DEFAULT NULL,
  `qty` varchar(255) DEFAULT NULL,
  `mrc` varchar(255) DEFAULT NULL,
  `revenue` varchar(255) DEFAULT NULL,
  `mrr` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Open',
  `dnd_status` varchar(10) DEFAULT 'No',
  `dnd_check_date` varchar(30) DEFAULT NULL,
  `close_date` varchar(50) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` int(11) DEFAULT 0,
  `state` int(11) DEFAULT 0,
  `city` varchar(255) DEFAULT NULL,
  `poc2` varchar(255) DEFAULT NULL,
  `contact2` varchar(255) DEFAULT NULL,
  `c_partner` varchar(255) DEFAULT NULL,
  `otc` varchar(255) DEFAULT NULL,
  `lead_cat` varchar(255) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT 0,
  `assigned_users` text DEFAULT NULL,
  `raised_by` int(11) NOT NULL DEFAULT 0,
  `lead_source` varchar(255) DEFAULT NULL,
  `followup_date` varchar(50) DEFAULT NULL,
  `followup_time` varchar(50) DEFAULT NULL,
  `m_date` varchar(50) DEFAULT NULL,
  `m_time` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT 0,
  `created` varchar(50) DEFAULT NULL,
  `modified` varchar(50) DEFAULT NULL,
  `task_scope` enum('internal','external') NOT NULL DEFAULT 'internal',
  `client_id` int(11) DEFAULT 0,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_freq` varchar(50) DEFAULT NULL,
  `recurrence_config` text DEFAULT NULL,
  `next_occurrence` datetime DEFAULT NULL,
  `invoice_amount` varchar(100) DEFAULT NULL,
  `invoiced` varchar(20) DEFAULT NULL,
  `invoice_ref` varchar(100) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_value` varchar(100) DEFAULT NULL,
  `inv_raf` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_terms` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `parent_id` int(255) NOT NULL,
  `cat_type` varchar(255) NOT NULL,
  `title` varchar(500) NOT NULL,
  `created` datetime NOT NULL,
  `status` int(255) NOT NULL DEFAULT 1,
  `delete_status` int(255) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_ticket_followups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `followup_date` date NOT NULL,
  `next_followup_date` date DEFAULT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `next_action` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_ticket_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `old_assigned` text DEFAULT NULL,
  `new_assigned` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_role` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_ticket_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_role` int(11) NOT NULL DEFAULT 0,
  `sender_name` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'General Issue',
  `priority` varchar(50) DEFAULT 'Medium',
  `description` text NOT NULL,
  `admin_response` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Open',
  `created_by` int(11) NOT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `poc` varchar(255) DEFAULT NULL,
  `contact1` varchar(255) DEFAULT NULL,
  `alt_mobile` varchar(100) DEFAULT NULL,
  `alt_contact` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `a_number` varchar(255) DEFAULT NULL,
  `a_department` varchar(255) DEFAULT NULL,
  `r_amount` varchar(255) DEFAULT NULL,
  `r_date` varchar(255) DEFAULT NULL,
  `business_category` varchar(150) DEFAULT NULL,
  `s_cat` varchar(255) DEFAULT NULL,
  `p_cat` varchar(255) DEFAULT NULL,
  `p_details` text DEFAULT NULL,
  `tl_number` varchar(255) DEFAULT NULL,
  `tl_authority` varchar(255) DEFAULT NULL,
  `tl_issue_date` varchar(255) DEFAULT NULL,
  `tl_expire_date` varchar(255) DEFAULT NULL,
  `qty` varchar(255) DEFAULT NULL,
  `mrc` varchar(255) DEFAULT NULL,
  `revenue` varchar(255) DEFAULT NULL,
  `mrr` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `dnd_status` varchar(10) DEFAULT 'No',
  `dnd_check_date` varchar(30) DEFAULT NULL,
  `close_date` varchar(50) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` int(11) DEFAULT 0,
  `state` int(11) DEFAULT 0,
  `city` varchar(255) DEFAULT NULL,
  `poc2` varchar(255) DEFAULT NULL,
  `contact2` varchar(255) DEFAULT NULL,
  `c_partner` varchar(255) DEFAULT NULL,
  `otc` varchar(255) DEFAULT NULL,
  `lead_cat` varchar(255) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT 0,
  `lead_source` varchar(255) DEFAULT NULL,
  `followup_date` varchar(50) DEFAULT NULL,
  `followup_time` varchar(50) DEFAULT NULL,
  `m_date` varchar(50) DEFAULT NULL,
  `m_time` varchar(50) DEFAULT NULL,
  `client_type` varchar(50) DEFAULT 'Onboarded',
  `company_name` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT 0,
  `ticket_type` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `ticket_title` varchar(255) DEFAULT NULL,
  `assigned_users` text DEFAULT NULL,
  `tagged_users` text DEFAULT NULL,
  `raised_by` int(11) DEFAULT 0,
  `created_date` datetime DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL,
  `last_updated_date` datetime DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT 0,
  `internal_notes` text DEFAULT NULL,
  `resolution_remarks` text DEFAULT NULL,
  `resolution_reviewed_by` int(11) DEFAULT 0,
  `resolution_confirmed_at` datetime DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_ticket_creator` (`created_by`),
  KEY `idx_ticket_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_training_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `content_type` varchar(30) NOT NULL DEFAULT 'guide',
  `description` text DEFAULT NULL,
  `body` mediumtext DEFAULT NULL,
  `content_url` varchar(500) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `audience_role_id` int(11) NOT NULL DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_audience` (`audience_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_training_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'In Progress',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_module_user` (`module_id`,`user_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `dob` varchar(50) DEFAULT NULL,
  `uae_mobile` varchar(100) DEFAULT NULL,
  `country_code` varchar(255) NOT NULL,
  `country_code2` varchar(255) NOT NULL,
  `emp_id` varchar(100) DEFAULT NULL,
  `c_email` varchar(255) NOT NULL,
  `c_username` varchar(255) NOT NULL,
  `c_password` varchar(255) NOT NULL,
  `c_mobile` varchar(255) NOT NULL,
  `c_salary` varchar(255) NOT NULL,
  `c_doj` varchar(255) NOT NULL,
  `c_dol` varchar(255) NOT NULL,
  `national` varchar(255) NOT NULL,
  `p_no` varchar(255) NOT NULL,
  `p_date` varchar(255) NOT NULL,
  `p_exdate` varchar(255) NOT NULL,
  `visa_no` varchar(255) DEFAULT NULL,
  `eid_no` varchar(255) DEFAULT NULL,
  `h_contact_name` varchar(255) DEFAULT NULL,
  `h_contact_no` varchar(255) DEFAULT NULL,
  `blood` varchar(255) DEFAULT NULL,
  `no_d_uae` varchar(255) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `bank_no` varchar(255) DEFAULT NULL,
  `country` int(255) DEFAULT NULL,
  `state` int(255) DEFAULT NULL,
  `department` text DEFAULT NULL,
  `office` text DEFAULT NULL,
  `visa_type` text DEFAULT NULL,
  `visa_by` text DEFAULT NULL,
  `visa_expiry` varchar(255) DEFAULT NULL,
  `eid_expiry` varchar(255) DEFAULT NULL,
  `h_contact_relation` varchar(255) DEFAULT NULL,
  `h_contact_no2` varchar(255) DEFAULT NULL,
  `bank_country` varchar(255) DEFAULT NULL,
  `ifsc` varchar(255) DEFAULT NULL,
  `swift` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created` varchar(255) DEFAULT NULL,
  `modified` varchar(255) DEFAULT NULL,
  `admin_status` int(255) NOT NULL DEFAULT 1,
  `fotp` varchar(255) DEFAULT NULL,
  `manager` int(255) DEFAULT NULL,
  `user_level` int(11) NOT NULL DEFAULT 1,
  `user_type` int(11) NOT NULL DEFAULT 1,
  `current_session_token` varchar(64) DEFAULT NULL,
  `last_ip` varchar(100) DEFAULT NULL,
  `last_active_at` datetime DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `currency` varchar(10) NOT NULL DEFAULT 'AED',
  PRIMARY KEY (`id`),
  KEY `idx_user_username` (`c_username`),
  KEY `idx_user_type_status` (`user_type`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_activity` (
  `user_id` int(11) NOT NULL,
  `minute` datetime NOT NULL,
  `page` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`minute`),
  KEY `at` (`minute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_contact` (
  `user_id` int(11) NOT NULL,
  `whatsapp_code` varchar(10) NOT NULL DEFAULT '',
  `whatsapp` varchar(30) NOT NULL DEFAULT '',
  `report_email` varchar(190) NOT NULL DEFAULT '',
  `report_email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `report_email_verified_at` datetime DEFAULT NULL,
  `report_email_verified_by` int(11) DEFAULT NULL,
  `report_phone` varchar(40) NOT NULL DEFAULT '',
  `report_phone_verified` tinyint(1) NOT NULL DEFAULT 0,
  `report_phone_verified_at` datetime DEFAULT NULL,
  `report_phone_verified_by` int(11) DEFAULT NULL,
  `report_whatsapp` varchar(40) NOT NULL DEFAULT '',
  `report_whatsapp_verified` tinyint(1) NOT NULL DEFAULT 0,
  `report_whatsapp_verified_at` datetime DEFAULT NULL,
  `report_whatsapp_verified_by` int(11) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` datetime DEFAULT NULL,
  `email_verified_by` int(11) DEFAULT NULL,
  `mobile_verified` tinyint(1) NOT NULL DEFAULT 0,
  `mobile_verified_at` datetime DEFAULT NULL,
  `mobile_verified_by` int(11) DEFAULT NULL,
  `wa_verified` tinyint(1) NOT NULL DEFAULT 0,
  `wa_verified_at` datetime DEFAULT NULL,
  `wa_verified_by` int(11) DEFAULT NULL,
  `alt_verified` tinyint(1) NOT NULL DEFAULT 0,
  `alt_verified_at` datetime DEFAULT NULL,
  `alt_verified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_designation` (
  `user_id` int(11) NOT NULL,
  `designation` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `doc_type` varchar(40) NOT NULL DEFAULT '',
  `file_name` varchar(80) NOT NULL,
  `orig_name` varchar(200) NOT NULL DEFAULT '',
  `mime` varchar(60) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_exit` (
  `user_id` int(11) NOT NULL,
  `left_on` date NOT NULL,
  `reason` varchar(60) NOT NULL DEFAULT '',
  `note` varchar(500) NOT NULL DEFAULT '',
  `marked_by` int(11) DEFAULT NULL,
  `marked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `field` varchar(60) NOT NULL,
  `old_value` varchar(500) NOT NULL DEFAULT '',
  `new_value` varchar(500) NOT NULL DEFAULT '',
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_when` (`user_id`,`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_live_locations` (
  `user_id` int(11) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  `tracking_active` tinyint(1) DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_live_offices` (
  `user_id` int(11) NOT NULL,
  `offices` text DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_mode` varchar(20) NOT NULL DEFAULT 'role',
  `permissions` mediumtext DEFAULT NULL,
  `last_notif_read_at` datetime DEFAULT NULL,
  `read_notif_ids` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `chatbot_permissions` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_user_prefs` (
  `user_id` int(11) NOT NULL,
  `dash_style` varchar(10) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `crm_view` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_vertical_payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vertical_id` int(11) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `pay_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `method` varchar(30) NOT NULL DEFAULT '',
  `reference` varchar(100) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vertical_id` (`vertical_id`),
  KEY `lead_id` (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_verticals` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `v_id` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `category` int(255) DEFAULT NULL,
  `sub_category` varchar(255) DEFAULT NULL,
  `poc` varchar(255) DEFAULT NULL,
  `contact1` varchar(255) DEFAULT NULL,
  `contact2` varchar(255) DEFAULT NULL,
  `whatsapp_web` varchar(255) DEFAULT NULL,
  `status` int(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `lastfollowup_date` varchar(255) DEFAULT NULL,
  `followup_date` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `nationality` int(255) DEFAULT NULL,
  `nationality_area` int(255) DEFAULT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` int(255) DEFAULT NULL,
  `state` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_by` int(255) DEFAULT NULL,
  `admin_status` int(255) NOT NULL DEFAULT 1,
  `created` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_tbl` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `emp_comment` varchar(5000) NOT NULL,
  `created` datetime NOT NULL,
  `status` int(255) NOT NULL DEFAULT 1,
  `is_read` int(255) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sortname` varchar(3) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phonecode` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schema_migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL DEFAULT 1,
  `executed_at` datetime NOT NULL,
  `execution_time_ms` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'SUCCESS',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_migration_version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'AF','Afghanistan',93);
INSERT INTO `countries` VALUES (2,'AL','Albania',355);
INSERT INTO `countries` VALUES (3,'DZ','Algeria',213);
INSERT INTO `countries` VALUES (4,'AS','American Samoa',1684);
INSERT INTO `countries` VALUES (5,'AD','Andorra',376);
INSERT INTO `countries` VALUES (6,'AO','Angola',244);
INSERT INTO `countries` VALUES (7,'AI','Anguilla',1264);
INSERT INTO `countries` VALUES (8,'AQ','Antarctica',0);
INSERT INTO `countries` VALUES (9,'AG','Antigua And Barbuda',1268);
INSERT INTO `countries` VALUES (10,'AR','Argentina',54);
INSERT INTO `countries` VALUES (11,'AM','Armenia',374);
INSERT INTO `countries` VALUES (12,'AW','Aruba',297);
INSERT INTO `countries` VALUES (13,'AU','Australia',61);
INSERT INTO `countries` VALUES (14,'AT','Austria',43);
INSERT INTO `countries` VALUES (15,'AZ','Azerbaijan',994);
INSERT INTO `countries` VALUES (16,'BS','Bahamas The',1242);
INSERT INTO `countries` VALUES (17,'BH','Bahrain',973);
INSERT INTO `countries` VALUES (18,'BD','Bangladesh',880);
INSERT INTO `countries` VALUES (19,'BB','Barbados',1246);
INSERT INTO `countries` VALUES (20,'BY','Belarus',375);
INSERT INTO `countries` VALUES (21,'BE','Belgium',32);
INSERT INTO `countries` VALUES (22,'BZ','Belize',501);
INSERT INTO `countries` VALUES (23,'BJ','Benin',229);
INSERT INTO `countries` VALUES (24,'BM','Bermuda',1441);
INSERT INTO `countries` VALUES (25,'BT','Bhutan',975);
INSERT INTO `countries` VALUES (26,'BO','Bolivia',591);
INSERT INTO `countries` VALUES (27,'BA','Bosnia and Herzegovina',387);
INSERT INTO `countries` VALUES (28,'BW','Botswana',267);
INSERT INTO `countries` VALUES (29,'BV','Bouvet Island',0);
INSERT INTO `countries` VALUES (30,'BR','Brazil',55);
INSERT INTO `countries` VALUES (31,'IO','British Indian Ocean Territory',246);
INSERT INTO `countries` VALUES (32,'BN','Brunei',673);
INSERT INTO `countries` VALUES (33,'BG','Bulgaria',359);
INSERT INTO `countries` VALUES (34,'BF','Burkina Faso',226);
INSERT INTO `countries` VALUES (35,'BI','Burundi',257);
INSERT INTO `countries` VALUES (36,'KH','Cambodia',855);
INSERT INTO `countries` VALUES (37,'CM','Cameroon',237);
INSERT INTO `countries` VALUES (38,'CA','Canada',1);
INSERT INTO `countries` VALUES (39,'CV','Cape Verde',238);
INSERT INTO `countries` VALUES (40,'KY','Cayman Islands',1345);
INSERT INTO `countries` VALUES (41,'CF','Central African Republic',236);
INSERT INTO `countries` VALUES (42,'TD','Chad',235);
INSERT INTO `countries` VALUES (43,'CL','Chile',56);
INSERT INTO `countries` VALUES (44,'CN','China',86);
INSERT INTO `countries` VALUES (45,'CX','Christmas Island',61);
INSERT INTO `countries` VALUES (46,'CC','Cocos (Keeling) Islands',672);
INSERT INTO `countries` VALUES (47,'CO','Colombia',57);
INSERT INTO `countries` VALUES (48,'KM','Comoros',269);
INSERT INTO `countries` VALUES (49,'CG','Republic Of The Congo',242);
INSERT INTO `countries` VALUES (50,'CD','Democratic Republic Of The Congo',242);
INSERT INTO `countries` VALUES (51,'CK','Cook Islands',682);
INSERT INTO `countries` VALUES (52,'CR','Costa Rica',506);
INSERT INTO `countries` VALUES (53,'CI','Cote D\'Ivoire (Ivory Coast)',225);
INSERT INTO `countries` VALUES (54,'HR','Croatia (Hrvatska)',385);
INSERT INTO `countries` VALUES (55,'CU','Cuba',53);
INSERT INTO `countries` VALUES (56,'CY','Cyprus',357);
INSERT INTO `countries` VALUES (57,'CZ','Czech Republic',420);
INSERT INTO `countries` VALUES (58,'DK','Denmark',45);
INSERT INTO `countries` VALUES (59,'DJ','Djibouti',253);
INSERT INTO `countries` VALUES (60,'DM','Dominica',1767);
INSERT INTO `countries` VALUES (61,'DO','Dominican Republic',1809);
INSERT INTO `countries` VALUES (62,'TP','East Timor',670);
INSERT INTO `countries` VALUES (63,'EC','Ecuador',593);
INSERT INTO `countries` VALUES (64,'EG','Egypt',20);
INSERT INTO `countries` VALUES (65,'SV','El Salvador',503);
INSERT INTO `countries` VALUES (66,'GQ','Equatorial Guinea',240);
INSERT INTO `countries` VALUES (67,'ER','Eritrea',291);
INSERT INTO `countries` VALUES (68,'EE','Estonia',372);
INSERT INTO `countries` VALUES (69,'ET','Ethiopia',251);
INSERT INTO `countries` VALUES (70,'XA','External Territories of Australia',61);
INSERT INTO `countries` VALUES (71,'FK','Falkland Islands',500);
INSERT INTO `countries` VALUES (72,'FO','Faroe Islands',298);
INSERT INTO `countries` VALUES (73,'FJ','Fiji Islands',679);
INSERT INTO `countries` VALUES (74,'FI','Finland',358);
INSERT INTO `countries` VALUES (75,'FR','France',33);
INSERT INTO `countries` VALUES (76,'GF','French Guiana',594);
INSERT INTO `countries` VALUES (77,'PF','French Polynesia',689);
INSERT INTO `countries` VALUES (78,'TF','French Southern Territories',0);
INSERT INTO `countries` VALUES (79,'GA','Gabon',241);
INSERT INTO `countries` VALUES (80,'GM','Gambia The',220);
INSERT INTO `countries` VALUES (81,'GE','Georgia',995);
INSERT INTO `countries` VALUES (82,'DE','Germany',49);
INSERT INTO `countries` VALUES (83,'GH','Ghana',233);
INSERT INTO `countries` VALUES (84,'GI','Gibraltar',350);
INSERT INTO `countries` VALUES (85,'GR','Greece',30);
INSERT INTO `countries` VALUES (86,'GL','Greenland',299);
INSERT INTO `countries` VALUES (87,'GD','Grenada',1473);
INSERT INTO `countries` VALUES (88,'GP','Guadeloupe',590);
INSERT INTO `countries` VALUES (89,'GU','Guam',1671);
INSERT INTO `countries` VALUES (90,'GT','Guatemala',502);
INSERT INTO `countries` VALUES (91,'XU','Guernsey and Alderney',44);
INSERT INTO `countries` VALUES (92,'GN','Guinea',224);
INSERT INTO `countries` VALUES (93,'GW','Guinea-Bissau',245);
INSERT INTO `countries` VALUES (94,'GY','Guyana',592);
INSERT INTO `countries` VALUES (95,'HT','Haiti',509);
INSERT INTO `countries` VALUES (96,'HM','Heard and McDonald Islands',0);
INSERT INTO `countries` VALUES (97,'HN','Honduras',504);
INSERT INTO `countries` VALUES (98,'HK','Hong Kong S.A.R.',852);
INSERT INTO `countries` VALUES (99,'HU','Hungary',36);
INSERT INTO `countries` VALUES (100,'IS','Iceland',354);
INSERT INTO `countries` VALUES (101,'IN','India',91);
INSERT INTO `countries` VALUES (102,'ID','Indonesia',62);
INSERT INTO `countries` VALUES (103,'IR','Iran',98);
INSERT INTO `countries` VALUES (104,'IQ','Iraq',964);
INSERT INTO `countries` VALUES (105,'IE','Ireland',353);
INSERT INTO `countries` VALUES (106,'IL','Israel',972);
INSERT INTO `countries` VALUES (107,'IT','Italy',39);
INSERT INTO `countries` VALUES (108,'JM','Jamaica',1876);
INSERT INTO `countries` VALUES (109,'JP','Japan',81);
INSERT INTO `countries` VALUES (110,'XJ','Jersey',44);
INSERT INTO `countries` VALUES (111,'JO','Jordan',962);
INSERT INTO `countries` VALUES (112,'KZ','Kazakhstan',7);
INSERT INTO `countries` VALUES (113,'KE','Kenya',254);
INSERT INTO `countries` VALUES (114,'KI','Kiribati',686);
INSERT INTO `countries` VALUES (115,'KP','Korea North',850);
INSERT INTO `countries` VALUES (116,'KR','Korea South',82);
INSERT INTO `countries` VALUES (117,'KW','Kuwait',965);
INSERT INTO `countries` VALUES (118,'KG','Kyrgyzstan',996);
INSERT INTO `countries` VALUES (119,'LA','Laos',856);
INSERT INTO `countries` VALUES (120,'LV','Latvia',371);
INSERT INTO `countries` VALUES (121,'LB','Lebanon',961);
INSERT INTO `countries` VALUES (122,'LS','Lesotho',266);
INSERT INTO `countries` VALUES (123,'LR','Liberia',231);
INSERT INTO `countries` VALUES (124,'LY','Libya',218);
INSERT INTO `countries` VALUES (125,'LI','Liechtenstein',423);
INSERT INTO `countries` VALUES (126,'LT','Lithuania',370);
INSERT INTO `countries` VALUES (127,'LU','Luxembourg',352);
INSERT INTO `countries` VALUES (128,'MO','Macau S.A.R.',853);
INSERT INTO `countries` VALUES (129,'MK','Macedonia',389);
INSERT INTO `countries` VALUES (130,'MG','Madagascar',261);
INSERT INTO `countries` VALUES (131,'MW','Malawi',265);
INSERT INTO `countries` VALUES (132,'MY','Malaysia',60);
INSERT INTO `countries` VALUES (133,'MV','Maldives',960);
INSERT INTO `countries` VALUES (134,'ML','Mali',223);
INSERT INTO `countries` VALUES (135,'MT','Malta',356);
INSERT INTO `countries` VALUES (136,'XM','Man (Isle of)',44);
INSERT INTO `countries` VALUES (137,'MH','Marshall Islands',692);
INSERT INTO `countries` VALUES (138,'MQ','Martinique',596);
INSERT INTO `countries` VALUES (139,'MR','Mauritania',222);
INSERT INTO `countries` VALUES (140,'MU','Mauritius',230);
INSERT INTO `countries` VALUES (141,'YT','Mayotte',269);
INSERT INTO `countries` VALUES (142,'MX','Mexico',52);
INSERT INTO `countries` VALUES (143,'FM','Micronesia',691);
INSERT INTO `countries` VALUES (144,'MD','Moldova',373);
INSERT INTO `countries` VALUES (145,'MC','Monaco',377);
INSERT INTO `countries` VALUES (146,'MN','Mongolia',976);
INSERT INTO `countries` VALUES (147,'MS','Montserrat',1664);
INSERT INTO `countries` VALUES (148,'MA','Morocco',212);
INSERT INTO `countries` VALUES (149,'MZ','Mozambique',258);
INSERT INTO `countries` VALUES (150,'MM','Myanmar',95);
INSERT INTO `countries` VALUES (151,'NA','Namibia',264);
INSERT INTO `countries` VALUES (152,'NR','Nauru',674);
INSERT INTO `countries` VALUES (153,'NP','Nepal',977);
INSERT INTO `countries` VALUES (154,'AN','Netherlands Antilles',599);
INSERT INTO `countries` VALUES (155,'NL','Netherlands The',31);
INSERT INTO `countries` VALUES (156,'NC','New Caledonia',687);
INSERT INTO `countries` VALUES (157,'NZ','New Zealand',64);
INSERT INTO `countries` VALUES (158,'NI','Nicaragua',505);
INSERT INTO `countries` VALUES (159,'NE','Niger',227);
INSERT INTO `countries` VALUES (160,'NG','Nigeria',234);
INSERT INTO `countries` VALUES (161,'NU','Niue',683);
INSERT INTO `countries` VALUES (162,'NF','Norfolk Island',672);
INSERT INTO `countries` VALUES (163,'MP','Northern Mariana Islands',1670);
INSERT INTO `countries` VALUES (164,'NO','Norway',47);
INSERT INTO `countries` VALUES (165,'OM','Oman',968);
INSERT INTO `countries` VALUES (166,'PK','Pakistan',92);
INSERT INTO `countries` VALUES (167,'PW','Palau',680);
INSERT INTO `countries` VALUES (168,'PS','Palestinian Territory Occupied',970);
INSERT INTO `countries` VALUES (169,'PA','Panama',507);
INSERT INTO `countries` VALUES (170,'PG','Papua new Guinea',675);
INSERT INTO `countries` VALUES (171,'PY','Paraguay',595);
INSERT INTO `countries` VALUES (172,'PE','Peru',51);
INSERT INTO `countries` VALUES (173,'PH','Philippines',63);
INSERT INTO `countries` VALUES (174,'PN','Pitcairn Island',0);
INSERT INTO `countries` VALUES (175,'PL','Poland',48);
INSERT INTO `countries` VALUES (176,'PT','Portugal',351);
INSERT INTO `countries` VALUES (177,'PR','Puerto Rico',1787);
INSERT INTO `countries` VALUES (178,'QA','Qatar',974);
INSERT INTO `countries` VALUES (179,'RE','Reunion',262);
INSERT INTO `countries` VALUES (180,'RO','Romania',40);
INSERT INTO `countries` VALUES (181,'RU','Russia',70);
INSERT INTO `countries` VALUES (182,'RW','Rwanda',250);
INSERT INTO `countries` VALUES (183,'SH','Saint Helena',290);
INSERT INTO `countries` VALUES (184,'KN','Saint Kitts And Nevis',1869);
INSERT INTO `countries` VALUES (185,'LC','Saint Lucia',1758);
INSERT INTO `countries` VALUES (186,'PM','Saint Pierre and Miquelon',508);
INSERT INTO `countries` VALUES (187,'VC','Saint Vincent And The Grenadines',1784);
INSERT INTO `countries` VALUES (188,'WS','Samoa',684);
INSERT INTO `countries` VALUES (189,'SM','San Marino',378);
INSERT INTO `countries` VALUES (190,'ST','Sao Tome and Principe',239);
INSERT INTO `countries` VALUES (191,'SA','Saudi Arabia',966);
INSERT INTO `countries` VALUES (192,'SN','Senegal',221);
INSERT INTO `countries` VALUES (193,'RS','Serbia',381);
INSERT INTO `countries` VALUES (194,'SC','Seychelles',248);
INSERT INTO `countries` VALUES (195,'SL','Sierra Leone',232);
INSERT INTO `countries` VALUES (196,'SG','Singapore',65);
INSERT INTO `countries` VALUES (197,'SK','Slovakia',421);
INSERT INTO `countries` VALUES (198,'SI','Slovenia',386);
INSERT INTO `countries` VALUES (199,'XG','Smaller Territories of the UK',44);
INSERT INTO `countries` VALUES (200,'SB','Solomon Islands',677);
INSERT INTO `countries` VALUES (201,'SO','Somalia',252);
INSERT INTO `countries` VALUES (202,'ZA','South Africa',27);
INSERT INTO `countries` VALUES (203,'GS','South Georgia',0);
INSERT INTO `countries` VALUES (204,'SS','South Sudan',211);
INSERT INTO `countries` VALUES (205,'ES','Spain',34);
INSERT INTO `countries` VALUES (206,'LK','Sri Lanka',94);
INSERT INTO `countries` VALUES (207,'SD','Sudan',249);
INSERT INTO `countries` VALUES (208,'SR','Suriname',597);
INSERT INTO `countries` VALUES (209,'SJ','Svalbard And Jan Mayen Islands',47);
INSERT INTO `countries` VALUES (210,'SZ','Swaziland',268);
INSERT INTO `countries` VALUES (211,'SE','Sweden',46);
INSERT INTO `countries` VALUES (212,'CH','Switzerland',41);
INSERT INTO `countries` VALUES (213,'SY','Syria',963);
INSERT INTO `countries` VALUES (214,'TW','Taiwan',886);
INSERT INTO `countries` VALUES (215,'TJ','Tajikistan',992);
INSERT INTO `countries` VALUES (216,'TZ','Tanzania',255);
INSERT INTO `countries` VALUES (217,'TH','Thailand',66);
INSERT INTO `countries` VALUES (218,'TG','Togo',228);
INSERT INTO `countries` VALUES (219,'TK','Tokelau',690);
INSERT INTO `countries` VALUES (220,'TO','Tonga',676);
INSERT INTO `countries` VALUES (221,'TT','Trinidad And Tobago',1868);
INSERT INTO `countries` VALUES (222,'TN','Tunisia',216);
INSERT INTO `countries` VALUES (223,'TR','Turkey',90);
INSERT INTO `countries` VALUES (224,'TM','Turkmenistan',7370);
INSERT INTO `countries` VALUES (225,'TC','Turks And Caicos Islands',1649);
INSERT INTO `countries` VALUES (226,'TV','Tuvalu',688);
INSERT INTO `countries` VALUES (227,'UG','Uganda',256);
INSERT INTO `countries` VALUES (228,'UA','Ukraine',380);
INSERT INTO `countries` VALUES (229,'AE','United Arab Emirates',971);
INSERT INTO `countries` VALUES (230,'GB','United Kingdom',44);
INSERT INTO `countries` VALUES (231,'US','United States',1);
INSERT INTO `countries` VALUES (232,'UM','United States Minor Outlying Islands',1);
INSERT INTO `countries` VALUES (233,'UY','Uruguay',598);
INSERT INTO `countries` VALUES (234,'UZ','Uzbekistan',998);
INSERT INTO `countries` VALUES (235,'VU','Vanuatu',678);
INSERT INTO `countries` VALUES (236,'VA','Vatican City State (Holy See)',39);
INSERT INTO `countries` VALUES (237,'VE','Venezuela',58);
INSERT INTO `countries` VALUES (238,'VN','Vietnam',84);
INSERT INTO `countries` VALUES (239,'VG','Virgin Islands (British)',1284);
INSERT INTO `countries` VALUES (240,'VI','Virgin Islands (US)',1340);
INSERT INTO `countries` VALUES (241,'WF','Wallis And Futuna Islands',681);
INSERT INTO `countries` VALUES (242,'EH','Western Sahara',212);
INSERT INTO `countries` VALUES (243,'YE','Yemen',967);
INSERT INTO `countries` VALUES (244,'YU','Yugoslavia',38);
INSERT INTO `countries` VALUES (245,'ZM','Zambia',260);
INSERT INTO `countries` VALUES (246,'ZW','Zimbabwe',263);
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,'Andaman and Nicobar Islands',101);
INSERT INTO `states` VALUES (2,'Andhra Pradesh',101);
INSERT INTO `states` VALUES (3,'Arunachal Pradesh',101);
INSERT INTO `states` VALUES (4,'Assam',101);
INSERT INTO `states` VALUES (5,'Bihar',101);
INSERT INTO `states` VALUES (6,'Chandigarh',101);
INSERT INTO `states` VALUES (7,'Chhattisgarh',101);
INSERT INTO `states` VALUES (8,'Dadra and Nagar Haveli',101);
INSERT INTO `states` VALUES (9,'Daman and Diu',101);
INSERT INTO `states` VALUES (10,'Delhi',101);
INSERT INTO `states` VALUES (11,'Goa',101);
INSERT INTO `states` VALUES (12,'Gujarat',101);
INSERT INTO `states` VALUES (13,'Haryana',101);
INSERT INTO `states` VALUES (14,'Himachal Pradesh',101);
INSERT INTO `states` VALUES (15,'Jammu and Kashmir',101);
INSERT INTO `states` VALUES (16,'Jharkhand',101);
INSERT INTO `states` VALUES (17,'Karnataka',101);
INSERT INTO `states` VALUES (18,'Kenmore',101);
INSERT INTO `states` VALUES (19,'Kerala',101);
INSERT INTO `states` VALUES (20,'Lakshadweep',101);
INSERT INTO `states` VALUES (21,'Madhya Pradesh',101);
INSERT INTO `states` VALUES (22,'Maharashtra',101);
INSERT INTO `states` VALUES (23,'Manipur',101);
INSERT INTO `states` VALUES (24,'Meghalaya',101);
INSERT INTO `states` VALUES (25,'Mizoram',101);
INSERT INTO `states` VALUES (26,'Nagaland',101);
INSERT INTO `states` VALUES (27,'Narora',101);
INSERT INTO `states` VALUES (28,'Natwar',101);
INSERT INTO `states` VALUES (29,'Odisha',101);
INSERT INTO `states` VALUES (30,'Paschim Medinipur',101);
INSERT INTO `states` VALUES (31,'Pondicherry',101);
INSERT INTO `states` VALUES (32,'Punjab',101);
INSERT INTO `states` VALUES (33,'Rajasthan',101);
INSERT INTO `states` VALUES (34,'Sikkim',101);
INSERT INTO `states` VALUES (35,'Tamil Nadu',101);
INSERT INTO `states` VALUES (36,'Telangana',101);
INSERT INTO `states` VALUES (37,'Tripura',101);
INSERT INTO `states` VALUES (38,'Uttar Pradesh',101);
INSERT INTO `states` VALUES (39,'Uttarakhand',101);
INSERT INTO `states` VALUES (40,'Vaishali',101);
INSERT INTO `states` VALUES (41,'West Bengal',101);
INSERT INTO `states` VALUES (42,'Badakhshan',1);
INSERT INTO `states` VALUES (43,'Badgis',1);
INSERT INTO `states` VALUES (44,'Baglan',1);
INSERT INTO `states` VALUES (45,'Balkh',1);
INSERT INTO `states` VALUES (46,'Bamiyan',1);
INSERT INTO `states` VALUES (47,'Farah',1);
INSERT INTO `states` VALUES (48,'Faryab',1);
INSERT INTO `states` VALUES (49,'Gawr',1);
INSERT INTO `states` VALUES (50,'Gazni',1);
INSERT INTO `states` VALUES (51,'Herat',1);
INSERT INTO `states` VALUES (52,'Hilmand',1);
INSERT INTO `states` VALUES (53,'Jawzjan',1);
INSERT INTO `states` VALUES (54,'Kabul',1);
INSERT INTO `states` VALUES (55,'Kapisa',1);
INSERT INTO `states` VALUES (56,'Khawst',1);
INSERT INTO `states` VALUES (57,'Kunar',1);
INSERT INTO `states` VALUES (58,'Lagman',1);
INSERT INTO `states` VALUES (59,'Lawghar',1);
INSERT INTO `states` VALUES (60,'Nangarhar',1);
INSERT INTO `states` VALUES (61,'Nimruz',1);
INSERT INTO `states` VALUES (62,'Nuristan',1);
INSERT INTO `states` VALUES (63,'Paktika',1);
INSERT INTO `states` VALUES (64,'Paktiya',1);
INSERT INTO `states` VALUES (65,'Parwan',1);
INSERT INTO `states` VALUES (66,'Qandahar',1);
INSERT INTO `states` VALUES (67,'Qunduz',1);
INSERT INTO `states` VALUES (68,'Samangan',1);
INSERT INTO `states` VALUES (69,'Sar-e Pul',1);
INSERT INTO `states` VALUES (70,'Takhar',1);
INSERT INTO `states` VALUES (71,'Uruzgan',1);
INSERT INTO `states` VALUES (72,'Wardag',1);
INSERT INTO `states` VALUES (73,'Zabul',1);
INSERT INTO `states` VALUES (74,'Berat',2);
INSERT INTO `states` VALUES (75,'Bulqize',2);
INSERT INTO `states` VALUES (76,'Delvine',2);
INSERT INTO `states` VALUES (77,'Devoll',2);
INSERT INTO `states` VALUES (78,'Dibre',2);
INSERT INTO `states` VALUES (79,'Durres',2);
INSERT INTO `states` VALUES (80,'Elbasan',2);
INSERT INTO `states` VALUES (81,'Fier',2);
INSERT INTO `states` VALUES (82,'Gjirokaster',2);
INSERT INTO `states` VALUES (83,'Gramsh',2);
INSERT INTO `states` VALUES (84,'Has',2);
INSERT INTO `states` VALUES (85,'Kavaje',2);
INSERT INTO `states` VALUES (86,'Kolonje',2);
INSERT INTO `states` VALUES (87,'Korce',2);
INSERT INTO `states` VALUES (88,'Kruje',2);
INSERT INTO `states` VALUES (89,'Kucove',2);
INSERT INTO `states` VALUES (90,'Kukes',2);
INSERT INTO `states` VALUES (91,'Kurbin',2);
INSERT INTO `states` VALUES (92,'Lezhe',2);
INSERT INTO `states` VALUES (93,'Librazhd',2);
INSERT INTO `states` VALUES (94,'Lushnje',2);
INSERT INTO `states` VALUES (95,'Mallakaster',2);
INSERT INTO `states` VALUES (96,'Malsi e Madhe',2);
INSERT INTO `states` VALUES (97,'Mat',2);
INSERT INTO `states` VALUES (98,'Mirdite',2);
INSERT INTO `states` VALUES (99,'Peqin',2);
INSERT INTO `states` VALUES (100,'Permet',2);
INSERT INTO `states` VALUES (101,'Pogradec',2);
INSERT INTO `states` VALUES (102,'Puke',2);
INSERT INTO `states` VALUES (103,'Sarande',2);
INSERT INTO `states` VALUES (104,'Shkoder',2);
INSERT INTO `states` VALUES (105,'Skrapar',2);
INSERT INTO `states` VALUES (106,'Tepelene',2);
INSERT INTO `states` VALUES (107,'Tirane',2);
INSERT INTO `states` VALUES (108,'Tropoje',2);
INSERT INTO `states` VALUES (109,'Vlore',2);
INSERT INTO `states` VALUES (110,'\'Ayn Daflah',3);
INSERT INTO `states` VALUES (111,'\'Ayn Tamushanat',3);
INSERT INTO `states` VALUES (112,'Adrar',3);
INSERT INTO `states` VALUES (113,'Algiers',3);
INSERT INTO `states` VALUES (114,'Annabah',3);
INSERT INTO `states` VALUES (115,'Bashshar',3);
INSERT INTO `states` VALUES (116,'Batnah',3);
INSERT INTO `states` VALUES (117,'Bijayah',3);
INSERT INTO `states` VALUES (118,'Biskrah',3);
INSERT INTO `states` VALUES (119,'Blidah',3);
INSERT INTO `states` VALUES (120,'Buirah',3);
INSERT INTO `states` VALUES (121,'Bumardas',3);
INSERT INTO `states` VALUES (122,'Burj Bu Arririj',3);
INSERT INTO `states` VALUES (123,'Ghalizan',3);
INSERT INTO `states` VALUES (124,'Ghardayah',3);
INSERT INTO `states` VALUES (125,'Ilizi',3);
INSERT INTO `states` VALUES (126,'Jijili',3);
INSERT INTO `states` VALUES (127,'Jilfah',3);
INSERT INTO `states` VALUES (128,'Khanshalah',3);
INSERT INTO `states` VALUES (129,'Masilah',3);
INSERT INTO `states` VALUES (130,'Midyah',3);
INSERT INTO `states` VALUES (131,'Milah',3);
INSERT INTO `states` VALUES (132,'Muaskar',3);
INSERT INTO `states` VALUES (133,'Mustaghanam',3);
INSERT INTO `states` VALUES (134,'Naama',3);
INSERT INTO `states` VALUES (135,'Oran',3);
INSERT INTO `states` VALUES (136,'Ouargla',3);
INSERT INTO `states` VALUES (137,'Qalmah',3);
INSERT INTO `states` VALUES (138,'Qustantinah',3);
INSERT INTO `states` VALUES (139,'Sakikdah',3);
INSERT INTO `states` VALUES (140,'Satif',3);
INSERT INTO `states` VALUES (141,'Sayda\'',3);
INSERT INTO `states` VALUES (142,'Sidi ban-al-\'Abbas',3);
INSERT INTO `states` VALUES (143,'Suq Ahras',3);
INSERT INTO `states` VALUES (144,'Tamanghasat',3);
INSERT INTO `states` VALUES (145,'Tibazah',3);
INSERT INTO `states` VALUES (146,'Tibissah',3);
INSERT INTO `states` VALUES (147,'Tilimsan',3);
INSERT INTO `states` VALUES (148,'Tinduf',3);
INSERT INTO `states` VALUES (149,'Tisamsilt',3);
INSERT INTO `states` VALUES (150,'Tiyarat',3);
INSERT INTO `states` VALUES (151,'Tizi Wazu',3);
INSERT INTO `states` VALUES (152,'Umm-al-Bawaghi',3);
INSERT INTO `states` VALUES (153,'Wahran',3);
INSERT INTO `states` VALUES (154,'Warqla',3);
INSERT INTO `states` VALUES (155,'Wilaya d Alger',3);
INSERT INTO `states` VALUES (156,'Wilaya de Bejaia',3);
INSERT INTO `states` VALUES (157,'Wilaya de Constantine',3);
INSERT INTO `states` VALUES (158,'al-Aghwat',3);
INSERT INTO `states` VALUES (159,'al-Bayadh',3);
INSERT INTO `states` VALUES (160,'al-Jaza\'ir',3);
INSERT INTO `states` VALUES (161,'al-Wad',3);
INSERT INTO `states` VALUES (162,'ash-Shalif',3);
INSERT INTO `states` VALUES (163,'at-Tarif',3);
INSERT INTO `states` VALUES (164,'Eastern',4);
INSERT INTO `states` VALUES (165,'Manu\'a',4);
INSERT INTO `states` VALUES (166,'Swains Island',4);
INSERT INTO `states` VALUES (167,'Western',4);
INSERT INTO `states` VALUES (168,'Andorra la Vella',5);
INSERT INTO `states` VALUES (169,'Canillo',5);
INSERT INTO `states` VALUES (170,'Encamp',5);
INSERT INTO `states` VALUES (171,'La Massana',5);
INSERT INTO `states` VALUES (172,'Les Escaldes',5);
INSERT INTO `states` VALUES (173,'Ordino',5);
INSERT INTO `states` VALUES (174,'Sant Julia de Loria',5);
INSERT INTO `states` VALUES (175,'Bengo',6);
INSERT INTO `states` VALUES (176,'Benguela',6);
INSERT INTO `states` VALUES (177,'Bie',6);
INSERT INTO `states` VALUES (178,'Cabinda',6);
INSERT INTO `states` VALUES (179,'Cunene',6);
INSERT INTO `states` VALUES (180,'Huambo',6);
INSERT INTO `states` VALUES (181,'Huila',6);
INSERT INTO `states` VALUES (182,'Kuando-Kubango',6);
INSERT INTO `states` VALUES (183,'Kwanza Norte',6);
INSERT INTO `states` VALUES (184,'Kwanza Sul',6);
INSERT INTO `states` VALUES (185,'Luanda',6);
INSERT INTO `states` VALUES (186,'Lunda Norte',6);
INSERT INTO `states` VALUES (187,'Lunda Sul',6);
INSERT INTO `states` VALUES (188,'Malanje',6);
INSERT INTO `states` VALUES (189,'Moxico',6);
INSERT INTO `states` VALUES (190,'Namibe',6);
INSERT INTO `states` VALUES (191,'Uige',6);
INSERT INTO `states` VALUES (192,'Zaire',6);
INSERT INTO `states` VALUES (193,'Other Provinces',7);
INSERT INTO `states` VALUES (194,'Sector claimed by Argentina/Ch',8);
INSERT INTO `states` VALUES (195,'Sector claimed by Argentina/UK',8);
INSERT INTO `states` VALUES (196,'Sector claimed by Australia',8);
INSERT INTO `states` VALUES (197,'Sector claimed by France',8);
INSERT INTO `states` VALUES (198,'Sector claimed by New Zealand',8);
INSERT INTO `states` VALUES (199,'Sector claimed by Norway',8);
INSERT INTO `states` VALUES (200,'Unclaimed Sector',8);
INSERT INTO `states` VALUES (201,'Barbuda',9);
INSERT INTO `states` VALUES (202,'Saint George',9);
INSERT INTO `states` VALUES (203,'Saint John',9);
INSERT INTO `states` VALUES (204,'Saint Mary',9);
INSERT INTO `states` VALUES (205,'Saint Paul',9);
INSERT INTO `states` VALUES (206,'Saint Peter',9);
INSERT INTO `states` VALUES (207,'Saint Philip',9);
INSERT INTO `states` VALUES (208,'Buenos Aires',10);
INSERT INTO `states` VALUES (209,'Catamarca',10);
INSERT INTO `states` VALUES (210,'Chaco',10);
INSERT INTO `states` VALUES (211,'Chubut',10);
INSERT INTO `states` VALUES (212,'Cordoba',10);
INSERT INTO `states` VALUES (213,'Corrientes',10);
INSERT INTO `states` VALUES (214,'Distrito Federal',10);
INSERT INTO `states` VALUES (215,'Entre Rios',10);
INSERT INTO `states` VALUES (216,'Formosa',10);
INSERT INTO `states` VALUES (217,'Jujuy',10);
INSERT INTO `states` VALUES (218,'La Pampa',10);
INSERT INTO `states` VALUES (219,'La Rioja',10);
INSERT INTO `states` VALUES (220,'Mendoza',10);
INSERT INTO `states` VALUES (221,'Misiones',10);
INSERT INTO `states` VALUES (222,'Neuquen',10);
INSERT INTO `states` VALUES (223,'Rio Negro',10);
INSERT INTO `states` VALUES (224,'Salta',10);
INSERT INTO `states` VALUES (225,'San Juan',10);
INSERT INTO `states` VALUES (226,'San Luis',10);
INSERT INTO `states` VALUES (227,'Santa Cruz',10);
INSERT INTO `states` VALUES (228,'Santa Fe',10);
INSERT INTO `states` VALUES (229,'Santiago del Estero',10);
INSERT INTO `states` VALUES (230,'Tierra del Fuego',10);
INSERT INTO `states` VALUES (231,'Tucuman',10);
INSERT INTO `states` VALUES (232,'Aragatsotn',11);
INSERT INTO `states` VALUES (233,'Ararat',11);
INSERT INTO `states` VALUES (234,'Armavir',11);
INSERT INTO `states` VALUES (235,'Gegharkunik',11);
INSERT INTO `states` VALUES (236,'Kotaik',11);
INSERT INTO `states` VALUES (237,'Lori',11);
INSERT INTO `states` VALUES (238,'Shirak',11);
INSERT INTO `states` VALUES (239,'Stepanakert',11);
INSERT INTO `states` VALUES (240,'Syunik',11);
INSERT INTO `states` VALUES (241,'Tavush',11);
INSERT INTO `states` VALUES (242,'Vayots Dzor',11);
INSERT INTO `states` VALUES (243,'Yerevan',11);
INSERT INTO `states` VALUES (244,'Aruba',12);
INSERT INTO `states` VALUES (245,'Auckland',13);
INSERT INTO `states` VALUES (246,'Australian Capital Territory',13);
INSERT INTO `states` VALUES (247,'Balgowlah',13);
INSERT INTO `states` VALUES (248,'Balmain',13);
INSERT INTO `states` VALUES (249,'Bankstown',13);
INSERT INTO `states` VALUES (250,'Baulkham Hills',13);
INSERT INTO `states` VALUES (251,'Bonnet Bay',13);
INSERT INTO `states` VALUES (252,'Camberwell',13);
INSERT INTO `states` VALUES (253,'Carole Park',13);
INSERT INTO `states` VALUES (254,'Castle Hill',13);
INSERT INTO `states` VALUES (255,'Caulfield',13);
INSERT INTO `states` VALUES (256,'Chatswood',13);
INSERT INTO `states` VALUES (257,'Cheltenham',13);
INSERT INTO `states` VALUES (258,'Cherrybrook',13);
INSERT INTO `states` VALUES (259,'Clayton',13);
INSERT INTO `states` VALUES (260,'Collingwood',13);
INSERT INTO `states` VALUES (261,'Frenchs Forest',13);
INSERT INTO `states` VALUES (262,'Hawthorn',13);
INSERT INTO `states` VALUES (263,'Jannnali',13);
INSERT INTO `states` VALUES (264,'Knoxfield',13);
INSERT INTO `states` VALUES (265,'Melbourne',13);
INSERT INTO `states` VALUES (266,'New South Wales',13);
INSERT INTO `states` VALUES (267,'Northern Territory',13);
INSERT INTO `states` VALUES (268,'Perth',13);
INSERT INTO `states` VALUES (269,'Queensland',13);
INSERT INTO `states` VALUES (270,'South Australia',13);
INSERT INTO `states` VALUES (271,'Tasmania',13);
INSERT INTO `states` VALUES (272,'Templestowe',13);
INSERT INTO `states` VALUES (273,'Victoria',13);
INSERT INTO `states` VALUES (274,'Werribee south',13);
INSERT INTO `states` VALUES (275,'Western Australia',13);
INSERT INTO `states` VALUES (276,'Wheeler',13);
INSERT INTO `states` VALUES (277,'Bundesland Salzburg',14);
INSERT INTO `states` VALUES (278,'Bundesland Steiermark',14);
INSERT INTO `states` VALUES (279,'Bundesland Tirol',14);
INSERT INTO `states` VALUES (280,'Burgenland',14);
INSERT INTO `states` VALUES (281,'Carinthia',14);
INSERT INTO `states` VALUES (282,'Karnten',14);
INSERT INTO `states` VALUES (283,'Liezen',14);
INSERT INTO `states` VALUES (284,'Lower Austria',14);
INSERT INTO `states` VALUES (285,'Niederosterreich',14);
INSERT INTO `states` VALUES (286,'Oberosterreich',14);
INSERT INTO `states` VALUES (287,'Salzburg',14);
INSERT INTO `states` VALUES (288,'Schleswig-Holstein',14);
INSERT INTO `states` VALUES (289,'Steiermark',14);
INSERT INTO `states` VALUES (290,'Styria',14);
INSERT INTO `states` VALUES (291,'Tirol',14);
INSERT INTO `states` VALUES (292,'Upper Austria',14);
INSERT INTO `states` VALUES (293,'Vorarlberg',14);
INSERT INTO `states` VALUES (294,'Wien',14);
INSERT INTO `states` VALUES (295,'Abseron',15);
INSERT INTO `states` VALUES (296,'Baki Sahari',15);
INSERT INTO `states` VALUES (297,'Ganca',15);
INSERT INTO `states` VALUES (298,'Ganja',15);
INSERT INTO `states` VALUES (299,'Kalbacar',15);
INSERT INTO `states` VALUES (300,'Lankaran',15);
INSERT INTO `states` VALUES (301,'Mil-Qarabax',15);
INSERT INTO `states` VALUES (302,'Mugan-Salyan',15);
INSERT INTO `states` VALUES (303,'Nagorni-Qarabax',15);
INSERT INTO `states` VALUES (304,'Naxcivan',15);
INSERT INTO `states` VALUES (305,'Priaraks',15);
INSERT INTO `states` VALUES (306,'Qazax',15);
INSERT INTO `states` VALUES (307,'Saki',15);
INSERT INTO `states` VALUES (308,'Sirvan',15);
INSERT INTO `states` VALUES (309,'Xacmaz',15);
INSERT INTO `states` VALUES (310,'Abaco',16);
INSERT INTO `states` VALUES (311,'Acklins Island',16);
INSERT INTO `states` VALUES (312,'Andros',16);
INSERT INTO `states` VALUES (313,'Berry Islands',16);
INSERT INTO `states` VALUES (314,'Biminis',16);
INSERT INTO `states` VALUES (315,'Cat Island',16);
INSERT INTO `states` VALUES (316,'Crooked Island',16);
INSERT INTO `states` VALUES (317,'Eleuthera',16);
INSERT INTO `states` VALUES (318,'Exuma and Cays',16);
INSERT INTO `states` VALUES (319,'Grand Bahama',16);
INSERT INTO `states` VALUES (320,'Inagua Islands',16);
INSERT INTO `states` VALUES (321,'Long Island',16);
INSERT INTO `states` VALUES (322,'Mayaguana',16);
INSERT INTO `states` VALUES (323,'New Providence',16);
INSERT INTO `states` VALUES (324,'Ragged Island',16);
INSERT INTO `states` VALUES (325,'Rum Cay',16);
INSERT INTO `states` VALUES (326,'San Salvador',16);
INSERT INTO `states` VALUES (327,'\'Isa',17);
INSERT INTO `states` VALUES (328,'Badiyah',17);
INSERT INTO `states` VALUES (329,'Hidd',17);
INSERT INTO `states` VALUES (330,'Jidd Hafs',17);
INSERT INTO `states` VALUES (331,'Mahama',17);
INSERT INTO `states` VALUES (332,'Manama',17);
INSERT INTO `states` VALUES (333,'Sitrah',17);
INSERT INTO `states` VALUES (334,'al-Manamah',17);
INSERT INTO `states` VALUES (335,'al-Muharraq',17);
INSERT INTO `states` VALUES (336,'ar-Rifa\'a',17);
INSERT INTO `states` VALUES (337,'Bagar Hat',18);
INSERT INTO `states` VALUES (338,'Bandarban',18);
INSERT INTO `states` VALUES (339,'Barguna',18);
INSERT INTO `states` VALUES (340,'Barisal',18);
INSERT INTO `states` VALUES (341,'Bhola',18);
INSERT INTO `states` VALUES (342,'Bogora',18);
INSERT INTO `states` VALUES (343,'Brahman Bariya',18);
INSERT INTO `states` VALUES (344,'Chandpur',18);
INSERT INTO `states` VALUES (345,'Chattagam',18);
INSERT INTO `states` VALUES (346,'Chittagong Division',18);
INSERT INTO `states` VALUES (347,'Chuadanga',18);
INSERT INTO `states` VALUES (348,'Dhaka',18);
INSERT INTO `states` VALUES (349,'Dinajpur',18);
INSERT INTO `states` VALUES (350,'Faridpur',18);
INSERT INTO `states` VALUES (351,'Feni',18);
INSERT INTO `states` VALUES (352,'Gaybanda',18);
INSERT INTO `states` VALUES (353,'Gazipur',18);
INSERT INTO `states` VALUES (354,'Gopalganj',18);
INSERT INTO `states` VALUES (355,'Habiganj',18);
INSERT INTO `states` VALUES (356,'Jaipur Hat',18);
INSERT INTO `states` VALUES (357,'Jamalpur',18);
INSERT INTO `states` VALUES (358,'Jessor',18);
INSERT INTO `states` VALUES (359,'Jhalakati',18);
INSERT INTO `states` VALUES (360,'Jhanaydah',18);
INSERT INTO `states` VALUES (361,'Khagrachhari',18);
INSERT INTO `states` VALUES (362,'Khulna',18);
INSERT INTO `states` VALUES (363,'Kishorganj',18);
INSERT INTO `states` VALUES (364,'Koks Bazar',18);
INSERT INTO `states` VALUES (365,'Komilla',18);
INSERT INTO `states` VALUES (366,'Kurigram',18);
INSERT INTO `states` VALUES (367,'Kushtiya',18);
INSERT INTO `states` VALUES (368,'Lakshmipur',18);
INSERT INTO `states` VALUES (369,'Lalmanir Hat',18);
INSERT INTO `states` VALUES (370,'Madaripur',18);
INSERT INTO `states` VALUES (371,'Magura',18);
INSERT INTO `states` VALUES (372,'Maimansingh',18);
INSERT INTO `states` VALUES (373,'Manikganj',18);
INSERT INTO `states` VALUES (374,'Maulvi Bazar',18);
INSERT INTO `states` VALUES (375,'Meherpur',18);
INSERT INTO `states` VALUES (376,'Munshiganj',18);
INSERT INTO `states` VALUES (377,'Naral',18);
INSERT INTO `states` VALUES (378,'Narayanganj',18);
INSERT INTO `states` VALUES (379,'Narsingdi',18);
INSERT INTO `states` VALUES (380,'Nator',18);
INSERT INTO `states` VALUES (381,'Naugaon',18);
INSERT INTO `states` VALUES (382,'Nawabganj',18);
INSERT INTO `states` VALUES (383,'Netrakona',18);
INSERT INTO `states` VALUES (384,'Nilphamari',18);
INSERT INTO `states` VALUES (385,'Noakhali',18);
INSERT INTO `states` VALUES (386,'Pabna',18);
INSERT INTO `states` VALUES (387,'Panchagarh',18);
INSERT INTO `states` VALUES (388,'Patuakhali',18);
INSERT INTO `states` VALUES (389,'Pirojpur',18);
INSERT INTO `states` VALUES (390,'Rajbari',18);
INSERT INTO `states` VALUES (391,'Rajshahi',18);
INSERT INTO `states` VALUES (392,'Rangamati',18);
INSERT INTO `states` VALUES (393,'Rangpur',18);
INSERT INTO `states` VALUES (394,'Satkhira',18);
INSERT INTO `states` VALUES (395,'Shariatpur',18);
INSERT INTO `states` VALUES (396,'Sherpur',18);
INSERT INTO `states` VALUES (397,'Silhat',18);
INSERT INTO `states` VALUES (398,'Sirajganj',18);
INSERT INTO `states` VALUES (399,'Sunamganj',18);
INSERT INTO `states` VALUES (400,'Tangayal',18);
INSERT INTO `states` VALUES (401,'Thakurgaon',18);
INSERT INTO `states` VALUES (402,'Christ Church',19);
INSERT INTO `states` VALUES (403,'Saint Andrew',19);
INSERT INTO `states` VALUES (404,'Saint George',19);
INSERT INTO `states` VALUES (405,'Saint James',19);
INSERT INTO `states` VALUES (406,'Saint John',19);
INSERT INTO `states` VALUES (407,'Saint Joseph',19);
INSERT INTO `states` VALUES (408,'Saint Lucy',19);
INSERT INTO `states` VALUES (409,'Saint Michael',19);
INSERT INTO `states` VALUES (410,'Saint Peter',19);
INSERT INTO `states` VALUES (411,'Saint Philip',19);
INSERT INTO `states` VALUES (412,'Saint Thomas',19);
INSERT INTO `states` VALUES (413,'Brest',20);
INSERT INTO `states` VALUES (414,'Homjel\'',20);
INSERT INTO `states` VALUES (415,'Hrodna',20);
INSERT INTO `states` VALUES (416,'Mahiljow',20);
INSERT INTO `states` VALUES (417,'Mahilyowskaya Voblasts',20);
INSERT INTO `states` VALUES (418,'Minsk',20);
INSERT INTO `states` VALUES (419,'Minskaja Voblasts\'',20);
INSERT INTO `states` VALUES (420,'Petrik',20);
INSERT INTO `states` VALUES (421,'Vicebsk',20);
INSERT INTO `states` VALUES (422,'Antwerpen',21);
INSERT INTO `states` VALUES (423,'Berchem',21);
INSERT INTO `states` VALUES (424,'Brabant',21);
INSERT INTO `states` VALUES (425,'Brabant Wallon',21);
INSERT INTO `states` VALUES (426,'Brussel',21);
INSERT INTO `states` VALUES (427,'East Flanders',21);
INSERT INTO `states` VALUES (428,'Hainaut',21);
INSERT INTO `states` VALUES (429,'Liege',21);
INSERT INTO `states` VALUES (430,'Limburg',21);
INSERT INTO `states` VALUES (431,'Luxembourg',21);
INSERT INTO `states` VALUES (432,'Namur',21);
INSERT INTO `states` VALUES (433,'Ontario',21);
INSERT INTO `states` VALUES (434,'Oost-Vlaanderen',21);
INSERT INTO `states` VALUES (435,'Provincie Brabant',21);
INSERT INTO `states` VALUES (436,'Vlaams-Brabant',21);
INSERT INTO `states` VALUES (437,'Wallonne',21);
INSERT INTO `states` VALUES (438,'West-Vlaanderen',21);
INSERT INTO `states` VALUES (439,'Belize',22);
INSERT INTO `states` VALUES (440,'Cayo',22);
INSERT INTO `states` VALUES (441,'Corozal',22);
INSERT INTO `states` VALUES (442,'Orange Walk',22);
INSERT INTO `states` VALUES (443,'Stann Creek',22);
INSERT INTO `states` VALUES (444,'Toledo',22);
INSERT INTO `states` VALUES (445,'Alibori',23);
INSERT INTO `states` VALUES (446,'Atacora',23);
INSERT INTO `states` VALUES (447,'Atlantique',23);
INSERT INTO `states` VALUES (448,'Borgou',23);
INSERT INTO `states` VALUES (449,'Collines',23);
INSERT INTO `states` VALUES (450,'Couffo',23);
INSERT INTO `states` VALUES (451,'Donga',23);
INSERT INTO `states` VALUES (452,'Littoral',23);
INSERT INTO `states` VALUES (453,'Mono',23);
INSERT INTO `states` VALUES (454,'Oueme',23);
INSERT INTO `states` VALUES (455,'Plateau',23);
INSERT INTO `states` VALUES (456,'Zou',23);
INSERT INTO `states` VALUES (457,'Hamilton',24);
INSERT INTO `states` VALUES (458,'Saint George',24);
INSERT INTO `states` VALUES (459,'Bumthang',25);
INSERT INTO `states` VALUES (460,'Chhukha',25);
INSERT INTO `states` VALUES (461,'Chirang',25);
INSERT INTO `states` VALUES (462,'Daga',25);
INSERT INTO `states` VALUES (463,'Geylegphug',25);
INSERT INTO `states` VALUES (464,'Ha',25);
INSERT INTO `states` VALUES (465,'Lhuntshi',25);
INSERT INTO `states` VALUES (466,'Mongar',25);
INSERT INTO `states` VALUES (467,'Pemagatsel',25);
INSERT INTO `states` VALUES (468,'Punakha',25);
INSERT INTO `states` VALUES (469,'Rinpung',25);
INSERT INTO `states` VALUES (470,'Samchi',25);
INSERT INTO `states` VALUES (471,'Samdrup Jongkhar',25);
INSERT INTO `states` VALUES (472,'Shemgang',25);
INSERT INTO `states` VALUES (473,'Tashigang',25);
INSERT INTO `states` VALUES (474,'Timphu',25);
INSERT INTO `states` VALUES (475,'Tongsa',25);
INSERT INTO `states` VALUES (476,'Wangdiphodrang',25);
INSERT INTO `states` VALUES (477,'Beni',26);
INSERT INTO `states` VALUES (478,'Chuquisaca',26);
INSERT INTO `states` VALUES (479,'Cochabamba',26);
INSERT INTO `states` VALUES (480,'La Paz',26);
INSERT INTO `states` VALUES (481,'Oruro',26);
INSERT INTO `states` VALUES (482,'Pando',26);
INSERT INTO `states` VALUES (483,'Potosi',26);
INSERT INTO `states` VALUES (484,'Santa Cruz',26);
INSERT INTO `states` VALUES (485,'Tarija',26);
INSERT INTO `states` VALUES (486,'Federacija Bosna i Hercegovina',27);
INSERT INTO `states` VALUES (487,'Republika Srpska',27);
INSERT INTO `states` VALUES (488,'Central Bobonong',28);
INSERT INTO `states` VALUES (489,'Central Boteti',28);
INSERT INTO `states` VALUES (490,'Central Mahalapye',28);
INSERT INTO `states` VALUES (491,'Central Serowe-Palapye',28);
INSERT INTO `states` VALUES (492,'Central Tutume',28);
INSERT INTO `states` VALUES (493,'Chobe',28);
INSERT INTO `states` VALUES (494,'Francistown',28);
INSERT INTO `states` VALUES (495,'Gaborone',28);
INSERT INTO `states` VALUES (496,'Ghanzi',28);
INSERT INTO `states` VALUES (497,'Jwaneng',28);
INSERT INTO `states` VALUES (498,'Kgalagadi North',28);
INSERT INTO `states` VALUES (499,'Kgalagadi South',28);
INSERT INTO `states` VALUES (500,'Kgatleng',28);
INSERT INTO `states` VALUES (501,'Kweneng',28);
INSERT INTO `states` VALUES (502,'Lobatse',28);
INSERT INTO `states` VALUES (503,'Ngamiland',28);
INSERT INTO `states` VALUES (504,'Ngwaketse',28);
INSERT INTO `states` VALUES (505,'North East',28);
INSERT INTO `states` VALUES (506,'Okavango',28);
INSERT INTO `states` VALUES (507,'Orapa',28);
INSERT INTO `states` VALUES (508,'Selibe Phikwe',28);
INSERT INTO `states` VALUES (509,'South East',28);
INSERT INTO `states` VALUES (510,'Sowa',28);
INSERT INTO `states` VALUES (511,'Bouvet Island',29);
INSERT INTO `states` VALUES (512,'Acre',30);
INSERT INTO `states` VALUES (513,'Alagoas',30);
INSERT INTO `states` VALUES (514,'Amapa',30);
INSERT INTO `states` VALUES (515,'Amazonas',30);
INSERT INTO `states` VALUES (516,'Bahia',30);
INSERT INTO `states` VALUES (517,'Ceara',30);
INSERT INTO `states` VALUES (518,'Distrito Federal',30);
INSERT INTO `states` VALUES (519,'Espirito Santo',30);
INSERT INTO `states` VALUES (520,'Estado de Sao Paulo',30);
INSERT INTO `states` VALUES (521,'Goias',30);
INSERT INTO `states` VALUES (522,'Maranhao',30);
INSERT INTO `states` VALUES (523,'Mato Grosso',30);
INSERT INTO `states` VALUES (524,'Mato Grosso do Sul',30);
INSERT INTO `states` VALUES (525,'Minas Gerais',30);
INSERT INTO `states` VALUES (526,'Para',30);
INSERT INTO `states` VALUES (527,'Paraiba',30);
INSERT INTO `states` VALUES (528,'Parana',30);
INSERT INTO `states` VALUES (529,'Pernambuco',30);
INSERT INTO `states` VALUES (530,'Piaui',30);
INSERT INTO `states` VALUES (531,'Rio Grande do Norte',30);
INSERT INTO `states` VALUES (532,'Rio Grande do Sul',30);
INSERT INTO `states` VALUES (533,'Rio de Janeiro',30);
INSERT INTO `states` VALUES (534,'Rondonia',30);
INSERT INTO `states` VALUES (535,'Roraima',30);
INSERT INTO `states` VALUES (536,'Santa Catarina',30);
INSERT INTO `states` VALUES (537,'Sao Paulo',30);
INSERT INTO `states` VALUES (538,'Sergipe',30);
INSERT INTO `states` VALUES (539,'Tocantins',30);
INSERT INTO `states` VALUES (540,'British Indian Ocean Territory',31);
INSERT INTO `states` VALUES (541,'Belait',32);
INSERT INTO `states` VALUES (542,'Brunei-Muara',32);
INSERT INTO `states` VALUES (543,'Temburong',32);
INSERT INTO `states` VALUES (544,'Tutong',32);
INSERT INTO `states` VALUES (545,'Blagoevgrad',33);
INSERT INTO `states` VALUES (546,'Burgas',33);
INSERT INTO `states` VALUES (547,'Dobrich',33);
INSERT INTO `states` VALUES (548,'Gabrovo',33);
INSERT INTO `states` VALUES (549,'Haskovo',33);
INSERT INTO `states` VALUES (550,'Jambol',33);
INSERT INTO `states` VALUES (551,'Kardzhali',33);
INSERT INTO `states` VALUES (552,'Kjustendil',33);
INSERT INTO `states` VALUES (553,'Lovech',33);
INSERT INTO `states` VALUES (554,'Montana',33);
INSERT INTO `states` VALUES (555,'Oblast Sofiya-Grad',33);
INSERT INTO `states` VALUES (556,'Pazardzhik',33);
INSERT INTO `states` VALUES (557,'Pernik',33);
INSERT INTO `states` VALUES (558,'Pleven',33);
INSERT INTO `states` VALUES (559,'Plovdiv',33);
INSERT INTO `states` VALUES (560,'Razgrad',33);
INSERT INTO `states` VALUES (561,'Ruse',33);
INSERT INTO `states` VALUES (562,'Shumen',33);
INSERT INTO `states` VALUES (563,'Silistra',33);
INSERT INTO `states` VALUES (564,'Sliven',33);
INSERT INTO `states` VALUES (565,'Smoljan',33);
INSERT INTO `states` VALUES (566,'Sofija grad',33);
INSERT INTO `states` VALUES (567,'Sofijska oblast',33);
INSERT INTO `states` VALUES (568,'Stara Zagora',33);
INSERT INTO `states` VALUES (569,'Targovishte',33);
INSERT INTO `states` VALUES (570,'Varna',33);
INSERT INTO `states` VALUES (571,'Veliko Tarnovo',33);
INSERT INTO `states` VALUES (572,'Vidin',33);
INSERT INTO `states` VALUES (573,'Vraca',33);
INSERT INTO `states` VALUES (574,'Yablaniza',33);
INSERT INTO `states` VALUES (575,'Bale',34);
INSERT INTO `states` VALUES (576,'Bam',34);
INSERT INTO `states` VALUES (577,'Bazega',34);
INSERT INTO `states` VALUES (578,'Bougouriba',34);
INSERT INTO `states` VALUES (579,'Boulgou',34);
INSERT INTO `states` VALUES (580,'Boulkiemde',34);
INSERT INTO `states` VALUES (581,'Comoe',34);
INSERT INTO `states` VALUES (582,'Ganzourgou',34);
INSERT INTO `states` VALUES (583,'Gnagna',34);
INSERT INTO `states` VALUES (584,'Gourma',34);
INSERT INTO `states` VALUES (585,'Houet',34);
INSERT INTO `states` VALUES (586,'Ioba',34);
INSERT INTO `states` VALUES (587,'Kadiogo',34);
INSERT INTO `states` VALUES (588,'Kenedougou',34);
INSERT INTO `states` VALUES (589,'Komandjari',34);
INSERT INTO `states` VALUES (590,'Kompienga',34);
INSERT INTO `states` VALUES (591,'Kossi',34);
INSERT INTO `states` VALUES (592,'Kouritenga',34);
INSERT INTO `states` VALUES (593,'Kourweogo',34);
INSERT INTO `states` VALUES (594,'Leraba',34);
INSERT INTO `states` VALUES (595,'Mouhoun',34);
INSERT INTO `states` VALUES (596,'Nahouri',34);
INSERT INTO `states` VALUES (597,'Namentenga',34);
INSERT INTO `states` VALUES (598,'Noumbiel',34);
INSERT INTO `states` VALUES (599,'Oubritenga',34);
INSERT INTO `states` VALUES (600,'Oudalan',34);
INSERT INTO `states` VALUES (601,'Passore',34);
INSERT INTO `states` VALUES (602,'Poni',34);
INSERT INTO `states` VALUES (603,'Sanguie',34);
INSERT INTO `states` VALUES (604,'Sanmatenga',34);
INSERT INTO `states` VALUES (605,'Seno',34);
INSERT INTO `states` VALUES (606,'Sissili',34);
INSERT INTO `states` VALUES (607,'Soum',34);
INSERT INTO `states` VALUES (608,'Sourou',34);
INSERT INTO `states` VALUES (609,'Tapoa',34);
INSERT INTO `states` VALUES (610,'Tuy',34);
INSERT INTO `states` VALUES (611,'Yatenga',34);
INSERT INTO `states` VALUES (612,'Zondoma',34);
INSERT INTO `states` VALUES (613,'Zoundweogo',34);
INSERT INTO `states` VALUES (614,'Bubanza',35);
INSERT INTO `states` VALUES (615,'Bujumbura',35);
INSERT INTO `states` VALUES (616,'Bururi',35);
INSERT INTO `states` VALUES (617,'Cankuzo',35);
INSERT INTO `states` VALUES (618,'Cibitoke',35);
INSERT INTO `states` VALUES (619,'Gitega',35);
INSERT INTO `states` VALUES (620,'Karuzi',35);
INSERT INTO `states` VALUES (621,'Kayanza',35);
INSERT INTO `states` VALUES (622,'Kirundo',35);
INSERT INTO `states` VALUES (623,'Makamba',35);
INSERT INTO `states` VALUES (624,'Muramvya',35);
INSERT INTO `states` VALUES (625,'Muyinga',35);
INSERT INTO `states` VALUES (626,'Ngozi',35);
INSERT INTO `states` VALUES (627,'Rutana',35);
INSERT INTO `states` VALUES (628,'Ruyigi',35);
INSERT INTO `states` VALUES (629,'Banteay Mean Chey',36);
INSERT INTO `states` VALUES (630,'Bat Dambang',36);
INSERT INTO `states` VALUES (631,'Kampong Cham',36);
INSERT INTO `states` VALUES (632,'Kampong Chhnang',36);
INSERT INTO `states` VALUES (633,'Kampong Spoeu',36);
INSERT INTO `states` VALUES (634,'Kampong Thum',36);
INSERT INTO `states` VALUES (635,'Kampot',36);
INSERT INTO `states` VALUES (636,'Kandal',36);
INSERT INTO `states` VALUES (637,'Kaoh Kong',36);
INSERT INTO `states` VALUES (638,'Kracheh',36);
INSERT INTO `states` VALUES (639,'Krong Kaeb',36);
INSERT INTO `states` VALUES (640,'Krong Pailin',36);
INSERT INTO `states` VALUES (641,'Krong Preah Sihanouk',36);
INSERT INTO `states` VALUES (642,'Mondol Kiri',36);
INSERT INTO `states` VALUES (643,'Otdar Mean Chey',36);
INSERT INTO `states` VALUES (644,'Phnum Penh',36);
INSERT INTO `states` VALUES (645,'Pousat',36);
INSERT INTO `states` VALUES (646,'Preah Vihear',36);
INSERT INTO `states` VALUES (647,'Prey Veaeng',36);
INSERT INTO `states` VALUES (648,'Rotanak Kiri',36);
INSERT INTO `states` VALUES (649,'Siem Reab',36);
INSERT INTO `states` VALUES (650,'Stueng Traeng',36);
INSERT INTO `states` VALUES (651,'Svay Rieng',36);
INSERT INTO `states` VALUES (652,'Takaev',36);
INSERT INTO `states` VALUES (653,'Adamaoua',37);
INSERT INTO `states` VALUES (654,'Centre',37);
INSERT INTO `states` VALUES (655,'Est',37);
INSERT INTO `states` VALUES (656,'Littoral',37);
INSERT INTO `states` VALUES (657,'Nord',37);
INSERT INTO `states` VALUES (658,'Nord Extreme',37);
INSERT INTO `states` VALUES (659,'Nordouest',37);
INSERT INTO `states` VALUES (660,'Ouest',37);
INSERT INTO `states` VALUES (661,'Sud',37);
INSERT INTO `states` VALUES (662,'Sudouest',37);
INSERT INTO `states` VALUES (663,'Alberta',38);
INSERT INTO `states` VALUES (664,'British Columbia',38);
INSERT INTO `states` VALUES (665,'Manitoba',38);
INSERT INTO `states` VALUES (666,'New Brunswick',38);
INSERT INTO `states` VALUES (667,'Newfoundland and Labrador',38);
INSERT INTO `states` VALUES (668,'Northwest Territories',38);
INSERT INTO `states` VALUES (669,'Nova Scotia',38);
INSERT INTO `states` VALUES (670,'Nunavut',38);
INSERT INTO `states` VALUES (671,'Ontario',38);
INSERT INTO `states` VALUES (672,'Prince Edward Island',38);
INSERT INTO `states` VALUES (673,'Quebec',38);
INSERT INTO `states` VALUES (674,'Saskatchewan',38);
INSERT INTO `states` VALUES (675,'Yukon',38);
INSERT INTO `states` VALUES (676,'Boavista',39);
INSERT INTO `states` VALUES (677,'Brava',39);
INSERT INTO `states` VALUES (678,'Fogo',39);
INSERT INTO `states` VALUES (679,'Maio',39);
INSERT INTO `states` VALUES (680,'Sal',39);
INSERT INTO `states` VALUES (681,'Santo Antao',39);
INSERT INTO `states` VALUES (682,'Sao Nicolau',39);
INSERT INTO `states` VALUES (683,'Sao Tiago',39);
INSERT INTO `states` VALUES (684,'Sao Vicente',39);
INSERT INTO `states` VALUES (685,'Grand Cayman',40);
INSERT INTO `states` VALUES (686,'Bamingui-Bangoran',41);
INSERT INTO `states` VALUES (687,'Bangui',41);
INSERT INTO `states` VALUES (688,'Basse-Kotto',41);
INSERT INTO `states` VALUES (689,'Haut-Mbomou',41);
INSERT INTO `states` VALUES (690,'Haute-Kotto',41);
INSERT INTO `states` VALUES (691,'Kemo',41);
INSERT INTO `states` VALUES (692,'Lobaye',41);
INSERT INTO `states` VALUES (693,'Mambere-Kadei',41);
INSERT INTO `states` VALUES (694,'Mbomou',41);
INSERT INTO `states` VALUES (695,'Nana-Gribizi',41);
INSERT INTO `states` VALUES (696,'Nana-Mambere',41);
INSERT INTO `states` VALUES (697,'Ombella Mpoko',41);
INSERT INTO `states` VALUES (698,'Ouaka',41);
INSERT INTO `states` VALUES (699,'Ouham',41);
INSERT INTO `states` VALUES (700,'Ouham-Pende',41);
INSERT INTO `states` VALUES (701,'Sangha-Mbaere',41);
INSERT INTO `states` VALUES (702,'Vakaga',41);
INSERT INTO `states` VALUES (703,'Batha',42);
INSERT INTO `states` VALUES (704,'Biltine',42);
INSERT INTO `states` VALUES (705,'Bourkou-Ennedi-Tibesti',42);
INSERT INTO `states` VALUES (706,'Chari-Baguirmi',42);
INSERT INTO `states` VALUES (707,'Guera',42);
INSERT INTO `states` VALUES (708,'Kanem',42);
INSERT INTO `states` VALUES (709,'Lac',42);
INSERT INTO `states` VALUES (710,'Logone Occidental',42);
INSERT INTO `states` VALUES (711,'Logone Oriental',42);
INSERT INTO `states` VALUES (712,'Mayo-Kebbi',42);
INSERT INTO `states` VALUES (713,'Moyen-Chari',42);
INSERT INTO `states` VALUES (714,'Ouaddai',42);
INSERT INTO `states` VALUES (715,'Salamat',42);
INSERT INTO `states` VALUES (716,'Tandjile',42);
INSERT INTO `states` VALUES (717,'Aisen',43);
INSERT INTO `states` VALUES (718,'Antofagasta',43);
INSERT INTO `states` VALUES (719,'Araucania',43);
INSERT INTO `states` VALUES (720,'Atacama',43);
INSERT INTO `states` VALUES (721,'Bio Bio',43);
INSERT INTO `states` VALUES (722,'Coquimbo',43);
INSERT INTO `states` VALUES (723,'Libertador General Bernardo O\'',43);
INSERT INTO `states` VALUES (724,'Los Lagos',43);
INSERT INTO `states` VALUES (725,'Magellanes',43);
INSERT INTO `states` VALUES (726,'Maule',43);
INSERT INTO `states` VALUES (727,'Metropolitana',43);
INSERT INTO `states` VALUES (728,'Metropolitana de Santiago',43);
INSERT INTO `states` VALUES (729,'Tarapaca',43);
INSERT INTO `states` VALUES (730,'Valparaiso',43);
INSERT INTO `states` VALUES (731,'Anhui',44);
INSERT INTO `states` VALUES (734,'Aomen',44);
INSERT INTO `states` VALUES (735,'Beijing',44);
INSERT INTO `states` VALUES (736,'Beijing Shi',44);
INSERT INTO `states` VALUES (737,'Chongqing',44);
INSERT INTO `states` VALUES (738,'Fujian',44);
INSERT INTO `states` VALUES (740,'Gansu',44);
INSERT INTO `states` VALUES (741,'Guangdong',44);
INSERT INTO `states` VALUES (743,'Guangxi',44);
INSERT INTO `states` VALUES (744,'Guizhou',44);
INSERT INTO `states` VALUES (745,'Hainan',44);
INSERT INTO `states` VALUES (746,'Hebei',44);
INSERT INTO `states` VALUES (747,'Heilongjiang',44);
INSERT INTO `states` VALUES (748,'Henan',44);
INSERT INTO `states` VALUES (749,'Hubei',44);
INSERT INTO `states` VALUES (750,'Hunan',44);
INSERT INTO `states` VALUES (751,'Jiangsu',44);
INSERT INTO `states` VALUES (753,'Jiangxi',44);
INSERT INTO `states` VALUES (754,'Jilin',44);
INSERT INTO `states` VALUES (755,'Liaoning',44);
INSERT INTO `states` VALUES (757,'Nei Monggol',44);
INSERT INTO `states` VALUES (758,'Ningxia Hui',44);
INSERT INTO `states` VALUES (759,'Qinghai',44);
INSERT INTO `states` VALUES (760,'Shaanxi',44);
INSERT INTO `states` VALUES (761,'Shandong',44);
INSERT INTO `states` VALUES (763,'Shanghai',44);
INSERT INTO `states` VALUES (764,'Shanxi',44);
INSERT INTO `states` VALUES (765,'Sichuan',44);
INSERT INTO `states` VALUES (766,'Tianjin',44);
INSERT INTO `states` VALUES (767,'Xianggang',44);
INSERT INTO `states` VALUES (768,'Xinjiang',44);
INSERT INTO `states` VALUES (769,'Xizang',44);
INSERT INTO `states` VALUES (770,'Yunnan',44);
INSERT INTO `states` VALUES (771,'Zhejiang',44);
INSERT INTO `states` VALUES (773,'Christmas Island',45);
INSERT INTO `states` VALUES (774,'Cocos (Keeling) Islands',46);
INSERT INTO `states` VALUES (775,'Amazonas',47);
INSERT INTO `states` VALUES (776,'Antioquia',47);
INSERT INTO `states` VALUES (777,'Arauca',47);
INSERT INTO `states` VALUES (778,'Atlantico',47);
INSERT INTO `states` VALUES (779,'Bogota',47);
INSERT INTO `states` VALUES (780,'Bolivar',47);
INSERT INTO `states` VALUES (781,'Boyaca',47);
INSERT INTO `states` VALUES (782,'Caldas',47);
INSERT INTO `states` VALUES (783,'Caqueta',47);
INSERT INTO `states` VALUES (784,'Casanare',47);
INSERT INTO `states` VALUES (785,'Cauca',47);
INSERT INTO `states` VALUES (786,'Cesar',47);
INSERT INTO `states` VALUES (787,'Choco',47);
INSERT INTO `states` VALUES (788,'Cordoba',47);
INSERT INTO `states` VALUES (789,'Cundinamarca',47);
INSERT INTO `states` VALUES (790,'Guainia',47);
INSERT INTO `states` VALUES (791,'Guaviare',47);
INSERT INTO `states` VALUES (792,'Huila',47);
INSERT INTO `states` VALUES (793,'La Guajira',47);
INSERT INTO `states` VALUES (794,'Magdalena',47);
INSERT INTO `states` VALUES (795,'Meta',47);
INSERT INTO `states` VALUES (796,'Narino',47);
INSERT INTO `states` VALUES (797,'Norte de Santander',47);
INSERT INTO `states` VALUES (798,'Putumayo',47);
INSERT INTO `states` VALUES (799,'Quindio',47);
INSERT INTO `states` VALUES (800,'Risaralda',47);
INSERT INTO `states` VALUES (801,'San Andres y Providencia',47);
INSERT INTO `states` VALUES (802,'Santander',47);
INSERT INTO `states` VALUES (803,'Sucre',47);
INSERT INTO `states` VALUES (804,'Tolima',47);
INSERT INTO `states` VALUES (805,'Valle del Cauca',47);
INSERT INTO `states` VALUES (806,'Vaupes',47);
INSERT INTO `states` VALUES (807,'Vichada',47);
INSERT INTO `states` VALUES (808,'Mwali',48);
INSERT INTO `states` VALUES (809,'Njazidja',48);
INSERT INTO `states` VALUES (810,'Nzwani',48);
INSERT INTO `states` VALUES (811,'Bouenza',49);
INSERT INTO `states` VALUES (812,'Brazzaville',49);
INSERT INTO `states` VALUES (813,'Cuvette',49);
INSERT INTO `states` VALUES (814,'Kouilou',49);
INSERT INTO `states` VALUES (815,'Lekoumou',49);
INSERT INTO `states` VALUES (816,'Likouala',49);
INSERT INTO `states` VALUES (817,'Niari',49);
INSERT INTO `states` VALUES (818,'Plateaux',49);
INSERT INTO `states` VALUES (819,'Pool',49);
INSERT INTO `states` VALUES (820,'Sangha',49);
INSERT INTO `states` VALUES (821,'Bandundu',50);
INSERT INTO `states` VALUES (822,'Bas-Congo',50);
INSERT INTO `states` VALUES (823,'Equateur',50);
INSERT INTO `states` VALUES (824,'Haut-Congo',50);
INSERT INTO `states` VALUES (825,'Kasai-Occidental',50);
INSERT INTO `states` VALUES (826,'Kasai-Oriental',50);
INSERT INTO `states` VALUES (827,'Katanga',50);
INSERT INTO `states` VALUES (828,'Kinshasa',50);
INSERT INTO `states` VALUES (829,'Maniema',50);
INSERT INTO `states` VALUES (830,'Nord-Kivu',50);
INSERT INTO `states` VALUES (831,'Sud-Kivu',50);
INSERT INTO `states` VALUES (832,'Aitutaki',51);
INSERT INTO `states` VALUES (833,'Atiu',51);
INSERT INTO `states` VALUES (834,'Mangaia',51);
INSERT INTO `states` VALUES (835,'Manihiki',51);
INSERT INTO `states` VALUES (836,'Mauke',51);
INSERT INTO `states` VALUES (837,'Mitiaro',51);
INSERT INTO `states` VALUES (838,'Nassau',51);
INSERT INTO `states` VALUES (839,'Pukapuka',51);
INSERT INTO `states` VALUES (840,'Rakahanga',51);
INSERT INTO `states` VALUES (841,'Rarotonga',51);
INSERT INTO `states` VALUES (842,'Tongareva',51);
INSERT INTO `states` VALUES (843,'Alajuela',52);
INSERT INTO `states` VALUES (844,'Cartago',52);
INSERT INTO `states` VALUES (845,'Guanacaste',52);
INSERT INTO `states` VALUES (846,'Heredia',52);
INSERT INTO `states` VALUES (847,'Limon',52);
INSERT INTO `states` VALUES (848,'Puntarenas',52);
INSERT INTO `states` VALUES (849,'San Jose',52);
INSERT INTO `states` VALUES (850,'Abidjan',53);
INSERT INTO `states` VALUES (851,'Agneby',53);
INSERT INTO `states` VALUES (852,'Bafing',53);
INSERT INTO `states` VALUES (853,'Denguele',53);
INSERT INTO `states` VALUES (854,'Dix-huit Montagnes',53);
INSERT INTO `states` VALUES (855,'Fromager',53);
INSERT INTO `states` VALUES (856,'Haut-Sassandra',53);
INSERT INTO `states` VALUES (857,'Lacs',53);
INSERT INTO `states` VALUES (858,'Lagunes',53);
INSERT INTO `states` VALUES (859,'Marahoue',53);
INSERT INTO `states` VALUES (860,'Moyen-Cavally',53);
INSERT INTO `states` VALUES (861,'Moyen-Comoe',53);
INSERT INTO `states` VALUES (862,'N\'zi-Comoe',53);
INSERT INTO `states` VALUES (863,'Sassandra',53);
INSERT INTO `states` VALUES (864,'Savanes',53);
INSERT INTO `states` VALUES (865,'Sud-Bandama',53);
INSERT INTO `states` VALUES (866,'Sud-Comoe',53);
INSERT INTO `states` VALUES (867,'Vallee du Bandama',53);
INSERT INTO `states` VALUES (868,'Worodougou',53);
INSERT INTO `states` VALUES (869,'Zanzan',53);
INSERT INTO `states` VALUES (870,'Bjelovar-Bilogora',54);
INSERT INTO `states` VALUES (871,'Dubrovnik-Neretva',54);
INSERT INTO `states` VALUES (872,'Grad Zagreb',54);
INSERT INTO `states` VALUES (873,'Istra',54);
INSERT INTO `states` VALUES (874,'Karlovac',54);
INSERT INTO `states` VALUES (875,'Koprivnica-Krizhevci',54);
INSERT INTO `states` VALUES (876,'Krapina-Zagorje',54);
INSERT INTO `states` VALUES (877,'Lika-Senj',54);
INSERT INTO `states` VALUES (878,'Medhimurje',54);
INSERT INTO `states` VALUES (879,'Medimurska Zupanija',54);
INSERT INTO `states` VALUES (880,'Osijek-Baranja',54);
INSERT INTO `states` VALUES (881,'Osjecko-Baranjska Zupanija',54);
INSERT INTO `states` VALUES (882,'Pozhega-Slavonija',54);
INSERT INTO `states` VALUES (883,'Primorje-Gorski Kotar',54);
INSERT INTO `states` VALUES (884,'Shibenik-Knin',54);
INSERT INTO `states` VALUES (885,'Sisak-Moslavina',54);
INSERT INTO `states` VALUES (886,'Slavonski Brod-Posavina',54);
INSERT INTO `states` VALUES (887,'Split-Dalmacija',54);
INSERT INTO `states` VALUES (888,'Varazhdin',54);
INSERT INTO `states` VALUES (889,'Virovitica-Podravina',54);
INSERT INTO `states` VALUES (890,'Vukovar-Srijem',54);
INSERT INTO `states` VALUES (891,'Zadar',54);
INSERT INTO `states` VALUES (892,'Zagreb',54);
INSERT INTO `states` VALUES (893,'Camaguey',55);
INSERT INTO `states` VALUES (894,'Ciego de Avila',55);
INSERT INTO `states` VALUES (895,'Cienfuegos',55);
INSERT INTO `states` VALUES (896,'Ciudad de la Habana',55);
INSERT INTO `states` VALUES (897,'Granma',55);
INSERT INTO `states` VALUES (898,'Guantanamo',55);
INSERT INTO `states` VALUES (899,'Habana',55);
INSERT INTO `states` VALUES (900,'Holguin',55);
INSERT INTO `states` VALUES (901,'Isla de la Juventud',55);
INSERT INTO `states` VALUES (902,'La Habana',55);
INSERT INTO `states` VALUES (903,'Las Tunas',55);
INSERT INTO `states` VALUES (904,'Matanzas',55);
INSERT INTO `states` VALUES (905,'Pinar del Rio',55);
INSERT INTO `states` VALUES (906,'Sancti Spiritus',55);
INSERT INTO `states` VALUES (907,'Santiago de Cuba',55);
INSERT INTO `states` VALUES (908,'Villa Clara',55);
INSERT INTO `states` VALUES (909,'Government controlled area',56);
INSERT INTO `states` VALUES (910,'Limassol',56);
INSERT INTO `states` VALUES (911,'Nicosia District',56);
INSERT INTO `states` VALUES (912,'Paphos',56);
INSERT INTO `states` VALUES (913,'Turkish controlled area',56);
INSERT INTO `states` VALUES (914,'Central Bohemian',57);
INSERT INTO `states` VALUES (915,'Frycovice',57);
INSERT INTO `states` VALUES (916,'Jihocesky Kraj',57);
INSERT INTO `states` VALUES (917,'Jihochesky',57);
INSERT INTO `states` VALUES (918,'Jihomoravsky',57);
INSERT INTO `states` VALUES (919,'Karlovarsky',57);
INSERT INTO `states` VALUES (920,'Klecany',57);
INSERT INTO `states` VALUES (921,'Kralovehradecky',57);
INSERT INTO `states` VALUES (922,'Liberecky',57);
INSERT INTO `states` VALUES (923,'Lipov',57);
INSERT INTO `states` VALUES (924,'Moravskoslezsky',57);
INSERT INTO `states` VALUES (925,'Olomoucky',57);
INSERT INTO `states` VALUES (926,'Olomoucky Kraj',57);
INSERT INTO `states` VALUES (927,'Pardubicky',57);
INSERT INTO `states` VALUES (928,'Plzensky',57);
INSERT INTO `states` VALUES (929,'Praha',57);
INSERT INTO `states` VALUES (930,'Rajhrad',57);
INSERT INTO `states` VALUES (931,'Smirice',57);
INSERT INTO `states` VALUES (932,'South Moravian',57);
INSERT INTO `states` VALUES (933,'Straz nad Nisou',57);
INSERT INTO `states` VALUES (934,'Stredochesky',57);
INSERT INTO `states` VALUES (935,'Unicov',57);
INSERT INTO `states` VALUES (936,'Ustecky',57);
INSERT INTO `states` VALUES (937,'Valletta',57);
INSERT INTO `states` VALUES (938,'Velesin',57);
INSERT INTO `states` VALUES (939,'Vysochina',57);
INSERT INTO `states` VALUES (940,'Zlinsky',57);
INSERT INTO `states` VALUES (941,'Arhus',58);
INSERT INTO `states` VALUES (942,'Bornholm',58);
INSERT INTO `states` VALUES (943,'Frederiksborg',58);
INSERT INTO `states` VALUES (944,'Fyn',58);
INSERT INTO `states` VALUES (945,'Hovedstaden',58);
INSERT INTO `states` VALUES (946,'Kobenhavn',58);
INSERT INTO `states` VALUES (947,'Kobenhavns Amt',58);
INSERT INTO `states` VALUES (948,'Kobenhavns Kommune',58);
INSERT INTO `states` VALUES (949,'Nordjylland',58);
INSERT INTO `states` VALUES (950,'Ribe',58);
INSERT INTO `states` VALUES (951,'Ringkobing',58);
INSERT INTO `states` VALUES (952,'Roervig',58);
INSERT INTO `states` VALUES (953,'Roskilde',58);
INSERT INTO `states` VALUES (954,'Roslev',58);
INSERT INTO `states` VALUES (955,'Sjaelland',58);
INSERT INTO `states` VALUES (956,'Soeborg',58);
INSERT INTO `states` VALUES (957,'Sonderjylland',58);
INSERT INTO `states` VALUES (958,'Storstrom',58);
INSERT INTO `states` VALUES (959,'Syddanmark',58);
INSERT INTO `states` VALUES (960,'Toelloese',58);
INSERT INTO `states` VALUES (961,'Vejle',58);
INSERT INTO `states` VALUES (962,'Vestsjalland',58);
INSERT INTO `states` VALUES (963,'Viborg',58);
INSERT INTO `states` VALUES (964,'\'Ali Sabih',59);
INSERT INTO `states` VALUES (965,'Dikhil',59);
INSERT INTO `states` VALUES (966,'Jibuti',59);
INSERT INTO `states` VALUES (967,'Tajurah',59);
INSERT INTO `states` VALUES (968,'Ubuk',59);
INSERT INTO `states` VALUES (969,'Saint Andrew',60);
INSERT INTO `states` VALUES (970,'Saint David',60);
INSERT INTO `states` VALUES (971,'Saint George',60);
INSERT INTO `states` VALUES (972,'Saint John',60);
INSERT INTO `states` VALUES (973,'Saint Joseph',60);
INSERT INTO `states` VALUES (974,'Saint Luke',60);
INSERT INTO `states` VALUES (975,'Saint Mark',60);
INSERT INTO `states` VALUES (976,'Saint Patrick',60);
INSERT INTO `states` VALUES (977,'Saint Paul',60);
INSERT INTO `states` VALUES (978,'Saint Peter',60);
INSERT INTO `states` VALUES (979,'Azua',61);
INSERT INTO `states` VALUES (980,'Bahoruco',61);
INSERT INTO `states` VALUES (981,'Barahona',61);
INSERT INTO `states` VALUES (982,'Dajabon',61);
INSERT INTO `states` VALUES (983,'Distrito Nacional',61);
INSERT INTO `states` VALUES (984,'Duarte',61);
INSERT INTO `states` VALUES (985,'El Seybo',61);
INSERT INTO `states` VALUES (986,'Elias Pina',61);
INSERT INTO `states` VALUES (987,'Espaillat',61);
INSERT INTO `states` VALUES (988,'Hato Mayor',61);
INSERT INTO `states` VALUES (989,'Independencia',61);
INSERT INTO `states` VALUES (990,'La Altagracia',61);
INSERT INTO `states` VALUES (991,'La Romana',61);
INSERT INTO `states` VALUES (992,'La Vega',61);
INSERT INTO `states` VALUES (993,'Maria Trinidad Sanchez',61);
INSERT INTO `states` VALUES (994,'Monsenor Nouel',61);
INSERT INTO `states` VALUES (995,'Monte Cristi',61);
INSERT INTO `states` VALUES (996,'Monte Plata',61);
INSERT INTO `states` VALUES (997,'Pedernales',61);
INSERT INTO `states` VALUES (998,'Peravia',61);
INSERT INTO `states` VALUES (999,'Puerto Plata',61);
INSERT INTO `states` VALUES (1000,'Salcedo',61);
INSERT INTO `states` VALUES (1001,'Samana',61);
INSERT INTO `states` VALUES (1002,'San Cristobal',61);
INSERT INTO `states` VALUES (1003,'San Juan',61);
INSERT INTO `states` VALUES (1004,'San Pedro de Macoris',61);
INSERT INTO `states` VALUES (1005,'Sanchez Ramirez',61);
INSERT INTO `states` VALUES (1006,'Santiago',61);
INSERT INTO `states` VALUES (1007,'Santiago Rodriguez',61);
INSERT INTO `states` VALUES (1008,'Valverde',61);
INSERT INTO `states` VALUES (1009,'Aileu',62);
INSERT INTO `states` VALUES (1010,'Ainaro',62);
INSERT INTO `states` VALUES (1011,'Ambeno',62);
INSERT INTO `states` VALUES (1012,'Baucau',62);
INSERT INTO `states` VALUES (1013,'Bobonaro',62);
INSERT INTO `states` VALUES (1014,'Cova Lima',62);
INSERT INTO `states` VALUES (1015,'Dili',62);
INSERT INTO `states` VALUES (1016,'Ermera',62);
INSERT INTO `states` VALUES (1017,'Lautem',62);
INSERT INTO `states` VALUES (1018,'Liquica',62);
INSERT INTO `states` VALUES (1019,'Manatuto',62);
INSERT INTO `states` VALUES (1020,'Manufahi',62);
INSERT INTO `states` VALUES (1021,'Viqueque',62);
INSERT INTO `states` VALUES (1022,'Azuay',63);
INSERT INTO `states` VALUES (1023,'Bolivar',63);
INSERT INTO `states` VALUES (1024,'Canar',63);
INSERT INTO `states` VALUES (1025,'Carchi',63);
INSERT INTO `states` VALUES (1026,'Chimborazo',63);
INSERT INTO `states` VALUES (1027,'Cotopaxi',63);
INSERT INTO `states` VALUES (1028,'El Oro',63);
INSERT INTO `states` VALUES (1029,'Esmeraldas',63);
INSERT INTO `states` VALUES (1030,'Galapagos',63);
INSERT INTO `states` VALUES (1031,'Guayas',63);
INSERT INTO `states` VALUES (1032,'Imbabura',63);
INSERT INTO `states` VALUES (1033,'Loja',63);
INSERT INTO `states` VALUES (1034,'Los Rios',63);
INSERT INTO `states` VALUES (1035,'Manabi',63);
INSERT INTO `states` VALUES (1036,'Morona Santiago',63);
INSERT INTO `states` VALUES (1037,'Napo',63);
INSERT INTO `states` VALUES (1038,'Orellana',63);
INSERT INTO `states` VALUES (1039,'Pastaza',63);
INSERT INTO `states` VALUES (1040,'Pichincha',63);
INSERT INTO `states` VALUES (1041,'Sucumbios',63);
INSERT INTO `states` VALUES (1042,'Tungurahua',63);
INSERT INTO `states` VALUES (1043,'Zamora Chinchipe',63);
INSERT INTO `states` VALUES (1044,'Aswan',64);
INSERT INTO `states` VALUES (1045,'Asyut',64);
INSERT INTO `states` VALUES (1046,'Bani Suwayf',64);
INSERT INTO `states` VALUES (1047,'Bur Sa\'id',64);
INSERT INTO `states` VALUES (1048,'Cairo',64);
INSERT INTO `states` VALUES (1049,'Dumyat',64);
INSERT INTO `states` VALUES (1050,'Kafr-ash-Shaykh',64);
INSERT INTO `states` VALUES (1051,'Matruh',64);
INSERT INTO `states` VALUES (1052,'Muhafazat ad Daqahliyah',64);
INSERT INTO `states` VALUES (1053,'Muhafazat al Fayyum',64);
INSERT INTO `states` VALUES (1054,'Muhafazat al Gharbiyah',64);
INSERT INTO `states` VALUES (1055,'Muhafazat al Iskandariyah',64);
INSERT INTO `states` VALUES (1056,'Muhafazat al Qahirah',64);
INSERT INTO `states` VALUES (1057,'Qina',64);
INSERT INTO `states` VALUES (1058,'Sawhaj',64);
INSERT INTO `states` VALUES (1059,'Sina al-Janubiyah',64);
INSERT INTO `states` VALUES (1060,'Sina ash-Shamaliyah',64);
INSERT INTO `states` VALUES (1061,'ad-Daqahliyah',64);
INSERT INTO `states` VALUES (1062,'al-Bahr-al-Ahmar',64);
INSERT INTO `states` VALUES (1063,'al-Buhayrah',64);
INSERT INTO `states` VALUES (1064,'al-Fayyum',64);
INSERT INTO `states` VALUES (1065,'al-Gharbiyah',64);
INSERT INTO `states` VALUES (1066,'al-Iskandariyah',64);
INSERT INTO `states` VALUES (1067,'al-Ismailiyah',64);
INSERT INTO `states` VALUES (1068,'al-Jizah',64);
INSERT INTO `states` VALUES (1069,'al-Minufiyah',64);
INSERT INTO `states` VALUES (1070,'al-Minya',64);
INSERT INTO `states` VALUES (1071,'al-Qahira',64);
INSERT INTO `states` VALUES (1072,'al-Qalyubiyah',64);
INSERT INTO `states` VALUES (1073,'al-Uqsur',64);
INSERT INTO `states` VALUES (1074,'al-Wadi al-Jadid',64);
INSERT INTO `states` VALUES (1075,'as-Suways',64);
INSERT INTO `states` VALUES (1076,'ash-Sharqiyah',64);
INSERT INTO `states` VALUES (1077,'Ahuachapan',65);
INSERT INTO `states` VALUES (1078,'Cabanas',65);
INSERT INTO `states` VALUES (1079,'Chalatenango',65);
INSERT INTO `states` VALUES (1080,'Cuscatlan',65);
INSERT INTO `states` VALUES (1081,'La Libertad',65);
INSERT INTO `states` VALUES (1082,'La Paz',65);
INSERT INTO `states` VALUES (1083,'La Union',65);
INSERT INTO `states` VALUES (1084,'Morazan',65);
INSERT INTO `states` VALUES (1085,'San Miguel',65);
INSERT INTO `states` VALUES (1086,'San Salvador',65);
INSERT INTO `states` VALUES (1087,'San Vicente',65);
INSERT INTO `states` VALUES (1088,'Santa Ana',65);
INSERT INTO `states` VALUES (1089,'Sonsonate',65);
INSERT INTO `states` VALUES (1090,'Usulutan',65);
INSERT INTO `states` VALUES (1091,'Annobon',66);
INSERT INTO `states` VALUES (1092,'Bioko Norte',66);
INSERT INTO `states` VALUES (1093,'Bioko Sur',66);
INSERT INTO `states` VALUES (1094,'Centro Sur',66);
INSERT INTO `states` VALUES (1095,'Kie-Ntem',66);
INSERT INTO `states` VALUES (1096,'Litoral',66);
INSERT INTO `states` VALUES (1097,'Wele-Nzas',66);
INSERT INTO `states` VALUES (1098,'Anseba',67);
INSERT INTO `states` VALUES (1099,'Debub',67);
INSERT INTO `states` VALUES (1100,'Debub-Keih-Bahri',67);
INSERT INTO `states` VALUES (1101,'Gash-Barka',67);
INSERT INTO `states` VALUES (1102,'Maekel',67);
INSERT INTO `states` VALUES (1103,'Semien-Keih-Bahri',67);
INSERT INTO `states` VALUES (1104,'Harju',68);
INSERT INTO `states` VALUES (1105,'Hiiu',68);
INSERT INTO `states` VALUES (1106,'Ida-Viru',68);
INSERT INTO `states` VALUES (1107,'Jarva',68);
INSERT INTO `states` VALUES (1108,'Jogeva',68);
INSERT INTO `states` VALUES (1109,'Laane',68);
INSERT INTO `states` VALUES (1110,'Laane-Viru',68);
INSERT INTO `states` VALUES (1111,'Parnu',68);
INSERT INTO `states` VALUES (1112,'Polva',68);
INSERT INTO `states` VALUES (1113,'Rapla',68);
INSERT INTO `states` VALUES (1114,'Saare',68);
INSERT INTO `states` VALUES (1115,'Tartu',68);
INSERT INTO `states` VALUES (1116,'Valga',68);
INSERT INTO `states` VALUES (1117,'Viljandi',68);
INSERT INTO `states` VALUES (1118,'Voru',68);
INSERT INTO `states` VALUES (1119,'Addis Abeba',69);
INSERT INTO `states` VALUES (1120,'Afar',69);
INSERT INTO `states` VALUES (1121,'Amhara',69);
INSERT INTO `states` VALUES (1122,'Benishangul',69);
INSERT INTO `states` VALUES (1123,'Diredawa',69);
INSERT INTO `states` VALUES (1124,'Gambella',69);
INSERT INTO `states` VALUES (1125,'Harar',69);
INSERT INTO `states` VALUES (1126,'Jigjiga',69);
INSERT INTO `states` VALUES (1127,'Mekele',69);
INSERT INTO `states` VALUES (1128,'Oromia',69);
INSERT INTO `states` VALUES (1129,'Somali',69);
INSERT INTO `states` VALUES (1130,'Southern',69);
INSERT INTO `states` VALUES (1131,'Tigray',69);
INSERT INTO `states` VALUES (1132,'Christmas Island',70);
INSERT INTO `states` VALUES (1133,'Cocos Islands',70);
INSERT INTO `states` VALUES (1134,'Coral Sea Islands',70);
INSERT INTO `states` VALUES (1135,'Falkland Islands',71);
INSERT INTO `states` VALUES (1136,'South Georgia',71);
INSERT INTO `states` VALUES (1137,'Klaksvik',72);
INSERT INTO `states` VALUES (1138,'Nor ara Eysturoy',72);
INSERT INTO `states` VALUES (1139,'Nor oy',72);
INSERT INTO `states` VALUES (1140,'Sandoy',72);
INSERT INTO `states` VALUES (1141,'Streymoy',72);
INSERT INTO `states` VALUES (1142,'Su uroy',72);
INSERT INTO `states` VALUES (1143,'Sy ra Eysturoy',72);
INSERT INTO `states` VALUES (1144,'Torshavn',72);
INSERT INTO `states` VALUES (1145,'Vaga',72);
INSERT INTO `states` VALUES (1146,'Central',73);
INSERT INTO `states` VALUES (1147,'Eastern',73);
INSERT INTO `states` VALUES (1148,'Northern',73);
INSERT INTO `states` VALUES (1149,'South Pacific',73);
INSERT INTO `states` VALUES (1150,'Western',73);
INSERT INTO `states` VALUES (1151,'Ahvenanmaa',74);
INSERT INTO `states` VALUES (1152,'Etela-Karjala',74);
INSERT INTO `states` VALUES (1153,'Etela-Pohjanmaa',74);
INSERT INTO `states` VALUES (1154,'Etela-Savo',74);
INSERT INTO `states` VALUES (1155,'Etela-Suomen Laani',74);
INSERT INTO `states` VALUES (1156,'Ita-Suomen Laani',74);
INSERT INTO `states` VALUES (1157,'Ita-Uusimaa',74);
INSERT INTO `states` VALUES (1158,'Kainuu',74);
INSERT INTO `states` VALUES (1159,'Kanta-Hame',74);
INSERT INTO `states` VALUES (1160,'Keski-Pohjanmaa',74);
INSERT INTO `states` VALUES (1161,'Keski-Suomi',74);
INSERT INTO `states` VALUES (1162,'Kymenlaakso',74);
INSERT INTO `states` VALUES (1163,'Lansi-Suomen Laani',74);
INSERT INTO `states` VALUES (1164,'Lappi',74);
INSERT INTO `states` VALUES (1165,'Northern Savonia',74);
INSERT INTO `states` VALUES (1166,'Ostrobothnia',74);
INSERT INTO `states` VALUES (1167,'Oulun Laani',74);
INSERT INTO `states` VALUES (1168,'Paijat-Hame',74);
INSERT INTO `states` VALUES (1169,'Pirkanmaa',74);
INSERT INTO `states` VALUES (1170,'Pohjanmaa',74);
INSERT INTO `states` VALUES (1171,'Pohjois-Karjala',74);
INSERT INTO `states` VALUES (1172,'Pohjois-Pohjanmaa',74);
INSERT INTO `states` VALUES (1173,'Pohjois-Savo',74);
INSERT INTO `states` VALUES (1174,'Saarijarvi',74);
INSERT INTO `states` VALUES (1175,'Satakunta',74);
INSERT INTO `states` VALUES (1176,'Southern Savonia',74);
INSERT INTO `states` VALUES (1177,'Tavastia Proper',74);
INSERT INTO `states` VALUES (1178,'Uleaborgs Lan',74);
INSERT INTO `states` VALUES (1179,'Uusimaa',74);
INSERT INTO `states` VALUES (1180,'Varsinais-Suomi',74);
INSERT INTO `states` VALUES (1181,'Ain',75);
INSERT INTO `states` VALUES (1182,'Aisne',75);
INSERT INTO `states` VALUES (1183,'Albi Le Sequestre',75);
INSERT INTO `states` VALUES (1184,'Allier',75);
INSERT INTO `states` VALUES (1185,'Alpes-Cote dAzur',75);
INSERT INTO `states` VALUES (1186,'Alpes-Maritimes',75);
INSERT INTO `states` VALUES (1187,'Alpes-de-Haute-Provence',75);
INSERT INTO `states` VALUES (1188,'Alsace',75);
INSERT INTO `states` VALUES (1189,'Aquitaine',75);
INSERT INTO `states` VALUES (1190,'Ardeche',75);
INSERT INTO `states` VALUES (1191,'Ardennes',75);
INSERT INTO `states` VALUES (1192,'Ariege',75);
INSERT INTO `states` VALUES (1193,'Aube',75);
INSERT INTO `states` VALUES (1194,'Aude',75);
INSERT INTO `states` VALUES (1195,'Auvergne',75);
INSERT INTO `states` VALUES (1196,'Aveyron',75);
INSERT INTO `states` VALUES (1197,'Bas-Rhin',75);
INSERT INTO `states` VALUES (1198,'Basse-Normandie',75);
INSERT INTO `states` VALUES (1199,'Bouches-du-Rhone',75);
INSERT INTO `states` VALUES (1200,'Bourgogne',75);
INSERT INTO `states` VALUES (1201,'Bretagne',75);
INSERT INTO `states` VALUES (1202,'Brittany',75);
INSERT INTO `states` VALUES (1203,'Burgundy',75);
INSERT INTO `states` VALUES (1204,'Calvados',75);
INSERT INTO `states` VALUES (1205,'Cantal',75);
INSERT INTO `states` VALUES (1206,'Cedex',75);
INSERT INTO `states` VALUES (1207,'Centre',75);
INSERT INTO `states` VALUES (1208,'Charente',75);
INSERT INTO `states` VALUES (1209,'Charente-Maritime',75);
INSERT INTO `states` VALUES (1210,'Cher',75);
INSERT INTO `states` VALUES (1211,'Correze',75);
INSERT INTO `states` VALUES (1212,'Corse-du-Sud',75);
INSERT INTO `states` VALUES (1213,'Cote-d\'Or',75);
INSERT INTO `states` VALUES (1214,'Cotes-d\'Armor',75);
INSERT INTO `states` VALUES (1215,'Creuse',75);
INSERT INTO `states` VALUES (1216,'Crolles',75);
INSERT INTO `states` VALUES (1217,'Deux-Sevres',75);
INSERT INTO `states` VALUES (1218,'Dordogne',75);
INSERT INTO `states` VALUES (1219,'Doubs',75);
INSERT INTO `states` VALUES (1220,'Drome',75);
INSERT INTO `states` VALUES (1221,'Essonne',75);
INSERT INTO `states` VALUES (1222,'Eure',75);
INSERT INTO `states` VALUES (1223,'Eure-et-Loir',75);
INSERT INTO `states` VALUES (1224,'Feucherolles',75);
INSERT INTO `states` VALUES (1225,'Finistere',75);
INSERT INTO `states` VALUES (1226,'Franche-Comte',75);
INSERT INTO `states` VALUES (1227,'Gard',75);
INSERT INTO `states` VALUES (1228,'Gers',75);
INSERT INTO `states` VALUES (1229,'Gironde',75);
INSERT INTO `states` VALUES (1230,'Haut-Rhin',75);
INSERT INTO `states` VALUES (1231,'Haute-Corse',75);
INSERT INTO `states` VALUES (1232,'Haute-Garonne',75);
INSERT INTO `states` VALUES (1233,'Haute-Loire',75);
INSERT INTO `states` VALUES (1234,'Haute-Marne',75);
INSERT INTO `states` VALUES (1235,'Haute-Saone',75);
INSERT INTO `states` VALUES (1236,'Haute-Savoie',75);
INSERT INTO `states` VALUES (1237,'Haute-Vienne',75);
INSERT INTO `states` VALUES (1238,'Hautes-Alpes',75);
INSERT INTO `states` VALUES (1239,'Hautes-Pyrenees',75);
INSERT INTO `states` VALUES (1240,'Hauts-de-Seine',75);
INSERT INTO `states` VALUES (1241,'Herault',75);
INSERT INTO `states` VALUES (1242,'Ile-de-France',75);
INSERT INTO `states` VALUES (1243,'Ille-et-Vilaine',75);
INSERT INTO `states` VALUES (1244,'Indre',75);
INSERT INTO `states` VALUES (1245,'Indre-et-Loire',75);
INSERT INTO `states` VALUES (1246,'Isere',75);
INSERT INTO `states` VALUES (1247,'Jura',75);
INSERT INTO `states` VALUES (1248,'Klagenfurt',75);
INSERT INTO `states` VALUES (1249,'Landes',75);
INSERT INTO `states` VALUES (1250,'Languedoc-Roussillon',75);
INSERT INTO `states` VALUES (1251,'Larcay',75);
INSERT INTO `states` VALUES (1252,'Le Castellet',75);
INSERT INTO `states` VALUES (1253,'Le Creusot',75);
INSERT INTO `states` VALUES (1254,'Limousin',75);
INSERT INTO `states` VALUES (1255,'Loir-et-Cher',75);
INSERT INTO `states` VALUES (1256,'Loire',75);
INSERT INTO `states` VALUES (1257,'Loire-Atlantique',75);
INSERT INTO `states` VALUES (1258,'Loiret',75);
INSERT INTO `states` VALUES (1259,'Lorraine',75);
INSERT INTO `states` VALUES (1260,'Lot',75);
INSERT INTO `states` VALUES (1261,'Lot-et-Garonne',75);
INSERT INTO `states` VALUES (1262,'Lower Normandy',75);
INSERT INTO `states` VALUES (1263,'Lozere',75);
INSERT INTO `states` VALUES (1264,'Maine-et-Loire',75);
INSERT INTO `states` VALUES (1265,'Manche',75);
INSERT INTO `states` VALUES (1266,'Marne',75);
INSERT INTO `states` VALUES (1267,'Mayenne',75);
INSERT INTO `states` VALUES (1268,'Meurthe-et-Moselle',75);
INSERT INTO `states` VALUES (1269,'Meuse',75);
INSERT INTO `states` VALUES (1270,'Midi-Pyrenees',75);
INSERT INTO `states` VALUES (1271,'Morbihan',75);
INSERT INTO `states` VALUES (1272,'Moselle',75);
INSERT INTO `states` VALUES (1273,'Nievre',75);
INSERT INTO `states` VALUES (1274,'Nord',75);
INSERT INTO `states` VALUES (1275,'Nord-Pas-de-Calais',75);
INSERT INTO `states` VALUES (1276,'Oise',75);
INSERT INTO `states` VALUES (1277,'Orne',75);
INSERT INTO `states` VALUES (1278,'Paris',75);
INSERT INTO `states` VALUES (1279,'Pas-de-Calais',75);
INSERT INTO `states` VALUES (1280,'Pays de la Loire',75);
INSERT INTO `states` VALUES (1281,'Pays-de-la-Loire',75);
INSERT INTO `states` VALUES (1282,'Picardy',75);
INSERT INTO `states` VALUES (1283,'Puy-de-Dome',75);
INSERT INTO `states` VALUES (1284,'Pyrenees-Atlantiques',75);
INSERT INTO `states` VALUES (1285,'Pyrenees-Orientales',75);
INSERT INTO `states` VALUES (1286,'Quelmes',75);
INSERT INTO `states` VALUES (1287,'Rhone',75);
INSERT INTO `states` VALUES (1288,'Rhone-Alpes',75);
INSERT INTO `states` VALUES (1289,'Saint Ouen',75);
INSERT INTO `states` VALUES (1290,'Saint Viatre',75);
INSERT INTO `states` VALUES (1291,'Saone-et-Loire',75);
INSERT INTO `states` VALUES (1292,'Sarthe',75);
INSERT INTO `states` VALUES (1293,'Savoie',75);
INSERT INTO `states` VALUES (1294,'Seine-Maritime',75);
INSERT INTO `states` VALUES (1295,'Seine-Saint-Denis',75);
INSERT INTO `states` VALUES (1296,'Seine-et-Marne',75);
INSERT INTO `states` VALUES (1297,'Somme',75);
INSERT INTO `states` VALUES (1298,'Sophia Antipolis',75);
INSERT INTO `states` VALUES (1299,'Souvans',75);
INSERT INTO `states` VALUES (1300,'Tarn',75);
INSERT INTO `states` VALUES (1301,'Tarn-et-Garonne',75);
INSERT INTO `states` VALUES (1302,'Territoire de Belfort',75);
INSERT INTO `states` VALUES (1303,'Treignac',75);
INSERT INTO `states` VALUES (1304,'Upper Normandy',75);
INSERT INTO `states` VALUES (1305,'Val-d\'Oise',75);
INSERT INTO `states` VALUES (1306,'Val-de-Marne',75);
INSERT INTO `states` VALUES (1307,'Var',75);
INSERT INTO `states` VALUES (1308,'Vaucluse',75);
INSERT INTO `states` VALUES (1309,'Vellise',75);
INSERT INTO `states` VALUES (1310,'Vendee',75);
INSERT INTO `states` VALUES (1311,'Vienne',75);
INSERT INTO `states` VALUES (1312,'Vosges',75);
INSERT INTO `states` VALUES (1313,'Yonne',75);
INSERT INTO `states` VALUES (1314,'Yvelines',75);
INSERT INTO `states` VALUES (1315,'Cayenne',76);
INSERT INTO `states` VALUES (1316,'Saint-Laurent-du-Maroni',76);
INSERT INTO `states` VALUES (1317,'Iles du Vent',77);
INSERT INTO `states` VALUES (1318,'Iles sous le Vent',77);
INSERT INTO `states` VALUES (1319,'Marquesas',77);
INSERT INTO `states` VALUES (1320,'Tuamotu',77);
INSERT INTO `states` VALUES (1321,'Tubuai',77);
INSERT INTO `states` VALUES (1322,'Amsterdam',78);
INSERT INTO `states` VALUES (1323,'Crozet Islands',78);
INSERT INTO `states` VALUES (1324,'Kerguelen',78);
INSERT INTO `states` VALUES (1325,'Estuaire',79);
INSERT INTO `states` VALUES (1326,'Haut-Ogooue',79);
INSERT INTO `states` VALUES (1327,'Moyen-Ogooue',79);
INSERT INTO `states` VALUES (1328,'Ngounie',79);
INSERT INTO `states` VALUES (1329,'Nyanga',79);
INSERT INTO `states` VALUES (1330,'Ogooue-Ivindo',79);
INSERT INTO `states` VALUES (1331,'Ogooue-Lolo',79);
INSERT INTO `states` VALUES (1332,'Ogooue-Maritime',79);
INSERT INTO `states` VALUES (1333,'Woleu-Ntem',79);
INSERT INTO `states` VALUES (1334,'Banjul',80);
INSERT INTO `states` VALUES (1335,'Basse',80);
INSERT INTO `states` VALUES (1336,'Brikama',80);
INSERT INTO `states` VALUES (1337,'Janjanbureh',80);
INSERT INTO `states` VALUES (1338,'Kanifing',80);
INSERT INTO `states` VALUES (1339,'Kerewan',80);
INSERT INTO `states` VALUES (1340,'Kuntaur',80);
INSERT INTO `states` VALUES (1341,'Mansakonko',80);
INSERT INTO `states` VALUES (1342,'Abhasia',81);
INSERT INTO `states` VALUES (1343,'Ajaria',81);
INSERT INTO `states` VALUES (1344,'Guria',81);
INSERT INTO `states` VALUES (1345,'Imereti',81);
INSERT INTO `states` VALUES (1346,'Kaheti',81);
INSERT INTO `states` VALUES (1347,'Kvemo Kartli',81);
INSERT INTO `states` VALUES (1348,'Mcheta-Mtianeti',81);
INSERT INTO `states` VALUES (1349,'Racha',81);
INSERT INTO `states` VALUES (1350,'Samagrelo-Zemo Svaneti',81);
INSERT INTO `states` VALUES (1351,'Samche-Zhavaheti',81);
INSERT INTO `states` VALUES (1352,'Shida Kartli',81);
INSERT INTO `states` VALUES (1353,'Tbilisi',81);
INSERT INTO `states` VALUES (1354,'Auvergne',82);
INSERT INTO `states` VALUES (1355,'Baden-Wurttemberg',82);
INSERT INTO `states` VALUES (1356,'Bavaria',82);
INSERT INTO `states` VALUES (1357,'Bayern',82);
INSERT INTO `states` VALUES (1358,'Beilstein Wurtt',82);
INSERT INTO `states` VALUES (1359,'Berlin',82);
INSERT INTO `states` VALUES (1360,'Brandenburg',82);
INSERT INTO `states` VALUES (1361,'Bremen',82);
INSERT INTO `states` VALUES (1362,'Dreisbach',82);
INSERT INTO `states` VALUES (1363,'Freistaat Bayern',82);
INSERT INTO `states` VALUES (1364,'Hamburg',82);
INSERT INTO `states` VALUES (1365,'Hannover',82);
INSERT INTO `states` VALUES (1366,'Heroldstatt',82);
INSERT INTO `states` VALUES (1367,'Hessen',82);
INSERT INTO `states` VALUES (1368,'Kortenberg',82);
INSERT INTO `states` VALUES (1369,'Laasdorf',82);
INSERT INTO `states` VALUES (1370,'Land Baden-Wurttemberg',82);
INSERT INTO `states` VALUES (1371,'Land Bayern',82);
INSERT INTO `states` VALUES (1372,'Land Brandenburg',82);
INSERT INTO `states` VALUES (1373,'Land Hessen',82);
INSERT INTO `states` VALUES (1374,'Land Mecklenburg-Vorpommern',82);
INSERT INTO `states` VALUES (1375,'Land Nordrhein-Westfalen',82);
INSERT INTO `states` VALUES (1376,'Land Rheinland-Pfalz',82);
INSERT INTO `states` VALUES (1377,'Land Sachsen',82);
INSERT INTO `states` VALUES (1378,'Land Sachsen-Anhalt',82);
INSERT INTO `states` VALUES (1379,'Land Thuringen',82);
INSERT INTO `states` VALUES (1380,'Lower Saxony',82);
INSERT INTO `states` VALUES (1381,'Mecklenburg-Vorpommern',82);
INSERT INTO `states` VALUES (1382,'Mulfingen',82);
INSERT INTO `states` VALUES (1383,'Munich',82);
INSERT INTO `states` VALUES (1384,'Neubeuern',82);
INSERT INTO `states` VALUES (1385,'Niedersachsen',82);
INSERT INTO `states` VALUES (1386,'Noord-Holland',82);
INSERT INTO `states` VALUES (1387,'Nordrhein-Westfalen',82);
INSERT INTO `states` VALUES (1388,'North Rhine-Westphalia',82);
INSERT INTO `states` VALUES (1389,'Osterode',82);
INSERT INTO `states` VALUES (1390,'Rheinland-Pfalz',82);
INSERT INTO `states` VALUES (1391,'Rhineland-Palatinate',82);
INSERT INTO `states` VALUES (1392,'Saarland',82);
INSERT INTO `states` VALUES (1393,'Sachsen',82);
INSERT INTO `states` VALUES (1394,'Sachsen-Anhalt',82);
INSERT INTO `states` VALUES (1395,'Saxony',82);
INSERT INTO `states` VALUES (1396,'Schleswig-Holstein',82);
INSERT INTO `states` VALUES (1397,'Thuringia',82);
INSERT INTO `states` VALUES (1398,'Webling',82);
INSERT INTO `states` VALUES (1399,'Weinstrabe',82);
INSERT INTO `states` VALUES (1400,'schlobborn',82);
INSERT INTO `states` VALUES (1401,'Ashanti',83);
INSERT INTO `states` VALUES (1402,'Brong-Ahafo',83);
INSERT INTO `states` VALUES (1403,'Central',83);
INSERT INTO `states` VALUES (1404,'Eastern',83);
INSERT INTO `states` VALUES (1405,'Greater Accra',83);
INSERT INTO `states` VALUES (1406,'Northern',83);
INSERT INTO `states` VALUES (1407,'Upper East',83);
INSERT INTO `states` VALUES (1408,'Upper West',83);
INSERT INTO `states` VALUES (1409,'Volta',83);
INSERT INTO `states` VALUES (1410,'Western',83);
INSERT INTO `states` VALUES (1411,'Gibraltar',84);
INSERT INTO `states` VALUES (1412,'Acharnes',85);
INSERT INTO `states` VALUES (1413,'Ahaia',85);
INSERT INTO `states` VALUES (1414,'Aitolia kai Akarnania',85);
INSERT INTO `states` VALUES (1415,'Argolis',85);
INSERT INTO `states` VALUES (1416,'Arkadia',85);
INSERT INTO `states` VALUES (1417,'Arta',85);
INSERT INTO `states` VALUES (1418,'Attica',85);
INSERT INTO `states` VALUES (1419,'Attiki',85);
INSERT INTO `states` VALUES (1420,'Ayion Oros',85);
INSERT INTO `states` VALUES (1421,'Crete',85);
INSERT INTO `states` VALUES (1422,'Dodekanisos',85);
INSERT INTO `states` VALUES (1423,'Drama',85);
INSERT INTO `states` VALUES (1424,'Evia',85);
INSERT INTO `states` VALUES (1425,'Evritania',85);
INSERT INTO `states` VALUES (1426,'Evros',85);
INSERT INTO `states` VALUES (1427,'Evvoia',85);
INSERT INTO `states` VALUES (1428,'Florina',85);
INSERT INTO `states` VALUES (1429,'Fokis',85);
INSERT INTO `states` VALUES (1430,'Fthiotis',85);
INSERT INTO `states` VALUES (1431,'Grevena',85);
INSERT INTO `states` VALUES (1432,'Halandri',85);
INSERT INTO `states` VALUES (1433,'Halkidiki',85);
INSERT INTO `states` VALUES (1434,'Hania',85);
INSERT INTO `states` VALUES (1435,'Heraklion',85);
INSERT INTO `states` VALUES (1436,'Hios',85);
INSERT INTO `states` VALUES (1437,'Ilia',85);
INSERT INTO `states` VALUES (1438,'Imathia',85);
INSERT INTO `states` VALUES (1439,'Ioannina',85);
INSERT INTO `states` VALUES (1440,'Iraklion',85);
INSERT INTO `states` VALUES (1441,'Karditsa',85);
INSERT INTO `states` VALUES (1442,'Kastoria',85);
INSERT INTO `states` VALUES (1443,'Kavala',85);
INSERT INTO `states` VALUES (1444,'Kefallinia',85);
INSERT INTO `states` VALUES (1445,'Kerkira',85);
INSERT INTO `states` VALUES (1446,'Kiklades',85);
INSERT INTO `states` VALUES (1447,'Kilkis',85);
INSERT INTO `states` VALUES (1448,'Korinthia',85);
INSERT INTO `states` VALUES (1449,'Kozani',85);
INSERT INTO `states` VALUES (1450,'Lakonia',85);
INSERT INTO `states` VALUES (1451,'Larisa',85);
INSERT INTO `states` VALUES (1452,'Lasithi',85);
INSERT INTO `states` VALUES (1453,'Lesvos',85);
INSERT INTO `states` VALUES (1454,'Levkas',85);
INSERT INTO `states` VALUES (1455,'Magnisia',85);
INSERT INTO `states` VALUES (1456,'Messinia',85);
INSERT INTO `states` VALUES (1457,'Nomos Attikis',85);
INSERT INTO `states` VALUES (1458,'Nomos Zakynthou',85);
INSERT INTO `states` VALUES (1459,'Pella',85);
INSERT INTO `states` VALUES (1460,'Pieria',85);
INSERT INTO `states` VALUES (1461,'Piraios',85);
INSERT INTO `states` VALUES (1462,'Preveza',85);
INSERT INTO `states` VALUES (1463,'Rethimni',85);
INSERT INTO `states` VALUES (1464,'Rodopi',85);
INSERT INTO `states` VALUES (1465,'Samos',85);
INSERT INTO `states` VALUES (1466,'Serrai',85);
INSERT INTO `states` VALUES (1467,'Thesprotia',85);
INSERT INTO `states` VALUES (1468,'Thessaloniki',85);
INSERT INTO `states` VALUES (1469,'Trikala',85);
INSERT INTO `states` VALUES (1470,'Voiotia',85);
INSERT INTO `states` VALUES (1471,'West Greece',85);
INSERT INTO `states` VALUES (1472,'Xanthi',85);
INSERT INTO `states` VALUES (1473,'Zakinthos',85);
INSERT INTO `states` VALUES (1474,'Aasiaat',86);
INSERT INTO `states` VALUES (1475,'Ammassalik',86);
INSERT INTO `states` VALUES (1476,'Illoqqortoormiut',86);
INSERT INTO `states` VALUES (1477,'Ilulissat',86);
INSERT INTO `states` VALUES (1478,'Ivittuut',86);
INSERT INTO `states` VALUES (1479,'Kangaatsiaq',86);
INSERT INTO `states` VALUES (1480,'Maniitsoq',86);
INSERT INTO `states` VALUES (1481,'Nanortalik',86);
INSERT INTO `states` VALUES (1482,'Narsaq',86);
INSERT INTO `states` VALUES (1483,'Nuuk',86);
INSERT INTO `states` VALUES (1484,'Paamiut',86);
INSERT INTO `states` VALUES (1485,'Qaanaaq',86);
INSERT INTO `states` VALUES (1486,'Qaqortoq',86);
INSERT INTO `states` VALUES (1487,'Qasigiannguit',86);
INSERT INTO `states` VALUES (1488,'Qeqertarsuaq',86);
INSERT INTO `states` VALUES (1489,'Sisimiut',86);
INSERT INTO `states` VALUES (1490,'Udenfor kommunal inddeling',86);
INSERT INTO `states` VALUES (1491,'Upernavik',86);
INSERT INTO `states` VALUES (1492,'Uummannaq',86);
INSERT INTO `states` VALUES (1493,'Carriacou-Petite Martinique',87);
INSERT INTO `states` VALUES (1494,'Saint Andrew',87);
INSERT INTO `states` VALUES (1495,'Saint Davids',87);
INSERT INTO `states` VALUES (1496,'Saint George\'s',87);
INSERT INTO `states` VALUES (1497,'Saint John',87);
INSERT INTO `states` VALUES (1498,'Saint Mark',87);
INSERT INTO `states` VALUES (1499,'Saint Patrick',87);
INSERT INTO `states` VALUES (1500,'Basse-Terre',88);
INSERT INTO `states` VALUES (1501,'Grande-Terre',88);
INSERT INTO `states` VALUES (1502,'Iles des Saintes',88);
INSERT INTO `states` VALUES (1503,'La Desirade',88);
INSERT INTO `states` VALUES (1504,'Marie-Galante',88);
INSERT INTO `states` VALUES (1505,'Saint Barthelemy',88);
INSERT INTO `states` VALUES (1506,'Saint Martin',88);
INSERT INTO `states` VALUES (1507,'Agana Heights',89);
INSERT INTO `states` VALUES (1508,'Agat',89);
INSERT INTO `states` VALUES (1509,'Barrigada',89);
INSERT INTO `states` VALUES (1510,'Chalan-Pago-Ordot',89);
INSERT INTO `states` VALUES (1511,'Dededo',89);
INSERT INTO `states` VALUES (1512,'Hagatna',89);
INSERT INTO `states` VALUES (1513,'Inarajan',89);
INSERT INTO `states` VALUES (1514,'Mangilao',89);
INSERT INTO `states` VALUES (1515,'Merizo',89);
INSERT INTO `states` VALUES (1516,'Mongmong-Toto-Maite',89);
INSERT INTO `states` VALUES (1517,'Santa Rita',89);
INSERT INTO `states` VALUES (1518,'Sinajana',89);
INSERT INTO `states` VALUES (1519,'Talofofo',89);
INSERT INTO `states` VALUES (1520,'Tamuning',89);
INSERT INTO `states` VALUES (1521,'Yigo',89);
INSERT INTO `states` VALUES (1522,'Yona',89);
INSERT INTO `states` VALUES (1523,'Alta Verapaz',90);
INSERT INTO `states` VALUES (1524,'Baja Verapaz',90);
INSERT INTO `states` VALUES (1525,'Chimaltenango',90);
INSERT INTO `states` VALUES (1526,'Chiquimula',90);
INSERT INTO `states` VALUES (1527,'El Progreso',90);
INSERT INTO `states` VALUES (1528,'Escuintla',90);
INSERT INTO `states` VALUES (1529,'Guatemala',90);
INSERT INTO `states` VALUES (1530,'Huehuetenango',90);
INSERT INTO `states` VALUES (1531,'Izabal',90);
INSERT INTO `states` VALUES (1532,'Jalapa',90);
INSERT INTO `states` VALUES (1533,'Jutiapa',90);
INSERT INTO `states` VALUES (1534,'Peten',90);
INSERT INTO `states` VALUES (1535,'Quezaltenango',90);
INSERT INTO `states` VALUES (1536,'Quiche',90);
INSERT INTO `states` VALUES (1537,'Retalhuleu',90);
INSERT INTO `states` VALUES (1538,'Sacatepequez',90);
INSERT INTO `states` VALUES (1539,'San Marcos',90);
INSERT INTO `states` VALUES (1540,'Santa Rosa',90);
INSERT INTO `states` VALUES (1541,'Solola',90);
INSERT INTO `states` VALUES (1542,'Suchitepequez',90);
INSERT INTO `states` VALUES (1543,'Totonicapan',90);
INSERT INTO `states` VALUES (1544,'Zacapa',90);
INSERT INTO `states` VALUES (1545,'Alderney',91);
INSERT INTO `states` VALUES (1546,'Castel',91);
INSERT INTO `states` VALUES (1547,'Forest',91);
INSERT INTO `states` VALUES (1548,'Saint Andrew',91);
INSERT INTO `states` VALUES (1549,'Saint Martin',91);
INSERT INTO `states` VALUES (1550,'Saint Peter Port',91);
INSERT INTO `states` VALUES (1551,'Saint Pierre du Bois',91);
INSERT INTO `states` VALUES (1552,'Saint Sampson',91);
INSERT INTO `states` VALUES (1553,'Saint Saviour',91);
INSERT INTO `states` VALUES (1554,'Sark',91);
INSERT INTO `states` VALUES (1555,'Torteval',91);
INSERT INTO `states` VALUES (1556,'Vale',91);
INSERT INTO `states` VALUES (1557,'Beyla',92);
INSERT INTO `states` VALUES (1558,'Boffa',92);
INSERT INTO `states` VALUES (1559,'Boke',92);
INSERT INTO `states` VALUES (1560,'Conakry',92);
INSERT INTO `states` VALUES (1561,'Coyah',92);
INSERT INTO `states` VALUES (1562,'Dabola',92);
INSERT INTO `states` VALUES (1563,'Dalaba',92);
INSERT INTO `states` VALUES (1564,'Dinguiraye',92);
INSERT INTO `states` VALUES (1565,'Faranah',92);
INSERT INTO `states` VALUES (1566,'Forecariah',92);
INSERT INTO `states` VALUES (1567,'Fria',92);
INSERT INTO `states` VALUES (1568,'Gaoual',92);
INSERT INTO `states` VALUES (1569,'Gueckedou',92);
INSERT INTO `states` VALUES (1570,'Kankan',92);
INSERT INTO `states` VALUES (1571,'Kerouane',92);
INSERT INTO `states` VALUES (1572,'Kindia',92);
INSERT INTO `states` VALUES (1573,'Kissidougou',92);
INSERT INTO `states` VALUES (1574,'Koubia',92);
INSERT INTO `states` VALUES (1575,'Koundara',92);
INSERT INTO `states` VALUES (1576,'Kouroussa',92);
INSERT INTO `states` VALUES (1577,'Labe',92);
INSERT INTO `states` VALUES (1578,'Lola',92);
INSERT INTO `states` VALUES (1579,'Macenta',92);
INSERT INTO `states` VALUES (1580,'Mali',92);
INSERT INTO `states` VALUES (1581,'Mamou',92);
INSERT INTO `states` VALUES (1582,'Mandiana',92);
INSERT INTO `states` VALUES (1583,'Nzerekore',92);
INSERT INTO `states` VALUES (1584,'Pita',92);
INSERT INTO `states` VALUES (1585,'Siguiri',92);
INSERT INTO `states` VALUES (1586,'Telimele',92);
INSERT INTO `states` VALUES (1587,'Tougue',92);
INSERT INTO `states` VALUES (1588,'Yomou',92);
INSERT INTO `states` VALUES (1589,'Bafata',93);
INSERT INTO `states` VALUES (1590,'Bissau',93);
INSERT INTO `states` VALUES (1591,'Bolama',93);
INSERT INTO `states` VALUES (1592,'Cacheu',93);
INSERT INTO `states` VALUES (1593,'Gabu',93);
INSERT INTO `states` VALUES (1594,'Oio',93);
INSERT INTO `states` VALUES (1595,'Quinara',93);
INSERT INTO `states` VALUES (1596,'Tombali',93);
INSERT INTO `states` VALUES (1597,'Barima-Waini',94);
INSERT INTO `states` VALUES (1598,'Cuyuni-Mazaruni',94);
INSERT INTO `states` VALUES (1599,'Demerara-Mahaica',94);
INSERT INTO `states` VALUES (1600,'East Berbice-Corentyne',94);
INSERT INTO `states` VALUES (1601,'Essequibo Islands-West Demerar',94);
INSERT INTO `states` VALUES (1602,'Mahaica-Berbice',94);
INSERT INTO `states` VALUES (1603,'Pomeroon-Supenaam',94);
INSERT INTO `states` VALUES (1604,'Potaro-Siparuni',94);
INSERT INTO `states` VALUES (1605,'Upper Demerara-Berbice',94);
INSERT INTO `states` VALUES (1606,'Upper Takutu-Upper Essequibo',94);
INSERT INTO `states` VALUES (1607,'Artibonite',95);
INSERT INTO `states` VALUES (1608,'Centre',95);
INSERT INTO `states` VALUES (1609,'Grand\'Anse',95);
INSERT INTO `states` VALUES (1610,'Nord',95);
INSERT INTO `states` VALUES (1611,'Nord-Est',95);
INSERT INTO `states` VALUES (1612,'Nord-Ouest',95);
INSERT INTO `states` VALUES (1613,'Ouest',95);
INSERT INTO `states` VALUES (1614,'Sud',95);
INSERT INTO `states` VALUES (1615,'Sud-Est',95);
INSERT INTO `states` VALUES (1616,'Heard and McDonald Islands',96);
INSERT INTO `states` VALUES (1617,'Atlantida',97);
INSERT INTO `states` VALUES (1618,'Choluteca',97);
INSERT INTO `states` VALUES (1619,'Colon',97);
INSERT INTO `states` VALUES (1620,'Comayagua',97);
INSERT INTO `states` VALUES (1621,'Copan',97);
INSERT INTO `states` VALUES (1622,'Cortes',97);
INSERT INTO `states` VALUES (1623,'Distrito Central',97);
INSERT INTO `states` VALUES (1624,'El Paraiso',97);
INSERT INTO `states` VALUES (1625,'Francisco Morazan',97);
INSERT INTO `states` VALUES (1626,'Gracias a Dios',97);
INSERT INTO `states` VALUES (1627,'Intibuca',97);
INSERT INTO `states` VALUES (1628,'Islas de la Bahia',97);
INSERT INTO `states` VALUES (1629,'La Paz',97);
INSERT INTO `states` VALUES (1630,'Lempira',97);
INSERT INTO `states` VALUES (1631,'Ocotepeque',97);
INSERT INTO `states` VALUES (1632,'Olancho',97);
INSERT INTO `states` VALUES (1633,'Santa Barbara',97);
INSERT INTO `states` VALUES (1634,'Valle',97);
INSERT INTO `states` VALUES (1635,'Yoro',97);
INSERT INTO `states` VALUES (1636,'Hong Kong',98);
INSERT INTO `states` VALUES (1637,'Bacs-Kiskun',99);
INSERT INTO `states` VALUES (1638,'Baranya',99);
INSERT INTO `states` VALUES (1639,'Bekes',99);
INSERT INTO `states` VALUES (1640,'Borsod-Abauj-Zemplen',99);
INSERT INTO `states` VALUES (1641,'Budapest',99);
INSERT INTO `states` VALUES (1642,'Csongrad',99);
INSERT INTO `states` VALUES (1643,'Fejer',99);
INSERT INTO `states` VALUES (1644,'Gyor-Moson-Sopron',99);
INSERT INTO `states` VALUES (1645,'Hajdu-Bihar',99);
INSERT INTO `states` VALUES (1646,'Heves',99);
INSERT INTO `states` VALUES (1647,'Jasz-Nagykun-Szolnok',99);
INSERT INTO `states` VALUES (1648,'Komarom-Esztergom',99);
INSERT INTO `states` VALUES (1649,'Nograd',99);
INSERT INTO `states` VALUES (1650,'Pest',99);
INSERT INTO `states` VALUES (1651,'Somogy',99);
INSERT INTO `states` VALUES (1652,'Szabolcs-Szatmar-Bereg',99);
INSERT INTO `states` VALUES (1653,'Tolna',99);
INSERT INTO `states` VALUES (1654,'Vas',99);
INSERT INTO `states` VALUES (1655,'Veszprem',99);
INSERT INTO `states` VALUES (1656,'Zala',99);
INSERT INTO `states` VALUES (1657,'Austurland',100);
INSERT INTO `states` VALUES (1658,'Gullbringusysla',100);
INSERT INTO `states` VALUES (1659,'Hofu borgarsva i',100);
INSERT INTO `states` VALUES (1660,'Nor urland eystra',100);
INSERT INTO `states` VALUES (1661,'Nor urland vestra',100);
INSERT INTO `states` VALUES (1662,'Su urland',100);
INSERT INTO `states` VALUES (1663,'Su urnes',100);
INSERT INTO `states` VALUES (1664,'Vestfir ir',100);
INSERT INTO `states` VALUES (1665,'Vesturland',100);
INSERT INTO `states` VALUES (1666,'Aceh',102);
INSERT INTO `states` VALUES (1667,'Bali',102);
INSERT INTO `states` VALUES (1668,'Bangka-Belitung',102);
INSERT INTO `states` VALUES (1669,'Banten',102);
INSERT INTO `states` VALUES (1670,'Bengkulu',102);
INSERT INTO `states` VALUES (1671,'Gandaria',102);
INSERT INTO `states` VALUES (1672,'Gorontalo',102);
INSERT INTO `states` VALUES (1673,'Jakarta',102);
INSERT INTO `states` VALUES (1674,'Jambi',102);
INSERT INTO `states` VALUES (1675,'Jawa Barat',102);
INSERT INTO `states` VALUES (1676,'Jawa Tengah',102);
INSERT INTO `states` VALUES (1677,'Jawa Timur',102);
INSERT INTO `states` VALUES (1678,'Kalimantan Barat',102);
INSERT INTO `states` VALUES (1679,'Kalimantan Selatan',102);
INSERT INTO `states` VALUES (1680,'Kalimantan Tengah',102);
INSERT INTO `states` VALUES (1681,'Kalimantan Timur',102);
INSERT INTO `states` VALUES (1682,'Kendal',102);
INSERT INTO `states` VALUES (1683,'Lampung',102);
INSERT INTO `states` VALUES (1684,'Maluku',102);
INSERT INTO `states` VALUES (1685,'Maluku Utara',102);
INSERT INTO `states` VALUES (1686,'Nusa Tenggara Barat',102);
INSERT INTO `states` VALUES (1687,'Nusa Tenggara Timur',102);
INSERT INTO `states` VALUES (1688,'Papua',102);
INSERT INTO `states` VALUES (1689,'Riau',102);
INSERT INTO `states` VALUES (1690,'Riau Kepulauan',102);
INSERT INTO `states` VALUES (1691,'Solo',102);
INSERT INTO `states` VALUES (1692,'Sulawesi Selatan',102);
INSERT INTO `states` VALUES (1693,'Sulawesi Tengah',102);
INSERT INTO `states` VALUES (1694,'Sulawesi Tenggara',102);
INSERT INTO `states` VALUES (1695,'Sulawesi Utara',102);
INSERT INTO `states` VALUES (1696,'Sumatera Barat',102);
INSERT INTO `states` VALUES (1697,'Sumatera Selatan',102);
INSERT INTO `states` VALUES (1698,'Sumatera Utara',102);
INSERT INTO `states` VALUES (1699,'Yogyakarta',102);
INSERT INTO `states` VALUES (1700,'Ardabil',103);
INSERT INTO `states` VALUES (1701,'Azarbayjan-e Bakhtari',103);
INSERT INTO `states` VALUES (1702,'Azarbayjan-e Khavari',103);
INSERT INTO `states` VALUES (1703,'Bushehr',103);
INSERT INTO `states` VALUES (1704,'Chahar Mahal-e Bakhtiari',103);
INSERT INTO `states` VALUES (1705,'Esfahan',103);
INSERT INTO `states` VALUES (1706,'Fars',103);
INSERT INTO `states` VALUES (1707,'Gilan',103);
INSERT INTO `states` VALUES (1708,'Golestan',103);
INSERT INTO `states` VALUES (1709,'Hamadan',103);
INSERT INTO `states` VALUES (1710,'Hormozgan',103);
INSERT INTO `states` VALUES (1711,'Ilam',103);
INSERT INTO `states` VALUES (1712,'Kerman',103);
INSERT INTO `states` VALUES (1713,'Kermanshah',103);
INSERT INTO `states` VALUES (1714,'Khorasan',103);
INSERT INTO `states` VALUES (1715,'Khuzestan',103);
INSERT INTO `states` VALUES (1716,'Kohgiluyeh-e Boyerahmad',103);
INSERT INTO `states` VALUES (1717,'Kordestan',103);
INSERT INTO `states` VALUES (1718,'Lorestan',103);
INSERT INTO `states` VALUES (1719,'Markazi',103);
INSERT INTO `states` VALUES (1720,'Mazandaran',103);
INSERT INTO `states` VALUES (1721,'Ostan-e Esfahan',103);
INSERT INTO `states` VALUES (1722,'Qazvin',103);
INSERT INTO `states` VALUES (1723,'Qom',103);
INSERT INTO `states` VALUES (1724,'Semnan',103);
INSERT INTO `states` VALUES (1725,'Sistan-e Baluchestan',103);
INSERT INTO `states` VALUES (1726,'Tehran',103);
INSERT INTO `states` VALUES (1727,'Yazd',103);
INSERT INTO `states` VALUES (1728,'Zanjan',103);
INSERT INTO `states` VALUES (1729,'Babil',104);
INSERT INTO `states` VALUES (1730,'Baghdad',104);
INSERT INTO `states` VALUES (1731,'Dahuk',104);
INSERT INTO `states` VALUES (1732,'Dhi Qar',104);
INSERT INTO `states` VALUES (1733,'Diyala',104);
INSERT INTO `states` VALUES (1734,'Erbil',104);
INSERT INTO `states` VALUES (1735,'Irbil',104);
INSERT INTO `states` VALUES (1736,'Karbala',104);
INSERT INTO `states` VALUES (1737,'Kurdistan',104);
INSERT INTO `states` VALUES (1738,'Maysan',104);
INSERT INTO `states` VALUES (1739,'Ninawa',104);
INSERT INTO `states` VALUES (1740,'Salah-ad-Din',104);
INSERT INTO `states` VALUES (1741,'Wasit',104);
INSERT INTO `states` VALUES (1742,'al-Anbar',104);
INSERT INTO `states` VALUES (1743,'al-Basrah',104);
INSERT INTO `states` VALUES (1744,'al-Muthanna',104);
INSERT INTO `states` VALUES (1745,'al-Qadisiyah',104);
INSERT INTO `states` VALUES (1746,'an-Najaf',104);
INSERT INTO `states` VALUES (1747,'as-Sulaymaniyah',104);
INSERT INTO `states` VALUES (1748,'at-Ta\'mim',104);
INSERT INTO `states` VALUES (1749,'Armagh',105);
INSERT INTO `states` VALUES (1750,'Carlow',105);
INSERT INTO `states` VALUES (1751,'Cavan',105);
INSERT INTO `states` VALUES (1752,'Clare',105);
INSERT INTO `states` VALUES (1753,'Cork',105);
INSERT INTO `states` VALUES (1754,'Donegal',105);
INSERT INTO `states` VALUES (1755,'Dublin',105);
INSERT INTO `states` VALUES (1756,'Galway',105);
INSERT INTO `states` VALUES (1757,'Kerry',105);
INSERT INTO `states` VALUES (1758,'Kildare',105);
INSERT INTO `states` VALUES (1759,'Kilkenny',105);
INSERT INTO `states` VALUES (1760,'Laois',105);
INSERT INTO `states` VALUES (1761,'Leinster',105);
INSERT INTO `states` VALUES (1762,'Leitrim',105);
INSERT INTO `states` VALUES (1763,'Limerick',105);
INSERT INTO `states` VALUES (1764,'Loch Garman',105);
INSERT INTO `states` VALUES (1765,'Longford',105);
INSERT INTO `states` VALUES (1766,'Louth',105);
INSERT INTO `states` VALUES (1767,'Mayo',105);
INSERT INTO `states` VALUES (1768,'Meath',105);
INSERT INTO `states` VALUES (1769,'Monaghan',105);
INSERT INTO `states` VALUES (1770,'Offaly',105);
INSERT INTO `states` VALUES (1771,'Roscommon',105);
INSERT INTO `states` VALUES (1772,'Sligo',105);
INSERT INTO `states` VALUES (1773,'Tipperary North Riding',105);
INSERT INTO `states` VALUES (1774,'Tipperary South Riding',105);
INSERT INTO `states` VALUES (1775,'Ulster',105);
INSERT INTO `states` VALUES (1776,'Waterford',105);
INSERT INTO `states` VALUES (1777,'Westmeath',105);
INSERT INTO `states` VALUES (1778,'Wexford',105);
INSERT INTO `states` VALUES (1779,'Wicklow',105);
INSERT INTO `states` VALUES (1780,'Beit Hanania',106);
INSERT INTO `states` VALUES (1781,'Ben Gurion Airport',106);
INSERT INTO `states` VALUES (1782,'Bethlehem',106);
INSERT INTO `states` VALUES (1783,'Caesarea',106);
INSERT INTO `states` VALUES (1784,'Centre',106);
INSERT INTO `states` VALUES (1785,'Gaza',106);
INSERT INTO `states` VALUES (1786,'Hadaron',106);
INSERT INTO `states` VALUES (1787,'Haifa District',106);
INSERT INTO `states` VALUES (1788,'Hamerkaz',106);
INSERT INTO `states` VALUES (1789,'Hazafon',106);
INSERT INTO `states` VALUES (1790,'Hebron',106);
INSERT INTO `states` VALUES (1791,'Jaffa',106);
INSERT INTO `states` VALUES (1792,'Jerusalem',106);
INSERT INTO `states` VALUES (1793,'Khefa',106);
INSERT INTO `states` VALUES (1794,'Kiryat Yam',106);
INSERT INTO `states` VALUES (1795,'Lower Galilee',106);
INSERT INTO `states` VALUES (1796,'Qalqilya',106);
INSERT INTO `states` VALUES (1797,'Talme Elazar',106);
INSERT INTO `states` VALUES (1798,'Tel Aviv',106);
INSERT INTO `states` VALUES (1799,'Tsafon',106);
INSERT INTO `states` VALUES (1800,'Umm El Fahem',106);
INSERT INTO `states` VALUES (1801,'Yerushalayim',106);
INSERT INTO `states` VALUES (1802,'Abruzzi',107);
INSERT INTO `states` VALUES (1803,'Abruzzo',107);
INSERT INTO `states` VALUES (1804,'Agrigento',107);
INSERT INTO `states` VALUES (1805,'Alessandria',107);
INSERT INTO `states` VALUES (1806,'Ancona',107);
INSERT INTO `states` VALUES (1807,'Arezzo',107);
INSERT INTO `states` VALUES (1808,'Ascoli Piceno',107);
INSERT INTO `states` VALUES (1809,'Asti',107);
INSERT INTO `states` VALUES (1810,'Avellino',107);
INSERT INTO `states` VALUES (1811,'Bari',107);
INSERT INTO `states` VALUES (1812,'Basilicata',107);
INSERT INTO `states` VALUES (1813,'Belluno',107);
INSERT INTO `states` VALUES (1814,'Benevento',107);
INSERT INTO `states` VALUES (1815,'Bergamo',107);
INSERT INTO `states` VALUES (1816,'Biella',107);
INSERT INTO `states` VALUES (1817,'Bologna',107);
INSERT INTO `states` VALUES (1818,'Bolzano',107);
INSERT INTO `states` VALUES (1819,'Brescia',107);
INSERT INTO `states` VALUES (1820,'Brindisi',107);
INSERT INTO `states` VALUES (1821,'Calabria',107);
INSERT INTO `states` VALUES (1822,'Campania',107);
INSERT INTO `states` VALUES (1823,'Cartoceto',107);
INSERT INTO `states` VALUES (1824,'Caserta',107);
INSERT INTO `states` VALUES (1825,'Catania',107);
INSERT INTO `states` VALUES (1826,'Chieti',107);
INSERT INTO `states` VALUES (1827,'Como',107);
INSERT INTO `states` VALUES (1828,'Cosenza',107);
INSERT INTO `states` VALUES (1829,'Cremona',107);
INSERT INTO `states` VALUES (1830,'Cuneo',107);
INSERT INTO `states` VALUES (1831,'Emilia-Romagna',107);
INSERT INTO `states` VALUES (1832,'Ferrara',107);
INSERT INTO `states` VALUES (1833,'Firenze',107);
INSERT INTO `states` VALUES (1834,'Florence',107);
INSERT INTO `states` VALUES (1835,'Forli-Cesena ',107);
INSERT INTO `states` VALUES (1836,'Friuli-Venezia Giulia',107);
INSERT INTO `states` VALUES (1837,'Frosinone',107);
INSERT INTO `states` VALUES (1838,'Genoa',107);
INSERT INTO `states` VALUES (1839,'Gorizia',107);
INSERT INTO `states` VALUES (1840,'L\'Aquila',107);
INSERT INTO `states` VALUES (1841,'Lazio',107);
INSERT INTO `states` VALUES (1842,'Lecce',107);
INSERT INTO `states` VALUES (1843,'Lecco',107);
INSERT INTO `states` VALUES (1845,'Liguria',107);
INSERT INTO `states` VALUES (1846,'Lodi',107);
INSERT INTO `states` VALUES (1847,'Lombardia',107);
INSERT INTO `states` VALUES (1848,'Lombardy',107);
INSERT INTO `states` VALUES (1849,'Macerata',107);
INSERT INTO `states` VALUES (1850,'Mantova',107);
INSERT INTO `states` VALUES (1851,'Marche',107);
INSERT INTO `states` VALUES (1852,'Messina',107);
INSERT INTO `states` VALUES (1853,'Milan',107);
INSERT INTO `states` VALUES (1854,'Modena',107);
INSERT INTO `states` VALUES (1855,'Molise',107);
INSERT INTO `states` VALUES (1856,'Molteno',107);
INSERT INTO `states` VALUES (1857,'Montenegro',107);
INSERT INTO `states` VALUES (1858,'Monza and Brianza',107);
INSERT INTO `states` VALUES (1859,'Naples',107);
INSERT INTO `states` VALUES (1860,'Novara',107);
INSERT INTO `states` VALUES (1861,'Padova',107);
INSERT INTO `states` VALUES (1862,'Parma',107);
INSERT INTO `states` VALUES (1863,'Pavia',107);
INSERT INTO `states` VALUES (1864,'Perugia',107);
INSERT INTO `states` VALUES (1865,'Pesaro-Urbino',107);
INSERT INTO `states` VALUES (1866,'Piacenza',107);
INSERT INTO `states` VALUES (1867,'Piedmont',107);
INSERT INTO `states` VALUES (1868,'Piemonte',107);
INSERT INTO `states` VALUES (1869,'Pisa',107);
INSERT INTO `states` VALUES (1870,'Pordenone',107);
INSERT INTO `states` VALUES (1871,'Potenza',107);
INSERT INTO `states` VALUES (1872,'Puglia',107);
INSERT INTO `states` VALUES (1873,'Reggio Emilia',107);
INSERT INTO `states` VALUES (1874,'Rimini',107);
INSERT INTO `states` VALUES (1875,'Roma',107);
INSERT INTO `states` VALUES (1876,'Salerno',107);
INSERT INTO `states` VALUES (1877,'Sardegna',107);
INSERT INTO `states` VALUES (1878,'Sassari',107);
INSERT INTO `states` VALUES (1879,'Savona',107);
INSERT INTO `states` VALUES (1880,'Sicilia',107);
INSERT INTO `states` VALUES (1881,'Siena',107);
INSERT INTO `states` VALUES (1882,'Sondrio',107);
INSERT INTO `states` VALUES (1883,'South Tyrol',107);
INSERT INTO `states` VALUES (1884,'Taranto',107);
INSERT INTO `states` VALUES (1885,'Teramo',107);
INSERT INTO `states` VALUES (1886,'Torino',107);
INSERT INTO `states` VALUES (1887,'Toscana',107);
INSERT INTO `states` VALUES (1888,'Trapani',107);
INSERT INTO `states` VALUES (1889,'Trentino-Alto Adige',107);
INSERT INTO `states` VALUES (1890,'Trento',107);
INSERT INTO `states` VALUES (1891,'Treviso',107);
INSERT INTO `states` VALUES (1892,'Udine',107);
INSERT INTO `states` VALUES (1893,'Umbria',107);
INSERT INTO `states` VALUES (1894,'Valle d\'Aosta',107);
INSERT INTO `states` VALUES (1895,'Varese',107);
INSERT INTO `states` VALUES (1896,'Veneto',107);
INSERT INTO `states` VALUES (1897,'Venezia',107);
INSERT INTO `states` VALUES (1898,'Verbano-Cusio-Ossola',107);
INSERT INTO `states` VALUES (1899,'Vercelli',107);
INSERT INTO `states` VALUES (1900,'Verona',107);
INSERT INTO `states` VALUES (1901,'Vicenza',107);
INSERT INTO `states` VALUES (1902,'Viterbo',107);
INSERT INTO `states` VALUES (1903,'Buxoro Viloyati',108);
INSERT INTO `states` VALUES (1904,'Clarendon',108);
INSERT INTO `states` VALUES (1905,'Hanover',108);
INSERT INTO `states` VALUES (1906,'Kingston',108);
INSERT INTO `states` VALUES (1907,'Manchester',108);
INSERT INTO `states` VALUES (1908,'Portland',108);
INSERT INTO `states` VALUES (1909,'Saint Andrews',108);
INSERT INTO `states` VALUES (1910,'Saint Ann',108);
INSERT INTO `states` VALUES (1911,'Saint Catherine',108);
INSERT INTO `states` VALUES (1912,'Saint Elizabeth',108);
INSERT INTO `states` VALUES (1913,'Saint James',108);
INSERT INTO `states` VALUES (1914,'Saint Mary',108);
INSERT INTO `states` VALUES (1915,'Saint Thomas',108);
INSERT INTO `states` VALUES (1916,'Trelawney',108);
INSERT INTO `states` VALUES (1917,'Westmoreland',108);
INSERT INTO `states` VALUES (1918,'Aichi',109);
INSERT INTO `states` VALUES (1919,'Akita',109);
INSERT INTO `states` VALUES (1920,'Aomori',109);
INSERT INTO `states` VALUES (1921,'Chiba',109);
INSERT INTO `states` VALUES (1922,'Ehime',109);
INSERT INTO `states` VALUES (1923,'Fukui',109);
INSERT INTO `states` VALUES (1924,'Fukuoka',109);
INSERT INTO `states` VALUES (1925,'Fukushima',109);
INSERT INTO `states` VALUES (1926,'Gifu',109);
INSERT INTO `states` VALUES (1927,'Gumma',109);
INSERT INTO `states` VALUES (1928,'Hiroshima',109);
INSERT INTO `states` VALUES (1929,'Hokkaido',109);
INSERT INTO `states` VALUES (1930,'Hyogo',109);
INSERT INTO `states` VALUES (1931,'Ibaraki',109);
INSERT INTO `states` VALUES (1932,'Ishikawa',109);
INSERT INTO `states` VALUES (1933,'Iwate',109);
INSERT INTO `states` VALUES (1934,'Kagawa',109);
INSERT INTO `states` VALUES (1935,'Kagoshima',109);
INSERT INTO `states` VALUES (1936,'Kanagawa',109);
INSERT INTO `states` VALUES (1937,'Kanto',109);
INSERT INTO `states` VALUES (1938,'Kochi',109);
INSERT INTO `states` VALUES (1939,'Kumamoto',109);
INSERT INTO `states` VALUES (1940,'Kyoto',109);
INSERT INTO `states` VALUES (1941,'Mie',109);
INSERT INTO `states` VALUES (1942,'Miyagi',109);
INSERT INTO `states` VALUES (1943,'Miyazaki',109);
INSERT INTO `states` VALUES (1944,'Nagano',109);
INSERT INTO `states` VALUES (1945,'Nagasaki',109);
INSERT INTO `states` VALUES (1946,'Nara',109);
INSERT INTO `states` VALUES (1947,'Niigata',109);
INSERT INTO `states` VALUES (1948,'Oita',109);
INSERT INTO `states` VALUES (1949,'Okayama',109);
INSERT INTO `states` VALUES (1950,'Okinawa',109);
INSERT INTO `states` VALUES (1951,'Osaka',109);
INSERT INTO `states` VALUES (1952,'Saga',109);
INSERT INTO `states` VALUES (1953,'Saitama',109);
INSERT INTO `states` VALUES (1954,'Shiga',109);
INSERT INTO `states` VALUES (1955,'Shimane',109);
INSERT INTO `states` VALUES (1956,'Shizuoka',109);
INSERT INTO `states` VALUES (1957,'Tochigi',109);
INSERT INTO `states` VALUES (1958,'Tokushima',109);
INSERT INTO `states` VALUES (1959,'Tokyo',109);
INSERT INTO `states` VALUES (1960,'Tottori',109);
INSERT INTO `states` VALUES (1961,'Toyama',109);
INSERT INTO `states` VALUES (1962,'Wakayama',109);
INSERT INTO `states` VALUES (1963,'Yamagata',109);
INSERT INTO `states` VALUES (1964,'Yamaguchi',109);
INSERT INTO `states` VALUES (1965,'Yamanashi',109);
INSERT INTO `states` VALUES (1966,'Grouville',110);
INSERT INTO `states` VALUES (1967,'Saint Brelade',110);
INSERT INTO `states` VALUES (1968,'Saint Clement',110);
INSERT INTO `states` VALUES (1969,'Saint Helier',110);
INSERT INTO `states` VALUES (1970,'Saint John',110);
INSERT INTO `states` VALUES (1971,'Saint Lawrence',110);
INSERT INTO `states` VALUES (1972,'Saint Martin',110);
INSERT INTO `states` VALUES (1973,'Saint Mary',110);
INSERT INTO `states` VALUES (1974,'Saint Peter',110);
INSERT INTO `states` VALUES (1975,'Saint Saviour',110);
INSERT INTO `states` VALUES (1976,'Trinity',110);
INSERT INTO `states` VALUES (1977,'\'Ajlun',111);
INSERT INTO `states` VALUES (1978,'Amman',111);
INSERT INTO `states` VALUES (1979,'Irbid',111);
INSERT INTO `states` VALUES (1980,'Jarash',111);
INSERT INTO `states` VALUES (1981,'Ma\'an',111);
INSERT INTO `states` VALUES (1982,'Madaba',111);
INSERT INTO `states` VALUES (1983,'al-\'Aqabah',111);
INSERT INTO `states` VALUES (1984,'al-Balqa\'',111);
INSERT INTO `states` VALUES (1985,'al-Karak',111);
INSERT INTO `states` VALUES (1986,'al-Mafraq',111);
INSERT INTO `states` VALUES (1987,'at-Tafilah',111);
INSERT INTO `states` VALUES (1988,'az-Zarqa\'',111);
INSERT INTO `states` VALUES (1989,'Akmecet',112);
INSERT INTO `states` VALUES (1990,'Akmola',112);
INSERT INTO `states` VALUES (1991,'Aktobe',112);
INSERT INTO `states` VALUES (1992,'Almati',112);
INSERT INTO `states` VALUES (1993,'Atirau',112);
INSERT INTO `states` VALUES (1994,'Batis Kazakstan',112);
INSERT INTO `states` VALUES (1995,'Burlinsky Region',112);
INSERT INTO `states` VALUES (1996,'Karagandi',112);
INSERT INTO `states` VALUES (1997,'Kostanay',112);
INSERT INTO `states` VALUES (1998,'Mankistau',112);
INSERT INTO `states` VALUES (1999,'Ontustik Kazakstan',112);
INSERT INTO `states` VALUES (2000,'Pavlodar',112);
INSERT INTO `states` VALUES (2001,'Sigis Kazakstan',112);
INSERT INTO `states` VALUES (2002,'Soltustik Kazakstan',112);
INSERT INTO `states` VALUES (2003,'Taraz',112);
INSERT INTO `states` VALUES (2004,'Central',113);
INSERT INTO `states` VALUES (2005,'Coast',113);
INSERT INTO `states` VALUES (2006,'Eastern',113);
INSERT INTO `states` VALUES (2007,'Nairobi',113);
INSERT INTO `states` VALUES (2008,'North Eastern',113);
INSERT INTO `states` VALUES (2009,'Nyanza',113);
INSERT INTO `states` VALUES (2010,'Rift Valley',113);
INSERT INTO `states` VALUES (2011,'Western',113);
INSERT INTO `states` VALUES (2012,'Abaiang',114);
INSERT INTO `states` VALUES (2013,'Abemana',114);
INSERT INTO `states` VALUES (2014,'Aranuka',114);
INSERT INTO `states` VALUES (2015,'Arorae',114);
INSERT INTO `states` VALUES (2016,'Banaba',114);
INSERT INTO `states` VALUES (2017,'Beru',114);
INSERT INTO `states` VALUES (2018,'Butaritari',114);
INSERT INTO `states` VALUES (2019,'Kiritimati',114);
INSERT INTO `states` VALUES (2020,'Kuria',114);
INSERT INTO `states` VALUES (2021,'Maiana',114);
INSERT INTO `states` VALUES (2022,'Makin',114);
INSERT INTO `states` VALUES (2023,'Marakei',114);
INSERT INTO `states` VALUES (2024,'Nikunau',114);
INSERT INTO `states` VALUES (2025,'Nonouti',114);
INSERT INTO `states` VALUES (2026,'Onotoa',114);
INSERT INTO `states` VALUES (2027,'Phoenix Islands',114);
INSERT INTO `states` VALUES (2028,'Tabiteuea North',114);
INSERT INTO `states` VALUES (2029,'Tabiteuea South',114);
INSERT INTO `states` VALUES (2030,'Tabuaeran',114);
INSERT INTO `states` VALUES (2031,'Tamana',114);
INSERT INTO `states` VALUES (2032,'Tarawa North',114);
INSERT INTO `states` VALUES (2033,'Tarawa South',114);
INSERT INTO `states` VALUES (2034,'Teraina',114);
INSERT INTO `states` VALUES (2035,'Chagangdo',115);
INSERT INTO `states` VALUES (2036,'Hamgyeongbukto',115);
INSERT INTO `states` VALUES (2037,'Hamgyeongnamdo',115);
INSERT INTO `states` VALUES (2038,'Hwanghaebukto',115);
INSERT INTO `states` VALUES (2039,'Hwanghaenamdo',115);
INSERT INTO `states` VALUES (2040,'Kaeseong',115);
INSERT INTO `states` VALUES (2041,'Kangweon',115);
INSERT INTO `states` VALUES (2042,'Nampo',115);
INSERT INTO `states` VALUES (2043,'Pyeonganbukto',115);
INSERT INTO `states` VALUES (2044,'Pyeongannamdo',115);
INSERT INTO `states` VALUES (2045,'Pyeongyang',115);
INSERT INTO `states` VALUES (2046,'Yanggang',115);
INSERT INTO `states` VALUES (2047,'Busan',116);
INSERT INTO `states` VALUES (2048,'Cheju',116);
INSERT INTO `states` VALUES (2049,'Chollabuk',116);
INSERT INTO `states` VALUES (2050,'Chollanam',116);
INSERT INTO `states` VALUES (2051,'Chungbuk',116);
INSERT INTO `states` VALUES (2052,'Chungcheongbuk',116);
INSERT INTO `states` VALUES (2053,'Chungcheongnam',116);
INSERT INTO `states` VALUES (2054,'Chungnam',116);
INSERT INTO `states` VALUES (2055,'Daegu',116);
INSERT INTO `states` VALUES (2056,'Gangwon-do',116);
INSERT INTO `states` VALUES (2057,'Goyang-si',116);
INSERT INTO `states` VALUES (2058,'Gyeonggi-do',116);
INSERT INTO `states` VALUES (2059,'Gyeongsang ',116);
INSERT INTO `states` VALUES (2060,'Gyeongsangnam-do',116);
INSERT INTO `states` VALUES (2061,'Incheon',116);
INSERT INTO `states` VALUES (2062,'Jeju-Si',116);
INSERT INTO `states` VALUES (2063,'Jeonbuk',116);
INSERT INTO `states` VALUES (2064,'Kangweon',116);
INSERT INTO `states` VALUES (2065,'Kwangju',116);
INSERT INTO `states` VALUES (2066,'Kyeonggi',116);
INSERT INTO `states` VALUES (2067,'Kyeongsangbuk',116);
INSERT INTO `states` VALUES (2068,'Kyeongsangnam',116);
INSERT INTO `states` VALUES (2069,'Kyonggi-do',116);
INSERT INTO `states` VALUES (2070,'Kyungbuk-Do',116);
INSERT INTO `states` VALUES (2071,'Kyunggi-Do',116);
INSERT INTO `states` VALUES (2072,'Kyunggi-do',116);
INSERT INTO `states` VALUES (2073,'Pusan',116);
INSERT INTO `states` VALUES (2074,'Seoul',116);
INSERT INTO `states` VALUES (2075,'Sudogwon',116);
INSERT INTO `states` VALUES (2076,'Taegu',116);
INSERT INTO `states` VALUES (2077,'Taejeon',116);
INSERT INTO `states` VALUES (2078,'Taejon-gwangyoksi',116);
INSERT INTO `states` VALUES (2079,'Ulsan',116);
INSERT INTO `states` VALUES (2080,'Wonju',116);
INSERT INTO `states` VALUES (2081,'gwangyoksi',116);
INSERT INTO `states` VALUES (2082,'Al Asimah',117);
INSERT INTO `states` VALUES (2083,'Hawalli',117);
INSERT INTO `states` VALUES (2084,'Mishref',117);
INSERT INTO `states` VALUES (2085,'Qadesiya',117);
INSERT INTO `states` VALUES (2086,'Safat',117);
INSERT INTO `states` VALUES (2087,'Salmiya',117);
INSERT INTO `states` VALUES (2088,'al-Ahmadi',117);
INSERT INTO `states` VALUES (2089,'al-Farwaniyah',117);
INSERT INTO `states` VALUES (2090,'al-Jahra',117);
INSERT INTO `states` VALUES (2091,'al-Kuwayt',117);
INSERT INTO `states` VALUES (2092,'Batken',118);
INSERT INTO `states` VALUES (2093,'Bishkek',118);
INSERT INTO `states` VALUES (2094,'Chui',118);
INSERT INTO `states` VALUES (2095,'Issyk-Kul',118);
INSERT INTO `states` VALUES (2096,'Jalal-Abad',118);
INSERT INTO `states` VALUES (2097,'Naryn',118);
INSERT INTO `states` VALUES (2098,'Osh',118);
INSERT INTO `states` VALUES (2099,'Talas',118);
INSERT INTO `states` VALUES (2100,'Attopu',119);
INSERT INTO `states` VALUES (2101,'Bokeo',119);
INSERT INTO `states` VALUES (2102,'Bolikhamsay',119);
INSERT INTO `states` VALUES (2103,'Champasak',119);
INSERT INTO `states` VALUES (2104,'Houaphanh',119);
INSERT INTO `states` VALUES (2105,'Khammouane',119);
INSERT INTO `states` VALUES (2106,'Luang Nam Tha',119);
INSERT INTO `states` VALUES (2107,'Luang Prabang',119);
INSERT INTO `states` VALUES (2108,'Oudomxay',119);
INSERT INTO `states` VALUES (2109,'Phongsaly',119);
INSERT INTO `states` VALUES (2110,'Saravan',119);
INSERT INTO `states` VALUES (2111,'Savannakhet',119);
INSERT INTO `states` VALUES (2112,'Sekong',119);
INSERT INTO `states` VALUES (2113,'Viangchan Prefecture',119);
INSERT INTO `states` VALUES (2114,'Viangchan Province',119);
INSERT INTO `states` VALUES (2115,'Xaignabury',119);
INSERT INTO `states` VALUES (2116,'Xiang Khuang',119);
INSERT INTO `states` VALUES (2117,'Aizkraukles',120);
INSERT INTO `states` VALUES (2118,'Aluksnes',120);
INSERT INTO `states` VALUES (2119,'Balvu',120);
INSERT INTO `states` VALUES (2120,'Bauskas',120);
INSERT INTO `states` VALUES (2121,'Cesu',120);
INSERT INTO `states` VALUES (2122,'Daugavpils',120);
INSERT INTO `states` VALUES (2123,'Daugavpils City',120);
INSERT INTO `states` VALUES (2124,'Dobeles',120);
INSERT INTO `states` VALUES (2125,'Gulbenes',120);
INSERT INTO `states` VALUES (2126,'Jekabspils',120);
INSERT INTO `states` VALUES (2127,'Jelgava',120);
INSERT INTO `states` VALUES (2128,'Jelgavas',120);
INSERT INTO `states` VALUES (2129,'Jurmala City',120);
INSERT INTO `states` VALUES (2130,'Kraslavas',120);
INSERT INTO `states` VALUES (2131,'Kuldigas',120);
INSERT INTO `states` VALUES (2132,'Liepaja',120);
INSERT INTO `states` VALUES (2133,'Liepajas',120);
INSERT INTO `states` VALUES (2134,'Limbazhu',120);
INSERT INTO `states` VALUES (2135,'Ludzas',120);
INSERT INTO `states` VALUES (2136,'Madonas',120);
INSERT INTO `states` VALUES (2137,'Ogres',120);
INSERT INTO `states` VALUES (2138,'Preilu',120);
INSERT INTO `states` VALUES (2139,'Rezekne',120);
INSERT INTO `states` VALUES (2140,'Rezeknes',120);
INSERT INTO `states` VALUES (2141,'Riga',120);
INSERT INTO `states` VALUES (2142,'Rigas',120);
INSERT INTO `states` VALUES (2143,'Saldus',120);
INSERT INTO `states` VALUES (2144,'Talsu',120);
INSERT INTO `states` VALUES (2145,'Tukuma',120);
INSERT INTO `states` VALUES (2146,'Valkas',120);
INSERT INTO `states` VALUES (2147,'Valmieras',120);
INSERT INTO `states` VALUES (2148,'Ventspils',120);
INSERT INTO `states` VALUES (2149,'Ventspils City',120);
INSERT INTO `states` VALUES (2150,'Beirut',121);
INSERT INTO `states` VALUES (2151,'Jabal Lubnan',121);
INSERT INTO `states` VALUES (2152,'Mohafazat Liban-Nord',121);
INSERT INTO `states` VALUES (2153,'Mohafazat Mont-Liban',121);
INSERT INTO `states` VALUES (2154,'Sidon',121);
INSERT INTO `states` VALUES (2155,'al-Biqa',121);
INSERT INTO `states` VALUES (2156,'al-Janub',121);
INSERT INTO `states` VALUES (2157,'an-Nabatiyah',121);
INSERT INTO `states` VALUES (2158,'ash-Shamal',121);
INSERT INTO `states` VALUES (2159,'Berea',122);
INSERT INTO `states` VALUES (2160,'Butha-Buthe',122);
INSERT INTO `states` VALUES (2161,'Leribe',122);
INSERT INTO `states` VALUES (2162,'Mafeteng',122);
INSERT INTO `states` VALUES (2163,'Maseru',122);
INSERT INTO `states` VALUES (2164,'Mohale\'s Hoek',122);
INSERT INTO `states` VALUES (2165,'Mokhotlong',122);
INSERT INTO `states` VALUES (2166,'Qacha\'s Nek',122);
INSERT INTO `states` VALUES (2167,'Quthing',122);
INSERT INTO `states` VALUES (2168,'Thaba-Tseka',122);
INSERT INTO `states` VALUES (2169,'Bomi',123);
INSERT INTO `states` VALUES (2170,'Bong',123);
INSERT INTO `states` VALUES (2171,'Grand Bassa',123);
INSERT INTO `states` VALUES (2172,'Grand Cape Mount',123);
INSERT INTO `states` VALUES (2173,'Grand Gedeh',123);
INSERT INTO `states` VALUES (2174,'Loffa',123);
INSERT INTO `states` VALUES (2175,'Margibi',123);
INSERT INTO `states` VALUES (2176,'Maryland and Grand Kru',123);
INSERT INTO `states` VALUES (2177,'Montserrado',123);
INSERT INTO `states` VALUES (2178,'Nimba',123);
INSERT INTO `states` VALUES (2179,'Rivercess',123);
INSERT INTO `states` VALUES (2180,'Sinoe',123);
INSERT INTO `states` VALUES (2181,'Ajdabiya',124);
INSERT INTO `states` VALUES (2182,'Fezzan',124);
INSERT INTO `states` VALUES (2183,'Banghazi',124);
INSERT INTO `states` VALUES (2184,'Darnah',124);
INSERT INTO `states` VALUES (2185,'Ghadamis',124);
INSERT INTO `states` VALUES (2186,'Gharyan',124);
INSERT INTO `states` VALUES (2187,'Misratah',124);
INSERT INTO `states` VALUES (2188,'Murzuq',124);
INSERT INTO `states` VALUES (2189,'Sabha',124);
INSERT INTO `states` VALUES (2190,'Sawfajjin',124);
INSERT INTO `states` VALUES (2191,'Surt',124);
INSERT INTO `states` VALUES (2192,'Tarabulus',124);
INSERT INTO `states` VALUES (2193,'Tarhunah',124);
INSERT INTO `states` VALUES (2194,'Tripolitania',124);
INSERT INTO `states` VALUES (2195,'Tubruq',124);
INSERT INTO `states` VALUES (2196,'Yafran',124);
INSERT INTO `states` VALUES (2197,'Zlitan',124);
INSERT INTO `states` VALUES (2198,'al-\'Aziziyah',124);
INSERT INTO `states` VALUES (2199,'al-Fatih',124);
INSERT INTO `states` VALUES (2200,'al-Jabal al Akhdar',124);
INSERT INTO `states` VALUES (2201,'al-Jufrah',124);
INSERT INTO `states` VALUES (2202,'al-Khums',124);
INSERT INTO `states` VALUES (2203,'al-Kufrah',124);
INSERT INTO `states` VALUES (2204,'an-Nuqat al-Khams',124);
INSERT INTO `states` VALUES (2205,'ash-Shati\'',124);
INSERT INTO `states` VALUES (2206,'az-Zawiyah',124);
INSERT INTO `states` VALUES (2207,'Balzers',125);
INSERT INTO `states` VALUES (2208,'Eschen',125);
INSERT INTO `states` VALUES (2209,'Gamprin',125);
INSERT INTO `states` VALUES (2210,'Mauren',125);
INSERT INTO `states` VALUES (2211,'Planken',125);
INSERT INTO `states` VALUES (2212,'Ruggell',125);
INSERT INTO `states` VALUES (2213,'Schaan',125);
INSERT INTO `states` VALUES (2214,'Schellenberg',125);
INSERT INTO `states` VALUES (2215,'Triesen',125);
INSERT INTO `states` VALUES (2216,'Triesenberg',125);
INSERT INTO `states` VALUES (2217,'Vaduz',125);
INSERT INTO `states` VALUES (2218,'Alytaus',126);
INSERT INTO `states` VALUES (2219,'Anyksciai',126);
INSERT INTO `states` VALUES (2220,'Kauno',126);
INSERT INTO `states` VALUES (2221,'Klaipedos',126);
INSERT INTO `states` VALUES (2222,'Marijampoles',126);
INSERT INTO `states` VALUES (2223,'Panevezhio',126);
INSERT INTO `states` VALUES (2224,'Panevezys',126);
INSERT INTO `states` VALUES (2225,'Shiauliu',126);
INSERT INTO `states` VALUES (2226,'Taurages',126);
INSERT INTO `states` VALUES (2227,'Telshiu',126);
INSERT INTO `states` VALUES (2228,'Telsiai',126);
INSERT INTO `states` VALUES (2229,'Utenos',126);
INSERT INTO `states` VALUES (2230,'Vilniaus',126);
INSERT INTO `states` VALUES (2231,'Capellen',127);
INSERT INTO `states` VALUES (2232,'Clervaux',127);
INSERT INTO `states` VALUES (2233,'Diekirch',127);
INSERT INTO `states` VALUES (2234,'Echternach',127);
INSERT INTO `states` VALUES (2235,'Esch-sur-Alzette',127);
INSERT INTO `states` VALUES (2236,'Grevenmacher',127);
INSERT INTO `states` VALUES (2237,'Luxembourg',127);
INSERT INTO `states` VALUES (2238,'Mersch',127);
INSERT INTO `states` VALUES (2239,'Redange',127);
INSERT INTO `states` VALUES (2240,'Remich',127);
INSERT INTO `states` VALUES (2241,'Vianden',127);
INSERT INTO `states` VALUES (2242,'Wiltz',127);
INSERT INTO `states` VALUES (2243,'Macau',128);
INSERT INTO `states` VALUES (2244,'Berovo',129);
INSERT INTO `states` VALUES (2245,'Bitola',129);
INSERT INTO `states` VALUES (2246,'Brod',129);
INSERT INTO `states` VALUES (2247,'Debar',129);
INSERT INTO `states` VALUES (2248,'Delchevo',129);
INSERT INTO `states` VALUES (2249,'Demir Hisar',129);
INSERT INTO `states` VALUES (2250,'Gevgelija',129);
INSERT INTO `states` VALUES (2251,'Gostivar',129);
INSERT INTO `states` VALUES (2252,'Kavadarci',129);
INSERT INTO `states` VALUES (2253,'Kichevo',129);
INSERT INTO `states` VALUES (2254,'Kochani',129);
INSERT INTO `states` VALUES (2255,'Kratovo',129);
INSERT INTO `states` VALUES (2256,'Kriva Palanka',129);
INSERT INTO `states` VALUES (2257,'Krushevo',129);
INSERT INTO `states` VALUES (2258,'Kumanovo',129);
INSERT INTO `states` VALUES (2259,'Negotino',129);
INSERT INTO `states` VALUES (2260,'Ohrid',129);
INSERT INTO `states` VALUES (2261,'Prilep',129);
INSERT INTO `states` VALUES (2262,'Probishtip',129);
INSERT INTO `states` VALUES (2263,'Radovish',129);
INSERT INTO `states` VALUES (2264,'Resen',129);
INSERT INTO `states` VALUES (2265,'Shtip',129);
INSERT INTO `states` VALUES (2266,'Skopje',129);
INSERT INTO `states` VALUES (2267,'Struga',129);
INSERT INTO `states` VALUES (2268,'Strumica',129);
INSERT INTO `states` VALUES (2269,'Sveti Nikole',129);
INSERT INTO `states` VALUES (2270,'Tetovo',129);
INSERT INTO `states` VALUES (2271,'Valandovo',129);
INSERT INTO `states` VALUES (2272,'Veles',129);
INSERT INTO `states` VALUES (2273,'Vinica',129);
INSERT INTO `states` VALUES (2274,'Antananarivo',130);
INSERT INTO `states` VALUES (2275,'Antsiranana',130);
INSERT INTO `states` VALUES (2276,'Fianarantsoa',130);
INSERT INTO `states` VALUES (2277,'Mahajanga',130);
INSERT INTO `states` VALUES (2278,'Toamasina',130);
INSERT INTO `states` VALUES (2279,'Toliary',130);
INSERT INTO `states` VALUES (2280,'Balaka',131);
INSERT INTO `states` VALUES (2281,'Blantyre City',131);
INSERT INTO `states` VALUES (2282,'Chikwawa',131);
INSERT INTO `states` VALUES (2283,'Chiradzulu',131);
INSERT INTO `states` VALUES (2284,'Chitipa',131);
INSERT INTO `states` VALUES (2285,'Dedza',131);
INSERT INTO `states` VALUES (2286,'Dowa',131);
INSERT INTO `states` VALUES (2287,'Karonga',131);
INSERT INTO `states` VALUES (2288,'Kasungu',131);
INSERT INTO `states` VALUES (2289,'Lilongwe City',131);
INSERT INTO `states` VALUES (2290,'Machinga',131);
INSERT INTO `states` VALUES (2291,'Mangochi',131);
INSERT INTO `states` VALUES (2292,'Mchinji',131);
INSERT INTO `states` VALUES (2293,'Mulanje',131);
INSERT INTO `states` VALUES (2294,'Mwanza',131);
INSERT INTO `states` VALUES (2295,'Mzimba',131);
INSERT INTO `states` VALUES (2296,'Mzuzu City',131);
INSERT INTO `states` VALUES (2297,'Nkhata Bay',131);
INSERT INTO `states` VALUES (2298,'Nkhotakota',131);
INSERT INTO `states` VALUES (2299,'Nsanje',131);
INSERT INTO `states` VALUES (2300,'Ntcheu',131);
INSERT INTO `states` VALUES (2301,'Ntchisi',131);
INSERT INTO `states` VALUES (2302,'Phalombe',131);
INSERT INTO `states` VALUES (2303,'Rumphi',131);
INSERT INTO `states` VALUES (2304,'Salima',131);
INSERT INTO `states` VALUES (2305,'Thyolo',131);
INSERT INTO `states` VALUES (2306,'Zomba Municipality',131);
INSERT INTO `states` VALUES (2307,'Johor',132);
INSERT INTO `states` VALUES (2308,'Kedah',132);
INSERT INTO `states` VALUES (2309,'Kelantan',132);
INSERT INTO `states` VALUES (2310,'Kuala Lumpur',132);
INSERT INTO `states` VALUES (2311,'Labuan',132);
INSERT INTO `states` VALUES (2312,'Melaka',132);
INSERT INTO `states` VALUES (2313,'Negeri Johor',132);
INSERT INTO `states` VALUES (2314,'Negeri Sembilan',132);
INSERT INTO `states` VALUES (2315,'Pahang',132);
INSERT INTO `states` VALUES (2316,'Penang',132);
INSERT INTO `states` VALUES (2317,'Perak',132);
INSERT INTO `states` VALUES (2318,'Perlis',132);
INSERT INTO `states` VALUES (2319,'Pulau Pinang',132);
INSERT INTO `states` VALUES (2320,'Sabah',132);
INSERT INTO `states` VALUES (2321,'Sarawak',132);
INSERT INTO `states` VALUES (2322,'Selangor',132);
INSERT INTO `states` VALUES (2323,'Sembilan',132);
INSERT INTO `states` VALUES (2324,'Terengganu',132);
INSERT INTO `states` VALUES (2325,'Alif Alif',133);
INSERT INTO `states` VALUES (2326,'Alif Dhaal',133);
INSERT INTO `states` VALUES (2327,'Baa',133);
INSERT INTO `states` VALUES (2328,'Dhaal',133);
INSERT INTO `states` VALUES (2329,'Faaf',133);
INSERT INTO `states` VALUES (2330,'Gaaf Alif',133);
INSERT INTO `states` VALUES (2331,'Gaaf Dhaal',133);
INSERT INTO `states` VALUES (2332,'Ghaviyani',133);
INSERT INTO `states` VALUES (2333,'Haa Alif',133);
INSERT INTO `states` VALUES (2334,'Haa Dhaal',133);
INSERT INTO `states` VALUES (2335,'Kaaf',133);
INSERT INTO `states` VALUES (2336,'Laam',133);
INSERT INTO `states` VALUES (2337,'Lhaviyani',133);
INSERT INTO `states` VALUES (2338,'Male',133);
INSERT INTO `states` VALUES (2339,'Miim',133);
INSERT INTO `states` VALUES (2340,'Nuun',133);
INSERT INTO `states` VALUES (2341,'Raa',133);
INSERT INTO `states` VALUES (2342,'Shaviyani',133);
INSERT INTO `states` VALUES (2343,'Siin',133);
INSERT INTO `states` VALUES (2344,'Thaa',133);
INSERT INTO `states` VALUES (2345,'Vaav',133);
INSERT INTO `states` VALUES (2346,'Bamako',134);
INSERT INTO `states` VALUES (2347,'Gao',134);
INSERT INTO `states` VALUES (2348,'Kayes',134);
INSERT INTO `states` VALUES (2349,'Kidal',134);
INSERT INTO `states` VALUES (2350,'Koulikoro',134);
INSERT INTO `states` VALUES (2351,'Mopti',134);
INSERT INTO `states` VALUES (2352,'Segou',134);
INSERT INTO `states` VALUES (2353,'Sikasso',134);
INSERT INTO `states` VALUES (2354,'Tombouctou',134);
INSERT INTO `states` VALUES (2355,'Gozo and Comino',135);
INSERT INTO `states` VALUES (2356,'Inner Harbour',135);
INSERT INTO `states` VALUES (2357,'Northern',135);
INSERT INTO `states` VALUES (2358,'Outer Harbour',135);
INSERT INTO `states` VALUES (2359,'South Eastern',135);
INSERT INTO `states` VALUES (2360,'Valletta',135);
INSERT INTO `states` VALUES (2361,'Western',135);
INSERT INTO `states` VALUES (2362,'Castletown',136);
INSERT INTO `states` VALUES (2363,'Douglas',136);
INSERT INTO `states` VALUES (2364,'Laxey',136);
INSERT INTO `states` VALUES (2365,'Onchan',136);
INSERT INTO `states` VALUES (2366,'Peel',136);
INSERT INTO `states` VALUES (2367,'Port Erin',136);
INSERT INTO `states` VALUES (2368,'Port Saint Mary',136);
INSERT INTO `states` VALUES (2369,'Ramsey',136);
INSERT INTO `states` VALUES (2370,'Ailinlaplap',137);
INSERT INTO `states` VALUES (2371,'Ailuk',137);
INSERT INTO `states` VALUES (2372,'Arno',137);
INSERT INTO `states` VALUES (2373,'Aur',137);
INSERT INTO `states` VALUES (2374,'Bikini',137);
INSERT INTO `states` VALUES (2375,'Ebon',137);
INSERT INTO `states` VALUES (2376,'Enewetak',137);
INSERT INTO `states` VALUES (2377,'Jabat',137);
INSERT INTO `states` VALUES (2378,'Jaluit',137);
INSERT INTO `states` VALUES (2379,'Kili',137);
INSERT INTO `states` VALUES (2380,'Kwajalein',137);
INSERT INTO `states` VALUES (2381,'Lae',137);
INSERT INTO `states` VALUES (2382,'Lib',137);
INSERT INTO `states` VALUES (2383,'Likiep',137);
INSERT INTO `states` VALUES (2384,'Majuro',137);
INSERT INTO `states` VALUES (2385,'Maloelap',137);
INSERT INTO `states` VALUES (2386,'Mejit',137);
INSERT INTO `states` VALUES (2387,'Mili',137);
INSERT INTO `states` VALUES (2388,'Namorik',137);
INSERT INTO `states` VALUES (2389,'Namu',137);
INSERT INTO `states` VALUES (2390,'Rongelap',137);
INSERT INTO `states` VALUES (2391,'Ujae',137);
INSERT INTO `states` VALUES (2392,'Utrik',137);
INSERT INTO `states` VALUES (2393,'Wotho',137);
INSERT INTO `states` VALUES (2394,'Wotje',137);
INSERT INTO `states` VALUES (2395,'Fort-de-France',138);
INSERT INTO `states` VALUES (2396,'La Trinite',138);
INSERT INTO `states` VALUES (2397,'Le Marin',138);
INSERT INTO `states` VALUES (2398,'Saint-Pierre',138);
INSERT INTO `states` VALUES (2399,'Adrar',139);
INSERT INTO `states` VALUES (2400,'Assaba',139);
INSERT INTO `states` VALUES (2401,'Brakna',139);
INSERT INTO `states` VALUES (2402,'Dhakhlat Nawadibu',139);
INSERT INTO `states` VALUES (2403,'Hudh-al-Gharbi',139);
INSERT INTO `states` VALUES (2404,'Hudh-ash-Sharqi',139);
INSERT INTO `states` VALUES (2405,'Inshiri',139);
INSERT INTO `states` VALUES (2406,'Nawakshut',139);
INSERT INTO `states` VALUES (2407,'Qidimagha',139);
INSERT INTO `states` VALUES (2408,'Qurqul',139);
INSERT INTO `states` VALUES (2409,'Taqant',139);
INSERT INTO `states` VALUES (2410,'Tiris Zammur',139);
INSERT INTO `states` VALUES (2411,'Trarza',139);
INSERT INTO `states` VALUES (2412,'Black River',140);
INSERT INTO `states` VALUES (2413,'Eau Coulee',140);
INSERT INTO `states` VALUES (2414,'Flacq',140);
INSERT INTO `states` VALUES (2415,'Floreal',140);
INSERT INTO `states` VALUES (2416,'Grand Port',140);
INSERT INTO `states` VALUES (2417,'Moka',140);
INSERT INTO `states` VALUES (2418,'Pamplempousses',140);
INSERT INTO `states` VALUES (2419,'Plaines Wilhelm',140);
INSERT INTO `states` VALUES (2420,'Port Louis',140);
INSERT INTO `states` VALUES (2421,'Riviere du Rempart',140);
INSERT INTO `states` VALUES (2422,'Rodrigues',140);
INSERT INTO `states` VALUES (2423,'Rose Hill',140);
INSERT INTO `states` VALUES (2424,'Savanne',140);
INSERT INTO `states` VALUES (2425,'Mayotte',141);
INSERT INTO `states` VALUES (2426,'Pamanzi',141);
INSERT INTO `states` VALUES (2427,'Aguascalientes',142);
INSERT INTO `states` VALUES (2428,'Baja California',142);
INSERT INTO `states` VALUES (2429,'Baja California Sur',142);
INSERT INTO `states` VALUES (2430,'Campeche',142);
INSERT INTO `states` VALUES (2431,'Chiapas',142);
INSERT INTO `states` VALUES (2432,'Chihuahua',142);
INSERT INTO `states` VALUES (2433,'Coahuila',142);
INSERT INTO `states` VALUES (2434,'Colima',142);
INSERT INTO `states` VALUES (2435,'Distrito Federal',142);
INSERT INTO `states` VALUES (2436,'Durango',142);
INSERT INTO `states` VALUES (2437,'Estado de Mexico',142);
INSERT INTO `states` VALUES (2438,'Guanajuato',142);
INSERT INTO `states` VALUES (2439,'Guerrero',142);
INSERT INTO `states` VALUES (2440,'Hidalgo',142);
INSERT INTO `states` VALUES (2441,'Jalisco',142);
INSERT INTO `states` VALUES (2442,'Mexico',142);
INSERT INTO `states` VALUES (2443,'Michoacan',142);
INSERT INTO `states` VALUES (2444,'Morelos',142);
INSERT INTO `states` VALUES (2445,'Nayarit',142);
INSERT INTO `states` VALUES (2446,'Nuevo Leon',142);
INSERT INTO `states` VALUES (2447,'Oaxaca',142);
INSERT INTO `states` VALUES (2448,'Puebla',142);
INSERT INTO `states` VALUES (2449,'Queretaro',142);
INSERT INTO `states` VALUES (2450,'Quintana Roo',142);
INSERT INTO `states` VALUES (2451,'San Luis Potosi',142);
INSERT INTO `states` VALUES (2452,'Sinaloa',142);
INSERT INTO `states` VALUES (2453,'Sonora',142);
INSERT INTO `states` VALUES (2454,'Tabasco',142);
INSERT INTO `states` VALUES (2455,'Tamaulipas',142);
INSERT INTO `states` VALUES (2456,'Tlaxcala',142);
INSERT INTO `states` VALUES (2457,'Veracruz',142);
INSERT INTO `states` VALUES (2458,'Yucatan',142);
INSERT INTO `states` VALUES (2459,'Zacatecas',142);
INSERT INTO `states` VALUES (2460,'Chuuk',143);
INSERT INTO `states` VALUES (2461,'Kusaie',143);
INSERT INTO `states` VALUES (2462,'Pohnpei',143);
INSERT INTO `states` VALUES (2463,'Yap',143);
INSERT INTO `states` VALUES (2464,'Balti',144);
INSERT INTO `states` VALUES (2465,'Cahul',144);
INSERT INTO `states` VALUES (2466,'Chisinau',144);
INSERT INTO `states` VALUES (2467,'Chisinau Oras',144);
INSERT INTO `states` VALUES (2468,'Edinet',144);
INSERT INTO `states` VALUES (2469,'Gagauzia',144);
INSERT INTO `states` VALUES (2470,'Lapusna',144);
INSERT INTO `states` VALUES (2471,'Orhei',144);
INSERT INTO `states` VALUES (2472,'Soroca',144);
INSERT INTO `states` VALUES (2473,'Taraclia',144);
INSERT INTO `states` VALUES (2474,'Tighina',144);
INSERT INTO `states` VALUES (2475,'Transnistria',144);
INSERT INTO `states` VALUES (2476,'Ungheni',144);
INSERT INTO `states` VALUES (2477,'Fontvieille',145);
INSERT INTO `states` VALUES (2478,'La Condamine',145);
INSERT INTO `states` VALUES (2479,'Monaco-Ville',145);
INSERT INTO `states` VALUES (2480,'Monte Carlo',145);
INSERT INTO `states` VALUES (2481,'Arhangaj',146);
INSERT INTO `states` VALUES (2482,'Bajan-Olgij',146);
INSERT INTO `states` VALUES (2483,'Bajanhongor',146);
INSERT INTO `states` VALUES (2484,'Bulgan',146);
INSERT INTO `states` VALUES (2485,'Darhan-Uul',146);
INSERT INTO `states` VALUES (2486,'Dornod',146);
INSERT INTO `states` VALUES (2487,'Dornogovi',146);
INSERT INTO `states` VALUES (2488,'Dundgovi',146);
INSERT INTO `states` VALUES (2489,'Govi-Altaj',146);
INSERT INTO `states` VALUES (2490,'Govisumber',146);
INSERT INTO `states` VALUES (2491,'Hentij',146);
INSERT INTO `states` VALUES (2492,'Hovd',146);
INSERT INTO `states` VALUES (2493,'Hovsgol',146);
INSERT INTO `states` VALUES (2494,'Omnogovi',146);
INSERT INTO `states` VALUES (2495,'Orhon',146);
INSERT INTO `states` VALUES (2496,'Ovorhangaj',146);
INSERT INTO `states` VALUES (2497,'Selenge',146);
INSERT INTO `states` VALUES (2498,'Suhbaatar',146);
INSERT INTO `states` VALUES (2499,'Tov',146);
INSERT INTO `states` VALUES (2500,'Ulaanbaatar',146);
INSERT INTO `states` VALUES (2501,'Uvs',146);
INSERT INTO `states` VALUES (2502,'Zavhan',146);
INSERT INTO `states` VALUES (2503,'Montserrat',147);
INSERT INTO `states` VALUES (2504,'Agadir',148);
INSERT INTO `states` VALUES (2505,'Casablanca',148);
INSERT INTO `states` VALUES (2506,'Chaouia-Ouardigha',148);
INSERT INTO `states` VALUES (2507,'Doukkala-Abda',148);
INSERT INTO `states` VALUES (2508,'Fes-Boulemane',148);
INSERT INTO `states` VALUES (2509,'Gharb-Chrarda-Beni Hssen',148);
INSERT INTO `states` VALUES (2510,'Guelmim',148);
INSERT INTO `states` VALUES (2511,'Kenitra',148);
INSERT INTO `states` VALUES (2512,'Marrakech-Tensift-Al Haouz',148);
INSERT INTO `states` VALUES (2513,'Meknes-Tafilalet',148);
INSERT INTO `states` VALUES (2514,'Oriental',148);
INSERT INTO `states` VALUES (2515,'Oujda',148);
INSERT INTO `states` VALUES (2516,'Province de Tanger',148);
INSERT INTO `states` VALUES (2517,'Rabat-Sale-Zammour-Zaer',148);
INSERT INTO `states` VALUES (2518,'Sala Al Jadida',148);
INSERT INTO `states` VALUES (2519,'Settat',148);
INSERT INTO `states` VALUES (2520,'Souss Massa-Draa',148);
INSERT INTO `states` VALUES (2521,'Tadla-Azilal',148);
INSERT INTO `states` VALUES (2522,'Tangier-Tetouan',148);
INSERT INTO `states` VALUES (2523,'Taza-Al Hoceima-Taounate',148);
INSERT INTO `states` VALUES (2524,'Wilaya de Casablanca',148);
INSERT INTO `states` VALUES (2525,'Wilaya de Rabat-Sale',148);
INSERT INTO `states` VALUES (2526,'Cabo Delgado',149);
INSERT INTO `states` VALUES (2527,'Gaza',149);
INSERT INTO `states` VALUES (2528,'Inhambane',149);
INSERT INTO `states` VALUES (2529,'Manica',149);
INSERT INTO `states` VALUES (2530,'Maputo',149);
INSERT INTO `states` VALUES (2531,'Maputo Provincia',149);
INSERT INTO `states` VALUES (2532,'Nampula',149);
INSERT INTO `states` VALUES (2533,'Niassa',149);
INSERT INTO `states` VALUES (2534,'Sofala',149);
INSERT INTO `states` VALUES (2535,'Tete',149);
INSERT INTO `states` VALUES (2536,'Zambezia',149);
INSERT INTO `states` VALUES (2537,'Ayeyarwady',150);
INSERT INTO `states` VALUES (2538,'Bago',150);
INSERT INTO `states` VALUES (2539,'Chin',150);
INSERT INTO `states` VALUES (2540,'Kachin',150);
INSERT INTO `states` VALUES (2541,'Kayah',150);
INSERT INTO `states` VALUES (2542,'Kayin',150);
INSERT INTO `states` VALUES (2543,'Magway',150);
INSERT INTO `states` VALUES (2544,'Mandalay',150);
INSERT INTO `states` VALUES (2545,'Mon',150);
INSERT INTO `states` VALUES (2546,'Nay Pyi Taw',150);
INSERT INTO `states` VALUES (2547,'Rakhine',150);
INSERT INTO `states` VALUES (2548,'Sagaing',150);
INSERT INTO `states` VALUES (2549,'Shan',150);
INSERT INTO `states` VALUES (2550,'Tanintharyi',150);
INSERT INTO `states` VALUES (2551,'Yangon',150);
INSERT INTO `states` VALUES (2552,'Caprivi',151);
INSERT INTO `states` VALUES (2553,'Erongo',151);
INSERT INTO `states` VALUES (2554,'Hardap',151);
INSERT INTO `states` VALUES (2555,'Karas',151);
INSERT INTO `states` VALUES (2556,'Kavango',151);
INSERT INTO `states` VALUES (2557,'Khomas',151);
INSERT INTO `states` VALUES (2558,'Kunene',151);
INSERT INTO `states` VALUES (2559,'Ohangwena',151);
INSERT INTO `states` VALUES (2560,'Omaheke',151);
INSERT INTO `states` VALUES (2561,'Omusati',151);
INSERT INTO `states` VALUES (2562,'Oshana',151);
INSERT INTO `states` VALUES (2563,'Oshikoto',151);
INSERT INTO `states` VALUES (2564,'Otjozondjupa',151);
INSERT INTO `states` VALUES (2565,'Yaren',152);
INSERT INTO `states` VALUES (2566,'Bagmati',153);
INSERT INTO `states` VALUES (2567,'Bheri',153);
INSERT INTO `states` VALUES (2568,'Dhawalagiri',153);
INSERT INTO `states` VALUES (2569,'Gandaki',153);
INSERT INTO `states` VALUES (2570,'Janakpur',153);
INSERT INTO `states` VALUES (2571,'Karnali',153);
INSERT INTO `states` VALUES (2572,'Koshi',153);
INSERT INTO `states` VALUES (2573,'Lumbini',153);
INSERT INTO `states` VALUES (2574,'Mahakali',153);
INSERT INTO `states` VALUES (2575,'Mechi',153);
INSERT INTO `states` VALUES (2576,'Narayani',153);
INSERT INTO `states` VALUES (2577,'Rapti',153);
INSERT INTO `states` VALUES (2578,'Sagarmatha',153);
INSERT INTO `states` VALUES (2579,'Seti',153);
INSERT INTO `states` VALUES (2580,'Bonaire',154);
INSERT INTO `states` VALUES (2581,'Curacao',154);
INSERT INTO `states` VALUES (2582,'Saba',154);
INSERT INTO `states` VALUES (2583,'Sint Eustatius',154);
INSERT INTO `states` VALUES (2584,'Sint Maarten',154);
INSERT INTO `states` VALUES (2585,'Amsterdam',155);
INSERT INTO `states` VALUES (2586,'Benelux',155);
INSERT INTO `states` VALUES (2587,'Drenthe',155);
INSERT INTO `states` VALUES (2588,'Flevoland',155);
INSERT INTO `states` VALUES (2589,'Friesland',155);
INSERT INTO `states` VALUES (2590,'Gelderland',155);
INSERT INTO `states` VALUES (2591,'Groningen',155);
INSERT INTO `states` VALUES (2592,'Limburg',155);
INSERT INTO `states` VALUES (2593,'Noord-Brabant',155);
INSERT INTO `states` VALUES (2594,'Noord-Holland',155);
INSERT INTO `states` VALUES (2595,'Overijssel',155);
INSERT INTO `states` VALUES (2596,'South Holland',155);
INSERT INTO `states` VALUES (2597,'Utrecht',155);
INSERT INTO `states` VALUES (2598,'Zeeland',155);
INSERT INTO `states` VALUES (2599,'Zuid-Holland',155);
INSERT INTO `states` VALUES (2600,'Iles',156);
INSERT INTO `states` VALUES (2601,'Nord',156);
INSERT INTO `states` VALUES (2602,'Sud',156);
INSERT INTO `states` VALUES (2603,'Area Outside Region',157);
INSERT INTO `states` VALUES (2604,'Auckland',157);
INSERT INTO `states` VALUES (2605,'Bay of Plenty',157);
INSERT INTO `states` VALUES (2606,'Canterbury',157);
INSERT INTO `states` VALUES (2607,'Christchurch',157);
INSERT INTO `states` VALUES (2608,'Gisborne',157);
INSERT INTO `states` VALUES (2609,'Hawke\'s Bay',157);
INSERT INTO `states` VALUES (2610,'Manawatu-Wanganui',157);
INSERT INTO `states` VALUES (2611,'Marlborough',157);
INSERT INTO `states` VALUES (2612,'Nelson',157);
INSERT INTO `states` VALUES (2613,'Northland',157);
INSERT INTO `states` VALUES (2614,'Otago',157);
INSERT INTO `states` VALUES (2615,'Rodney',157);
INSERT INTO `states` VALUES (2616,'Southland',157);
INSERT INTO `states` VALUES (2617,'Taranaki',157);
INSERT INTO `states` VALUES (2618,'Tasman',157);
INSERT INTO `states` VALUES (2619,'Waikato',157);
INSERT INTO `states` VALUES (2620,'Wellington',157);
INSERT INTO `states` VALUES (2621,'West Coast',157);
INSERT INTO `states` VALUES (2622,'Atlantico Norte',158);
INSERT INTO `states` VALUES (2623,'Atlantico Sur',158);
INSERT INTO `states` VALUES (2624,'Boaco',158);
INSERT INTO `states` VALUES (2625,'Carazo',158);
INSERT INTO `states` VALUES (2626,'Chinandega',158);
INSERT INTO `states` VALUES (2627,'Chontales',158);
INSERT INTO `states` VALUES (2628,'Esteli',158);
INSERT INTO `states` VALUES (2629,'Granada',158);
INSERT INTO `states` VALUES (2630,'Jinotega',158);
INSERT INTO `states` VALUES (2631,'Leon',158);
INSERT INTO `states` VALUES (2632,'Madriz',158);
INSERT INTO `states` VALUES (2633,'Managua',158);
INSERT INTO `states` VALUES (2634,'Masaya',158);
INSERT INTO `states` VALUES (2635,'Matagalpa',158);
INSERT INTO `states` VALUES (2636,'Nueva Segovia',158);
INSERT INTO `states` VALUES (2637,'Rio San Juan',158);
INSERT INTO `states` VALUES (2638,'Rivas',158);
INSERT INTO `states` VALUES (2639,'Agadez',159);
INSERT INTO `states` VALUES (2640,'Diffa',159);
INSERT INTO `states` VALUES (2641,'Dosso',159);
INSERT INTO `states` VALUES (2642,'Maradi',159);
INSERT INTO `states` VALUES (2643,'Niamey',159);
INSERT INTO `states` VALUES (2644,'Tahoua',159);
INSERT INTO `states` VALUES (2645,'Tillabery',159);
INSERT INTO `states` VALUES (2646,'Zinder',159);
INSERT INTO `states` VALUES (2647,'Abia',160);
INSERT INTO `states` VALUES (2648,'Abuja Federal Capital Territor',160);
INSERT INTO `states` VALUES (2649,'Adamawa',160);
INSERT INTO `states` VALUES (2650,'Akwa Ibom',160);
INSERT INTO `states` VALUES (2651,'Anambra',160);
INSERT INTO `states` VALUES (2652,'Bauchi',160);
INSERT INTO `states` VALUES (2653,'Bayelsa',160);
INSERT INTO `states` VALUES (2654,'Benue',160);
INSERT INTO `states` VALUES (2655,'Borno',160);
INSERT INTO `states` VALUES (2656,'Cross River',160);
INSERT INTO `states` VALUES (2657,'Delta',160);
INSERT INTO `states` VALUES (2658,'Ebonyi',160);
INSERT INTO `states` VALUES (2659,'Edo',160);
INSERT INTO `states` VALUES (2660,'Ekiti',160);
INSERT INTO `states` VALUES (2661,'Enugu',160);
INSERT INTO `states` VALUES (2662,'Gombe',160);
INSERT INTO `states` VALUES (2663,'Imo',160);
INSERT INTO `states` VALUES (2664,'Jigawa',160);
INSERT INTO `states` VALUES (2665,'Kaduna',160);
INSERT INTO `states` VALUES (2666,'Kano',160);
INSERT INTO `states` VALUES (2667,'Katsina',160);
INSERT INTO `states` VALUES (2668,'Kebbi',160);
INSERT INTO `states` VALUES (2669,'Kogi',160);
INSERT INTO `states` VALUES (2670,'Kwara',160);
INSERT INTO `states` VALUES (2671,'Lagos',160);
INSERT INTO `states` VALUES (2672,'Nassarawa',160);
INSERT INTO `states` VALUES (2673,'Niger',160);
INSERT INTO `states` VALUES (2674,'Ogun',160);
INSERT INTO `states` VALUES (2675,'Ondo',160);
INSERT INTO `states` VALUES (2676,'Osun',160);
INSERT INTO `states` VALUES (2677,'Oyo',160);
INSERT INTO `states` VALUES (2678,'Plateau',160);
INSERT INTO `states` VALUES (2679,'Rivers',160);
INSERT INTO `states` VALUES (2680,'Sokoto',160);
INSERT INTO `states` VALUES (2681,'Taraba',160);
INSERT INTO `states` VALUES (2682,'Yobe',160);
INSERT INTO `states` VALUES (2683,'Zamfara',160);
INSERT INTO `states` VALUES (2684,'Niue',161);
INSERT INTO `states` VALUES (2685,'Norfolk Island',162);
INSERT INTO `states` VALUES (2686,'Northern Islands',163);
INSERT INTO `states` VALUES (2687,'Rota',163);
INSERT INTO `states` VALUES (2688,'Saipan',163);
INSERT INTO `states` VALUES (2689,'Tinian',163);
INSERT INTO `states` VALUES (2690,'Akershus',164);
INSERT INTO `states` VALUES (2691,'Aust Agder',164);
INSERT INTO `states` VALUES (2692,'Bergen',164);
INSERT INTO `states` VALUES (2693,'Buskerud',164);
INSERT INTO `states` VALUES (2694,'Finnmark',164);
INSERT INTO `states` VALUES (2695,'Hedmark',164);
INSERT INTO `states` VALUES (2696,'Hordaland',164);
INSERT INTO `states` VALUES (2697,'Moere og Romsdal',164);
INSERT INTO `states` VALUES (2698,'Nord Trondelag',164);
INSERT INTO `states` VALUES (2699,'Nordland',164);
INSERT INTO `states` VALUES (2700,'Oestfold',164);
INSERT INTO `states` VALUES (2701,'Oppland',164);
INSERT INTO `states` VALUES (2702,'Oslo',164);
INSERT INTO `states` VALUES (2703,'Rogaland',164);
INSERT INTO `states` VALUES (2704,'Soer Troendelag',164);
INSERT INTO `states` VALUES (2705,'Sogn og Fjordane',164);
INSERT INTO `states` VALUES (2706,'Stavern',164);
INSERT INTO `states` VALUES (2707,'Sykkylven',164);
INSERT INTO `states` VALUES (2708,'Telemark',164);
INSERT INTO `states` VALUES (2709,'Troms',164);
INSERT INTO `states` VALUES (2710,'Vest Agder',164);
INSERT INTO `states` VALUES (2711,'Vestfold',164);
INSERT INTO `states` VALUES (2712,'ÃƒÂ˜stfold',164);
INSERT INTO `states` VALUES (2713,'Al Buraimi',165);
INSERT INTO `states` VALUES (2714,'Dhufar',165);
INSERT INTO `states` VALUES (2715,'Masqat',165);
INSERT INTO `states` VALUES (2716,'Musandam',165);
INSERT INTO `states` VALUES (2717,'Rusayl',165);
INSERT INTO `states` VALUES (2718,'Wadi Kabir',165);
INSERT INTO `states` VALUES (2719,'ad-Dakhiliyah',165);
INSERT INTO `states` VALUES (2720,'adh-Dhahirah',165);
INSERT INTO `states` VALUES (2721,'al-Batinah',165);
INSERT INTO `states` VALUES (2722,'ash-Sharqiyah',165);
INSERT INTO `states` VALUES (2723,'Baluchistan',166);
INSERT INTO `states` VALUES (2724,'Federal Capital Area',166);
INSERT INTO `states` VALUES (2725,'Federally administered Tribal ',166);
INSERT INTO `states` VALUES (2726,'North-West Frontier',166);
INSERT INTO `states` VALUES (2727,'Northern Areas',166);
INSERT INTO `states` VALUES (2728,'Punjab',166);
INSERT INTO `states` VALUES (2729,'Sind',166);
INSERT INTO `states` VALUES (2730,'Aimeliik',167);
INSERT INTO `states` VALUES (2731,'Airai',167);
INSERT INTO `states` VALUES (2732,'Angaur',167);
INSERT INTO `states` VALUES (2733,'Hatobohei',167);
INSERT INTO `states` VALUES (2734,'Kayangel',167);
INSERT INTO `states` VALUES (2735,'Koror',167);
INSERT INTO `states` VALUES (2736,'Melekeok',167);
INSERT INTO `states` VALUES (2737,'Ngaraard',167);
INSERT INTO `states` VALUES (2738,'Ngardmau',167);
INSERT INTO `states` VALUES (2739,'Ngaremlengui',167);
INSERT INTO `states` VALUES (2740,'Ngatpang',167);
INSERT INTO `states` VALUES (2741,'Ngchesar',167);
INSERT INTO `states` VALUES (2742,'Ngerchelong',167);
INSERT INTO `states` VALUES (2743,'Ngiwal',167);
INSERT INTO `states` VALUES (2744,'Peleliu',167);
INSERT INTO `states` VALUES (2745,'Sonsorol',167);
INSERT INTO `states` VALUES (2746,'Ariha',168);
INSERT INTO `states` VALUES (2747,'Bayt Lahm',168);
INSERT INTO `states` VALUES (2748,'Bethlehem',168);
INSERT INTO `states` VALUES (2749,'Dayr-al-Balah',168);
INSERT INTO `states` VALUES (2750,'Ghazzah',168);
INSERT INTO `states` VALUES (2751,'Ghazzah ash-Shamaliyah',168);
INSERT INTO `states` VALUES (2752,'Janin',168);
INSERT INTO `states` VALUES (2753,'Khan Yunis',168);
INSERT INTO `states` VALUES (2754,'Nabulus',168);
INSERT INTO `states` VALUES (2755,'Qalqilyah',168);
INSERT INTO `states` VALUES (2756,'Rafah',168);
INSERT INTO `states` VALUES (2757,'Ram Allah wal-Birah',168);
INSERT INTO `states` VALUES (2758,'Salfit',168);
INSERT INTO `states` VALUES (2759,'Tubas',168);
INSERT INTO `states` VALUES (2760,'Tulkarm',168);
INSERT INTO `states` VALUES (2761,'al-Khalil',168);
INSERT INTO `states` VALUES (2762,'al-Quds',168);
INSERT INTO `states` VALUES (2763,'Bocas del Toro',169);
INSERT INTO `states` VALUES (2764,'Chiriqui',169);
INSERT INTO `states` VALUES (2765,'Cocle',169);
INSERT INTO `states` VALUES (2766,'Colon',169);
INSERT INTO `states` VALUES (2767,'Darien',169);
INSERT INTO `states` VALUES (2768,'Embera',169);
INSERT INTO `states` VALUES (2769,'Herrera',169);
INSERT INTO `states` VALUES (2770,'Kuna Yala',169);
INSERT INTO `states` VALUES (2771,'Los Santos',169);
INSERT INTO `states` VALUES (2772,'Ngobe Bugle',169);
INSERT INTO `states` VALUES (2773,'Panama',169);
INSERT INTO `states` VALUES (2774,'Veraguas',169);
INSERT INTO `states` VALUES (2775,'East New Britain',170);
INSERT INTO `states` VALUES (2776,'East Sepik',170);
INSERT INTO `states` VALUES (2777,'Eastern Highlands',170);
INSERT INTO `states` VALUES (2778,'Enga',170);
INSERT INTO `states` VALUES (2779,'Fly River',170);
INSERT INTO `states` VALUES (2780,'Gulf',170);
INSERT INTO `states` VALUES (2781,'Madang',170);
INSERT INTO `states` VALUES (2782,'Manus',170);
INSERT INTO `states` VALUES (2783,'Milne Bay',170);
INSERT INTO `states` VALUES (2784,'Morobe',170);
INSERT INTO `states` VALUES (2785,'National Capital District',170);
INSERT INTO `states` VALUES (2786,'New Ireland',170);
INSERT INTO `states` VALUES (2787,'North Solomons',170);
INSERT INTO `states` VALUES (2788,'Oro',170);
INSERT INTO `states` VALUES (2789,'Sandaun',170);
INSERT INTO `states` VALUES (2790,'Simbu',170);
INSERT INTO `states` VALUES (2791,'Southern Highlands',170);
INSERT INTO `states` VALUES (2792,'West New Britain',170);
INSERT INTO `states` VALUES (2793,'Western Highlands',170);
INSERT INTO `states` VALUES (2794,'Alto Paraguay',171);
INSERT INTO `states` VALUES (2795,'Alto Parana',171);
INSERT INTO `states` VALUES (2796,'Amambay',171);
INSERT INTO `states` VALUES (2797,'Asuncion',171);
INSERT INTO `states` VALUES (2798,'Boqueron',171);
INSERT INTO `states` VALUES (2799,'Caaguazu',171);
INSERT INTO `states` VALUES (2800,'Caazapa',171);
INSERT INTO `states` VALUES (2801,'Canendiyu',171);
INSERT INTO `states` VALUES (2802,'Central',171);
INSERT INTO `states` VALUES (2803,'Concepcion',171);
INSERT INTO `states` VALUES (2804,'Cordillera',171);
INSERT INTO `states` VALUES (2805,'Guaira',171);
INSERT INTO `states` VALUES (2806,'Itapua',171);
INSERT INTO `states` VALUES (2807,'Misiones',171);
INSERT INTO `states` VALUES (2808,'Neembucu',171);
INSERT INTO `states` VALUES (2809,'Paraguari',171);
INSERT INTO `states` VALUES (2810,'Presidente Hayes',171);
INSERT INTO `states` VALUES (2811,'San Pedro',171);
INSERT INTO `states` VALUES (2812,'Amazonas',172);
INSERT INTO `states` VALUES (2813,'Ancash',172);
INSERT INTO `states` VALUES (2814,'Apurimac',172);
INSERT INTO `states` VALUES (2815,'Arequipa',172);
INSERT INTO `states` VALUES (2816,'Ayacucho',172);
INSERT INTO `states` VALUES (2817,'Cajamarca',172);
INSERT INTO `states` VALUES (2818,'Cusco',172);
INSERT INTO `states` VALUES (2819,'Huancavelica',172);
INSERT INTO `states` VALUES (2820,'Huanuco',172);
INSERT INTO `states` VALUES (2821,'Ica',172);
INSERT INTO `states` VALUES (2822,'Junin',172);
INSERT INTO `states` VALUES (2823,'La Libertad',172);
INSERT INTO `states` VALUES (2824,'Lambayeque',172);
INSERT INTO `states` VALUES (2825,'Lima y Callao',172);
INSERT INTO `states` VALUES (2826,'Loreto',172);
INSERT INTO `states` VALUES (2827,'Madre de Dios',172);
INSERT INTO `states` VALUES (2828,'Moquegua',172);
INSERT INTO `states` VALUES (2829,'Pasco',172);
INSERT INTO `states` VALUES (2830,'Piura',172);
INSERT INTO `states` VALUES (2831,'Puno',172);
INSERT INTO `states` VALUES (2832,'San Martin',172);
INSERT INTO `states` VALUES (2833,'Tacna',172);
INSERT INTO `states` VALUES (2834,'Tumbes',172);
INSERT INTO `states` VALUES (2835,'Ucayali',172);
INSERT INTO `states` VALUES (2836,'Batangas',173);
INSERT INTO `states` VALUES (2837,'Bicol',173);
INSERT INTO `states` VALUES (2838,'Bulacan',173);
INSERT INTO `states` VALUES (2839,'Cagayan',173);
INSERT INTO `states` VALUES (2840,'Caraga',173);
INSERT INTO `states` VALUES (2841,'Central Luzon',173);
INSERT INTO `states` VALUES (2842,'Central Mindanao',173);
INSERT INTO `states` VALUES (2843,'Central Visayas',173);
INSERT INTO `states` VALUES (2844,'Cordillera',173);
INSERT INTO `states` VALUES (2845,'Davao',173);
INSERT INTO `states` VALUES (2846,'Eastern Visayas',173);
INSERT INTO `states` VALUES (2847,'Greater Metropolitan Area',173);
INSERT INTO `states` VALUES (2848,'Ilocos',173);
INSERT INTO `states` VALUES (2849,'Laguna',173);
INSERT INTO `states` VALUES (2850,'Luzon',173);
INSERT INTO `states` VALUES (2851,'Mactan',173);
INSERT INTO `states` VALUES (2852,'Metropolitan Manila Area',173);
INSERT INTO `states` VALUES (2853,'Muslim Mindanao',173);
INSERT INTO `states` VALUES (2854,'Northern Mindanao',173);
INSERT INTO `states` VALUES (2855,'Southern Mindanao',173);
INSERT INTO `states` VALUES (2856,'Southern Tagalog',173);
INSERT INTO `states` VALUES (2857,'Western Mindanao',173);
INSERT INTO `states` VALUES (2858,'Western Visayas',173);
INSERT INTO `states` VALUES (2859,'Pitcairn Island',174);
INSERT INTO `states` VALUES (2860,'Biale Blota',175);
INSERT INTO `states` VALUES (2861,'Dobroszyce',175);
INSERT INTO `states` VALUES (2862,'Dolnoslaskie',175);
INSERT INTO `states` VALUES (2863,'Dziekanow Lesny',175);
INSERT INTO `states` VALUES (2864,'Hopowo',175);
INSERT INTO `states` VALUES (2865,'Kartuzy',175);
INSERT INTO `states` VALUES (2866,'Koscian',175);
INSERT INTO `states` VALUES (2867,'Krakow',175);
INSERT INTO `states` VALUES (2868,'Kujawsko-Pomorskie',175);
INSERT INTO `states` VALUES (2869,'Lodzkie',175);
INSERT INTO `states` VALUES (2870,'Lubelskie',175);
INSERT INTO `states` VALUES (2871,'Lubuskie',175);
INSERT INTO `states` VALUES (2872,'Malomice',175);
INSERT INTO `states` VALUES (2873,'Malopolskie',175);
INSERT INTO `states` VALUES (2874,'Mazowieckie',175);
INSERT INTO `states` VALUES (2875,'Mirkow',175);
INSERT INTO `states` VALUES (2876,'Opolskie',175);
INSERT INTO `states` VALUES (2877,'Ostrowiec',175);
INSERT INTO `states` VALUES (2878,'Podkarpackie',175);
INSERT INTO `states` VALUES (2879,'Podlaskie',175);
INSERT INTO `states` VALUES (2880,'Polska',175);
INSERT INTO `states` VALUES (2881,'Pomorskie',175);
INSERT INTO `states` VALUES (2882,'Poznan',175);
INSERT INTO `states` VALUES (2883,'Pruszkow',175);
INSERT INTO `states` VALUES (2884,'Rymanowska',175);
INSERT INTO `states` VALUES (2885,'Rzeszow',175);
INSERT INTO `states` VALUES (2886,'Slaskie',175);
INSERT INTO `states` VALUES (2887,'Stare Pole',175);
INSERT INTO `states` VALUES (2888,'Swietokrzyskie',175);
INSERT INTO `states` VALUES (2889,'Warminsko-Mazurskie',175);
INSERT INTO `states` VALUES (2890,'Warsaw',175);
INSERT INTO `states` VALUES (2891,'Wejherowo',175);
INSERT INTO `states` VALUES (2892,'Wielkopolskie',175);
INSERT INTO `states` VALUES (2893,'Wroclaw',175);
INSERT INTO `states` VALUES (2894,'Zachodnio-Pomorskie',175);
INSERT INTO `states` VALUES (2895,'Zukowo',175);
INSERT INTO `states` VALUES (2896,'Abrantes',176);
INSERT INTO `states` VALUES (2897,'Acores',176);
INSERT INTO `states` VALUES (2898,'Alentejo',176);
INSERT INTO `states` VALUES (2899,'Algarve',176);
INSERT INTO `states` VALUES (2900,'Braga',176);
INSERT INTO `states` VALUES (2901,'Centro',176);
INSERT INTO `states` VALUES (2902,'Distrito de Leiria',176);
INSERT INTO `states` VALUES (2903,'Distrito de Viana do Castelo',176);
INSERT INTO `states` VALUES (2904,'Distrito de Vila Real',176);
INSERT INTO `states` VALUES (2905,'Distrito do Porto',176);
INSERT INTO `states` VALUES (2906,'Lisboa e Vale do Tejo',176);
INSERT INTO `states` VALUES (2907,'Madeira',176);
INSERT INTO `states` VALUES (2908,'Norte',176);
INSERT INTO `states` VALUES (2909,'Paivas',176);
INSERT INTO `states` VALUES (2910,'Arecibo',177);
INSERT INTO `states` VALUES (2911,'Bayamon',177);
INSERT INTO `states` VALUES (2912,'Carolina',177);
INSERT INTO `states` VALUES (2913,'Florida',177);
INSERT INTO `states` VALUES (2914,'Guayama',177);
INSERT INTO `states` VALUES (2915,'Humacao',177);
INSERT INTO `states` VALUES (2916,'Mayaguez-Aguadilla',177);
INSERT INTO `states` VALUES (2917,'Ponce',177);
INSERT INTO `states` VALUES (2918,'Salinas',177);
INSERT INTO `states` VALUES (2919,'San Juan',177);
INSERT INTO `states` VALUES (2920,'Doha',178);
INSERT INTO `states` VALUES (2921,'Jarian-al-Batnah',178);
INSERT INTO `states` VALUES (2922,'Umm Salal',178);
INSERT INTO `states` VALUES (2923,'ad-Dawhah',178);
INSERT INTO `states` VALUES (2924,'al-Ghuwayriyah',178);
INSERT INTO `states` VALUES (2925,'al-Jumayliyah',178);
INSERT INTO `states` VALUES (2926,'al-Khawr',178);
INSERT INTO `states` VALUES (2927,'al-Wakrah',178);
INSERT INTO `states` VALUES (2928,'ar-Rayyan',178);
INSERT INTO `states` VALUES (2929,'ash-Shamal',178);
INSERT INTO `states` VALUES (2930,'Saint-Benoit',179);
INSERT INTO `states` VALUES (2931,'Saint-Denis',179);
INSERT INTO `states` VALUES (2932,'Saint-Paul',179);
INSERT INTO `states` VALUES (2933,'Saint-Pierre',179);
INSERT INTO `states` VALUES (2934,'Alba',180);
INSERT INTO `states` VALUES (2935,'Arad',180);
INSERT INTO `states` VALUES (2936,'Arges',180);
INSERT INTO `states` VALUES (2937,'Bacau',180);
INSERT INTO `states` VALUES (2938,'Bihor',180);
INSERT INTO `states` VALUES (2939,'Bistrita-Nasaud',180);
INSERT INTO `states` VALUES (2940,'Botosani',180);
INSERT INTO `states` VALUES (2941,'Braila',180);
INSERT INTO `states` VALUES (2942,'Brasov',180);
INSERT INTO `states` VALUES (2943,'Bucuresti',180);
INSERT INTO `states` VALUES (2944,'Buzau',180);
INSERT INTO `states` VALUES (2945,'Calarasi',180);
INSERT INTO `states` VALUES (2946,'Caras-Severin',180);
INSERT INTO `states` VALUES (2947,'Cluj',180);
INSERT INTO `states` VALUES (2948,'Constanta',180);
INSERT INTO `states` VALUES (2949,'Covasna',180);
INSERT INTO `states` VALUES (2950,'Dambovita',180);
INSERT INTO `states` VALUES (2951,'Dolj',180);
INSERT INTO `states` VALUES (2952,'Galati',180);
INSERT INTO `states` VALUES (2953,'Giurgiu',180);
INSERT INTO `states` VALUES (2954,'Gorj',180);
INSERT INTO `states` VALUES (2955,'Harghita',180);
INSERT INTO `states` VALUES (2956,'Hunedoara',180);
INSERT INTO `states` VALUES (2957,'Ialomita',180);
INSERT INTO `states` VALUES (2958,'Iasi',180);
INSERT INTO `states` VALUES (2959,'Ilfov',180);
INSERT INTO `states` VALUES (2960,'Maramures',180);
INSERT INTO `states` VALUES (2961,'Mehedinti',180);
INSERT INTO `states` VALUES (2962,'Mures',180);
INSERT INTO `states` VALUES (2963,'Neamt',180);
INSERT INTO `states` VALUES (2964,'Olt',180);
INSERT INTO `states` VALUES (2965,'Prahova',180);
INSERT INTO `states` VALUES (2966,'Salaj',180);
INSERT INTO `states` VALUES (2967,'Satu Mare',180);
INSERT INTO `states` VALUES (2968,'Sibiu',180);
INSERT INTO `states` VALUES (2969,'Sondelor',180);
INSERT INTO `states` VALUES (2970,'Suceava',180);
INSERT INTO `states` VALUES (2971,'Teleorman',180);
INSERT INTO `states` VALUES (2972,'Timis',180);
INSERT INTO `states` VALUES (2973,'Tulcea',180);
INSERT INTO `states` VALUES (2974,'Valcea',180);
INSERT INTO `states` VALUES (2975,'Vaslui',180);
INSERT INTO `states` VALUES (2976,'Vrancea',180);
INSERT INTO `states` VALUES (2977,'Adygeja',181);
INSERT INTO `states` VALUES (2978,'Aga',181);
INSERT INTO `states` VALUES (2979,'Alanija',181);
INSERT INTO `states` VALUES (2980,'Altaj',181);
INSERT INTO `states` VALUES (2981,'Amur',181);
INSERT INTO `states` VALUES (2982,'Arhangelsk',181);
INSERT INTO `states` VALUES (2983,'Astrahan',181);
INSERT INTO `states` VALUES (2984,'Bashkortostan',181);
INSERT INTO `states` VALUES (2985,'Belgorod',181);
INSERT INTO `states` VALUES (2986,'Brjansk',181);
INSERT INTO `states` VALUES (2987,'Burjatija',181);
INSERT INTO `states` VALUES (2988,'Chechenija',181);
INSERT INTO `states` VALUES (2989,'Cheljabinsk',181);
INSERT INTO `states` VALUES (2990,'Chita',181);
INSERT INTO `states` VALUES (2991,'Chukotka',181);
INSERT INTO `states` VALUES (2992,'Chuvashija',181);
INSERT INTO `states` VALUES (2993,'Dagestan',181);
INSERT INTO `states` VALUES (2994,'Evenkija',181);
INSERT INTO `states` VALUES (2995,'Gorno-Altaj',181);
INSERT INTO `states` VALUES (2996,'Habarovsk',181);
INSERT INTO `states` VALUES (2997,'Hakasija',181);
INSERT INTO `states` VALUES (2998,'Hanty-Mansija',181);
INSERT INTO `states` VALUES (2999,'Ingusetija',181);
INSERT INTO `states` VALUES (3000,'Irkutsk',181);
INSERT INTO `states` VALUES (3001,'Ivanovo',181);
INSERT INTO `states` VALUES (3002,'Jamalo-Nenets',181);
INSERT INTO `states` VALUES (3003,'Jaroslavl',181);
INSERT INTO `states` VALUES (3004,'Jevrej',181);
INSERT INTO `states` VALUES (3005,'Kabardino-Balkarija',181);
INSERT INTO `states` VALUES (3006,'Kaliningrad',181);
INSERT INTO `states` VALUES (3007,'Kalmykija',181);
INSERT INTO `states` VALUES (3008,'Kaluga',181);
INSERT INTO `states` VALUES (3009,'Kamchatka',181);
INSERT INTO `states` VALUES (3010,'Karachaj-Cherkessija',181);
INSERT INTO `states` VALUES (3011,'Karelija',181);
INSERT INTO `states` VALUES (3012,'Kemerovo',181);
INSERT INTO `states` VALUES (3013,'Khabarovskiy Kray',181);
INSERT INTO `states` VALUES (3014,'Kirov',181);
INSERT INTO `states` VALUES (3015,'Komi',181);
INSERT INTO `states` VALUES (3016,'Komi-Permjakija',181);
INSERT INTO `states` VALUES (3017,'Korjakija',181);
INSERT INTO `states` VALUES (3018,'Kostroma',181);
INSERT INTO `states` VALUES (3019,'Krasnodar',181);
INSERT INTO `states` VALUES (3020,'Krasnojarsk',181);
INSERT INTO `states` VALUES (3021,'Krasnoyarskiy Kray',181);
INSERT INTO `states` VALUES (3022,'Kurgan',181);
INSERT INTO `states` VALUES (3023,'Kursk',181);
INSERT INTO `states` VALUES (3024,'Leningrad',181);
INSERT INTO `states` VALUES (3025,'Lipeck',181);
INSERT INTO `states` VALUES (3026,'Magadan',181);
INSERT INTO `states` VALUES (3027,'Marij El',181);
INSERT INTO `states` VALUES (3028,'Mordovija',181);
INSERT INTO `states` VALUES (3029,'Moscow',181);
INSERT INTO `states` VALUES (3030,'Moskovskaja Oblast',181);
INSERT INTO `states` VALUES (3031,'Moskovskaya Oblast',181);
INSERT INTO `states` VALUES (3032,'Moskva',181);
INSERT INTO `states` VALUES (3033,'Murmansk',181);
INSERT INTO `states` VALUES (3034,'Nenets',181);
INSERT INTO `states` VALUES (3035,'Nizhnij Novgorod',181);
INSERT INTO `states` VALUES (3036,'Novgorod',181);
INSERT INTO `states` VALUES (3037,'Novokusnezk',181);
INSERT INTO `states` VALUES (3038,'Novosibirsk',181);
INSERT INTO `states` VALUES (3039,'Omsk',181);
INSERT INTO `states` VALUES (3040,'Orenburg',181);
INSERT INTO `states` VALUES (3041,'Orjol',181);
INSERT INTO `states` VALUES (3042,'Penza',181);
INSERT INTO `states` VALUES (3043,'Perm',181);
INSERT INTO `states` VALUES (3044,'Primorje',181);
INSERT INTO `states` VALUES (3045,'Pskov',181);
INSERT INTO `states` VALUES (3046,'Pskovskaya Oblast',181);
INSERT INTO `states` VALUES (3047,'Rjazan',181);
INSERT INTO `states` VALUES (3048,'Rostov',181);
INSERT INTO `states` VALUES (3049,'Saha',181);
INSERT INTO `states` VALUES (3050,'Sahalin',181);
INSERT INTO `states` VALUES (3051,'Samara',181);
INSERT INTO `states` VALUES (3052,'Samarskaya',181);
INSERT INTO `states` VALUES (3053,'Sankt-Peterburg',181);
INSERT INTO `states` VALUES (3054,'Saratov',181);
INSERT INTO `states` VALUES (3055,'Smolensk',181);
INSERT INTO `states` VALUES (3056,'Stavropol',181);
INSERT INTO `states` VALUES (3057,'Sverdlovsk',181);
INSERT INTO `states` VALUES (3058,'Tajmyrija',181);
INSERT INTO `states` VALUES (3059,'Tambov',181);
INSERT INTO `states` VALUES (3060,'Tatarstan',181);
INSERT INTO `states` VALUES (3061,'Tjumen',181);
INSERT INTO `states` VALUES (3062,'Tomsk',181);
INSERT INTO `states` VALUES (3063,'Tula',181);
INSERT INTO `states` VALUES (3064,'Tver',181);
INSERT INTO `states` VALUES (3065,'Tyva',181);
INSERT INTO `states` VALUES (3066,'Udmurtija',181);
INSERT INTO `states` VALUES (3067,'Uljanovsk',181);
INSERT INTO `states` VALUES (3068,'Ulyanovskaya Oblast',181);
INSERT INTO `states` VALUES (3069,'Ust-Orda',181);
INSERT INTO `states` VALUES (3070,'Vladimir',181);
INSERT INTO `states` VALUES (3071,'Volgograd',181);
INSERT INTO `states` VALUES (3072,'Vologda',181);
INSERT INTO `states` VALUES (3073,'Voronezh',181);
INSERT INTO `states` VALUES (3074,'Butare',182);
INSERT INTO `states` VALUES (3075,'Byumba',182);
INSERT INTO `states` VALUES (3076,'Cyangugu',182);
INSERT INTO `states` VALUES (3077,'Gikongoro',182);
INSERT INTO `states` VALUES (3078,'Gisenyi',182);
INSERT INTO `states` VALUES (3079,'Gitarama',182);
INSERT INTO `states` VALUES (3080,'Kibungo',182);
INSERT INTO `states` VALUES (3081,'Kibuye',182);
INSERT INTO `states` VALUES (3082,'Kigali-ngali',182);
INSERT INTO `states` VALUES (3083,'Ruhengeri',182);
INSERT INTO `states` VALUES (3084,'Ascension',183);
INSERT INTO `states` VALUES (3085,'Gough Island',183);
INSERT INTO `states` VALUES (3086,'Saint Helena',183);
INSERT INTO `states` VALUES (3087,'Tristan da Cunha',183);
INSERT INTO `states` VALUES (3088,'Christ Church Nichola Town',184);
INSERT INTO `states` VALUES (3089,'Saint Anne Sandy Point',184);
INSERT INTO `states` VALUES (3090,'Saint George Basseterre',184);
INSERT INTO `states` VALUES (3091,'Saint George Gingerland',184);
INSERT INTO `states` VALUES (3092,'Saint James Windward',184);
INSERT INTO `states` VALUES (3093,'Saint John Capesterre',184);
INSERT INTO `states` VALUES (3094,'Saint John Figtree',184);
INSERT INTO `states` VALUES (3095,'Saint Mary Cayon',184);
INSERT INTO `states` VALUES (3096,'Saint Paul Capesterre',184);
INSERT INTO `states` VALUES (3097,'Saint Paul Charlestown',184);
INSERT INTO `states` VALUES (3098,'Saint Peter Basseterre',184);
INSERT INTO `states` VALUES (3099,'Saint Thomas Lowland',184);
INSERT INTO `states` VALUES (3100,'Saint Thomas Middle Island',184);
INSERT INTO `states` VALUES (3101,'Trinity Palmetto Point',184);
INSERT INTO `states` VALUES (3102,'Anse-la-Raye',185);
INSERT INTO `states` VALUES (3103,'Canaries',185);
INSERT INTO `states` VALUES (3104,'Castries',185);
INSERT INTO `states` VALUES (3105,'Choiseul',185);
INSERT INTO `states` VALUES (3106,'Dennery',185);
INSERT INTO `states` VALUES (3107,'Gros Inlet',185);
INSERT INTO `states` VALUES (3108,'Laborie',185);
INSERT INTO `states` VALUES (3109,'Micoud',185);
INSERT INTO `states` VALUES (3110,'Soufriere',185);
INSERT INTO `states` VALUES (3111,'Vieux Fort',185);
INSERT INTO `states` VALUES (3112,'Miquelon-Langlade',186);
INSERT INTO `states` VALUES (3113,'Saint-Pierre',186);
INSERT INTO `states` VALUES (3114,'Charlotte',187);
INSERT INTO `states` VALUES (3115,'Grenadines',187);
INSERT INTO `states` VALUES (3116,'Saint Andrew',187);
INSERT INTO `states` VALUES (3117,'Saint David',187);
INSERT INTO `states` VALUES (3118,'Saint George',187);
INSERT INTO `states` VALUES (3119,'Saint Patrick',187);
INSERT INTO `states` VALUES (3120,'A\'ana',188);
INSERT INTO `states` VALUES (3121,'Aiga-i-le-Tai',188);
INSERT INTO `states` VALUES (3122,'Atua',188);
INSERT INTO `states` VALUES (3123,'Fa\'asaleleaga',188);
INSERT INTO `states` VALUES (3124,'Gaga\'emauga',188);
INSERT INTO `states` VALUES (3125,'Gagaifomauga',188);
INSERT INTO `states` VALUES (3126,'Palauli',188);
INSERT INTO `states` VALUES (3127,'Satupa\'itea',188);
INSERT INTO `states` VALUES (3128,'Tuamasaga',188);
INSERT INTO `states` VALUES (3129,'Va\'a-o-Fonoti',188);
INSERT INTO `states` VALUES (3130,'Vaisigano',188);
INSERT INTO `states` VALUES (3131,'Acquaviva',189);
INSERT INTO `states` VALUES (3132,'Borgo Maggiore',189);
INSERT INTO `states` VALUES (3133,'Chiesanuova',189);
INSERT INTO `states` VALUES (3134,'Domagnano',189);
INSERT INTO `states` VALUES (3135,'Faetano',189);
INSERT INTO `states` VALUES (3136,'Fiorentino',189);
INSERT INTO `states` VALUES (3137,'Montegiardino',189);
INSERT INTO `states` VALUES (3138,'San Marino',189);
INSERT INTO `states` VALUES (3139,'Serravalle',189);
INSERT INTO `states` VALUES (3140,'Agua Grande',190);
INSERT INTO `states` VALUES (3141,'Cantagalo',190);
INSERT INTO `states` VALUES (3142,'Lemba',190);
INSERT INTO `states` VALUES (3143,'Lobata',190);
INSERT INTO `states` VALUES (3144,'Me-Zochi',190);
INSERT INTO `states` VALUES (3145,'Pague',190);
INSERT INTO `states` VALUES (3146,'Al Khobar',191);
INSERT INTO `states` VALUES (3147,'Aseer',191);
INSERT INTO `states` VALUES (3148,'Ash Sharqiyah',191);
INSERT INTO `states` VALUES (3149,'Asir',191);
INSERT INTO `states` VALUES (3150,'Central Province',191);
INSERT INTO `states` VALUES (3151,'Eastern Province',191);
INSERT INTO `states` VALUES (3152,'Ha\'il',191);
INSERT INTO `states` VALUES (3153,'Jawf',191);
INSERT INTO `states` VALUES (3154,'Jizan',191);
INSERT INTO `states` VALUES (3155,'Makkah',191);
INSERT INTO `states` VALUES (3156,'Najran',191);
INSERT INTO `states` VALUES (3157,'Qasim',191);
INSERT INTO `states` VALUES (3158,'Tabuk',191);
INSERT INTO `states` VALUES (3159,'Western Province',191);
INSERT INTO `states` VALUES (3160,'al-Bahah',191);
INSERT INTO `states` VALUES (3161,'al-Hudud-ash-Shamaliyah',191);
INSERT INTO `states` VALUES (3162,'al-Madinah',191);
INSERT INTO `states` VALUES (3163,'ar-Riyad',191);
INSERT INTO `states` VALUES (3164,'Dakar',192);
INSERT INTO `states` VALUES (3165,'Diourbel',192);
INSERT INTO `states` VALUES (3166,'Fatick',192);
INSERT INTO `states` VALUES (3167,'Kaolack',192);
INSERT INTO `states` VALUES (3168,'Kolda',192);
INSERT INTO `states` VALUES (3169,'Louga',192);
INSERT INTO `states` VALUES (3170,'Saint-Louis',192);
INSERT INTO `states` VALUES (3171,'Tambacounda',192);
INSERT INTO `states` VALUES (3172,'Thies',192);
INSERT INTO `states` VALUES (3173,'Ziguinchor',192);
INSERT INTO `states` VALUES (3174,'Central Serbia',193);
INSERT INTO `states` VALUES (3175,'Kosovo and Metohija',193);
INSERT INTO `states` VALUES (3176,'Vojvodina',193);
INSERT INTO `states` VALUES (3177,'Anse Boileau',194);
INSERT INTO `states` VALUES (3178,'Anse Royale',194);
INSERT INTO `states` VALUES (3179,'Cascade',194);
INSERT INTO `states` VALUES (3180,'Takamaka',194);
INSERT INTO `states` VALUES (3181,'Victoria',194);
INSERT INTO `states` VALUES (3182,'Eastern',195);
INSERT INTO `states` VALUES (3183,'Northern',195);
INSERT INTO `states` VALUES (3184,'Southern',195);
INSERT INTO `states` VALUES (3185,'Western',195);
INSERT INTO `states` VALUES (3186,'Singapore',196);
INSERT INTO `states` VALUES (3187,'Banskobystricky',197);
INSERT INTO `states` VALUES (3188,'Bratislavsky',197);
INSERT INTO `states` VALUES (3189,'Kosicky',197);
INSERT INTO `states` VALUES (3190,'Nitriansky',197);
INSERT INTO `states` VALUES (3191,'Presovsky',197);
INSERT INTO `states` VALUES (3192,'Trenciansky',197);
INSERT INTO `states` VALUES (3193,'Trnavsky',197);
INSERT INTO `states` VALUES (3194,'Zilinsky',197);
INSERT INTO `states` VALUES (3195,'Benedikt',198);
INSERT INTO `states` VALUES (3196,'Gorenjska',198);
INSERT INTO `states` VALUES (3197,'Gorishka',198);
INSERT INTO `states` VALUES (3198,'Jugovzhodna Slovenija',198);
INSERT INTO `states` VALUES (3199,'Koroshka',198);
INSERT INTO `states` VALUES (3200,'Notranjsko-krashka',198);
INSERT INTO `states` VALUES (3201,'Obalno-krashka',198);
INSERT INTO `states` VALUES (3202,'Obcina Domzale',198);
INSERT INTO `states` VALUES (3203,'Obcina Vitanje',198);
INSERT INTO `states` VALUES (3204,'Osrednjeslovenska',198);
INSERT INTO `states` VALUES (3205,'Podravska',198);
INSERT INTO `states` VALUES (3206,'Pomurska',198);
INSERT INTO `states` VALUES (3207,'Savinjska',198);
INSERT INTO `states` VALUES (3208,'Slovenian Littoral',198);
INSERT INTO `states` VALUES (3209,'Spodnjeposavska',198);
INSERT INTO `states` VALUES (3210,'Zasavska',198);
INSERT INTO `states` VALUES (3211,'Pitcairn',199);
INSERT INTO `states` VALUES (3212,'Central',200);
INSERT INTO `states` VALUES (3213,'Choiseul',200);
INSERT INTO `states` VALUES (3214,'Guadalcanal',200);
INSERT INTO `states` VALUES (3215,'Isabel',200);
INSERT INTO `states` VALUES (3216,'Makira and Ulawa',200);
INSERT INTO `states` VALUES (3217,'Malaita',200);
INSERT INTO `states` VALUES (3218,'Rennell and Bellona',200);
INSERT INTO `states` VALUES (3219,'Temotu',200);
INSERT INTO `states` VALUES (3220,'Western',200);
INSERT INTO `states` VALUES (3221,'Awdal',201);
INSERT INTO `states` VALUES (3222,'Bakol',201);
INSERT INTO `states` VALUES (3223,'Banadir',201);
INSERT INTO `states` VALUES (3224,'Bari',201);
INSERT INTO `states` VALUES (3225,'Bay',201);
INSERT INTO `states` VALUES (3226,'Galgudug',201);
INSERT INTO `states` VALUES (3227,'Gedo',201);
INSERT INTO `states` VALUES (3228,'Hiran',201);
INSERT INTO `states` VALUES (3229,'Jubbada Hose',201);
INSERT INTO `states` VALUES (3230,'Jubbadha Dexe',201);
INSERT INTO `states` VALUES (3231,'Mudug',201);
INSERT INTO `states` VALUES (3232,'Nugal',201);
INSERT INTO `states` VALUES (3233,'Sanag',201);
INSERT INTO `states` VALUES (3234,'Shabellaha Dhexe',201);
INSERT INTO `states` VALUES (3235,'Shabellaha Hose',201);
INSERT INTO `states` VALUES (3236,'Togdher',201);
INSERT INTO `states` VALUES (3237,'Woqoyi Galbed',201);
INSERT INTO `states` VALUES (3238,'Eastern Cape',202);
INSERT INTO `states` VALUES (3239,'Free State',202);
INSERT INTO `states` VALUES (3240,'Gauteng',202);
INSERT INTO `states` VALUES (3241,'Kempton Park',202);
INSERT INTO `states` VALUES (3242,'Kramerville',202);
INSERT INTO `states` VALUES (3243,'KwaZulu Natal',202);
INSERT INTO `states` VALUES (3244,'Limpopo',202);
INSERT INTO `states` VALUES (3245,'Mpumalanga',202);
INSERT INTO `states` VALUES (3246,'North West',202);
INSERT INTO `states` VALUES (3247,'Northern Cape',202);
INSERT INTO `states` VALUES (3248,'Parow',202);
INSERT INTO `states` VALUES (3249,'Table View',202);
INSERT INTO `states` VALUES (3250,'Umtentweni',202);
INSERT INTO `states` VALUES (3251,'Western Cape',202);
INSERT INTO `states` VALUES (3252,'South Georgia',203);
INSERT INTO `states` VALUES (3253,'Central Equatoria',204);
INSERT INTO `states` VALUES (3254,'A Coruna',205);
INSERT INTO `states` VALUES (3255,'Alacant',205);
INSERT INTO `states` VALUES (3256,'Alava',205);
INSERT INTO `states` VALUES (3257,'Albacete',205);
INSERT INTO `states` VALUES (3258,'Almeria',205);
INSERT INTO `states` VALUES (3260,'Asturias',205);
INSERT INTO `states` VALUES (3261,'Avila',205);
INSERT INTO `states` VALUES (3262,'Badajoz',205);
INSERT INTO `states` VALUES (3263,'Balears',205);
INSERT INTO `states` VALUES (3264,'Barcelona',205);
INSERT INTO `states` VALUES (3267,'Burgos',205);
INSERT INTO `states` VALUES (3268,'Caceres',205);
INSERT INTO `states` VALUES (3269,'Cadiz',205);
INSERT INTO `states` VALUES (3270,'Cantabria',205);
INSERT INTO `states` VALUES (3271,'Castello',205);
INSERT INTO `states` VALUES (3273,'Ceuta',205);
INSERT INTO `states` VALUES (3274,'Ciudad Real',205);
INSERT INTO `states` VALUES (3281,'Cordoba',205);
INSERT INTO `states` VALUES (3282,'Cuenca',205);
INSERT INTO `states` VALUES (3284,'Girona',205);
INSERT INTO `states` VALUES (3285,'Granada',205);
INSERT INTO `states` VALUES (3286,'Guadalajara',205);
INSERT INTO `states` VALUES (3287,'Guipuzcoa',205);
INSERT INTO `states` VALUES (3288,'Huelva',205);
INSERT INTO `states` VALUES (3289,'Huesca',205);
INSERT INTO `states` VALUES (3290,'Jaen',205);
INSERT INTO `states` VALUES (3291,'La Rioja',205);
INSERT INTO `states` VALUES (3292,'Las Palmas',205);
INSERT INTO `states` VALUES (3293,'Leon',205);
INSERT INTO `states` VALUES (3295,'Lleida',205);
INSERT INTO `states` VALUES (3296,'Lugo',205);
INSERT INTO `states` VALUES (3297,'Madrid',205);
INSERT INTO `states` VALUES (3298,'Malaga',205);
INSERT INTO `states` VALUES (3299,'Melilla',205);
INSERT INTO `states` VALUES (3300,'Murcia',205);
INSERT INTO `states` VALUES (3301,'Navarra',205);
INSERT INTO `states` VALUES (3302,'Ourense',205);
INSERT INTO `states` VALUES (3303,'Pais Vasco',205);
INSERT INTO `states` VALUES (3304,'Palencia',205);
INSERT INTO `states` VALUES (3305,'Pontevedra',205);
INSERT INTO `states` VALUES (3306,'Salamanca',205);
INSERT INTO `states` VALUES (3308,'Segovia',205);
INSERT INTO `states` VALUES (3309,'Sevilla',205);
INSERT INTO `states` VALUES (3310,'Soria',205);
INSERT INTO `states` VALUES (3311,'Tarragona',205);
INSERT INTO `states` VALUES (3312,'Santa Cruz de Tenerife',205);
INSERT INTO `states` VALUES (3313,'Teruel',205);
INSERT INTO `states` VALUES (3314,'Toledo',205);
INSERT INTO `states` VALUES (3315,'Valencia',205);
INSERT INTO `states` VALUES (3316,'Valladolid',205);
INSERT INTO `states` VALUES (3317,'Vizcaya',205);
INSERT INTO `states` VALUES (3318,'Zamora',205);
INSERT INTO `states` VALUES (3319,'Zaragoza',205);
INSERT INTO `states` VALUES (3320,'Amparai',206);
INSERT INTO `states` VALUES (3321,'Anuradhapuraya',206);
INSERT INTO `states` VALUES (3322,'Badulla',206);
INSERT INTO `states` VALUES (3323,'Boralesgamuwa',206);
INSERT INTO `states` VALUES (3324,'Colombo',206);
INSERT INTO `states` VALUES (3325,'Galla',206);
INSERT INTO `states` VALUES (3326,'Gampaha',206);
INSERT INTO `states` VALUES (3327,'Hambantota',206);
INSERT INTO `states` VALUES (3328,'Kalatura',206);
INSERT INTO `states` VALUES (3329,'Kegalla',206);
INSERT INTO `states` VALUES (3330,'Kilinochchi',206);
INSERT INTO `states` VALUES (3331,'Kurunegala',206);
INSERT INTO `states` VALUES (3332,'Madakalpuwa',206);
INSERT INTO `states` VALUES (3333,'Maha Nuwara',206);
INSERT INTO `states` VALUES (3334,'Malwana',206);
INSERT INTO `states` VALUES (3335,'Mannarama',206);
INSERT INTO `states` VALUES (3336,'Matale',206);
INSERT INTO `states` VALUES (3337,'Matara',206);
INSERT INTO `states` VALUES (3338,'Monaragala',206);
INSERT INTO `states` VALUES (3339,'Mullaitivu',206);
INSERT INTO `states` VALUES (3340,'North Eastern Province',206);
INSERT INTO `states` VALUES (3341,'North Western Province',206);
INSERT INTO `states` VALUES (3342,'Nuwara Eliya',206);
INSERT INTO `states` VALUES (3343,'Polonnaruwa',206);
INSERT INTO `states` VALUES (3344,'Puttalama',206);
INSERT INTO `states` VALUES (3345,'Ratnapuraya',206);
INSERT INTO `states` VALUES (3346,'Southern Province',206);
INSERT INTO `states` VALUES (3347,'Tirikunamalaya',206);
INSERT INTO `states` VALUES (3348,'Tuscany',206);
INSERT INTO `states` VALUES (3349,'Vavuniyawa',206);
INSERT INTO `states` VALUES (3350,'Western Province',206);
INSERT INTO `states` VALUES (3351,'Yapanaya',206);
INSERT INTO `states` VALUES (3352,'kadawatha',206);
INSERT INTO `states` VALUES (3353,'A\'ali-an-Nil',207);
INSERT INTO `states` VALUES (3354,'Bahr-al-Jabal',207);
INSERT INTO `states` VALUES (3355,'Central Equatoria',207);
INSERT INTO `states` VALUES (3356,'Gharb Bahr-al-Ghazal',207);
INSERT INTO `states` VALUES (3357,'Gharb Darfur',207);
INSERT INTO `states` VALUES (3358,'Gharb Kurdufan',207);
INSERT INTO `states` VALUES (3359,'Gharb-al-Istiwa\'iyah',207);
INSERT INTO `states` VALUES (3360,'Janub Darfur',207);
INSERT INTO `states` VALUES (3361,'Janub Kurdufan',207);
INSERT INTO `states` VALUES (3362,'Junqali',207);
INSERT INTO `states` VALUES (3363,'Kassala',207);
INSERT INTO `states` VALUES (3364,'Nahr-an-Nil',207);
INSERT INTO `states` VALUES (3365,'Shamal Bahr-al-Ghazal',207);
INSERT INTO `states` VALUES (3366,'Shamal Darfur',207);
INSERT INTO `states` VALUES (3367,'Shamal Kurdufan',207);
INSERT INTO `states` VALUES (3368,'Sharq-al-Istiwa\'iyah',207);
INSERT INTO `states` VALUES (3369,'Sinnar',207);
INSERT INTO `states` VALUES (3370,'Warab',207);
INSERT INTO `states` VALUES (3371,'Wilayat al Khartum',207);
INSERT INTO `states` VALUES (3372,'al-Bahr-al-Ahmar',207);
INSERT INTO `states` VALUES (3373,'al-Buhayrat',207);
INSERT INTO `states` VALUES (3374,'al-Jazirah',207);
INSERT INTO `states` VALUES (3375,'al-Khartum',207);
INSERT INTO `states` VALUES (3376,'al-Qadarif',207);
INSERT INTO `states` VALUES (3377,'al-Wahdah',207);
INSERT INTO `states` VALUES (3378,'an-Nil-al-Abyad',207);
INSERT INTO `states` VALUES (3379,'an-Nil-al-Azraq',207);
INSERT INTO `states` VALUES (3380,'ash-Shamaliyah',207);
INSERT INTO `states` VALUES (3381,'Brokopondo',208);
INSERT INTO `states` VALUES (3382,'Commewijne',208);
INSERT INTO `states` VALUES (3383,'Coronie',208);
INSERT INTO `states` VALUES (3384,'Marowijne',208);
INSERT INTO `states` VALUES (3385,'Nickerie',208);
INSERT INTO `states` VALUES (3386,'Para',208);
INSERT INTO `states` VALUES (3387,'Paramaribo',208);
INSERT INTO `states` VALUES (3388,'Saramacca',208);
INSERT INTO `states` VALUES (3389,'Wanica',208);
INSERT INTO `states` VALUES (3390,'Svalbard',209);
INSERT INTO `states` VALUES (3391,'Hhohho',210);
INSERT INTO `states` VALUES (3392,'Lubombo',210);
INSERT INTO `states` VALUES (3393,'Manzini',210);
INSERT INTO `states` VALUES (3394,'Shiselweni',210);
INSERT INTO `states` VALUES (3395,'Alvsborgs Lan',211);
INSERT INTO `states` VALUES (3396,'Angermanland',211);
INSERT INTO `states` VALUES (3397,'Blekinge',211);
INSERT INTO `states` VALUES (3398,'Bohuslan',211);
INSERT INTO `states` VALUES (3399,'Dalarna',211);
INSERT INTO `states` VALUES (3400,'Gavleborg',211);
INSERT INTO `states` VALUES (3401,'Gaza',211);
INSERT INTO `states` VALUES (3402,'Gotland',211);
INSERT INTO `states` VALUES (3403,'Halland',211);
INSERT INTO `states` VALUES (3404,'Jamtland',211);
INSERT INTO `states` VALUES (3405,'Jonkoping',211);
INSERT INTO `states` VALUES (3406,'Kalmar',211);
INSERT INTO `states` VALUES (3407,'Kristianstads',211);
INSERT INTO `states` VALUES (3408,'Kronoberg',211);
INSERT INTO `states` VALUES (3409,'Norrbotten',211);
INSERT INTO `states` VALUES (3410,'Orebro',211);
INSERT INTO `states` VALUES (3411,'Ostergotland',211);
INSERT INTO `states` VALUES (3412,'Saltsjo-Boo',211);
INSERT INTO `states` VALUES (3413,'Skane',211);
INSERT INTO `states` VALUES (3414,'Smaland',211);
INSERT INTO `states` VALUES (3415,'Sodermanland',211);
INSERT INTO `states` VALUES (3416,'Stockholm',211);
INSERT INTO `states` VALUES (3417,'Uppsala',211);
INSERT INTO `states` VALUES (3418,'Varmland',211);
INSERT INTO `states` VALUES (3419,'Vasterbotten',211);
INSERT INTO `states` VALUES (3420,'Vastergotland',211);
INSERT INTO `states` VALUES (3421,'Vasternorrland',211);
INSERT INTO `states` VALUES (3422,'Vastmanland',211);
INSERT INTO `states` VALUES (3423,'Vastra Gotaland',211);
INSERT INTO `states` VALUES (3424,'Aargau',212);
INSERT INTO `states` VALUES (3425,'Appenzell Inner-Rhoden',212);
INSERT INTO `states` VALUES (3426,'Appenzell-Ausser Rhoden',212);
INSERT INTO `states` VALUES (3427,'Basel-Landschaft',212);
INSERT INTO `states` VALUES (3428,'Basel-Stadt',212);
INSERT INTO `states` VALUES (3429,'Bern',212);
INSERT INTO `states` VALUES (3430,'Canton Ticino',212);
INSERT INTO `states` VALUES (3431,'Fribourg',212);
INSERT INTO `states` VALUES (3432,'Geneve',212);
INSERT INTO `states` VALUES (3433,'Glarus',212);
INSERT INTO `states` VALUES (3434,'Graubunden',212);
INSERT INTO `states` VALUES (3435,'Heerbrugg',212);
INSERT INTO `states` VALUES (3436,'Jura',212);
INSERT INTO `states` VALUES (3437,'Kanton Aargau',212);
INSERT INTO `states` VALUES (3438,'Luzern',212);
INSERT INTO `states` VALUES (3439,'Morbio Inferiore',212);
INSERT INTO `states` VALUES (3440,'Muhen',212);
INSERT INTO `states` VALUES (3441,'Neuchatel',212);
INSERT INTO `states` VALUES (3442,'Nidwalden',212);
INSERT INTO `states` VALUES (3443,'Obwalden',212);
INSERT INTO `states` VALUES (3444,'Sankt Gallen',212);
INSERT INTO `states` VALUES (3445,'Schaffhausen',212);
INSERT INTO `states` VALUES (3446,'Schwyz',212);
INSERT INTO `states` VALUES (3447,'Solothurn',212);
INSERT INTO `states` VALUES (3448,'Thurgau',212);
INSERT INTO `states` VALUES (3449,'Ticino',212);
INSERT INTO `states` VALUES (3450,'Uri',212);
INSERT INTO `states` VALUES (3451,'Valais',212);
INSERT INTO `states` VALUES (3452,'Vaud',212);
INSERT INTO `states` VALUES (3453,'Vauffelin',212);
INSERT INTO `states` VALUES (3454,'Zug',212);
INSERT INTO `states` VALUES (3455,'Zurich',212);
INSERT INTO `states` VALUES (3456,'Aleppo',213);
INSERT INTO `states` VALUES (3457,'Dar\'a',213);
INSERT INTO `states` VALUES (3458,'Dayr-az-Zawr',213);
INSERT INTO `states` VALUES (3459,'Dimashq',213);
INSERT INTO `states` VALUES (3460,'Halab',213);
INSERT INTO `states` VALUES (3461,'Hamah',213);
INSERT INTO `states` VALUES (3462,'Hims',213);
INSERT INTO `states` VALUES (3463,'Idlib',213);
INSERT INTO `states` VALUES (3464,'Madinat Dimashq',213);
INSERT INTO `states` VALUES (3465,'Tartus',213);
INSERT INTO `states` VALUES (3466,'al-Hasakah',213);
INSERT INTO `states` VALUES (3467,'al-Ladhiqiyah',213);
INSERT INTO `states` VALUES (3468,'al-Qunaytirah',213);
INSERT INTO `states` VALUES (3469,'ar-Raqqah',213);
INSERT INTO `states` VALUES (3470,'as-Suwayda',213);
INSERT INTO `states` VALUES (3471,'Changhua County',214);
INSERT INTO `states` VALUES (3472,'Chiayi County',214);
INSERT INTO `states` VALUES (3473,'Chiayi City',214);
INSERT INTO `states` VALUES (3474,'Taipei City',214);
INSERT INTO `states` VALUES (3475,'Hsinchu County',214);
INSERT INTO `states` VALUES (3476,'Hsinchu City',214);
INSERT INTO `states` VALUES (3477,'Hualien County',214);
INSERT INTO `states` VALUES (3480,'Kaohsiung City',214);
INSERT INTO `states` VALUES (3481,'Keelung City',214);
INSERT INTO `states` VALUES (3482,'Kinmen County',214);
INSERT INTO `states` VALUES (3483,'Miaoli County',214);
INSERT INTO `states` VALUES (3484,'Nantou County',214);
INSERT INTO `states` VALUES (3486,'Penghu County',214);
INSERT INTO `states` VALUES (3487,'Pingtung County',214);
INSERT INTO `states` VALUES (3488,'Taichung City',214);
INSERT INTO `states` VALUES (3492,'Tainan City',214);
INSERT INTO `states` VALUES (3493,'New Taipei City',214);
INSERT INTO `states` VALUES (3495,'Taitung County',214);
INSERT INTO `states` VALUES (3496,'Taoyuan City',214);
INSERT INTO `states` VALUES (3497,'Yilan County',214);
INSERT INTO `states` VALUES (3498,'YunLin County',214);
INSERT INTO `states` VALUES (3500,'Dushanbe',215);
INSERT INTO `states` VALUES (3501,'Gorno-Badakhshan',215);
INSERT INTO `states` VALUES (3502,'Karotegin',215);
INSERT INTO `states` VALUES (3503,'Khatlon',215);
INSERT INTO `states` VALUES (3504,'Sughd',215);
INSERT INTO `states` VALUES (3505,'Arusha',216);
INSERT INTO `states` VALUES (3506,'Dar es Salaam',216);
INSERT INTO `states` VALUES (3507,'Dodoma',216);
INSERT INTO `states` VALUES (3508,'Iringa',216);
INSERT INTO `states` VALUES (3509,'Kagera',216);
INSERT INTO `states` VALUES (3510,'Kigoma',216);
INSERT INTO `states` VALUES (3511,'Kilimanjaro',216);
INSERT INTO `states` VALUES (3512,'Lindi',216);
INSERT INTO `states` VALUES (3513,'Mara',216);
INSERT INTO `states` VALUES (3514,'Mbeya',216);
INSERT INTO `states` VALUES (3515,'Morogoro',216);
INSERT INTO `states` VALUES (3516,'Mtwara',216);
INSERT INTO `states` VALUES (3517,'Mwanza',216);
INSERT INTO `states` VALUES (3518,'Pwani',216);
INSERT INTO `states` VALUES (3519,'Rukwa',216);
INSERT INTO `states` VALUES (3520,'Ruvuma',216);
INSERT INTO `states` VALUES (3521,'Shinyanga',216);
INSERT INTO `states` VALUES (3522,'Singida',216);
INSERT INTO `states` VALUES (3523,'Tabora',216);
INSERT INTO `states` VALUES (3524,'Tanga',216);
INSERT INTO `states` VALUES (3525,'Zanzibar and Pemba',216);
INSERT INTO `states` VALUES (3526,'Amnat Charoen',217);
INSERT INTO `states` VALUES (3527,'Ang Thong',217);
INSERT INTO `states` VALUES (3528,'Bangkok',217);
INSERT INTO `states` VALUES (3529,'Buri Ram',217);
INSERT INTO `states` VALUES (3530,'Chachoengsao',217);
INSERT INTO `states` VALUES (3531,'Chai Nat',217);
INSERT INTO `states` VALUES (3532,'Chaiyaphum',217);
INSERT INTO `states` VALUES (3533,'Changwat Chaiyaphum',217);
INSERT INTO `states` VALUES (3534,'Chanthaburi',217);
INSERT INTO `states` VALUES (3535,'Chiang Mai',217);
INSERT INTO `states` VALUES (3536,'Chiang Rai',217);
INSERT INTO `states` VALUES (3537,'Chon Buri',217);
INSERT INTO `states` VALUES (3538,'Chumphon',217);
INSERT INTO `states` VALUES (3539,'Kalasin',217);
INSERT INTO `states` VALUES (3540,'Kamphaeng Phet',217);
INSERT INTO `states` VALUES (3541,'Kanchanaburi',217);
INSERT INTO `states` VALUES (3542,'Khon Kaen',217);
INSERT INTO `states` VALUES (3543,'Krabi',217);
INSERT INTO `states` VALUES (3544,'Krung Thep',217);
INSERT INTO `states` VALUES (3545,'Lampang',217);
INSERT INTO `states` VALUES (3546,'Lamphun',217);
INSERT INTO `states` VALUES (3547,'Loei',217);
INSERT INTO `states` VALUES (3548,'Lop Buri',217);
INSERT INTO `states` VALUES (3549,'Mae Hong Son',217);
INSERT INTO `states` VALUES (3550,'Maha Sarakham',217);
INSERT INTO `states` VALUES (3551,'Mukdahan',217);
INSERT INTO `states` VALUES (3552,'Nakhon Nayok',217);
INSERT INTO `states` VALUES (3553,'Nakhon Pathom',217);
INSERT INTO `states` VALUES (3554,'Nakhon Phanom',217);
INSERT INTO `states` VALUES (3555,'Nakhon Ratchasima',217);
INSERT INTO `states` VALUES (3556,'Nakhon Sawan',217);
INSERT INTO `states` VALUES (3557,'Nakhon Si Thammarat',217);
INSERT INTO `states` VALUES (3558,'Nan',217);
INSERT INTO `states` VALUES (3559,'Narathiwat',217);
INSERT INTO `states` VALUES (3560,'Nong Bua Lam Phu',217);
INSERT INTO `states` VALUES (3561,'Nong Khai',217);
INSERT INTO `states` VALUES (3562,'Nonthaburi',217);
INSERT INTO `states` VALUES (3563,'Pathum Thani',217);
INSERT INTO `states` VALUES (3564,'Pattani',217);
INSERT INTO `states` VALUES (3565,'Phangnga',217);
INSERT INTO `states` VALUES (3566,'Phatthalung',217);
INSERT INTO `states` VALUES (3567,'Phayao',217);
INSERT INTO `states` VALUES (3568,'Phetchabun',217);
INSERT INTO `states` VALUES (3569,'Phetchaburi',217);
INSERT INTO `states` VALUES (3570,'Phichit',217);
INSERT INTO `states` VALUES (3571,'Phitsanulok',217);
INSERT INTO `states` VALUES (3572,'Phra Nakhon Si Ayutthaya',217);
INSERT INTO `states` VALUES (3573,'Phrae',217);
INSERT INTO `states` VALUES (3574,'Phuket',217);
INSERT INTO `states` VALUES (3575,'Prachin Buri',217);
INSERT INTO `states` VALUES (3576,'Prachuap Khiri Khan',217);
INSERT INTO `states` VALUES (3577,'Ranong',217);
INSERT INTO `states` VALUES (3578,'Ratchaburi',217);
INSERT INTO `states` VALUES (3579,'Rayong',217);
INSERT INTO `states` VALUES (3580,'Roi Et',217);
INSERT INTO `states` VALUES (3581,'Sa Kaeo',217);
INSERT INTO `states` VALUES (3582,'Sakon Nakhon',217);
INSERT INTO `states` VALUES (3583,'Samut Prakan',217);
INSERT INTO `states` VALUES (3584,'Samut Sakhon',217);
INSERT INTO `states` VALUES (3585,'Samut Songkhran',217);
INSERT INTO `states` VALUES (3586,'Saraburi',217);
INSERT INTO `states` VALUES (3587,'Satun',217);
INSERT INTO `states` VALUES (3588,'Si Sa Ket',217);
INSERT INTO `states` VALUES (3589,'Sing Buri',217);
INSERT INTO `states` VALUES (3590,'Songkhla',217);
INSERT INTO `states` VALUES (3591,'Sukhothai',217);
INSERT INTO `states` VALUES (3592,'Suphan Buri',217);
INSERT INTO `states` VALUES (3593,'Surat Thani',217);
INSERT INTO `states` VALUES (3594,'Surin',217);
INSERT INTO `states` VALUES (3595,'Tak',217);
INSERT INTO `states` VALUES (3596,'Trang',217);
INSERT INTO `states` VALUES (3597,'Trat',217);
INSERT INTO `states` VALUES (3598,'Ubon Ratchathani',217);
INSERT INTO `states` VALUES (3599,'Udon Thani',217);
INSERT INTO `states` VALUES (3600,'Uthai Thani',217);
INSERT INTO `states` VALUES (3601,'Uttaradit',217);
INSERT INTO `states` VALUES (3602,'Yala',217);
INSERT INTO `states` VALUES (3603,'Yasothon',217);
INSERT INTO `states` VALUES (3604,'Centre',218);
INSERT INTO `states` VALUES (3605,'Kara',218);
INSERT INTO `states` VALUES (3606,'Maritime',218);
INSERT INTO `states` VALUES (3607,'Plateaux',218);
INSERT INTO `states` VALUES (3608,'Savanes',218);
INSERT INTO `states` VALUES (3609,'Atafu',219);
INSERT INTO `states` VALUES (3610,'Fakaofo',219);
INSERT INTO `states` VALUES (3611,'Nukunonu',219);
INSERT INTO `states` VALUES (3612,'Eua',220);
INSERT INTO `states` VALUES (3613,'Ha\'apai',220);
INSERT INTO `states` VALUES (3614,'Niuas',220);
INSERT INTO `states` VALUES (3615,'Tongatapu',220);
INSERT INTO `states` VALUES (3616,'Vava\'u',220);
INSERT INTO `states` VALUES (3617,'Arima-Tunapuna-Piarco',221);
INSERT INTO `states` VALUES (3618,'Caroni',221);
INSERT INTO `states` VALUES (3619,'Chaguanas',221);
INSERT INTO `states` VALUES (3620,'Couva-Tabaquite-Talparo',221);
INSERT INTO `states` VALUES (3621,'Diego Martin',221);
INSERT INTO `states` VALUES (3622,'Glencoe',221);
INSERT INTO `states` VALUES (3623,'Penal Debe',221);
INSERT INTO `states` VALUES (3624,'Point Fortin',221);
INSERT INTO `states` VALUES (3625,'Port of Spain',221);
INSERT INTO `states` VALUES (3626,'Princes Town',221);
INSERT INTO `states` VALUES (3627,'Saint George',221);
INSERT INTO `states` VALUES (3628,'San Fernando',221);
INSERT INTO `states` VALUES (3629,'San Juan',221);
INSERT INTO `states` VALUES (3630,'Sangre Grande',221);
INSERT INTO `states` VALUES (3631,'Siparia',221);
INSERT INTO `states` VALUES (3632,'Tobago',221);
INSERT INTO `states` VALUES (3633,'Aryanah',222);
INSERT INTO `states` VALUES (3634,'Bajah',222);
INSERT INTO `states` VALUES (3635,'Bin \'Arus',222);
INSERT INTO `states` VALUES (3636,'Binzart',222);
INSERT INTO `states` VALUES (3637,'Gouvernorat de Ariana',222);
INSERT INTO `states` VALUES (3638,'Gouvernorat de Nabeul',222);
INSERT INTO `states` VALUES (3639,'Gouvernorat de Sousse',222);
INSERT INTO `states` VALUES (3640,'Hammamet Yasmine',222);
INSERT INTO `states` VALUES (3641,'Jundubah',222);
INSERT INTO `states` VALUES (3642,'Madaniyin',222);
INSERT INTO `states` VALUES (3643,'Manubah',222);
INSERT INTO `states` VALUES (3644,'Monastir',222);
INSERT INTO `states` VALUES (3645,'Nabul',222);
INSERT INTO `states` VALUES (3646,'Qabis',222);
INSERT INTO `states` VALUES (3647,'Qafsah',222);
INSERT INTO `states` VALUES (3648,'Qibili',222);
INSERT INTO `states` VALUES (3649,'Safaqis',222);
INSERT INTO `states` VALUES (3650,'Sfax',222);
INSERT INTO `states` VALUES (3651,'Sidi Bu Zayd',222);
INSERT INTO `states` VALUES (3652,'Silyanah',222);
INSERT INTO `states` VALUES (3653,'Susah',222);
INSERT INTO `states` VALUES (3654,'Tatawin',222);
INSERT INTO `states` VALUES (3655,'Tawzar',222);
INSERT INTO `states` VALUES (3656,'Tunis',222);
INSERT INTO `states` VALUES (3657,'Zaghwan',222);
INSERT INTO `states` VALUES (3658,'al-Kaf',222);
INSERT INTO `states` VALUES (3659,'al-Mahdiyah',222);
INSERT INTO `states` VALUES (3660,'al-Munastir',222);
INSERT INTO `states` VALUES (3661,'al-Qasrayn',222);
INSERT INTO `states` VALUES (3662,'al-Qayrawan',222);
INSERT INTO `states` VALUES (3663,'Adana',223);
INSERT INTO `states` VALUES (3664,'Adiyaman',223);
INSERT INTO `states` VALUES (3665,'Afyon',223);
INSERT INTO `states` VALUES (3666,'Agri',223);
INSERT INTO `states` VALUES (3667,'Aksaray',223);
INSERT INTO `states` VALUES (3668,'Amasya',223);
INSERT INTO `states` VALUES (3669,'Ankara',223);
INSERT INTO `states` VALUES (3670,'Antalya',223);
INSERT INTO `states` VALUES (3671,'Ardahan',223);
INSERT INTO `states` VALUES (3672,'Artvin',223);
INSERT INTO `states` VALUES (3673,'Aydin',223);
INSERT INTO `states` VALUES (3674,'Balikesir',223);
INSERT INTO `states` VALUES (3675,'Bartin',223);
INSERT INTO `states` VALUES (3676,'Batman',223);
INSERT INTO `states` VALUES (3677,'Bayburt',223);
INSERT INTO `states` VALUES (3678,'Bilecik',223);
INSERT INTO `states` VALUES (3679,'Bingol',223);
INSERT INTO `states` VALUES (3680,'Bitlis',223);
INSERT INTO `states` VALUES (3681,'Bolu',223);
INSERT INTO `states` VALUES (3682,'Burdur',223);
INSERT INTO `states` VALUES (3683,'Bursa',223);
INSERT INTO `states` VALUES (3684,'Canakkale',223);
INSERT INTO `states` VALUES (3685,'Cankiri',223);
INSERT INTO `states` VALUES (3686,'Corum',223);
INSERT INTO `states` VALUES (3687,'Denizli',223);
INSERT INTO `states` VALUES (3688,'Diyarbakir',223);
INSERT INTO `states` VALUES (3689,'Duzce',223);
INSERT INTO `states` VALUES (3690,'Edirne',223);
INSERT INTO `states` VALUES (3691,'Elazig',223);
INSERT INTO `states` VALUES (3692,'Erzincan',223);
INSERT INTO `states` VALUES (3693,'Erzurum',223);
INSERT INTO `states` VALUES (3694,'Eskisehir',223);
INSERT INTO `states` VALUES (3695,'Gaziantep',223);
INSERT INTO `states` VALUES (3696,'Giresun',223);
INSERT INTO `states` VALUES (3697,'Gumushane',223);
INSERT INTO `states` VALUES (3698,'Hakkari',223);
INSERT INTO `states` VALUES (3699,'Hatay',223);
INSERT INTO `states` VALUES (3700,'Icel',223);
INSERT INTO `states` VALUES (3701,'Igdir',223);
INSERT INTO `states` VALUES (3702,'Isparta',223);
INSERT INTO `states` VALUES (3703,'Istanbul',223);
INSERT INTO `states` VALUES (3704,'Izmir',223);
INSERT INTO `states` VALUES (3705,'Kahramanmaras',223);
INSERT INTO `states` VALUES (3706,'Karabuk',223);
INSERT INTO `states` VALUES (3707,'Karaman',223);
INSERT INTO `states` VALUES (3708,'Kars',223);
INSERT INTO `states` VALUES (3709,'Karsiyaka',223);
INSERT INTO `states` VALUES (3710,'Kastamonu',223);
INSERT INTO `states` VALUES (3711,'Kayseri',223);
INSERT INTO `states` VALUES (3712,'Kilis',223);
INSERT INTO `states` VALUES (3713,'Kirikkale',223);
INSERT INTO `states` VALUES (3714,'Kirklareli',223);
INSERT INTO `states` VALUES (3715,'Kirsehir',223);
INSERT INTO `states` VALUES (3716,'Kocaeli',223);
INSERT INTO `states` VALUES (3717,'Konya',223);
INSERT INTO `states` VALUES (3718,'Kutahya',223);
INSERT INTO `states` VALUES (3719,'Lefkosa',223);
INSERT INTO `states` VALUES (3720,'Malatya',223);
INSERT INTO `states` VALUES (3721,'Manisa',223);
INSERT INTO `states` VALUES (3722,'Mardin',223);
INSERT INTO `states` VALUES (3723,'Mugla',223);
INSERT INTO `states` VALUES (3724,'Mus',223);
INSERT INTO `states` VALUES (3725,'Nevsehir',223);
INSERT INTO `states` VALUES (3726,'Nigde',223);
INSERT INTO `states` VALUES (3727,'Ordu',223);
INSERT INTO `states` VALUES (3728,'Osmaniye',223);
INSERT INTO `states` VALUES (3729,'Rize',223);
INSERT INTO `states` VALUES (3730,'Sakarya',223);
INSERT INTO `states` VALUES (3731,'Samsun',223);
INSERT INTO `states` VALUES (3732,'Sanliurfa',223);
INSERT INTO `states` VALUES (3733,'Siirt',223);
INSERT INTO `states` VALUES (3734,'Sinop',223);
INSERT INTO `states` VALUES (3735,'Sirnak',223);
INSERT INTO `states` VALUES (3736,'Sivas',223);
INSERT INTO `states` VALUES (3737,'Tekirdag',223);
INSERT INTO `states` VALUES (3738,'Tokat',223);
INSERT INTO `states` VALUES (3739,'Trabzon',223);
INSERT INTO `states` VALUES (3740,'Tunceli',223);
INSERT INTO `states` VALUES (3741,'Usak',223);
INSERT INTO `states` VALUES (3742,'Van',223);
INSERT INTO `states` VALUES (3743,'Yalova',223);
INSERT INTO `states` VALUES (3744,'Yozgat',223);
INSERT INTO `states` VALUES (3745,'Zonguldak',223);
INSERT INTO `states` VALUES (3746,'Ahal',224);
INSERT INTO `states` VALUES (3747,'Asgabat',224);
INSERT INTO `states` VALUES (3748,'Balkan',224);
INSERT INTO `states` VALUES (3749,'Dasoguz',224);
INSERT INTO `states` VALUES (3750,'Lebap',224);
INSERT INTO `states` VALUES (3751,'Mari',224);
INSERT INTO `states` VALUES (3752,'Grand Turk',225);
INSERT INTO `states` VALUES (3753,'South Caicos and East Caicos',225);
INSERT INTO `states` VALUES (3754,'Funafuti',226);
INSERT INTO `states` VALUES (3755,'Nanumanga',226);
INSERT INTO `states` VALUES (3756,'Nanumea',226);
INSERT INTO `states` VALUES (3757,'Niutao',226);
INSERT INTO `states` VALUES (3758,'Nui',226);
INSERT INTO `states` VALUES (3759,'Nukufetau',226);
INSERT INTO `states` VALUES (3760,'Nukulaelae',226);
INSERT INTO `states` VALUES (3761,'Vaitupu',226);
INSERT INTO `states` VALUES (3762,'Central',227);
INSERT INTO `states` VALUES (3763,'Eastern',227);
INSERT INTO `states` VALUES (3764,'Northern',227);
INSERT INTO `states` VALUES (3765,'Western',227);
INSERT INTO `states` VALUES (3766,'Cherkas\'ka',228);
INSERT INTO `states` VALUES (3767,'Chernihivs\'ka',228);
INSERT INTO `states` VALUES (3768,'Chernivets\'ka',228);
INSERT INTO `states` VALUES (3769,'Crimea',228);
INSERT INTO `states` VALUES (3770,'Dnipropetrovska',228);
INSERT INTO `states` VALUES (3771,'Donets\'ka',228);
INSERT INTO `states` VALUES (3772,'Ivano-Frankivs\'ka',228);
INSERT INTO `states` VALUES (3773,'Kharkiv',228);
INSERT INTO `states` VALUES (3774,'Kharkov',228);
INSERT INTO `states` VALUES (3775,'Khersonska',228);
INSERT INTO `states` VALUES (3776,'Khmel\'nyts\'ka',228);
INSERT INTO `states` VALUES (3777,'Kirovohrad',228);
INSERT INTO `states` VALUES (3778,'Krym',228);
INSERT INTO `states` VALUES (3779,'Kyyiv',228);
INSERT INTO `states` VALUES (3780,'Kyyivs\'ka',228);
INSERT INTO `states` VALUES (3781,'L\'vivs\'ka',228);
INSERT INTO `states` VALUES (3782,'Luhans\'ka',228);
INSERT INTO `states` VALUES (3783,'Mykolayivs\'ka',228);
INSERT INTO `states` VALUES (3784,'Odes\'ka',228);
INSERT INTO `states` VALUES (3785,'Odessa',228);
INSERT INTO `states` VALUES (3786,'Poltavs\'ka',228);
INSERT INTO `states` VALUES (3787,'Rivnens\'ka',228);
INSERT INTO `states` VALUES (3788,'Sevastopol\'',228);
INSERT INTO `states` VALUES (3789,'Sums\'ka',228);
INSERT INTO `states` VALUES (3790,'Ternopil\'s\'ka',228);
INSERT INTO `states` VALUES (3791,'Volyns\'ka',228);
INSERT INTO `states` VALUES (3792,'Vynnyts\'ka',228);
INSERT INTO `states` VALUES (3793,'Zakarpats\'ka',228);
INSERT INTO `states` VALUES (3794,'Zaporizhia',228);
INSERT INTO `states` VALUES (3795,'Zhytomyrs\'ka',228);
INSERT INTO `states` VALUES (3796,'Abu Dhabi',229);
INSERT INTO `states` VALUES (3797,'Ajman',229);
INSERT INTO `states` VALUES (3798,'Dubai',229);
INSERT INTO `states` VALUES (3799,'Fujairah',229);
INSERT INTO `states` VALUES (3800,'Ras Al Khaimah',229);
INSERT INTO `states` VALUES (3801,'Sharjah',229);
INSERT INTO `states` VALUES (3802,'Umm Al Quwain',229);
INSERT INTO `states` VALUES (3805,'Aberdeen',230);
INSERT INTO `states` VALUES (3806,'Aberdeenshire',230);
INSERT INTO `states` VALUES (3807,'Argyll',230);
INSERT INTO `states` VALUES (3808,'Armagh',230);
INSERT INTO `states` VALUES (3809,'Bedfordshire',230);
INSERT INTO `states` VALUES (3810,'Belfast',230);
INSERT INTO `states` VALUES (3811,'Berkshire',230);
INSERT INTO `states` VALUES (3812,'Birmingham',230);
INSERT INTO `states` VALUES (3813,'Brechin',230);
INSERT INTO `states` VALUES (3814,'Bridgnorth',230);
INSERT INTO `states` VALUES (3815,'Bristol',230);
INSERT INTO `states` VALUES (3816,'Buckinghamshire',230);
INSERT INTO `states` VALUES (3817,'Cambridge',230);
INSERT INTO `states` VALUES (3818,'Cambridgeshire',230);
INSERT INTO `states` VALUES (3819,'Channel Islands',230);
INSERT INTO `states` VALUES (3820,'Cheshire',230);
INSERT INTO `states` VALUES (3821,'Cleveland',230);
INSERT INTO `states` VALUES (3822,'Co Fermanagh',230);
INSERT INTO `states` VALUES (3823,'Conwy',230);
INSERT INTO `states` VALUES (3824,'Cornwall',230);
INSERT INTO `states` VALUES (3825,'Coventry',230);
INSERT INTO `states` VALUES (3826,'Craven Arms',230);
INSERT INTO `states` VALUES (3827,'Cumbria',230);
INSERT INTO `states` VALUES (3828,'Denbighshire',230);
INSERT INTO `states` VALUES (3829,'Derby',230);
INSERT INTO `states` VALUES (3830,'Derbyshire',230);
INSERT INTO `states` VALUES (3831,'Devon',230);
INSERT INTO `states` VALUES (3832,'Dial Code Dungannon',230);
INSERT INTO `states` VALUES (3833,'Didcot',230);
INSERT INTO `states` VALUES (3834,'Dorset',230);
INSERT INTO `states` VALUES (3835,'Dunbartonshire',230);
INSERT INTO `states` VALUES (3836,'Durham',230);
INSERT INTO `states` VALUES (3837,'East Dunbartonshire',230);
INSERT INTO `states` VALUES (3838,'East Lothian',230);
INSERT INTO `states` VALUES (3839,'East Midlands',230);
INSERT INTO `states` VALUES (3840,'East Sussex',230);
INSERT INTO `states` VALUES (3841,'East Yorkshire',230);
INSERT INTO `states` VALUES (3842,'England',230);
INSERT INTO `states` VALUES (3843,'Essex',230);
INSERT INTO `states` VALUES (3844,'Fermanagh',230);
INSERT INTO `states` VALUES (3845,'Fife',230);
INSERT INTO `states` VALUES (3846,'Flintshire',230);
INSERT INTO `states` VALUES (3847,'Fulham',230);
INSERT INTO `states` VALUES (3848,'Gainsborough',230);
INSERT INTO `states` VALUES (3849,'Glocestershire',230);
INSERT INTO `states` VALUES (3850,'Gwent',230);
INSERT INTO `states` VALUES (3851,'Hampshire',230);
INSERT INTO `states` VALUES (3852,'Hants',230);
INSERT INTO `states` VALUES (3853,'Herefordshire',230);
INSERT INTO `states` VALUES (3854,'Hertfordshire',230);
INSERT INTO `states` VALUES (3855,'Ireland',230);
INSERT INTO `states` VALUES (3856,'Isle Of Man',230);
INSERT INTO `states` VALUES (3857,'Isle of Wight',230);
INSERT INTO `states` VALUES (3858,'Kenford',230);
INSERT INTO `states` VALUES (3859,'Kent',230);
INSERT INTO `states` VALUES (3860,'Kilmarnock',230);
INSERT INTO `states` VALUES (3861,'Lanarkshire',230);
INSERT INTO `states` VALUES (3862,'Lancashire',230);
INSERT INTO `states` VALUES (3863,'Leicestershire',230);
INSERT INTO `states` VALUES (3864,'Lincolnshire',230);
INSERT INTO `states` VALUES (3865,'Llanymynech',230);
INSERT INTO `states` VALUES (3866,'London',230);
INSERT INTO `states` VALUES (3867,'Ludlow',230);
INSERT INTO `states` VALUES (3868,'Manchester',230);
INSERT INTO `states` VALUES (3869,'Mayfair',230);
INSERT INTO `states` VALUES (3870,'Merseyside',230);
INSERT INTO `states` VALUES (3871,'Mid Glamorgan',230);
INSERT INTO `states` VALUES (3872,'Middlesex',230);
INSERT INTO `states` VALUES (3873,'Mildenhall',230);
INSERT INTO `states` VALUES (3874,'Monmouthshire',230);
INSERT INTO `states` VALUES (3875,'Newton Stewart',230);
INSERT INTO `states` VALUES (3876,'Norfolk',230);
INSERT INTO `states` VALUES (3877,'North Humberside',230);
INSERT INTO `states` VALUES (3878,'North Yorkshire',230);
INSERT INTO `states` VALUES (3879,'Northamptonshire',230);
INSERT INTO `states` VALUES (3880,'Northants',230);
INSERT INTO `states` VALUES (3881,'Northern Ireland',230);
INSERT INTO `states` VALUES (3882,'Northumberland',230);
INSERT INTO `states` VALUES (3883,'Nottinghamshire',230);
INSERT INTO `states` VALUES (3884,'Oxford',230);
INSERT INTO `states` VALUES (3885,'Powys',230);
INSERT INTO `states` VALUES (3886,'Roos-shire',230);
INSERT INTO `states` VALUES (3887,'SUSSEX',230);
INSERT INTO `states` VALUES (3888,'Sark',230);
INSERT INTO `states` VALUES (3889,'Scotland',230);
INSERT INTO `states` VALUES (3890,'Scottish Borders',230);
INSERT INTO `states` VALUES (3891,'Shropshire',230);
INSERT INTO `states` VALUES (3892,'Somerset',230);
INSERT INTO `states` VALUES (3893,'South Glamorgan',230);
INSERT INTO `states` VALUES (3894,'South Wales',230);
INSERT INTO `states` VALUES (3895,'South Yorkshire',230);
INSERT INTO `states` VALUES (3896,'Southwell',230);
INSERT INTO `states` VALUES (3897,'Staffordshire',230);
INSERT INTO `states` VALUES (3898,'Strabane',230);
INSERT INTO `states` VALUES (3899,'Suffolk',230);
INSERT INTO `states` VALUES (3900,'Surrey',230);
INSERT INTO `states` VALUES (3901,'Sussex',230);
INSERT INTO `states` VALUES (3902,'Twickenham',230);
INSERT INTO `states` VALUES (3903,'Tyne and Wear',230);
INSERT INTO `states` VALUES (3904,'Tyrone',230);
INSERT INTO `states` VALUES (3905,'Utah',230);
INSERT INTO `states` VALUES (3906,'Wales',230);
INSERT INTO `states` VALUES (3907,'Warwickshire',230);
INSERT INTO `states` VALUES (3908,'West Lothian',230);
INSERT INTO `states` VALUES (3909,'West Midlands',230);
INSERT INTO `states` VALUES (3910,'West Sussex',230);
INSERT INTO `states` VALUES (3911,'West Yorkshire',230);
INSERT INTO `states` VALUES (3912,'Whissendine',230);
INSERT INTO `states` VALUES (3913,'Wiltshire',230);
INSERT INTO `states` VALUES (3914,'Wokingham',230);
INSERT INTO `states` VALUES (3915,'Worcestershire',230);
INSERT INTO `states` VALUES (3916,'Wrexham',230);
INSERT INTO `states` VALUES (3917,'Wurttemberg',230);
INSERT INTO `states` VALUES (3918,'Yorkshire',230);
INSERT INTO `states` VALUES (3919,'Alabama',231);
INSERT INTO `states` VALUES (3920,'Alaska',231);
INSERT INTO `states` VALUES (3921,'Arizona',231);
INSERT INTO `states` VALUES (3922,'Arkansas',231);
INSERT INTO `states` VALUES (3923,'Byram',231);
INSERT INTO `states` VALUES (3924,'California',231);
INSERT INTO `states` VALUES (3925,'Cokato',231);
INSERT INTO `states` VALUES (3926,'Colorado',231);
INSERT INTO `states` VALUES (3927,'Connecticut',231);
INSERT INTO `states` VALUES (3928,'Delaware',231);
INSERT INTO `states` VALUES (3929,'District of Columbia',231);
INSERT INTO `states` VALUES (3930,'Florida',231);
INSERT INTO `states` VALUES (3931,'Georgia',231);
INSERT INTO `states` VALUES (3932,'Hawaii',231);
INSERT INTO `states` VALUES (3933,'Idaho',231);
INSERT INTO `states` VALUES (3934,'Illinois',231);
INSERT INTO `states` VALUES (3935,'Indiana',231);
INSERT INTO `states` VALUES (3936,'Iowa',231);
INSERT INTO `states` VALUES (3937,'Kansas',231);
INSERT INTO `states` VALUES (3938,'Kentucky',231);
INSERT INTO `states` VALUES (3939,'Louisiana',231);
INSERT INTO `states` VALUES (3940,'Lowa',231);
INSERT INTO `states` VALUES (3941,'Maine',231);
INSERT INTO `states` VALUES (3942,'Maryland',231);
INSERT INTO `states` VALUES (3943,'Massachusetts',231);
INSERT INTO `states` VALUES (3944,'Medfield',231);
INSERT INTO `states` VALUES (3945,'Michigan',231);
INSERT INTO `states` VALUES (3946,'Minnesota',231);
INSERT INTO `states` VALUES (3947,'Mississippi',231);
INSERT INTO `states` VALUES (3948,'Missouri',231);
INSERT INTO `states` VALUES (3949,'Montana',231);
INSERT INTO `states` VALUES (3950,'Nebraska',231);
INSERT INTO `states` VALUES (3951,'Nevada',231);
INSERT INTO `states` VALUES (3952,'New Hampshire',231);
INSERT INTO `states` VALUES (3953,'New Jersey',231);
INSERT INTO `states` VALUES (3954,'New Jersy',231);
INSERT INTO `states` VALUES (3955,'New Mexico',231);
INSERT INTO `states` VALUES (3956,'New York',231);
INSERT INTO `states` VALUES (3957,'North Carolina',231);
INSERT INTO `states` VALUES (3958,'North Dakota',231);
INSERT INTO `states` VALUES (3959,'Ohio',231);
INSERT INTO `states` VALUES (3960,'Oklahoma',231);
INSERT INTO `states` VALUES (3961,'Ontario',231);
INSERT INTO `states` VALUES (3962,'Oregon',231);
INSERT INTO `states` VALUES (3963,'Pennsylvania',231);
INSERT INTO `states` VALUES (3964,'Ramey',231);
INSERT INTO `states` VALUES (3965,'Rhode Island',231);
INSERT INTO `states` VALUES (3966,'South Carolina',231);
INSERT INTO `states` VALUES (3967,'South Dakota',231);
INSERT INTO `states` VALUES (3968,'Sublimity',231);
INSERT INTO `states` VALUES (3969,'Tennessee',231);
INSERT INTO `states` VALUES (3970,'Texas',231);
INSERT INTO `states` VALUES (3971,'Trimble',231);
INSERT INTO `states` VALUES (3972,'Utah',231);
INSERT INTO `states` VALUES (3973,'Vermont',231);
INSERT INTO `states` VALUES (3974,'Virginia',231);
INSERT INTO `states` VALUES (3975,'Washington',231);
INSERT INTO `states` VALUES (3976,'West Virginia',231);
INSERT INTO `states` VALUES (3977,'Wisconsin',231);
INSERT INTO `states` VALUES (3978,'Wyoming',231);
INSERT INTO `states` VALUES (3979,'United States Minor Outlying I',232);
INSERT INTO `states` VALUES (3980,'Artigas',233);
INSERT INTO `states` VALUES (3981,'Canelones',233);
INSERT INTO `states` VALUES (3982,'Cerro Largo',233);
INSERT INTO `states` VALUES (3983,'Colonia',233);
INSERT INTO `states` VALUES (3984,'Durazno',233);
INSERT INTO `states` VALUES (3985,'FLorida',233);
INSERT INTO `states` VALUES (3986,'Flores',233);
INSERT INTO `states` VALUES (3987,'Lavalleja',233);
INSERT INTO `states` VALUES (3988,'Maldonado',233);
INSERT INTO `states` VALUES (3989,'Montevideo',233);
INSERT INTO `states` VALUES (3990,'Paysandu',233);
INSERT INTO `states` VALUES (3991,'Rio Negro',233);
INSERT INTO `states` VALUES (3992,'Rivera',233);
INSERT INTO `states` VALUES (3993,'Rocha',233);
INSERT INTO `states` VALUES (3994,'Salto',233);
INSERT INTO `states` VALUES (3995,'San Jose',233);
INSERT INTO `states` VALUES (3996,'Soriano',233);
INSERT INTO `states` VALUES (3997,'Tacuarembo',233);
INSERT INTO `states` VALUES (3998,'Treinta y Tres',233);
INSERT INTO `states` VALUES (3999,'Andijon',234);
INSERT INTO `states` VALUES (4000,'Buhoro',234);
INSERT INTO `states` VALUES (4001,'Buxoro Viloyati',234);
INSERT INTO `states` VALUES (4002,'Cizah',234);
INSERT INTO `states` VALUES (4003,'Fargona',234);
INSERT INTO `states` VALUES (4004,'Horazm',234);
INSERT INTO `states` VALUES (4005,'Kaskadar',234);
INSERT INTO `states` VALUES (4006,'Korakalpogiston',234);
INSERT INTO `states` VALUES (4007,'Namangan',234);
INSERT INTO `states` VALUES (4008,'Navoi',234);
INSERT INTO `states` VALUES (4009,'Samarkand',234);
INSERT INTO `states` VALUES (4010,'Sirdare',234);
INSERT INTO `states` VALUES (4011,'Surhondar',234);
INSERT INTO `states` VALUES (4012,'Toskent',234);
INSERT INTO `states` VALUES (4013,'Malampa',235);
INSERT INTO `states` VALUES (4014,'Penama',235);
INSERT INTO `states` VALUES (4015,'Sanma',235);
INSERT INTO `states` VALUES (4016,'Shefa',235);
INSERT INTO `states` VALUES (4017,'Tafea',235);
INSERT INTO `states` VALUES (4018,'Torba',235);
INSERT INTO `states` VALUES (4019,'Vatican City State (Holy See)',236);
INSERT INTO `states` VALUES (4020,'Amazonas',237);
INSERT INTO `states` VALUES (4021,'Anzoategui',237);
INSERT INTO `states` VALUES (4022,'Apure',237);
INSERT INTO `states` VALUES (4023,'Aragua',237);
INSERT INTO `states` VALUES (4024,'Barinas',237);
INSERT INTO `states` VALUES (4025,'Bolivar',237);
INSERT INTO `states` VALUES (4026,'Carabobo',237);
INSERT INTO `states` VALUES (4027,'Cojedes',237);
INSERT INTO `states` VALUES (4028,'Delta Amacuro',237);
INSERT INTO `states` VALUES (4029,'Distrito Federal',237);
INSERT INTO `states` VALUES (4030,'Falcon',237);
INSERT INTO `states` VALUES (4031,'Guarico',237);
INSERT INTO `states` VALUES (4032,'Lara',237);
INSERT INTO `states` VALUES (4033,'Merida',237);
INSERT INTO `states` VALUES (4034,'Miranda',237);
INSERT INTO `states` VALUES (4035,'Monagas',237);
INSERT INTO `states` VALUES (4036,'Nueva Esparta',237);
INSERT INTO `states` VALUES (4037,'Portuguesa',237);
INSERT INTO `states` VALUES (4038,'Sucre',237);
INSERT INTO `states` VALUES (4039,'Tachira',237);
INSERT INTO `states` VALUES (4040,'Trujillo',237);
INSERT INTO `states` VALUES (4041,'Vargas',237);
INSERT INTO `states` VALUES (4042,'Yaracuy',237);
INSERT INTO `states` VALUES (4043,'Zulia',237);
INSERT INTO `states` VALUES (4044,'Bac Giang',238);
INSERT INTO `states` VALUES (4045,'Binh Dinh',238);
INSERT INTO `states` VALUES (4046,'Binh Duong',238);
INSERT INTO `states` VALUES (4047,'Da Nang',238);
INSERT INTO `states` VALUES (4048,'Dong Bang Song Cuu Long',238);
INSERT INTO `states` VALUES (4049,'Dong Bang Song Hong',238);
INSERT INTO `states` VALUES (4050,'Dong Nai',238);
INSERT INTO `states` VALUES (4051,'Dong Nam Bo',238);
INSERT INTO `states` VALUES (4052,'Duyen Hai Mien Trung',238);
INSERT INTO `states` VALUES (4053,'Hanoi',238);
INSERT INTO `states` VALUES (4054,'Hung Yen',238);
INSERT INTO `states` VALUES (4055,'Khu Bon Cu',238);
INSERT INTO `states` VALUES (4056,'Long An',238);
INSERT INTO `states` VALUES (4057,'Mien Nui Va Trung Du',238);
INSERT INTO `states` VALUES (4058,'Thai Nguyen',238);
INSERT INTO `states` VALUES (4059,'Thanh Pho Ho Chi Minh',238);
INSERT INTO `states` VALUES (4060,'Thu Do Ha Noi',238);
INSERT INTO `states` VALUES (4061,'Tinh Can Tho',238);
INSERT INTO `states` VALUES (4062,'Tinh Da Nang',238);
INSERT INTO `states` VALUES (4063,'Tinh Gia Lai',238);
INSERT INTO `states` VALUES (4064,'Anegada',239);
INSERT INTO `states` VALUES (4065,'Jost van Dyke',239);
INSERT INTO `states` VALUES (4066,'Tortola',239);
INSERT INTO `states` VALUES (4067,'Saint Croix',240);
INSERT INTO `states` VALUES (4068,'Saint John',240);
INSERT INTO `states` VALUES (4069,'Saint Thomas',240);
INSERT INTO `states` VALUES (4070,'Alo',241);
INSERT INTO `states` VALUES (4071,'Singave',241);
INSERT INTO `states` VALUES (4072,'Wallis',241);
INSERT INTO `states` VALUES (4073,'Bu Jaydur',242);
INSERT INTO `states` VALUES (4074,'Wad-adh-Dhahab',242);
INSERT INTO `states` VALUES (4075,'al-\'Ayun',242);
INSERT INTO `states` VALUES (4076,'as-Samarah',242);
INSERT INTO `states` VALUES (4077,'\'Adan',243);
INSERT INTO `states` VALUES (4078,'Abyan',243);
INSERT INTO `states` VALUES (4079,'Dhamar',243);
INSERT INTO `states` VALUES (4080,'Hadramaut',243);
INSERT INTO `states` VALUES (4081,'Hajjah',243);
INSERT INTO `states` VALUES (4082,'Hudaydah',243);
INSERT INTO `states` VALUES (4083,'Ibb',243);
INSERT INTO `states` VALUES (4084,'Lahij',243);
INSERT INTO `states` VALUES (4085,'Ma\'rib',243);
INSERT INTO `states` VALUES (4086,'Madinat San\'a',243);
INSERT INTO `states` VALUES (4087,'Sa\'dah',243);
INSERT INTO `states` VALUES (4088,'Sana',243);
INSERT INTO `states` VALUES (4089,'Shabwah',243);
INSERT INTO `states` VALUES (4090,'Ta\'izz',243);
INSERT INTO `states` VALUES (4091,'al-Bayda',243);
INSERT INTO `states` VALUES (4092,'al-Hudaydah',243);
INSERT INTO `states` VALUES (4093,'al-Jawf',243);
INSERT INTO `states` VALUES (4094,'al-Mahrah',243);
INSERT INTO `states` VALUES (4095,'al-Mahwit',243);
INSERT INTO `states` VALUES (4096,'Central Serbia',244);
INSERT INTO `states` VALUES (4097,'Kosovo and Metohija',244);
INSERT INTO `states` VALUES (4098,'Montenegro',244);
INSERT INTO `states` VALUES (4099,'Republic of Serbia',244);
INSERT INTO `states` VALUES (4100,'Serbia',244);
INSERT INTO `states` VALUES (4101,'Vojvodina',244);
INSERT INTO `states` VALUES (4102,'Central',245);
INSERT INTO `states` VALUES (4103,'Copperbelt',245);
INSERT INTO `states` VALUES (4104,'Eastern',245);
INSERT INTO `states` VALUES (4105,'Luapala',245);
INSERT INTO `states` VALUES (4106,'Lusaka',245);
INSERT INTO `states` VALUES (4107,'North-Western',245);
INSERT INTO `states` VALUES (4108,'Northern',245);
INSERT INTO `states` VALUES (4109,'Southern',245);
INSERT INTO `states` VALUES (4110,'Western',245);
INSERT INTO `states` VALUES (4111,'Bulawayo',246);
INSERT INTO `states` VALUES (4112,'Harare',246);
INSERT INTO `states` VALUES (4113,'Manicaland',246);
INSERT INTO `states` VALUES (4114,'Mashonaland Central',246);
INSERT INTO `states` VALUES (4115,'Mashonaland East',246);
INSERT INTO `states` VALUES (4116,'Mashonaland West',246);
INSERT INTO `states` VALUES (4117,'Masvingo',246);
INSERT INTO `states` VALUES (4118,'Matabeleland North',246);
INSERT INTO `states` VALUES (4119,'Matabeleland South',246);
INSERT INTO `states` VALUES (4120,'Midlands',246);
INSERT INTO `states` VALUES (4121,'Lienchiang County',214);
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `country_code` WRITE;
/*!40000 ALTER TABLE `country_code` DISABLE KEYS */;
INSERT INTO `country_code` VALUES (1,'AF','AFGHANISTAN','Afghanistan','AFG',4,93);
INSERT INTO `country_code` VALUES (2,'AL','ALBANIA','Albania','ALB',8,355);
INSERT INTO `country_code` VALUES (3,'DZ','ALGERIA','Algeria','DZA',12,213);
INSERT INTO `country_code` VALUES (4,'AS','AMERICAN SAMOA','American Samoa','ASM',16,1684);
INSERT INTO `country_code` VALUES (5,'AD','ANDORRA','Andorra','AND',20,376);
INSERT INTO `country_code` VALUES (6,'AO','ANGOLA','Angola','AGO',24,244);
INSERT INTO `country_code` VALUES (7,'AI','ANGUILLA','Anguilla','AIA',660,1264);
INSERT INTO `country_code` VALUES (8,'AQ','ANTARCTICA','Antarctica',NULL,NULL,0);
INSERT INTO `country_code` VALUES (9,'AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG',28,1268);
INSERT INTO `country_code` VALUES (10,'AR','ARGENTINA','Argentina','ARG',32,54);
INSERT INTO `country_code` VALUES (11,'AM','ARMENIA','Armenia','ARM',51,374);
INSERT INTO `country_code` VALUES (12,'AW','ARUBA','Aruba','ABW',533,297);
INSERT INTO `country_code` VALUES (13,'AU','AUSTRALIA','Australia','AUS',36,61);
INSERT INTO `country_code` VALUES (14,'AT','AUSTRIA','Austria','AUT',40,43);
INSERT INTO `country_code` VALUES (15,'AZ','AZERBAIJAN','Azerbaijan','AZE',31,994);
INSERT INTO `country_code` VALUES (16,'BS','BAHAMAS','Bahamas','BHS',44,1242);
INSERT INTO `country_code` VALUES (17,'BH','BAHRAIN','Bahrain','BHR',48,973);
INSERT INTO `country_code` VALUES (18,'BD','BANGLADESH','Bangladesh','BGD',50,880);
INSERT INTO `country_code` VALUES (19,'BB','BARBADOS','Barbados','BRB',52,1246);
INSERT INTO `country_code` VALUES (20,'BY','BELARUS','Belarus','BLR',112,375);
INSERT INTO `country_code` VALUES (21,'BE','BELGIUM','Belgium','BEL',56,32);
INSERT INTO `country_code` VALUES (22,'BZ','BELIZE','Belize','BLZ',84,501);
INSERT INTO `country_code` VALUES (23,'BJ','BENIN','Benin','BEN',204,229);
INSERT INTO `country_code` VALUES (24,'BM','BERMUDA','Bermuda','BMU',60,1441);
INSERT INTO `country_code` VALUES (25,'BT','BHUTAN','Bhutan','BTN',64,975);
INSERT INTO `country_code` VALUES (26,'BO','BOLIVIA','Bolivia','BOL',68,591);
INSERT INTO `country_code` VALUES (27,'BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH',70,387);
INSERT INTO `country_code` VALUES (28,'BW','BOTSWANA','Botswana','BWA',72,267);
INSERT INTO `country_code` VALUES (29,'BV','BOUVET ISLAND','Bouvet Island',NULL,NULL,0);
INSERT INTO `country_code` VALUES (30,'BR','BRAZIL','Brazil','BRA',76,55);
INSERT INTO `country_code` VALUES (31,'IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory',NULL,NULL,246);
INSERT INTO `country_code` VALUES (32,'BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN',96,673);
INSERT INTO `country_code` VALUES (33,'BG','BULGARIA','Bulgaria','BGR',100,359);
INSERT INTO `country_code` VALUES (34,'BF','BURKINA FASO','Burkina Faso','BFA',854,226);
INSERT INTO `country_code` VALUES (35,'BI','BURUNDI','Burundi','BDI',108,257);
INSERT INTO `country_code` VALUES (36,'KH','CAMBODIA','Cambodia','KHM',116,855);
INSERT INTO `country_code` VALUES (37,'CM','CAMEROON','Cameroon','CMR',120,237);
INSERT INTO `country_code` VALUES (38,'CA','CANADA','Canada','CAN',124,1);
INSERT INTO `country_code` VALUES (39,'CV','CAPE VERDE','Cape Verde','CPV',132,238);
INSERT INTO `country_code` VALUES (40,'KY','CAYMAN ISLANDS','Cayman Islands','CYM',136,1345);
INSERT INTO `country_code` VALUES (41,'CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF',140,236);
INSERT INTO `country_code` VALUES (42,'TD','CHAD','Chad','TCD',148,235);
INSERT INTO `country_code` VALUES (43,'CL','CHILE','Chile','CHL',152,56);
INSERT INTO `country_code` VALUES (44,'CN','CHINA','China','CHN',156,86);
INSERT INTO `country_code` VALUES (45,'CX','CHRISTMAS ISLAND','Christmas Island',NULL,NULL,61);
INSERT INTO `country_code` VALUES (46,'CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands',NULL,NULL,672);
INSERT INTO `country_code` VALUES (47,'CO','COLOMBIA','Colombia','COL',170,57);
INSERT INTO `country_code` VALUES (48,'KM','COMOROS','Comoros','COM',174,269);
INSERT INTO `country_code` VALUES (49,'CG','CONGO','Congo','COG',178,242);
INSERT INTO `country_code` VALUES (50,'CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD',180,242);
INSERT INTO `country_code` VALUES (51,'CK','COOK ISLANDS','Cook Islands','COK',184,682);
INSERT INTO `country_code` VALUES (52,'CR','COSTA RICA','Costa Rica','CRI',188,506);
INSERT INTO `country_code` VALUES (53,'CI','COTE D\'IVOIRE','Cote D\'Ivoire','CIV',384,225);
INSERT INTO `country_code` VALUES (54,'HR','CROATIA','Croatia','HRV',191,385);
INSERT INTO `country_code` VALUES (55,'CU','CUBA','Cuba','CUB',192,53);
INSERT INTO `country_code` VALUES (56,'CY','CYPRUS','Cyprus','CYP',196,357);
INSERT INTO `country_code` VALUES (57,'CZ','CZECH REPUBLIC','Czech Republic','CZE',203,420);
INSERT INTO `country_code` VALUES (58,'DK','DENMARK','Denmark','DNK',208,45);
INSERT INTO `country_code` VALUES (59,'DJ','DJIBOUTI','Djibouti','DJI',262,253);
INSERT INTO `country_code` VALUES (60,'DM','DOMINICA','Dominica','DMA',212,1767);
INSERT INTO `country_code` VALUES (61,'DO','DOMINICAN REPUBLIC','Dominican Republic','DOM',214,1809);
INSERT INTO `country_code` VALUES (62,'EC','ECUADOR','Ecuador','ECU',218,593);
INSERT INTO `country_code` VALUES (63,'EG','EGYPT','Egypt','EGY',818,20);
INSERT INTO `country_code` VALUES (64,'SV','EL SALVADOR','El Salvador','SLV',222,503);
INSERT INTO `country_code` VALUES (65,'GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ',226,240);
INSERT INTO `country_code` VALUES (66,'ER','ERITREA','Eritrea','ERI',232,291);
INSERT INTO `country_code` VALUES (67,'EE','ESTONIA','Estonia','EST',233,372);
INSERT INTO `country_code` VALUES (68,'ET','ETHIOPIA','Ethiopia','ETH',231,251);
INSERT INTO `country_code` VALUES (69,'FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK',238,500);
INSERT INTO `country_code` VALUES (70,'FO','FAROE ISLANDS','Faroe Islands','FRO',234,298);
INSERT INTO `country_code` VALUES (71,'FJ','FIJI','Fiji','FJI',242,679);
INSERT INTO `country_code` VALUES (72,'FI','FINLAND','Finland','FIN',246,358);
INSERT INTO `country_code` VALUES (73,'FR','FRANCE','France','FRA',250,33);
INSERT INTO `country_code` VALUES (74,'GF','FRENCH GUIANA','French Guiana','GUF',254,594);
INSERT INTO `country_code` VALUES (75,'PF','FRENCH POLYNESIA','French Polynesia','PYF',258,689);
INSERT INTO `country_code` VALUES (76,'TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories',NULL,NULL,0);
INSERT INTO `country_code` VALUES (77,'GA','GABON','Gabon','GAB',266,241);
INSERT INTO `country_code` VALUES (78,'GM','GAMBIA','Gambia','GMB',270,220);
INSERT INTO `country_code` VALUES (79,'GE','GEORGIA','Georgia','GEO',268,995);
INSERT INTO `country_code` VALUES (80,'DE','GERMANY','Germany','DEU',276,49);
INSERT INTO `country_code` VALUES (81,'GH','GHANA','Ghana','GHA',288,233);
INSERT INTO `country_code` VALUES (82,'GI','GIBRALTAR','Gibraltar','GIB',292,350);
INSERT INTO `country_code` VALUES (83,'GR','GREECE','Greece','GRC',300,30);
INSERT INTO `country_code` VALUES (84,'GL','GREENLAND','Greenland','GRL',304,299);
INSERT INTO `country_code` VALUES (85,'GD','GRENADA','Grenada','GRD',308,1473);
INSERT INTO `country_code` VALUES (86,'GP','GUADELOUPE','Guadeloupe','GLP',312,590);
INSERT INTO `country_code` VALUES (87,'GU','GUAM','Guam','GUM',316,1671);
INSERT INTO `country_code` VALUES (88,'GT','GUATEMALA','Guatemala','GTM',320,502);
INSERT INTO `country_code` VALUES (89,'GN','GUINEA','Guinea','GIN',324,224);
INSERT INTO `country_code` VALUES (90,'GW','GUINEA-BISSAU','Guinea-Bissau','GNB',624,245);
INSERT INTO `country_code` VALUES (91,'GY','GUYANA','Guyana','GUY',328,592);
INSERT INTO `country_code` VALUES (92,'HT','HAITI','Haiti','HTI',332,509);
INSERT INTO `country_code` VALUES (93,'HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands',NULL,NULL,0);
INSERT INTO `country_code` VALUES (94,'VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT',336,39);
INSERT INTO `country_code` VALUES (95,'HN','HONDURAS','Honduras','HND',340,504);
INSERT INTO `country_code` VALUES (96,'HK','HONG KONG','Hong Kong','HKG',344,852);
INSERT INTO `country_code` VALUES (97,'HU','HUNGARY','Hungary','HUN',348,36);
INSERT INTO `country_code` VALUES (98,'IS','ICELAND','Iceland','ISL',352,354);
INSERT INTO `country_code` VALUES (99,'IN','INDIA','India','IND',356,91);
INSERT INTO `country_code` VALUES (100,'ID','INDONESIA','Indonesia','IDN',360,62);
INSERT INTO `country_code` VALUES (101,'IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN',364,98);
INSERT INTO `country_code` VALUES (102,'IQ','IRAQ','Iraq','IRQ',368,964);
INSERT INTO `country_code` VALUES (103,'IE','IRELAND','Ireland','IRL',372,353);
INSERT INTO `country_code` VALUES (104,'IL','ISRAEL','Israel','ISR',376,972);
INSERT INTO `country_code` VALUES (105,'IT','ITALY','Italy','ITA',380,39);
INSERT INTO `country_code` VALUES (106,'JM','JAMAICA','Jamaica','JAM',388,1876);
INSERT INTO `country_code` VALUES (107,'JP','JAPAN','Japan','JPN',392,81);
INSERT INTO `country_code` VALUES (108,'JO','JORDAN','Jordan','JOR',400,962);
INSERT INTO `country_code` VALUES (109,'KZ','KAZAKHSTAN','Kazakhstan','KAZ',398,7);
INSERT INTO `country_code` VALUES (110,'KE','KENYA','Kenya','KEN',404,254);
INSERT INTO `country_code` VALUES (111,'KI','KIRIBATI','Kiribati','KIR',296,686);
INSERT INTO `country_code` VALUES (112,'KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','Korea, Democratic People\'s Republic of','PRK',408,850);
INSERT INTO `country_code` VALUES (113,'KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR',410,82);
INSERT INTO `country_code` VALUES (114,'KW','KUWAIT','Kuwait','KWT',414,965);
INSERT INTO `country_code` VALUES (115,'KG','KYRGYZSTAN','Kyrgyzstan','KGZ',417,996);
INSERT INTO `country_code` VALUES (116,'LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','Lao People\'s Democratic Republic','LAO',418,856);
INSERT INTO `country_code` VALUES (117,'LV','LATVIA','Latvia','LVA',428,371);
INSERT INTO `country_code` VALUES (118,'LB','LEBANON','Lebanon','LBN',422,961);
INSERT INTO `country_code` VALUES (119,'LS','LESOTHO','Lesotho','LSO',426,266);
INSERT INTO `country_code` VALUES (120,'LR','LIBERIA','Liberia','LBR',430,231);
INSERT INTO `country_code` VALUES (121,'LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY',434,218);
INSERT INTO `country_code` VALUES (122,'LI','LIECHTENSTEIN','Liechtenstein','LIE',438,423);
INSERT INTO `country_code` VALUES (123,'LT','LITHUANIA','Lithuania','LTU',440,370);
INSERT INTO `country_code` VALUES (124,'LU','LUXEMBOURG','Luxembourg','LUX',442,352);
INSERT INTO `country_code` VALUES (125,'MO','MACAO','Macao','MAC',446,853);
INSERT INTO `country_code` VALUES (126,'MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD',807,389);
INSERT INTO `country_code` VALUES (127,'MG','MADAGASCAR','Madagascar','MDG',450,261);
INSERT INTO `country_code` VALUES (128,'MW','MALAWI','Malawi','MWI',454,265);
INSERT INTO `country_code` VALUES (129,'MY','MALAYSIA','Malaysia','MYS',458,60);
INSERT INTO `country_code` VALUES (130,'MV','MALDIVES','Maldives','MDV',462,960);
INSERT INTO `country_code` VALUES (131,'ML','MALI','Mali','MLI',466,223);
INSERT INTO `country_code` VALUES (132,'MT','MALTA','Malta','MLT',470,356);
INSERT INTO `country_code` VALUES (133,'MH','MARSHALL ISLANDS','Marshall Islands','MHL',584,692);
INSERT INTO `country_code` VALUES (134,'MQ','MARTINIQUE','Martinique','MTQ',474,596);
INSERT INTO `country_code` VALUES (135,'MR','MAURITANIA','Mauritania','MRT',478,222);
INSERT INTO `country_code` VALUES (136,'MU','MAURITIUS','Mauritius','MUS',480,230);
INSERT INTO `country_code` VALUES (137,'YT','MAYOTTE','Mayotte',NULL,NULL,269);
INSERT INTO `country_code` VALUES (138,'MX','MEXICO','Mexico','MEX',484,52);
INSERT INTO `country_code` VALUES (139,'FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM',583,691);
INSERT INTO `country_code` VALUES (140,'MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA',498,373);
INSERT INTO `country_code` VALUES (141,'MC','MONACO','Monaco','MCO',492,377);
INSERT INTO `country_code` VALUES (142,'MN','MONGOLIA','Mongolia','MNG',496,976);
INSERT INTO `country_code` VALUES (143,'MS','MONTSERRAT','Montserrat','MSR',500,1664);
INSERT INTO `country_code` VALUES (144,'MA','MOROCCO','Morocco','MAR',504,212);
INSERT INTO `country_code` VALUES (145,'MZ','MOZAMBIQUE','Mozambique','MOZ',508,258);
INSERT INTO `country_code` VALUES (146,'MM','MYANMAR','Myanmar','MMR',104,95);
INSERT INTO `country_code` VALUES (147,'NA','NAMIBIA','Namibia','NAM',516,264);
INSERT INTO `country_code` VALUES (148,'NR','NAURU','Nauru','NRU',520,674);
INSERT INTO `country_code` VALUES (149,'NP','NEPAL','Nepal','NPL',524,977);
INSERT INTO `country_code` VALUES (150,'NL','NETHERLANDS','Netherlands','NLD',528,31);
INSERT INTO `country_code` VALUES (151,'AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT',530,599);
INSERT INTO `country_code` VALUES (152,'NC','NEW CALEDONIA','New Caledonia','NCL',540,687);
INSERT INTO `country_code` VALUES (153,'NZ','NEW ZEALAND','New Zealand','NZL',554,64);
INSERT INTO `country_code` VALUES (154,'NI','NICARAGUA','Nicaragua','NIC',558,505);
INSERT INTO `country_code` VALUES (155,'NE','NIGER','Niger','NER',562,227);
INSERT INTO `country_code` VALUES (156,'NG','NIGERIA','Nigeria','NGA',566,234);
INSERT INTO `country_code` VALUES (157,'NU','NIUE','Niue','NIU',570,683);
INSERT INTO `country_code` VALUES (158,'NF','NORFOLK ISLAND','Norfolk Island','NFK',574,672);
INSERT INTO `country_code` VALUES (159,'MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP',580,1670);
INSERT INTO `country_code` VALUES (160,'NO','NORWAY','Norway','NOR',578,47);
INSERT INTO `country_code` VALUES (161,'OM','OMAN','Oman','OMN',512,968);
INSERT INTO `country_code` VALUES (162,'PK','PAKISTAN','Pakistan','PAK',586,92);
INSERT INTO `country_code` VALUES (163,'PW','PALAU','Palau','PLW',585,680);
INSERT INTO `country_code` VALUES (164,'PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied',NULL,NULL,970);
INSERT INTO `country_code` VALUES (165,'PA','PANAMA','Panama','PAN',591,507);
INSERT INTO `country_code` VALUES (166,'PG','PAPUA NEW GUINEA','Papua New Guinea','PNG',598,675);
INSERT INTO `country_code` VALUES (167,'PY','PARAGUAY','Paraguay','PRY',600,595);
INSERT INTO `country_code` VALUES (168,'PE','PERU','Peru','PER',604,51);
INSERT INTO `country_code` VALUES (169,'PH','PHILIPPINES','Philippines','PHL',608,63);
INSERT INTO `country_code` VALUES (170,'PN','PITCAIRN','Pitcairn','PCN',612,0);
INSERT INTO `country_code` VALUES (171,'PL','POLAND','Poland','POL',616,48);
INSERT INTO `country_code` VALUES (172,'PT','PORTUGAL','Portugal','PRT',620,351);
INSERT INTO `country_code` VALUES (173,'PR','PUERTO RICO','Puerto Rico','PRI',630,1787);
INSERT INTO `country_code` VALUES (174,'QA','QATAR','Qatar','QAT',634,974);
INSERT INTO `country_code` VALUES (175,'RE','REUNION','Reunion','REU',638,262);
INSERT INTO `country_code` VALUES (176,'RO','ROMANIA','Romania','ROM',642,40);
INSERT INTO `country_code` VALUES (177,'RU','RUSSIAN FEDERATION','Russian Federation','RUS',643,70);
INSERT INTO `country_code` VALUES (178,'RW','RWANDA','Rwanda','RWA',646,250);
INSERT INTO `country_code` VALUES (179,'SH','SAINT HELENA','Saint Helena','SHN',654,290);
INSERT INTO `country_code` VALUES (180,'KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA',659,1869);
INSERT INTO `country_code` VALUES (181,'LC','SAINT LUCIA','Saint Lucia','LCA',662,1758);
INSERT INTO `country_code` VALUES (182,'PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM',666,508);
INSERT INTO `country_code` VALUES (183,'VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT',670,1784);
INSERT INTO `country_code` VALUES (184,'WS','SAMOA','Samoa','WSM',882,684);
INSERT INTO `country_code` VALUES (185,'SM','SAN MARINO','San Marino','SMR',674,378);
INSERT INTO `country_code` VALUES (186,'ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP',678,239);
INSERT INTO `country_code` VALUES (187,'SA','SAUDI ARABIA','Saudi Arabia','SAU',682,966);
INSERT INTO `country_code` VALUES (188,'SN','SENEGAL','Senegal','SEN',686,221);
INSERT INTO `country_code` VALUES (189,'CS','SERBIA AND MONTENEGRO','Serbia and Montenegro',NULL,NULL,381);
INSERT INTO `country_code` VALUES (190,'SC','SEYCHELLES','Seychelles','SYC',690,248);
INSERT INTO `country_code` VALUES (191,'SL','SIERRA LEONE','Sierra Leone','SLE',694,232);
INSERT INTO `country_code` VALUES (192,'SG','SINGAPORE','Singapore','SGP',702,65);
INSERT INTO `country_code` VALUES (193,'SK','SLOVAKIA','Slovakia','SVK',703,421);
INSERT INTO `country_code` VALUES (194,'SI','SLOVENIA','Slovenia','SVN',705,386);
INSERT INTO `country_code` VALUES (195,'SB','SOLOMON ISLANDS','Solomon Islands','SLB',90,677);
INSERT INTO `country_code` VALUES (196,'SO','SOMALIA','Somalia','SOM',706,252);
INSERT INTO `country_code` VALUES (197,'ZA','SOUTH AFRICA','South Africa','ZAF',710,27);
INSERT INTO `country_code` VALUES (198,'GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands',NULL,NULL,0);
INSERT INTO `country_code` VALUES (199,'ES','SPAIN','Spain','ESP',724,34);
INSERT INTO `country_code` VALUES (200,'LK','SRI LANKA','Sri Lanka','LKA',144,94);
INSERT INTO `country_code` VALUES (201,'SD','SUDAN','Sudan','SDN',736,249);
INSERT INTO `country_code` VALUES (202,'SR','SURINAME','Suriname','SUR',740,597);
INSERT INTO `country_code` VALUES (203,'SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM',744,47);
INSERT INTO `country_code` VALUES (204,'SZ','SWAZILAND','Swaziland','SWZ',748,268);
INSERT INTO `country_code` VALUES (205,'SE','SWEDEN','Sweden','SWE',752,46);
INSERT INTO `country_code` VALUES (206,'CH','SWITZERLAND','Switzerland','CHE',756,41);
INSERT INTO `country_code` VALUES (207,'SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR',760,963);
INSERT INTO `country_code` VALUES (208,'TW','TAIWAN, PROVINCE OF CHINA','Taiwan, Province of China','TWN',158,886);
INSERT INTO `country_code` VALUES (209,'TJ','TAJIKISTAN','Tajikistan','TJK',762,992);
INSERT INTO `country_code` VALUES (210,'TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA',834,255);
INSERT INTO `country_code` VALUES (211,'TH','THAILAND','Thailand','THA',764,66);
INSERT INTO `country_code` VALUES (212,'TL','TIMOR-LESTE','Timor-Leste',NULL,NULL,670);
INSERT INTO `country_code` VALUES (213,'TG','TOGO','Togo','TGO',768,228);
INSERT INTO `country_code` VALUES (214,'TK','TOKELAU','Tokelau','TKL',772,690);
INSERT INTO `country_code` VALUES (215,'TO','TONGA','Tonga','TON',776,676);
INSERT INTO `country_code` VALUES (216,'TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO',780,1868);
INSERT INTO `country_code` VALUES (217,'TN','TUNISIA','Tunisia','TUN',788,216);
INSERT INTO `country_code` VALUES (218,'TR','TURKEY','Turkey','TUR',792,90);
INSERT INTO `country_code` VALUES (219,'TM','TURKMENISTAN','Turkmenistan','TKM',795,7370);
INSERT INTO `country_code` VALUES (220,'TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA',796,1649);
INSERT INTO `country_code` VALUES (221,'TV','TUVALU','Tuvalu','TUV',798,688);
INSERT INTO `country_code` VALUES (222,'UG','UGANDA','Uganda','UGA',800,256);
INSERT INTO `country_code` VALUES (223,'UA','UKRAINE','Ukraine','UKR',804,380);
INSERT INTO `country_code` VALUES (224,'AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE',784,971);
INSERT INTO `country_code` VALUES (225,'GB','UNITED KINGDOM','United Kingdom','GBR',826,44);
INSERT INTO `country_code` VALUES (226,'US','UNITED STATES','United States','USA',840,1);
INSERT INTO `country_code` VALUES (227,'UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands',NULL,NULL,1);
INSERT INTO `country_code` VALUES (228,'UY','URUGUAY','Uruguay','URY',858,598);
INSERT INTO `country_code` VALUES (229,'UZ','UZBEKISTAN','Uzbekistan','UZB',860,998);
INSERT INTO `country_code` VALUES (230,'VU','VANUATU','Vanuatu','VUT',548,678);
INSERT INTO `country_code` VALUES (231,'VE','VENEZUELA','Venezuela','VEN',862,58);
INSERT INTO `country_code` VALUES (232,'VN','VIET NAM','Viet Nam','VNM',704,84);
INSERT INTO `country_code` VALUES (233,'VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB',92,1284);
INSERT INTO `country_code` VALUES (234,'VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR',850,1340);
INSERT INTO `country_code` VALUES (235,'WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF',876,681);
INSERT INTO `country_code` VALUES (236,'EH','WESTERN SAHARA','Western Sahara','ESH',732,212);
INSERT INTO `country_code` VALUES (237,'YE','YEMEN','Yemen','YEM',887,967);
INSERT INTO `country_code` VALUES (238,'ZM','ZAMBIA','Zambia','ZMB',894,260);
INSERT INTO `country_code` VALUES (239,'ZW','ZIMBABWE','Zimbabwe','ZWE',716,263);
/*!40000 ALTER TABLE `country_code` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `ci_roles` WRITE;
/*!40000 ALTER TABLE `ci_roles` DISABLE KEYS */;
INSERT INTO `ci_roles` VALUES (1,'Admin','admin',1,0,1,1,'2020-05-18 00:00:00',0,'{\"dashboard\":\"write\",\"calls\":\"write\",\"hr\":\"write\",\"hr_targets\":\"write\",\"hr_attendance\":\"write\",\"hr_shifts\":\"write\",\"hr_leaves\":\"write\",\"hr_payroll\":\"write\",\"hr_appraisals\":\"write\",\"hr_lifecycle\":\"write\",\"database\":\"write\",\"leads\":\"write\",\"orders\":\"write\",\"sales\":\"write\",\"followups\":\"write\",\"meetings\":\"write\",\"verticals\":\"write\",\"vertical_followups\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"database_transfer\":\"write\",\"team_members\":\"write\",\"team_chart\":\"write\",\"team_database\":\"write\",\"team_leads\":\"write\",\"team_orders\":\"write\",\"team_verticals\":\"write\",\"team_followups\":\"write\",\"team_meetings\":\"write\",\"team_assignmeetings\":\"write\",\"team_assignleads\":\"write\",\"team_tasks\":\"write\",\"team_tickets\":\"write\",\"lead_review\":\"no_access\",\"orders_pending\":\"no_access\",\"kam_onboard\":\"no_access\",\"orders_management\":\"no_access\",\"kam_schedule\":\"write\",\"kam_task_followup\":\"write\",\"kam_reminders\":\"write\",\"users\":\"write\",\"roles\":\"write\",\"terms\":\"write\",\"leads_import\":\"write\",\"leads_export\":\"write\",\"assigned_leads\":\"write\",\"admin_logs\":\"write\",\"system_assignee\":\"write\",\"kam_clients\":\"write\",\"calling_api\":\"write\",\"integrations\":\"write\",\"reports_revenue\":\"write\",\"reports_projections\":\"write\",\"reports_download\":\"write\",\"my_zone\":\"write\",\"team\":\"write\",\"back_office\":\"no_access\",\"kam\":\"write\",\"reports\":\"write\",\"system\":\"write\"}');
INSERT INTO `ci_roles` VALUES (2,'Back Office','department',2,1,2,1,'2020-05-20 00:00:00',0,'{\"dash6_company\":\"no_access\",\"dashboard\":\"read\",\"dash6_backoffice\":\"read\",\"dash6_kam\":\"no_access\",\"calls\":\"write\",\"live\":\"no_access\",\"help_support\":\"read\",\"self_training\":\"read\",\"events_view\":\"read\",\"data_creation\":\"no_access\",\"all_data\":\"no_access\",\"support_desk\":\"no_access\",\"start_live\":\"no_access\",\"database\":\"write\",\"leads\":\"write\",\"orders\":\"write\",\"sales\":\"write\",\"followups\":\"write\",\"meetings\":\"write\",\"verticals\":\"write\",\"vertical_followups\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"task_followups\":\"write\",\"tickets\":\"write\",\"database_transfer\":\"write\",\"campaigns\":\"write\",\"ai_agents\":\"no_access\",\"team_database\":\"write\",\"team_leads\":\"write\",\"team_orders\":\"write\",\"team_followups\":\"write\",\"team_meetings\":\"write\",\"team_assignmeetings\":\"write\",\"team_assignleads\":\"write\",\"team_verticals\":\"write\",\"team_members\":\"write\",\"team_chart\":\"write\",\"team_tasks\":\"write\",\"team_tickets\":\"write\",\"lead_review\":\"write\",\"orders_pending\":\"write\",\"kam_onboard\":\"write\",\"orders_open\":\"write\",\"orders_followups\":\"write\",\"orders_completed\":\"write\",\"orders_closed\":\"write\",\"orders_rejected\":\"write\",\"orders_management\":\"write\",\"orders_invoices\":\"write\",\"orders_payments\":\"write\",\"kam_clients\":\"no_access\",\"kam_schedule\":\"no_access\",\"kam_sales_approval\":\"no_access\",\"kam_task_followup\":\"no_access\",\"kam_reminders\":\"no_access\",\"kam_report\":\"no_access\",\"hr\":\"no_access\",\"hr_staff\":\"no_access\",\"hr_employees\":\"no_access\",\"hr_salary\":\"no_access\",\"hr_settings\":\"no_access\",\"hr_attendance\":\"no_access\",\"hr_shifts\":\"no_access\",\"hr_leaves\":\"no_access\",\"hr_training\":\"no_access\",\"hr_topics\":\"no_access\",\"hr_targets\":\"no_access\",\"hr_lifecycle\":\"no_access\",\"hr_payroll\":\"no_access\",\"hr_vertical_payouts\":\"no_access\",\"hr_payroll_approve\":\"no_access\",\"hr_taxation\":\"no_access\",\"hr_appraisals\":\"no_access\",\"hr_letters\":\"no_access\",\"hr_documents\":\"no_access\",\"events_list\":\"no_access\",\"reports_revenue\":\"no_access\",\"reports_projections\":\"no_access\",\"reports_download\":\"no_access\",\"rep_company\":\"no_access\",\"reports_analysis\":\"no_access\",\"rep_backoffice\":\"no_access\",\"rep_kam\":\"no_access\",\"reports_insights\":\"no_access\",\"reports_leads\":\"no_access\",\"reports_followups\":\"no_access\",\"reports_users\":\"no_access\",\"reports_perf_dep\":\"no_access\",\"reports_perf_ind\":\"no_access\",\"users\":\"no_access\",\"roles\":\"no_access\",\"terms\":\"no_access\",\"leads_import\":\"no_access\",\"leads_export\":\"no_access\",\"assigned_leads\":\"no_access\",\"admin_logs\":\"no_access\",\"live_location\":\"no_access\",\"system_assignee\":\"no_access\",\"system_kam_clients\":\"no_access\",\"calling_api\":\"no_access\",\"integrations\":\"no_access\",\"marketing\":\"no_access\",\"qc_panel\":\"no_access\",\"my_zone\":\"write\",\"team\":\"write\",\"back_office\":\"write\",\"kam\":\"no_access\",\"reports\":\"no_access\",\"system\":\"no_access\",\"tasks\":\"write\",\"events\":\"read\"}');
INSERT INTO `ci_roles` VALUES (3,'Sales','department',2,1,3,1,'2020-05-20 00:00:00',1,'{\"dashboard\":\"write\",\"calls\":\"write\",\"calls_autodialer\":\"write\",\"calls_manualdialer\":\"write\",\"calls_logs\":\"write\",\"live\":\"read\",\"live_presentation\":\"read\",\"database\":\"write\",\"leads\":\"write\",\"orders\":\"write\",\"sales\":\"write\",\"followups\":\"write\",\"meetings\":\"write\",\"verticals\":\"write\",\"vertical_followups\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"database_transfer\":\"write\",\"team_members\":\"write\",\"team_chart\":\"write\",\"team_database\":\"write\",\"team_leads\":\"write\",\"team_orders\":\"write\",\"team_verticals\":\"write\",\"team_followups\":\"write\",\"team_meetings\":\"write\",\"team_assignmeetings\":\"write\",\"team_assignleads\":\"write\",\"team_tasks\":\"write\",\"team_tickets\":\"write\",\"lead_review\":\"no_access\",\"orders_pending\":\"no_access\",\"orders_pending_sales\":\"no_access\",\"kam_onboard\":\"no_access\",\"orders_open\":\"no_access\",\"orders_followups\":\"no_access\",\"orders_completed\":\"no_access\",\"orders_closed\":\"no_access\",\"orders_rejected\":\"no_access\",\"orders_management\":\"no_access\",\"kam_clients\":\"no_access\",\"kam_schedule\":\"no_access\",\"kam_task_followup\":\"no_access\",\"kam_reminders\":\"no_access\",\"kam_report\":\"no_access\",\"kam_assignee_mgmt\":\"no_access\",\"hr\":\"no_access\",\"hr_targets\":\"no_access\",\"hr_attendance\":\"no_access\",\"hr_shifts\":\"no_access\",\"hr_leaves\":\"no_access\",\"hr_payroll\":\"no_access\",\"hr_taxation\":\"no_access\",\"hr_appraisals\":\"no_access\",\"hr_lifecycle\":\"no_access\",\"hr_documents\":\"no_access\",\"reports_revenue\":\"write\",\"reports_projections\":\"write\",\"reports_download\":\"write\",\"reports_admin\":\"write\",\"reports_leads\":\"write\",\"reports_perf_dep\":\"write\",\"reports_perf_ind\":\"write\",\"events_list\":\"read\",\"awards_list\":\"read\",\"users\":\"no_access\",\"roles\":\"no_access\",\"terms\":\"no_access\",\"leads_import\":\"no_access\",\"leads_export\":\"no_access\",\"assigned_leads\":\"no_access\",\"admin_logs\":\"no_access\",\"system_assignee\":\"no_access\",\"calling_api\":\"no_access\",\"integrations\":\"no_access\",\"live_location\":\"no_access\",\"qc_panel\":\"no_access\",\"my_zone\":\"write\",\"team\":\"write\",\"back_office\":\"no_access\",\"kam\":\"no_access\",\"reports\":\"write\",\"events\":\"read\",\"system\":\"no_access\",\"hr_staff\":\"no_access\",\"hr_employees\":\"no_access\",\"hr_salary\":\"no_access\",\"hr_settings\":\"no_access\",\"hr_vertical_payouts\":\"no_access\",\"hr_payroll_approve\":\"no_access\",\"hr_letters\":\"no_access\"}');
INSERT INTO `ci_roles` VALUES (4,'Support','department',2,1,4,1,'2026-10-14 10:42:12',0,'{\"dashboard\":\"write\",\"my_zone\":\"write\",\"database\":\"write\",\"leads\":\"write\",\"calls\":\"write\",\"team\":\"write\",\"kam\":\"read\",\"tasks\":\"write\",\"tickets\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"reports\":\"write\",\"system\":\"no_access\",\"users\":\"no_access\",\"roles\":\"no_access\",\"all_data\":\"read\",\"support_desk\":\"read\"}');
INSERT INTO `ci_roles` VALUES (5,'Operations','department',2,1,3,1,'2026-10-15 17:10:46',1,'{\"dashboard\":\"write\",\"my_zone\":\"write\",\"database\":\"write\",\"leads\":\"write\",\"calls\":\"write\",\"team\":\"write\",\"kam\":\"write\",\"tasks\":\"write\",\"tickets\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"reports\":\"write\",\"system\":\"no_access\",\"users\":\"no_access\",\"roles\":\"no_access\",\"start_live\":\"read\"}');
INSERT INTO `ci_roles` VALUES (6,'IT','department',2,1,3,1,'2026-10-15 17:10:46',1,'{\"dashboard\":\"write\",\"my_zone\":\"write\",\"database\":\"write\",\"leads\":\"write\",\"calls\":\"write\",\"team\":\"write\",\"kam\":\"read\",\"tasks\":\"write\",\"tickets\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"reports\":\"write\",\"system\":\"no_access\",\"users\":\"no_access\",\"roles\":\"no_access\"}');
INSERT INTO `ci_roles` VALUES (7,'HR','department',2,1,3,1,'2026-10-15 17:10:46',1,'{\"dashboard\":\"write\",\"my_zone\":\"write\",\"database\":\"write\",\"leads\":\"write\",\"calls\":\"write\",\"team\":\"write\",\"kam\":\"read\",\"tasks\":\"write\",\"tickets\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"reports\":\"write\",\"system\":\"no_access\",\"users\":\"write\",\"roles\":\"no_access\",\"hr\":\"write\",\"events_list\":\"write\"}');
INSERT INTO `ci_roles` VALUES (9,'Presentation','system',2,1,3,1,'2026-11-04 00:00:00',1,'{\"live\":\"read\",\"live_presentation\":\"read\"}');
INSERT INTO `ci_roles` VALUES (10,'KAM','department',2,1,3,1,'2026-11-05 21:12:02',1,'{\"dash6_company\":\"no_access\",\"dashboard\":\"read\",\"dash6_backoffice\":\"no_access\",\"dash6_kam\":\"read\",\"calls\":\"write\",\"live\":\"read\",\"help_support\":\"read\",\"self_training\":\"read\",\"events_view\":\"read\",\"data_creation\":\"no_access\",\"all_data\":\"no_access\",\"support_desk\":\"no_access\",\"start_live\":\"no_access\",\"database\":\"write\",\"leads\":\"write\",\"orders\":\"write\",\"sales\":\"write\",\"followups\":\"write\",\"meetings\":\"write\",\"verticals\":\"write\",\"vertical_followups\":\"write\",\"internal_tasks\":\"write\",\"external_tasks\":\"write\",\"task_followups\":\"write\",\"tickets\":\"write\",\"database_transfer\":\"write\",\"campaigns\":\"write\",\"ai_agents\":\"no_access\",\"team_database\":\"write\",\"team_leads\":\"write\",\"team_orders\":\"write\",\"team_followups\":\"write\",\"team_meetings\":\"write\",\"team_assignmeetings\":\"write\",\"team_assignleads\":\"write\",\"team_verticals\":\"write\",\"team_members\":\"write\",\"team_chart\":\"write\",\"team_tasks\":\"write\",\"team_tickets\":\"write\",\"lead_review\":\"no_access\",\"orders_pending\":\"no_access\",\"kam_onboard\":\"no_access\",\"orders_open\":\"no_access\",\"orders_followups\":\"no_access\",\"orders_completed\":\"no_access\",\"orders_closed\":\"no_access\",\"orders_rejected\":\"no_access\",\"orders_management\":\"no_access\",\"orders_invoices\":\"no_access\",\"orders_payments\":\"no_access\",\"kam_clients\":\"write\",\"kam_schedule\":\"write\",\"kam_sales_approval\":\"write\",\"kam_task_followup\":\"write\",\"kam_reminders\":\"write\",\"kam_report\":\"read\",\"hr\":\"no_access\",\"hr_staff\":\"no_access\",\"hr_employees\":\"no_access\",\"hr_salary\":\"no_access\",\"hr_settings\":\"no_access\",\"hr_attendance\":\"no_access\",\"hr_shifts\":\"no_access\",\"hr_leaves\":\"no_access\",\"hr_training\":\"read\",\"hr_topics\":\"read\",\"hr_targets\":\"no_access\",\"hr_lifecycle\":\"no_access\",\"hr_payroll\":\"no_access\",\"hr_vertical_payouts\":\"no_access\",\"hr_payroll_approve\":\"no_access\",\"hr_taxation\":\"no_access\",\"hr_appraisals\":\"no_access\",\"hr_letters\":\"no_access\",\"hr_documents\":\"no_access\",\"events_list\":\"read\",\"reports_revenue\":\"read\",\"reports_projections\":\"read\",\"reports_download\":\"read\",\"rep_company\":\"no_access\",\"reports_analysis\":\"read\",\"rep_backoffice\":\"no_access\",\"rep_kam\":\"no_access\",\"reports_insights\":\"read\",\"reports_leads\":\"read\",\"reports_followups\":\"read\",\"reports_users\":\"read\",\"reports_perf_dep\":\"read\",\"reports_perf_ind\":\"read\",\"users\":\"no_access\",\"roles\":\"no_access\",\"terms\":\"no_access\",\"leads_import\":\"no_access\",\"leads_export\":\"no_access\",\"assigned_leads\":\"no_access\",\"admin_logs\":\"no_access\",\"live_location\":\"no_access\",\"system_assignee\":\"no_access\",\"system_kam_clients\":\"write\",\"calling_api\":\"no_access\",\"integrations\":\"no_access\",\"marketing\":\"no_access\",\"qc_panel\":\"no_access\",\"my_zone\":\"write\",\"team\":\"write\",\"back_office\":\"no_access\",\"kam\":\"write\",\"reports\":\"read\",\"system\":\"write\",\"tasks\":\"write\",\"events\":\"read\"}');
/*!40000 ALTER TABLE `ci_roles` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `ci_terms` WRITE;
/*!40000 ALTER TABLE `ci_terms` DISABLE KEYS */;
INSERT INTO `ci_terms` VALUES (1,0,'v_cat','Advertising & Newsmedia','2020-08-18 07:31:23',1,0);
INSERT INTO `ci_terms` VALUES (2,0,'v_cat','Aerospace','2020-08-18 07:34:39',1,0);
INSERT INTO `ci_terms` VALUES (3,0,'v_cat','Agriculture & Commodities','2020-08-18 09:24:53',1,0);
INSERT INTO `ci_terms` VALUES (4,0,'v_cat','E-Commerce & Fashion','2020-08-18 09:25:27',1,0);
INSERT INTO `ci_terms` VALUES (5,0,'v_cat','Education','2020-08-18 09:26:12',1,0);
INSERT INTO `ci_terms` VALUES (6,0,'v_cat','Electronics & Electricals','2020-08-18 09:26:20',1,0);
INSERT INTO `ci_terms` VALUES (7,0,'v_cat','Energy, Oil & Gas','2020-08-18 09:26:28',1,0);
INSERT INTO `ci_terms` VALUES (8,0,'v_cat','Entertainment & Music','2020-08-18 09:26:36',1,0);
INSERT INTO `ci_terms` VALUES (9,0,'v_cat','Finance & Consulting','2020-08-18 09:26:47',1,0);
INSERT INTO `ci_terms` VALUES (10,9,'v_cat','Accountants / Bookkeepers','2020-08-18 09:27:19',1,0);
INSERT INTO `ci_terms` VALUES (11,9,'v_cat','Auditor / Chartered / Certified Accountants','2020-08-18 09:27:45',1,0);
INSERT INTO `ci_terms` VALUES (12,9,'v_cat','Bank','2020-08-18 09:28:09',1,0);
INSERT INTO `ci_terms` VALUES (13,9,'v_cat','Business Management consultancy','2020-08-18 09:28:22',1,0);
INSERT INTO `ci_terms` VALUES (14,9,'v_cat','Credit Cards & Loans Agencies','2020-08-18 09:28:35',1,0);
INSERT INTO `ci_terms` VALUES (15,9,'v_cat','PRO & Business Setup','2020-08-18 09:28:47',1,0);
INSERT INTO `ci_terms` VALUES (16,9,'v_cat','Legal & General Services','2020-08-18 09:29:09',1,0);
INSERT INTO `ci_terms` VALUES (17,0,'v_cat','Healthcare & Medical','2020-08-18 09:29:24',1,0);
INSERT INTO `ci_terms` VALUES (18,17,'v_cat','Clinics & Laboratories','2020-08-18 09:29:34',1,0);
INSERT INTO `ci_terms` VALUES (19,17,'v_cat','Doctors','2020-08-18 09:29:46',1,0);
INSERT INTO `ci_terms` VALUES (20,17,'v_cat','Hospitals','2020-08-18 09:29:57',1,0);
INSERT INTO `ci_terms` VALUES (21,17,'v_cat','Pharmaceutical','2020-08-18 09:30:25',1,0);
INSERT INTO `ci_terms` VALUES (22,0,'v_cat','Manufacturing & Industrial Production','2020-08-18 09:30:40',1,0);
INSERT INTO `ci_terms` VALUES (23,0,'v_cat','Mining','2020-08-18 09:30:56',1,0);
INSERT INTO `ci_terms` VALUES (24,0,'v_cat','Real Estate & Construction','2020-08-18 09:31:17',1,0);
INSERT INTO `ci_terms` VALUES (25,24,'v_cat','Building Management Agency','2020-08-18 09:32:01',1,0);
INSERT INTO `ci_terms` VALUES (26,24,'v_cat','Real Estate Brokers','2020-08-18 09:32:14',1,0);
INSERT INTO `ci_terms` VALUES (27,24,'v_cat','Real Estate Consultants','2020-08-18 09:32:26',1,0);
INSERT INTO `ci_terms` VALUES (28,24,'v_cat','Real Estate Development','2020-08-18 09:32:42',1,0);
INSERT INTO `ci_terms` VALUES (29,24,'v_cat','Architect & Engineers','2020-08-18 09:32:55',1,0);
INSERT INTO `ci_terms` VALUES (30,24,'v_cat','Security Guards Agency','2020-08-18 09:33:07',1,0);
INSERT INTO `ci_terms` VALUES (31,0,'v_cat','Telecom & IT','2020-08-18 09:33:27',1,0);
INSERT INTO `ci_terms` VALUES (32,31,'v_cat','IT Trading','2020-08-18 09:33:59',1,0);
INSERT INTO `ci_terms` VALUES (33,31,'v_cat','Telecom','2020-08-18 09:34:09',1,0);
INSERT INTO `ci_terms` VALUES (34,31,'v_cat','Web / Software Development','2020-08-18 09:34:19',1,0);
INSERT INTO `ci_terms` VALUES (35,31,'v_cat','Digital Marketing','2020-08-18 09:34:31',1,0);
INSERT INTO `ci_terms` VALUES (36,0,'v_cat','Transport','2020-08-18 09:34:37',1,0);
INSERT INTO `ci_terms` VALUES (37,36,'v_cat','Delivery Services','2020-08-18 09:34:54',1,0);
INSERT INTO `ci_terms` VALUES (38,36,'v_cat','Loading Vehicles','2020-08-18 09:35:05',1,0);
INSERT INTO `ci_terms` VALUES (39,36,'v_cat','Packers & Movers','2020-08-18 09:35:35',1,0);
INSERT INTO `ci_terms` VALUES (40,36,'v_cat','Shipping Cargo','2020-08-18 09:35:47',1,0);
INSERT INTO `ci_terms` VALUES (41,36,'v_cat','Taxi Services','2020-08-18 09:36:15',1,0);
INSERT INTO `ci_terms` VALUES (42,0,'v_cat','Travel, Hospitality & Food','2020-08-18 09:36:35',1,0);
INSERT INTO `ci_terms` VALUES (43,42,'v_cat','Hotels','2020-08-18 09:36:50',1,0);
INSERT INTO `ci_terms` VALUES (44,42,'v_cat','Restaurant, Lounges & Bars','2020-08-18 09:37:05',1,0);
INSERT INTO `ci_terms` VALUES (45,42,'v_cat','Spa, Massage & Beauty Center','2020-08-18 09:37:19',1,0);
INSERT INTO `ci_terms` VALUES (46,42,'v_cat','Travel / VISA Agency','2020-08-18 09:37:33',1,0);
INSERT INTO `ci_terms` VALUES (47,0,'v_cat','Misc','2020-08-18 09:37:49',1,0);
INSERT INTO `ci_terms` VALUES (48,47,'v_cat','Sales Executive / BDE','2020-08-18 09:38:00',1,0);
INSERT INTO `ci_terms` VALUES (49,47,'v_cat','Misc Commission Contracts','2020-08-18 09:38:10',1,0);
INSERT INTO `ci_terms` VALUES (50,0,'v_status','Active','2020-08-18 09:39:11',1,0);
INSERT INTO `ci_terms` VALUES (51,0,'v_status','Inactive','2020-08-18 09:39:55',1,0);
INSERT INTO `ci_terms` VALUES (52,0,'v_status','Warm','2020-08-18 09:40:16',0,1);
INSERT INTO `ci_terms` VALUES (53,0,'v_status','Hot','2020-08-18 09:40:27',0,1);
INSERT INTO `ci_terms` VALUES (54,0,'v_status','Boom','2020-08-18 09:40:37',0,1);
INSERT INTO `ci_terms` VALUES (55,0,'v_status','Lost','2020-08-18 09:40:47',0,1);
INSERT INTO `ci_terms` VALUES (56,0,'v_status','Not Interested','2020-08-18 09:40:57',1,0);
INSERT INTO `ci_terms` VALUES (57,0,'p_cat','Telecom','2020-09-18 06:34:39',1,0);
INSERT INTO `ci_terms` VALUES (58,0,'p_cat','Accounting','2020-09-18 06:35:19',1,0);
INSERT INTO `ci_terms` VALUES (59,0,'p_cat','IT','2020-09-18 06:35:59',1,0);
INSERT INTO `ci_terms` VALUES (60,0,'p_cat','Other','2020-09-18 06:36:20',1,0);
INSERT INTO `ci_terms` VALUES (61,57,'p_cat','All','2020-09-18 06:40:01',1,0);
INSERT INTO `ci_terms` VALUES (62,57,'p_cat','Fixed','2020-09-18 06:40:22',1,0);
INSERT INTO `ci_terms` VALUES (63,57,'p_cat','Mobile','2020-09-18 06:40:40',1,0);
INSERT INTO `ci_terms` VALUES (64,57,'p_cat','Managed','2020-09-18 06:41:05',1,0);
INSERT INTO `ci_terms` VALUES (65,57,'p_cat','Other','2020-09-18 06:41:34',1,0);
INSERT INTO `ci_terms` VALUES (66,58,'p_cat','All','2020-09-18 06:42:00',1,0);
INSERT INTO `ci_terms` VALUES (67,58,'p_cat','Registration','2020-09-18 06:42:23',1,0);
INSERT INTO `ci_terms` VALUES (68,58,'p_cat','Bookkeeping','2020-09-18 06:43:07',1,0);
INSERT INTO `ci_terms` VALUES (69,58,'p_cat','Audit','2020-09-18 06:43:23',1,0);
INSERT INTO `ci_terms` VALUES (70,58,'p_cat','Other','2020-09-18 06:43:48',1,0);
INSERT INTO `ci_terms` VALUES (71,59,'p_cat','Server','2020-09-18 06:45:30',1,0);
INSERT INTO `ci_terms` VALUES (72,59,'p_cat','PABX','2020-09-18 06:46:09',1,0);
INSERT INTO `ci_terms` VALUES (73,59,'p_cat','Security','2020-09-18 06:47:03',1,0);
INSERT INTO `ci_terms` VALUES (74,59,'p_cat','AMC','2020-09-18 06:47:30',1,0);
INSERT INTO `ci_terms` VALUES (75,60,'p_cat','Other','2020-09-18 07:01:16',1,0);
INSERT INTO `ci_terms` VALUES (76,0,'lead_cat','Database','2020-12-11 11:08:29',1,0);
INSERT INTO `ci_terms` VALUES (77,0,'lead_cat','Cold','2020-12-11 11:08:41',1,0);
INSERT INTO `ci_terms` VALUES (78,0,'lead_cat','Warm','2020-12-11 11:08:54',1,0);
INSERT INTO `ci_terms` VALUES (79,0,'lead_cat','Hot','2020-12-11 11:09:05',1,0);
INSERT INTO `ci_terms` VALUES (80,0,'lead_cat','Instant','2020-12-11 11:09:16',1,0);
INSERT INTO `ci_terms` VALUES (81,0,'v_status','Database','2026-10-14 08:34:09',1,0);
INSERT INTO `ci_terms` VALUES (82,0,'business_cat','Trading','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (83,0,'business_cat','Retail','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (84,0,'business_cat','Corporate Services','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (85,0,'business_cat','Information Technology','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (86,0,'business_cat','Real Estate','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (87,0,'business_cat','Hospitality & Tourism','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (88,0,'business_cat','Healthcare','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (89,0,'business_cat','Manufacturing','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (90,0,'business_cat','Finance & Accounting','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (91,0,'business_cat','Logistics & Transport','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (92,0,'business_cat','Other','2026-10-14 10:44:03',1,0);
INSERT INTO `ci_terms` VALUES (93,0,'data_source','Google Maps','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (94,0,'data_source','GPS','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (95,0,'data_source','Referral','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (96,0,'data_source','Website','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (97,0,'data_source','Social Media','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (98,0,'data_source','Other','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (99,0,'database_cat','New Database','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (100,0,'database_cat','Online','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (101,0,'database_cat','Reference','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (102,0,'database_cat','Ported Out','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (103,0,'database_cat','Competitor DB','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (104,0,'database_cat','Contract Agent','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (105,0,'database_cat','Raw / Unverified','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (106,0,'database_cat','Cleansed','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (107,0,'database_cat','High Value','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (108,0,'database_cat','Re-engaged','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (109,0,'database_cat','Other','0000-00-00 00:00:00',1,0);
INSERT INTO `ci_terms` VALUES (110,0,'p_cat','Learning','2026-10-23 15:09:00',1,0);
INSERT INTO `ci_terms` VALUES (111,110,'p_cat','Corporate Training','2026-10-23 15:09:00',1,0);
INSERT INTO `ci_terms` VALUES (112,110,'p_cat','Professional Certifications','2026-10-23 15:09:00',1,0);
INSERT INTO `ci_terms` VALUES (113,110,'p_cat','Individual Courses','2026-10-23 15:09:00',1,0);
INSERT INTO `ci_terms` VALUES (114,110,'p_cat','Workshops','2026-10-23 15:09:00',1,0);
/*!40000 ALTER TABLE `ci_terms` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `ci_sources` WRITE;
/*!40000 ALTER TABLE `ci_sources` DISABLE KEYS */;
INSERT INTO `ci_sources` VALUES (1,'New Database',1);
INSERT INTO `ci_sources` VALUES (2,'Online',1);
INSERT INTO `ci_sources` VALUES (3,'Reference',1);
INSERT INTO `ci_sources` VALUES (4,'Ported Out',1);
INSERT INTO `ci_sources` VALUES (5,'Competitor DB',1);
INSERT INTO `ci_sources` VALUES (6,'Contract Agent',1);
/*!40000 ALTER TABLE `ci_sources` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `ci_status` WRITE;
/*!40000 ALTER TABLE `ci_status` DISABLE KEYS */;
INSERT INTO `ci_status` VALUES (36,'Database',1);
INSERT INTO `ci_status` VALUES (37,'Expected',1);
INSERT INTO `ci_status` VALUES (38,'In Process',1);
INSERT INTO `ci_status` VALUES (39,'Closed',1);
INSERT INTO `ci_status` VALUES (40,'Submitted',1);
INSERT INTO `ci_status` VALUES (41,'Pending Activation',1);
INSERT INTO `ci_status` VALUES (42,'Activated',1);
INSERT INTO `ci_status` VALUES (43,'Lost',1);
INSERT INTO `ci_status` VALUES (44,'Invalid Entry',1);
INSERT INTO `ci_status` VALUES (45,'Busy / Not Responding',1);
INSERT INTO `ci_status` VALUES (46,'DNCR / DND',1);
INSERT INTO `ci_status` VALUES (47,'Follow up / Call Back',1);
INSERT INTO `ci_status` VALUES (48,'Future',1);
INSERT INTO `ci_status` VALUES (49,'Not Interested',1);
INSERT INTO `ci_status` VALUES (50,'Not Valid',1);
/*!40000 ALTER TABLE `ci_status` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `ci_reference_sequences` WRITE;
/*!40000 ALTER TABLE `ci_reference_sequences` DISABLE KEYS */;
INSERT INTO `ci_reference_sequences` VALUES ('database','D',0,10,'2026-10-26 11:47:09');
INSERT INTO `ci_reference_sequences` VALUES ('lead','L',0,10,'2026-10-26 11:47:09');
INSERT INTO `ci_reference_sequences` VALUES ('order','O',0,10,'2026-10-26 11:47:09');
INSERT INTO `ci_reference_sequences` VALUES ('sales','S',0,10,'2026-10-26 11:47:09');
/*!40000 ALTER TABLE `ci_reference_sequences` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `schema_migrations` WRITE;
/*!40000 ALTER TABLE `schema_migrations` DISABLE KEYS */;
INSERT INTO `schema_migrations` VALUES (1,'001','core_crm_schema',1,'2026-11-02 06:43:35',249,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (2,'002','ticket_history_and_followups',1,'2026-11-02 06:43:35',140,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (3,'003','user_security_and_currency',1,'2026-11-02 06:43:35',28,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (4,'004','lead_quote_and_trn',1,'2026-11-02 06:43:35',69,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (5,'005','finance_and_operations',1,'2026-11-02 06:43:36',173,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (6,'006','orders_and_sales_approval',1,'2026-11-02 06:43:36',71,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (7,'007','performance_indexes',1,'2026-11-02 06:43:36',69,'SUCCESS');
INSERT INTO `schema_migrations` VALUES (8,'008','pipeline_sales_ref_no',2,'2026-11-04 14:22:24',54,'SUCCESS');
/*!40000 ALTER TABLE `schema_migrations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


UPDATE ci_reference_sequences SET current_val = 0;
SET FOREIGN_KEY_CHECKS = 1;
