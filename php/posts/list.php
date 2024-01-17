<?php

require_once "database/db.php";

function post_list(): array {
    $cond = "TRUE";
    $args = [];
    if(isset($_GET['author_id'])) {
        $cond = "author_id = ?";
        $args = [intval($_GET['author_id'])];
    }
    $query = "
    SELECT posts.post_id, posts.title, author_id, users.name AS username, IF(pe.post_id IS NULL, FALSE, TRUE) AS has_pending_edit
    FROM posts
    JOIN users ON users.user_id = posts.author_id
    LEFT JOIN pending_edits pe on posts.post_id = pe.post_id
    WHERE $cond
    ORDER BY created_at DESC";
    $posts = fetch_all($query, $args);
    return $posts;
}
