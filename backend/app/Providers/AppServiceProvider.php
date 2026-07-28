<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /*
         * El enlace llevará al formulario de React.
         */
        ResetPassword::createUrlUsing(
            function (
                User $usuario,
                string $token
            ): string {
                $frontendUrl = rtrim(
                    (string) config('app.frontend_url'),
                    '/'
                );

                return $frontendUrl
                    . '/reset-password?token='
                    . urlencode($token)
                    . '&email='
                    . urlencode(
                        $usuario->getEmailForPasswordReset()
                    );
            }
        );
    }
}