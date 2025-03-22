<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $errorHandler;

    public function add(string $method, string $path, array $controller)
    {
        $path = $this->normelizePath($path);

        ## replacing the natch value of the routes path with the second regular expresion in the function (deleting the {} from the routs path)
        $regexPath = preg_replace('#{[^/]+}#', '([^/]+)', $path);

        $this->routes[] = [
            'path' => $path,
            'method' => $method,
            'controller' => $controller,
            'middlewares' => [],
            'regexPath' => $regexPath
        ];
    }
    private function normelizePath(string $path)
    {
        trim($path, '/');
        $path = "/{$path}/";
        $path = preg_replace("#[/]{2,}#", '/', $path);
        return $path;
    }

    public function dispatch(string $path, string $method,Container $container=null)
    {
        $path = $this->normelizePath($path);

        $method = strtoupper($_POST['_METHOD'] ?? $method);

        foreach ($this->routes as $route) {
                            
                            ## the match values will be stored in paramvalues array
            if (!preg_match("#^{$route['regexPath']}$#", $path, $paramValues) || $route['method'] !== $method) {
                continue;
            }

            ## remove the first item in the array( the full path) because weonly  need  the route param
            array_shift($paramValues);

            ## get the all matches of the place holder name from the original path , and stor them
            preg_match_all('#{([^/]+)}#', $route['path'], $paramKeys);

            $paramKeys = $paramKeys[1];
            
            ## get an assosiatuve array with the keyparam and his correspending value
            $params = array_combine($paramKeys, $paramValues);

            [$class, $function] = $route['controller'];

            $controllerInstance = $container ? 
                $container->resolve($class) :
                new $class;

            $action = fn() => $controllerInstance->{$function}($params);

            $allMiddleware = [...$route['middlewares'], ...$this->middlewares];

            foreach($allMiddleware as $middleware){
                $middlewareInstance = $container ? 
                $container->resolve($middleware):
                new $middleware;
                
                $action = fn() => $middlewareInstance->process($action);
            }

            $action();
            return;
        }

        $this->dispatchNotFound($container);
    }

    public function addMiddleware(string $middleware){
        $this->middlewares[] = $middleware;
    }

    public function addRouteMiddleware(string $middleware){

        ## grep the last route registred
        $lastRouteKey = array_key_last($this->routes);

        $this->routes[$lastRouteKey]['middleware'][] = $middleware ;
    }

    public function setErrorHander(array $controller)
    {
        $this->errorHandler = $controller;
    }

    public function dispatchNotFound(Container $container)
    {
        [$class, $function] = $this->errorHandler;

            $controllerInstance = $container ? 
                $container->resolve($class) :
                new $class;

            $action = fn() => $controllerInstance->{$function}();

            foreach($this->middlewares as $middleware){
                $middlewareInstance = $container ? 
                $container->resolve($middleware):
                new $middleware;
                
                $action = fn() => $middlewareInstance->process($action);
            }

            $action();
    }
}
