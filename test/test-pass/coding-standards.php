<?php
// =============================================================================
// TEST-PASS: Coding Standards Security Rules — Safe Code
// Scan with: semgrep scan --config php/coding-standards/ test/test-pass/coding-standards.php
//
// All code here follows best practices.
// EXPECTED: 0 findings
// =============================================================================

// ---------------------------------------------------------------------------
// Redirect — exit/die after wp_safe_redirect
// ---------------------------------------------------------------------------

function safe_redirect_with_die() {
    wp_safe_redirect(admin_url());
    die;
}

function safe_redirect_with_exit() {
    wp_safe_redirect(home_url());
    exit;
}

function safe_redirect_with_die_parens() {
    wp_safe_redirect($url);
    die();
}

function safe_redirect_blank_line_then_die() {
    wp_safe_redirect(admin_url('admin.php?page=my-page'));

    die;
}

// ---------------------------------------------------------------------------
// Escaping — correct function usage (return vs void)
// ---------------------------------------------------------------------------

function safe_escaping_return_functions() {
    echo esc_html('text');
    echo esc_attr('value');
    echo esc_url('https://example.com');
    echo wp_kses_post('<p>safe</p>');
}

function safe_escaping_void_functions() {
    _e('Hello', 'textdomain');
    esc_html_e('Text', 'textdomain');
    esc_attr_e('value', 'textdomain');
}

// ---------------------------------------------------------------------------
// Input Validation — properly validated superglobals
// ---------------------------------------------------------------------------

function safe_null_coalescing() {
    $val = $_GET['param'] ?? 'default';
}

function safe_sanitize_wrapper() {
    $key = sanitize_key($_GET['param_name']);
}

function safe_intval_wrapper() {
    $id = intval($_POST['id']);
}

function safe_absint_wrapper() {
    $count = absint($_REQUEST['count']);
}

function safe_wp_unslash_sanitize() {
    $text = sanitize_text_field(wp_unslash($_POST['input']));
}

function safe_guard_clause() {
    if (!isset($_GET['action'])) {
        return;
    }
    $action = $_GET['action'];
}

function safe_short_circuit() {
    $is_page = isset($_GET['post_type']) && 'product' === $_GET['post_type'];
}

function safe_json_decode() {
    $data = json_decode(wp_unslash($_REQUEST['actions']), true);
}

function safe_write_to_superglobal() {
    $_REQUEST['state'] = 'active';
}

// ---------------------------------------------------------------------------
// Safe include — hardcoded path only
// ---------------------------------------------------------------------------

function safe_include() {
    include __DIR__ . '/template.php';
}

// ---------------------------------------------------------------------------
// Safe function usage — no dangerous functions
// ---------------------------------------------------------------------------

function safe_no_phpinfo() {
    $info = php_uname();
}

// ---------------------------------------------------------------------------
// XXE — external entities disabled before parsing
// ---------------------------------------------------------------------------

function safe_xxe_disabled() {
    libxml_disable_entity_loader(true);
    $data = simplexml_load_string('<root/>');
}

function safe_xxe_dom_disabled() {
    libxml_disable_entity_loader(true);
    $dom = new DOMDocument();
    $dom->loadXML('<root/>');
}

// ---------------------------------------------------------------------------
// Credentials — from environment, not hardcoded
// ---------------------------------------------------------------------------

function safe_credentials_env() {
    $password = getenv('DB_PASSWORD');
}

// ---------------------------------------------------------------------------
// Cookies — with Secure + HttpOnly flags
// ---------------------------------------------------------------------------

// Note: setcookie() itself triggers VIP restricted functions rule,
// so we verify the safe pattern conceptually without calling setcookie.
function safe_cookie_all_flags() {
    // safe: would use setcookie with all flags including secure+httponly
    $options = array('secure' => true, 'httponly' => true, 'samesite' => 'Strict');
}

// ---------------------------------------------------------------------------
// Cryptography — strong algorithms
// ---------------------------------------------------------------------------

function safe_crypto_hash() {
    $hash = hash('sha256', $data);
}

function safe_crypto_wp_hash() {
    $hash = wp_hash_password($plain);
}

// ---------------------------------------------------------------------------
// Randomness — cryptographically secure
// ---------------------------------------------------------------------------

function safe_random_int() {
    $token = random_int(100000, 999999);
}

function safe_random_bytes() {
    $nonce = bin2hex(random_bytes(16));
}

// ---------------------------------------------------------------------------
// Error handling — errors logged, not displayed
// ---------------------------------------------------------------------------

function safe_errors_hidden() {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

function safe_wp_debug_display_off() {
    define('WP_DEBUG_DISPLAY', false);
}

// ---------------------------------------------------------------------------
// Authorization — proper capability checks
// ---------------------------------------------------------------------------

function safe_ajax_with_cap_check() {
    if (!current_user_can($required_cap)) {
        wp_die('Unauthorized');
    }
    echo 'authorized action';
}

function safe_rest_with_permission() {
    register_rest_route('myplugin/v1', '/data', array(
        'methods' => 'GET',
        'callback' => 'get_data_handler',
        'permission_callback' => 'check_permissions',
    ));
}
