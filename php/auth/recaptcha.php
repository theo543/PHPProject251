<?php
declare(strict_types=1);

require_once "load_config_file.php";

function get_recaptcha_keys(): array {
    static $keys = null;
    if($keys === null) {
        $keys = load_config_file(dirname(__FILE__) . "/recaptcha_keys.secrets.php", array("site-key", "secret-key"));
    }
    return $keys;
}

function get_recaptcha_site_key(): string {
    return get_recaptcha_keys()["site-key"];
}

function get_recaptcha_secret_key(): string {
    return get_recaptcha_keys()["secret-key"];
}

function get_recaptcha_html(): string {
    return '<div class="g-recaptcha" data-sitekey="' . get_recaptcha_site_key() . '"></div>';
}

function google_api_recaptcha_verification(string $response): bool {
    $api = 'https://www.google.com/recaptcha/api/siteverify?secret=';
    $url = $api . get_recaptcha_secret_key() . '&response=' . urlencode($response);
    $response = file_get_contents($url);
    $response = json_decode($response);
    return $response->success;
}

function validate_post_request_recaptcha(): bool {
    if(empty($_POST['g-recaptcha-response'])) {
        return false;
    }
    return google_api_recaptcha_verification($_POST['g-recaptcha-response']);
}
