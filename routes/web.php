<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\PipelineStageController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes (no auth)
Route::get('/p/{token}', [ProposalController::class, 'publicView'])->name('proposals.public');
Route::post('/p/{token}/accept', [ProposalController::class, 'accept'])->name('proposals.accept');
Route::get('/track/{token}', [TrackController::class, 'pixel'])->name('track.pixel');
Route::get('/track/{token}.gif', [TrackController::class, 'pixel'])->name('track.pixel.gif');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/export', [ClientController::class, 'export'])->name('clients.export');
    Route::post('/clients/{client}/anonymize', [ClientController::class, 'anonymize'])->name('clients.anonymize');

    // Contacts (nested under clients)
    Route::get('/clients/{client}/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/clients/{client}/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // Plans
    Route::resource('plans', PlanController::class)->except('show');

    // Contracts
    Route::resource('contracts', ContractController::class);

    // Pipelines
    Route::get('/pipelines', [PipelineController::class, 'index'])->name('pipelines.index');
    Route::post('/pipelines', [PipelineController::class, 'store'])->name('pipelines.store');
    Route::put('/pipelines/{pipeline}', [PipelineController::class, 'update'])->name('pipelines.update');
    Route::delete('/pipelines/{pipeline}', [PipelineController::class, 'destroy'])->name('pipelines.destroy');
    Route::get('/pipelines/{pipeline}/stages', [PipelineController::class, 'stages'])->name('pipelines.stages');

    // Pipeline Stages
    Route::post('/pipelines/{pipeline}/stages', [PipelineStageController::class, 'store'])->name('pipeline-stages.store');
    Route::put('/pipeline-stages/{stage}', [PipelineStageController::class, 'update'])->name('pipeline-stages.update');
    Route::delete('/pipeline-stages/{stage}', [PipelineStageController::class, 'destroy'])->name('pipeline-stages.destroy');
    Route::post('/pipelines/{pipeline}/stages/reorder', [PipelineStageController::class, 'reorder'])->name('pipeline-stages.reorder');

    // Leads
    Route::resource('leads', LeadController::class);
    Route::patch('/leads/{lead}/move', [LeadController::class, 'move'])->name('leads.move');

    // Activities
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    // Proposals
    Route::resource('proposals', ProposalController::class);
    Route::post('/proposals/{proposal}/send', [ProposalController::class, 'send'])->name('proposals.send');
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'pdf'])->name('proposals.pdf');

    // Import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/upload', [ImportController::class, 'upload'])->name('import.upload');
    Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');

    // Admin only
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
