<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class LeadController extends Controller
{
  public function index(Request $request)
  {
    Gate::authorize('viewAny', Lead::class);

    $leads = Lead::with(['assignedTo'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->when($request->source, function ($query, $source) {
        $query->bySource($source);
      })
      ->when($request->assigned_to, function ($query, $assignedTo) {
        $query->assignedTo($assignedTo);
      })
      ->when($request->score_min, function ($query, $scoreMin) {
        $query->where('score', '>=', $scoreMin);
      })
      ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Leads/Index', [
      'leads' => $leads,
      'users' => $users,
      'filters' => $request->only(['search', 'status', 'source', 'assigned_to', 'score_min']),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create()
  {
    Gate::authorize('create', Lead::class);

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Leads/Create', [
      'users' => $users,
    ]);
  }

  public function store(Request $request)
  {
    Gate::authorize('create', Lead::class);

    $validated = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|email|unique:leads,email',
      'phone' => 'nullable|string|max:255',
      'company' => 'nullable|string|max:255',
      'job_title' => 'nullable|string|max:255',
      'status' => 'nullable|in:new,contacted,qualified,unqualified,converted,lost',
      'source' => 'nullable|in:website,referral,social_media,email_campaign,phone_call,trade_show,advertisement,other',
      'estimated_value' => 'nullable|numeric|min:0',
      'notes' => 'nullable|string',
      'custom_fields' => 'nullable|array',
      'assigned_to' => 'nullable|exists:users,id',
    ]);

    $lead = Lead::create($validated);

    // Auto-calculate initial lead score
    $lead->updateScore();

    return redirect()->route('leads.index')->with('success', __('Lead created successfully.'));
  }

  public function show(Lead $lead)
  {
    Gate::authorize('view', $lead);

    $lead->load([
      'assignedTo',
      'convertedContact',
      'interactions' => function ($query) {
        $query->with('user')->latest('interaction_date');
      },
    ]);

    return Inertia::render('CRM/Leads/Show', [
      'lead' => $lead,
    ]);
  }

  public function edit(Lead $lead)
  {
    Gate::authorize('update', $lead);

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Leads/Edit', [
      'lead' => $lead,
      'users' => $users,
    ]);
  }

  public function update(Request $request, Lead $lead)
  {
    Gate::authorize('update', $lead);

    $validated = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|email|unique:leads,email,' . $lead->id,
      'phone' => 'nullable|string|max:255',
      'company' => 'nullable|string|max:255',
      'job_title' => 'nullable|string|max:255',
      'status' => 'nullable|in:new,contacted,qualified,unqualified,converted,lost',
      'source' => 'nullable|in:website,referral,social_media,email_campaign,phone_call,trade_show,advertisement,other',
      'estimated_value' => 'nullable|numeric|min:0',
      'notes' => 'nullable|string',
      'custom_fields' => 'nullable|array',
      'assigned_to' => 'nullable|exists:users,id',
    ]);

    $lead->update($validated);

    return redirect()->route('leads.show', $lead)->with('success', __('Lead updated successfully.'));
  }

  public function destroy(Lead $lead)
  {
    Gate::authorize('delete', $lead);

    $lead->delete();

    return redirect()->route('leads.index')->with('success', __('Lead deleted successfully.'));
  }

  public function convert(Request $request, Lead $lead)
  {
    Gate::authorize('update', $lead);

    if ($lead->status === 'converted') {
      return back()->with('error', __('Lead has already been converted.'));
    }

    $validated = $request->validate([
      'address' => 'nullable|string',
      'city' => 'nullable|string|max:255',
      'state' => 'nullable|string|max:255',
      'country' => 'nullable|string|max:255',
      'postal_code' => 'nullable|string|max:255',
      'website' => 'nullable|url',
      'linkedin' => 'nullable|url',
      'twitter' => 'nullable|url',
      'department' => 'nullable|string|max:255',
      'mobile' => 'nullable|string|max:255',
      'birthday' => 'nullable|date',
    ]);

    $contact = $lead->convert($validated);

    return redirect()->route('contacts.show', $contact)->with('success', __('Lead converted to contact successfully.'));
  }

  public function updateScore(Lead $lead)
  {
    Gate::authorize('update', $lead);

    $newScore = $lead->updateScore();

    return response()->json([
      'message' => __('Lead score updated successfully.'),
      'score' => $newScore,
    ]);
  }

  public function bulkAction(Request $request)
  {
    Gate::authorize('viewAny', Lead::class);

    $validated = $request->validate([
      'action' => 'required|in:assign,update_status,delete',
      'lead_ids' => 'required|array|min:1',
      'lead_ids.*' => 'exists:leads,id',
      'assigned_to' => 'nullable|exists:users,id',
      'status' => 'nullable|in:new,contacted,qualified,unqualified,converted,lost',
    ]);

    $leads = Lead::whereIn('id', $validated['lead_ids'])->get();

    foreach ($leads as $lead) {
      Gate::authorize('update', $lead);
    }

    switch ($validated['action']) {
      case 'assign':
        if (isset($validated['assigned_to'])) {
          Lead::whereIn('id', $validated['lead_ids'])->update(['assigned_to' => $validated['assigned_to']]);
          $message = __('Leads assigned successfully.');
        }
        break;

      case 'update_status':
        if (isset($validated['status'])) {
          Lead::whereIn('id', $validated['lead_ids'])->update(['status' => $validated['status']]);
          $message = __('Lead status updated successfully.');
        }
        break;

      case 'delete':
        Lead::whereIn('id', $validated['lead_ids'])->delete();
        $message = __('Leads deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }

  public function export(Request $request)
  {
    Gate::authorize('viewAny', Lead::class);

    // In a full implementation, you would use Laravel Excel or similar
    $leads = Lead::with(['assignedTo'])
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->when($request->source, function ($query, $source) {
        $query->bySource($source);
      })
      ->get();

    return response()->json([
      'message' => __('Export functionality to be implemented.'),
      'count' => $leads->count(),
    ]);
  }
}
