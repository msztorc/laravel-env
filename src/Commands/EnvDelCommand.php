<?php

namespace msztorc\LaravelEnv\Commands;

use Illuminate\Console\Command;
use msztorc\LaravelEnv\Commands\Traits\CommandValidator;
use msztorc\LaravelEnv\Env;

class EnvDelCommand extends Command
{
    use CommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:del {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete variable from an environment file';

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

        if (!is_null($key)) {
            $this->isValidKey((string)$key);
        }

        $env = new Env();
        if (!$env->exists((string)$key)) {
            $this->info("There is no variable {$key}");
        } else {
            $env->deleteVariable((string)$key);
            $this->info("Variable '{$key}' has been deleted");
        }
    }
}
