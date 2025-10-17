<?php
/**
 * CHWR - Application Configuration
 */

return [
    'app_name' => 'CHWR - Cobourg Homeless Warming Room',
    'app_version' => '1.0.0',
    'base_url' => 'http://localhost/CHWR',
    
    // Security
    'session_lifetime' => 7200, // 2 hours in seconds
    'remember_me_duration' => 345600, // 4 days in seconds
    'password_min_length' => 8,
    'max_login_attempts' => 5,
    'login_lockout_duration' => 900, // 15 minutes
    
    // Assessment
    'autosave_delay' => 2000, // milliseconds
    'assessment_sections' => [
        'A' => 'Consent & Preferences',
        'B' => 'Mental Health, Safety, Crisis',
        'C' => 'Housing & Shelter',
        'D' => 'Medical, Dental, Vision',
        'E' => 'Counseling / Support',
        'F' => 'Substance Use',
        'G' => 'Legal Issues',
        'H' => 'Income, Benefits, ID',
        'I' => 'Family & Social Network',
        'J' => 'Employment & Education',
        'K' => 'Life Skills & Daily Living',
        'L' => 'Recovery Planning'
    ],
    
    // Traffic light thresholds
    'traffic_light' => [
        'green_max' => 33,
        'yellow_max' => 66,
        'red_min' => 67
    ],
    
    // File uploads
    'upload_path' => __DIR__ . '/../uploads/',
    'max_file_size' => 5242880, // 5MB
    'allowed_file_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
    
    // Pagination
    'items_per_page' => 20,
    
    // Timezone
    'timezone' => 'America/Toronto',
    
    // Email (if implementing email notifications)
    'email_enabled' => false,
    'smtp_host' => '',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'from_email' => 'noreply@chwr.org',
    'from_name' => 'CHWR System'
];
