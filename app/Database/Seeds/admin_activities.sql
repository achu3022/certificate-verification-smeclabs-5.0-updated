-- Create admin_activities table
CREATE TABLE `admin_activities` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) UNSIGNED NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add sample data
INSERT INTO `admin_activities` (`admin_id`, `activity_type`, `description`, `ip_address`, `created_at`) VALUES
(1, 'login', 'Logged in to the system', '127.0.0.1', NOW() - INTERVAL 5 HOUR),
(1, 'profile_update', 'Updated profile information', '127.0.0.1', NOW() - INTERVAL 3 HOUR),
(1, 'password_update', 'Changed account password', '127.0.0.1', NOW() - INTERVAL 2 HOUR),
(1, 'certificate_verify', 'Verified certificate #12345', '127.0.0.1', NOW() - INTERVAL 1 HOUR),
(1, 'login', 'Logged in to the system', '127.0.0.1', NOW());