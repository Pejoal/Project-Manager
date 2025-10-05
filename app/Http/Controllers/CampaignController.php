<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class CampaignController extends Controller
{
  public function index(Request $request)
  {
    // Gate::authorize('viewAny', Campaign::class);

    $campaigns = Campaign::with(['createdBy'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->when($request->type, function ($query, $type) {
        $query->byType($type);
      })
      ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    return Inertia::render('CRM/Campaigns/Index', [
      'campaigns' => $campaigns,
      'filters' => $request->only(['search', 'status', 'type']),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create()
  {
    // Gate::authorize('create', Campaign::class);

    return Inertia::render('CRM/Campaigns/Create');
  }

  public function store(Request $request)
  {
    // Gate::authorize('create', Campaign::class);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'type' => 'required|in:email,social_media,webinar,trade_show,direct_mail,telemarketing,other',
      'status' => 'nullable|in:draft,active,paused,completed,cancelled',
      'budget' => 'nullable|numeric|min:0',
      'expected_audience_size' => 'nullable|integer|min:0',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after:start_date',
      'goals' => 'nullable|array',
      'channels' => 'nullable|array',
      'demographics' => 'nullable|array',
      'content' => 'nullable|string',
      'attachments' => 'nullable|array',
    ]);

    $validated['created_by'] = auth()->id();

    $campaign = Campaign::create($validated);

    return redirect()->route('campaigns.index')->with('success', __('Campaign created successfully.'));
  }

  public function show(Campaign $campaign)
  {
    // Gate::authorize('view', $campaign);

    $campaign->load([
      'createdBy',
      'contacts' => function ($query) {
        $query->withPivot([
          'status',
          'sent_at',
          'delivered_at',
          'opened_at',
          'clicked_at',
          'responded_at',
          'open_count',
          'click_count',
        ]);
      },
      'interactions' => function ($query) {
        $query->with('user')->latest('interaction_date');
      },
    ]);

    $metrics = $campaign->getEngagementMetrics();

    return Inertia::render('CRM/Campaigns/Show', [
      'campaign' => $campaign,
      'metrics' => $metrics,
    ]);
  }

  public function edit(Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    return Inertia::render('CRM/Campaigns/Edit', [
      'campaign' => $campaign,
    ]);
  }

  public function update(Request $request, Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'type' => 'required|in:email,social_media,webinar,trade_show,direct_mail,telemarketing,other',
      'status' => 'nullable|in:draft,active,paused,completed,cancelled',
      'budget' => 'nullable|numeric|min:0',
      'actual_cost' => 'nullable|numeric|min:0',
      'expected_audience_size' => 'nullable|integer|min:0',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after:start_date',
      'goals' => 'nullable|array',
      'channels' => 'nullable|array',
      'demographics' => 'nullable|array',
      'content' => 'nullable|string',
      'attachments' => 'nullable|array',
      'leads_generated' => 'nullable|integer|min:0',
      'opportunities_created' => 'nullable|integer|min:0',
      'revenue_generated' => 'nullable|numeric|min:0',
    ]);

    $campaign->update($validated);

    return redirect()->route('campaigns.show', $campaign)->with('success', __('Campaign updated successfully.'));
  }

  public function destroy(Campaign $campaign)
  {
    // Gate::authorize('delete', $campaign);

    $campaign->delete();

    return redirect()->route('campaigns.index')->with('success', __('Campaign deleted successfully.'));
  }

  public function launch(Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $campaign->launch();

    return response()->json([
      'message' => __('Campaign launched successfully.'),
      'campaign' => $campaign->fresh(),
    ]);
  }

  public function pause(Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $campaign->pause();

    return response()->json([
      'message' => __('Campaign paused successfully.'),
      'campaign' => $campaign->fresh(),
    ]);
  }

  public function resume(Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $campaign->resume();

    return response()->json([
      'message' => __('Campaign resumed successfully.'),
      'campaign' => $campaign->fresh(),
    ]);
  }

  public function complete(Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $campaign->complete();

    return response()->json([
      'message' => __('Campaign completed successfully.'),
      'campaign' => $campaign->fresh(),
    ]);
  }

  public function addContacts(Request $request, Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $validated = $request->validate([
      'contact_ids' => 'required|array|min:1',
      'contact_ids.*' => 'exists:contacts,id',
    ]);

    foreach ($validated['contact_ids'] as $contactId) {
      $contact = Contact::find($contactId);
      $campaign->addContact($contact);
    }

    return response()->json([
      'message' => __('Contacts added to campaign successfully.'),
      'audience_size' => $campaign->fresh()->actual_audience_size,
    ]);
  }

  public function removeContact(Request $request, Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $validated = $request->validate([
      'contact_id' => 'required|exists:contacts,id',
    ]);

    $contact = Contact::find($validated['contact_id']);
    $campaign->removeContact($contact);

    return response()->json([
      'message' => __('Contact removed from campaign successfully.'),
      'audience_size' => $campaign->fresh()->actual_audience_size,
    ]);
  }

  public function trackEngagement(Request $request, Campaign $campaign)
  {
    // Gate::authorize('update', $campaign);

    $validated = $request->validate([
      'contact_id' => 'required|exists:contacts,id',
      'action' => 'required|in:sent,delivered,opened,clicked,responded,unsubscribed,bounced',
      'data' => 'nullable|array',
    ]);

    $contact = Contact::find($validated['contact_id']);
    $campaign->trackEmail($contact, $validated['action'], $validated['data'] ?? []);

    return response()->json([
      'message' => __('Engagement tracked successfully.'),
    ]);
  }

  public function analytics(Campaign $campaign)
  {
    // Gate::authorize('view', $campaign);

    $metrics = $campaign->getEngagementMetrics();

    $dailyMetrics = $campaign
      ->contacts()
      ->selectRaw('DATE(pivot.sent_at) as date, COUNT(*) as sent')
      ->whereNotNull('pivot.sent_at')
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    return Inertia::render('CRM/Campaigns/Analytics', [
      'campaign' => $campaign,
      'metrics' => $metrics,
      'dailyMetrics' => $dailyMetrics,
    ]);
  }

  public function bulkAction(Request $request)
  {
    // Gate::authorize('viewAny', Campaign::class);

    $validated = $request->validate([
      'action' => 'required|in:launch,pause,complete,delete',
      'campaign_ids' => 'required|array|min:1',
      'campaign_ids.*' => 'exists:campaigns,id',
    ]);

    $campaigns = Campaign::whereIn('id', $validated['campaign_ids'])->get();

    foreach ($campaigns as $campaign) {
      // Gate::authorize('update', $campaign);
    }

    switch ($validated['action']) {
      case 'launch':
        foreach ($campaigns as $campaign) {
          if ($campaign->status === 'draft') {
            $campaign->launch();
          }
        }
        $message = __('Campaigns launched successfully.');
        break;

      case 'pause':
        foreach ($campaigns as $campaign) {
          if ($campaign->status === 'active') {
            $campaign->pause();
          }
        }
        $message = __('Campaigns paused successfully.');
        break;

      case 'complete':
        foreach ($campaigns as $campaign) {
          $campaign->complete();
        }
        $message = __('Campaigns completed successfully.');
        break;

      case 'delete':
        Campaign::whereIn('id', $validated['campaign_ids'])->delete();
        $message = __('Campaigns deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }

  public function export(Request $request)
  {
    // Gate::authorize('viewAny', Campaign::class);

    // In a full implementation, you would use Laravel Excel or similar
    $campaigns = Campaign::with(['createdBy'])
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->when($request->type, function ($query, $type) {
        $query->byType($type);
      })
      ->get();

    return response()->json([
      'message' => __('Export functionality to be implemented.'),
      'count' => $campaigns->count(),
    ]);
  }
}
