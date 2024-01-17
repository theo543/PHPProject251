<?php

require_once "phpmailer/mail_cod.php";

function mail_admin_endpoint(Account $account): void {
    if(!isset($_POST['message'])) {
        echo "Message not set.";
        return;
    }
    $message = $_POST['message'];
    $query = "
    SELECT email, name
    FROM users
    WHERE is_admin = 1
    ";
    $admin = fetch_one($query, array());
    if($admin === null) {
        echo "No admin account found.";
        return;
    }
    $admin_email = $admin['email'];
    $admin_name = $admin['name'];
    $user_email = $account->email;
    $user = $account->name;
    send($message, $admin_email, $user, $user_email, $admin_email, $admin_name);
    echo "Success.";
    echo "<a href='/'>Back to home</a>";
}

function register_mail_endpoints(Router $r, Account|null $account): void {
    if($account === null) {
        $r->post("/mail_admin", function() { echo "You must be logged in to send mail to the admin."; });
    } else {
        $r->post("/mail_admin", fn() => mail_admin_endpoint($account));
    }
}
