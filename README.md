# Mini Ticket Tracker

A lightweight support ticket tracker built with Laravel 12, PostgreSQL, Blade, and HTMX.

## Stack

| Layer | Choice | Why |
|-------|--------|-----|
| Backend | Laravel 12 / PHP 8.3 | Rapid API development, built-in validation, Eloquent, API resources |
| Database | PostgreSQL | Production-grade, reliable with Laravel |
| Frontend | Blade + HTMX 1.9 | No build step, no JS framework — real-time UI via HTML-over-the-wire |
| Testing | Pest | Clean syntax for feature tests |

## Requirements

- PHP 8.3+
- Composer
- PostgreSQL

## Setup

```bash
git clone https://github.com/mwaniel/mini-ticket-tracker.git
cd mini-ticket-tracker
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mini_ticket_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database and run migrations:

```bash
createdb mini_ticket_tracker
php artisan migrate
```

Optionally seed sample data (10 tickets across all statuses):

```bash
php artisan db:seed
```

Start the server:

```bash
php artisan serve
```

Open [http://127.0.0.1:8000]

## Running Tests

Tests require a separate PostgreSQL database:

```bash
createdb mini_ticket_tracker_test
```

Update `phpunit.xml` with your test DB credentials (already configured for `mini_ticket_tracker_test`), then:

```bash
php artisan test
```

Expected output: **15 passing tests** covering all four API endpoints.

## API Endpoints

All endpoints return `application/json` when called with `Accept: application/json`.

### Create a ticket
```
POST /api/tickets
```
Body:
```json
{
  "title": "Login page broken",
  "description": "Users get 500 on Google OAuth",
  "priority": "high"
}
```
Response `201`:
```json
{
  "data": {
    "id": 1,
    "title": "Login page broken",
    "description": "Users get 500 on Google OAuth",
    "status": "open",
    "priority": "high",
    "created_at": "2026-10-10T14:00:00+00:00"
  }
}
```

### List tickets
```
GET /api/tickets?status=open&page=1&limit=15
```
- `status` — optional filter: `open`, `in_progress`, `closed`. Returns 422 if invalid.
- `limit` — items per page, defaults to 15
- `page` — page number, defaults to 1

Response `200` — wrapped in `data` with `links` and `meta` pagination.

### Update a ticket
```
PATCH /api/tickets/{id}
```
Body (partial — send one or both):
```json
{
  "status": "in_progress",
  "priority": "low"
}
```
Response `200` — updated ticket resource.

### Stats
```
GET /api/tickets/stats
```
Response `200`:
```json
{
  "open": 5,
  "in_progress": 2,
  "closed": 1
}
```
Always returns all three statuses, including zeros.

## Valid Values

| Field | Values |
|-------|--------|
| `status` | `open`, `in_progress`, `closed` |
| `priority` | `low`, `medium`, `high` |

## UI

The web interface at `/` is built with Blade + HTMX:

- **Stats bar** — auto-refreshes after every create or status change (no polling — driven by `HX-Trigger` response header)
- **Create form** — submits via HTMX, new row prepended to the table without a page reload
- **Status dropdown** — inline PATCH on change, row swaps in place
- **Pagination** — prev/next page links

## Project Structure

```
app/
├── Enums/
│   ├── TicketStatus.php       # open | in_progress | closed
│   └── TicketPriority.php     # low | medium | high
├── Http/
│   ├── Controllers/
│   │   └── TicketController.php
│   ├── Requests/
│   │   ├── StoreTicketRequest.php
│   │   └── UpdateTicketRequest.php
│   └── Resources/
│       └── TicketResource.php
└── Models/
    └── Ticket.php
database/
├── factories/TicketFactory.php
├── migrations/
└── seeders/TicketSeeder.php
resources/views/
├── layouts/app.blade.php
└── tickets/
    ├── index.blade.php
    └── partials/
        ├── list.blade.php
        ├── row.blade.php
        └── stats.blade.php
routes/
├── api.php    # JSON API endpoints
└── web.php    # Blade + HTMX routes
tests/
└── Feature/TicketTest.php
```

## Assumptions

- **No authentication** — all endpoints are public; auth was not in scope
- **Default status** = `open`, **default priority** = `medium` on create
- **Status transitions are unrestricted** — any status to any status via PATCH, no state machine
- **PATCH is partial** — send only the fields you want to change
- **Stats always includes all three statuses** including zeros, so the UI renders consistently
- **Invalid `?status=` filter returns 422** — not an empty list; clearer for API consumers
- **Integer IDs** — not UUIDs; this is a single-instance app, not a distributed system
- **String columns for status/priority** — not DB enums; PHP enums enforce valid values at the application layer and string columns migrate cleanly across MySQL and PostgreSQL
- **No DELETE endpoint** — not required per the brief
- **No soft deletes**
- **Timestamps in UTC, ISO-8601 in JSON**
- **`/api/tickets/stats` registered before the resource routes** — avoids Laravel treating "stats" as a ticket ID

## Decisions & Tradeoffs

**Blade + HTMX over a JS framework** — the brief said not to over-build. HTMX delivers real-time row swaps and stats updates with zero JavaScript written and no build toolchain. The API routes work independently and return JSON for any client that sends `Accept: application/json`. If the team prefers React or Vue, the API layer requires no changes.

**One controller for both web and API** — the controller checks `$request->wantsJson()` to decide whether to return a JSON resource or a Blade partial. This keeps the codebase lean without duplicating validation or business logic.

**String columns not DB enums** — PHP 8.1 backed enums (`TicketStatus`, `TicketPriority`) enforce valid values at the application layer. String columns are portable across MySQL and PostgreSQL and don't require `ALTER TABLE` to add a new status later.

**Feature tests only** — tests cover the HTTP contract for all four API endpoints. No browser or HTMX integration tests — not worth the setup investment for a take-home. 15 tests, all green.

**PostgreSQL for tests** — the local PHP environment (Conda) does not have `pdo_sqlite` compiled in. Using a dedicated `mini_ticket_tracker_test` PostgreSQL database keeps tests consistent with the production driver.

## What I'd Add With More Time

- **Laravel Sanctum** — token-based auth with `user_id` on tickets
- **Status transition rules** — state machine (e.g. `closed → open` requires a reason)
- **Soft deletes + DELETE endpoint**
- **Cursor-based pagination** — more efficient for large datasets
- **Sorting** — by priority, status, created date
- **Full-text search** on title and description
- **GitHub Actions CI** — run `php artisan test` on every push
- **OpenAPI/Swagger docs** generated from the routes
- **Rate limiting** on the API
```
