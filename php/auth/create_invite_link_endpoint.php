<?php
declare(strict_types=1);

require_once "database/db.php";

function create_invite_link_endpoint($creator_account): void {
    if(!$creator_account->is_admin) {
        echo "You are not an admin.";
        return;
    }
    function checked($name): int {
        if(!isset($_POST[$name])) {
            return 0;
        }
        return $_POST[$name] === "on" ? 1 : 0;
    }
    $token = random_bytes(255);
    $result = execute("INSERT INTO invite_tokens (token, author, editor, admin) VALUES (?, ?, ?, ?)", [$token, checked("author"), checked("editor"), checked("admin")]);
    if($result === 0) {
        echo "Could not create invite link.";
        return;
    }
    $token = urlencode(base64_encode($token));
    echo "<a href='/invite?token=$token'>Invite link created. You may now share it.</a>";
}
