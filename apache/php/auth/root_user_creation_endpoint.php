<?php

require_once "auth/account.php";
require_once "auth/session.php";

function root_user_creation_endpoint($username, $email, $password) {
    if(!isset($_POST["password"]) || $_POST["password"] !== $password) {
        echo "Invalid password.";
        return;
    }
    $account = create_account($username, $email, $password, true, true, true);
    if($account === null) {
        echo "Could not create root user.";
        return;
    }
    echo "Root user created.<br>";
    create_session($account->id);
    echo "Session started.<br>";
    echo "You should now disable the root user creation form and <b>remove the password from debugmode.secrets.php</b>.";
}
