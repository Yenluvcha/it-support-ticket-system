<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Requests\AdvanceTicketStatusRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusAdvancedNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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

        $ticket = DB::transaction(function () use ($request): Ticket {
            $ticket = new Ticket([
                ...$request->validated(),
                'status' => TicketStatus::Open,
            ]);
            $ticket->user()->associate($request->user());
            $ticket->save();

            $admins = User::query()
                ->where('role', UserRole::Admin)
                ->whereKeyNot($request->user()->id)
                ->get();

            Notification::send($admins, new TicketCreatedNotification(
                $ticket->id,
                $ticket->subject,
                $request->user()->name,
            ));

            return $ticket;
        });

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

        $previousStatus = $ticket->status;

        DB::transaction(function () use ($ticket, $nextStatus, $previousStatus, $request): void {
            $ticket->update(['status' => $nextStatus]);

            $owner = $ticket->user;

            if ($owner->isNot($request->user())) {
                $owner->notify(new TicketStatusAdvancedNotification(
                    $ticket->id,
                    $ticket->subject,
                    $previousStatus,
                    $nextStatus,
                ));
            }
        });

        $ticket->refresh();

        if ($request->expectsJson()) {
            return new TicketResource($ticket);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->id} advanced to ".str($nextStatus->value)->replace('_', ' ')->title().'.');
    }
}
