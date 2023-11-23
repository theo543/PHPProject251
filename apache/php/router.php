<?php

function match_route(string $kind, string $path, callable $callback) {
    if($_SERVER["REQUEST_METHOD"] != $kind) {
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

get("/ping_php_router", function() {
    echo "Hello from the PHP router!<br>";
    echo "Request URI: " . $_SERVER["REQUEST_URI"];
});
