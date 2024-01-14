<?php

require_once "load_config_file.php";

class CSRFToken {
    function __construct(
        public string $token,
        public string $token_hmac
    ) {}
}

function get_secret_csrf_key(): string {
    static $secret_key = null;
    if($secret_key === null) {
        $secret_key = load_config_file(dirname(__FILE__) . '/csrf_secret_key.secrets.php', array('csrf-secret-key'))['csrf-secret-key'];
    }
    return $secret_key;
}

function generate_csrf_token(int $session_id, int $user_id, string|null $token = null): CSRFToken {
    if($token === null) {
        $token = bin2hex(random_bytes(32));
    }
    $hmac_secret_key = implode(':', array(strval($session_id), strval($user_id), $token, get_secret_csrf_key()));
    $token_hmac = hash_hmac("sha256", $token, $hmac_secret_key, false);
    return new CSRFToken($token, $token_hmac);
}

function validate_csrf_token(int $session_id, int $user_id, string $token, string $token_hmac): bool {
    return hash_equals($token_hmac, generate_csrf_token($session_id, $user_id, $token)->token_hmac);
}

function bind_generate_csrf_token(int $session_id, int $user_id): callable {
    return function(string|null $token = null) use ($session_id, $user_id): string {
        $csrf_token = generate_csrf_token($session_id, $user_id, $token);
        return '<input type="hidden" name="csrf-token" value="' . $csrf_token->token . '">'
            .  '<input type="hidden" name="csrf-token-hmac" value="' . $csrf_token->token_hmac . '">';
    };
}
