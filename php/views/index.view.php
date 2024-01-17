<MIXIN_NEST base>
    <MIXIN header/>
    <body>
        <p>Hi, user {{{$account->name}}}!</p>
        <MIXIN logout/>
        <IF $account->is_admin>
            <a href="/create_invite_link">Create invite link</a>
        </IF>
        <hr>
        <MIXIN post_list/>
    </body>
</MIXIN_NEST>
