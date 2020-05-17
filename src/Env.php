<?php

namespace msztorc\LaravelEnv;

class Env {

    private $_envContent = null;
    private $_envVars = null;
    private $_envPath = null;
    private $_saved = false;
    private $_changed = false;

    public function __construct()
    {
        $this->_envPath = app()->environmentFilePath();
        $this->_envContent = file_get_contents($this->_envPath);

        $this->parse();
    }

    /**
     *  Parse env content into array
     */
    public function parse() {

        $env_lines = preg_split('/\r\n|\r|\n/', $this->_envContent);

        foreach ($env_lines as $line)
        {
            if (strlen($line)) {
                [$key, $val] = explode('=', $line);
                $this->_envVars[$key] = $val;
            }
        }
    }


    /**
     * Check if the variable exists
     * @param string $key Variable key
     * @return bool
     */
    public function exists(string $key): bool
    {
        if (is_null($this->_envVars))
            $this->parse();

        return isset($this->_envVars[strtoupper($key)]);
    }

    /**
     * Get the current env variable value
     *
     * @param string $key Variable key
     * @return string
     */
    public function getValue(string $key): string
    {
        if (is_null($this->_envVars))
            $this->parse();

        return $this->_envVars[strtoupper($key)] ?? '';
    }


    /**
     * Get env key-value
     *
     * @param string $key Variable key
     * @return array
     */
    public function getKeyValue(string $key): array
    {
        if (is_null($this->_envVars))
            $this->parse();

        return [strtoupper($key) => $this->_envVars[strtoupper($key)]] ?? [];
    }


    /**
     * Set env variable value
     * @param string $key Variable key
     * @param string $value Variable value
     * @param bool $save Save to .env file
     * @return string
     */
    public function setValue(string $key, string $value, $save = true): string
    {
        $key = strtoupper($key);
        $value = $this->_prepareValue($value);

        if ($this->exists($key)) {
            $current_val = $this->getValue($key);
            $this->_envContent = str_replace("{$key}={$current_val}", "{$key}={$value}", $this->_envContent);
        } else {
            $this->_envContent .= PHP_EOL . "{$key}={$value}" . PHP_EOL;
        }

        $this->_changed = true;
        $this->_saved = false;

        $this->parse();
        if ($save) $this->write();

        return $this->getValue($key);

    }


    public function deleteVariable($key)
    {
        $key = strtoupper($key);

        if ($this->exists($key)) {
            $this->_envContent = preg_replace("/^{$key}=.*$/m", '', $this->_envContent);
            dd($this->_envContent);
        }

    }

    /**
     * Check and prepare value to be safe
     * @param string $value
     * @return string
     */
    private function _prepareValue(string $value): string
    {
        if (false !== strpos($value, ' ') || in_array($value[0], ['=', '$'])) {
            $value = '"' . $value . '"';
        }

        return $value;
    }

    /**
     * Get all env variables
     * @return array
     */
    public function getVariables(): array
    {
        return $this->_envVars;
    }

    /**
     * Get current env entire content from memory
     * @return string
     */
    public function getEnvContent(): string
    {
        return $this->_envContent;
    }

    public function write()
    {
        return $this->_saved = file_put_contents($this->_envPath, $this->_envContent);
    }

    /**
     * CHeck if the changes has been saved
     * @return bool
     */
    public function isSaved(): bool
    {
        return $this->_saved;
    }

    /**
     * Check if there were any env content changes
     * @return bool
     */
    public function wasChanged(): bool
    {
        return $this->_changed;
    }


    /*public function getEnvValue(string $key): string
    {
        preg_match("/^{$key}=(.*)$/m", $this->_envContent, $matches);
        return $matches[1] ?? '';
    }*/
}