@php
    $weeklyCounts = collect($dashboard['weekly_bookings'])->pluck('count');
    $maxWeeklyCount = max(6, $weeklyCounts->max() ?: 0);
    $chartWidth = 580;
    $chartHeight = 220;
    $leftPadding = 24;
    $topPadding = 18;
    $bottomPadding = 36;
    $usableHeight = $chartHeight - $topPadding - $bottomPadding;
    $stepX = count($dashboard['weekly_bookings']) > 1 ? ($chartWidth - ($leftPadding * 2)) / (count($dashboard['weekly_bookings']) - 1) : 0;
    $linePoints = collect($dashboard['weekly_bookings'])->values()->map(function (array $point, int $index) use ($bottomPadding, $chartHeight, $leftPadding, $maxWeeklyCount, $stepX, $topPadding, $usableHeight) {
        $x = $leftPadding + ($stepX * $index);
        $y = $chartHeight - $bottomPadding - (($point['count'] / $maxWeeklyCount) * $usableHeight);

        return [
            'x' => round($x, 2),
            'y' => round($y, 2),
            'count' => $point['count'],
            'label' => $point['label'],
        ];
    });
    $polyline = $linePoints->map(fn (array $point) => "{$point['x']},{$point['y']}")->implode(' ');
    $areaPolyline = $polyline . " {$linePoints->last()['x']}," . ($chartHeight - $bottomPadding) . " {$linePoints->first()['x']}," . ($chartHeight - $bottomPadding);

    $iconSvg = function (string $name): \Illuminate\Support\HtmlString {
        $icons = [
            'field' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16v10H4z"/><path d="M12 7v10"/><path d="M4 12h16"/><circle cx="12" cy="12" r="2.6"/></svg>',
            'shield-check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l7 3v6c0 4.5-3 7.7-7 9-4-1.3-7-4.5-7-9V6l7-3z"/><path d="M9.5 12.5l1.8 1.8 3.7-4"/></svg>',
            'document' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 3h6l5 5v13H8z"/><path d="M14 3v5h5"/><path d="M10 13h6M10 17h4"/></svg>',
            'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 8v5l3 2"/></svg>',
            'check-circle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M9 12.5l2 2 4-4"/></svg>',
            'badge-check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l2.1 2.4 3.2-.1.8 3 2.9 1.3-1.4 2.9 1.4 2.9-2.9 1.3-.8 3-3.2-.1L12 21l-2.1-2.4-3.2.1-.8-3-2.9-1.3 1.4-2.9-1.4-2.9 2.9-1.3.8-3 3.2.1z"/><path d="M9.4 12.4l1.7 1.8 3.6-4"/></svg>',
            'x-mark' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7l10 10M17 7L7 17"/></svg>',
            'plus' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg>',
            'list' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 7h10M9 12h10M9 17h10"/><circle cx="5" cy="7" r="1"/><circle cx="5" cy="12" r="1"/><circle cx="5" cy="17" r="1"/></svg>',
            'warning' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 4l8 14H4L12 4z"/><path d="M12 9v4M12 16h.01"/></svg>',
            'info' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 10v5M12 7h.01"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 17H5l1.2-1.6A4 4 0 0 0 7 13V10a5 5 0 1 1 10 0v3c0 .9.3 1.8.8 2.6L19 17h-4"/><path d="M10 18a2 2 0 0 0 4 0"/></svg>',
            'chevron-down' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 10l5 5 5-5"/></svg>',
            'chevron-right' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 7l5 5-5 5"/></svg>',
            'dots' => '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="12" cy="19" r="1.7"/></svg>',
        ];

        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['field']);
    };
@endphp

<div class="owner-dashboard space-y-6">
    <section class="owner-dashboard__hero flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <h1 class="owner-dashboard__title">Dashboard</h1>
            <p class="owner-dashboard__subtitle">Welcome back! Here's what's happening with your fields today.</p>
        </div>

        <div class="owner-dashboard__date-chip">
            <span class="owner-dashboard__date-icon">{!! $iconSvg('calendar') !!}</span>
            <span>{{ $dashboard['today_label'] }}</span>
            <span class="owner-dashboard__date-arrow">{!! $iconSvg('chevron-down') !!}</span>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1.9fr)_minmax(320px,0.9fr)]">
        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-5">
                @foreach ($dashboard['stats'] as $stat)
                    <article class="owner-stat-card owner-tone-{{ $stat['tone'] }}">
                        <div class="owner-stat-card__icon">
                            {!! $iconSvg($stat['icon']) !!}
                        </div>
                        <div class="owner-stat-card__content">
                            <p class="owner-stat-card__label">{{ $stat['label'] }}</p>
                            <p class="owner-stat-card__value">{{ $stat['value'] }}</p>
                            <p class="owner-stat-card__hint">{{ $stat['hint'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <article class="owner-panel-card owner-panel-card--chart">
                <div class="owner-panel-card__header">
                    <div>
                        <h2 class="owner-panel-card__title">Booking Overview</h2>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($dashboard['status_cards'] as $statusCard)
                        <div class="owner-status-card owner-tone-{{ $statusCard['tone'] }}">
                            <div class="owner-status-card__icon">
                                {!! $iconSvg($statusCard['icon']) !!}
                            </div>
                            <div>
                                <p class="owner-status-card__label">{{ $statusCard['label'] }}</p>
                                <p class="owner-status-card__value">{{ $statusCard['value'] }}</p>
                                <p class="owner-status-card__hint">{{ $statusCard['hint'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="owner-chart-card">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="owner-chart-card__title">Bookings This Week</h3>
                        <span class="owner-chart-card__tag">
                            <span>This Week</span>
                            <span class="owner-chart-card__tag-icon">{!! $iconSvg('chevron-down') !!}</span>
                        </span>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="owner-chart">
                            <defs>
                                <linearGradient id="owner-bookings-fill" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="rgba(34,197,94,0.35)" />
                                    <stop offset="100%" stop-color="rgba(34,197,94,0.02)" />
                                </linearGradient>
                            </defs>

                            <line x1="{{ $leftPadding }}" y1="{{ $chartHeight - $bottomPadding }}" x2="{{ $chartWidth - $leftPadding }}" y2="{{ $chartHeight - $bottomPadding }}" class="owner-chart__axis" />

                            <polygon points="{{ $areaPolyline }}" fill="url(#owner-bookings-fill)"></polygon>
                            <polyline points="{{ $polyline }}" class="owner-chart__line"></polyline>

                            @foreach ($linePoints as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4.5" class="owner-chart__dot"></circle>
                                <text x="{{ $point['x'] }}" y="{{ $chartHeight - 10 }}" text-anchor="middle" class="owner-chart__label">{{ $point['label'] }}</text>
                            @endforeach
                        </svg>
                    </div>
                </div>
            </article>

            <article class="owner-panel-card">
                <div class="owner-panel-card__header">
                    <div>
                        <h2 class="owner-panel-card__title">My Fields</h2>
                        <p class="owner-panel-card__copy">Overview of your venues and their status.</p>
                    </div>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    @forelse ($dashboard['my_fields'] as $field)
                        <article class="owner-field-card">
                            <div class="owner-field-card__main">
                                <div
                                    class="owner-field-card__cover"
                                    style="{{ $field['image_url'] ? "background-image: linear-gradient(180deg, rgba(3, 7, 18, 0.15), rgba(3, 7, 18, 0.72)), url('{$field['image_url']}'); background-size: cover; background-position: center;" : "background: {$field['surface_style']}" }}"
                                >
                                </div>

                                <div class="owner-field-card__body">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="owner-field-card__title">{{ $field['name'] }}</h3>
                                            <p class="owner-field-card__meta">{{ $field['location'] }}</p>
                                        </div>

                                        <span class="owner-field-card__approval owner-field-card__approval--{{ $field['approved'] ? 'approved' : ($field['approval_status'] === 'Rejected' ? 'pending' : 'pending') }}">
                                            {{ $field['approval_status'] }}
                                        </span>
                                    </div>

                                    <span class="owner-field-card__sport-pill" style="background: {{ $field['accent_style'] }}">{{ $field['sport'] }}</span>

                                    <div class="owner-field-card__details">
                                        <span class="owner-field-card__detail-item">
                                            <span class="owner-field-card__detail-dot"></span>
                                            <span>{{ $field['type'] }}</span>
                                        </span>
                                        <span class="owner-field-card__detail-item">
                                            <span class="owner-field-card__detail-dot"></span>
                                            <span>{{ $field['price'] }}</span>
                                        </span>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <p class="owner-field-card__metric-label">Occupancy Rate</p>
                                            <p class="owner-field-card__metric-value">{{ $field['occupancy_rate'] }}%</p>
                                            <div class="owner-progress">
                                                <span class="owner-progress__bar" style="width: {{ $field['occupancy_rate'] }}%; background: {{ $field['accent_style'] }}"></span>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="owner-field-card__metric-label">Slots Booked</p>
                                            <p class="owner-field-card__metric-value">{{ $field['weekly_booked_slots'] }} / {{ $field['time_slots'] * 7 }}</p>
                                            <div class="owner-progress">
                                                <span class="owner-progress__bar" style="width: {{ min(100, round(($field['weekly_booked_slots'] / max(1, $field['time_slots'] * 7)) * 100)) }}%; background: {{ $field['accent_style'] }}"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="owner-field-card__footer">
                                <a href="{{ $field['bookings_url'] }}" class="owner-field-card__cta">View Bookings</a>
                                <a href="{{ $field['edit_url'] }}" class="owner-field-card__menu" aria-label="Edit {{ $field['name'] }}">
                                    {!! $iconSvg('dots') !!}
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="owner-empty-state xl:col-span-2">
                            <h3>No fields yet</h3>
                            <p>Add your first field to start configuring availability and receiving bookings.</p>
                            <a href="{{ \App\Filament\Owner\Resources\FieldResource::getUrl('create') }}">Create your first field</a>
                        </div>
                    @endforelse
                </div>
            </article>
        </div>

        <div class="space-y-4">
            <article class="owner-panel-card">
                <div class="owner-panel-card__header">
                    <div>
                        <h2 class="owner-panel-card__title">Quick Actions</h2>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($dashboard['quick_actions'] as $action)
                        <a href="{{ $action['url'] }}" class="owner-action-card owner-tone-{{ $action['tone'] }}">
                            <span class="owner-action-card__icon">
                                {!! $iconSvg($action['icon']) !!}
                            </span>
                            <div class="owner-action-card__body">
                                <p class="owner-action-card__title">{{ $action['label'] }}</p>
                                <p class="owner-action-card__copy">{{ $action['description'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </article>

            <article class="owner-panel-card">
                <div class="owner-panel-card__header">
                    <div>
                        <h2 class="owner-panel-card__title">Alerts</h2>
                    </div>
                </div>

                <div class="space-y-2">
                    @forelse ($dashboard['alerts'] as $alert)
                        <a href="{{ $alert['url'] }}" class="owner-alert-card owner-tone-{{ $alert['tone'] }}">
                            <span class="owner-alert-card__icon">
                                {!! $iconSvg($alert['icon']) !!}
                            </span>
                            <div class="owner-alert-card__body">
                                <p class="owner-alert-card__title">{{ $alert['title'] }}</p>
                                <p class="owner-alert-card__copy">{{ $alert['subtitle'] }}</p>
                            </div>
                            <span class="owner-alert-card__arrow">{!! $iconSvg('chevron-right') !!}</span>
                        </a>
                    @empty
                        <div class="owner-inline-empty">
                            No urgent alerts right now.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="owner-panel-card">
                <div class="owner-panel-card__header">
                    <div>
                        <h2 class="owner-panel-card__title">Today's Schedule (All Fields)</h2>
                    </div>

                    <a href="{{ $dashboard['schedule_url'] }}" class="owner-panel-card__link">View All</a>
                </div>

                <div class="space-y-2">
                    @forelse ($dashboard['today_schedule'] as $slot)
                        <div class="owner-schedule-row">
                            <div class="owner-schedule-row__summary">
                                <span class="owner-schedule-row__dot owner-schedule-row__dot--{{ $slot['tone'] }}"></span>
                                <div>
                                    <p class="owner-schedule-row__time">{{ $slot['range'] }}</p>
                                    <p class="owner-schedule-row__field">{{ $slot['field'] }}</p>
                                </div>
                            </div>

                            <span class="owner-schedule-row__status owner-schedule-row__status--{{ $slot['tone'] }}">
                                {{ $slot['status'] }}
                            </span>
                        </div>
                    @empty
                        <div class="owner-inline-empty">
                            No configured schedule yet. Add time slots to start taking bookings.
                        </div>
                    @endforelse

                    @if ($dashboard['today_schedule_overflow'] > 0)
                        <a href="{{ $dashboard['schedule_url'] }}" class="owner-schedule-row owner-schedule-row--more">
                            <span class="owner-schedule-row__more-copy">{{ $dashboard['today_schedule_overflow'] }} more slots available</span>
                            <span class="owner-alert-card__arrow">{!! $iconSvg('chevron-right') !!}</span>
                        </a>
                    @endif
                </div>
            </article>
        </div>
    </section>
</div>
