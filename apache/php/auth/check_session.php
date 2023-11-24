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
    if(!$query) {
        die("Failed to prepare query: (" . $db->errno . ") " . $db->error);
    }
    $query->execute(array($session_user, $session_token));
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row !== null;
}
