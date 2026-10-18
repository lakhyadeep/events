<?php

namespace App\Providers;

use App\Services\Fraud\FraudPreventionService;
use App\Services\Otp\OtpManager;
use App\Services\Otp\OtpService;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($publicPath = env('APP_PUBLIC_PATH')) {
            $this->app->usePublicPath(
                str_starts_with($publicPath, '/') || preg_match('/^[a-zA-Z]:/', $publicPath)
                    ? $publicPath
                    : base_path($publicPath)
            );
        }

        $this->app->singleton(FraudPreventionService::class, fn () => new FraudPreventionService);
        $this->app->singleton(OtpManager::class, fn () => new OtpManager);
        $this->app->singleton(OtpService::class, fn ($app) => new OtpService($app->make(OtpManager::class), $app->make(FraudPreventionService::class)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://') || app()->environment('production')) {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        FileUpload::configureUsing(function (FileUpload $fileUpload): void {
            $fileUpload->disk('public');
        });

        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action
                ->requiresConfirmation()
                ->modalDescription('Security Verification: Please enter your administrator password to confirm this deletion.')
                ->form([
                    TextInput::make('current_password')
                        ->label('Confirm Current Password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->rules(['current_password'])
                        ->validationMessages([
                            'current_password' => 'The provided password does not match your administrator password.',
                        ]),
                ]);
        });

        DeleteBulkAction::configureUsing(function (DeleteBulkAction $action): void {
            $action
                ->requiresConfirmation()
                ->modalDescription('Security Verification: Please enter your administrator password to confirm bulk deletion.')
                ->form([
                    TextInput::make('current_password')
                        ->label('Confirm Current Password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->rules(['current_password'])
                        ->validationMessages([
                            'current_password' => 'The provided password does not match your administrator password.',
                        ]),
                ]);
        });
    }
}
