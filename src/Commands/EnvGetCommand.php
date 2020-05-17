<?php

namespace msztorc\LaravelEnv;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class EnvGetCommand extends Command
{
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
            $this->validKey($key);

        $json = $this->option('json');
        $keyValFormat = $this->option('key-value');

        $env = new Env();

        if (is_null($key))
            return $this->line(($json) ? json_encode($env->getEnvContent()) : $env->getEnvContent());


        $value = ($json) ? json_encode($env->getKeyValue($key)) : ($keyValFormat ? $env->getKeyValue($key) : $env->getValue($key));

        return ($json)
            ? $this->line($value)
            : $this->line(($keyValFormat ? "{$key}={$value[$key]}" : $value));
    }


    /**
     * Check if a given string is valid as an environment variable key.
     *
     * @param string $key
     * @return boolean
     */
    protected function validKey(string $key): bool
    {
        if (!preg_match('/^[a-zA-Z_0-9]+$/', $key)) {
            throw new InvalidArgumentException('Invalid environment variable. Use only letters, digits and underscores.');
        }

        return true;
    }
}
