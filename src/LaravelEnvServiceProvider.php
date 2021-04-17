<?php

namespace msztorc\LaravelEnv;

use Illuminate\Support\ServiceProvider;
use msztorc\LaravelEnv\Commands\EnvDelCommand;
use msztorc\LaravelEnv\Commands\EnvGetCommand;
use msztorc\LaravelEnv\Commands\EnvListCommand;
use msztorc\LaravelEnv\Commands\EnvSetCommand;

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
        $this->app->bind('command.env:del', EnvDelCommand::class);
        $this->app->bind('command.env:list', EnvListCommand::class);

        $this->commands([
            'command.env:get',
            'command.env:set',
            'command.env:del',
            'command.env:list',
        ]);
    }
}
