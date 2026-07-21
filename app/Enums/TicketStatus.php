<?php

namespace App\Enums;

/**
 * The support lifecycle. Tickets can only move to the immediate next state.
 */
enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function next(): ?self
    {
        return match ($this) {
            self::Open => self::InProgress,
            self::InProgress => self::Resolved,
            self::Resolved => self::Closed,
            self::Closed => null,
        };
    }
}
