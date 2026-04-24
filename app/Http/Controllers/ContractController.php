<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Plan;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $contracts = Contract::with('client', 'plan')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->whereHas('client', fn($q2) => $q2->where('name', 'like', "%{$s}%")))
            ->orderBy('renewal_date')
            ->paginate(20)
            ->withQueryString();

        return view('contracts.index', compact('contracts'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $plans   = Plan::where('status', 'active')->orderBy('name')->get();
        $selectedClient = $request->client_id ? Client::find($request->client_id) : null;

        return view('contracts.create', compact('clients', 'plans', 'selectedClient'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'plan_id'       => 'nullable|exists:plans,id',
            'title'         => 'nullable|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
            'renewal_date'  => 'nullable|date',
            'value'         => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'status'        => 'required|in:active,suspended,cancelled',
            'notes'         => 'nullable|string',
        ]);

        $contract = Contract::create($data);
        AuditLog::record('create', $contract, [], $data);

        return redirect()->route('contracts.show', $contract)->with('success', 'Contrato criado.');
    }

    public function show(Contract $contract)
    {
        $contract->load('client', 'plan');
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $clients = Client::orderBy('name')->get();
        $plans   = Plan::where('status', 'active')->orderBy('name')->get();
        return view('contracts.edit', compact('contract', 'clients', 'plans'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'plan_id'       => 'nullable|exists:plans,id',
            'title'         => 'nullable|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
            'renewal_date'  => 'nullable|date',
            'value'         => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'status'        => 'required|in:active,suspended,cancelled',
            'notes'         => 'nullable|string',
        ]);

        $old = $contract->toArray();
        $contract->update($data);
        AuditLog::record('update', $contract, $old, $data);

        return redirect()->route('contracts.show', $contract)->with('success', 'Contrato actualizado.');
    }

    public function destroy(Contract $contract)
    {
        AuditLog::record('delete', $contract, $contract->toArray());
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contrato arquivado.');
    }
}
