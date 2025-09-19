<?php

namespace TokoBot\Core;

use TokoBot\Controllers\ErrorController;
use FastRoute\Dispatcher;

class App
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $dispatcher = $this->container->get('dispatcher');

        // Fetch method and URI
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $errorController = new ErrorController();
                $errorController->notFound();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                echo "405 Method Not Allowed";
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // --- Automatic Validation Handling ---
                if (is_array($handler) && count($handler) >= 2) {
                    $controllerClass = $handler[0];
                    $methodName = $handler[1];

                    try {
                        $reflectionMethod = new \ReflectionMethod($controllerClass, $methodName);
                        $attributes = $reflectionMethod->getAttributes(\TokoBot\Core\Validation\Validate::class);

                        if (count($attributes) > 0) {
                            $validationAttribute = $attributes[0]->newInstance();
                            $formRequestClass = $validationAttribute->formRequestClass;

                            if (class_exists($formRequestClass)) {
                                /** @var \TokoBot\Core\Validation\FormRequest $formRequest */
                                $formRequest = new $formRequestClass();
                                if (!$formRequest->validate()) {
                                    $formRequest->redirectBack(); // This will exit
                                }
                            }
                        }
                    } catch (\ReflectionException $e) {
                        // Method doesn't exist, let it fail later
                    }
                }
                // --- End Automatic Validation ---

                $middlewares = [];
                if (isset($handler[2]['middleware'])) {
                    $middlewareDefinitions = $handler[2]['middleware'];
                    foreach ($middlewareDefinitions as $middlewareDef) {
                        if (is_array($middlewareDef)) {
                            $middlewareClass = 'TokoBot\\Middleware\\' . $middlewareDef[0];
                            $constructorArgs = array_slice($middlewareDef, 1);

                            // Use reflection to map constructor arguments by position
                            $reflectionMethod = new \ReflectionMethod($middlewareClass, '__construct');
                            $params = $reflectionMethod->getParameters();

                            $predefinedParams = [];
                            foreach ($constructorArgs as $index => $arg) {
                                if (isset($params[$index])) {
                                    $predefinedParams[$params[$index]->getName()] = $arg;
                                }
                            }
                            $middlewares[] = $this->container->build($middlewareClass, $predefinedParams);
                        } else {
                            $middlewareClass = 'TokoBot\\Middleware\\' . $middlewareDef;
                            $middlewares[] = $this->container->build($middlewareClass);
                        }
                    }
                    // Remove middleware definitions from handler to avoid passing them to controller
                    unset($handler[2]);
                }

                // Build the pipeline
                $pipeline = array_reduce(
                    array_reverse($middlewares),
                    function ($next, $middleware) {
                        return function () use ($middleware, $next) {
                            return $middleware->handle($next);
                        };
                    },
                    function () use ($handler, $vars) {
                        // Final handler (controller action)
                        if (is_array($handler) && count($handler) >= 2) {
                            $controllerClass = $handler[0];
                            $method = $handler[1];

                            $controller = $this->container->build($controllerClass);

                            return call_user_func_array([$controller, $method], $vars);
                        } else {
                            // Handle closure or other callable
                            return call_user_func_array($handler, $vars);
                        }
                    }
                );

                // Execute the pipeline
                $pipeline();
                break;
        }
    }
}
