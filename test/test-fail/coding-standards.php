<?php
// =============================================================================
// TEST-FAIL: Coding Standards Security Rules
// Scan with: semgrep scan --config php/coding-standards/ test/test-fail/coding-standards.php
//
// Each function contains a pattern that MUST trigger specific rules.
//
// EXPECTED_RULES:
// codevigilant.php.coding-standards.redirect.exit_missing
// codevigilant.php.coding-standards.redirect.use_wp_safe_redirect
// codevigilant.php.coding-standards.redirect.unsafe_wp_redirect
// codevigilant.php.coding-standards.xss.void_return_escaping
// codevigilant.php.coding-standards.validation.superglobal.not_validated
// codevigilant.php.coding-standards.validation.superglobal.not_sanitized
// codevigilant.php.coding-standards.validation.filter_default
// codevigilant.php.coding-standards.rce.eval
// codevigilant.php.coding-standards.rce.exec_functions
// codevigilant.php.coding-standards.info_disclosure.phpinfo
// codevigilant.php.coding-standards.info_disclosure.var_dump
// codevigilant.php.coding-standards.lfi.dynamic_include
// codevigilant.php.coding-standards.sqli.direct_db_query
// =============================================================================

// ---------------------------------------------------------------------------
// Redirect — missing exit after wp_redirect
// Triggers: redirect.exit_missing, redirect.use_wp_safe_redirect
// ---------------------------------------------------------------------------

function vuln_redirect_no_exit() {
    wp_redirect(admin_url());
}

function vuln_redirect_with_return() {
    wp_safe_redirect(home_url());
    return;
}

// ---------------------------------------------------------------------------
// Escaping — echo of void-returning function
// Triggers: xss.void_return_escaping
// ---------------------------------------------------------------------------

function vuln_echo_void_e() {
    echo _e('Hello', 'textdomain');
}

function vuln_echo_void_esc_html_e() {
    echo esc_html_e('Text', 'textdomain');
}

// ---------------------------------------------------------------------------
// Input Validation — unvalidated superglobal access
// Triggers: validation.superglobal.not_validated
// ---------------------------------------------------------------------------

function vuln_superglobal_get() {
    $val = $_GET['user_input'];
}

function vuln_superglobal_post() {
    $data = $_POST['payload'];
}

function vuln_superglobal_cookie() {
    $sess = $_COOKIE['session_id'];
}

function vuln_superglobal_request() {
    $action = $_REQUEST['action'] . ' extra';
}

// ---------------------------------------------------------------------------
// Redirect — wp_redirect with user-controlled URL (open redirect)
// Triggers: redirect.unsafe_wp_redirect, redirect.use_wp_safe_redirect,
//           validation.superglobal.not_validated
// ---------------------------------------------------------------------------

function vuln_unsafe_redirect() {
    wp_redirect($_GET['url']);
    exit;
}

// ---------------------------------------------------------------------------
// RCE — eval usage
// Triggers: rce.eval
// ---------------------------------------------------------------------------

function vuln_eval() {
    $code = get_option('custom_code');
    eval($code);
}

// ---------------------------------------------------------------------------
// RCE — exec functions
// Triggers: rce.exec_functions
// ---------------------------------------------------------------------------

function vuln_exec() {
    $output = shell_exec('ls -la');
}

// ---------------------------------------------------------------------------
// Info Disclosure — phpinfo
// Triggers: info_disclosure.phpinfo
// ---------------------------------------------------------------------------

function vuln_phpinfo() {
    phpinfo();
}

// ---------------------------------------------------------------------------
// Info Disclosure — var_dump
// Triggers: info_disclosure.var_dump
// ---------------------------------------------------------------------------

function vuln_var_dump() {
    $data = array(1, 2, 3);
    var_dump($data);
}

// ---------------------------------------------------------------------------
// LFI — dynamic include with user input
// Triggers: lfi.dynamic_include, validation.superglobal.not_validated,
//           validation.superglobal.not_sanitized (taint: $_GET → include)
// ---------------------------------------------------------------------------

function vuln_dynamic_include() {
    $template = $_GET['template'];
    include $template;
}

// ---------------------------------------------------------------------------
// SQL — direct database query (no caching/prepare concern)
// Triggers: sqli.direct_db_query
// ---------------------------------------------------------------------------

function vuln_direct_db_query() {
    global $wpdb;
    $wpdb->query("DELETE FROM wp_options WHERE option_name = 'temp'");
}

function vuln_direct_db_get_results() {
    global $wpdb;
    $wpdb->get_results("SELECT * FROM wp_options WHERE autoload = 'yes'");
}

// ---------------------------------------------------------------------------
// Filter Functions — FILTER_DEFAULT provides no filtering
// Triggers: validation.filter_default
// ---------------------------------------------------------------------------

function vuln_filter_default() {
    $val = filter_input(INPUT_GET, 'param', FILTER_DEFAULT);
}
