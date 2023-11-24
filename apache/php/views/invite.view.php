<?php if(!$_GET["token"]): ?>
    Invite link is required.
<?php else: ?>
    <form action="/invite" method="post">
        <label> Name: <input type="text" name="username" /> </label>
        <label> Password: <input type="password" name="password" /> </label>
        <label> Email: <input type="email" name="email" /> </label>
        <input type="hidden" name="token" value="<?= $eh($_GET["token"]) ?>" />
        <input type="submit" value="Create account" />
    </form>
<?php endif ?>
