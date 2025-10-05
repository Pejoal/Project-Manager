<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\SupportTicket;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class InteractionController extends Controller
{
  public function index(Request $request)
  {
    // Gate::authorize('viewAny', Interaction::class);

    $interactions = Interaction::with(['user', 'interactable'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->type, function ($query, $type) {
        $query->byType($type);
      })
      ->when($request->direction, function ($query, $direction) {
        $query->byDirection($direction);
      })
      ->when($request->user_id, function ($query, $userId) {
        $query->byUser($userId);
      })
      ->when($request->date_from, function ($query, $dateFrom) {
        $query->whereDate('interaction_date', '>=', $dateFrom);
      })
      ->when($request->date_to, function ($query, $dateTo) {
        $query->whereDate('interaction_date', '<=', $dateTo);
      })
      ->when($request->interactable_type, function ($query, $type) {
        $query->where('interactable_type', $type);
      })
      ->orderBy($request->sort ?? 'interaction_date', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Interactions/Index', [
      'interactions' => $interactions,
      'users' => $users,
      'filters' => $request->only([
        'search',
        'type',
        'direction',
        'user_id',
        'date_from',
        'date_to',
        'interactable_type',
      ]),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create(Request $request)
  {
    // Gate::authorize('create', Interaction::class);

    $users = User::select(['id', 'name'])->get();

    // Get the related entity if specified
    $related = null;
    if ($request->type && $request->id) {
      $related = match ($request->type) {
        'contact' => Contact::find($request->id),
        'lead' => Lead::find($request->id),
        'opportunity' => Opportunity::find($request->id),
        'support_ticket' => SupportTicket::find($request->id),
        'campaign' => Campaign::find($request->id),
        default => null,
      };
    }

    return Inertia::render('CRM/Interactions/Create', [
      'users' => $users,
      'related' => $related,
      'relatedType' => $request->type,
    ]);
  }

  public function store(Request $request)
  {
    // Gate::authorize('create', Interaction::class);

    $validated = $request->validate([
      'type' => 'required|in:call,email,meeting,note,sms,social_media,webinar,demo,other',
      'direction' => 'required|in:inbound,outbound',
      'subject' => 'required|string|max:255',
      'description' => 'required|string',
      'interaction_date' => 'required|date',
      'duration_minutes' => 'nullable|integer|min:0',
      'outcome' => 'nullable|in:positive,neutral,negative',
      'follow_up_required' => 'nullable|boolean',
      'follow_up_date' => 'nullable|date|after:interaction_date',
      'contact_details' => 'nullable|array',
      'attachments' => 'nullable|array',
      'interactable_type' => 'required|string',
      'interactable_id' => 'required|integer',
    ]);

    $validated['user_id'] = auth()->id();

    // Validate the polymorphic relationship
    $this->validateInteractable($validated['interactable_type'], $validated['interactable_id']);

    $interaction = Interaction::create($validated);

    return redirect()->route('interactions.index')->with('success', __('Interaction recorded successfully.'));
  }

  public function show(Interaction $interaction)
  {
    // Gate::authorize('view', $interaction);

    $interaction->load(['user', 'interactable']);

    return Inertia::render('CRM/Interactions/Show', [
      'interaction' => $interaction,
    ]);
  }

  public function edit(Interaction $interaction)
  {
    // Gate::authorize('update', $interaction);

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Interactions/Edit', [
      'interaction' => $interaction,
      'users' => $users,
    ]);
  }

  public function update(Request $request, Interaction $interaction)
  {
    // Gate::authorize('update', $interaction);

    $validated = $request->validate([
      'type' => 'required|in:call,email,meeting,note,sms,social_media,webinar,demo,other',
      'direction' => 'required|in:inbound,outbound',
      'subject' => 'required|string|max:255',
      'description' => 'required|string',
      'interaction_date' => 'required|date',
      'duration_minutes' => 'nullable|integer|min:0',
      'outcome' => 'nullable|in:positive,neutral,negative',
      'follow_up_required' => 'nullable|boolean',
      'follow_up_date' => 'nullable|date|after:interaction_date',
      'contact_details' => 'nullable|array',
      'attachments' => 'nullable|array',
    ]);

    $interaction->update($validated);

    return redirect()
      ->route('interactions.show', $interaction)
      ->with('success', __('Interaction updated successfully.'));
  }

  public function destroy(Interaction $interaction)
  {
    // Gate::authorize('delete', $interaction);

    $interaction->delete();

    return redirect()->route('interactions.index')->with('success', __('Interaction deleted successfully.'));
  }

  public function markFollowUpComplete(Interaction $interaction)
  {
    // Gate::authorize('update', $interaction);

    $interaction->markFollowUpComplete();

    return response()->json([
      'message' => __('Follow-up marked as complete.'),
      'interaction' => $interaction->fresh(),
    ]);
  }

  public function addToContact(Request $request, Contact $contact)
  {
    // Gate::authorize('create', Interaction::class);

    $validated = $request->validate([
      'type' => 'required|in:call,email,meeting,note,sms,social_media,webinar,demo,other',
      'direction' => 'required|in:inbound,outbound',
      'subject' => 'required|string|max:255',
      'description' => 'required|string',
      'interaction_date' => 'required|date',
      'duration_minutes' => 'nullable|integer|min:0',
      'outcome' => 'nullable|in:positive,neutral,negative',
      'follow_up_required' => 'nullable|boolean',
      'follow_up_date' => 'nullable|date|after:interaction_date',
    ]);

    $validated['user_id'] = auth()->id();
    $validated['interactable_type'] = 'App\\Models\\Contact';
    $validated['interactable_id'] = $contact->id;

    $interaction = Interaction::create($validated);

    return response()->json([
      'message' => __('Interaction added successfully.'),
      'interaction' => $interaction->load('user'),
    ]);
  }

  public function analytics(Request $request)
  {
    // Gate::authorize('viewAny', Interaction::class);

    $dateFrom = $request->date_from ?? now()->subDays(30);
    $dateTo = $request->date_to ?? now();

    $stats = [
      'total_interactions' => Interaction::whereBetween('interaction_date', [$dateFrom, $dateTo])->count(),
      'total_calls' => Interaction::byType('call')
        ->whereBetween('interaction_date', [$dateFrom, $dateTo])
        ->count(),
      'total_emails' => Interaction::byType('email')
        ->whereBetween('interaction_date', [$dateFrom, $dateTo])
        ->count(),
      'total_meetings' => Interaction::byType('meeting')
        ->whereBetween('interaction_date', [$dateFrom, $dateTo])
        ->count(),
      'avg_duration' => Interaction::whereBetween('interaction_date', [$dateFrom, $dateTo])
        ->whereNotNull('duration_minutes')
        ->avg('duration_minutes'),
      'positive_outcomes' => Interaction::where('outcome', 'positive')
        ->whereBetween('interaction_date', [$dateFrom, $dateTo])
        ->count(),
      'pending_follow_ups' => Interaction::pendingFollowUp()->count(),
    ];

    $dailyInteractions = Interaction::selectRaw('DATE(interaction_date) as date, COUNT(*) as count')
      ->whereBetween('interaction_date', [$dateFrom, $dateTo])
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    $interactionsByType = Interaction::selectRaw('type, COUNT(*) as count')
      ->whereBetween('interaction_date', [$dateFrom, $dateTo])
      ->groupBy('type')
      ->get();

    $topUsers = Interaction::selectRaw('user_id, COUNT(*) as count')
      ->with('user:id,name')
      ->whereBetween('interaction_date', [$dateFrom, $dateTo])
      ->groupBy('user_id')
      ->orderBy('count', 'desc')
      ->limit(10)
      ->get();

    return Inertia::render('CRM/Interactions/Analytics', [
      'stats' => $stats,
      'dailyInteractions' => $dailyInteractions,
      'interactionsByType' => $interactionsByType,
      'topUsers' => $topUsers,
      'filters' => $request->only(['date_from', 'date_to']),
    ]);
  }

  public function followUps(Request $request)
  {
    // Gate::authorize('viewAny', Interaction::class);

    $followUps = Interaction::with(['user', 'interactable'])
      ->pendingFollowUp()
      ->when($request->overdue, function ($query) {
        $query->where('follow_up_date', '<', now());
      })
      ->when($request->user_id, function ($query, $userId) {
        $query->byUser($userId);
      })
      ->orderBy('follow_up_date')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Interactions/FollowUps', [
      'followUps' => $followUps,
      'users' => $users,
      'filters' => $request->only(['overdue', 'user_id']),
    ]);
  }

  public function bulkAction(Request $request)
  {
    // Gate::authorize('viewAny', Interaction::class);

    $validated = $request->validate([
      'action' => 'required|in:mark_follow_up_complete,delete',
      'interaction_ids' => 'required|array|min:1',
      'interaction_ids.*' => 'exists:interactions,id',
    ]);

    $interactions = Interaction::whereIn('id', $validated['interaction_ids'])->get();

    foreach ($interactions as $interaction) {
      // Gate::authorize('update', $interaction);
    }

    switch ($validated['action']) {
      case 'mark_follow_up_complete':
        foreach ($interactions as $interaction) {
          $interaction->markFollowUpComplete();
        }
        $message = __('Follow-ups marked as complete.');
        break;

      case 'delete':
        Interaction::whereIn('id', $validated['interaction_ids'])->delete();
        $message = __('Interactions deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }

  private function validateInteractable($type, $id)
  {
    $model = match ($type) {
      'App\\Models\\Contact' => Contact::class,
      'App\\Models\\Lead' => Lead::class,
      'App\\Models\\Opportunity' => Opportunity::class,
      'App\\Models\\SupportTicket' => SupportTicket::class,
      'App\\Models\\Campaign' => Campaign::class,
      default => null,
    };

    if (!$model || !$model::find($id)) {
      abort(422, __('Invalid related entity.'));
    }
  }
}
