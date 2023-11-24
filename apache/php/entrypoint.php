<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "router/Router.php";
require_once "endpoints/test_endpoints.php";
require_once "endpoints/404.php";
require_once "auth/login.php";

$pre_auth = new Router;

register_test_endpoints($pre_auth);
register_auth_endpoints($pre_auth);

if($pre_auth->run()) {
    exit;
}

if(!check_session()) {
    // TODO redirect to the actual page to login once it's implemented
    http_response_code(403);
    die("Not logged in");
}

$post_auth = new Router;

$post_auth->get("/", function() {
    echo "Hi! You're logged in. You must have used the create root user endpoint to get an account, since there's no way to register yet.";
});

if($post_auth->run()) {
    exit;
}

