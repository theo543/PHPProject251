<?php

require_once "database/db.php";

function post_list(): array {
    $cond = "TRUE";
    $args = [];
    if(isset($_GET['author_id'])) {
        $cond = "author_id = ?";
        $args = [intval($_GET['author_id'])];
    }
    $query = "SELECT post_id, title, author_id, users.name AS username FROM posts JOIN users ON users.user_id = posts.author_id WHERE $cond ORDER BY created_at DESC";
    $posts = fetch_all($query, $args);
    return $posts;
}
