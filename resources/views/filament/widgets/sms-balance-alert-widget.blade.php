<x-filament-widgets::widget>
    @php
        $details = $this->balanceDetails;
        $balance = $details['balance'] !== null ? number_format((float) $details['balance'], 2) : '0.00';
        $threshold = number_format((float) ($details['threshold'] ?? 50), 2);
    @endphp

    <div class="relative overflow-hidden rounded-xl border border-rose-300 bg-gradient-to-r from-rose-50 via-amber-50 to-rose-50 p-4 sm:p-5 shadow-sm dark:border-rose-900/60 dark:from-rose-950/40 dark:via-zinc-900 dark:to-rose-950/30">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3.5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-600/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                    <x-filament::icon
                        icon="heroicon-o-exclamation-triangle"
                        class="h-6 w-6"
                    />
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-base font-semibold tracking-tight text-rose-950 dark:text-rose-200">
                            Fast2SMS Gateway Low Balance Alert
                        </h3>
                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800 dark:bg-rose-900/70 dark:text-rose-200">
                            Action Required
                        </span>
                    </div>

                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        Remaining SMS wallet balance is
                        <strong class="font-bold text-rose-700 dark:text-rose-300">₹{{ $balance }}</strong>
                        (configured alert threshold:
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">₹{{ $threshold }}</span>).
                        Outgoing voter verification OTPs will be interrupted if the wallet runs out of credits.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                <button
                    type="button"
                    wire:click="refreshBalance"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-rose-500 disabled:opacity-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                >
                    <x-filament::icon
                        icon="heroicon-o-arrow-path"
                        class="h-4 w-4"
                        wire:loading.class="animate-spin"
                    />
                    <span>Re-check Balance</span>
                </button>

                <a
                    href="{{ $this->settingsUrl }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                >
                    <x-filament::icon
                        icon="heroicon-o-cog-6-tooth"
                        class="h-4 w-4"
                    />
                    <span>SMS Settings</span>
                </a>

                <a
                    href="https://www.fast2sms.com/panel/wallet"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow transition hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-rose-600 dark:hover:bg-rose-500"
                >
                    <span>Recharge Fast2SMS</span>
                    <x-filament::icon
                        icon="heroicon-m-arrow-top-right-on-square"
                        class="h-3.5 w-3.5"
                    />
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
