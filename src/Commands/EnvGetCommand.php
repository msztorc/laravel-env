<?php

namespace msztorc\LaravelEnv\Commands;

use Illuminate\Console\Command;
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
     * Environment variable key.
     *
     * @var string
     */
    protected $key;

    /**
     * key-value format arg
     *
     * @var bool
     */
    protected $keyValFormat;

    /**
     * json format argument
     *
     * @var bool
     */
    protected $json;

    /**
     * Env object
     *
     * @var object
     */
    protected $env;

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
        $this->key = (string)$this->argument('key');

        if (strlen($this->key)) {
            $this->isValidKey($this->key);
        }

        $this->json = (bool)$this->option('json');
        $this->keyValFormat = (bool)$this->option('key-value');
        $this->env = new Env();

        return $this->_printOutput();
    }

    private function _getEntireEnvContent()
    {
        return ($this->json) ? json_encode($this->env->getVariables()) : $this->env->getEnvContent();
        ;
    }

    private function _printKeyValue()
    {
        $value = ($this->json) ? json_encode($this->env->getKeyValue($this->key)) : ($this->keyValFormat ? $this->env->getKeyValue($this->key) : $this->env->getValue($this->key));

        return($this->json) ? (string)$value : ($this->keyValFormat ? "{$this->key}={$value[$this->key]}" : (string)$value);
    }

    private function _printOutput(): void
    {
        if (!strlen($this->key)) {
            $this->line($this->_getEntireEnvContent());

            return;
        }

        if (strlen($this->key) && $this->env->exists($this->key)) {
            $this->line($this->_printKeyValue());
        } else {
            $this->line("There is no variable '{$this->key}'");
        }

        return;
    }
}
