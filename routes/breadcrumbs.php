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
