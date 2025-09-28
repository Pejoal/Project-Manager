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

  'home' => 'Startseite',
  'welcome' => 'Willkommen',
  'language' => 'Sprache',

  // Navigation
  'dashboard' => 'Dashboard',
  'clients' => 'Kunden',
  'projects' => 'Projekte',
  'tasks' => 'Aufgaben',
  'profile' => 'Profil',
  'map' => 'Karte',
  'activities' => 'Aktivitäten',
  'logout' => 'Abmelden',
  'login' => 'Anmelden',
  'register' => 'Registrieren',

  // Theme
  'light_mode' => '☀️ Heller Modus',
  'dark_mode' => '🌙 Dunkler Modus',

  // Team Management
  'manage_team' => 'Team verwalten',
  'team_settings' => 'Team-Einstellungen',
  'create_new_team' => 'Neues Team erstellen',
  'switch_teams' => 'Teams wechseln',
  'add_team_member' => 'Teammitglied hinzufügen',
  'add_team_member_description' =>
    'Fügen Sie ein neues Teammitglied zu Ihrem Team hinzu, damit es mit Ihnen zusammenarbeiten kann.',
  'provide_email_address_team' =>
    'Bitte geben Sie die E-Mail-Adresse der Person an, die Sie zu diesem Team hinzufügen möchten.',
  'added' => 'Hinzugefügt.',
  'add' => 'Hinzufügen',
  'pending_team_invitations' => 'Ausstehende Team-Einladungen',
  'pending_team_invitations_description' =>
    'Diese Personen wurden zu Ihrem Team eingeladen und haben eine Einladungs-E-Mail erhalten. Sie können dem Team beitreten, indem sie die E-Mail-Einladung annehmen.',
  'team_members' => 'Teammitglieder',
  'team_members_description' => 'Alle Personen, die Teil dieses Teams sind.',
  'manage_role' => 'Rolle verwalten',
  'leave' => 'Verlassen',
  'remove' => 'Entfernen',
  'leave_team' => 'Team verlassen',
  'leave_team_confirmation' => 'Sind Sie sicher, dass Sie dieses Team verlassen möchten?',
  'remove_team_member' => 'Teammitglied entfernen',
  'remove_team_member_confirmation' => 'Sind Sie sicher, dass Sie diese Person aus dem Team entfernen möchten?',
  'role' => 'Rolle',

  // Account Management
  'manage_account' => 'Konto verwalten',
  'project_statuses' => 'Projekt-Status',
  'project_priorities' => 'Projekt-Prioritäten',
  'task_statuses' => 'Aufgaben-Status',
  'task_priorities' => 'Aufgaben-Prioritäten',
  'api_tokens' => 'API-Token',
  'terms_of_service' => 'Nutzungsbedingungen',
  'privacy_policy' => 'Datenschutzrichtlinie',

  // Profile
  'profile_information' => 'Profil-Informationen',
  'profile_information_description' => 'Aktualisieren Sie die Profil-Informationen und E-Mail-Adresse Ihres Kontos.',
  'photo' => 'Foto',
  'select_new_photo' => 'Neues Foto auswählen',
  'remove_photo' => 'Foto entfernen',
  'name' => 'Name',
  'username' => 'Benutzername',
  'email' => 'E-Mail',
  'email_unverified' => 'Ihre E-Mail-Adresse ist nicht verifiziert.',
  'resend_verification_email' => 'Klicken Sie hier, um die Verifizierungs-E-Mail erneut zu senden.',
  'verification_link_sent' => 'Ein neuer Verifizierungslink wurde an Ihre E-Mail-Adresse gesendet.',
  'password' => 'Passwort',
  'confirm_password' => 'Passwort bestätigen',
  'current_password' => 'Aktuelles Passwort',
  'new_password' => 'Neues Passwort',
  'user_name' => 'Benutzername',

  // Password Update
  'update_password' => 'Passwort aktualisieren',
  'update_password_description' =>
    'Stellen Sie sicher, dass Ihr Konto ein langes, zufälliges Passwort verwendet, um sicher zu bleiben.',
  'saved' => 'Gespeichert.',
  'save' => 'Speichern',

  // Two Factor Authentication
  'two_factor_authentication' => 'Zwei-Faktor-Authentifizierung',
  'two_factor_authentication_description' =>
    'Fügen Sie zusätzliche Sicherheit zu Ihrem Konto mit Zwei-Faktor-Authentifizierung hinzu.',
  'enabled' => 'Sie haben die Zwei-Faktor-Authentifizierung aktiviert.',
  'finish_enabling' => 'Aktivierung der Zwei-Faktor-Authentifizierung abschließen.',
  'not_enabled' => 'Sie haben die Zwei-Faktor-Authentifizierung nicht aktiviert.',
  'secure_random_token' =>
    'Wenn die Zwei-Faktor-Authentifizierung aktiviert ist, werden Sie während der Authentifizierung zu einem sicheren, zufälligen Token aufgefordert. Sie können diesen Token aus der Google Authenticator-App Ihres Telefons abrufen.',
  'qr_code_instructions' =>
    'Um die Aktivierung der Zwei-Faktor-Authentifizierung abzuschließen, scannen Sie den folgenden QR-Code mit der Authenticator-App Ihres Telefons oder geben Sie den Setup-Schlüssel ein und geben Sie den generierten OTP-Code an.',
  'enabled_qr_code_instructions' =>
    'Die Zwei-Faktor-Authentifizierung ist jetzt aktiviert. Scannen Sie den folgenden QR-Code mit der Authenticator-App Ihres Telefons oder geben Sie den Setup-Schlüssel ein.',
  'setup_key' => 'Setup-Schlüssel:',
  'code' => 'Code',
  'recovery_codes_instructions' =>
    'Bewahren Sie diese Wiederherstellungscodes in einem sicheren Passwort-Manager auf. Sie können verwendet werden, um den Zugang zu Ihrem Konto wiederherzustellen, wenn Ihr Zwei-Faktor-Authentifizierungsgerät verloren geht.',
  'regenerate_recovery_codes' => 'Wiederherstellungscodes neu generieren',
  'show_recovery_codes' => 'Wiederherstellungscodes anzeigen',
  'enable' => 'Aktivieren',
  'confirm' => 'Bestätigen',
  'cancel' => 'Abbrechen',
  'disable' => 'Deaktivieren',

  // Delete Account
  'delete_account' => 'Konto löschen',
  'delete_account_description' => 'Ihr Konto dauerhaft löschen.',
  'delete_account_confirmation' =>
    'Sobald Ihr Konto gelöscht wird, werden alle seine Ressourcen und Daten dauerhaft gelöscht. Bevor Sie Ihr Konto löschen, laden Sie bitte alle Daten oder Informationen herunter, die Sie behalten möchten.',

  // Password Confirmation
  'please_confirm_your_password' => 'Zu Ihrer Sicherheit bestätigen Sie bitte Ihr Passwort, um fortzufahren.',

  // Pagination
  'previous' => 'Vorherige',
  'next' => 'Nächste',
  'total' => 'Gesamt',

  // Maps
  'maplibre_example' => 'Maplibre Beispiel',
  'show_controls' => 'Steuerelemente anzeigen',
  'hide_controls' => 'Steuerelemente ausblenden',
  'settings' => 'Einstellungen',
  'select_map_style' => 'Kartenstil auswählen',
  'reset_view' => 'Ansicht zurücksetzen',
  'select_mode' => 'Auswahlmodus',
  'enable_adding_points' => 'Punkte hinzufügen aktivieren',
  'disable_adding_points' => 'Punkte hinzufügen deaktivieren',
  'enable_adding_linestring' => 'Linien hinzufügen aktivieren',
  'disable_adding_linestring' => 'Linien hinzufügen deaktivieren',
  'enable_adding_polygon' => 'Polygone hinzufügen aktivieren',
  'disable_adding_polygon' => 'Polygone hinzufügen deaktivieren',
  'delete_all_features' => 'Alle Features löschen',
  'show_features_list' => 'Features-Liste anzeigen',
  'hide_features_list' => 'Features-Liste ausblenden',
  'filter_features' => 'Features filtern',
  'name_column' => 'Name',
  'type_column' => 'Typ',
  'actions_column' => 'Aktionen',
  'edit_action' => 'Bearbeiten',
  'delete_action' => 'Löschen',
  'edit_feature' => 'Feature bearbeiten',
  'properties' => 'Eigenschaften',
  'property_key' => 'Eigenschafts-Schlüssel',
  'property_value' => 'Eigenschafts-Wert',
  'add_property' => 'Eigenschaft hinzufügen',
  'are_you_sure_delete_all_features' => 'Sind Sie sicher, dass Sie alle Features löschen möchten?',
  'are_you_sure_delete_feature' => 'Sind Sie sicher, dass Sie dieses Feature löschen möchten?',

  // Dashboard
  'update_chart_colors' => 'Diagrammfarben aktualisieren',
  'clients_color' => 'Kundenfarbe',
  'projects_color' => 'Projektfarbe',
  'tasks_color' => 'Aufgabenfarbe',
  'overview' => 'Übersicht',
  'tasks_statuses' => 'Aufgaben-Status',
  'tasks_priorities' => 'Aufgaben-Prioritäten',
  'clients_projects_tasks' => 'Kunden, Projekte und Aufgaben',

  // Welcome Page
  'welcome_title' => 'Willkommen bei Ihrem Unternehmensverwaltungssystem!',
  'welcome_description' =>
    'Verwalten Sie die Ressourcen Ihres Unternehmens effizient und effektiv mit unserem umfassenden Verwaltungssystem.',

  // Team Name Form
  'team_name' => 'Team-Name',
  'team_name_description' => 'Der Name des Teams und Eigentümer-Informationen.',
  'team_owner' => 'Team-Eigentümer',

  // Project Components
  'basic_data' => 'Basisdaten',
  'project_id' => 'Projekt-ID',
  'description' => 'Beschreibung',
  'status' => 'Status',
  'priority' => 'Priorität',
  'start_date' => 'Startdatum',
  'end_date' => 'Enddatum',
  'created_at' => 'Erstellt am',
  'tasks_completed' => 'Aufgaben abgeschlossen',
  'progress' => 'Fortschritt',

  // Common Actions
  'create' => 'Erstellen',
  'update' => 'Aktualisieren',
  'edit' => 'Bearbeiten',
  'delete' => 'Löschen',
  'show' => 'Anzeigen',
  'view' => 'Ansehen',
  'back' => 'Zurück',
  'close' => 'Schließen',

  // Milestones
  'milestones' => 'Meilensteine',
  'milestone' => 'Meilenstein',
  'create_milestone' => 'Meilenstein erstellen',
  'milestone_id' => 'Meilenstein-ID',
  'phase' => 'Phase',
  'phases' => 'Phasen',

  // Clients
  'client' => 'Kunde',
  'client_id' => 'Kunden-ID',
  'phone' => 'Telefon',
  'add_new_client' => 'Neuen Kunden hinzufügen',
  'create_client' => 'Kunde erstellen',
  'edit_client' => 'Kunde bearbeiten',
  'show_client' => 'Kunde anzeigen',

  // General
  'id' => 'ID',
  'project' => 'Projekt',
  'are_you_sure' => 'Sind Sie sicher?',
  'already_registered' => 'Bereits registriert?',
  'remember_me' => 'Angemeldet bleiben',
  'forgot_password' => 'Passwort vergessen?',
  'reset_password' => 'Passwort zurücksetzen',
  'email_password_reset_link' => 'Passwort-Reset-Link per E-Mail senden',
  'activity_log' => 'Aktivitätsprotokoll',
  'api_tokens_title' => 'API-Token',
  'email_verification' => 'E-Mail-Verifizierung',
  'resend_verification_email_button' => 'Verifizierungs-E-Mail erneut senden',
  'two_factor_confirmation' => 'Zwei-Faktor-Bestätigung',
  'secure_area' => 'Sicherer Bereich',
  'confirm_password_secure' =>
    'Dies ist ein sicherer Bereich der Anwendung. Bitte bestätigen Sie Ihr Passwort, um fortzufahren.',
  'use_recovery_code' => 'Wiederherstellungscode verwenden',
  'use_authentication_code' => 'Authentifizierungscode verwenden',
  'recovery_code' => 'Wiederherstellungscode',

  // Additional keys for forms and UI
  'select_option' => 'Option auswählen',
  'assigned_to' => 'Zugewiesen an',
  'assigned_to_me' => 'Mir zugewiesen',
  'created_by' => 'Erstellt von',
  'updated_by' => 'Aktualisiert von',
  'all_tasks' => 'Alle Aufgaben',
  'task_for' => 'Aufgabe für',
  'task_id' => 'Aufgaben-ID',
  'show_description' => 'Beschreibung anzeigen',
  'show_status' => 'Status anzeigen',
  'show_priority' => 'Priorität anzeigen',
  'show_assigned_to' => 'Zugewiesene anzeigen',
  'kanban_view' => 'Kanban-Ansicht',
  'phases_tasks' => 'Phasen - Aufgaben',
  'create_task' => 'Aufgabe erstellen',
  'edit_task' => 'Aufgabe bearbeiten',
  'view_phases' => 'Phasen anzeigen',
  'view_milestones' => 'Meilensteine anzeigen',
  'view_tasks' => 'Aufgaben anzeigen',
  'create_phase' => 'Phase erstellen',
  'show_phase' => 'Phase anzeigen',
  'phase_for' => 'Phase für',
  'phase_id' => 'Phasen-ID',
  'projects_for_client' => 'Projekte für Kunde',
  'number_of_tasks' => 'Anzahl der Aufgaben',
  'end_date' => 'Enddatum',
  'are_you_sure_delete' => 'Sind Sie sicher, dass Sie dies löschen möchten?',
  'create_project_status' => 'Projekt-Status erstellen',
  'create_project_priority' => 'Projekt-Priorität erstellen',
  'create_task_status' => 'Aufgaben-Status erstellen',
  'create_task_priority' => 'Aufgaben-Priorität erstellen',
  'select_projects' => 'Projekte auswählen',
  'select_assigned_to' => 'Zugewiesene auswählen',
  'start_datetime' => 'Start Datum/Zeit',
  'end_datetime' => 'End Datum/Zeit',

  // Profile Additional Translations
  'click_to_resend' => 'Klicken Sie hier, um die Verifizierungs-E-Mail erneut zu senden.',
  'ensure_secure_password' =>
    'Stellen Sie sicher, dass Ihr Konto ein langes, zufälliges Passwort verwendet, um sicher zu bleiben.',
  'browser_sessions' => 'Browser-Sitzungen',
  'browser_sessions_description' =>
    'Verwalten und beenden Sie Ihre aktiven Sitzungen auf anderen Browsern und Geräten.',
  'log_out_other_browser_sessions' => 'Andere Browser-Sitzungen abmelden',
  'log_out_other_browser_sessions_description' =>
    'Bitte geben Sie Ihr Passwort ein, um zu bestätigen, dass Sie sich von Ihren anderen Browser-Sitzungen auf allen Ihren Geräten abmelden möchten.',
  'done' => 'Fertig.',
  'this_device' => 'Dieses Gerät',
  'last_active' => 'Zuletzt aktiv',
  'unknown' => 'Unbekannt',
];
