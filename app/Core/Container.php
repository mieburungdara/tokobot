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
    public function build(string $className, array $predefinedParams = []): object
    {
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new \Exception("Class {$className} is not instantiable.");
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            $type = $parameter->getType();

            if (array_key_exists($paramName, $predefinedParams)) {
                // Use the predefined parameter value
                $dependencies[] = $predefinedParams[$paramName];
            } elseif ($type && !$type->isBuiltin()) {
                $dependencyClassName = $type->getName();
                if ($dependencyClassName === self::class) {
                    $dependencies[] = $this;
                } else {
                    $dependencies[] = $this->get($dependencyClassName);
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new \Exception("Cannot resolve parameter '{$paramName}' in class {$className}");
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
