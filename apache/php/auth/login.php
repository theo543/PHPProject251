<?php

require_once "views/render_view.php";
require_once "auth/login_endpoint.php";
require_once "auth/logout_endpoint.php";
require_once "auth/root_user_creation_endpoint.php";

function register_auth_endpoints(Router $r) {
    $r->post("/auth", fn() => login_endpoint());
    $r->get("/auth", view("auth")->callback());
    $r->post("/logout", fn() => logout_endpoint());
    $r->get("/logout", view("logout")->callback());
    $debugmode = include("debugmode.secrets.php");
    if($debugmode["allow_root_create"]) {
        $r->post("/create_root_user", fn() => root_user_creation_endpoint($debugmode["username"], $debugmode["email"], $debugmode["password"]));
        $r->get("/create_root_user", view("create_root_user")->callback());
    }
}
