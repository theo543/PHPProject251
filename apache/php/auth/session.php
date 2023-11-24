<?php

require_once "database/db.php";

function check_session(): bool {
    $db = connect_to_db();
    if(!isset($_COOKIE["session_token"]) || !isset($_COOKIE["session_user_id"])) {
        return false;
    }
    $session_token = $_COOKIE["session_token"];
    $session_user = $_COOKIE["session_user_id"];
    $query = $db->prepare("SELECT session_id FROM sessions WHERE user_id = ? AND expiry > NOW() AND token = FROM_BASE64(?)");
    $query->bind_param("is", $session_user, $session_token);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row !== null;
}

function create_session(int $user_id): void {
    $db = connect_to_db();
    $token = random_bytes(255);
    $query = $db->prepare("INSERT INTO sessions (user_id, token, expiry) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))");
    $query->bind_param("is", $user_id, $token);
    $query->execute();
    setcookie("session_token", base64_encode($token), time() + 86400, "/");
    setcookie("session_user_id", $user_id, time() + 86400, "/");
}

function end_session(string $user_id, string $token): bool {
    $db = connect_to_db();
    $query = $db->prepare("DELETE FROM sessions WHERE user_id = ? AND token = FROM_BASE64(?)");
    $query->bind_param("is", $user_id, $token);
    $success = $query->execute();
    return $success && ($db->affected_rows !== 0);
}
