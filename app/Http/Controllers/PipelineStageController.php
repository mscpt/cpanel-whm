<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Http\Request;

class PipelineStageController extends Controller
{
    public function store(Request $request, Pipeline $pipeline)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'color'   => 'required|string|max:20',
            'is_won'  => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $data['order'] = $pipeline->stages()->max('order') + 1;
        $pipeline->stages()->create($data);

        return back()->with('success', 'Etapa criada.');
    }

    public function update(Request $request, PipelineStage $stage)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'color'   => 'required|string|max:20',
            'is_won'  => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $stage->update($data);

        return back()->with('success', 'Etapa actualizada.');
    }

    public function reorder(Request $request, Pipeline $pipeline)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $position => $stageId) {
            $pipeline->stages()->where('id', $stageId)->update(['order' => $position + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(PipelineStage $stage)
    {
        if ($stage->leads()->exists()) {
            return back()->withErrors(['error' => 'Não é possível eliminar uma etapa com leads associados.']);
        }

        $stage->delete();

        return back()->with('success', 'Etapa eliminada.');
    }
}
