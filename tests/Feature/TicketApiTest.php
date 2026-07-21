<?php

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an authenticated user can create an open ticket they own', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('tickets.store'), [
        'subject' => 'Laptop will not start',
        'description' => 'The power indicator does not turn on after charging.',
        'user_id' => User::factory()->create()->id,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.subject', 'Laptop will not start')
        ->assertJsonPath('data.status', TicketStatus::Open->value)
        ->assertJsonPath('data.creator_id', $user->id);

    $this->assertDatabaseHas('tickets', [
        'user_id' => $user->id,
        'subject' => 'Laptop will not start',
        'status' => TicketStatus::Open->value,
    ]);
});

test('users can list and view only their own tickets', function () {
    $user = User::factory()->create();
    $ownTicket = Ticket::factory()->for($user)->create();
    $otherTicket = Ticket::factory()->create();

    $this->actingAs($user)->getJson(route('tickets.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $ownTicket->id);

    $this->actingAs($user)->getJson(route('tickets.show', $ownTicket))
        ->assertOk()
        ->assertJsonPath('data.id', $ownTicket->id);

    $this->actingAs($user)->getJson(route('tickets.show', $otherTicket))
        ->assertForbidden();
});

test('admins can list and view every ticket', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $tickets = Ticket::factory()->count(2)->create();

    $this->actingAs($admin)->getJson(route('admin.tickets.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');

    $this->actingAs($admin)->getJson(route('tickets.show', $tickets->first()))
        ->assertOk()
        ->assertJsonPath('data.id', $tickets->first()->id);
});

test('normal users cannot advance ticket statuses', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create();

    $this->actingAs($user)->patchJson(route('admin.tickets.advance-status', $ticket), [
        'status' => TicketStatus::InProgress->value,
    ])->assertForbidden();
});

test('admins can advance tickets through each lifecycle state', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()->create();

    foreach ([TicketStatus::InProgress, TicketStatus::Resolved, TicketStatus::Closed] as $status) {
        $this->actingAs($admin)->patchJson(route('admin.tickets.advance-status', $ticket), [
            'status' => $status->value,
        ])->assertOk()->assertJsonPath('data.status', $status->value);

        $ticket->refresh();
    }
});

it('rejects invalid lifecycle transitions', function (TicketStatus $current, string $requestedStatus) {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()->create(['status' => $current]);

    $this->actingAs($admin)->patchJson(route('admin.tickets.advance-status', $ticket), [
        'status' => $requestedStatus,
    ])->assertUnprocessable()->assertJsonValidationErrors('status');
})->with([
    'skipped status' => [TicketStatus::Open, TicketStatus::Resolved->value],
    'repeated status' => [TicketStatus::InProgress, TicketStatus::InProgress->value],
    'reopened status' => [TicketStatus::Resolved, TicketStatus::Open->value],
    'closed status' => [TicketStatus::Closed, TicketStatus::Closed->value],
]);

test('ticket requests validate content and status input', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()->create();

    $this->actingAs($user)->postJson(route('tickets.store'), [
        'subject' => str_repeat('a', 256),
        'description' => str_repeat('b', 5001),
    ])->assertUnprocessable()->assertJsonValidationErrors(['subject', 'description']);

    $this->actingAs($admin)->patchJson(route('admin.tickets.advance-status', $ticket), [
        'status' => 'invalid',
    ])->assertUnprocessable()->assertJsonValidationErrors('status');
});
