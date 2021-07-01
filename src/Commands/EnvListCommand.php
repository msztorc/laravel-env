<?php

namespace msztorc\LaravelEnv\Commands;

use Illuminate\Console\Command;
use msztorc\LaravelEnv\Commands\Traits\CommandValidator;
use msztorc\LaravelEnv\Env;

class EnvListCommand extends Command
{
    use CommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:list {key?} {--json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all variables from an environment file';

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
        $this->json = (bool)$this->option('json');

        $this->env = new Env();
        $this->line($this->_getEntireEnvContent());
    }

    private function _getEntireEnvContent()
    {
        return ($this->json) ? json_encode($this->env->getVariables()) : $this->env->getEnvContent();
    }
}
