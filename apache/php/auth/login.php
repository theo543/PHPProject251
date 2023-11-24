<?php

require_once "database/db.php";
require_once "auth/session.php";
require_once "auth/account.php";
require_once "views/render_view.php";

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
    $query = $db->prepare("SELECT user_id, bcrypt_password FROM users WHERE name = ?");
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
    create_session($row["user_id"]);
    header("Location: " . $post_login_redirect);
}

function logout_endpoint():void {
    if(isset($_COOKIE["session_token"]) && isset($_COOKIE["session_user_id"])) {
        if(!end_session($_COOKIE["session_user_id"], $_COOKIE["session_token"])) {
            echo "Could not find your session in the database.";
            return;
        }
        setcookie("session_token", "", time() - 3600, "/");
        setcookie("session_user_id", "", time() - 3600, "/");
        echo "Session deleted from database and cleared from your browser.";
    } else {
        echo "Token or ID not specified.";
    }
}

function root_user_creation_endpoint($secret_code):void {
    $db = connect_to_db();
    if(!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["email"]) || !isset($_POST["secret_code"])) {
        die("Missing username or password");
    }
    if($_POST["secret_code"] !== $secret_code) {
        die("Invalid secret code");
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $account = new Account(0, $username, $email, true, true, true);
    $account->create_account($password);
    echo "Root user created.<br>";
    create_session($account->id);
    echo "Session started.<br>";
}

function register_auth_endpoints(Router $r) {
    $r->post("/auth", fn() => login_endpoint());
    $r->get("/auth", create_view_callback("auth"));
    $r->post("/logout", fn() => logout_endpoint());
    $r->get("/logout", create_view_callback("logout"));
    $debugmode = include("debugmode.secrets.php");
    if($debugmode["allow_root_create"]) {
        $r->post("/create_root_user", fn() => root_user_creation_endpoint($debugmode["allow_root_create_secret_code"]));
        $r->get("/create_root_user", create_view_callback("create_root_user"));
    }
}
