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
    public function create_account(string $password): bool {
        $db = connect_to_db();
        $db->begin_transaction();
        $bcrypt_opts = [
            "cost" => 12,
        ];
        $bcrypt_password = password_hash($password, PASSWORD_BCRYPT, $bcrypt_opts);
        $query = $db->prepare("INSERT INTO users (name, email, bcrypt_password, is_admin, is_editor, is_author) VALUES (?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssss", $this->name, $this->email, $bcrypt_password, $this->is_admin, $this->is_editor, $this->is_author);
        $result = $query->execute();
        if($result === false) {
            $db->rollback();
            return false;
        }
        $last_auto_increment = $db->prepare("SELECT LAST_INSERT_ID() AS id");
        $last_auto_increment->execute();
        $this->id = $last_auto_increment->get_result()->fetch_assoc()["id"];
        $db->commit();
        return true;
    }
}

function get_account(int $id): Account | null {
    $db = connect_to_db();
    $query = $db->prepare("SELECT user_id, name, email, is_admin, is_editor, is_author FROM users WHERE user_id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    if($row === null) {
        return null;
    }
    return new Account($row["user_id"], $row["name"], $row["email"], $row["is_admin"], $row["is_editor"], $row["is_author"]);
}
