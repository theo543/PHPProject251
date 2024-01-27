<?php
declare(strict_types=1);

function put_log(string $str) {
    $log = "/var/www/PHPSites/phplog.log";
    file_put_contents($log, $str, FILE_APPEND);
}

$log_data = print_r(apache_request_headers(), true);
if($log_data === false) {
    $log_data = "apache_request_headers() returned false";
}
put_log("\n\n\n[" . date("Y-m-d H:i:s") . "]:\n" . $log_data . "\n");

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "router/Router.php";
require_once "endpoints/all.php";
require_once "auth/csrf.php";
require_once "posts/list.php";

$session = check_session();
if($session === null) {
    echo "new session...";
    $sessionID = create_session(null);
    if($sessionID === null) {
        die("Failed to create session");
    }
    // Pre-auth session is needed to prevent login CSRF
    // Assume the client will use the session cookie for the login/signup request
    // (If they have cookies disabled they couldn't login anyway)
    $session = new Session(null, $sessionID);
}

put_log('$_SERVER: ' . print_r($_SERVER, true));
put_log('$_GET: ' . print_r($_GET, true));
put_log('$_POST: ' . print_r($_POST, true));
put_log('$_FILES: ' . print_r($_FILES, true));
put_log('$_COOKIE: ' . print_r($_COOKIE, true));
put_log('$_ENV: ' . print_r($_ENV, true));

$csrf_gen = bind_generate_csrf_token($session);

$r = new Router;
$r->set_view_param('csrf', $csrf_gen);

$r->add_post_interceptor(function () use ($session): bool {
    if (!isset($_POST["csrf-token"]) || !isset($_POST["csrf-token-hmac"])) {
        echo "Missing CSRF token";
        return true;
    }
    if (!validate_csrf_token($session, $_POST["csrf-token"], $_POST["csrf-token-hmac"])) {
        echo "Invalid CSRF token";
        return true;
    }
    return false;
});

register_test_endpoints($r);
register_auth_endpoints($r);

$post_auth = new Router;
if($session->account === null) {
    $post_auth->add_interceptor(function() {
        // They can't use these endpoints without an account,
        // but if they try to we shouldn't just pretend they don't exist and 404
        header("Location: /auth");
        return true;
    });
}
$account = $session->account;
$post_auth->set_view_param('account', $account);
$post_auth->set_view_param('post_list', fn() => post_list());
register_logout_endpoints($post_auth);
register_post_display_endpoint($post_auth);
register_edit_endpoints($post_auth, $account);
register_mail_endpoints($post_auth, $account);
$post_auth->get("/", view("index"));
$post_auth->get("/create_invite_link", view("create_invite_link"));
$post_auth->post("/create_invite_link", fn() => create_invite_link_endpoint($account));

$r->add_subrouter($post_auth);

$r->all(null, function() {
    http_response_code(404);
    echo "404 Not Found";
});

if($r->run() === false) {
    die("Router failed to run despite having a catch-all 404 route.");
}
