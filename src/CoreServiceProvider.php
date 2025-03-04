<?php

namespace Core;

use Core\Socialite\CoreOauthProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function register()
    {
        // 
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadSocialiteDriver();
    }

    /**
     * Register the Socialite provider.
     * Socialite driver name: ep-core
     *
     * @return void
     */
    protected function loadSocialiteDriver()
    {
        Socialite::extend('ep-core', function ($app) {
            $config = $app->make('config')->get('services.ep-core');

            return new CoreOauthProvider(
                $app['request'],
                $config['client_id'] ?? null,
                $config['client_secret'] ?? null,
                $config['redirect'] ?? null,
                $config['base_url'] ?? null,
            );
        });
    }
}
