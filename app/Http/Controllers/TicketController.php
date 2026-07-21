<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\AdvanceTicketStatusRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Ticket::class);

        return TicketResource::collection(
            $request->user()->tickets()->latest()->paginate(15),
        );
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $this->authorize('create', Ticket::class);

        $ticket = new Ticket([
            ...$request->validated(),
            'status' => TicketStatus::Open,
        ]);
        $ticket->user()->associate($request->user());
        $ticket->save();

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);

        return new TicketResource($ticket);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAnyForAdmin', Ticket::class);

        return TicketResource::collection(Ticket::query()->latest()->paginate(15));
    }

    public function advanceStatus(AdvanceTicketStatusRequest $request, Ticket $ticket): TicketResource
    {
        $this->authorize('advanceStatus', $ticket);

        $nextStatus = $ticket->status->next();

        if ($nextStatus === null || $request->enum('status', TicketStatus::class) !== $nextStatus) {
            throw ValidationException::withMessages([
                'status' => ['The status must be the ticket\'s next lifecycle state.'],
            ]);
        }

        $ticket->update(['status' => $nextStatus]);

        return new TicketResource($ticket->refresh());
    }
}
