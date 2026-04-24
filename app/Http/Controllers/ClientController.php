<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'document'=> 'nullable|string|max:50',
            'type'    => 'required|in:person,company',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'zip'     => 'nullable|string|max:20',
            'country' => 'nullable|string|max:5',
            'notes'   => 'nullable|string',
        ]);

        $client = Client::create($data);
        AuditLog::record('create', $client, [], $data);

        return redirect()->route('clients.show', $client)->with('success', 'Cliente criado com sucesso.');
    }

    public function show(Client $client)
    {
        $client->load([
            'contacts',
            'contracts.plan',
            'activities.user',
            'leads.stage',
        ]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'document'=> 'nullable|string|max:50',
            'type'    => 'required|in:person,company',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'zip'     => 'nullable|string|max:20',
            'country' => 'nullable|string|max:5',
            'notes'   => 'nullable|string',
        ]);

        $old = $client->toArray();
        $client->update($data);
        AuditLog::record('update', $client, $old, $data);

        return redirect()->route('clients.show', $client)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Client $client)
    {
        AuditLog::record('delete', $client, $client->toArray());
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente arquivado.');
    }

    public function export(Client $client)
    {
        $data = $client->load(['contacts', 'contracts', 'activities'])->toArray();
        $filename = 'cliente-' . $client->id . '-' . now()->format('Ymd') . '.json';

        AuditLog::record('view', $client, [], [], 'Exportação RGPD');

        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function anonymize(Client $client)
    {
        $old = $client->toArray();
        $client->update([
            'name'     => 'Utilizador Anonimizado #' . $client->id,
            'email'    => null,
            'phone'    => null,
            'document' => null,
            'address'  => null,
            'notes'    => null,
        ]);
        AuditLog::record('update', $client, $old, [], 'Anonimização RGPD');

        return redirect()->route('clients.show', $client)->with('success', 'Dados do cliente anonimizados (RGPD).');
    }
}
