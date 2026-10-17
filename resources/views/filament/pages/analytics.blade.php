<x-filament-panels::page>
    <!-- Filter Control Bar -->
    <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        
        <!-- Event Filter Dropdown -->
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                <x-filament::icon icon="heroicon-m-calendar" class="w-4 h-4 inline me-1 text-primary-500" /> Event Scope:
            </span>
            <select
                wire:model.live="eventId"
                class="block w-full md:w-64 rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm focus:border-amber-500 focus:ring-amber-500 dark:text-gray-200 shadow-sm"
            >
                <option value="">All Events (Global)</option>
                @foreach ($this->events as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Period Toggle Pills -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider me-1">Range:</span>
            @php
                $periods = [
                    'today' => 'Today',
                    'yesterday' => 'Yesterday',
                    '7days' => 'Last 7 Days',
                    '30days' => 'Last 30 Days',
                    'all' => 'All Time',
                ];
            @endphp
            @foreach ($periods as $key => $label)
                <button
                    type="button"
                    wire:click="$set('period', '{{ $key }}')"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all {{ $period === $key ? 'bg-amber-500 text-white shadow-sm font-semibold' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

    </div>
</x-filament-panels::page>
