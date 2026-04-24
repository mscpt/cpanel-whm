<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create(Client $client)
    {
        return view('contacts.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role'  => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $client->contacts()->create($data);

        return redirect()->route('clients.show', $client)->with('success', 'Contacto adicionado.');
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role'  => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $contact->update($data);

        return redirect()->route('clients.show', $contact->client_id)->with('success', 'Contacto actualizado.');
    }

    public function destroy(Contact $contact)
    {
        $clientId = $contact->client_id;
        $contact->delete();

        return redirect()->route('clients.show', $clientId)->with('success', 'Contacto removido.');
    }
}
