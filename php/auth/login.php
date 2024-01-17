<?php

require_once "views/View.php";
require_once "auth/login_endpoint.php";
require_once "auth/logout_endpoint.php";
require_once "auth/root_user_creation_endpoint.php";
require_once "auth/invite_endpoint.php";
require_once "load_config_file.php";
require_once "auth/recaptcha.php";

function get_debugmode() {
    static $debugmode = null;
    if($debugmode === null) {
        $debugmode = load_config_file(dirname(__FILE__) . "/debugmode.secrets.php", array("allow_root_create", "username", "email", "password"), array("allow_root_create" => false));
    }
    return $debugmode;
}

function register_auth_endpoints(Router $router) {
    $r = new Router;
    $r->set_view_param('recaptcha', get_recaptcha_html());
    $r->post("/auth", fn() => login_endpoint());
    $r->get("/auth", view("auth"));
    $debugmode = get_debugmode();
    if($debugmode["allow_root_create"]) {
        $r->post("/create_root_user", fn() => root_user_creation_endpoint($debugmode["username"], $debugmode["email"], $debugmode["password"]));
        $r->get("/create_root_user", view("create_root_user"));
    }
    $r->post("/invite", fn() => invite_endpoint());
    $r->get("/invite", view("invite"));
    $r->add_post_interceptor(function(): bool {
        if(!validate_post_request_recaptcha()) {
            echo "Invalid captcha";
            return true;
        }
        return false;
    });
    $router->add_subrouter($r);
}

function register_logout_endpoints(Router $r) {
    $r->get("/logout", view("logout_full"));
    $r->post("/logout", fn() => logout_endpoint());
}
