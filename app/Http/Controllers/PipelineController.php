<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index()
    {
        $pipelines = Pipeline::withCount('leads')->with('stages')->get();
        return view('pipelines.index', compact('pipelines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $pipeline = Pipeline::create($data);

        // Default stages
        $defaults = [
            ['name' => 'Novo Lead',         'color' => '#6366f1', 'order' => 1],
            ['name' => 'Contactado',         'color' => '#f59e0b', 'order' => 2],
            ['name' => 'Proposta Enviada',   'color' => '#3b82f6', 'order' => 3],
            ['name' => 'Ganho',              'color' => '#10b981', 'order' => 4, 'is_won' => true],
            ['name' => 'Perdido',            'color' => '#ef4444', 'order' => 5, 'is_lost' => true],
        ];
        foreach ($defaults as $stage) {
            $pipeline->stages()->create($stage);
        }

        return redirect()->route('pipelines.index')->with('success', 'Pipeline criado.');
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $pipeline->update($data);

        return redirect()->route('pipelines.index')->with('success', 'Pipeline actualizado.');
    }

    public function destroy(Pipeline $pipeline)
    {
        if ($pipeline->leads()->exists()) {
            return back()->withErrors(['error' => 'Não é possível eliminar um pipeline com leads associados.']);
        }

        $pipeline->delete();

        return redirect()->route('pipelines.index')->with('success', 'Pipeline eliminado.');
    }

    public function stages(Pipeline $pipeline)
    {
        $pipeline->load('stages');
        return view('pipelines.stages', compact('pipeline'));
    }
}
