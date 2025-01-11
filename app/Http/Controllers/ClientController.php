<?php namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
  public function index()
  {
    $clients = Client::orderBy('id', 'desc')->get();
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

    return redirect()->route('clients.index');
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

    return redirect()->route('clients.index');
  }

  public function destroy(Client $client)
  {
    $client->delete();

    return redirect()->route('clients.index');
  }
}
