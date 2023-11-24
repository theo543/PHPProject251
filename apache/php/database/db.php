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
