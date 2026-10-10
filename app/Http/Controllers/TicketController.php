<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->string('status'))
            )
            ->latest()
            ->paginate($request->integer('limit', 15));
        if ($request->filled('status')) {
        $request->validate([
            'status' => [Rule::enum(TicketStatus::class)],
            ]);
        }
        if ($request->wantsJson()) {
            return TicketResource::collection($tickets);
        }

        if ($request->header('HX-Request')) {
            return view('tickets.partials.list', compact('tickets'));
        }

        // Full page load
        $stats = $this->buildStats();
        return view('tickets.index', compact('tickets', 'stats'));
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create($request->validated());

        if ($request->wantsJson()) {
            return (new TicketResource($ticket))
                ->response()
                ->setStatusCode(201);
        }

        return response()
            ->view('tickets.partials.row', compact('ticket'))
            ->header('HX-Trigger', 'ticketModified');
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->validated());
        $ticket->refresh();

        if ($request->wantsJson()) {
            return new TicketResource($ticket);
        }

        return response()
            ->view('tickets.partials.row', compact('ticket'))
            ->header('HX-Trigger', 'ticketModified');
    }

    public function stats(Request $request)
    {
        $stats = $this->buildStats();

        if ($request->wantsJson()) {
            return response()->json($stats);
        }

        return view('tickets.partials.stats', compact('stats'));
    }

    private function buildStats(): array
    {
        $counts = Ticket::query()
            ->selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        return collect(TicketStatus::cases())
            ->mapWithKeys(fn($s) => [$s->value => (int) ($counts[$s->value] ?? 0)])
            ->all();
    }
}
