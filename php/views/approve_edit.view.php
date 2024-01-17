<MIXIN_NEST base>
    <MIXIN header/>
    <body>
        <form action="/approve_edit" method="post">
            <input type="hidden" name="post_id" value="{{{$post_id}}}">
            {{{!|$csrf()}}}
            <input type="submit" value="Approve edit">
        </form>
        <h1>Previous post: <em>{{{$prev_title}}}</em></h1>
        <p>
        {{{!|$compile_prev_content()}}}
        </p>
        <h1>New post: <em>{{{$title}}}</em></h1>
        <p>
        {{{!|$compile_content()}}}
        </p>
    </body>
</MIXIN_NEST>
