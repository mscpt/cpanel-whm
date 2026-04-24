<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $pipelineId = $request->pipeline_id ?? Pipeline::value('id');
        $pipeline   = Pipeline::with(['stages.leads' => function ($q) use ($request) {
            $q->with('client', 'assignedUser')
              ->when($request->assigned_to, fn($q, $u) => $q->where('assigned_to', $u))
              ->whereNull('deleted_at')
              ->orderBy('order');
        }])->findOrFail($pipelineId ?? 0);

        $pipelines = Pipeline::all();
        $users     = User::where('is_active', true)->get();

        return view('leads.index', compact('pipeline', 'pipelines', 'users'));
    }

    public function create(Request $request)
    {
        [$pipelines, $clients, $users] = $this->formData();

        return view('leads.create', compact('pipelines', 'clients', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        Lead::create($data);

        return redirect()->route('leads.index', ['pipeline_id' => $data['pipeline_id']])->with('success', 'Lead criado.');
    }

    public function show(Lead $lead)
    {
        $lead->load('client', 'stage', 'pipeline', 'activities.user', 'proposals');
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        [$pipelines, $clients, $users] = $this->formData();

        return view('leads.edit', compact('lead', 'pipelines', 'clients', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate($this->rules());

        $lead->update($data);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead actualizado.');
    }

    public function move(Request $request, Lead $lead)
    {
        $request->validate([
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'order'             => 'nullable|integer',
        ]);

        $lead->update([
            'pipeline_stage_id' => $request->pipeline_stage_id,
            'order'             => $request->order ?? $lead->order,
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Lead $lead)
    {
        $pipelineId = $lead->pipeline_id;
        $lead->delete();

        return redirect()->route('leads.index', ['pipeline_id' => $pipelineId])->with('success', 'Lead arquivado.');
    }

    private function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:50',
            'company'           => 'nullable|string|max:255',
            'source'            => 'nullable|string|max:100',
            'value'             => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
            'pipeline_id'       => 'required|exists:pipelines,id',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'assigned_to'       => 'nullable|exists:users,id',
            'client_id'         => 'nullable|exists:clients,id',
        ];
    }

    private function formData(): array
    {
        return [
            Pipeline::with('stages')->get(),
            Client::orderBy('name')->get(),
            User::where('is_active', true)->get(),
        ];
    }
}
