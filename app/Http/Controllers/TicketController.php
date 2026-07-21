<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\AdvanceTicketStatusRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|View
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = $request->user()->tickets()->latest()->paginate(15);

        if ($request->expectsJson()) {
            return TicketResource::collection($tickets);
        }

        return view('tickets.index', [
            'adminView' => false,
            'tickets' => $tickets,
            'title' => 'My Tickets',
        ]);
    }

    public function create(): View
    {
        return view('tickets.create');
    }

    public function store(StoreTicketRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $ticket = new Ticket([
            ...$request->validated(),
            'status' => TicketStatus::Open,
        ]);
        $ticket->user()->associate($request->user());
        $ticket->save();

        if ($request->expectsJson()) {
            return (new TicketResource($ticket))
                ->response()
                ->setStatusCode(201);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->id} was created.");
    }

    public function show(Request $request, Ticket $ticket): TicketResource|View
    {
        $this->authorize('view', $ticket);

        if ($request->expectsJson()) {
            return new TicketResource($ticket);
        }

        return view('tickets.show', [
            'isAdmin' => $request->user()->isAdmin(),
            'nextStatus' => $ticket->status->next(),
            'ticket' => $ticket->load('user'),
        ]);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection|View
    {
        $this->authorize('viewAnyForAdmin', Ticket::class);

        $tickets = Ticket::query()->with('user')->latest()->paginate(15);

        if ($request->expectsJson()) {
            return TicketResource::collection($tickets);
        }

        return view('tickets.index', [
            'adminView' => true,
            'tickets' => $tickets,
            'title' => 'All Tickets',
        ]);
    }

    public function advanceStatus(AdvanceTicketStatusRequest $request, Ticket $ticket): TicketResource|RedirectResponse
    {
        $this->authorize('advanceStatus', $ticket);

        $nextStatus = $ticket->status->next();

        if ($nextStatus === null || $request->enum('status', TicketStatus::class) !== $nextStatus) {
            throw ValidationException::withMessages([
                'status' => ['The status must be the ticket\'s next lifecycle state.'],
            ]);
        }

        $ticket->update(['status' => $nextStatus]);

        $ticket->refresh();

        if ($request->expectsJson()) {
            return new TicketResource($ticket);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->id} advanced to ".str($nextStatus->value)->replace('_', ' ')->title().'.');
    }
}
