<?php
declare(strict_types=1);

function get_global_conn(): mysqli {
    static $DB_CONNECTION = null;
    if($DB_CONNECTION !== null) {
        return $DB_CONNECTION;
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $dblogininfo = require_once "dblogininfo.secrets.php";
    $DB_CONNECTION = new mysqli("localhost", $dblogininfo["db_user"], $dblogininfo["db_pass"], $dblogininfo["db_name"]);
    return $DB_CONNECTION;
}

function fetch_one($query, $params = [], $db = null): array|null {
    if($db === null) {
        $db = get_global_conn();
    }
    $query = $db->prepare($query);
    $query->execute($params);
    $result = $query->get_result();
    if($result === false) {
        return null;
    }
    $row = $result->fetch_assoc();
    if($row === null) {
        return null;
    }
    return $row;
}

function fetch_all($query, $params = [], $db = null): ?array {
    if($db === null) {
        $db = get_global_conn();
    }
    $query = $db->prepare($query);
    $query->execute($params);
    $result = $query->get_result();
    if($result === false) {
        return null;
    }
    $rows = [];
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function execute($query, $params = [], $db = null): int {
    if($db === null) {
        $db = get_global_conn();
    }
    $query = $db->prepare($query);
    $query->execute($params);
    return $db->affected_rows;
}
