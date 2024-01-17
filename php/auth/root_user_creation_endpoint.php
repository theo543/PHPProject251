<?php
declare(strict_types=1);

require_once "auth/account.php";
require_once "auth/session.php";

function root_user_creation_endpoint($username, $email, $password): void {
    if(!isset($_POST["password"]) || $_POST["password"] !== $password) {
        echo "Invalid password.";
        return;
    }
    $db = get_global_conn();
    $db->begin_transaction();
    $account = create_account($username, $email, $password, true, true, true, $db);
    if($account === null) {
        echo "Could not create root user.";
        $db->rollback();
        return;
    }
    echo "Root user created.<br>";
    if(!create_session($account->id)) {
        echo "Session could not be created. Please log in manually.";
    } else {
        echo "Session started.<br>";
    }
    echo "You should now disable the root user creation form and <b>remove the password from debugmode.secrets.php</b>.";
    $db->commit();
    echo "<p><a href='/'>Go to home page.</a></p>";
}
