###MIXIN_NEST(base)###
    ###MIXIN(header)###
    <body>
        ###IF(!$_GET["token"])###
            Invite link is required.
        ###ELSE###
            <form action="/invite" method="post">
                <label> Name: <input type="text" name="username" /> </label>
                <label> Password: <input type="password" name="password" /> </label>
                <label> Email: <input type="email" name="email" /> </label>
                <input type="hidden" name="token" value="{{{$_GET["token"]}}}" />
                {{{!$recaptcha}}}
                <input type="submit" value="Create account" />
            </form>
        ###ENDIF###
    </body>
###END_NEST###
