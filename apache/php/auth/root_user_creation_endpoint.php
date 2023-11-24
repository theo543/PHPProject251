<?php

require_once "auth/account.php";
require_once "auth/session.php";

function root_user_creation_endpoint($username, $email, $password) {
    $db = connect_to_db();
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
    if(!create_session($account->id)) {
        echo "Session could not be created. Please log in manually.";
    } else {
        echo "Session started.<br>";
    }
    echo "You should now disable the root user creation form and <b>remove the password from debugmode.secrets.php</b>.";
}
