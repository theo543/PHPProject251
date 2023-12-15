<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "router/Router.php";
require_once "test_endpoints.php";
require_once "auth/login.php";
require_once "auth/create_invite_link_endpoint.php";
require_once "auth/csrf.php";

$pre_auth = new Router;

register_test_endpoints($pre_auth);

$pre_auth_captcha_required = new Router;
register_auth_endpoints($pre_auth_captcha_required);
$pre_auth_captcha_required->add_post_interceptor(function(): bool {
    if(!validate_post_request_recaptcha()) {
        echo "Invalid captcha";
        return true;
    }
    return false;
});
$pre_auth->add_subrouter($pre_auth_captcha_required);

if($pre_auth->run()) {
    exit;
}

$validated_account = check_session();

if($validated_account === null) {
    header("Location: /auth");
    exit;
}

$account = $validated_account->account;

$post_auth = new Router;

$csrf = bind_generate_csrf_token($validated_account->session_id, $account->id);

register_logout_endpoints($post_auth);
$post_auth->get("/", view_with_account("index", $account)->set('csrf', $csrf)->callback());
$post_auth->get("/create_invite_link", view("create_invite_link")->set('csrf', $csrf)->callback());
$post_auth->post("/create_invite_link", fn() => create_invite_link_endpoint($account));
$post_auth->add_post_interceptor(function() use ($validated_account, $account): bool {
    if(!isset($_POST["csrf-token"]) || !isset($_POST["csrf-token-hmac"])) {
        echo "Missing CSRF token";
        return true;
    }
    if(!validate_csrf_token($validated_account->session_id, $account->id, $_POST["csrf-token"], $_POST["csrf-token-hmac"])) {
        echo "Invalid CSRF token";
        return true;
    }
    return false;
});

if(!$post_auth->run()) {
    http_response_code(404);
    echo "404 Not Found";
}
