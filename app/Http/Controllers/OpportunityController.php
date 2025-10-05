<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class OpportunityController extends Controller
{
  public function index(Request $request)
  {
    // Gate::authorize('viewAny', Opportunity::class);

    $opportunities = Opportunity::with(['contact', 'assignedTo'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->stage, function ($query, $stage) {
        $query->byStage($stage);
      })
      ->when($request->assigned_to, function ($query, $assignedTo) {
        $query->assignedTo($assignedTo);
      })
      ->when($request->type, function ($query, $type) {
        $query->where('type', $type);
      })
      ->when($request->probability_min, function ($query, $probMin) {
        $query->where('probability', '>=', $probMin);
      })
      ->when($request->value_min, function ($query, $valueMin) {
        $query->where('value', '>=', $valueMin);
      })
      ->when($request->closing_soon, function ($query) {
        $query->closingSoon(30);
      })
      ->when($request->overdue, function ($query) {
        $query->overdue();
      })
      ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $users = User::select(['id', 'name'])->get();
    $contacts = Contact::select(['id', 'first_name', 'last_name', 'company'])->get();

    return Inertia::render('CRM/Opportunities/Index', [
      'opportunities' => $opportunities,
      'users' => $users,
      'contacts' => $contacts,
      'filters' => $request->only([
        'search',
        'stage',
        'assigned_to',
        'type',
        'probability_min',
        'value_min',
        'closing_soon',
        'overdue',
      ]),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create()
  {
    // Gate::authorize('create', Opportunity::class);

    $users = User::select(['id', 'name'])->get();
    $contacts = Contact::select(['id', 'first_name', 'last_name', 'company'])->get();

    return Inertia::render('CRM/Opportunities/Create', [
      'users' => $users,
      'contacts' => $contacts,
    ]);
  }

  public function store(Request $request)
  {
    // Gate::authorize('create', Opportunity::class);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'value' => 'required|numeric|min:0',
      'probability' => 'nullable|integer|min:0|max:100',
      'stage' => 'nullable|in:prospecting,qualification,needs_analysis,proposal,negotiation,closed_won,closed_lost',
      'expected_close_date' => 'required|date|after:today',
      'type' => 'nullable|in:new_business,existing_business,renewal',
      'lead_source' => 'nullable|string|max:255',
      'next_steps' => 'nullable|string',
      'competitors' => 'nullable|array',
      'contact_id' => 'required|exists:contacts,id',
      'assigned_to' => 'nullable|exists:users,id',
    ]);

    $validated['created_by'] = auth()->id();

    $opportunity = Opportunity::create($validated);

    return redirect()->route('opportunities.index')->with('success', __('Opportunity created successfully.'));
  }

  public function show(Opportunity $opportunity)
  {
    // Gate::authorize('view', $opportunity);

    $opportunity->load([
      'contact',
      'assignedTo',
      'createdBy',
      'interactions' => function ($query) {
        $query->with('user')->latest('interaction_date');
      },
    ]);

    return Inertia::render('CRM/Opportunities/Show', [
      'opportunity' => $opportunity,
    ]);
  }

  public function edit(Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $users = User::select(['id', 'name'])->get();
    $contacts = Contact::select(['id', 'first_name', 'last_name', 'company'])->get();

    return Inertia::render('CRM/Opportunities/Edit', [
      'opportunity' => $opportunity,
      'users' => $users,
      'contacts' => $contacts,
    ]);
  }

  public function update(Request $request, Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'value' => 'required|numeric|min:0',
      'probability' => 'nullable|integer|min:0|max:100',
      'stage' => 'nullable|in:prospecting,qualification,needs_analysis,proposal,negotiation,closed_won,closed_lost',
      'expected_close_date' => 'required|date',
      'type' => 'nullable|in:new_business,existing_business,renewal',
      'lead_source' => 'nullable|string|max:255',
      'next_steps' => 'nullable|string',
      'competitors' => 'nullable|array',
      'loss_reason' => 'nullable|string',
      'contact_id' => 'required|exists:contacts,id',
      'assigned_to' => 'nullable|exists:users,id',
    ]);

    $opportunity->update($validated);

    return redirect()
      ->route('opportunities.show', $opportunity)
      ->with('success', __('Opportunity updated successfully.'));
  }

  public function destroy(Opportunity $opportunity)
  {
    // Gate::authorize('delete', $opportunity);

    $opportunity->delete();

    return redirect()->route('opportunities.index')->with('success', __('Opportunity deleted successfully.'));
  }

  public function updateStage(Request $request, Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $validated = $request->validate([
      'stage' => 'required|in:prospecting,qualification,needs_analysis,proposal,negotiation,closed_won,closed_lost',
      'probability' => 'nullable|integer|min:0|max:100',
      'loss_reason' => 'nullable|string',
      'notes' => 'nullable|string',
    ]);

    $opportunity->updateStage($validated['stage'], $validated);

    return response()->json([
      'message' => __('Opportunity stage updated successfully.'),
      'opportunity' => $opportunity->fresh(),
    ]);
  }

  public function win(Request $request, Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $validated = $request->validate([
      'notes' => 'nullable|string',
    ]);

    $opportunity->win($validated);

    return response()->json([
      'message' => __('Opportunity marked as won!'),
      'opportunity' => $opportunity->fresh(),
    ]);
  }

  public function lose(Request $request, Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $validated = $request->validate([
      'loss_reason' => 'required|string',
      'notes' => 'nullable|string',
    ]);

    $opportunity->lose($validated['loss_reason'], $validated);

    return response()->json([
      'message' => __('Opportunity marked as lost.'),
      'opportunity' => $opportunity->fresh(),
    ]);
  }

  public function addCompetitor(Request $request, Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $validated = $request->validate([
      'competitor' => 'required|string|max:255',
    ]);

    $opportunity->addCompetitor($validated['competitor']);

    return response()->json([
      'message' => __('Competitor added successfully.'),
      'competitors' => $opportunity->fresh()->competitors,
    ]);
  }

  public function removeCompetitor(Request $request, Opportunity $opportunity)
  {
    // Gate::authorize('update', $opportunity);

    $validated = $request->validate([
      'competitor' => 'required|string',
    ]);

    $opportunity->removeCompetitor($validated['competitor']);

    return response()->json([
      'message' => __('Competitor removed successfully.'),
      'competitors' => $opportunity->fresh()->competitors,
    ]);
  }

  public function pipeline(Request $request)
  {
    // Gate::authorize('viewAny', Opportunity::class);

    $opportunities = Opportunity::with(['contact', 'assignedTo'])
      ->open()
      ->when($request->assigned_to, function ($query, $assignedTo) {
        $query->assignedTo($assignedTo);
      })
      ->orderBy('stage')
      ->orderBy('probability', 'desc')
      ->get()
      ->groupBy('stage');

    $stats = [
      'total_open' => Opportunity::open()->count(),
      'total_value' => Opportunity::open()->sum('value'),
      'weighted_value' => Opportunity::open()->get()->sum('weighted_value'),
      'avg_probability' => Opportunity::open()->avg('probability'),
      'closing_this_month' => Opportunity::closingSoon(30)->count(),
      'overdue' => Opportunity::overdue()->count(),
    ];

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Opportunities/Pipeline', [
      'opportunitiesByStage' => $opportunities,
      'stats' => $stats,
      'users' => $users,
      'filters' => $request->only(['assigned_to']),
    ]);
  }

  public function bulkAction(Request $request)
  {
    // Gate::authorize('viewAny', Opportunity::class);

    $validated = $request->validate([
      'action' => 'required|in:assign,update_stage,delete',
      'opportunity_ids' => 'required|array|min:1',
      'opportunity_ids.*' => 'exists:opportunities,id',
      'assigned_to' => 'nullable|exists:users,id',
      'stage' => 'nullable|in:prospecting,qualification,needs_analysis,proposal,negotiation,closed_won,closed_lost',
    ]);

    $opportunities = Opportunity::whereIn('id', $validated['opportunity_ids'])->get();

    foreach ($opportunities as $opportunity) {
      // Gate::authorize('update', $opportunity);
    }

    switch ($validated['action']) {
      case 'assign':
        if (isset($validated['assigned_to'])) {
          Opportunity::whereIn('id', $validated['opportunity_ids'])->update([
            'assigned_to' => $validated['assigned_to'],
          ]);
          $message = __('Opportunities assigned successfully.');
        }
        break;

      case 'update_stage':
        if (isset($validated['stage'])) {
          foreach ($opportunities as $opportunity) {
            $opportunity->updateStage($validated['stage']);
          }
          $message = __('Opportunity stages updated successfully.');
        }
        break;

      case 'delete':
        Opportunity::whereIn('id', $validated['opportunity_ids'])->delete();
        $message = __('Opportunities deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }

  public function export(Request $request)
  {
    // Gate::authorize('viewAny', Opportunity::class);

    // In a full implementation, you would use Laravel Excel or similar
    $opportunities = Opportunity::with(['contact', 'assignedTo'])
      ->when($request->stage, function ($query, $stage) {
        $query->byStage($stage);
      })
      ->when($request->assigned_to, function ($query, $assignedTo) {
        $query->assignedTo($assignedTo);
      })
      ->get();

    return response()->json([
      'message' => __('Export functionality to be implemented.'),
      'count' => $opportunities->count(),
    ]);
  }
}
