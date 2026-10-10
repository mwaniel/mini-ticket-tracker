<tr id="ticket-{{ $ticket->id }}">
    <td style="color:#9ca3af;">#{{ $ticket->id }}</td>
    <td>
        <div class="ticket-title">{{ $ticket->title }}</div>
        @if($ticket->description)
            <div class="ticket-desc">{{ Str::limit($ticket->description, 70) }}</div>
        @endif
    </td>
    <td>
        <span class="badge {{ $ticket->priority->badgeClass() }}">
            {{ $ticket->priority->label() }}
        </span>
    </td>
    <td>
        {{-- HTMX: on change, PATCH this ticket and swap the whole row --}}
        <select class="status-select"
                name="status"
                hx-patch="/tickets/{{ $ticket->id }}"
                hx-target="#ticket-{{ $ticket->id }}"
                hx-swap="outerHTML"
                hx-trigger="change">
            @foreach(\App\Enums\TicketStatus::cases() as $s)
                <option value="{{ $s->value }}"
                        {{ $ticket->status === $s ? 'selected' : '' }}>
                    {{ $s->label() }}
                </option>
            @endforeach
        </select>
    </td>
    <td style="color:#9ca3af;white-space:nowrap;">{{ $ticket->created_at->diffForHumans() }}</td>
</tr>
