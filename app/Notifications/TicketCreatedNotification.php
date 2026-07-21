<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $ticketId,
        private readonly string $subject,
        private readonly string $creatorName,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'ticket_created',
            'ticket_id' => $this->ticketId,
            'title' => "New ticket #{$this->ticketId}",
            'message' => "{$this->creatorName} created: {$this->subject}",
            'url' => route('tickets.show', $this->ticketId),
        ];
    }
}
