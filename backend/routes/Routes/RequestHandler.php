<?php

namespace Routes;

use AddressBook\Container;
use AddressBook\Responses\JsonResponse;
use ReflectionException;

class RequestHandler
{
    private array $routes = [];
    private array $middlewares = [];
    private string $prefix = '';

    public function group($attributes, $callback): void
    {
        $prefix = $attributes['prefix'] ?? '';
        $middlewares = $attributes['middlewares'] ?? [];

        // Push the prefix and middlewares to their respective arrays
        array_push($this->middlewares, $middlewares);
        $this->prefix .= $prefix;

        // Call the group callback
        $callback($this);

        // Remove the prefix and middlewares after the group is defined
        $this->prefix = substr($this->prefix, 0, -strlen($prefix));
        array_pop($this->middlewares);
    }

    public function addRoute($method, $path, $controller, $action = 'index', $middlewares = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->prefix . $path,
            'controller' => $controller,
            'action' => $action,
            'middlewares' => array_merge($this->getMiddlewares(), $middlewares),
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function handleRequest(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = $this->convertRouteToRegex($route['path']);

            if (preg_match($pattern, $requestUri, $matches) && $requestMethod === $route['method']) {
                $params = $this->extractParams($matches);

                foreach ($route['middlewares'] as $middleware) {
                    $middleware->handle($this, $params);
                }

                $controllerClass = $route['controller'];
                $action = $route['action'];

                $container = new Container();
                $controller = $container->resolve($controllerClass);

                call_user_func_array([$controller, $action], $params);

                return;
            }
        }

        // Handle 404 Not Found
        JsonResponse::handle(['message' => 'Not Found'], 404);
    }

    private function getMiddlewares(): array
    {
        return array_merge(...$this->middlewares);
    }

    private function convertRouteToRegex($route): string
    {
        $routeRegex = preg_replace('/:\w+/', '(.+)', $route);
        return '/^' . str_replace('/', '\/', $routeRegex) . '$/';
    }

    private function extractParams($matches): array
    {
        return array_slice($matches, 1);
    }
}