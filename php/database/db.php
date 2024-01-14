<?php

function connect_to_db(): mysqli {
    static $DB_CONNECTION = null;
    if($DB_CONNECTION !== null) {
        return $DB_CONNECTION;
    }
    $dblogininfo = require_once "dblogininfo.secrets.php";
    $db = new mysqli("localhost", $dblogininfo["db_user"], $dblogininfo["db_pass"], $dblogininfo["db_name"]);
    if($db->connect_errno) {
        die("Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error);
    }
    $DB_CONNECTION = $db;
    return $DB_CONNECTION;
}

function fetch_one($query, $params = [], $db = null) {
    if($db === null) {
        $db = connect_to_db();
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

function fetch_all($query, $params = [], $db = null) {
    if($db === null) {
        $db = connect_to_db();
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

function execute($query, $params = [], $db = null): int | null {
    if($db === null) {
        $db = connect_to_db();
    }
    try {
        $query = $db->prepare($query);
        $result = $query->execute($params);
    } catch(Exception $e) {
        return null;
    }
    if($result === false) {
        return null;
    }
    return $db->affected_rows;
}
