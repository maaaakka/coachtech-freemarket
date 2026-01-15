<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\LoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::lower($request->input(Fortify::username())).'|'.$request->ip();
            return Limit::perMinute(5)->by($throttleKey);
        });

        Fortify::authenticateThrough(function () {
            return [
                // バリデーション
                function ($request, $next) {
                    app(LoginRequest::class)->validateResolved();
                    return $next($request);
                },
                // 認証
                function ($request, $next) {
                    if (Auth::attempt(
                        $request->only('email', 'password'),
                        $request->boolean('remember')
                    )) {
                        return $next($request);
                    }

                    throw ValidationException::withMessages([
                        'email' => 'ログイン情報が登録されていません',
                    ]);
                },
            ];
        });
    }
}