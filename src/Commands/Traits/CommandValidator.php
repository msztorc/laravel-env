<?php

namespace msztorc\LaravelEnv\Commands\Traits;

use InvalidArgumentException;

trait CommandValidator
{

    private $invalidKeyException = 'Invalid environment key. Only use upper letters, digits, and underscores. A variable must start with the letter.';

    /**
     * Check if a given string is valid as an environment variable key.
     *
     * @param string $key
     * @return bool
     */
    protected function isValidKey(string $key): bool
    {
        if (!preg_match('/^[A-Z_]\w*$/', $key)) {
            throw new InvalidArgumentException($this->invalidKeyException);
        }

        return true;
    }
}
