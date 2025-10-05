<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class SupportTicketController extends Controller
{
  public function index(Request $request)
  {
    Gate::authorize('viewAny', SupportTicket::class);

    $tickets = SupportTicket::with(['contact', 'assignedTo'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->status, function ($query, $status) {
        $query->byStatus($status);
      })
      ->when($request->priority, function ($query, $priority) {
        $query->byPriority($priority);
      })
      ->when($request->type, function ($query, $type) {
        $query->byType($type);
      })
      ->when($request->assigned_to, function ($query, $assignedTo) {
        $query->assignedTo($assignedTo);
      })
      ->when($request->overdue, function ($query) {
        $query->overdue();
      })
      ->when($request->tag, function ($query, $tag) {
        $query->withTag($tag);
      })
      ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $users = User::select(['id', 'name'])->get();
    $contacts = Contact::select(['id', 'first_name', 'last_name', 'company'])->get();

    return Inertia::render('CRM/SupportTickets/Index', [
      'tickets' => $tickets,
      'users' => $users,
      'contacts' => $contacts,
      'filters' => $request->only(['search', 'status', 'priority', 'type', 'assigned_to', 'overdue', 'tag']),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create()
  {
    Gate::authorize('create', SupportTicket::class);

    $users = User::select(['id', 'name'])->get();
    $contacts = Contact::select(['id', 'first_name', 'last_name', 'company'])->get();

    return Inertia::render('CRM/SupportTickets/Create', [
      'users' => $users,
      'contacts' => $contacts,
    ]);
  }

  public function store(Request $request)
  {
    Gate::authorize('create', SupportTicket::class);

    $validated = $request->validate([
      'subject' => 'required|string|max:255',
      'description' => 'required|string',
      'priority' => 'required|in:urgent,high,medium,low',
      'type' => 'required|in:bug,feature_request,question,complaint,other',
      'status' => 'nullable|in:open,in_progress,pending_customer,resolved,closed',
      'contact_id' => 'required|exists:contacts,id',
      'assigned_to' => 'nullable|exists:users,id',
      'tags' => 'nullable|array',
      'custom_fields' => 'nullable|array',
    ]);

    $validated['created_by'] = auth()->id();

    $ticket = SupportTicket::create($validated);

    return redirect()->route('support-tickets.index')->with('success', __('Support ticket created successfully.'));
  }

  public function show(SupportTicket $supportTicket)
  {
    Gate::authorize('view', $supportTicket);

    $supportTicket->load([
      'contact',
      'assignedTo',
      'createdBy',
      'interactions' => function ($query) {
        $query->with('user')->latest('interaction_date');
      },
    ]);

    return Inertia::render('CRM/SupportTickets/Show', [
      'ticket' => $supportTicket,
    ]);
  }

  public function edit(SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $users = User::select(['id', 'name'])->get();
    $contacts = Contact::select(['id', 'first_name', 'last_name', 'company'])->get();

    return Inertia::render('CRM/SupportTickets/Edit', [
      'ticket' => $supportTicket,
      'users' => $users,
      'contacts' => $contacts,
    ]);
  }

  public function update(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'subject' => 'required|string|max:255',
      'description' => 'required|string',
      'priority' => 'required|in:urgent,high,medium,low',
      'type' => 'required|in:bug,feature_request,question,complaint,other',
      'status' => 'nullable|in:open,in_progress,pending_customer,resolved,closed',
      'contact_id' => 'required|exists:contacts,id',
      'assigned_to' => 'nullable|exists:users,id',
      'tags' => 'nullable|array',
      'custom_fields' => 'nullable|array',
      'satisfaction_rating' => 'nullable|integer|min:1|max:5',
      'satisfaction_comment' => 'nullable|string',
    ]);

    $supportTicket->update($validated);

    return redirect()
      ->route('support-tickets.show', $supportTicket)
      ->with('success', __('Support ticket updated successfully.'));
  }

  public function destroy(SupportTicket $supportTicket)
  {
    Gate::authorize('delete', $supportTicket);

    $supportTicket->delete();

    return redirect()->route('support-tickets.index')->with('success', __('Support ticket deleted successfully.'));
  }

  public function assign(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'assigned_to' => 'required|exists:users,id',
    ]);

    $supportTicket->assign($validated['assigned_to']);

    return response()->json([
      'message' => __('Ticket assigned successfully.'),
      'ticket' => $supportTicket->fresh(['assignedTo']),
    ]);
  }

  public function updateStatus(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'status' => 'required|in:open,in_progress,pending_customer,resolved,closed',
      'notes' => 'nullable|string',
    ]);

    $supportTicket->updateStatus($validated['status'], $validated['notes'] ?? null);

    return response()->json([
      'message' => __('Ticket status updated successfully.'),
      'ticket' => $supportTicket->fresh(),
    ]);
  }

  public function resolve(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'notes' => 'nullable|string',
    ]);

    $supportTicket->resolve($validated['notes'] ?? null);

    return response()->json([
      'message' => __('Ticket resolved successfully.'),
      'ticket' => $supportTicket->fresh(),
    ]);
  }

  public function close(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'notes' => 'nullable|string',
    ]);

    $supportTicket->close($validated['notes'] ?? null);

    return response()->json([
      'message' => __('Ticket closed successfully.'),
      'ticket' => $supportTicket->fresh(),
    ]);
  }

  public function reopen(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'notes' => 'nullable|string',
    ]);

    $supportTicket->reopen($validated['notes'] ?? null);

    return response()->json([
      'message' => __('Ticket reopened successfully.'),
      'ticket' => $supportTicket->fresh(),
    ]);
  }

  public function addTag(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'tag' => 'required|string|max:255',
    ]);

    $supportTicket->addTag($validated['tag']);

    return response()->json([
      'message' => __('Tag added successfully.'),
      'tags' => $supportTicket->fresh()->tags,
    ]);
  }

  public function removeTag(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('update', $supportTicket);

    $validated = $request->validate([
      'tag' => 'required|string',
    ]);

    $supportTicket->removeTag($validated['tag']);

    return response()->json([
      'message' => __('Tag removed successfully.'),
      'tags' => $supportTicket->fresh()->tags,
    ]);
  }

  public function rateSatisfaction(Request $request, SupportTicket $supportTicket)
  {
    Gate::authorize('view', $supportTicket);

    $validated = $request->validate([
      'rating' => 'required|integer|min:1|max:5',
      'comment' => 'nullable|string',
    ]);

    $supportTicket->rateSatisfaction($validated['rating'], $validated['comment'] ?? null);

    return response()->json([
      'message' => __('Thank you for your feedback!'),
      'ticket' => $supportTicket->fresh(),
    ]);
  }

  public function dashboard(Request $request)
  {
    Gate::authorize('viewAny', SupportTicket::class);

    $stats = [
      'total_open' => SupportTicket::open()->count(),
      'total_overdue' => SupportTicket::overdue()->count(),
      'total_resolved_today' => SupportTicket::resolved()->whereDate('resolved_at', today())->count(),
      'avg_resolution_time' => SupportTicket::resolved()
        ->whereNotNull('resolved_at')
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
        ->value('avg_hours'),
      'satisfaction_rating' => SupportTicket::whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
    ];

    $recentTickets = SupportTicket::with(['contact', 'assignedTo'])
      ->latest()
      ->limit(10)
      ->get();

    $overdueTickets = SupportTicket::with(['contact', 'assignedTo'])
      ->overdue()
      ->limit(5)
      ->get();

    return Inertia::render('CRM/SupportTickets/Dashboard', [
      'stats' => $stats,
      'recentTickets' => $recentTickets,
      'overdueTickets' => $overdueTickets,
    ]);
  }

  public function bulkAction(Request $request)
  {
    Gate::authorize('viewAny', SupportTicket::class);

    $validated = $request->validate([
      'action' => 'required|in:assign,update_status,update_priority,add_tag,delete',
      'ticket_ids' => 'required|array|min:1',
      'ticket_ids.*' => 'exists:support_tickets,id',
      'assigned_to' => 'nullable|exists:users,id',
      'status' => 'nullable|in:open,in_progress,pending_customer,resolved,closed',
      'priority' => 'nullable|in:urgent,high,medium,low',
      'tag' => 'nullable|string|max:255',
    ]);

    $tickets = SupportTicket::whereIn('id', $validated['ticket_ids'])->get();

    foreach ($tickets as $ticket) {
      Gate::authorize('update', $ticket);
    }

    switch ($validated['action']) {
      case 'assign':
        if (isset($validated['assigned_to'])) {
          SupportTicket::whereIn('id', $validated['ticket_ids'])->update(['assigned_to' => $validated['assigned_to']]);
          $message = __('Tickets assigned successfully.');
        }
        break;

      case 'update_status':
        if (isset($validated['status'])) {
          foreach ($tickets as $ticket) {
            $ticket->updateStatus($validated['status']);
          }
          $message = __('Ticket status updated successfully.');
        }
        break;

      case 'update_priority':
        if (isset($validated['priority'])) {
          SupportTicket::whereIn('id', $validated['ticket_ids'])->update(['priority' => $validated['priority']]);
          $message = __('Ticket priority updated successfully.');
        }
        break;

      case 'add_tag':
        if (isset($validated['tag'])) {
          foreach ($tickets as $ticket) {
            $ticket->addTag($validated['tag']);
          }
          $message = __('Tag added to tickets successfully.');
        }
        break;

      case 'delete':
        SupportTicket::whereIn('id', $validated['ticket_ids'])->delete();
        $message = __('Tickets deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }

  public function export(Request $request)
  {
    Gate::authorize('viewAny', SupportTicket::class);

    // In a full implementation, you would use Laravel Excel or similar
    $tickets = SupportTicket::with(['contact', 'assignedTo'])
      ->when($request->status, function ($query, $status) {
        $query->byStatus($status);
      })
      ->when($request->priority, function ($query, $priority) {
        $query->byPriority($priority);
      })
      ->get();

    return response()->json([
      'message' => __('Export functionality to be implemented.'),
      'count' => $tickets->count(),
    ]);
  }
}
