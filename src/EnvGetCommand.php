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
    protected $signature = 'env:get {key?} {--key-value}';

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

        $env_path = app()->environmentFilePath();
        $env_config = file_get_contents($env_path);

        if (is_null($key))
            return $this->line($env_config);

        $value = $this->getEnvValue($env_config, $key);

        $keyValFormat = $this->option('key-value');
        return ($keyValFormat)
            ? $this->line("{$key}={$value}")
            : $this->line($value);
    }

    /**
     * Get the current value of a given key from an environment file.
     *
     * @param string $envFile
     * @param string $key
     * @return string
     */
    protected function getEnvValue(&$envFile, string $key): string
    {
        preg_match("/^{$key}=(.*)\r\n/m", $envFile, $matches);

        return $matches[1] ?? '';
    }


    /**
     * Check if a given string is valid as an environment variable key.
     *
     * @param string $key
     * @return boolean
     */
    protected function validKey(string $key): bool
    {
        if (!preg_match('/^[a-zA-Z_]+$/', $key)) {
            throw new InvalidArgumentException('Invalid environment variable. Use only letters and underscores.');
        }

        return true;
    }
}
