<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['company', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                    ->orWhereHas('company', fn($c) => $c->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $tickets = $query->latest()->paginate(20);

        $stats = [
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
            'closed'      => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('super-admin.support-tickets.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load(['company', 'user']);

        return view('super-admin.support-tickets.show', compact('supportTicket'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'user_id'    => 'nullable|exists:users,id',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
            'priority'   => 'required|in:low,medium,high,urgent',
        ]);

        SupportTicket::create($data);

        return redirect()->route('superadmin.support-tickets.index')
            ->with('success', 'Support ticket created.');
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $data = $request->validate([
            'status'      => 'required|in:open,in_progress,resolved,closed',
            'admin_reply' => 'nullable|string',
        ]);

        if (!empty($data['admin_reply'])) {
            $data['replied_at'] = now();
        }

        $supportTicket->update($data);

        return redirect()->route('superadmin.support-tickets.show', $supportTicket->id)
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(SupportTicket $supportTicket)
    {
        $supportTicket->delete();

        return redirect()->route('superadmin.support-tickets.index')
            ->with('success', 'Ticket deleted.');
    }
}
