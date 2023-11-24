<?php

require_once "database/db.php";
require_once "auth/session.php";
require_once "auth/account.php";

function login_endpoint():void {
    $db = connect_to_db();
    if(!isset($_POST["username"]) || !isset($_POST["password"])) {
        die("Missing username or password");
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    $post_login_redirect = "/";
    if(isset($_POST["post_login_redirect"])) {
        $post_login_redirect = $_POST["post_login_redirect"];
    }
    $query = $db->prepare("SELECT id, bcrypt_password FROM users WHERE username = ?");
    if(!$query) {
        die("Failed to prepare query: (" . $db->errno . ") " . $db->error);
    }
    $query->execute(array($username));
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    if($row === null) {
        die("Invalid username or password");
    }
    $correct_hash = $row["bcrypt_password"];
    if(!password_verify($password, $correct_hash)) {
        die("Invalid username or password");
    }
    create_session($row["id"]);
    header("Location: " . $post_login_redirect);
}

function logout_endpoint():void {
    if(isset($_COOKIE["session_token"]) && isset($_COOKIE["session_user_id"])) {
        end_session($_COOKIE["session_user_id"], $_COOKIE["session_token"]);
    }
    setcookie("session_token", "", time() - 3600, "/");
    setcookie("session_user_id", "", time() - 3600, "/");
}

function root_user_creation_endpoint():void {
    $db = connect_to_db();
    if(!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["email"])) {
        die("Missing username or password");
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $account = new Account(null, $username, $email, true, true, true);
    $account->create_account($password);
    create_session($account->id);
}

function register_auth_endpoints(Router $r) {
    $r->post("/auth", fn() => login_endpoint());
    $r->post("/logout", fn() => logout_endpoint());
    $debugmode = include("debugmode.secrets.php");
    if($debugmode["allow_root_create"]) {
        $r->post("/create_root_user", fn() => root_user_creation_endpoint());
    }
}
