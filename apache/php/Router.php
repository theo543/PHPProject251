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
    private array $routes = [];
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
    public function run(): bool {
        $request_path = strtok($_SERVER["REQUEST_URI"], '?');
        foreach($this->routes as $route) {
            if(($route->kind !== null) && $_SERVER["REQUEST_METHOD"] !== $route->kind) {
                continue;
            }
            if($request_path != $route->path) {
                continue;
            }
            ($route->callback)();
            return true;
        }
        return false;
    }
    public function __construct() {
    }
}
