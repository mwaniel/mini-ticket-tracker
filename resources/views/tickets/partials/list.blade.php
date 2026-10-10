@forelse($tickets as $ticket)
    @include('tickets.partials.row', ['ticket' => $ticket])
@empty
    <tr>
        <td colspan="5" style="text-align:center;padding:2.5rem;color:#9ca3af;">
            No tickets yet. Create one above.
        </td>
    </tr>
@endforelse
