@extends('layouts.app')

@section('content')

<h1>Ticket Tracker</h1>

{{-- Stats bar — refreshes when ticketModified event fires from body --}}
<div id="stats-bar"
     hx-get="/tickets/stats"
     hx-trigger="ticketModified from:body"
     hx-swap="innerHTML">
    @include('tickets.partials.stats', ['stats' => $stats])
</div>

{{-- Create form --}}
<div class="form-card">
    <h2>New Ticket</h2>
    <form id="create-form"
          hx-post="/tickets"
          hx-target="#ticket-tbody"
          hx-swap="afterbegin"
          hx-on:htmx:after-request="if(event.detail.successful) this.reset()">
        @csrf
        <div class="form-row">
            <input type="text" name="title" placeholder="Title *" required>
            <select name="priority">
                @foreach(\App\Enums\TicketPriority::cases() as $p)
                    <option value="{{ $p->value }}"
                            {{ $p->value === 'medium' ? 'selected' : '' }}>
                        {{ $p->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit">+ Add Ticket</button>
        </div>
        <div class="form-row">
            <textarea name="description" rows="2" placeholder="Description (optional)"></textarea>
        </div>
    </form>
</div>

{{-- Ticket table --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody id="ticket-tbody">
            @include('tickets.partials.list', ['tickets' => $tickets])
        </tbody>
    </table>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-top:.75rem;font-size:.875rem;color:#6b7280;">
    <span>Showing {{ $tickets->count() }} of {{ $tickets->total() }} tickets &nbsp;·&nbsp; Page {{ $tickets->currentPage() }} of {{ $tickets->lastPage() }}</span>
    <div style="display:flex;gap:.5rem;">
        @if(!$tickets->onFirstPage())
            <a href="?page={{ $tickets->currentPage() - 1 }}" style="background:#fff;border:1px solid #d1d5db;border-radius:6px;padding:.35rem .75rem;color:#374151;text-decoration:none;">← Prev</a>
        @endif
        @if($tickets->hasMorePages())
            <a href="?page={{ $tickets->currentPage() + 1 }}" style="background:#fff;border:1px solid #d1d5db;border-radius:6px;padding:.35rem .75rem;color:#374151;text-decoration:none;">Next →</a>
        @endif
    </div>
</div>

@endsection
