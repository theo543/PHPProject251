<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

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

$post_auth->all("/tmp_test", function() {
    echo "This should be currently inaccessible as logging in is not yet implemented";
});

if($post_auth->run()) {
    exit;
}

handle_404();
