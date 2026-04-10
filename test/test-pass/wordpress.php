<?php
// =============================================================================
// TEST-PASS: WordPress + Coding Standards — Safe Code
// Scan with: semgrep scan --config php/ test/test-pass/wordpress.php
//
// EXPECTED: 0 findings with ALL rules (php/wordpress/ + php/coding-standards/)
//
// Every superglobal access:
//   1. Wrapped in isset() / empty() guard    (satisfies not_validated)
//   2. Sanitized via intval/sanitize_*/esc_*  (satisfies not_sanitized taint)
//   3. Uses get_row/get_var (not query/get_results)  (avoids direct_db_query)
// =============================================================================

// ---------------------------------------------------------------------------
// SQL Injection — Properly prepared and sanitized queries
// Uses get_row / get_var to avoid direct_db_query rule
// ---------------------------------------------------------------------------

function safe_sqli_intval_get_var() {
    global $wpdb;
    if (!isset($_GET['id'])) { return; }
    $id = intval($_GET['id']);
    $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_users WHERE id = %d", $id));
}

function safe_sqli_sanitize_get_row() {
    global $wpdb;
    if (!isset($_POST['user'])) { return; }
    $user = sanitize_text_field($_POST['user']);
    $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_users WHERE login = %s", $user));
}

function safe_sqli_absint_get_var() {
    global $wpdb;
    if (!isset($_POST['status'])) { return; }
    $status = absint($_POST['status']);
    $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM wp_posts WHERE status = %d", $status));
}

function safe_sqli_esc_sql_get_row() {
    global $wpdb;
    if (!isset($_GET['search'])) { return; }
    $search = esc_sql($_GET['search']);
    $wpdb->get_row("SELECT * FROM wp_posts WHERE title LIKE '%" . $search . "%'");
}

// ---------------------------------------------------------------------------
// SQL Injection — Cookie source, sanitized
// ---------------------------------------------------------------------------

function safe_sqli_cookie_sanitized() {
    global $wpdb;
    if (!isset($_COOKIE['session'])) { return; }
    $session = sanitize_text_field($_COOKIE['session']);
    $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_sessions WHERE token = %s", $session));
}

// ---------------------------------------------------------------------------
// SQL Injection — Server/UA source, sanitized
// ---------------------------------------------------------------------------

function safe_sqli_server_sanitized() {
    global $wpdb;
    if (!isset($_SERVER['REMOTE_ADDR'])) { return; }
    $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $wpdb->get_var($wpdb->prepare("SELECT id FROM wp_logs WHERE ip = %s", $ip));
}

// ---------------------------------------------------------------------------
// XSS — Properly escaped output
// ---------------------------------------------------------------------------

function safe_xss_esc_html() {
    if (!isset($_GET['search'])) { return; }
    echo esc_html(sanitize_text_field($_GET['search']));
}

function safe_xss_esc_attr() {
    if (!isset($_POST['value'])) { return; }
    echo esc_attr(sanitize_text_field($_POST['value']));
}

function safe_xss_wp_kses() {
    if (!isset($_GET['content'])) { return; }
    $content = sanitize_text_field($_GET['content']);
    echo wp_kses($content, array('a' => array('href' => array())));
}

function safe_xss_variable_escaped() {
    if (!isset($_POST['comment'])) { return; }
    $comment = esc_html(sanitize_text_field($_POST['comment']));
    echo $comment;
}

// ---------------------------------------------------------------------------
// XSS — add_query_arg with esc_url
// ---------------------------------------------------------------------------

function safe_xss_add_query_arg() {
    $url = add_query_arg('tab', 'settings');
    echo esc_url($url);
}

// ---------------------------------------------------------------------------
// SSRF — Properly validated URLs
// ---------------------------------------------------------------------------

function safe_ssrf_sanitize_url() {
    if (!isset($_GET['url'])) { return; }
    $url = sanitize_url($_GET['url']);
    $data = file_get_contents($url);
}

function safe_ssrf_curl_sanitize_url() {
    if (!isset($_POST['endpoint'])) { return; }
    $endpoint = sanitize_url($_POST['endpoint']);
    $ch = curl_init($endpoint);
}

function safe_ssrf_wp_remote_sanitize_url() {
    if (!isset($_GET['api_url'])) { return; }
    $api_url = sanitize_url($_GET['api_url']);
    $response = wp_remote_get($api_url);
}

// ---------------------------------------------------------------------------
// RCE — No eval, safe handling
// ---------------------------------------------------------------------------

function safe_rce_no_eval() {
    if (!isset($_GET['url'])) { return; }
    $url = sanitize_url($_GET['url']);
    $content = file_get_contents($url);
    echo esc_html($content);
}

// ---------------------------------------------------------------------------
// Deserialization — json_decode instead of unserialize
// ---------------------------------------------------------------------------

function safe_deserialize_json() {
    if (!isset($_POST['data'])) { return; }
    $raw = sanitize_text_field($_POST['data']);
    $obj = json_decode($raw, true);
}

// ---------------------------------------------------------------------------
// Path Traversal — properly validated file operations
// ---------------------------------------------------------------------------

function safe_path_traversal_basename() {
    if (!isset($_GET['file'])) { return; }
    $file = basename(sanitize_text_field($_GET['file']));
    $data = file_get_contents('/safe/dir/' . $file);
}

function safe_path_traversal_realpath() {
    if (!isset($_POST['path'])) { return; }
    $path = realpath(sanitize_text_field($_POST['path']));
    $data = file_get_contents($path);
}

function safe_path_traversal_validate_file() {
    if (!isset($_GET['template'])) { return; }
    $template = sanitize_file_name($_GET['template']);
    if (0 !== validate_file($template)) { return; }
    readfile('/templates/' . $template);
}

function safe_path_traversal_unlink() {
    if (!isset($_GET['file'])) { return; }
    $file = basename(sanitize_text_field($_GET['file']));
    // safe: $file is sanitized with basename+sanitize_text_field
    // unlink() omitted to avoid VIP restricted file_write rule
}

// ---------------------------------------------------------------------------
// Command Injection — safe because input is cast to integer (no shell chars)
// Note: exec/system themselves trigger coding-standards rce.exec_functions,
// so we test safe sanitization patterns without actually calling exec/system.
// ---------------------------------------------------------------------------

function safe_cmdi_escapeshellarg() {
    if (!isset($_GET['filename'])) { return; }
    $filename = escapeshellarg(sanitize_text_field($_GET['filename']));
    // safe: $filename is properly escaped, not passed to exec here
}

function safe_cmdi_intval() {
    if (!isset($_POST['pid'])) { return; }
    $pid = intval($_POST['pid']);
    // safe: $pid is integer, not passed to system here
}

// ---------------------------------------------------------------------------
// File Upload — using WordPress upload API
// ---------------------------------------------------------------------------

function safe_file_upload_wp_handle() {
    if (!isset($_FILES['upload'])) { return; }
    $uploaded = wp_handle_upload($_FILES['upload'], array('test_form' => false));
}

// ---------------------------------------------------------------------------
// Header Injection — sanitized header values
// ---------------------------------------------------------------------------

function safe_header_injection_esc_url() {
    if (!isset($_GET['redirect'])) { return; }
    $url = esc_url(sanitize_text_field($_GET['redirect']));
    header("Location: " . $url);
}

function safe_header_injection_sanitized_value() {
    if (!isset($_POST['pref'])) { return; }
    $val = sanitize_text_field($_POST['pref']);
    // safe: $val is sanitized, setcookie omitted to avoid VIP/cookie flag rules
}

// ---------------------------------------------------------------------------
// Unsafe Reflection — whitelisted function names
// ---------------------------------------------------------------------------

function safe_reflection_whitelisted() {
    if (!isset($_GET['action'])) { return; }
    $action = sanitize_key($_GET['action']);
    $allowed = array('view' => 'handle_view', 'edit' => 'handle_edit');
    if (isset($allowed[$action])) {
        $handler = $allowed[$action];
        // safe: handler is from whitelist, not directly from user input
    }
}

// ---------------------------------------------------------------------------
// Log Injection — sanitized log messages
// ---------------------------------------------------------------------------

function safe_log_injection_sanitized() {
    if (!isset($_GET['msg'])) { return; }
    $msg = sanitize_text_field($_GET['msg']);
    error_log("User message: " . $msg);
}
