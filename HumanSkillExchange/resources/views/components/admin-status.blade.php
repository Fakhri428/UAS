@props(['status' => 'pending'])

@php
    $s = strtolower((string) $status);
    $map = [
        'pending'   => 'bg-brand-yellow',
        'approved'  => 'bg-brand-lime',
        'accepted'  => 'bg-brand-lime',
        'completed' => 'bg-brand-lime',
        'paid'      => 'bg-brand-lime',
        'open'      => 'bg-brand-sky',
        'declined'  => 'bg-brand-pink',
        'rejected'  => 'bg-brand-pink',
        'cancelled' => 'bg-brand-pink',
        'failed'    => 'bg-brand-pink',
    ];
    $bg = $map[$s] ?? 'bg-white';
@endphp

<span class="nb-badge {{ $bg }}">{{ ucfirst($s ?: '—') }}</span>
