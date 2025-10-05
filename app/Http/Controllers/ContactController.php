<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class ContactController extends Controller
{
  public function index(Request $request)
  {
    // Gate::authorize('viewAny', Contact::class);

    $contacts = Contact::with(['assignedTo', 'createdBy'])
      ->when($request->search, function ($query, $search) {
        $query->search($search);
      })
      ->when($request->type, function ($query, $type) {
        $query->byType($type);
      })
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->when($request->assigned_to, function ($query, $assignedTo) {
        $query->assignedTo($assignedTo);
      })
      ->when($request->tag, function ($query, $tag) {
        $query->withTag($tag);
      })
      ->when($request->location, function ($query, $location) {
        $query->inLocation($location);
      })
      ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
      ->paginate($request->per_page ?? 15)
      ->withQueryString();

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Contacts/Index', [
      'contacts' => $contacts,
      'users' => $users,
      'filters' => $request->only(['search', 'type', 'status', 'assigned_to', 'tag', 'location']),
      'sort' => $request->only(['sort', 'direction']),
    ]);
  }

  public function create()
  {
    // Gate::authorize('create', Contact::class);

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Contacts/Create', [
      'users' => $users,
    ]);
  }

  public function store(Request $request)
  {
    // Gate::authorize('create', Contact::class);

    $validated = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|email|unique:contacts,email',
      'phone' => 'nullable|string|max:255',
      'mobile' => 'nullable|string|max:255',
      'company' => 'nullable|string|max:255',
      'job_title' => 'nullable|string|max:255',
      'department' => 'nullable|string|max:255',
      'address' => 'nullable|string',
      'city' => 'nullable|string|max:255',
      'state' => 'nullable|string|max:255',
      'country' => 'nullable|string|max:255',
      'postal_code' => 'nullable|string|max:255',
      'website' => 'nullable|url',
      'linkedin' => 'nullable|url',
      'twitter' => 'nullable|url',
      'type' => 'nullable|in:customer,prospect,partner,vendor,other',
      'status' => 'nullable|in:active,inactive,do_not_contact',
      'notes' => 'nullable|string',
      'custom_fields' => 'nullable|array',
      'tags' => 'nullable|array',
      'communication_preferences' => 'nullable|array',
      'assigned_to' => 'nullable|exists:users,id',
      'birthday' => 'nullable|date',
    ]);

    $validated['created_by'] = auth()->id();

    $contact = Contact::create($validated);

    return redirect()->route('contacts.index')->with('success', __('Contact created successfully.'));
  }

  public function show(Contact $contact)
  {
    // Gate::authorize('view', $contact);

    $contact->load([
      'assignedTo',
      'createdBy',
      'opportunities' => function ($query) {
        $query->with('assignedTo')->latest();
      },
      'supportTickets' => function ($query) {
        $query->with('assignedTo')->latest();
      },
      'interactions' => function ($query) {
        $query->with('user')->latest('interaction_date');
      },
      'campaigns' => function ($query) {
        $query->latest('created_at');
      },
      'convertedFromLead',
    ]);

    return Inertia::render('CRM/Contacts/Show', [
      'contact' => $contact,
      'engagementScore' => $contact->getEngagementScore(),
    ]);
  }

  public function edit(Contact $contact)
  {
    // Gate::authorize('update', $contact);

    $users = User::select(['id', 'name'])->get();

    return Inertia::render('CRM/Contacts/Edit', [
      'contact' => $contact,
      'users' => $users,
    ]);
  }

  public function update(Request $request, Contact $contact)
  {
    // Gate::authorize('update', $contact);

    $validated = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|email|unique:contacts,email,' . $contact->id,
      'phone' => 'nullable|string|max:255',
      'mobile' => 'nullable|string|max:255',
      'company' => 'nullable|string|max:255',
      'job_title' => 'nullable|string|max:255',
      'department' => 'nullable|string|max:255',
      'address' => 'nullable|string',
      'city' => 'nullable|string|max:255',
      'state' => 'nullable|string|max:255',
      'country' => 'nullable|string|max:255',
      'postal_code' => 'nullable|string|max:255',
      'website' => 'nullable|url',
      'linkedin' => 'nullable|url',
      'twitter' => 'nullable|url',
      'type' => 'nullable|in:customer,prospect,partner,vendor,other',
      'status' => 'nullable|in:active,inactive,do_not_contact',
      'notes' => 'nullable|string',
      'custom_fields' => 'nullable|array',
      'tags' => 'nullable|array',
      'communication_preferences' => 'nullable|array',
      'assigned_to' => 'nullable|exists:users,id',
      'birthday' => 'nullable|date',
    ]);

    $contact->update($validated);

    return redirect()->route('contacts.show', $contact)->with('success', __('Contact updated successfully.'));
  }

  public function destroy(Contact $contact)
  {
    // Gate::authorize('delete', $contact);

    $contact->delete();

    return redirect()->route('contacts.index')->with('success', __('Contact deleted successfully.'));
  }

  public function addTag(Request $request, Contact $contact)
  {
    // Gate::authorize('update', $contact);

    $validated = $request->validate([
      'tag' => 'required|string|max:255',
    ]);

    $contact->addTag($validated['tag']);

    return response()->json([
      'message' => __('Tag added successfully.'),
      'tags' => $contact->fresh()->tags,
    ]);
  }

  public function removeTag(Request $request, Contact $contact)
  {
    // Gate::authorize('update', $contact);

    $validated = $request->validate([
      'tag' => 'required|string',
    ]);

    $contact->removeTag($validated['tag']);

    return response()->json([
      'message' => __('Tag removed successfully.'),
      'tags' => $contact->fresh()->tags,
    ]);
  }

  public function updateCommunicationPreference(Request $request, Contact $contact)
  {
    // Gate::authorize('update', $contact);

    $validated = $request->validate([
      'channel' => 'required|string|in:email,phone,sms,mail',
      'preference' => 'required|boolean',
    ]);

    $contact->updateCommunicationPreference($validated['channel'], $validated['preference']);

    return response()->json([
      'message' => __('Communication preference updated successfully.'),
      'preferences' => $contact->fresh()->communication_preferences,
    ]);
  }

  public function bulkAction(Request $request)
  {
    // Gate::authorize('viewAny', Contact::class);

    $validated = $request->validate([
      'action' => 'required|in:assign,update_status,update_type,add_tag,delete',
      'contact_ids' => 'required|array|min:1',
      'contact_ids.*' => 'exists:contacts,id',
      'assigned_to' => 'nullable|exists:users,id',
      'status' => 'nullable|in:active,inactive,do_not_contact',
      'type' => 'nullable|in:customer,prospect,partner,vendor,other',
      'tag' => 'nullable|string|max:255',
    ]);

    $contacts = Contact::whereIn('id', $validated['contact_ids'])->get();

    foreach ($contacts as $contact) {
      // Gate::authorize('update', $contact);
    }

    switch ($validated['action']) {
      case 'assign':
        if (isset($validated['assigned_to'])) {
          Contact::whereIn('id', $validated['contact_ids'])->update(['assigned_to' => $validated['assigned_to']]);
          $message = __('Contacts assigned successfully.');
        }
        break;

      case 'update_status':
        if (isset($validated['status'])) {
          Contact::whereIn('id', $validated['contact_ids'])->update(['status' => $validated['status']]);
          $message = __('Contact status updated successfully.');
        }
        break;

      case 'update_type':
        if (isset($validated['type'])) {
          Contact::whereIn('id', $validated['contact_ids'])->update(['type' => $validated['type']]);
          $message = __('Contact type updated successfully.');
        }
        break;

      case 'add_tag':
        if (isset($validated['tag'])) {
          foreach ($contacts as $contact) {
            $contact->addTag($validated['tag']);
          }
          $message = __('Tag added to contacts successfully.');
        }
        break;

      case 'delete':
        Contact::whereIn('id', $validated['contact_ids'])->delete();
        $message = __('Contacts deleted successfully.');
        break;
    }

    return back()->with('success', $message ?? __('Bulk action completed.'));
  }

  public function export(Request $request)
  {
    // Gate::authorize('viewAny', Contact::class);

    // In a full implementation, you would use Laravel Excel or similar
    $contacts = Contact::with(['assignedTo'])
      ->when($request->type, function ($query, $type) {
        $query->byType($type);
      })
      ->when($request->status, function ($query, $status) {
        $query->where('status', $status);
      })
      ->get();

    return response()->json([
      'message' => __('Export functionality to be implemented.'),
      'count' => $contacts->count(),
    ]);
  }
}
