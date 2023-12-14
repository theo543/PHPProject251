<form action="/auth" method="post">
    <label>User name: <input type="text" name="username" /></label>
    <label>User password: <input type="password" name="password" /></label>
    <?= $recaptcha_form_element() ?>
    <input type="submit" value="Authenticate" />
</form>
