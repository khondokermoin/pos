<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['company', 'subscription.plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('company', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20);

        $stats = [
            'total_revenue' => Transaction::where('status', 'success')->sum('amount'),
            'total_count' => Transaction::count(),
            'success_count' => Transaction::where('status', 'success')->count(),
            'pending_count' => Transaction::where('status', 'pending')->count(),
            'failed_count' => Transaction::where('status', 'failed')->count(),
        ];

        return view('super-admin.transactions.index', compact('transactions', 'stats'));
    }
}