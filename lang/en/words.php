<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Front-End Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines are used during Front-End for various
  | messages that we need to display to the user. You are free to modify
  | these language lines according to your application's requirements.
  |
   */

  'home' => 'Home',
  'welcome' => 'Welcome',
  'language' => 'Language',

  // Navigation
  'dashboard' => 'Dashboard',
  'clients' => 'Clients',
  'projects' => 'Projects',
  'tasks' => 'Tasks',
  'profile' => 'Profile',
  'map' => 'Map',
  'activities' => 'Activities',
  'logout' => 'Log Out',
  'login' => 'Log in',
  'register' => 'Register',

  // Theme
  'light_mode' => '☀️ Light Mode',
  'dark_mode' => '🌙 Dark Mode',

  // Team Management
  'manage_team' => 'Manage Team',
  'team_settings' => 'Team Settings',
  'create_new_team' => 'Create New Team',
  'switch_teams' => 'Switch Teams',

  // Account Management
  'manage_account' => 'Manage Account',
  'project_statuses' => 'Project Statuses',
  'project_priorities' => 'Project Priorities',
  'task_statuses' => 'Task Statuses',
  'task_priorities' => 'Task Priorities',
  'api_tokens' => 'API Tokens',
  'terms_of_service' => 'Terms of Service',
  'privacy_policy' => 'Privacy Policy',

  // Profile
  'profile_information' => 'Profile Information',
  'profile_information_description' => 'Update your account\'s profile information and email address.',
  'photo' => 'Photo',
  'select_new_photo' => 'Select A New Photo',
  'remove_photo' => 'Remove Photo',
  'name' => 'Name',
  'username' => 'Username',
  'email' => 'Email',
  'email_unverified' => 'Your email address is unverified.',
  'resend_verification_email' => 'Click here to re-send the verification email.',
  'verification_link_sent' => 'A new verification link has been sent to your email address.',
  'password' => 'Password',
  'confirm_password' => 'Confirm Password',
  'current_password' => 'Current Password',
  'new_password' => 'New Password',
  'user_name' => 'User Name',

  // Password Update
  'update_password' => 'Update Password',
  'update_password_description' => 'Ensure your account is using a long, random password to stay secure.',
  'saved' => 'Saved.',
  'save' => 'Save',

  // Two Factor Authentication
  'two_factor_authentication' => 'Two Factor Authentication',
  'two_factor_authentication_description' => 'Add additional security to your account using two factor authentication.',
  'enabled' => 'You have enabled two factor authentication.',
  'finish_enabling' => 'Finish enabling two factor authentication.',
  'not_enabled' => 'You have not enabled two factor authentication.',
  'secure_random_token' =>
    'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',
  'qr_code_instructions' =>
    'To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.',
  'enabled_qr_code_instructions' =>
    'Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.',
  'setup_key' => 'Setup Key:',
  'code' => 'Code',
  'recovery_codes_instructions' =>
    'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.',
  'regenerate_recovery_codes' => 'Regenerate Recovery Codes',
  'show_recovery_codes' => 'Show Recovery Codes',
  'enable' => 'Enable',
  'confirm' => 'Confirm',
  'cancel' => 'Cancel',
  'disable' => 'Disable',

  // Delete Account
  'delete_account' => 'Delete Account',
  'delete_account_description' => 'Permanently delete your account.',
  'delete_account_confirmation' =>
    'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.',

  // Password Confirmation
  'please_confirm_your_password' => 'For your security, please confirm your password to continue.',

  // Pagination
  'previous' => 'Previous',
  'next' => 'Next',
  'total' => 'Total',

  // Maps
  'maplibre_example' => 'Maplibre Example',
  'show_controls' => 'Show Controls',
  'hide_controls' => 'Hide Controls',
  'settings' => 'Settings',
  'select_map_style' => 'Select Map Style',
  'reset_view' => 'Reset View',
  'select_mode' => 'Select Mode',
  'enable_adding_points' => 'Enable Adding Points',
  'disable_adding_points' => 'Disable Adding Points',
  'enable_adding_linestring' => 'Enable Adding LineString',
  'disable_adding_linestring' => 'Disable Adding LineString',
  'enable_adding_polygon' => 'Enable Adding Polygon',
  'disable_adding_polygon' => 'Disable Adding Polygon',
  'delete_all_features' => 'Delete All Features',
  'show_features_list' => 'Show Features List',
  'hide_features_list' => 'Hide Features List',
  'filter_features' => 'Filter features',
  'name_column' => 'Name',
  'type_column' => 'Type',
  'actions_column' => 'Actions',
  'edit_action' => 'Edit',
  'delete_action' => 'Delete',
  'edit_feature' => 'Edit Feature',
  'properties' => 'Properties',
  'property_key' => 'Property Key',
  'property_value' => 'Property Value',
  'add_property' => 'Add Property',
  'are_you_sure_delete_all_features' => 'Are you sure you want to delete all features?',
  'are_you_sure_delete_feature' => 'Are you sure you want to delete this feature?',

  // Dashboard
  'update_chart_colors' => 'Update Chart Colors',
  'clients_color' => 'Clients Color',
  'projects_color' => 'Projects Color',
  'tasks_color' => 'Tasks Color',
  'overview' => 'Overview',
  'tasks_statuses' => 'Tasks Statuses',
  'tasks_priorities' => 'Tasks Priorities',
  'clients_projects_tasks' => 'Clients, Projects, and Tasks',

  // Welcome Page
  'welcome_title' => 'Welcome to Your Company Management System!',
  'welcome_description' =>
    'Manage your company\'s resources efficiently and effectively with our comprehensive management system.',

  // Team Name Form
  'team_name' => 'Team Name',
  'team_name_description' => 'The team\'s name and owner information.',
  'team_owner' => 'Team Owner',

  // Project Components
  'basic_data' => 'Basic Data',
  'project_id' => 'Project ID',
  'description' => 'Description',
  'status' => 'Status',
  'priority' => 'Priority',
  'start_date' => 'Start Date',
  'end_date' => 'End Date',
  'created_at' => 'Created at',
  'tasks_completed' => 'tasks completed',
  'progress' => 'Progress',

  // Common Actions
  'create' => 'Create',
  'update' => 'Update',
  'edit' => 'Edit',
  'delete' => 'Delete',
  'show' => 'Show',
  'view' => 'View',
  'back' => 'Back',
  'close' => 'Close',

  // Milestones
  'milestones' => 'Milestones',
  'milestone' => 'Milestone',
  'create_milestone' => 'Create Milestone',
  'milestone_id' => 'Milestone ID',
  'phase' => 'Phase',
  'phases' => 'Phases',

  // Clients
  'client' => 'Client',
  'client_id' => 'Client ID',
  'phone' => 'Phone',
  'add_new_client' => 'Add New Client',
  'create_client' => 'Create Client',
  'edit_client' => 'Edit Client',
  'show_client' => 'Show Client',

  // General
  'id' => 'ID',
  'project' => 'Project',
  'are_you_sure' => 'Are you sure?',
  'already_registered' => 'Already registered?',
  'remember_me' => 'Remember me',
  'forgot_password' => 'Forgot your password?',
  'reset_password' => 'Reset Password',
  'email_password_reset_link' => 'Email Password Reset Link',
  'activity_log' => 'Activity Log',
  'api_tokens_title' => 'API Tokens',
  'email_verification' => 'Email Verification',
  'resend_verification_email_button' => 'Resend Verification Email',
  'two_factor_confirmation' => 'Two-factor Confirmation',
  'secure_area' => 'Secure Area',
  'confirm_password_secure' =>
    'This is a secure area of the application. Please confirm your password before continuing.',
  'use_recovery_code' => 'Use a recovery code',
  'use_authentication_code' => 'Use an authentication code',
  'recovery_code' => 'Recovery Code',
];
