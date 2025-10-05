<?php

// routes/breadcrumbs.php

use App\Models\Client;
use App\Models\EmployeeProfile;
use App\Models\Milestone;
use App\Models\Payslip;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Campaign;
use App\Models\SupportTicket;
use App\Models\Interaction;
use App\Models\KnowledgeBaseArticle;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// --- Home ---
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
  $trail->push('Dashboard', route('dashboard'));
});

// --- Client Management ---
// Dashboard > Clients
Breadcrumbs::for('clients.index', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('Clients', route('clients.index'));
});

// Dashboard > Clients > [Client Name]
Breadcrumbs::for('clients.show', function (BreadcrumbTrail $trail, Client $client) {
  $trail->parent('clients.index');
  $trail->push($client->name, route('clients.show', $client));
});

// Dashboard > Clients > [Client Name] > Edit
Breadcrumbs::for('clients.edit', function (BreadcrumbTrail $trail, Client $client) {
  $trail->parent('clients.show', $client);
  $trail->push('Edit', route('clients.edit', $client));
});

// --- Project Management ---
// Dashboard > Projects
Breadcrumbs::for('projects.index', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('Projects', route('projects.index'));
});

// Dashboard > Projects > Create
Breadcrumbs::for('projects.create', function (BreadcrumbTrail $trail) {
  $trail->parent('projects.index');
  $trail->push('Create', route('projects.create'));
});

// Dashboard > Projects > [Project Name]
Breadcrumbs::for('projects.show', function (BreadcrumbTrail $trail, Project $project) {
  $trail->parent('projects.index');
  $trail->push($project->name, route('projects.show', $project));
});

// Dashboard > Projects > [Project Name] > Edit
Breadcrumbs::for('projects.edit', function (BreadcrumbTrail $trail, Project $project) {
  $trail->parent('projects.show', $project);
  $trail->push('Edit', route('projects.edit', $project));
});

// --- Task Management (Nested under Projects) ---
// Dashboard > All Tasks
Breadcrumbs::for('tasks.all', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('All Tasks', route('tasks.all'));
});

// Dashboard > Projects > [Project Name] > Tasks
Breadcrumbs::for('tasks.index', function (BreadcrumbTrail $trail, Project $project) {
  $trail->parent('projects.show', $project);
  $trail->push('Tasks', route('tasks.index', $project));
});

// Dashboard > Projects > [Project Name] > Tasks > [Task Name]
Breadcrumbs::for('tasks.show', function (BreadcrumbTrail $trail, Project $project, Task $task) {
  $trail->parent('tasks.index', $project);
  $trail->push($task->name, route('tasks.show', [$project, $task]));
});

// Dashboard > Projects > [Project Name] > Tasks > [Task Name] > Edit
Breadcrumbs::for('tasks.edit', function (BreadcrumbTrail $trail, Project $project, Task $task) {
  $trail->parent('tasks.show', $project, $task);
  $trail->push('Edit', route('tasks.edit', [$project, $task]));
});

// --- Phases & Milestones (Nested under Projects) ---
// Dashboard > Projects > [Project Name] > Phases
Breadcrumbs::for('phases.index', function (BreadcrumbTrail $trail, Project $project) {
  $trail->parent('projects.show', $project);
  $trail->push('Phases', route('phases.index', $project));
});

// Dashboard > Projects > [Project Name] > Phases > [Phase Name]
Breadcrumbs::for('phases.show', function (BreadcrumbTrail $trail, Project $project, Phase $phase) {
  $trail->parent('phases.index', $project);
  $trail->push($phase->name, route('phases.show', [$project, $phase]));
});

// Dashboard > Projects > [Project Name] > Milestones
Breadcrumbs::for('milestones.index', function (BreadcrumbTrail $trail, Project $project) {
  $trail->parent('projects.show', $project);
  $trail->push('Milestones', route('milestones.index', $project));
});

// Dashboard > Projects > [Project Name] > Milestones > [Milestone Name]
Breadcrumbs::for('milestones.show', function (BreadcrumbTrail $trail, Project $project, Milestone $milestone) {
  $trail->parent('milestones.index', $project);
  $trail->push($milestone->name, route('milestones.show', [$project, $milestone]));
});

// --- CRM Management ---
// Dashboard > CRM (Generic parent for CRM sections)
Breadcrumbs::for('crm.index', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('CRM');
});

// Dashboard > CRM > Leads
Breadcrumbs::for('leads.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Leads', route('leads.index'));
});

// Dashboard > CRM > Leads > [Lead Name]
Breadcrumbs::for('leads.show', function (BreadcrumbTrail $trail, Lead $lead) {
  $trail->parent('leads.index');
  $trail->push($lead->company_name ?: $lead->first_name . ' ' . $lead->last_name, route('leads.show', $lead));
});

// Dashboard > CRM > Leads > [Lead Name] > Edit
Breadcrumbs::for('leads.edit', function (BreadcrumbTrail $trail, Lead $lead) {
  $trail->parent('leads.show', $lead);
  $trail->push('Edit', route('leads.edit', $lead));
});

// Dashboard > CRM > Contacts
Breadcrumbs::for('contacts.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Contacts', route('contacts.index'));
});

// Dashboard > CRM > Contacts > [Contact Name]
Breadcrumbs::for('contacts.show', function (BreadcrumbTrail $trail, Contact $contact) {
  $trail->parent('contacts.index');
  $trail->push(
    $contact->company_name ?: $contact->first_name . ' ' . $contact->last_name,
    route('contacts.show', $contact)
  );
});

// Dashboard > CRM > Contacts > [Contact Name] > Edit
Breadcrumbs::for('contacts.edit', function (BreadcrumbTrail $trail, Contact $contact) {
  $trail->parent('contacts.show', $contact);
  $trail->push('Edit', route('contacts.edit', $contact));
});

// Dashboard > CRM > Opportunities
Breadcrumbs::for('opportunities.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Opportunities', route('opportunities.index'));
});

// Dashboard > CRM > Opportunities > [Opportunity Name]
Breadcrumbs::for('opportunities.show', function (BreadcrumbTrail $trail, Opportunity $opportunity) {
  $trail->parent('opportunities.index');
  $trail->push($opportunity->name, route('opportunities.show', $opportunity));
});

// Dashboard > CRM > Opportunities > [Opportunity Name] > Edit
Breadcrumbs::for('opportunities.edit', function (BreadcrumbTrail $trail, Opportunity $opportunity) {
  $trail->parent('opportunities.show', $opportunity);
  $trail->push('Edit', route('opportunities.edit', $opportunity));
});

// Dashboard > CRM > Campaigns
Breadcrumbs::for('campaigns.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Campaigns', route('campaigns.index'));
});

// Dashboard > CRM > Campaigns > [Campaign Name]
Breadcrumbs::for('campaigns.show', function (BreadcrumbTrail $trail, Campaign $campaign) {
  $trail->parent('campaigns.index');
  $trail->push($campaign->name, route('campaigns.show', $campaign));
});

// Dashboard > CRM > Campaigns > [Campaign Name] > Edit
Breadcrumbs::for('campaigns.edit', function (BreadcrumbTrail $trail, Campaign $campaign) {
  $trail->parent('campaigns.show', $campaign);
  $trail->push('Edit', route('campaigns.edit', $campaign));
});

// Dashboard > CRM > Support Tickets
Breadcrumbs::for('support-tickets.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Support Tickets', route('support-tickets.index'));
});

// Dashboard > CRM > Support Tickets > [Ticket Subject]
Breadcrumbs::for('support-tickets.show', function (BreadcrumbTrail $trail, SupportTicket $supportTicket) {
  $trail->parent('support-tickets.index');
  $trail->push($supportTicket->subject, route('support-tickets.show', $supportTicket));
});

// Dashboard > CRM > Support Tickets > [Ticket Subject] > Edit
Breadcrumbs::for('support-tickets.edit', function (BreadcrumbTrail $trail, SupportTicket $supportTicket) {
  $trail->parent('support-tickets.show', $supportTicket);
  $trail->push('Edit', route('support-tickets.edit', $supportTicket));
});

// Dashboard > CRM > Interactions
Breadcrumbs::for('interactions.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Interactions', route('interactions.index'));
});

// Dashboard > CRM > Interactions > [Interaction Subject]
Breadcrumbs::for('interactions.show', function (BreadcrumbTrail $trail, Interaction $interaction) {
  $trail->parent('interactions.index');
  $trail->push($interaction->subject, route('interactions.show', $interaction));
});

// Dashboard > CRM > Interactions > [Interaction Subject] > Edit
Breadcrumbs::for('interactions.edit', function (BreadcrumbTrail $trail, Interaction $interaction) {
  $trail->parent('interactions.show', $interaction);
  $trail->push('Edit', route('interactions.edit', $interaction));
});

// Dashboard > CRM > Knowledge Base
Breadcrumbs::for('knowledge-base.index', function (BreadcrumbTrail $trail) {
  $trail->parent('crm.index');
  $trail->push('Knowledge Base', route('knowledge-base.index'));
});

// Dashboard > CRM > Knowledge Base > [Article Title]
Breadcrumbs::for('knowledge-base.show', function (BreadcrumbTrail $trail, KnowledgeBaseArticle $article) {
  $trail->parent('knowledge-base.index');
  $trail->push($article->title, route('knowledge-base.show', $article));
});

// Dashboard > CRM > Knowledge Base > [Article Title] > Edit
Breadcrumbs::for('knowledge-base.edit', function (BreadcrumbTrail $trail, KnowledgeBaseArticle $article) {
  $trail->parent('knowledge-base.show', $article);
  $trail->push('Edit', route('knowledge-base.edit', $article));
});

// --- Payroll Management ---
// Dashboard > Payroll
Breadcrumbs::for('payroll.dashboard', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('Payroll', route('payroll.dashboard'));
});
// Alias for payroll index
Breadcrumbs::for('payroll.index', function (BreadcrumbTrail $trail) {
  $trail->parent('payroll.dashboard');
});

// Dashboard > Payroll > Employee Profiles
Breadcrumbs::for('employee-profiles.index', function (BreadcrumbTrail $trail) {
  $trail->parent('payroll.dashboard');
  $trail->push('Employee Profiles', route('employee-profiles.index'));
});

// Dashboard > Payroll > Employee Profiles > [Employee Profile]
Breadcrumbs::for('employee-profiles.show', function (BreadcrumbTrail $trail, EmployeeProfile $employeeProfile) {
  $trail->parent('employee-profiles.index');
  $trail->push($employeeProfile->user->name, route('employee-profiles.show', $employeeProfile));
});

// Dashboard > Payroll > Time Entries
Breadcrumbs::for('time-entries.index', function (BreadcrumbTrail $trail) {
  $trail->parent('payroll.dashboard');
  $trail->push('Time Entries', route('time-entries.index'));
});

// Dashboard > Payroll > Time Entries > [Time Entry]
Breadcrumbs::for('time-entries.show', function (BreadcrumbTrail $trail, TimeEntry $timeEntry) {
  $trail->parent('time-entries.index');
  $trail->push(
    'Entry for ' . $timeEntry->user->name . ' on ' . $timeEntry->start_datetime->format('Y-m-d'),
    route('time-entries.show', $timeEntry)
  );
});

// Dashboard > Payroll > Payslips
Breadcrumbs::for('payslips.index', function (BreadcrumbTrail $trail) {
  $trail->parent('payroll.dashboard');
  $trail->push('Payslips', route('payslips.index'));
});

// Dashboard > Payroll > Payslips > [Payslip]
Breadcrumbs::for('payslips.show', function (BreadcrumbTrail $trail, Payslip $payslip) {
  $trail->parent('payslips.index');
  $trail->push('Payslip ' . $payslip->payslip_id, route('payslips.show', $payslip));
});

// Dashboard > Payroll > Reports
Breadcrumbs::for('payroll.reports', function (BreadcrumbTrail $trail) {
  $trail->parent('payroll.dashboard');
  $trail->push('Reports', route('payroll.reports'));
});

// Dashboard > Payroll > Settings
Breadcrumbs::for('payroll.settings', function (BreadcrumbTrail $trail) {
  $trail->parent('payroll.dashboard');
  $trail->push('Settings', route('payroll.settings'));
});

// --- Master Settings ---
// Dashboard > Settings (as a generic parent)
Breadcrumbs::for('settings.index', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('Settings');
});

// Dashboard > Settings > Project Statuses
Breadcrumbs::for('project-statuses.index', function (BreadcrumbTrail $trail) {
  $trail->parent('settings.index');
  $trail->push('Project Statuses', route('project-statuses.index'));
});

// Dashboard > Settings > Project Priorities
Breadcrumbs::for('project-priorities.index', function (BreadcrumbTrail $trail) {
  $trail->parent('settings.index');
  $trail->push('Project Priorities', route('project-priorities.index'));
});

// Dashboard > Settings > Task Statuses
Breadcrumbs::for('task-statuses.index', function (BreadcrumbTrail $trail) {
  $trail->parent('settings.index');
  $trail->push('Task Statuses', route('task-statuses.index'));
});

// Dashboard > Settings > Task Priorities
Breadcrumbs::for('task-priorities.index', function (BreadcrumbTrail $trail) {
  $trail->parent('settings.index');
  $trail->push('Task Priorities', route('task-priorities.index'));
});

// --- Other ---
// Dashboard > Activities
Breadcrumbs::for('activities.index', function (BreadcrumbTrail $trail) {
  $trail->parent('dashboard');
  $trail->push('Activity Log', route('activities.index'));
});
