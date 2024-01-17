<?php
declare(strict_types=1);

require_once "database/db.php";

function logout_endpoint():void {
    if(isset($_COOKIE["session_token"]) && isset($_COOKIE["session_user_id"])) {
        if(!end_session(intval($_COOKIE["session_user_id"]), $_COOKIE["session_token"])) {
            echo "Could not delete your session in the database. It might not exist.";
            return;
        }
        setcookie("session_token", "", time() - 3600, "/");
        setcookie("session_user_id", "", time() - 3600, "/");
        header("Location: /");
    } else {
        echo "Token or ID not specified.";
    }
}
