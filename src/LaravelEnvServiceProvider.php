<?php

namespace msztorc\LaravelEnv;

use Illuminate\Support\ServiceProvider;

class LaravelEnvServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // ...
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('command.env:get', EnvGetCommand::class);
        $this->app->bind('command.env:set', EnvSetCommand::class);

        $this->commands([
            'command.env:get',
            'command.env:set'
        ]);
    }
}
