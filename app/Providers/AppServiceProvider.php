<?php

namespace App\Providers;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
