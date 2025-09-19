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
        if (!isset($this->bindings[$key])) {
            throw new \Exception("No binding found for {$key}");
        }

        $binding = $this->bindings[$key];

        // If the binding is a closure, it's a factory. Resolve it.
        if ($binding instanceof \Closure) {
            // Store the instance for subsequent calls (singleton)
            $this->bindings[$key] = $binding($this);
        }

        return $this->bindings[$key];
    }

    /**
     * Build an instance of the given class, resolving dependencies automatically.
     *
     * @param string $className
     * @return object
     * @throws \Exception
     */
    public function build(string $className): object
    {
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new \Exception("Class {$className} is not instantiable.");
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            // No constructor, just create a new instance
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type && !$type->isBuiltin()) {
                $dependencyClassName = $type->getName();
                // Check if the dependency is the container itself
                if ($dependencyClassName === self::class) {
                    $dependencies[] = $this;
                } else {
                    // Get the dependency from the container
                    $dependencies[] = $this->get($dependencyClassName);
                }
            } else {
                // Cannot resolve built-in types (string, int, etc.) without default value
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve primitive parameter '{$parameter->getName()}' in class {$className}");
                }
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
