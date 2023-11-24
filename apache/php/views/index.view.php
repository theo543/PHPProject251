<p>Hi, user <?= $eh($account->name) ?>!</p>
<?php view("logout")->render() ?>
<?php if($account->is_admin): ?>
    <a href="/create_invite_link">Create invite link</a>
<?php endif ?>
