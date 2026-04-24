<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\Proposal;
use App\Models\TrackEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $proposals = Proposal::with('client', 'lead', 'creator')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('proposals.index', compact('proposals'));
    }

    public function create(Request $request)
    {
        [$clients, $leads, $plans] = $this->formData();
        $selectedLead = $request->lead_id ? Lead::find($request->lead_id) : null;

        return view('proposals.create', compact('clients', 'leads', 'plans', 'selectedLead'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        [$items, $subtotal, $discount] = $this->calculateTotals($data);

        $proposal = Proposal::create([
            ...$data,
            'items'            => $items,
            'created_by'       => auth()->id(),
            'subtotal'         => $subtotal,
            'total'            => $subtotal - $discount,
            'tracking_enabled' => $request->boolean('tracking_enabled', true),
        ]);

        AuditLog::record('create', $proposal, [], ['title' => $proposal->title, 'total' => $proposal->total]);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta criada.');
    }

    public function show(Proposal $proposal)
    {
        $proposal->load('client', 'lead', 'creator', 'trackEvents');
        return view('proposals.show', compact('proposal'));
    }

    public function edit(Proposal $proposal)
    {
        [$clients, $leads, $plans] = $this->formData(includeAllLeads: true);

        return view('proposals.edit', compact('proposal', 'clients', 'leads', 'plans'));
    }

    public function update(Request $request, Proposal $proposal)
    {
        $data = $request->validate($this->rules());
        [$items, $subtotal, $discount] = $this->calculateTotals($data);

        $old = $proposal->only(['title', 'total', 'status']);
        $proposal->update([
            ...$data,
            'items'            => $items,
            'subtotal'         => $subtotal,
            'total'            => $subtotal - $discount,
            'tracking_enabled' => $request->boolean('tracking_enabled', true),
        ]);
        AuditLog::record('update', $proposal, $old, ['title' => $proposal->title, 'total' => $proposal->total]);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta actualizada.');
    }

    public function send(Proposal $proposal)
    {
        $proposal->update(['status' => 'sent', 'sent_at' => now()]);

        AuditLog::record('update', $proposal, ['status' => 'draft'], ['status' => 'sent'], 'Proposta enviada');

        return back()->with('success', 'Proposta marcada como enviada. Link: ' . $proposal->utm_url);
    }

    public function publicView(string $token)
    {
        $proposal = Proposal::where('token', $token)
            ->where('status', '!=', 'draft')
            ->firstOrFail();

        if ($proposal->tracking_enabled) {
            TrackEvent::create([
                'token'          => $token,
                'trackable_type' => Proposal::class,
                'trackable_id'   => $proposal->id,
                'event'          => 'view',
                'context'        => 'proposal_view',
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent(),
                'created_at'     => now(),
            ]);
        }

        // Record first view only; subsequent opens keep the original viewed_at
        if ($proposal->status === 'sent') {
            $proposal->update(['status' => 'viewed', 'viewed_at' => now()]);
        }

        $proposal->load('client', 'lead');
        return view('proposals.public', compact('proposal'));
    }

    public function accept(string $token)
    {
        $proposal = Proposal::where('token', $token)
            ->whereNotIn('status', ['draft', 'rejected'])
            ->firstOrFail();

        $proposal->update(['status' => 'accepted']);

        return view('proposals.accepted', compact('proposal'));
    }

    public function pdf(Proposal $proposal)
    {
        $proposal->load('client', 'lead', 'creator');
        $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));

        return $pdf->download("proposta-{$proposal->id}.pdf");
    }

    public function destroy(Proposal $proposal)
    {
        AuditLog::record('delete', $proposal, $proposal->only(['title', 'total', 'status']));
        $proposal->delete();

        return redirect()->route('proposals.index')->with('success', 'Proposta arquivada.');
    }

    private function rules(): array
    {
        return [
            'title'               => 'required|string|max:255',
            'client_id'           => 'nullable|exists:clients,id',
            'lead_id'             => 'nullable|exists:leads,id',
            'items'               => 'nullable|array',
            'items.*.description' => 'required_with:items|string',
            'items.*.qty'         => 'required_with:items|numeric|min:0',
            'items.*.unit_price'  => 'required_with:items|numeric|min:0',
            'discount'            => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'tracking_enabled'    => 'boolean',
            'expires_at'          => 'nullable|date',
        ];
    }

    /** @return array{array, float, float} [items, subtotal, discount] */
    private function calculateTotals(array $data): array
    {
        $items    = $data['items'] ?? [];
        $discount = (float) ($data['discount'] ?? 0);

        $items = array_map(fn($i) => array_merge($i, [
            'total' => (float) ($i['qty'] ?? 0) * (float) ($i['unit_price'] ?? 0),
        ]), $items);

        $subtotal = array_sum(array_column($items, 'total'));

        return [$items, $subtotal, $discount];
    }

    private function formData(bool $includeAllLeads = false): array
    {
        $leads = $includeAllLeads
            ? Lead::with('pipeline')->orderBy('name')->get()
            : Lead::whereHas('stage', fn($q) => $q->where('is_won', false)->where('is_lost', false))
                ->with('pipeline')->orderBy('name')->get();

        return [
            Client::orderBy('name')->get(),
            $leads,
            Plan::where('status', 'active')->get(),
        ];
    }
}
