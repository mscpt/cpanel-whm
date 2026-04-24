<?php

namespace App\Http\Controllers;

use App\Models\Activity;
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

        // withCount avoids loading all lead records just to call ->count() in the view
        $pipelines = Pipeline::with(['stages' => function ($q) {
            $q->withCount(['leads' => fn($q) => $q->whereNull('deleted_at')])
              ->orderBy('order');
        }])->get();

        $recentActivities = Activity::with('user')
            ->latest('occurred_at')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'renewals', 'pipelines', 'recentActivities'));
    }
}
