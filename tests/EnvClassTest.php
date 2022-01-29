<?php declare(strict_types=1);

namespace msztorc\LaravelEnv\Tests\EnvClassTest;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use msztorc\LaravelEnv\Env;
use msztorc\LaravelEnv\LaravelEnvServiceProvider;
use Orchestra\Testbench\TestCase;

final class EnvClassTest extends TestCase
{
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

    public function testEnvGetSelected(): void
    {
        $env = new Env();
        $app_name = $env->getValue('APP_NAME');
        $this->assertEquals('Laravel', $app_name);

        $redis_host = $env->getValue('REDIS_HOST');
        $this->assertEquals('127.0.0.1', $redis_host);

        $mail_name = $env->getValue('MAIL_FROM_NAME');
        $this->assertEquals('${APP_NAME}', $mail_name);
    }

    public function testEnvGetAll(): void
    {
        $env_vars = [
            'APP_NAME' => 'Laravel',
            'APP_ENV' => 'local',
            'APP_KEY' => '',
            'APP_DEBUG' => 'true',
            'APP_URL' => 'http://localhost',
            'LOG_CHANNEL' => 'stack',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'laravel',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'BROADCAST_DRIVER' => 'log',
            'CACHE_DRIVER' => 'file',
            'QUEUE_CONNECTION' => 'sync',
            'SESSION_DRIVER' => 'file',
            'SESSION_LIFETIME' => '120',
            'REDIS_HOST' => '127.0.0.1',
            'REDIS_PASSWORD' => 'null',
            'REDIS_PORT' => '6379',
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.mailtrap.io',
            'MAIL_PORT' => '2525',
            'MAIL_USERNAME' => 'null',
            'MAIL_PASSWORD' => 'null',
            'MAIL_ENCRYPTION' => 'null',
            'MAIL_FROM_ADDRESS' => 'null',
            'MAIL_FROM_NAME' => '${APP_NAME}',
            'AWS_ACCESS_KEY_ID' => '',
            'AWS_SECRET_ACCESS_KEY' => '',
            'AWS_DEFAULT_REGION' => 'us-east-1',
            'AWS_BUCKET' => '',
            'PUSHER_APP_ID' => '',
            'PUSHER_APP_KEY' => '',
            'PUSHER_APP_SECRET' => '',
            'PUSHER_APP_CLUSTER' => 'mt1',
            'MIX_PUSHER_APP_KEY' => '${PUSHER_APP_KEY}',
            'MIX_PUSHER_APP_CLUSTER' => '${PUSHER_APP_CLUSTER}',
            'dummy_variable' => 'Adf4$r-Ac"',
        ];

        $env = new Env();

        foreach ($env_vars as $key => $val) {
            $ret_value = $env->getValue($key);
            $this->assertEquals($val, $ret_value);

            $ret_array = $env->getKeyValue($key);
            $this->assertIsArray($ret_array);
            $this->assertEquals([$key => $val], $ret_array);
        }
    }

    public function testEnvSetAll(): void
    {
        $env_vars = [
            'APP_NAME' => '',
            'APP_ENV' => '',
            'APP_KEY' => '',
            'APP_DEBUG' => '',
            'APP_URL' => '',
            'LOG_CHANNEL' => '',
            'DB_CONNECTION' => '',
            'DB_HOST' => '',
            'DB_PORT' => '',
            'DB_DATABASE' => '',
            'DB_USERNAME' => '',
            'DB_PASSWORD' => '',
            'BROADCAST_DRIVER' => '',
            'CACHE_DRIVER' => '',
            'QUEUE_CONNECTION' => '',
            'SESSION_DRIVER' => '',
            'SESSION_LIFETIME' => '',
            'REDIS_HOST' => '',
            'REDIS_PASSWORD' => '',
            'REDIS_PORT' => '',
            'MAIL_MAILER' => '',
            'MAIL_HOST' => '',
            'MAIL_PORT' => '',
            'MAIL_USERNAME' => '',
            'MAIL_PASSWORD' => '',
            'MAIL_ENCRYPTION' => '',
            'MAIL_FROM_ADDRESS' => '',
            'MAIL_FROM_NAME' => '',
            'AWS_ACCESS_KEY_ID' => '',
            'AWS_SECRET_ACCESS_KEY' => '',
            'AWS_DEFAULT_REGION' => '',
            'AWS_BUCKET' => '',
            'PUSHER_APP_ID' => '',
            'PUSHER_APP_KEY' => '',
            'PUSHER_APP_SECRET' => '',
            'PUSHER_APP_CLUSTER' => '',
            'MIX_PUSHER_APP_KEY' => '',
            'MIX_PUSHER_APP_CLUSTER' => '',
            'dummy_variable' => '',
        ];

        function genRandomString($length = 8)
        {
            return substr(str_shuffle(str_repeat($x = '$0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ', (int)ceil($length / strlen($x)))), 1, $length);
        }

        foreach ($env_vars as $key => &$val) {
            $val = genRandomString(rand(4, 25));
        }

        $env = new Env();

        foreach ($env_vars as $key => $val) {
            $new_val = $env->setValue($key, $val);
            $ver_val = $env->getValue($key);
            $this->assertTrue($new_val === $ver_val);
            $this->assertEquals($val, $ver_val);
        }
    }

    public function testEnvDelAll(): void
    {
        $env_vars = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL', 'LOG_CHANNEL', 'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'BROADCAST_DRIVER', 'CACHE_DRIVER', 'QUEUE_CONNECTION', 'SESSION_DRIVER', 'SESSION_LIFETIME', 'REDIS_HOST', 'REDIS_PASSWORD', 'REDIS_PORT', 'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME', 'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_BUCKET', 'PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER', 'MIX_PUSHER_APP_KEY', 'MIX_PUSHER_APP_CLUSTER', 'dummy_variable'];

        foreach ($env_vars as $key) {
            $env = new Env();

            $exists = $env->exists($key);
            $this->assertTrue($exists);

            $notChanged = $env->wasChanged();
            $this->assertFalse($notChanged);

            $env->deleteVariable($key);

            $changed = $env->wasChanged();
            $this->assertTrue($changed);

            $saved = $env->isSaved();
            $this->assertTrue($saved);

            unset($env);

            $env = new Env();
            $notExists = $env->exists($key);

            $this->assertFalse($notExists);

            unset($env);
        }
    }

    public function testUrls(): void
    {
        $env = new Env();
        $app_url = 'https://mywebsite.com';
        $env->setValue('APP_URL', $app_url);
        $ver_value = $env->getValue('APP_URL');
        $this->assertEquals($app_url, $ver_value);
    }

    public function testEmptyValue(): void
    {
        $env = new Env();
        $env->setValue('APP_NAME', '');
        $ver_value = $env->getValue('APP_NAME');
        $this->assertEmpty($ver_value);
        $this->assertEquals('', $ver_value);
    }

    public function testSetValueWithHyphen(): void
    {
        $env = new Env();
        $env->setValue('APP_NAME', 'my-app');
        $ver_value = $env->getValue('APP_NAME');
        $this->assertEquals('my-app', $ver_value);
    }
}
