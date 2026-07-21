@props(['status'])

@php
    $value = $status instanceof \App\Enums\TicketStatus ? $status->value : $status;
    $classes = match ($value) {
        'open' => 'badge-info',
        'in_progress' => 'badge-warning',
        'resolved' => 'badge-success',
        'closed' => 'badge-neutral',
        default => 'badge-ghost',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge {$classes}"]) }}>{{ str($value)->replace('_', ' ')->title() }}</span>
