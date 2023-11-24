<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once("Router.php");

$r = new Router;

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

$r->run();
