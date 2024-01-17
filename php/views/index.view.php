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
    </body>
</MIXIN_NEST>
