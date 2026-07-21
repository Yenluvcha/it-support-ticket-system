<?php

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user dashboard shows only their ticket overview', function () {
    $user = User::factory()->create();
    $ownTicket = Ticket::factory()->for($user)->create(['subject' => 'User laptop issue']);
    Ticket::factory()->create(['subject' => 'Another employee issue']);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Your ticket overview')
        ->assertSee($ownTicket->subject)
        ->assertDontSee('Another employee issue');
});

test('an admin dashboard shows every ticket overview', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Ticket::factory()->create(['subject' => 'Network outage']);
    Ticket::factory()->create(['subject' => 'Printer setup']);

    $this->actingAs($admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('All ticket overview')
        ->assertSee('Network outage')
        ->assertSee('Printer setup');
});

test('a user can create a ticket through the browser workflow', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('tickets.create'))
        ->assertOk()
        ->assertSee('Create a support ticket');

    $response = $this->actingAs($user)->post(route('tickets.store'), [
        'subject' => 'VPN connection fails',
        'description' => 'The VPN disconnects immediately after I sign in.',
    ]);

    $ticket = Ticket::query()->sole();

    $response->assertRedirect(route('tickets.show', $ticket))
        ->assertSessionHas('success', "Ticket #{$ticket->id} was created.");
    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'user_id' => $user->id,
        'status' => TicketStatus::Open->value,
    ]);
});

test('the browser ticket form returns validation errors', function () {
    $user = User::factory()->create();

    $this->from(route('tickets.create'))->actingAs($user)->post(route('tickets.store'), [
        'subject' => '',
        'description' => '',
    ])->assertRedirect(route('tickets.create'))
        ->assertSessionHasErrors(['subject', 'description']);
});

test('a user cannot view another users ticket in the browser', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create();

    $this->actingAs($user)->get(route('tickets.show', $ticket))->assertForbidden();
});

test('an admin can review all tickets and advance a ticket from its detail page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()->create();

    $this->actingAs($admin)->get(route('admin.tickets.index'))
        ->assertOk()
        ->assertSee('All Tickets')
        ->assertSee($ticket->subject);

    $this->actingAs($admin)->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertSee('Advance to In Progress');

    $this->actingAs($admin)->patch(route('admin.tickets.advance-status', $ticket), [
        'status' => TicketStatus::InProgress->value,
    ])->assertRedirect(route('tickets.show', $ticket))
        ->assertSessionHas('success', "Ticket #{$ticket->id} advanced to In Progress.");

    expect($ticket->fresh()->status)->toBe(TicketStatus::InProgress);
});
