<?php

namespace App\Enums;

/**
 * The support lifecycle. Status transitions will be enforced by admin-only
 * ticket actions when CRUD functionality is added.
 */
enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';
}
