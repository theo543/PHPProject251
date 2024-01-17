<MIXIN_NEST base>
    <MIXIN header/>
    <body>
        <form action="/create_root_user" method="post">
            <p> Note: this form is controlled by a file stored on the server to allow the creation of an initial root user. </p>
            <p> After creating the root user, you should disable this form. </p>
            <p> You must enter the password from debugmode.secrets.php to create the root user. </p>
            <label> Password: <input type="password" name="password" /> </label>
            {{{!|$csrf()}}}
            <input type="submit" value="Create root user" />
        </form>
    </body>
</MIXIN_NEST>
