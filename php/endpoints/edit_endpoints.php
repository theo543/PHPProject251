<?php

function post_edit_endpoint(Account $account): void {
    if(!$account->is_author) {
        echo "You do not have permission to make posts.";
        return;
    }
    if(!isset($_POST['post_id']) || !isset($_POST['title']) || !isset($_POST['content'])) {
        echo "Post ID or content not set.";
        return;
    }
    $post_id = intval($_POST['post_id']);
    $title = $_POST['title'];
    $content = $_POST['content'];
    $query = "";
    $args = [];
    if($post_id == 0) {
        $query = "
        INSERT INTO posts (title, content, author_id, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())";
        $args = [$title, $content, $account->id];
    } else {
        $post_author = fetch_one("SELECT author_id FROM posts WHERE post_id = ?", array($post_id));
        if($post_author === null || intval($post_author['author_id']) !== $account->id) {
            echo "Cannot edit non-existent post or post by someone else.";
            return;
        }
        $query = "
        INSERT INTO pending_edits (post_id, content, title)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY
        UPDATE content = ?, title = ?;
        ";
        $args = [$post_id, $content, $title, $content, $title];
    }
    $result = execute($query, $args);
    if($result === 0) {
        echo "Identical edit already pending.";
    } else {
        echo "Success.";
    }
    echo "<a href='/'>Back to home</a>";
}

function get_edit_endpoint(Account $account): null|View {
    $view = view("edit");
    if(isset($_GET["post_id"])) {
        $prev_post = fetch_one("
        SELECT posts.post_id, posts.title, COALESCE(pe.title, posts.title) AS prev_title, COALESCE(pe.content, posts.content) AS prev_content, author_id
        FROM posts
        LEFT JOIN pending_edits pe on posts.post_id = pe.post_id
        WHERE posts.post_id = ?
        ", array(intval($_GET["post_id"])));
        if($prev_post !== null) {
            if($prev_post['author_id'] !== $account->id) {
                echo "Cannot edit post by someone else.";
                return null;
            }
            $args = $prev_post;
        }
        $view->set_many($prev_post);
    } else {
        $view->set_many(array("prev_title" => "New post", "prev_content" => "Type your post here...", "post_id" => 0));
    }
    return $view;
}

function post_approve(Account $account) {
    if(!$account->is_editor) {
        echo "You do not have permission to edit posts.";
        return;
    }
    if(!isset($_POST['post_id'])) {
        echo "Post ID not set.";
        return;
    }
    $db = get_global_conn();
    $db->begin_transaction();
    $post_id = intval($_POST['post_id']);
    $update = execute("
    UPDATE posts
    JOIN pending_edits pe on posts.post_id = pe.post_id
    SET posts.title = pe.title, posts.content = pe.content, updated_at = NOW()
    WHERE posts.post_id = ?
    ", array($post_id));
    $delete = execute("DELETE FROM pending_edits pe WHERE pe.post_id = ?", array($post_id));
    if($update === 0 || $delete === 0) {
        $db->rollback();
        echo "Failed to approve edit.";
    } else {
        $db->commit();
        echo "Success. <a href='/'>Back to home</a>";
    }
}

function get_approve(Account $account): null|View {
    if(!$account->is_editor) {
        echo "You do not have permission to edit posts.";
        return null;
    }
    if(!isset($_GET['post_id'])) {
        echo "Post ID not set.";
        return null;
    }
    $post_id = intval($_GET['post_id']);
    $post = fetch_one("
    SELECT posts.post_id AS post_id, posts.content AS prev_content, posts.title AS prev_title, pe.title AS title, pe.content AS content
    FROM posts
    JOIN pending_edits pe on posts.post_id = pe.post_id
    WHERE posts.post_id = ?
    ", array($post_id));
    if($post === null) {
        echo "Post or pending edit not found.";
        return null;
    }
    $post['compile_content'] = fn() => compile_post($post['content']);
    $post['compile_prev_content'] = fn() => compile_post($post['prev_content']);
    $view = view("approve_edit");
    $view->set_many($post);
    return $view;
}

function register_edit_endpoints(Router $r, null|Account $account): void {
    if($account === null) {
        $deny = function() {
            echo "You must be logged in.";
            return;
        };
        // Won't get run anyways because of the interceptor
        // Add these so the user gets redirected to /auth instead of 404
        $r->get("/edit", $deny);
        $r->post("/edit", $deny);
        $r->get("/approve_edit", $deny);
        $r->post("/approve_edit", $deny);
    }
    $r->get("/edit", fn() => get_edit_endpoint($account));
    $r->post("/edit", fn() => post_edit_endpoint($account));
    $r->get("/approve_edit", fn() => get_approve($account));
    $r->post("/approve_edit", fn() => post_approve($account));
}
