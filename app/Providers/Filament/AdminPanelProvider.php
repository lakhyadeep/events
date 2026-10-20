<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\RegisterSuperadmin;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AnalyticsQuickStatsWidget;
use App\Filament\Widgets\SmsBalanceAlertWidget;
use App\Filament\Widgets\SmsUsageOverviewWidget;
use App\Filament\Widgets\TopContendersVotesChartWidget;
use App\Filament\Widgets\VotingLeaderboardWidget;
use App\Filament\Widgets\VotingStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->registration(RegisterSuperadmin::class)
            ->spa()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15.5rem')
            ->collapsedSidebarWidth('4.5rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AnalyticsQuickStatsWidget::class,
                SmsBalanceAlertWidget::class,
                VotingStatsOverview::class,
                SmsUsageOverviewWidget::class,
                TopContendersVotesChartWidget::class,
                VotingLeaderboardWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
<script>
    (function() {
        const registerStackedBarLabels = function() {
            if (typeof Chart !== 'undefined' && !window.__filamentStackedBarLabelsRegistered) {
                window.__filamentStackedBarLabelsRegistered = true;
                Chart.register({
                    id: 'filamentStackedBarLabels',
                    afterDatasetsDraw(chart) {
                        const isStacked = chart.options?.scales?.x?.stacked && chart.options?.scales?.y?.stacked;
                        if (!isStacked) return;

                        const isHorizontal = chart.options?.indexAxis === 'y';
                        const ctx = chart.ctx;
                        ctx.save();
                        ctx.font = 'bold 11px system-ui, -apple-system, sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets.forEach((dataset, datasetIndex) => {
                            const meta = chart.getDatasetMeta(datasetIndex);
                            if (meta.hidden) return;

                            meta.data.forEach((element, index) => {
                                const value = dataset.data[index];
                                if (value && value > 0) {
                                    if (isHorizontal) {
                                        const { x, y, base } = element;
                                        const width = Math.abs(x - base);
                                        if (width >= 16) {
                                            const posX = base + (x - base) / 2;
                                            ctx.fillStyle = '#ffffff';
                                            ctx.shadowColor = 'rgba(0, 0, 0, 0.65)';
                                            ctx.shadowBlur = 3;
                                            ctx.fillText(value, posX, y);
                                        }
                                    } else {
                                        const { x, y, base } = element;
                                        const height = Math.abs(base - y);
                                        if (height >= 16) {
                                            const posY = base + (y - base) / 2;
                                            ctx.fillStyle = '#ffffff';
                                            ctx.shadowColor = 'rgba(0, 0, 0, 0.65)';
                                            ctx.shadowBlur = 3;
                                            ctx.fillText(value, x, posY);
                                        }
                                    }
                                }
                            });
                        });
                        ctx.restore();
                    }
                });
            }
        };

        const interval = setInterval(function() {
            if (typeof Chart !== 'undefined') {
                registerStackedBarLabels();
                clearInterval(interval);
            }
        }, 150);
        setTimeout(function() { clearInterval(interval); }, 10000);
    })();
</script>
HTML
                )
            );
    }
}
