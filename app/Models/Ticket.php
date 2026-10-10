<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
    ];
    protected $attributes = [
        'status'   => 'open',
        'priority' => 'medium',
    ];

    protected $casts = [
        'status'   => TicketStatus::class,
        'priority' => TicketPriority::class,
    ];
}
