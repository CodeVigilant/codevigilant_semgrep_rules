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
// codevigilant.php.coding-standards.xxe.simplexml_load_string
// codevigilant.php.coding-standards.xxe.simplexml_load_file
// codevigilant.php.coding-standards.xxe.dom_loadxml
// codevigilant.php.coding-standards.hardcoded_credentials.password_assignment
// codevigilant.php.coding-standards.hardcoded_credentials.define_password
// codevigilant.php.coding-standards.insecure_cookie.missing_secure_flag
// codevigilant.php.coding-standards.insecure_cookie.missing_httponly_flag
// codevigilant.php.coding-standards.weak_crypto.md5_hash
// codevigilant.php.coding-standards.weak_crypto.sha1_hash
// codevigilant.php.coding-standards.weak_crypto.mcrypt_functions
// codevigilant.php.coding-standards.weak_randomness.rand
// codevigilant.php.coding-standards.weak_randomness.mt_rand
// codevigilant.php.coding-standards.weak_randomness.uniqid
// codevigilant.php.coding-standards.error_disclosure.display_errors_on
// codevigilant.php.coding-standards.error_disclosure.display_startup_errors
// codevigilant.php.coding-standards.error_disclosure.wp_debug_display
// codevigilant.php.coding-standards.incorrect_authorization.ajax_nopriv_no_capability_check
// codevigilant.php.coding-standards.incorrect_authorization.rest_no_permission_callback
// codevigilant.php.coding-standards.vip.restricted.cookies
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

// ---------------------------------------------------------------------------
// XXE — XML parsing without disabling external entities
// Triggers: xxe.simplexml_load_string, xxe.simplexml_load_file, xxe.dom_loadxml
// ---------------------------------------------------------------------------

function vuln_xxe_simplexml_string() {
    $xml = '<root><item>test</item></root>';
    $data = simplexml_load_string($xml);
}

function vuln_xxe_simplexml_file() {
    $data = simplexml_load_file('/tmp/data.xml');
}

function vuln_xxe_dom_loadxml() {
    $dom = new DOMDocument();
    $dom->loadXML('<root/>');
}

// ---------------------------------------------------------------------------
// Hard-coded Credentials
// Triggers: hardcoded_credentials.password_assignment,
//           hardcoded_credentials.define_password
// ---------------------------------------------------------------------------

function vuln_hardcoded_password() {
    $password = "SuperSecret123!";
}

function vuln_hardcoded_define() {
    define('DB_PASSWORD', 'my_db_pass');
}

// ---------------------------------------------------------------------------
// Insecure Cookies — missing Secure and HttpOnly flags
// Triggers: insecure_cookie.missing_secure_flag,
//           insecure_cookie.missing_httponly_flag
// ---------------------------------------------------------------------------

function vuln_cookie_no_flags() {
    setcookie('session', 'abc123');
}

function vuln_cookie_no_httponly() {
    setcookie('session', 'abc123', time() + 3600, '/', '.example.com', true);
}

// ---------------------------------------------------------------------------
// Weak Cryptography — MD5, SHA1, mcrypt
// Triggers: weak_crypto.md5_hash, weak_crypto.sha1_hash,
//           weak_crypto.mcrypt_functions
// ---------------------------------------------------------------------------

function vuln_md5_hash() {
    $hash = md5($input);
}

function vuln_sha1_hash() {
    $hash = sha1($data);
}

function vuln_mcrypt() {
    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC);
}

// ---------------------------------------------------------------------------
// Weak Randomness — rand, mt_rand, uniqid
// Triggers: weak_randomness.rand, weak_randomness.mt_rand,
//           weak_randomness.uniqid
// ---------------------------------------------------------------------------

function vuln_rand() {
    $token = rand(100000, 999999);
}

function vuln_mt_rand() {
    $nonce = mt_rand();
}

function vuln_uniqid() {
    $session_id = uniqid('sess_', true);
}

// ---------------------------------------------------------------------------
// Error Disclosure — display_errors enabled
// Triggers: error_disclosure.display_errors_on,
//           error_disclosure.display_startup_errors,
//           error_disclosure.wp_debug_display
// ---------------------------------------------------------------------------

function vuln_display_errors() {
    ini_set('display_errors', '1');
}

function vuln_display_startup_errors() {
    ini_set('display_startup_errors', '1');
}

function vuln_wp_debug_display() {
    define('WP_DEBUG_DISPLAY', true);
}

// ---------------------------------------------------------------------------
// Incorrect Authorization — AJAX/REST without capability checks
// Triggers: incorrect_authorization.ajax_nopriv_no_capability_check,
//           incorrect_authorization.rest_no_permission_callback
// ---------------------------------------------------------------------------

function vuln_ajax_handler() {
    echo 'delete done';
}
add_action('wp_ajax_nopriv_delete_item', 'vuln_ajax_handler');

function vuln_rest_no_permission() {
    register_rest_route('myplugin/v1', '/data', array(
        'methods' => 'GET',
        'callback' => 'get_data_handler',
    ));
}
