<?php

require_once "router/Router.php";

function register_test_endpoints(Router $r) {
    $r->get("/ping_php_router_get", function() {
        echo "Hello from the PHP router GET endpoint!<br>";
    });

    $r->post("/ping_php_router_post", function() {
        echo "Hello from the PHP router POST endpoint!<br>";
    });

    $r->all("/ping_php_router", function() {
        echo "Hello from the PHP router all HTTP request methods endpoint!<br>";
        echo "Request method: " . $_SERVER["REQUEST_METHOD"];
    });
}
