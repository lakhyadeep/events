@php
    $breakdown = $breakdown->reject(function ($item) {
        $name = strtolower($item['name'] ?? '');
        $displayName = strtolower($item['display_name'] ?? '');

        return str_contains($name, 'best puja') || str_contains($displayName, 'best puja');
    })->values();

    $totalVotes = $breakdown->sum('votes_count');

    $breakdown = $breakdown->map(function ($item) use ($totalVotes) {
        $item['percentage'] = $totalVotes > 0 ? round(($item['votes_count'] / $totalVotes) * 100, 1) : 0.0;

        return $item;
    });

    $leadingCategory = $breakdown->sortByDesc('votes_count')->first();
    $colors = [
        '#10b981', // Emerald (Best Pratima / Art & Sculpture)
        '#6366f1', // Indigo (Best Theme / Social Message)
        '#0ea5e9', // Sky (Best Pandal / Architecture)
        '#f59e0b', // Amber
        '#ec4899', // Pink
        '#8b5cf6', // Violet
        '#f43f5e', // Rose
    ];

    $chartDatasets = $breakdown->map(function ($item, $index) use ($colors) {
        $color = $colors[$index % count($colors)];
        $catLabel = $item['category'] ? ' (' . $item['category'] . ')' : '';

        return [
            'label' => $item['display_name'] . $catLabel,
            'data' => [(int) $item['votes_count']],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'borderWidth' => 1,
            'borderRadius' => 6,
            'maxBarThickness' => 48,
        ];
    })->values()->all();

    $contenderLabel = $record->display_name ?: $record->name;
@endphp

<div class="space-y-6 py-2">
    {{-- Contender Quick Summary Banner --}}
    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-amber-50/60 via-white to-zinc-50 p-5 shadow-sm dark:border-zinc-800 dark:from-zinc-900 dark:via-zinc-900/90 dark:to-zinc-950">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                @if ($record->primary_image_url)
                    <img
                        src="{{ $record->primary_image_url }}"
                        alt="{{ $record->display_name }}"
                        class="h-14 w-14 rounded-xl object-cover ring-2 ring-amber-500/20 shadow-sm"
                    />
                @else
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/20 dark:text-amber-400">
                        <x-filament::icon icon="heroicon-o-trophy" class="h-7 w-7" />
                    </div>
                @endif

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ $record->display_name ?: $record->name }}
                        </h3>
                        @if ($record->is_shortlisted)
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                <x-filament::icon icon="heroicon-s-star" class="h-3.5 w-3.5" />
                                Shortlisted
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        <x-filament::icon icon="heroicon-m-map-pin" class="inline h-3.5 w-3.5 mr-0.5 text-zinc-400" />
                        {{ $record->locality ? $record->locality . ', ' . $record->zone : ($record->zone ?: 'Dibrugarh') }}
                        @if ($record->event)
                            <span class="mx-1.5 text-zinc-300 dark:text-zinc-700">&bull;</span>
                            <span>{{ $record->event->display_name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 sm:text-right">
                <div class="rounded-xl border border-rose-200 bg-rose-50/70 px-3.5 py-2 dark:border-rose-900/50 dark:bg-rose-950/40">
                    <span class="text-xs font-medium text-rose-600 dark:text-rose-400 block">Total Verified Votes</span>
                    <span class="text-xl font-extrabold text-rose-700 dark:text-rose-300">
                        {{ number_format($totalVotes) }}
                    </span>
                </div>

                @if ($leadingCategory && $leadingCategory['votes_count'] > 0)
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 px-3.5 py-2 dark:border-emerald-900/50 dark:bg-emerald-950/40">
                        <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 block">Leading Category</span>
                        <span class="text-sm font-bold text-emerald-700 dark:text-emerald-300">
                            {{ $leadingCategory['display_name'] }} ({{ $leadingCategory['votes_count'] }})
                        </span>
                    </div>
                @endif
            </div>
        </div>

        @if ($startDate || $endDate)
            <div class="mt-3 flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400 bg-amber-100/60 dark:bg-amber-950/40 rounded-lg px-2.5 py-1 w-fit">
                <x-filament::icon icon="heroicon-m-funnel" class="h-3.5 w-3.5" />
                <span>Filtered Range: <strong>{{ $startDate ? $startDate->format('M d, H:i') : 'Start' }}</strong> &rarr; <strong>{{ $endDate ? $endDate->format('M d, H:i') : 'Now' }}</strong></span>
            </div>
        @endif
    </div>

    {{-- Horizontal Stacked Bar Chart Section --}}
    <div
        x-data="{
            chart: null,
            init() {
                this.$nextTick(() => {
                    this.renderChart();
                });
            },
            renderChart() {
                const canvas = this.$refs.chartCanvas;
                if (!canvas) return;

                const datasets = @js($chartDatasets);
                const contender = @js($contenderLabel);
                const totalVotes = @js($totalVotes);
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#d4d4d8' : '#3f3f46';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

                // If Chart.js is available globally
                if (typeof Chart !== 'undefined') {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    this.chart = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: ['Category Votes'],
                            datasets: datasets,
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        color: textColor,
                                        padding: 14,
                                        font: { size: 11, weight: 600 }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const val = context.parsed.x || 0;
                                            const pct = totalVotes > 0 ? ((val / totalVotes) * 100).toFixed(1) : 0;
                                            return ` ${context.dataset.label}: ${val} votes (${pct}%)`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    beginAtZero: true,
                                    grid: { color: gridColor },
                                    ticks: {
                                        color: textColor,
                                        precision: 0,
                                    }
                                },
                                y: {
                                    stacked: true,
                                    grid: { display: false },
                                    ticks: {
                                        color: textColor,
                                        font: { size: 12, weight: 600 },
                                    }
                                }
                            }
                        },
                        plugins: [{
                            id: 'stackedDataLabels',
                            afterDatasetsDraw(chart) {
                                const { ctx } = chart;
                                ctx.save();
                                ctx.font = 'bold 12px system-ui, -apple-system, sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';

                                chart.data.datasets.forEach((dataset, i) => {
                                    const meta = chart.getDatasetMeta(i);
                                    if (meta.hidden) return;

                                    meta.data.forEach((element, idx) => {
                                        const val = dataset.data[idx];
                                        if (val && val > 0) {
                                            const { x, y, base } = element;
                                            const width = Math.abs(x - base);
                                            if (width >= 18) {
                                                const posX = base + (x - base) / 2;
                                                ctx.fillStyle = '#ffffff';
                                                ctx.shadowColor = 'rgba(0, 0, 0, 0.6)';
                                                ctx.shadowBlur = 3;
                                                ctx.fillText(val, posX, y);
                                            }
                                        }
                                    });
                                });
                                ctx.restore();
                            }
                        }]
                    });
                }
            }
        }"
        class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
    >
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                    <x-filament::icon icon="heroicon-m-chart-bar" class="h-4 w-4" />
                </div>
                <div>
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">
                        Stacked Award Category Vote Counts
                    </h4>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Category vote counts stacked directly on the bar with category breakdown
                    </p>
                </div>
            </div>
            <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                Stacked Horizontal Bar
            </span>
        </div>

        {{-- Canvas Container for Stacked Chart.js --}}
        <div class="relative h-40 w-full mb-4">
            <canvas x-ref="chartCanvas"></canvas>
        </div>

        {{-- Interactive Segmented Stacked Progress Bar Visual --}}
        <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                <span>Stacked Segment Breakdown</span>
                <span>{{ number_format($totalVotes) }} {{ Str::plural('vote', $totalVotes) }} total</span>
            </div>

            @if ($totalVotes > 0)
                <div class="h-9 w-full flex overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800 p-1 gap-1 border border-zinc-200 dark:border-zinc-700/60 shadow-inner">
                    @foreach ($breakdown as $index => $item)
                        @if ($item['votes_count'] > 0)
                            @php $segColor = $colors[$index % count($colors)]; @endphp
                            <div
                                style="width: {{ $item['percentage'] }}%; background-color: {{ $segColor }};"
                                class="h-full rounded-lg flex items-center justify-center text-xs font-extrabold text-white shadow-xs transition-all duration-300 hover:brightness-110 cursor-default"
                                title="{{ $item['display_name'] }} ({{ $item['category'] }}): {{ $item['votes_count'] }} votes ({{ $item['percentage'] }}%)"
                            >
                                <span class="truncate px-1.5 drop-shadow-sm flex items-center gap-1">
                                    <span>{{ $item['votes_count'] }}</span>
                                    <span class="text-[10px] font-normal opacity-90 hidden sm:inline">({{ $item['display_name'] }})</span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="h-9 w-full rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs text-zinc-400">
                    No votes recorded yet for this contender
                </div>
            @endif
        </div>
    </div>

    {{-- Individual Category Progress Bars & Share --}}
    <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
            Category Breakdown & Vote Share
        </h4>

        <div class="space-y-4">
            @forelse ($breakdown as $index => $item)
                @php
                    $color = $colors[$index % count($colors)];
                    $isTop = $leadingCategory && $leadingCategory['award_id'] === $item['award_id'] && $item['votes_count'] > 0;
                @endphp
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-3 w-3 rounded-full shadow-sm" style="background-color: {{ $color }};"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                {{ $item['display_name'] }}
                            </span>
                            <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $item['category'] }}
                            </span>
                            @if ($isTop)
                                <span class="rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                    Top Category
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-zinc-900 dark:text-zinc-100">
                                {{ number_format($item['votes_count']) }} {{ Str::plural('vote', $item['votes_count']) }}
                            </span>
                            <span class="text-zinc-400 dark:text-zinc-500">({{ $item['percentage'] }}%)</span>
                        </div>
                    </div>

                    {{-- Accessible Animated Progress Bar --}}
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div
                            class="h-full rounded-full transition-all duration-700 ease-out"
                            style="width: {{ max($item['percentage'], $item['votes_count'] > 0 ? 3 : 0) }}%; background-color: {{ $color }};"
                        ></div>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    No award categories configured or active.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Detailed Category Data Table --}}
    <div class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-zinc-100 bg-zinc-50/80 text-zinc-600 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="py-2.5 px-4 font-semibold">Award Category</th>
                    <th scope="col" class="py-2.5 px-4 font-semibold">Classification</th>
                    <th scope="col" class="py-2.5 px-4 text-right font-semibold">Verified Votes</th>
                    <th scope="col" class="py-2.5 px-4 text-right font-semibold">Share</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-zinc-800 dark:text-zinc-200">
                @foreach ($breakdown as $index => $item)
                    @php $color = $colors[$index % count($colors)]; @endphp
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                        <td class="py-2.5 px-4 font-medium flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full" style="background-color: {{ $color }};"></span>
                            <span>{{ $item['display_name'] }}</span>
                        </td>
                        <td class="py-2.5 px-4 text-zinc-500 dark:text-zinc-400">
                            {{ $item['category'] }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($item['votes_count']) }}
                        </td>
                        <td class="py-2.5 px-4 text-right text-zinc-500 dark:text-zinc-400 font-mono">
                            {{ $item['percentage'] }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-zinc-200 bg-zinc-50/60 font-semibold dark:border-zinc-800 dark:bg-zinc-800/40">
                <tr>
                    <td class="py-2.5 px-4 text-zinc-900 dark:text-zinc-100" colspan="2">Total Verified Public Votes</td>
                    <td class="py-2.5 px-4 text-right font-extrabold text-rose-600 dark:text-rose-400">
                        {{ number_format($totalVotes) }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-mono text-zinc-700 dark:text-zinc-300">
                        {{ $totalVotes > 0 ? '100%' : '0%' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
