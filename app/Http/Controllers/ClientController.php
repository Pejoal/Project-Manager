<?php namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class ClientController extends Controller
{
  public function index()
  {
    $clients = Client::with('projects')->orderBy('id', 'desc')->get();
    $projects = Project::orderBy('id', 'desc')->get();
    return Inertia::render('Clients/Index', compact(['clients', 'projects']));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:clients,email',
      'phone' => 'nullable|string|max:20',
      'projects' => 'nullable|array',
      'projects.*' => 'exists:projects,id',
    ]);

    $client = Client::create($request->except(['projects']));
    $client->projects()->sync($request->projects);
    event(new ActivityLogged(auth()->user(), 'created_client', 'Created a client', $client));

    return redirect()->route('clients.index')->with('success', 'Client created successfully!');
  }

  public function show(Client $client)
  {
    $client->load(['projects']);
    return Inertia::render('Clients/Show', compact('client'));
  }

  public function edit(Client $client)
  {
    $projects = Project::orderBy('id', 'desc')->get();
    $client->load('projects');
    return Inertia::render('Clients/Edit', compact(['client', 'projects']));
  }

  public function update(Request $request, Client $client)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:clients,email,' . $client->id,
      'phone' => 'nullable|string|max:20',
      'projects' => 'nullable|array',
      'projects.*' => 'exists:projects,id',
    ]);

    $client->update($request->except(['projects']));
    $client->projects()->sync($request->projects);
    event(new ActivityLogged(auth()->user(), 'updated_client', 'Updated a client', $client));

    session()->flash('flash.banner', 'Client updated successfully!');
    session()->flash('flash.bannerStyle', 'success');
    return redirect()->route('clients.index');
  }

  public function destroy(Client $client)
  {
    $client->delete();
    event(new ActivityLogged(auth()->user(), 'deleted_client', 'Deleted a client', $client));

    
    session()->flash('flash.banner', 'Client deleted successfully!');
    session()->flash('flash.bannerStyle', 'danger');
    return redirect()->route('clients.index');
  }
}
