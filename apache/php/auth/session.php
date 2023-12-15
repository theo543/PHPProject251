<?php

require_once "database/db.php";
require_once "auth/account.php";

class ValidatedAccount {
    public function __construct(
        public Account $account,
        public int $session_id, // for CSRF invalidation
    ) {}
}

function check_session(): null | ValidatedAccount {
    $db = connect_to_db();
    if(!isset($_COOKIE["session_token"]) || !isset($_COOKIE["session_user_id"])) {
        return null;
    }
    $session_token = $_COOKIE["session_token"];
    $session_user = $_COOKIE["session_user_id"];
    $row = fetch_one("SELECT session_id, user_id FROM sessions WHERE user_id = ? AND expiry > NOW() AND token = FROM_BASE64(?)", [$session_user, $session_token]);
    if($row === null || $row["user_id"] !== intval($session_user)) {
        return null;
    }
    return new ValidatedAccount(get_account($session_user), $row["session_id"]);
}

function create_session(int $user_id): bool {
    $db = connect_to_db();
    $token = random_bytes(255);
    $result = execute("INSERT INTO sessions (user_id, token, expiry) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))", [$user_id, $token]);
    if($result === null || $result === 0) {
        return false;
    }
    setcookie("session_token", base64_encode($token), time() + 86400, "/");
    setcookie("session_user_id", $user_id, time() + 86400, "/");
    return true;
}

function end_session(string $user_id, string $token): bool {
    $db = connect_to_db();
    $query = $db->prepare("DELETE FROM sessions WHERE user_id = ? AND token = FROM_BASE64(?)");
    $query->bind_param("is", $user_id, $token);
    $success = $query->execute();
    return $success !== null && ($db->affected_rows !== 0);
}
