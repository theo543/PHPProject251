###MIXIN_NEST(base)###
    ###MIXIN(header)###
    <body>
        <form action="/create_invite_link" method="post">
            <p> Create an invite link to allow others to create accounts. </p>
            <label> Should be author: <input type="checkbox" name="author" /> </label>
            <label> Should be editor: <input type="checkbox" name="editor" /> </label>
            <label> Should be admin: <input type="checkbox" name="admin" /> </label>
            <input type="submit" value="Create invite link" />
        </form>
    </body>
###END_NEST###
