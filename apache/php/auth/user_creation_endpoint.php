<?php

function root_user_creation_endpoint($secret_code):void {
    $db = connect_to_db();
    if(!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["email"]) || !isset($_POST["secret_code"])) {
        echo "Missing username or password";
        return;
    }
    if($_POST["secret_code"] !== $secret_code) {
        echo "Invalid secret code";
        return;
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $account = new Account(0, $username, $email, true, true, true);
    if(!$account->create_account($password)) {
        echo "Could not create root user.";
        return;
    }
    echo "Root user created.<br>";
    create_session($account->id);
    echo "Session started.<br>";
}
