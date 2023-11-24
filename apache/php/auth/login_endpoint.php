<?php

require_once "database/db.php";

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
