<?php declare(strict_types=1);

namespace msztorc\LaravelEnv\Tests\EnvArtisanTest;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use msztorc\LaravelEnv\Env;
use msztorc\LaravelEnv\LaravelEnvServiceProvider;
use Orchestra\Testbench\TestCase;

final class EnvArtisanTest extends TestCase
{
    private $env_vars_empty = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL', 'LOG_CHANNEL', 'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'BROADCAST_DRIVER', 'CACHE_DRIVER', 'QUEUE_CONNECTION', 'SESSION_DRIVER', 'SESSION_LIFETIME', 'REDIS_HOST', 'REDIS_PASSWORD', 'REDIS_PORT', 'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME', 'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_BUCKET', 'PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER', 'MIX_PUSHER_APP_KEY', 'MIX_PUSHER_APP_CLUSTER', 'DUMMY_VARIABLE'];
    private $_env_vars = ['APP_NAME' => 'Laravel', 'APP_ENV' => 'local', 'APP_KEY' => '', 'APP_DEBUG' => 'true', 'APP_URL' => 'http://localhost', 'LOG_CHANNEL' => 'stack', 'DB_CONNECTION' => 'mysql', 'DB_HOST' => '127.0.0.1', 'DB_PORT' => '3306', 'DB_DATABASE' => 'laravel', 'DB_USERNAME' => 'root', 'DB_PASSWORD' => '', 'BROADCAST_DRIVER' => 'log', 'CACHE_DRIVER' => 'file', 'QUEUE_CONNECTION' => 'sync', 'SESSION_DRIVER' => 'file', 'SESSION_LIFETIME' => '120', 'REDIS_HOST' => '127.0.0.1', 'REDIS_PASSWORD' => 'null', 'REDIS_PORT' => '6379', 'MAIL_MAILER' => 'smtp', 'MAIL_HOST' => 'smtp.mailtrap.io', 'MAIL_PORT' => '2525', 'MAIL_USERNAME' => 'null', 'MAIL_PASSWORD' => 'null', 'MAIL_ENCRYPTION' => 'null', 'MAIL_FROM_ADDRESS' => 'null', 'MAIL_FROM_NAME' => '${APP_NAME}', 'AWS_ACCESS_KEY_ID' => '', 'AWS_SECRET_ACCESS_KEY' => '', 'AWS_DEFAULT_REGION' => 'us-east-1', 'AWS_BUCKET' => '', 'PUSHER_APP_ID' => '', 'PUSHER_APP_KEY' => '', 'PUSHER_APP_SECRET' => '', 'PUSHER_APP_CLUSTER' => 'mt1', 'MIX_PUSHER_APP_KEY' => '${PUSHER_APP_KEY}', 'MIX_PUSHER_APP_CLUSTER' => '${PUSHER_APP_CLUSTER}', 'DUMMY_VARIABLE' => 'Adf4$r-Ac\"'];

    public function setUp(): void
    {
        parent::setUp();
        copy(__DIR__.'/.env.example', __DIR__ . '/.env');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unlink(__DIR__.'/.env');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelEnvServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__.'/');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }


    private function _genRandomString($length = 8)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', (int)ceil($length / strlen($x)))), 1, $length);
    }

    public function testEnvGetSelected(): void
    {
        $this->artisan('env:get', ['key' => 'APP_NAME'])
            ->expectsOutput('Laravel')
            ->assertExitCode(0)
        ;

        $this->artisan('env:get', ['key' => 'REDIS_HOST'])
            ->expectsOutput('127.0.0.1')
            ->assertExitCode(0);

        $this->artisan('env:get', ['key' => 'MAIL_FROM_NAME'])
            ->expectsOutput('${APP_NAME}')
            ->assertExitCode(0);
    }

    public function testEnvGetAll(): void
    {
        foreach ($this->_env_vars as $key => $val) {
            $this->artisan('env:get', ['key' => $key])
                ->expectsOutput($val)
                ->assertExitCode(0);
        }
    }

    public function testEnvListAll(): void
    {
        $env_content = file_get_contents(__DIR__ . '/.env');

        $this->artisan('env:get')
        ->expectsOutput($env_content)
        ->assertExitCode(0);

        $this->artisan('env:get', ['--json' => true])
            ->expectsOutput(json_encode($this->_env_vars))
            ->assertExitCode(0);


        $this->artisan('env:list')
            ->expectsOutput($env_content)
            ->assertExitCode(0);

        $this->artisan('env:list', ['--json' => true])
            ->expectsOutput(json_encode($this->_env_vars))
            ->assertExitCode(0);
    }

    public function testEnvArtisanSetAllKeyValueArgs(): void
    {
        foreach ($this->env_vars_empty as $key => &$val) {
            $val = $this->_genRandomString(rand(4, 25));
        }

        foreach ($this->env_vars_empty as $key => $val) {
            $this->artisan('env:set', ['key' => 'A_' . $key, 'value' => $val])
                ->expectsOutput("Environment variable with key 'A_{$key}' has been set to '{$val}'")
                ->assertExitCode(0);

            $this->artisan('env:get', ['key' => 'A_' . $key])
                ->expectsOutput($val)
                ->assertExitCode(0);
        }
    }

    public function testEnvArtisanSetAllKeyEqualsValueArgs(): void
    {
        foreach ($this->env_vars_empty as $key => &$val) {
            $val = $this->_genRandomString(rand(4, 25));
        }

        foreach ($this->env_vars_empty as $key => $val) {
            $this->artisan('env:set', ['key' => 'A_' . $key .'=' .$val])
                ->expectsOutput("Environment variable with key 'A_{$key}' has been set to '{$val}'")
                ->assertExitCode(0);

            $this->artisan('env:get', ['key' => 'A_' . $key])
                ->expectsOutput($val)
                ->assertExitCode(0);
        }
    }

    public function testEnvDelAll(): void
    {
        foreach ($this->env_vars_empty as $key) {
            $this->artisan('env:del', ['key' => $key])
                ->expectsOutput("Variable '{$key}' has been deleted")
                ->assertExitCode(0);

            $this->artisan('env:get', ['key' => $key])
                ->expectsOutput("There is no variable '{$key}'")
                ->assertExitCode(0);
        }
    }

    public function testUrls(): void
    {
        $app_url = 'https://mywebsite.com';

        $this->artisan('env:set', ['key' => 'APP_URL' .'=' .$app_url])
            ->expectsOutput("Environment variable with key 'APP_URL' has been set to '{$app_url}'")
            ->assertExitCode(0);

        $this->artisan('env:get', ['key' => 'APP_URL'])
            ->expectsOutput($app_url)
            ->assertExitCode(0);
    }

    public function testEmptyValue(): void
    {
        $app_name = '';

        $this->artisan('env:set', ['key' => 'APP_NAME' .'=' . ''])
            ->expectsOutput("Environment variable with key 'APP_NAME' has been set to '{$app_name}'")
            ->assertExitCode(0);

        $this->artisan('env:get', ['key' => 'APP_NAME'])
            ->expectsOutput($app_name)
            ->assertExitCode(0);
    }

    public function testSetValueWithHyphen(): void
    {
        $app_name = 'my-app';

        $this->artisan('env:set', ['key' => 'APP_NAME' .'=' . 'my-app'])
            ->expectsOutput("Environment variable with key 'APP_NAME' has been set to '{$app_name}'")
            ->assertExitCode(0);

        $this->artisan('env:get', ['key' => 'APP_NAME'])
            ->expectsOutput($app_name)
            ->assertExitCode(0);
    }

    public function testSetSpecialCharacters(): void
    {
        $app_key = '=t+++=.,hHya:df';

        $this->artisan('env:set', ['key' => 'APP_KEY' .'=' . $app_key])
            ->expectsOutput("Environment variable with key 'APP_KEY' has been set to '{$app_key}'")
            ->assertExitCode(0);

        $this->artisan('env:get', ['key' => 'APP_KEY'])
            ->expectsOutput($app_key)
            ->assertExitCode(0);
    }

    public function testSetSpecialCharactersExtended(): void
    {
        $app_key = '1"=t+++/\\//\=.,h\"Hya:df';

        $this->artisan('env:set', ['key' => 'APP_KEY' .'=' . $app_key])
            ->expectsOutput("Environment variable with key 'APP_KEY' has been set to '{$app_key}'")
            ->assertExitCode(0);

        $this->artisan('env:get', ['key' => 'APP_KEY'])
            ->expectsOutput($app_key)
            ->assertExitCode(0);
    }

    public function testKeyNames(): void
    {
        $valid_key1 = 'A';
        $valid_key2 = 'ABC';
        $valid_key3 = 'ABC123';
        $valid_key4 = 'ABC_123';
        $valid_key5 = '_ABC__123';

        $this->artisan('env:set', ['key' => $valid_key1 .'=testvalue'])
            ->expectsOutput("Environment variable with key '$valid_key1' has been set to 'testvalue'")
            ->assertExitCode(0);

        $this->artisan('env:set', ['key' => $valid_key2 .'=testvalue'])
            ->expectsOutput("Environment variable with key '$valid_key2' has been set to 'testvalue'")
            ->assertExitCode(0);

        $this->artisan('env:set', ['key' => $valid_key3 .'=testvalue'])
            ->expectsOutput("Environment variable with key '$valid_key3' has been set to 'testvalue'")
            ->assertExitCode(0);

        $this->artisan('env:set', ['key' => $valid_key4 .'=testvalue'])
            ->expectsOutput("Environment variable with key '$valid_key4' has been set to 'testvalue'")
            ->assertExitCode(0);

        $this->artisan('env:set', ['key' => $valid_key5 .'=testvalue'])
            ->expectsOutput("Environment variable with key '$valid_key5' has been set to 'testvalue'")
            ->assertExitCode(0);


        $invalid_key1 = '1';
        $invalid_key2 = '123';
        $invalid_key3 = 'TEST KEY';
        $invalid_key4 = 'test';
        $invalid_key5 = 'test_key';


        $this->artisan('env:set', ['key' => $invalid_key1 .'=testvalue'])
            ->expectsOutput('Invalid environment key. Only use upper letters, digits, and underscores. A variable must start with the letter.')
            ->assertExitCode(1);

        $this->artisan('env:set', ['key' => $invalid_key2 .'=testvalue'])
            ->expectsOutput('Invalid environment key. Only use upper letters, digits, and underscores. A variable must start with the letter.')
            ->assertExitCode(1);

        $this->artisan('env:set', ['key' => $invalid_key3 .'=testvalue'])
            ->expectsOutput('Invalid environment key. Only use upper letters, digits, and underscores. A variable must start with the letter.')
            ->assertExitCode(1);

        $this->artisan('env:set', ['key' => $invalid_key4 .'=testvalue'])
            ->expectsOutput('Invalid environment key. Only use upper letters, digits, and underscores. A variable must start with the letter.')
            ->assertExitCode(1);

        $this->artisan('env:set', ['key' => $invalid_key5 .'=testvalue'])
            ->expectsOutput('Invalid environment key. Only use upper letters, digits, and underscores. A variable must start with the letter.')
            ->assertExitCode(1);

    }
}
