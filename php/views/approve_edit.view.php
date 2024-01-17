<MIXIN_NEST base>
    <MIXIN header/>
    <body>
        <form action="/approve_edit" method="post">
            <input type="hidden" name="post_id" value="{{{$post_id}}}">
            {{{!|$csrf()}}}
            <input type="submit" value="Approve edit">
        </form>
        <p>Previous post <b>{{{$prev_title}}}</b></p>
        <p>
        {{{!|$compile_prev_content()}}}
        </p>
        <p>New post <b>{{{$title}}}</b></p>
        <p>
        {{{!|$compile_content()}}}
        </p>
    </body>
</MIXIN_NEST>
