<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        Blade::directive('currency', function ($expression) {
            if ($expression == null) $expression = 0;
            return "Rp. <?php echo number_format($expression, 0, ',', '.'); ?>";
        });

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            return substr($value, 0, 2) == '08';
        });

        Validator::replacer('phone_number', function ($message, $attribute, $rule, $parameters) {
            return 'Format No HP tidak valid';
        });

        Validator::extend('initial_password', function ($attribute, $value, $parameters) {
            $user = User::find($parameters[0]);

            return $user && Hash::check($value, $user->password);
        });

        Validator::replacer('initial_password', function ($message, $attribute, $rule, $parameters) {
            return 'Password lama Anda tidak cocok';
        });

        Validator::extend('different_password', function ($attribute, $value, $parameters) {
            $user = User::find($parameters[0]);

            return $user && !Hash::check($value, $user->password);
        });

        Validator::replacer('different_password', function ($message, $attribute, $rule, $parameters) {
            return 'Password baru harus berbeda dengan password lama';
        });
    }
}
