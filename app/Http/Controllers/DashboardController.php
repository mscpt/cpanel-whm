<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\Proposal;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'clients'          => Client::count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'open_leads'       => Lead::whereHas('stage', fn($q) => $q->where('is_won', false)->where('is_lost', false))->count(),
            'proposals_sent'   => Proposal::whereIn('status', ['sent', 'viewed'])->count(),
        ];

        $renewals = Contract::where('status', 'active')
            ->whereNotNull('renewal_date')
            ->where('renewal_date', '<=', now()->addDays(60))
            ->orderBy('renewal_date')
            ->with('client')
            ->limit(10)
            ->get();

        $pipelines = Pipeline::with(['stages.leads' => function ($q) {
            $q->whereNull('deleted_at');
        }])->get();

        $recentActivities = \App\Models\Activity::with(['user', 'activityable'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'renewals', 'pipelines', 'recentActivities'));
    }
}
