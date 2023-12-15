<?php

require_once "views/render_view.php";
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

function register_auth_endpoints(Router $r) {
    $recaptcha = array('recaptcha' => get_recaptcha_html());
    $r->post("/auth", fn() => login_endpoint());
    $r->get("/auth", view("auth")->set_many($recaptcha)->callback());
    $debugmode = get_debugmode();
    if($debugmode["allow_root_create"]) {
        $r->post("/create_root_user", fn() => root_user_creation_endpoint($debugmode["username"], $debugmode["email"], $debugmode["password"]));
        $r->get("/create_root_user", view("create_root_user")->callback());
    }
    $r->post("/invite", fn() => invite_endpoint());
    $r->get("/invite", view("invite")->set_many($recaptcha)->callback());
}

function register_logout_endpoints(Router $r) {
    $r->post("/logout", fn() => logout_endpoint());
}
