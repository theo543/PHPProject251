<?php
declare(strict_types=1);

require_once "database/db.php";
require_once "auth/account.php";

class Session {
    public function __construct(
        public Account | null $account,
        public int $session_id // for CSRF invalidation
    ) {}
}

function check_session(): null | Session {
    if(!isset($_COOKIE["session_token"]) || !isset($_COOKIE["session_user_id"])) {
        return null;
    }
    $session_token = $_COOKIE["session_token"];
    $session_user = $_COOKIE["session_user_id"];
    if($session_user === "NULL"){
        $session_user = null;
        $row = fetch_one("SELECT session_id, user_id FROM sessions WHERE user_id IS NULL AND expiry > NOW() AND token = FROM_BASE64(?)", [$session_token]);
    } else {
        $session_user = intval($session_user);
        $row = fetch_one("SELECT session_id, user_id FROM sessions WHERE user_id = ? AND expiry > NOW() AND token = FROM_BASE64(?)", [$session_user, $session_token]);
    }
    if($row === null) {
        return null;
    }
    if ($row["user_id"] !== $session_user) {
        // maybe log this?
        return null;
    }
    if($session_user === null) {
        return new Session(null, $row["session_id"]);
    }
    $account = get_account($session_user);
    if($account === null) {
        return null;
    }
    return new Session($account, $row["session_id"]);
}

function create_session(int|null $user_id): int|null {
    $token = random_bytes(255);
    if($user_id === null) {
        $result = execute("INSERT INTO sessions (user_id, token, expiry) VALUES (NULL, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))", [$token]);
    } else {
        $user_id = strval($user_id);
        $result = execute("INSERT INTO sessions (user_id, token, expiry) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))", [$user_id, $token]);
    }
    if($result === 0) {
        throw new LogicException("Session insert affected 0 rows. Impossible?");
    }
    $sessionID = get_global_conn()->insert_id;
    if($sessionID === 0) {
        // should never happen unless somehow the primary key is not auto-increment
        throw new LogicException("No insert ID after creating session. Maybe AUTO_INCREMENT is missing?");
    }
    setcookie("session_token", base64_encode($token), time() + 86400, "/");
    setcookie("session_user_id", $user_id ?? "NULL", time() + 86400, "/");
    return $sessionID;
}

function end_session(int|null $user_id, string $token): bool {
    $success = execute("DELETE FROM sessions WHERE user_id = ? AND token = FROM_BASE64(?)", [$user_id, $token]);
    return $success !== 0;
}
