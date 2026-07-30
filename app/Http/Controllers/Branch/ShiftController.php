<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;

class ShiftController extends Controller
{
    /**
     * List all shifts for the current branch
     */
    public function index(): View
    {
        $user   = Auth::user();
        $shifts = Shift::where('branch_id', $user->branch_id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('branch.shifts.index', compact('shifts'));
    }

    /**
     * Show form to open a new shift (cash drawer)
     */
    public function create(): View
    {
        return view('branch.shifts.create');
    }

    /**
     * Store a newly opened shift
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        $shift = Shift::create([
            'company_id' => $user->company_id,
            'branch_id' => $request->input('branch_id'),
            'opened_by' => $user->id,
            'opening_balance' => $request->input('opening_balance'),
            'status' => 'open',
        ]);

        return redirect()->route('branch.pos.index')->with('success', 'Shift opened successfully.');
    }

    /**
     * Close an open shift
     */
    public function close(Request $request, Shift $shift): RedirectResponse
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // Authorization: only opener or company admin can close
        if ($shift->opened_by !== $user->id && ! $user->hasRole('Company Admin')) {
            abort(403);
        }

        $shift->closing_balance = $request->input('closing_balance');
        $shift->status = 'closed';
        $shift->save();

        return redirect()->route('branch.pos.index')->with('success', 'Shift closed successfully.');
    }
}
