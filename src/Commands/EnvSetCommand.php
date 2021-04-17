<?php

namespace msztorc\LaravelEnv\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;
use msztorc\LaravelEnv\Commands\Traits\CommandValidator;
use msztorc\LaravelEnv\Env;

class EnvSetCommand extends Command
{
    use CommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:set {key} {value?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update an environment variable value in .env file';

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
            [$key, $value] = $this->getKeyValue();

            $env = new Env();
            $env->setValue($key, $value);

            $this->info("Environment variable with key '{$key}' has been set to '{$value}'");
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
        }

        return;
    }


    /**
     * Determine what the supplied key and value is from the current command.
     *
     * @return array
     */
    protected function getKeyValue(): array
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        if (is_null($value) || !$value) {
            $parts = explode('=', (string)$key, 2);

            if (count($parts) !== 2) {
                throw new InvalidArgumentException('No value was set');
            }

            [$key, $value] = $parts;
        }

        if (Str::contains((string)$key, '=')) {
            throw new InvalidArgumentException("Environment key should not contain '='");
        }

        if (!$this->isValidKey((string)$key)) {
            throw new InvalidArgumentException('Invalid argument key');
        }

        return [(string)$key, (string)$value];
    }
}
