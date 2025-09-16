<?php

namespace TokoBot\Core;

class Container
{
    private array $bindings = [];

    public function set(string $key, $value): void
    {
        $this->bindings[$key] = $value;
    }

    public function get(string $key)
    {
        if (isset($this->bindings[$key])) {
            return $this->bindings[$key];
        }

        throw new \Exception("No binding found for {$key}");
    }
}
