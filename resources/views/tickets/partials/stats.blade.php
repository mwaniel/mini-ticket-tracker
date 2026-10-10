<div class="stats">
    @foreach(\App\Enums\TicketStatus::cases() as $s)
        <div class="stat-card">
            <div class="count">{{ $stats[$s->value] ?? 0 }}</div>
            <div class="label">{{ $s->label() }}</div>
        </div>
    @endforeach
</div>
