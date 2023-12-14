<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "router/Router.php";
require_once "test_endpoints.php";
require_once "auth/login.php";
require_once "auth/create_invite_link_endpoint.php";

$pre_auth = new Router;

register_test_endpoints($pre_auth);
register_auth_endpoints($pre_auth);

if($pre_auth->run()) {
    exit;
}

$account = check_session();

if($account === null) {
    header("Location: /auth");
    exit;
}

$post_auth = new Router;

register_logout_endpoints($post_auth);
$post_auth->get("/", view_with_account("index", $account)->callback());
$post_auth->get("/create_invite_link", view("create_invite_link")->callback());
$post_auth->post("/create_invite_link", fn() => create_invite_link_endpoint($account));

if(!$post_auth->run()) {
    http_response_code(404);
    echo "404 Not Found";
}
