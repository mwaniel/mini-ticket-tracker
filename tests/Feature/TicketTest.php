<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ──────────────────────────────────────────
// POST /api/tickets
// ──────────────────────────────────────────

it('creates a ticket and returns 201', function () {
    $res = $this->postJson('/api/tickets', [
        'title' => 'Broken login',
        'description' => 'Users get 500 on Google OAuth',
        'priority' => 'high',
    ]);

    $res->assertCreated()
        ->assertJsonPath('data.title', 'Broken login')
        ->assertJsonPath('data.status', 'open')     // default
        ->assertJsonPath('data.priority', 'high');

    $this->assertDatabaseHas('tickets', ['title' => 'Broken login']);
});

it('rejects a ticket with no title', function () {
    $this->postJson('/api/tickets', ['description' => 'no title here'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

it('rejects an invalid priority value', function () {
    $this->postJson('/api/tickets', ['title' => 'Test', 'priority' => 'critical'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('priority');
});

it('rejects title longer than 255 chars', function () {
    $this->postJson('/api/tickets', ['title' => str_repeat('a', 256)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

// ──────────────────────────────────────────
// GET /api/tickets
// ──────────────────────────────────────────

it('lists all tickets with pagination meta', function () {
    Ticket::factory()->count(3)->create();

    $res = $this->getJson('/api/tickets');

    $res->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data', 'links', 'meta']);
});

it('filters tickets by status', function () {
    Ticket::factory()->count(3)->open()->create();
    Ticket::factory()->count(2)->closed()->create();

    $res = $this->getJson('/api/tickets?status=open');

    $res->assertOk()->assertJsonCount(3, 'data');
    collect($res->json('data'))->each(
        fn($t) => expect($t['status'])->toBe('open')
    );
});

it('paginates with custom limit', function () {
    Ticket::factory()->count(20)->create();

    $res = $this->getJson('/api/tickets?limit=5');

    $res->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.total', 20);
});

it('returns 422 for an invalid status filter', function () {
    $this->getJson('/api/tickets?status=unknown')
        ->assertUnprocessable();
});

// ──────────────────────────────────────────
// PATCH /api/tickets/{id}
// ──────────────────────────────────────────

it('updates ticket status', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->patchJson("/api/tickets/{$ticket->id}", ['status' => 'closed'])
        ->assertOk()
        ->assertJsonPath('data.status', 'closed');

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'status' => 'closed',
    ]);
});

it('updates ticket priority', function () {
    $ticket = Ticket::factory()->create(['priority' => 'low']);

    $this->patchJson("/api/tickets/{$ticket->id}", ['priority' => 'high'])
        ->assertOk()
        ->assertJsonPath('data.priority', 'high');
});

it('rejects an invalid status on update', function () {
    $ticket = Ticket::factory()->create();

    $this->patchJson("/api/tickets/{$ticket->id}", ['status' => 'broken'])
        ->assertUnprocessable();
});

it('returns 404 for a non-existent ticket', function () {
    $this->patchJson('/api/tickets/9999', ['status' => 'closed'])
        ->assertNotFound();
});

// ──────────────────────────────────────────
// GET /api/tickets/stats
// ──────────────────────────────────────────

it('returns ticket counts grouped by status', function () {
    Ticket::factory()->count(4)->open()->create();
    Ticket::factory()->count(2)->inProgress()->create();
    Ticket::factory()->count(1)->closed()->create();

    $this->getJson('/api/tickets/stats')
        ->assertOk()
        ->assertExactJson([
            'open' => 4,
            'in_progress' => 2,
            'closed' => 1,
        ]);
});

it('returns zero counts when no tickets exist', function () {
    $this->getJson('/api/tickets/stats')
        ->assertOk()
        ->assertExactJson([
            'open' => 0,
            'in_progress' => 0,
            'closed' => 0,
        ]);
});

it('stats reflect status updates immediately', function () {
    $ticket = Ticket::factory()->open()->create();

    $this->getJson('/api/tickets/stats')
        ->assertJsonPath('open', 1)
        ->assertJsonPath('closed', 0);

    $this->patchJson("/api/tickets/{$ticket->id}", ['status' => 'closed']);

    $this->getJson('/api/tickets/stats')
        ->assertJsonPath('open', 0)
        ->assertJsonPath('closed', 1);
});