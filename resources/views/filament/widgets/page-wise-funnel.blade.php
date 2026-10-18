<x-filament::section class="overflow-hidden">
    {{-- Section Header --}}
    <x-slot name="heading">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center shrink-0">
                    <x-filament::icon icon="heroicon-o-funnel" class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-base font-bold text-gray-950 dark:text-white">
                        Page-Wise Tracking & Voting Conversion Funnel
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-normal">
                        Journey telemetry, stage-to-stage transition rates, and drop-off analysis
                    </p>
                </div>
            </div>

            {{-- Summary & Period Badges --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <x-filament::icon icon="heroicon-m-check-badge" class="w-4 h-4 text-emerald-500" />
                    <span>{{ $summaryKpis['overallConversion'] }}% Overall Conversion</span>
                    <span class="text-emerald-600/70 dark:text-emerald-400/70 font-normal">({{ number_format($summaryKpis['totalVotes']) }} Votes)</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                    <x-filament::icon icon="heroicon-m-calendar" class="w-3.5 h-3.5 text-gray-400" />
                    <span>{{ $summaryKpis['periodLabel'] }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Clean Table Layout --}}
    <div class="overflow-x-auto -mx-6 -mb-6 mt-1 border-t border-gray-200 dark:border-gray-800">
        <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-800">
            <thead>
                <tr class="bg-gray-50/75 dark:bg-gray-900/50 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <th class="py-3 px-4 w-12 text-center">#</th>
                    <th class="py-3 px-4">Journey Stage / Page</th>
                    <th class="py-3 px-4 w-32">Type</th>
                    <th class="py-3 px-4 text-right w-36">Total Hits</th>
                    <th class="py-3 px-4 text-right w-36">Unique Visitors</th>
                    <th class="py-3 px-4 text-right w-48">Step Conversion</th>
                    <th class="py-3 px-4 text-right w-44">Funnel Retention</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60 font-medium">
                @foreach ($funnelData as $step)
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors">
                        {{-- Step Index --}}
                        <td class="py-3.5 px-4 text-center">
                            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs font-extrabold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                {{ $step['step'] }}
                            </span>
                        </td>

                        {{-- Stage Name & Description --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $step['icon_bg'] }}">
                                    <x-filament::icon :icon="$step['icon']" class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 dark:text-gray-100 text-sm">
                                        {{ $step['name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-normal truncate max-w-md">
                                        {{ $step['description'] }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Tracking Type --}}
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $step['type'] === 'Page View' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40' }}">
                                @if ($step['type'] === 'Page View')
                                    <x-filament::icon icon="heroicon-m-globe-alt" class="w-3.5 h-3.5 inline" />
                                @else
                                    <x-filament::icon icon="heroicon-m-bolt" class="w-3.5 h-3.5 inline" />
                                @endif
                                {{ $step['type'] }}
                            </span>
                        </td>

                        {{-- Total Hits --}}
                        <td class="py-3.5 px-4 text-right font-black text-gray-950 dark:text-white text-base tabular-nums">
                            {{ number_format($step['views']) }}
                        </td>

                        {{-- Unique Visitors --}}
                        <td class="py-3.5 px-4 text-right text-gray-700 dark:text-gray-300 font-semibold tabular-nums">
                            <div class="flex items-center justify-end gap-1.5">
                                <x-filament::icon icon="heroicon-o-users" class="w-3.5 h-3.5 text-gray-400" />
                                <span>{{ number_format($step['visitors']) }}</span>
                            </div>
                        </td>

                        {{-- Step Conversion / Proceeded --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex flex-col items-end gap-0.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold {{ $step['step'] === 1 ? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' : ($step['step_pct'] >= 75 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($step['step_pct'] >= 40 ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300')) }}">
                                    @if ($step['step'] === 1)
                                        100% Entry Baseline
                                    @else
                                        <x-filament::icon icon="heroicon-m-arrow-down-right" class="w-3 h-3 inline" />
                                        {{ $step['step_pct'] }}% proceeded
                                    @endif
                                </span>
                                @if ($step['step'] > 1 && $step['dropoff_count'] > 0)
                                    <span class="text-[11px] text-gray-400">
                                        -{{ number_format($step['dropoff_count']) }} drop ({{ $step['dropoff_pct'] }}%)
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Funnel Retention Progress Bar --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2.5">
                                <div class="w-20 bg-gray-200/80 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary-500 h-2 rounded-full transition-all duration-500" style="width: {{ max(3, $step['baseline_pct']) }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums w-12 text-right">
                                    {{ $step['baseline_pct'] }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50/80 dark:bg-gray-900/60 text-xs font-semibold text-gray-700 dark:text-gray-300 border-t border-gray-200 dark:border-gray-800">
                <tr>
                    <td colspan="3" class="py-3 px-4">
                        <span class="uppercase tracking-wider font-bold text-[11px] text-gray-500">Funnel Totals & Conversion Summary</span>
                    </td>
                    <td class="py-3 px-4 text-right font-black text-gray-950 dark:text-white tabular-nums">
                        {{ number_format(array_sum(array_column($funnelData, 'views'))) }} Hits
                    </td>
                    <td class="py-3 px-4 text-right font-black text-gray-950 dark:text-white tabular-nums">
                        {{ number_format($summaryKpis['totalVisitors']) }} Visitors
                    </td>
                    <td class="py-3 px-4 text-right text-emerald-600 dark:text-emerald-400 font-bold">
                        {{ $summaryKpis['overallConversion'] }}% Conversion Rate
                    </td>
                    <td class="py-3 px-4 text-right text-gray-500">
                        {{ number_format($summaryKpis['totalVotes']) }} Votes Cast
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-filament::section>
