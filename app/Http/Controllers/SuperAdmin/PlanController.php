<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        // ডেটাবেস থেকে সব প্ল্যান নিয়ে index ভিউতে পাঠানো হচ্ছে (এটাই missing ছিল)
        $plans = Plan::orderBy('price', 'asc')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'price' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'user_limit' => 'required|integer|min:1',
            'branch_limit' => 'required|integer|min:1',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:active,inactive',
            'features' => 'nullable|string',
        ]);

        // Features কে লাইন বাই লাইন নিয়ে Array তে কনভার্ট করা
        $featuresArray = [];
        if ($request->filled('features')) {
            $featuresArray = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        Plan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'trial_days' => $request->trial_days,
            'user_limit' => $request->user_limit,
            'branch_limit' => $request->branch_limit,
            'billing_cycle' => $request->billing_cycle,
            'status' => $request->status,
            'features' => json_encode($featuresArray), // JSON হিসেবে সেভ হবে
        ]);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function show(string $id)
    {
        $plan = Plan::findOrFail($id);
        return view('super-admin.plans.show', compact('plan'));
    }

    public function edit(string $id)
    {
        $plan = Plan::findOrFail($id);
        return view('super-admin.plans.form', compact('plan'));
    }

    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'user_limit' => 'required|integer|min:1',
            'branch_limit' => 'required|integer|min:1',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:active,inactive',
            'features' => 'nullable|string',
        ]);

        $featuresArray = [];
        if ($request->filled('features')) {
            $featuresArray = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        $plan->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'trial_days' => $request->trial_days,
            'user_limit' => $request->user_limit,
            'branch_limit' => $request->branch_limit,
            'billing_cycle' => $request->billing_cycle,
            'status' => $request->status,
            'features' => json_encode($featuresArray),
        ]);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}