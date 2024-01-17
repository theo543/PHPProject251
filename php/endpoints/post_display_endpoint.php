<?php
declare(strict_types=1);

require_once "views/View.php";
require_once "posts/post_compiler.php";

function post_display_endpoint(): null|View {
    if(!isset($_GET['post_id'])) {
        echo "No post id provided";
        return null;
    }
    $post_id = $_GET['post_id'];
    $row = fetch_one("
    SELECT author_id, name AS author_name, title, content, created_at, updated_at
    FROM posts
    JOIN users ON posts.author_id = users.user_id
    WHERE post_id = ?", [$post_id]);
    if(!$row) {
        http_response_code(404);
        echo "Post not found";
        return null;
    }
    $content = $row['content'];
    $row['content'] = null;
    $row['compile_post'] = fn() => compile_post($content);
    return view("post")->set_many($row);
}

function register_post_display_endpoint(Router $r): void {
    $r->get("/post", fn() => post_display_endpoint());
}
