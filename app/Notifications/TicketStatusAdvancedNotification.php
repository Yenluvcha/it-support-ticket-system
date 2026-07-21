<?php

namespace App\Notifications;

use App\Enums\TicketStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketStatusAdvancedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $ticketId,
        private readonly string $subject,
        private readonly TicketStatus $previousStatus,
        private readonly TicketStatus $newStatus,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $previous = str($this->previousStatus->value)->replace('_', ' ')->title();
        $new = str($this->newStatus->value)->replace('_', ' ')->title();

        return [
            'event' => 'ticket_status_advanced',
            'ticket_id' => $this->ticketId,
            'title' => "Ticket #{$this->ticketId} status updated",
            'message' => "{$this->subject}: {$previous} → {$new}",
            'url' => route('tickets.show', $this->ticketId),
        ];
    }
}
