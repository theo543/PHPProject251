<?php

require_once "database/db.php";

function login_endpoint():void {
    if(!isset($_POST["username"]) || !isset($_POST["password"])) {
        die("Missing username or password");
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    $post_login_redirect = "/";
    if(isset($_POST["post_login_redirect"])) {
        $post_login_redirect = $_POST["post_login_redirect"];
    }
    $row = fetch_one("SELECT user_id, bcrypt_password FROM users WHERE name = ?", [$username]);
    if($row === null) {
        die("Invalid username or password");
    }
    $correct_hash = $row["bcrypt_password"];
    if(!password_verify($password, $correct_hash)) {
        die("Invalid username or password");
    }
    if(!create_session($row["user_id"])) {
        die("Could not create session");
    }
    header("Location: " . $post_login_redirect);
}
