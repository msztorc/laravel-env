<?php

namespace msztorc\LaravelEnv\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use msztorc\LaravelEnv\Commands\Traits\CommandValidator;
use msztorc\LaravelEnv\Env;

class EnvRenameCommand extends Command
{
    use CommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:rename {key} {new-key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename an environment variable key in .env file';

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
        try {
            $key = $this->argument('key');
            $newKey = $this->argument('new-key');

            if (!$this->isValidKey((string)$key)) {
                throw new InvalidArgumentException('Invalid argument key');
            }

            if (!$this->isValidKey((string)$newKey)) {
                throw new InvalidArgumentException('Invalid argument new-key');
            }

            $env = new Env();

            if (!$env->exists((string)$key)) {
                $this->error("There is no variable '{$key}'");

                return 1;
            }

            $env->renameVariable((string)$key, (string)$newKey);

            $this->info("Environment variable '{$key}' has been renamed to '{$newKey}'");
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }
}
