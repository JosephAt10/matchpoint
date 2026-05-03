@php
    $user = auth()->user();
    $initials = str($user?->name ?? 'Owner')
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
        ->implode('');
    $profileUrl = filament()->hasProfile() ? filament()->getProfileUrl() : '#';
@endphp

<a href="{{ $profileUrl }}" class="owner-sidebar-footer">
    <div class="owner-sidebar-footer__avatar">{{ $initials }}</div>
    <div class="owner-sidebar-footer__meta">
        <p class="owner-sidebar-footer__name">{{ $user?->name }}</p>
        <p class="owner-sidebar-footer__email">{{ $user?->email }}</p>
    </div>
    <span class="owner-sidebar-footer__chevron">›</span>
</a>
