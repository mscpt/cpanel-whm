<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Services\WhmcsImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(private WhmcsImportService $importer) {}

    public function index()
    {
        $pipelines = Pipeline::with('stages')->get();
        $imports   = \App\Models\Import::with('user')->latest()->limit(20)->get();

        return view('import.index', compact('pipelines', 'imports'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:csv,txt|max:10240',
            'pipeline_id' => 'required|exists:pipelines,id',
        ]);

        $path    = $request->file('file')->store('imports', 'local');
        $preview = $this->importer->preview(storage_path("app/private/{$path}"));

        session(['import_path' => $path, 'import_pipeline_id' => $request->pipeline_id]);

        return view('import.map', [
            'headers'    => $preview['headers'],
            'rows'       => $preview['rows'],
            'pipeline'   => Pipeline::with('stages')->find($request->pipeline_id),
            'crmFields'  => WhmcsImportService::CRM_FIELDS,
            'suggested'  => $preview['suggested'],
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'mapping'           => 'required|array',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'on_duplicate'      => 'required|in:skip,update',
        ]);

        $path = session('import_path');
        if (! $path) {
            return redirect()->route('import.index')->withErrors(['error' => 'Sessão expirada. Faça novo upload.']);
        }

        $result = $this->importer->import(
            storage_path("app/private/{$path}"),
            $request->mapping,
            (int) session('import_pipeline_id'),
            (int) $request->pipeline_stage_id,
            $request->on_duplicate,
            auth()->id()
        );

        session()->forget(['import_path', 'import_pipeline_id']);

        return redirect()->route('import.index')->with('success',
            "Importação concluída: {$result->created} criados, {$result->updated} actualizados, {$result->skipped} ignorados, {$result->errors} erros."
        );
    }
}
