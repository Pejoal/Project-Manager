<?php namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
  public function index()
  {
    $clients = Client::all();
    return Inertia::render('Clients/Index', compact('clients'));
  }

  public function create()
  {
    return Inertia::render('Clients/Create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:clients,email',
      'phone' => 'nullable|string|max:20',
    ]);

    Client::create($request->all());

    return redirect()->route('clients.index');
  }

  public function show(Client $client)
  {
    return Inertia::render('Clients/Show', compact('client'));
  }

  public function edit(Client $client)
  {
    return Inertia::render('Clients/Edit', compact('client'));
  }

  public function update(Request $request, Client $client)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:clients,email,' . $client->id,
      'phone' => 'nullable|string|max:20',
    ]);

    $client->update($request->all());

    return redirect()->route('clients.index');
  }

  public function destroy(Client $client)
  {
    $client->delete();

    return redirect()->route('clients.index');
  }
}
