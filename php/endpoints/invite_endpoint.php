<?php
declare(strict_types=1);

require_once "database/db.php";
require_once "auth/account.php";

function invite_endpoint(): void {
    if(!isset($_POST["token"])) {
        echo "Missing token.";
        return;
    }
    if(!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["email"])) {
        echo "Missing username, password or email.";
        return;
    }
    $token = base64_decode($_POST["token"]);
    $db = get_global_conn();
    $db->begin_transaction();
    $row = fetch_one("SELECT author, editor, admin FROM invite_tokens WHERE token = ?", [$token], $db);
    if($row === null) {
        echo "Invalid token.";
        $db->rollback();
        return;
    }
    $result = execute("DELETE FROM invite_tokens WHERE token = ?", [$token], $db);
    if($result === 0) {
        echo "Invalid token.";
        $db->rollback();
        return;
    }
    $account = create_account($_POST["username"], $_POST["email"], $_POST["password"], !!$row["admin"], !!$row["editor"], !!$row["author"], $db);
    if($account === null) {
        echo "Could not create account.";
        $db->rollback();
        return;
    }
    $db->commit();
    if(!create_session($account->id)) {
        echo "Session could not be created. Please log in manually.";
    } else {
        header("Location: /");
    }
}
