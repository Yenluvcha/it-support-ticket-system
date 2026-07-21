<?php

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusAdvancedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creating a ticket stores a notification for every admin except the actor', function () {
    $creator = User::factory()->create();
    $firstAdmin = User::factory()->create(['role' => UserRole::Admin]);
    $secondAdmin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($creator)->postJson(route('tickets.store'), [
        'subject' => 'Laptop screen flickers',
        'description' => 'The screen starts flickering after a few minutes.',
    ])->assertCreated();

    $ticket = Ticket::query()->sole();

    expect($firstAdmin->notifications()->count())->toBe(1)
        ->and($secondAdmin->notifications()->count())->toBe(1)
        ->and($creator->notifications()->count())->toBe(0);

    $notification = $firstAdmin->notifications()->sole();

    expect($notification->data)->toMatchArray([
        'event' => 'ticket_created',
        'ticket_id' => $ticket->id,
        'title' => "New ticket #{$ticket->id}",
        'url' => route('tickets.show', $ticket),
    ]);
});

test('an admin ticket creator is excluded from new ticket alerts', function () {
    $creator = User::factory()->create(['role' => UserRole::Admin]);
    $otherAdmin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($creator)->postJson(route('tickets.store'), [
        'subject' => 'Admin laptop issue',
        'description' => 'The administrator has an issue with a laptop.',
    ])->assertCreated();

    expect($creator->notifications()->count())->toBe(0)
        ->and($otherAdmin->notifications()->count())->toBe(1);
});

test('status progression notifies the ticket owner unless they performed the action', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()->for($owner)->create();

    $this->actingAs($admin)->patchJson(route('admin.tickets.advance-status', $ticket), [
        'status' => TicketStatus::InProgress->value,
    ])->assertOk();

    $notification = $owner->notifications()->sole();

    expect($notification->data)->toMatchArray([
        'event' => 'ticket_status_advanced',
        'ticket_id' => $ticket->id,
        'url' => route('tickets.show', $ticket),
    ])->and($notification->data['message'])->toContain('Open → In Progress');
});

test('queued ticket notifications implement the queue contract', function () {
    expect(new TicketCreatedNotification(1, 'Email issue', 'Amina'))->toBeInstanceOf(ShouldQueue::class)
        ->and(new TicketStatusAdvancedNotification(1, 'Email issue', TicketStatus::Open, TicketStatus::InProgress))
        ->toBeInstanceOf(ShouldQueue::class);
});

test('users can manage only their own notifications', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $user->notify(new TicketStatusAdvancedNotification(
        $ticket->id,
        $ticket->subject,
        TicketStatus::Open,
        TicketStatus::InProgress,
    ));
    $otherUser->notify(new TicketStatusAdvancedNotification(
        $ticket->id,
        $ticket->subject,
        TicketStatus::Open,
        TicketStatus::InProgress,
    ));

    $notification = $user->notifications()->sole();
    $otherNotification = $otherUser->notifications()->sole();

    $this->actingAs($user)->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notifications')
        ->assertSee($notification->data['title']);

    $this->actingAs($user)->patch(route('notifications.read', $notification->id))->assertRedirect();

    $this->assertDatabaseMissing('notifications', ['id' => $notification->id, 'read_at' => null]);

    $this->actingAs($user)->patch(route('notifications.read', $otherNotification->id))->assertNotFound();

    $user->notify(new TicketStatusAdvancedNotification(
        $ticket->id,
        $ticket->subject,
        TicketStatus::InProgress,
        TicketStatus::Resolved,
    ));

    $this->actingAs($user)->patch(route('notifications.read-all'))->assertRedirect();

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});
