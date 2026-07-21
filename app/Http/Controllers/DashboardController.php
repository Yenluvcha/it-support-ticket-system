<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $isAdmin = $request->user()->isAdmin();
        $tickets = Ticket::query()->with('user');

        if (! $isAdmin) {
            $tickets->whereBelongsTo($request->user());
        }

        $statusCounts = [];

        foreach (TicketStatus::cases() as $status) {
            $statusCounts[$status->value] = (clone $tickets)
                ->where('status', $status)
                ->count();
        }

        return view('dashboard', [
            'isAdmin' => $isAdmin,
            'recentTickets' => $tickets->latest()->take(5)->get(),
            'statusCounts' => $statusCounts,
        ]);
    }
}
