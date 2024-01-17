###MIXIN_NEST(base)###
    ###MIXIN(header)###
    <body>
        <form action="/auth" method="post">
            <label>User name: <input type="text" name="username" /></label>
            <label>User password: <input type="password" name="password" /></label>
            {{{!|$recaptcha}}}
            {{{!|$csrf()}}}
            <input type="submit" value="Authenticate" />
        </form>
    </body>
###END_NEST###
