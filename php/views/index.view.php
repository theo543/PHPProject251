<MIXIN_NEST base>
    <MIXIN header/>
    <body>
        <p>Hi, user {{{$account->name}}}!</p>
        <MIXIN logout/>
        <IF $account->is_admin>
            <p><a href="/create_invite_link">Create invite link</a></p>
        </IF>
        <IF $account->is_author>
            <p><a href="/edit">Create a post</a></p>
        </IF>
        <hr>
        <MIXIN post_list/>
        <footer>
            <p>Admin contact</p>
            <form action="/mail_admin" method="post">
                {{{!|$csrf()}}}
                <textarea name="message" rows="5" cols="40"></textarea>
                <input type="submit" value="Send">
            </form>
        </footer>
    </body>
</MIXIN_NEST>
