<?php

namespace DeveoDK\CoreTimeTracker\Services;

class OptionService
{
    /** @var array */
    protected $options = [];

    /**
     * Options constructor.
     */
    public function __construct()
    {
        $this->options = config('api.authenticator');
    }

    /**
     * Return all options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get option value from key
     * @param string $key
     * @return array|mixed|null|string
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->options)) {
            return null;
        }
        /** @var string|array|mixed $options */
        $options = $this->options[$key];
        return $options;
    }
}
