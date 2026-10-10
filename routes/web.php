<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;


// Full page
Route::get('/', [TicketController::class, 'index'])->name('tickets.index');

// HTMX endpoints
Route::get('/tickets/stats',      [TicketController::class, 'stats'])->name('tickets.stats');
Route::post('/tickets',           [TicketController::class, 'store'])->name('tickets.store');
Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
