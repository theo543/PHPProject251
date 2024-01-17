<MIXIN_NEST base>
    <MIXIN header/>
    <body>
        <IF $post_id === 0>
            <p>Creating a new post.</p>
        <ELSE/>
            <p>Editing post <b>{{{$title}}}</b></p>
            <p>Please note that to edit an existing post, an editor must approve the new post.</p>
        </IF>
        <form action="/edit" method="post">
            <input type="hidden" name="post_id" value="{{{$post_id}}}">
            {{{!|$csrf()}}}
            <p>
                <label>
                    Title:
                    <input type="text" name="title" value="{{{$prev_title}}}">
                </label>
            </p>
            <p>
                <label>
                    New content:
                    <br>
                    <textarea name="content" rows="10" cols="80">{{{$prev_content}}}</textarea>
                </label>
            </p>
            <input type="submit" value="Submit">
        </form>
    </body>
</MIXIN_NEST>
