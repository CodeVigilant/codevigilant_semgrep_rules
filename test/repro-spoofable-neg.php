<?php
// NEGATIVE repro: legit usage must NOT fire.

// 1) User-Agent used only for analytics, gated by real auth (no UA in if)
function track_bot() {
    $is_bot = stripos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false;
    if ($is_bot && is_user_logged_in()) {
        record_bot_hit();
    }
}

// 2) cookie verified with HMAC + hash_equals (constant time, keyed)
function check_token() {
    $expected = hash_hmac('sha256', 'unlock|' . time(), wp_salt('auth'));
    if (hash_equals($expected, $_COOKIE['unlock_token'] ?? '')) {
        return true;
    }
    return false;
}

// 3) md5 used for a non-security checksum
function etag() {
    return md5($content);
}
