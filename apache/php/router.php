<?php

function match_route(string | null $kind, string $path, callable $callback) {
    if(($kind !== null) && $_SERVER["REQUEST_METHOD"] !== $kind) {
        return;
    }
    $request_path = strtok($_SERVER["REQUEST_URI"], '?');
    if($request_path != $path) {
        return;
    }
    $callback();
}

function get(string $path, callable $callback) {
    match_route("GET", $path, $callback);
}

function post(string $path, callable $callback) {
    match_route("POST", $path, $callback);
}

function all(string $path, callable $callback) {
    match_route(null, $path, $callback);
}

get("/ping_php_router_get", function() {
    echo "Hello from the PHP router GET endpoint!<br>";
});

post("/ping_php_router_post", function() {
    echo "Hello from the PHP router POST endpoint!<br>";
});

all("/ping_php_router", function() {
    echo "Hello from the PHP router all HTTP request methods endpoint!<br>";
    echo "Request method: " . $_SERVER["REQUEST_METHOD"];
});
