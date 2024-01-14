<?php

require_once "database/db.php";

class Account {
    readonly public int $id;
    readonly public string $name;
    readonly public string $email;
    readonly public bool $is_admin;
    readonly public bool $is_editor;
    readonly public bool $is_author;
    public function __construct($id = null, $name = null, $email = null, $is_admin = null, $is_editor = null, $is_author = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->is_admin = $is_admin;
        $this->is_editor = $is_editor;
        $this->is_author = $is_author;
    }

}

function get_account(int $id): Account | null {
    $row = fetch_one("SELECT user_id, name, email, is_admin, is_editor, is_author FROM users WHERE user_id = ?", [$id]);
    if($row === null) {
        return null;
    }
    return new Account($row["user_id"], $row["name"], $row["email"], $row["is_admin"], $row["is_editor"], $row["is_author"]);
}

function create_account(string $name, string $email, string $password, bool $is_admin, bool $is_editor, bool $is_author, mysqli | null $db = null): null | Account {
    if($db === null) {
        $db = connect_to_db();
    }
    $bcrypt_opts = [
        "cost" => 12,
    ];
    $bcrypt_password = password_hash($password, PASSWORD_BCRYPT, $bcrypt_opts);
    function btoi(bool $b): int {
        return $b ? 1 : 0;
    }
    $result = execute("INSERT INTO users (name, email, bcrypt_password, is_admin, is_editor, is_author) VALUES (?, ?, ?, ?, ?, ?)", [$name, $email, $bcrypt_password, btoi($is_admin), btoi($is_editor), btoi($is_author)], $db);
    if($result === null) {
        return null;
    }
    $id = fetch_one("SELECT LAST_INSERT_ID() AS id", [], $db);
    if($id === null) {
        return null;
    }
    $id = $id["id"];
    return new Account($id, $name, $email, $is_admin, $is_editor, $is_author);
}
