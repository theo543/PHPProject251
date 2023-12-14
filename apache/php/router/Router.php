<?php

class Route {
    public string | null $kind;
    public string $path;
    public $callback;

    public function __construct(string | null $kind, string $path, callable $callback) {
        $this->kind = $kind;
        $this->path = $path;
        $this->callback = $callback;
    }
}

class Router {
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
    public function match_route(string | null $kind, string $path, callable $callback): void {
        $route = new Route($kind, $path, $callback);
        array_push($this->routes, $route);
    }
    public function get(string $path, callable $callback): void {
        $this->match_route("GET", $path, $callback);
    }
    public function post(string $path, callable $callback): void {
        $this->match_route("POST", $path, $callback);
    }
    public function all(string $path, callable $callback): void {
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
    public function run(): bool {
        $request_path = strtok($_SERVER["REQUEST_URI"], '?');
        foreach($this->routes as $route) {
            if($route instanceof Router) {
                if($this->run_interceptors()) {
                    return true;
                }
                if($route->run()) {
                    return true;
                }
                continue;
            }
            if(($route->kind !== null) && $_SERVER["REQUEST_METHOD"] !== $route->kind) {
                continue;
            }
            if($request_path != $route->path) {
                continue;
            }
            if($this->run_interceptors()) {
                return true;
            }
            ($route->callback)();
            return true;
        }
        return false;
    }
    public function __construct() {
    }
}
