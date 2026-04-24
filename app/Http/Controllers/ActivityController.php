<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Lead;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => 'required|in:note,call,email,meeting',
            'subject'          => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'occurred_at'      => 'nullable|date',
            'activityable_type'=> 'required|in:client,lead',
            'activityable_id'  => 'required|integer',
        ]);

        $modelClass = $data['activityable_type'] === 'client' ? Client::class : Lead::class;
        $model = $modelClass::findOrFail($data['activityable_id']);

        $activity = $model->activities()->create([
            'user_id'     => auth()->id(),
            'type'        => $data['type'],
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);

        return back()->with('success', 'Atividade registada.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return back()->with('success', 'Atividade removida.');
    }
}
