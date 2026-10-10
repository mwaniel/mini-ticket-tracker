<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open       = 'open';
    case InProgress = 'in_progress';
    case Closed     = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Open       => 'Open',
            self::InProgress => 'In Progress',
            self::Closed     => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Open       => 'badge-open',
            self::InProgress => 'badge-in-progress',
            self::Closed     => 'badge-closed',
        };
    }
}
