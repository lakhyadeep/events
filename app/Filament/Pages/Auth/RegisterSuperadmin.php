<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class RegisterSuperadmin extends BaseRegister
{
    public function mount(): void
    {
        if (User::exists()) {
            redirect()->to(Filament::getLoginUrl());

            return;
        }

        parent::mount();
    }

    public function register(): ?RegistrationResponse
    {
        if (User::exists()) {
            redirect()->to(Filament::getLoginUrl());

            return null;
        }

        return parent::register();
    }

    protected function handleRegistration(array $data): Model
    {
        $data['email_verified_at'] = now();

        return parent::handleRegistration($data);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Initial Superadmin Setup';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Setup Superadmin Account';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Create the primary administrator account for Dibrugarh 24x7 Events. Registration will permanently lock once completed.';
    }

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()
            ->label('Create Superadmin & Enter Dashboard');
    }

    public function loginAction(): Action
    {
        return parent::loginAction()
            ->visible(fn () => User::exists());
    }
}
