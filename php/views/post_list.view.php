<IF ESC='isset($_GET["author_id"])'>
    <p>Showing posts filtered by author <a href="/">(back to all posts)</a>:</p>
<ELSE/>
    <p>Showing all posts:</p>
</IF>
<ul>
    <FOR $post_list() as $post>
        <li>
            <a href="/post?post_id={{{$post['post_id']}}}">{{{$post['title']}}}</a> by <a href="/?author_id={{{$post['author_id']}}}">{{{$post['username']}}}</a>
        </li>
    </FOR>
</ul>
