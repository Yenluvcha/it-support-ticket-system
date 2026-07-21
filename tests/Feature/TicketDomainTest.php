<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('the tickets table has the ticket domain columns', function () {
    expect(Schema::hasColumns('tickets', [
        'id',
        'user_id',
        'subject',
        'description',
        'status',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});

test('tickets belong to their creator and default to open', function () {
    $user = User::factory()->create();
    $ticket = new Ticket([
        'subject' => 'Unable to access email',
        'description' => 'Email access has stopped working for this account.',
    ]);
    $ticket->user()->associate($user);
    $ticket->save();
    $ticket->refresh();

    expect($ticket->status)->toBe(TicketStatus::Open)
        ->and($ticket->user->is($user))->toBeTrue()
        ->and($user->tickets->sole()->is($ticket))->toBeTrue();
});

test('tickets cast every lifecycle status to the ticket status enum', function () {
    foreach (TicketStatus::cases() as $status) {
        $ticket = Ticket::factory()->create(['status' => $status]);

        expect($ticket->status)->toBe($status);
    }
});

test('the ticket factory creates a valid open ticket owned by a user', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->id)->not->toBeNull()
        ->and($ticket->user)->toBeInstanceOf(User::class)
        ->and($ticket->subject)->not->toBeEmpty()
        ->and($ticket->description)->not->toBeEmpty()
        ->and($ticket->status)->toBe(TicketStatus::Open);
});
