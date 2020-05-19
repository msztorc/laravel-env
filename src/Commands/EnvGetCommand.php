<?php

namespace msztorc\LaravelEnv\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use msztorc\LaravelEnv\Commands\Traits\CommandValidator;
use msztorc\LaravelEnv\Env;

class EnvGetCommand extends Command
{

    use CommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:get {key?} {--key-value} {--json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get variable value from an environment file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->argument('key');

        if (!is_null($key))
            $this->isValidKey($key);

        $json = $this->option('json');
        $keyValFormat = $this->option('key-value');

        $env = new Env();

        if (is_null($key))
            $this->line(($json) ? json_encode($env->getEnvContent()) : $env->getEnvContent());

        if ($env->exists($key)) {
            $value = ($json) ? json_encode($env->getKeyValue($key)) : ($keyValFormat ? $env->getKeyValue($key) : $env->getValue($key));

            $this->line(($json)
                ? $value
                : ($keyValFormat ? "{$key}={$value[$key]}" : $value)
            );
        } else {
            $this->line("There is no variable {$key}");
        }
    }
}
