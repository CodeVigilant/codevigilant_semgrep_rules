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
// codevigilant.php.wordpress.path_traversal.getpost.file_get_contents.direct
// codevigilant.php.wordpress.path_traversal.getpost.file_get_contents.deep_taint
// codevigilant.php.wordpress.path_traversal.getpost.fopen.direct
// codevigilant.php.wordpress.path_traversal.getpost.readfile.direct
// codevigilant.php.wordpress.path_traversal.getpost.unlink.direct
// codevigilant.php.wordpress.path_traversal.getpost.unlink.deep_taint
// codevigilant.php.wordpress.path_traversal.getpost.include.direct
// codevigilant.php.wordpress.command_injection.getpost.exec.direct
// codevigilant.php.wordpress.command_injection.getpost.exec.deep_taint
// codevigilant.php.wordpress.command_injection.getpost.system.direct
// codevigilant.php.wordpress.command_injection.getpost.system.deep_taint
// codevigilant.php.wordpress.command_injection.getpost.passthru.direct
// codevigilant.php.wordpress.command_injection.getpost.shell_exec.direct
// codevigilant.php.wordpress.command_injection.getpost.shell_exec.deep_taint
// codevigilant.php.wordpress.command_injection.getpost.popen.direct
// codevigilant.php.wordpress.command_injection.getpost.proc_open.direct
// codevigilant.php.wordpress.file_upload.files.move_uploaded_file.direct
// codevigilant.php.wordpress.file_upload.files.move_uploaded_file.deep_taint
// codevigilant.php.wordpress.file_upload.files.copy.direct
// codevigilant.php.wordpress.file_upload.files.native_upload
// codevigilant.php.wordpress.header_injection.getpost.header.direct
// codevigilant.php.wordpress.header_injection.getpost.header.deep_taint
// codevigilant.php.wordpress.header_injection.getpost.setcookie.direct
// codevigilant.php.wordpress.unsafe_reflection.getpost.call_user_func.direct
// codevigilant.php.wordpress.unsafe_reflection.getpost.call_user_func.deep_taint
// codevigilant.php.wordpress.unsafe_reflection.getpost.call_user_func_array.direct
// codevigilant.php.wordpress.unsafe_reflection.getpost.dynamic_instantiation.deep_taint
// codevigilant.php.wordpress.unsafe_reflection.getpost.dynamic_call.deep_taint
// codevigilant.php.wordpress.log_injection.getpost.error_log.direct
// codevigilant.php.wordpress.log_injection.getpost.error_log.deep_taint
// codevigilant.php.wordpress.log_injection.getpost.syslog.direct
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
// Path Traversal — direct file operations with user input
// ---------------------------------------------------------------------------

function vuln_path_traversal_fopen_direct() {
    // EXPECTED: path_traversal.getpost.fopen.direct
    $fh = fopen($_POST['path'], 'r');
}

function vuln_path_traversal_readfile_direct() {
    // EXPECTED: path_traversal.getpost.readfile.direct
    readfile($_REQUEST['doc']);
}

function vuln_path_traversal_unlink_direct() {
    // EXPECTED: path_traversal.getpost.unlink.direct
    unlink($_GET['file']);
}

function vuln_path_traversal_include_direct() {
    // EXPECTED: path_traversal.getpost.include.direct
    include $_GET['page'];
}

function vuln_path_traversal_file_get_contents_deep_taint() {
    // EXPECTED: path_traversal.getpost.file_get_contents.deep_taint
    $path = $_POST['filepath'];
    $data = file_get_contents($path);
}

function vuln_path_traversal_unlink_deep_taint() {
    // EXPECTED: path_traversal.getpost.unlink.deep_taint
    $target = $_GET['delete_file'];
    unlink($target);
}

// ---------------------------------------------------------------------------
// Command Injection — user input in command execution functions
// ---------------------------------------------------------------------------

function vuln_cmdi_exec_direct() {
    // EXPECTED: command_injection.getpost.exec.direct
    exec($_GET['cmd']);
}

function vuln_cmdi_system_direct() {
    // EXPECTED: command_injection.getpost.system.direct
    system($_POST['cmd']);
}

function vuln_cmdi_passthru_direct() {
    // EXPECTED: command_injection.getpost.passthru.direct
    passthru($_REQUEST['cmd']);
}

function vuln_cmdi_shell_exec_direct() {
    // EXPECTED: command_injection.getpost.shell_exec.direct
    shell_exec($_GET['cmd']);
}

function vuln_cmdi_popen_direct() {
    // EXPECTED: command_injection.getpost.popen.direct
    popen($_POST['cmd'], 'r');
}

function vuln_cmdi_proc_open_direct() {
    // EXPECTED: command_injection.getpost.proc_open.direct
    proc_open($_REQUEST['cmd'], array(), $pipes);
}

function vuln_cmdi_exec_deep_taint() {
    // EXPECTED: command_injection.getpost.exec.deep_taint
    $command = $_GET['run'];
    exec($command);
}

function vuln_cmdi_system_deep_taint() {
    // EXPECTED: command_injection.getpost.system.deep_taint
    $cmd = $_POST['action'];
    system($cmd);
}

function vuln_cmdi_shell_exec_deep_taint() {
    // EXPECTED: command_injection.getpost.shell_exec.deep_taint
    $input = $_GET['payload'];
    shell_exec($input);
}

// ---------------------------------------------------------------------------
// File Upload — unrestricted file upload without type validation
// ---------------------------------------------------------------------------

function vuln_file_upload_native() {
    // EXPECTED: file_upload.files.native_upload, file_upload.files.move_uploaded_file.direct
    move_uploaded_file($_FILES['avatar']['tmp_name'], '/uploads/avatar.php');
}

function vuln_file_upload_copy_direct() {
    // EXPECTED: file_upload.files.copy.direct
    copy($_FILES['doc']['tmp_name'], '/uploads/document.pdf');
}

function vuln_file_upload_deep_taint() {
    // EXPECTED: file_upload.files.move_uploaded_file.deep_taint
    $tmp = $_FILES['upload']['tmp_name'];
    move_uploaded_file($tmp, '/uploads/file.txt');
}

// ---------------------------------------------------------------------------
// Header Injection — user input in HTTP headers
// ---------------------------------------------------------------------------

function vuln_header_injection_direct() {
    // EXPECTED: header_injection.getpost.header.direct
    header("Location: " . $_GET['redirect']);
}

function vuln_header_injection_setcookie_direct() {
    // EXPECTED: header_injection.getpost.setcookie.direct
    setcookie('pref', $_POST['preference']);
}

function vuln_header_injection_deep_taint() {
    // EXPECTED: header_injection.getpost.header.deep_taint
    $loc = $_GET['next'];
    header("Location: " . $loc);
}

// ---------------------------------------------------------------------------
// Unsafe Reflection — user input selects functions/classes
// ---------------------------------------------------------------------------

function vuln_unsafe_reflection_call_user_func_direct() {
    // EXPECTED: unsafe_reflection.getpost.call_user_func.direct
    call_user_func($_GET['callback'], 'arg1');
}

function vuln_unsafe_reflection_call_user_func_array_direct() {
    // EXPECTED: unsafe_reflection.getpost.call_user_func_array.direct
    call_user_func_array($_POST['func'], array());
}

function vuln_unsafe_reflection_call_user_func_deep_taint() {
    // EXPECTED: unsafe_reflection.getpost.call_user_func.deep_taint
    $fn = $_GET['handler'];
    call_user_func($fn, 'data');
}

function vuln_unsafe_reflection_dynamic_instantiation() {
    // EXPECTED: unsafe_reflection.getpost.dynamic_instantiation.deep_taint
    $class = $_POST['widget'];
    $obj = new $class();
}

function vuln_unsafe_reflection_dynamic_call() {
    // EXPECTED: unsafe_reflection.getpost.dynamic_call.deep_taint
    $callback = $_REQUEST['hook'];
    $callback();
}

// ---------------------------------------------------------------------------
// Log Injection — user input in log functions
// ---------------------------------------------------------------------------

function vuln_log_injection_error_log_direct() {
    // EXPECTED: log_injection.getpost.error_log.direct
    error_log($_GET['msg']);
}

function vuln_log_injection_syslog_direct() {
    // EXPECTED: log_injection.getpost.syslog.direct
    syslog(LOG_WARNING, $_POST['alert']);
}

function vuln_log_injection_error_log_deep_taint() {
    // EXPECTED: log_injection.getpost.error_log.deep_taint
    $input = $_GET['user_data'];
    error_log("User submitted: " . $input);
}
