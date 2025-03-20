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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/services.php',
            'services'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadSocialiteDriver();

        // publish the config file
        $this->publishes([
            __DIR__ . '/../config/core.php' => config_path('core.php'),
        ], 'core-config');

        // publish the migration file
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'core-migrations');
    }

    /**
     * Register the Socialite provider.
     * Socialite driver name: core-oauth2
     *
     * @return void
     */
    protected function loadSocialiteDriver()
    {
        Socialite::extend('core-oauth2', function ($app) {
            $config = $app->make('config')->get('services.core-oauth2');

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
