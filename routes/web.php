<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPriorityController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskPriorityController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\KnowledgeBaseArticleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Route::get('/broadcast-test', function () {
//   event(new MessageSent('Hello from Laravel Reverb!'));
//   return 'Message broadcasted!';
// });

Route::group(
  [
    'prefix' => LaravelLocalization::setLocale(),
    'middleware',
    ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
  ],
  function () {
    $locales = collect(LaravelLocalization::getSupportedLocales())->map(function ($properties, $localeCode) {
      return [
        'code' => $localeCode,
        'native' => $properties['native'],
        'url' => LaravelLocalization::getLocalizedURL($localeCode, null, [], true),
        'flag' => $properties['flag'],
      ];
    });
    Inertia::share([
      'locales' => $locales,
      'active_locale_code' => LaravelLocalization::getCurrentLocale(),
    ]);

    Route::get('/', function () {
      return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
      ]);
    })->name('welcome');

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
      Route::put('/api/settings', [SettingsController::class, 'updateSettings']);

      Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
      Route::get('/activities', [ActivityLogController::class, 'index'])->name('activities.index');

      // Export Route
      Route::post('/export', [ExportController::class, 'export'])->name('export.data');

      Route::get('/maps', function () {
        return Inertia::render('Maps/Index', []);
      })->name('maps.index');

      // Client Management Routes
      Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
      });

      Route::put('phases/sync', [PhaseController::class, 'sync'])->name('phases.sync');

      // Project Management Routes
      Route::prefix('projects')->group(function () {
        Route::get('/getData', [ProjectController::class, 'getData'])->name('projects.data');
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/single/{project:slug}', [ProjectController::class, 'single'])->name('projects.single');
        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/tasks', [TaskController::class, 'all'])->name('tasks.all');
        Route::get('/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/{project:slug}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/{project:slug}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/{project:slug}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Phase Management Routes
        Route::prefix('{project:slug}/phases')->group(function () {
          Route::get('/', [PhaseController::class, 'index'])->name('phases.index');
          Route::post('/', [PhaseController::class, 'store'])->name('phases.store');
          Route::get('/{phase}', [PhaseController::class, 'show'])->name('phases.show');
          Route::get('/{phase}/edit', [PhaseController::class, 'edit'])->name('phases.edit');
          Route::put('/{phase}', [PhaseController::class, 'update'])->name('phases.update');
          Route::delete('/{phase}', [PhaseController::class, 'destroy'])->name('phases.destroy');
        });

        // Milestone Management Routes
        Route::prefix('{project:slug}/milestones')->group(function () {
          Route::get('/', [MilestoneController::class, 'index'])->name('milestones.index');
          Route::post('/', [MilestoneController::class, 'store'])->name('milestones.store');
          Route::get('/{milestone}', [MilestoneController::class, 'show'])->name('milestones.show');
          Route::get('/{milestone}/edit', [MilestoneController::class, 'edit'])->name('milestones.edit');
          Route::put('/{milestone}', [MilestoneController::class, 'update'])->name('milestones.update');
          Route::delete('/{milestone}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');
        });

        // Task Management Routes
        Route::prefix('{project:slug}/tasks')->group(function () {
          // Task Attachment Routes
          Route::post('/{task}/attachments', [TaskAttachmentController::class, 'store'])->name(
            'task-attachments.store'
          );
          Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
          Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
          Route::get('/{task}', [TaskController::class, 'show'])->name('tasks.show');
          Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
          Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
          Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        });
      });

      // Task Attachment Routes (outside project scope for global access)
      Route::get('/task-attachments/{attachment}/download', [TaskAttachmentController::class, 'download'])->name(
        'task-attachments.download'
      );
      Route::put('/task-attachments/{attachment}', [TaskAttachmentController::class, 'update'])->name(
        'task-attachments.update'
      );
      Route::delete('/task-attachments/{attachment}', [TaskAttachmentController::class, 'destroy'])->name(
        'task-attachments.destroy'
      );

      // Project Status Management Routes
      Route::prefix('project-statuses')->group(function () {
        Route::get('/', [ProjectStatusController::class, 'index'])->name('project-statuses.index');
        Route::post('/', [ProjectStatusController::class, 'store'])->name('project-statuses.store');
        Route::put('/{projectStatus}', [ProjectStatusController::class, 'update'])->name('project-statuses.update');
        Route::delete('/{projectStatus}', [ProjectStatusController::class, 'destroy'])->name(
          'project-statuses.destroy'
        );
      });

      // Payroll Management Routes (Admin Only)
      Route::middleware('role:admin')->group(function () {
        // Payroll Dashboard
        Route::prefix('payroll')->group(function () {
          Route::get('/', [PayrollController::class, 'index'])->name('payroll.index');
          Route::get('/dashboard', [PayrollController::class, 'dashboard'])->name('payroll.dashboard');
          Route::get('/reports', [PayrollController::class, 'reports'])->name('payroll.reports');
          Route::get('/reports/export', [PayrollController::class, 'exportReports'])->name('payroll.reports.export');
          Route::get('/settings', [PayrollController::class, 'settings'])->name('payroll.settings');
          Route::put('/settings', [PayrollController::class, 'updateSettings'])->name('payroll.settings.update');
          Route::post('/generate-time-entries', [PayrollController::class, 'generateTimeEntries'])->name(
            'payroll.generate-time-entries'
          );
        });

        // Employee Profiles Management
        Route::resource('employee-profiles', EmployeeProfileController::class);
        Route::patch('employee-profiles/{employeeProfile}/activate', [
          EmployeeProfileController::class,
          'activate',
        ])->name('employee-profiles.activate');
        Route::patch('employee-profiles/{employeeProfile}/deactivate', [
          EmployeeProfileController::class,
          'deactivate',
        ])->name('employee-profiles.deactivate');
        Route::post('employee-profiles/bulk-update', [EmployeeProfileController::class, 'bulkUpdate'])->name(
          'employee-profiles.bulk-update'
        );

        // Payslips Management
        Route::get('payslips/generate-bulk', [PayslipController::class, 'generateBulk'])->name(
          'payslips.generate-bulk'
        );
        Route::resource('payslips', PayslipController::class)->except(['create', 'edit', 'update']);
        Route::post('payslips/generate', [PayslipController::class, 'generate'])->name('payslips.generate');
        Route::patch('payslips/{payslip}/approve', [PayslipController::class, 'approve'])->name('payslips.approve');
        Route::patch('payslips/{payslip}/mark-paid', [PayslipController::class, 'markAsPaid'])->name(
          'payslips.mark-paid'
        );
        Route::post('payslips/bulk-approve', [PayslipController::class, 'bulkApprove'])->name('payslips.bulk-approve');
        Route::post('payslips/bulk-update', [PayslipController::class, 'bulkUpdate'])->name('payslips.bulk-update');
        Route::get('payslips/{payslip}/pdf', [PayslipController::class, 'downloadPdf'])->name('payslips.download-pdf');
      });

      // Time Entries Management (Users can manage their own, Admins can manage all)
      Route::get('time-entries', [TimeEntryController::class, 'index'])->name('time-entries.index');
      Route::post('time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
      Route::put('time-entries/{timeEntry}', [TimeEntryController::class, 'update'])->name('time-entries.update');
      Route::delete('time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');
      Route::patch('time-entries/{timeEntry}/approve', [TimeEntryController::class, 'approve'])->name(
        'time-entries.approve'
      );
      Route::post('time-entries/bulk-approve', [TimeEntryController::class, 'bulkApprove'])->name(
        'time-entries.bulk-approve'
      );
      Route::post('time-entries/bulk-update', [TimeEntryController::class, 'bulkUpdate'])->name(
        'time-entries.bulk-update'
      );
      Route::get('time-entries/{timeEntry}', [TimeEntryController::class, 'show'])->name('time-entries.show');

      // Task bulk operations
      Route::post('tasks/bulk-update', [TaskController::class, 'bulkUpdate'])->name('tasks.bulk-update');

      // Add this route, preferably near your other time entry routes
      Route::get('/project/{project}/tasks', [TimeEntryController::class, 'getTasksForProject'])->name(
        'time-entries.tasks-for-project'
      );

      // Project Priority Management Routes
      Route::prefix('project-priorities')->group(function () {
        Route::get('/', [ProjectPriorityController::class, 'index'])->name('project-priorities.index');
        Route::post('/', [ProjectPriorityController::class, 'store'])->name('project-priorities.store');
        Route::put('/{projectPriority}', [ProjectPriorityController::class, 'update'])->name(
          'project-priorities.update'
        );
        Route::delete('/{projectPriority}', [ProjectPriorityController::class, 'destroy'])->name(
          'project-priorities.destroy'
        );
      });

      // Task Status Management Routes
      Route::prefix('task-statuses')->group(function () {
        Route::get('/', [TaskStatusController::class, 'index'])->name('task-statuses.index');
        Route::post('/', [TaskStatusController::class, 'store'])->name('task-statuses.store');
        Route::put('/{taskStatus}', [TaskStatusController::class, 'update'])->name('task-statuses.update');
        Route::delete('/{taskStatus}', [TaskStatusController::class, 'destroy'])->name('task-statuses.destroy');
      });

      // Task Priority Management Routes
      Route::prefix('task-priorities')->group(function () {
        Route::get('/', [TaskPriorityController::class, 'index'])->name('task-priorities.index');
        Route::post('/', [TaskPriorityController::class, 'store'])->name('task-priorities.store');
        Route::put('/{taskPriority}', [TaskPriorityController::class, 'update'])->name('task-priorities.update');
        Route::delete('/{taskPriority}', [TaskPriorityController::class, 'destroy'])->name('task-priorities.destroy');
      });

      // CRM Routes
      Route::prefix('crm')->group(function () {
        // Lead Management Routes
        Route::prefix('leads')->group(function () {
          Route::get('/', [LeadController::class, 'index'])->name('leads.index');
          Route::get('/create', [LeadController::class, 'create'])->name('leads.create');
          Route::post('/', [LeadController::class, 'store'])->name('leads.store');
          Route::get('/{lead}', [LeadController::class, 'show'])->name('leads.show');
          Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
          Route::put('/{lead}', [LeadController::class, 'update'])->name('leads.update');
          Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
          Route::post('/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
          Route::patch('/{lead}/update-score', [LeadController::class, 'updateScore'])->name('leads.update-score');
          Route::post('/bulk-action', [LeadController::class, 'bulkAction'])->name('leads.bulk-action');
          Route::get('/export', [LeadController::class, 'export'])->name('leads.export');
        });

        // Contact Management Routes
        Route::prefix('contacts')->group(function () {
          Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
          Route::get('/create', [ContactController::class, 'create'])->name('contacts.create');
          Route::post('/', [ContactController::class, 'store'])->name('contacts.store');
          Route::get('/{contact}', [ContactController::class, 'show'])->name('contacts.show');
          Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
          Route::put('/{contact}', [ContactController::class, 'update'])->name('contacts.update');
          Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        });

        // Opportunity Management Routes
        Route::prefix('opportunities')->group(function () {
          Route::get('/', [OpportunityController::class, 'index'])->name('opportunities.index');
          Route::get('/create', [OpportunityController::class, 'create'])->name('opportunities.create');
          Route::post('/', [OpportunityController::class, 'store'])->name('opportunities.store');
          Route::get('/pipeline', [OpportunityController::class, 'pipeline'])->name('opportunities.pipeline');
          Route::get('/{opportunity}', [OpportunityController::class, 'show'])->name('opportunities.show');
          Route::get('/{opportunity}/edit', [OpportunityController::class, 'edit'])->name('opportunities.edit');
          Route::put('/{opportunity}', [OpportunityController::class, 'update'])->name('opportunities.update');
          Route::delete('/{opportunity}', [OpportunityController::class, 'destroy'])->name('opportunities.destroy');
          Route::patch('/{opportunity}/update-stage', [OpportunityController::class, 'updateStage'])->name(
            'opportunities.update-stage'
          );
          Route::patch('/{opportunity}/win', [OpportunityController::class, 'win'])->name('opportunities.win');
          Route::patch('/{opportunity}/lose', [OpportunityController::class, 'lose'])->name('opportunities.lose');
          Route::post('/{opportunity}/add-competitor', [OpportunityController::class, 'addCompetitor'])->name(
            'opportunities.add-competitor'
          );
          Route::delete('/{opportunity}/remove-competitor', [OpportunityController::class, 'removeCompetitor'])->name(
            'opportunities.remove-competitor'
          );
          Route::post('/bulk-action', [OpportunityController::class, 'bulkAction'])->name('opportunities.bulk-action');
          Route::get('/export', [OpportunityController::class, 'export'])->name('opportunities.export');
        });

        // Campaign Management Routes
        Route::prefix('campaigns')->group(function () {
          Route::get('/', [CampaignController::class, 'index'])->name('campaigns.index');
          Route::get('/create', [CampaignController::class, 'create'])->name('campaigns.create');
          Route::post('/', [CampaignController::class, 'store'])->name('campaigns.store');
          Route::get('/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
          Route::get('/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
          Route::put('/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
          Route::delete('/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
          Route::patch('/{campaign}/launch', [CampaignController::class, 'launch'])->name('campaigns.launch');
          Route::patch('/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
          Route::patch('/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');
          Route::patch('/{campaign}/complete', [CampaignController::class, 'complete'])->name('campaigns.complete');
          Route::post('/{campaign}/add-contacts', [CampaignController::class, 'addContacts'])->name(
            'campaigns.add-contacts'
          );
          Route::delete('/{campaign}/remove-contact', [CampaignController::class, 'removeContact'])->name(
            'campaigns.remove-contact'
          );
          Route::post('/{campaign}/track-engagement', [CampaignController::class, 'trackEngagement'])->name(
            'campaigns.track-engagement'
          );
          Route::get('/{campaign}/analytics', [CampaignController::class, 'analytics'])->name('campaigns.analytics');
          Route::post('/bulk-action', [CampaignController::class, 'bulkAction'])->name('campaigns.bulk-action');
          Route::get('/export', [CampaignController::class, 'export'])->name('campaigns.export');
        });

        // Support Ticket Management Routes
        Route::prefix('support-tickets')->group(function () {
          Route::get('/', [SupportTicketController::class, 'index'])->name('support-tickets.index');
          Route::get('/create', [SupportTicketController::class, 'create'])->name('support-tickets.create');
          Route::post('/', [SupportTicketController::class, 'store'])->name('support-tickets.store');
          Route::get('/dashboard', [SupportTicketController::class, 'dashboard'])->name('support-tickets.dashboard');
          Route::get('/{supportTicket}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
          Route::get('/{supportTicket}/edit', [SupportTicketController::class, 'edit'])->name('support-tickets.edit');
          Route::put('/{supportTicket}', [SupportTicketController::class, 'update'])->name('support-tickets.update');
          Route::delete('/{supportTicket}', [SupportTicketController::class, 'destroy'])->name(
            'support-tickets.destroy'
          );
          Route::patch('/{supportTicket}/assign', [SupportTicketController::class, 'assign'])->name(
            'support-tickets.assign'
          );
          Route::patch('/{supportTicket}/update-status', [SupportTicketController::class, 'updateStatus'])->name(
            'support-tickets.update-status'
          );
          Route::patch('/{supportTicket}/resolve', [SupportTicketController::class, 'resolve'])->name(
            'support-tickets.resolve'
          );
          Route::patch('/{supportTicket}/close', [SupportTicketController::class, 'close'])->name(
            'support-tickets.close'
          );
          Route::patch('/{supportTicket}/reopen', [SupportTicketController::class, 'reopen'])->name(
            'support-tickets.reopen'
          );
          Route::post('/{supportTicket}/add-tag', [SupportTicketController::class, 'addTag'])->name(
            'support-tickets.add-tag'
          );
          Route::delete('/{supportTicket}/remove-tag', [SupportTicketController::class, 'removeTag'])->name(
            'support-tickets.remove-tag'
          );
          Route::post('/{supportTicket}/rate-satisfaction', [SupportTicketController::class, 'rateSatisfaction'])->name(
            'support-tickets.rate-satisfaction'
          );
          Route::post('/bulk-action', [SupportTicketController::class, 'bulkAction'])->name(
            'support-tickets.bulk-action'
          );
          Route::get('/export', [SupportTicketController::class, 'export'])->name('support-tickets.export');
        });

        // Interaction Management Routes
        Route::prefix('interactions')->group(function () {
          Route::get('/', [InteractionController::class, 'index'])->name('interactions.index');
          Route::get('/create', [InteractionController::class, 'create'])->name('interactions.create');
          Route::post('/', [InteractionController::class, 'store'])->name('interactions.store');
          Route::get('/analytics', [InteractionController::class, 'analytics'])->name('interactions.analytics');
          Route::get('/follow-ups', [InteractionController::class, 'followUps'])->name('interactions.follow-ups');
          Route::get('/{interaction}', [InteractionController::class, 'show'])->name('interactions.show');
          Route::get('/{interaction}/edit', [InteractionController::class, 'edit'])->name('interactions.edit');
          Route::put('/{interaction}', [InteractionController::class, 'update'])->name('interactions.update');
          Route::delete('/{interaction}', [InteractionController::class, 'destroy'])->name('interactions.destroy');
          Route::patch('/{interaction}/mark-follow-up-complete', [
            InteractionController::class,
            'markFollowUpComplete',
          ])->name('interactions.mark-follow-up-complete');
          Route::post('/contacts/{contact}/add', [InteractionController::class, 'addToContact'])->name(
            'interactions.add-to-contact'
          );
          Route::post('/bulk-action', [InteractionController::class, 'bulkAction'])->name('interactions.bulk-action');
        });
        
        // Knowledge Base Management Routes
        Route::prefix('knowledge-base')->group(function () {
          Route::get('/', [KnowledgeBaseArticleController::class, 'index'])->name('knowledge-base.index');
          Route::get('/create', [KnowledgeBaseArticleController::class, 'create'])->name('knowledge-base.create');
          Route::post('/', [KnowledgeBaseArticleController::class, 'store'])->name('knowledge-base.store');
          Route::get('/categories', [KnowledgeBaseArticleController::class, 'categories'])->name(
            'knowledge-base.categories'
          );
          Route::get('/popular', [KnowledgeBaseArticleController::class, 'popular'])->name('knowledge-base.popular');
          Route::get('/search', [KnowledgeBaseArticleController::class, 'search'])->name('knowledge-base.search');
          Route::get('/analytics', [KnowledgeBaseArticleController::class, 'analytics'])->name(
            'knowledge-base.analytics'
          );
          Route::post('/bulk-action', [KnowledgeBaseArticleController::class, 'bulkAction'])->name(
            'knowledge-base.bulk-action'
          );
          Route::get('/{knowledgeBaseArticle}', [KnowledgeBaseArticleController::class, 'show'])->name(
            'knowledge-base.show'
          );
          Route::get('/{knowledgeBaseArticle}/edit', [KnowledgeBaseArticleController::class, 'edit'])->name(
            'knowledge-base.edit'
          );
          Route::put('/{knowledgeBaseArticle}', [KnowledgeBaseArticleController::class, 'update'])->name(
            'knowledge-base.update'
          );
          Route::delete('/{knowledgeBaseArticle}', [KnowledgeBaseArticleController::class, 'destroy'])->name(
            'knowledge-base.destroy'
          );
          Route::patch('/{knowledgeBaseArticle}/publish', [KnowledgeBaseArticleController::class, 'publish'])->name(
            'knowledge-base.publish'
          );
          Route::patch('/{knowledgeBaseArticle}/unpublish', [KnowledgeBaseArticleController::class, 'unpublish'])->name(
            'knowledge-base.unpublish'
          );
          Route::patch('/{knowledgeBaseArticle}/archive', [KnowledgeBaseArticleController::class, 'archive'])->name(
            'knowledge-base.archive'
          );
          Route::patch('/{knowledgeBaseArticle}/feature', [KnowledgeBaseArticleController::class, 'feature'])->name(
            'knowledge-base.feature'
          );
          Route::patch('/{knowledgeBaseArticle}/unfeature', [KnowledgeBaseArticleController::class, 'unfeature'])->name(
            'knowledge-base.unfeature'
          );
          Route::post('/{knowledgeBaseArticle}/add-tag', [KnowledgeBaseArticleController::class, 'addTag'])->name(
            'knowledge-base.add-tag'
          );
          Route::delete('/{knowledgeBaseArticle}/remove-tag', [
            KnowledgeBaseArticleController::class,
            'removeTag',
          ])->name('knowledge-base.remove-tag');
        });
      });
    });
  }
);

Route::fallback(function () {
  return to_route('welcome');
});
