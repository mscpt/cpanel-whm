<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('contracts')->latest()->get();
        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'status'        => 'required|in:active,inactive',
        ]);

        Plan::create($data);

        return redirect()->route('plans.index')->with('success', 'Plano criado.');
    }

    public function edit(Plan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'status'        => 'required|in:active,inactive',
        ]);

        $plan->update($data);

        return redirect()->route('plans.index')->with('success', 'Plano actualizado.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Plano removido.');
    }
}
