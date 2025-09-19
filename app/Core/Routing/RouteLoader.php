<?php

namespace TokoBot\Core\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use ReflectionClass;
use ReflectionMethod;

class RouteLoader
{
    private array $routes = [];

    public function __construct(private array $controllerPaths)
    {
    }

    /**
     * Scans controller directories, finds Route attributes, and registers them.
     *
     * @return Dispatcher
     */
    public function register(): Dispatcher
    {
        $this->scanDirectories();
        return \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $route) {
                // The handler now includes middleware information for our App core to process.
                $handler = [
                    $route['controller'],
                    $route['action'],
                    ['middleware' => $route['middleware']]
                ];
                $r->addRoute($route['method'], $route['path'], $handler);
            }
        });
    }

    /**
     * Scans directories for PHP files and processes them.
     */
    private function scanDirectories(): void
    {
        foreach ($this->controllerPaths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isDir() || $file->getExtension() !== 'php') {
                    continue;
                }
                $this->processFile($file->getPathname());
            }
        }
    }

    /**
     * Processes a single file to find a class and its route attributes.
     *
     * @param string $filePath
     */
    private function processFile(string $filePath): void
    {
        // Convert filesystem path to a PSR-4 class name
        $class = str_replace(
            [ROOT_PATH . '/app/', '/', '.php'],
            ['TokoBot\\', '\\', ''],
            $filePath
        );

        if (!class_exists($class)) {
            return;
        }

        $reflectionClass = new ReflectionClass($class);
        if ($reflectionClass->isAbstract()) {
            return;
        }

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                $this->routes[] = [
                    'path' => $route->path,
                    'method' => strtoupper($route->method),
                    'controller' => $class,
                    'action' => $method->getName(),
                    'middleware' => $route->middleware,
                ];
            }
        }
    }
}
