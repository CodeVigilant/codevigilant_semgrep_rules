<?php
// NEGATIVE repro: sanitized / verified usage must NOT fire.

// 1) cookie value verified with hash_equals against a server-side secret
function check_unlock() {
    $expected = hash_hmac('sha256', 'unlock', wp_salt());
    if (hash_equals($expected, $_COOKIE['unlock_token'] ?? '')) {
        return true;
    }
    return false;
}

// 2) GET parameter used WITH nonce verification
function delete_item() {
    if (isset($_GET['delete']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_item')) {
        // legitimately delete
    }
}

// 3) cookie read for a benign preference without a conditional decision
function get_theme_pref() {
    $theme = $_COOKIE['theme_pref'] ?? 'light';
    return $theme;
}
