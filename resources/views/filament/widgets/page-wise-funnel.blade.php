<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-funnel" class="w-5 h-5 text-primary-500" />
            <span>Page-Wise Tracking & Voting Conversion Funnel</span>
        </div>
    </x-slot>

    <x-slot name="description">
        Direct tracking across key user journeys: Homepage, All Puja Directory, Vote Screen, Vote Initiations, Verified Votes, and Vote Success.
    </x-slot>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-800">
            <thead>
                <tr class="bg-gray-50/75 dark:bg-gray-900/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-4 w-12 text-center">#</th>
                    <th class="py-3 px-4">Journey Stage / Page</th>
                    <th class="py-3 px-4">Tracking Type</th>
                    <th class="py-3 px-4 text-right">Total Hits / Events</th>
                    <th class="py-3 px-4 text-right">Unique Visitors</th>
                    <th class="py-3 px-4 w-48 text-right">Stage Conversion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60 font-medium">
                @foreach ($funnelData as $step)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                        <td class="py-3.5 px-4 text-center font-bold text-gray-400">
                            {{ $step['step'] }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $step['badge_color'] }}">
                                    <x-filament::icon :icon="$step['icon']" class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ $step['name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-normal">
                                        {{ $step['description'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $step['type'] === 'Page View' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' }}">
                                {{ $step['type'] }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-gray-900 dark:text-gray-100 text-base">
                            {{ number_format($step['views']) }}
                        </td>
                        <td class="py-3.5 px-4 text-right text-gray-600 dark:text-gray-400">
                            {{ number_format($step['visitors']) }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-primary-500 h-1.5 rounded-full" style="width: {{ $step['pct'] }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $step['conversion_label'] }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>
