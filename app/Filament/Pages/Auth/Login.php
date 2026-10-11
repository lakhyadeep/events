<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getSubheading(): string|Htmlable|null
    {
        if (User::exists()) {
            return null;
        }

        return parent::getSubheading();
    }

    public function registerAction(): Action
    {
        return parent::registerAction()
            ->label('Initial Setup: Create Superadmin')
            ->visible(fn () => User::doesntExist());
    }
}
