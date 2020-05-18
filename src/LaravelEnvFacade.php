<?php

namespace msztorc\LaravelEnv;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Msztorc\LaravelEnv\Skeleton\SkeletonClass
 */
class LaravelEnvFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-env';
    }
}
