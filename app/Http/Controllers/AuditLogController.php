<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->action, fn($q, $a) => $q->where('action', $a))
            ->when($request->user_id, fn($q, $u) => $q->where('user_id', $u))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $users = \App\Models\User::all();

        return view('audit-logs.index', compact('logs', 'users'));
    }
}
