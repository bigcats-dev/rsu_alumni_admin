<?php

namespace App\Providers;

use App\Extensions\JWTGuard;
use App\Extensions\JWTUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // add custom guard provider
        Auth::provider('jwt', function ($app, array $config) {
            return new JWTUserProvider($app->make('App\Models\User'));
        });

        // custom guard
        // add custom guard
        Auth::extend('access_token', function ($app, $name, array $config) {
            $userProvider = app(JWTUserProvider::class);
			$request = $app->make('request');
            return new JWTGuard($userProvider, $request);
        });
    }
}
