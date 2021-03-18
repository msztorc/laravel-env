<?php

namespace msztorc\LaravelEnv\Commands\Traits;

use InvalidArgumentException;

trait CommandValidator
{

    /**
     * Check if a given string is valid as an environment variable key.
     *
     * @param string $key
     * @return bool
     */
    protected function isValidKey(string $key): bool
    {
        if (!preg_match('/^[a-zA-Z_0-9]+$/', $key)) {
            throw new InvalidArgumentException('Invalid environment key. Only use digits, letters and underscores');
        }

        return true;
    }
}
