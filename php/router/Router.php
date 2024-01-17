<?php

class Route {
    public ?string $kind;
    public ?string $path;
    public $callback;

    public function __construct(?string $kind, ?string $path, View|callable $callback) {
        $this->kind = $kind;
        $this->path = $path;
        $this->callback = $callback;
    }
}

class Router {
    private array $view_params = [];
    private array $interceptors = [];
    private array $routes = [];
    private function run_interceptors(): bool {
        foreach($this->interceptors as $interceptor) {
            if($interceptor()) {
                return true;
            }
        }
        return false;
    }
    public function match_route(?string $kind, ?string $path, View|callable $callback): void {
        $route = new Route($kind, $path, $callback);
        array_push($this->routes, $route);
    }
    public function get(?string $path, View|callable $callback): void {
        $this->match_route("GET", $path, $callback);
    }
    public function post(?string $path, View|callable $callback): void {
        $this->match_route("POST", $path, $callback);
    }
    public function all(?string $path, View|callable $callback): void {
        $this->match_route(null, $path, $callback);
    }
    public function add_subrouter(Router $subrouter): void {
        array_push($this->routes, $subrouter);
    }
    public function add_interceptor(callable $interceptor): void {
        array_push($this->interceptors, $interceptor);
    }
    public function add_post_interceptor(callable $interceptor): void {
        $this->add_interceptor(function() use($interceptor) {
            if($_SERVER["REQUEST_METHOD"] !== "POST") {
                return false;
            }
            return $interceptor();
        });
    }
    public function set_view_param(string $key, $value): void {
        $this->view_params[$key] = $value;
    }
    public function run(array $extra_view_params = array()): bool {
        $request_path = strtok($_SERVER["REQUEST_URI"], '?');
        foreach($this->routes as $route) {
            if($route instanceof Router) {
                if($this->run_interceptors()) {
                    return true;
                }
                if($route->run(array_merge($this->view_params, $extra_view_params))) {
                    return true;
                }
                continue;
            }
            if(($route->kind !== null) && $_SERVER["REQUEST_METHOD"] !== $route->kind) {
                continue;
            }
            if($route->path !== null && $request_path !== $route->path) {
                continue;
            }
            if($this->run_interceptors()) {
                return true;
            }
            if($route->callback instanceof View) {
                $route->callback->set_many($this->view_params);
                $route->callback->set_many($extra_view_params);
                $route->callback->render();
            } else {
                ($route->callback)();
            }
            return true;
        }
        return false;
    }
    public function __construct() {
    }
}
