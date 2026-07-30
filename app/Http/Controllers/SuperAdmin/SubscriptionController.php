<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['company', 'plan']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by company name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('company', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Stats for dashboard cards
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'trial' => Subscription::where('status', 'trial')->count(),
            'expired' => Subscription::where('ends_at', '<', now())->count(),
        ];

        return view('super-admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $plans = Plan::active()->orderBy('price')->get();

        return view('super-admin.subscriptions.create', compact('companies', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'status' => 'required|in:active,trial,pending',
            'started_at' => 'required|date',
            'ends_at' => 'nullable|date|after:started_at',
        ]);

        Subscription::create([
            'company_id' => $request->company_id,
            'plan_id' => $request->plan_id,
            'billing_cycle' => $request->billing_cycle,
            'status' => $request->status,
            'started_at' => $request->started_at,
            'ends_at' => $request->ends_at,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
        ]);

        Company::find($request->company_id)->update(['plan_id' => $request->plan_id]);

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    public function show(string $id)
    {
        $subscription = Subscription::with(['company', 'plan'])->findOrFail($id);
        return view('super-admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(string $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        return back()->with('success', 'Subscription cancelled successfully.');
    }

    public function suspend(string $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update(['status' => 'suspended']);
        return back()->with('success', 'Subscription suspended successfully.');
    }

    public function reactivate(string $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update([
            'status' => 'active',
            'cancelled_at' => null,
        ]);
        return back()->with('success', 'Subscription reactivated successfully.');
    }

    public function extend(Request $request, string $id)
    {
        $request->validate(['extend_days' => 'required|integer|min:1|max:365']);

        $subscription = Subscription::findOrFail($id);
        $currentEnd = $subscription->ends_at ? Carbon::parse($subscription->ends_at) : now();
        $newEndDate = $currentEnd->addDays($request->extend_days);

        $subscription->update(['ends_at' => $newEndDate]);

        return back()->with('success', "Subscription extended by {$request->extend_days} days. New end date: {$newEndDate->format('Y-m-d')}");
    }
}