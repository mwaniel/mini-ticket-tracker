
<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Stats
Route::get('/tickets/stats', [TicketController::class, 'stats']);

// The 3 required API endpoints
Route::apiResource('tickets', TicketController::class)
    ->only(['index', 'store', 'update']);
