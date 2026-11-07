<?php
// =============================================================================
// TEST-FAIL: WordPress Security Rules
// Scan with: semgrep scan --config php/wordpress/ test/test-fail/wordpress.php
//
// Each function contains a vulnerable pattern that MUST trigger specific rules.
//
// EXPECTED_RULES:
// codevigilant.php.wordpress.sqli.getpost.query.direct
// codevigilant.php.wordpress.sqli.getpost.query.taint
// codevigilant.php.wordpress.sqli.getpost.query.deep_taint
// codevigilant.php.wordpress.sqli.getpost.get_results.direct
// codevigilant.php.wordpress.sqli.getpost.get_row.direct
// codevigilant.php.wordpress.sqli.getpost.get_row.taint
// codevigilant.php.wordpress.sqli.getpost.get_row.deep_taint
// codevigilant.php.wordpress.sqli.getpost.get_var.direct
// codevigilant.php.wordpress.sqli.cookie.query.direct
// codevigilant.php.wordpress.sqli.cookie.query.deep_taint
// codevigilant.php.wordpress.sqli.cookie.get_results.direct
// codevigilant.php.wordpress.sqli.cookie.get_row.direct
// codevigilant.php.wordpress.sqli.cookie.get_row.deep_taint
// codevigilant.php.wordpress.sqli.server.get_var.direct
// codevigilant.php.wordpress.sqli.user_agent.query.direct
// codevigilant.php.wordpress.sqli.user_agent.query.deep_taint
// codevigilant.php.wordpress.sqli.user_agent.get_results.direct
// codevigilant.php.wordpress.sqli.user_agent.get_row.direct
// codevigilant.php.wordpress.sqli.user_agent.get_row.deep_taint
// codevigilant.php.wordpress.xss.getpost.echo.direct
// codevigilant.php.wordpress.xss.getpost.variable.taint
// codevigilant.php.wordpress.xss.getpost.variable.deep_taint
// codevigilant.php.wordpress.xss.add_query_arg.echo
// codevigilant.php.wordpress.ssrf.getpost.file_get_contents.direct
// codevigilant.php.wordpress.ssrf.getpost.curl_init.direct
// codevigilant.php.wordpress.ssrf.getpost.wp_remote.taint
// codevigilant.php.wordpress.rce.getpost.file_get_contents_eval.chain
// codevigilant.php.wordpress.insecure_deserialization.getpost.unserialize.direct
// codevigilant.php.wordpress.fileops.file_write.request_content.direct
// codevigilant.php.wordpress.fileops.file_write.request_content.taint
// =============================================================================

// ---------------------------------------------------------------------------
// SQL Injection — $_GET / $_POST sources
// ---------------------------------------------------------------------------

function vuln_sqli_getpost_query_direct() {
    global $wpdb;
    // EXPECTED: sqli.getpost.query.direct, sqli.getpost.query.taint
    $wpdb->query("DELETE FROM wp_users WHERE id = " . $_GET['id']);
}

function vuln_sqli_getpost_get_results_direct() {
    global $wpdb;
    // EXPECTED: sqli.getpost.get_results.direct
    $wpdb->get_results("SELECT * FROM wp_posts WHERE author = " . $_POST['author']);
}

function vuln_sqli_getpost_get_row_direct() {
    global $wpdb;
    // EXPECTED: sqli.getpost.get_row.direct, sqli.getpost.get_row.taint
    $wpdb->get_row("SELECT * FROM wp_users WHERE login = '" . $_GET['user'] . "'");
}

function vuln_sqli_getpost_get_var_direct() {
    global $wpdb;
    // EXPECTED: sqli.getpost.get_var.direct
    $wpdb->get_var("SELECT count(*) FROM wp_posts WHERE status = " . $_POST['status']);
}

function vuln_sqli_getpost_query_deep_taint() {
    global $wpdb;
    // EXPECTED: sqli.getpost.query.deep_taint
    $user_input = $_GET['search'];
    $wpdb->query("SELECT * FROM wp_posts WHERE title LIKE '%" . $user_input . "%'");
}

function vuln_sqli_getpost_get_row_deep_taint() {
    global $wpdb;
    // EXPECTED: sqli.getpost.get_row.deep_taint
    $name = $_POST['name'];
    $wpdb->get_row("SELECT * FROM wp_users WHERE display_name = '" . $name . "'");
}

// ---------------------------------------------------------------------------
// SQL Injection — $_COOKIE source
// ---------------------------------------------------------------------------

function vuln_sqli_cookie_query_direct() {
    global $wpdb;
    // EXPECTED: sqli.cookie.query.direct
    $wpdb->query("DELETE FROM wp_sessions WHERE token = '" . $_COOKIE['session'] . "'");
}

function vuln_sqli_cookie_get_results_direct() {
    global $wpdb;
    // EXPECTED: sqli.cookie.get_results.direct
    $wpdb->get_results("SELECT * FROM wp_prefs WHERE cookie = '" . $_COOKIE['pref'] . "'");
}

function vuln_sqli_cookie_get_row_direct() {
    global $wpdb;
    // EXPECTED: sqli.cookie.get_row.direct
    $wpdb->get_row("SELECT * FROM wp_sessions WHERE id = '" . $_COOKIE['sid'] . "'");
}

function vuln_sqli_cookie_query_deep_taint() {
    global $wpdb;
    // EXPECTED: sqli.cookie.query.deep_taint
    $token = $_COOKIE['auth'];
    $wpdb->query("UPDATE wp_sessions SET active = 0 WHERE token = '" . $token . "'");
}

function vuln_sqli_cookie_get_row_deep_taint() {
    global $wpdb;
    // EXPECTED: sqli.cookie.get_row.deep_taint
    $sess = $_COOKIE['sess_id'];
    $wpdb->get_row("SELECT * FROM wp_sessions WHERE sess = '" . $sess . "'");
}

// ---------------------------------------------------------------------------
// SQL Injection — $_SERVER source
// ---------------------------------------------------------------------------

function vuln_sqli_server_get_var_direct() {
    global $wpdb;
    // EXPECTED: sqli.server.get_var.direct
    $wpdb->get_var("SELECT id FROM wp_logs WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'");
}

// ---------------------------------------------------------------------------
// SQL Injection — User-Agent source
// ---------------------------------------------------------------------------

function vuln_sqli_ua_query_direct() {
    global $wpdb;
    // EXPECTED: sqli.user_agent.query.direct
    $wpdb->query("INSERT INTO wp_logs (ua) VALUES ('" . $_SERVER['HTTP_USER_AGENT'] . "')");
}

function vuln_sqli_ua_get_results_direct() {
    global $wpdb;
    // EXPECTED: sqli.user_agent.get_results.direct
    $wpdb->get_results("SELECT * FROM wp_logs WHERE ua = '" . $_SERVER['HTTP_USER_AGENT'] . "'");
}

function vuln_sqli_ua_get_row_direct() {
    global $wpdb;
    // EXPECTED: sqli.user_agent.get_row.direct
    $wpdb->get_row("SELECT * FROM wp_logs WHERE ua = '" . $_SERVER['HTTP_USER_AGENT'] . "'");
}

function vuln_sqli_ua_query_deep_taint() {
    global $wpdb;
    // EXPECTED: sqli.user_agent.query.deep_taint
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $wpdb->query("INSERT INTO wp_stats (browser) VALUES ('" . $ua . "')");
}

function vuln_sqli_ua_get_row_deep_taint() {
    global $wpdb;
    // EXPECTED: sqli.user_agent.get_row.deep_taint
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $wpdb->get_row("SELECT * FROM wp_stats WHERE browser = '" . $ua . "'");
}

// ---------------------------------------------------------------------------
// XSS — Direct echo
// ---------------------------------------------------------------------------

function vuln_xss_echo_direct() {
    // EXPECTED: xss.getpost.echo.direct
    echo $_GET['search'];
}

// ---------------------------------------------------------------------------
// XSS — Variable taint (assign then echo)
// ---------------------------------------------------------------------------

function vuln_xss_variable_taint() {
    // EXPECTED: xss.getpost.variable.taint, xss.getpost.variable.deep_taint
    $comment = $_POST['comment'];
    echo $comment;
}

// ---------------------------------------------------------------------------
// XSS — add_query_arg without escaping
// ---------------------------------------------------------------------------

function vuln_xss_add_query_arg() {
    // EXPECTED: xss.add_query_arg.echo
    $url = add_query_arg('tab', 'settings');
    echo $url;
}

// ---------------------------------------------------------------------------
// SSRF — file_get_contents with user input
// ---------------------------------------------------------------------------

function vuln_ssrf_file_get_contents() {
    // EXPECTED: ssrf.getpost.file_get_contents.direct
    $data = file_get_contents($_GET['url']);
}

// ---------------------------------------------------------------------------
// SSRF — curl_init with user input
// ---------------------------------------------------------------------------

function vuln_ssrf_curl_init() {
    // EXPECTED: ssrf.getpost.curl_init.direct
    $ch = curl_init($_POST['endpoint']);
}

// ---------------------------------------------------------------------------
// SSRF — wp_remote_get with user input (deep taint)
// ---------------------------------------------------------------------------

function vuln_ssrf_wp_remote_taint() {
    // EXPECTED: ssrf.getpost.wp_remote.taint
    $api_url = $_GET['api_url'];
    $response = wp_remote_get($api_url);
}

// ---------------------------------------------------------------------------
// RCE — file_get_contents + eval chain
// ---------------------------------------------------------------------------

function vuln_rce_file_get_contents_eval() {
    // EXPECTED: rce.getpost.file_get_contents_eval.chain
    $code = file_get_contents($_GET['plugin_url']);
    eval($code);
}

// ---------------------------------------------------------------------------
// Insecure Deserialization
// ---------------------------------------------------------------------------

function vuln_deserialize() {
    // EXPECTED: insecure_deserialization.getpost.unserialize.direct
    $obj = unserialize($_POST['data']);
}

// ---------------------------------------------------------------------------
// Request-controlled content written into an executable PHP file (CWE-94)
// ---------------------------------------------------------------------------

function vuln_php_file_content_write_inline() {
    // EXPECTED: fileops.file_write.request_content.direct
    // EXPECTED: fileops.file_write.request_content.taint
    if (!isset($_SERVER['HTTP_X_FORWARDED_HOST'])) { return; }
    $html = '<p>' . $_SERVER['HTTP_X_FORWARDED_HOST'] . '</p>';
    file_put_contents(WP_CONTENT_DIR . '/db-error.php', '<?php http_response_code(500); ?>' . $html);
}

function vuln_php_file_content_write_assigned() {
    // EXPECTED: fileops.file_write.request_content.direct
    // EXPECTED: fileops.file_write.request_content.taint
    if (!isset($_POST['cache_value'])) { return; }
    $cache_file = WP_CONTENT_DIR . '/cache/plugin-options.php';
    $body = "<?php\nreturn '" . $_POST['cache_value'] . "';\n";
    file_put_contents($cache_file, $body);
}

function vuln_php_file_content_write_direct() {
    // EXPECTED: fileops.file_write.request_content.direct
    // EXPECTED: fileops.file_write.request_content.taint
    if (!isset($_POST['tpl'])) { return; }
    file_put_contents(WP_CONTENT_DIR . '/cache/template.php', $_POST['tpl']);
}
