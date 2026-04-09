# Plan: New CWE Rules for PHP/WordPress

## Context
Cross-referencing the full CWE Top 25 (2024) and OWASP Top 10 (2021) CWE mappings against what already exists in the repo. Goal: identify CWEs **not yet covered** that are relevant to PHP/WordPress and can be expressed as semgrep rules.

## Current Coverage (27 CWEs)

### Full vulnerability rules (php/wordpress/) — 5 CWEs:
- CWE-79 (XSS), CWE-89 (SQLi), CWE-94 (Code Injection/RCE), CWE-502 (Deserialization), CWE-918 (SSRF)

### Coding-standards rules only (php/coding-standards/) — 22 CWEs:
- CWE-20, CWE-73, CWE-78, CWE-95, CWE-98, CWE-129, CWE-200, CWE-250, CWE-276, CWE-312, CWE-352, CWE-362, CWE-384, CWE-400, CWE-473, CWE-477, CWE-601, CWE-668, CWE-732, CWE-754, CWE-862, CWE-1021

---

## New CWEs to Add — Full Vulnerability Rules (php/wordpress/)

These have clear **source → sink** patterns and can use the same structure as existing rules.

### 1. CWE-22: Path Traversal (HIGH) — `php/wordpress/path_traversal/`
- **Sources**: `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`
- **Sinks**: `file_get_contents()`, `fopen()`, `readfile()`, `file()`, `unlink()`, `copy()`, `rename()`, `is_file()`, `file_exists()`
- **Sanitizers**: `realpath()`, `basename()`, `wp_normalize_path()`, `sanitize_file_name()`
- **Patterns**: direct, taint, deep_taint
- **OWASP**: A01:2021, CWE Top 25 #5
- **Severity**: HIGH

### 2. CWE-77: Command Injection (CRITICAL) — `php/wordpress/command_injection/`
- **Sources**: `$_GET`, `$_POST`, `$_REQUEST`
- **Sinks**: `exec()`, `system()`, `passthru()`, `shell_exec()`, `popen()`, `proc_open()`, `pcntl_exec()`
- **Sanitizers**: `escapeshellarg()`, `escapeshellcmd()`
- **Patterns**: direct, taint, deep_taint
- **OWASP**: A03:2021, CWE Top 25 #13
- **Severity**: CRITICAL
- **Note**: CWE-78 exists as coding-standard; this adds full taint tracking

### 3. CWE-434: Unrestricted File Upload (HIGH) — `php/wordpress/file_upload/`
- **Sources**: `$_FILES`
- **Sinks**: `move_uploaded_file()`, `copy()`, `rename()`
- **Sanitizers**: `wp_check_filetype()`, `wp_handle_upload()`, `wp_check_filetype_and_ext()`
- **Patterns**: direct, taint
- **OWASP**: A04:2021, CWE Top 25 #10
- **Severity**: HIGH

### 4. CWE-113: HTTP Header Injection / CRLF (MEDIUM) — `php/wordpress/header_injection/`
- **Sources**: `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`
- **Sinks**: `header()`, `setcookie()`
- **Sanitizers**: `sanitize_text_field()`, `esc_url()`, `filter_var()`
- **Patterns**: direct, taint, deep_taint
- **OWASP**: A03:2021
- **Severity**: MEDIUM

### 5. CWE-470: Unsafe Reflection / Dynamic Code (CRITICAL) — `php/wordpress/unsafe_reflection/`
- **Sources**: `$_GET`, `$_POST`, `$_REQUEST`
- **Sinks**: `new $var()`, `$var()`, `call_user_func()`, `call_user_func_array()`
- **Sanitizers**: whitelist check patterns
- **Patterns**: direct, taint
- **OWASP**: A03:2021
- **Severity**: CRITICAL

### 6. CWE-117: Log Injection (MEDIUM) — `php/wordpress/log_injection/`
- **Sources**: `$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`
- **Sinks**: `error_log()`, `syslog()`, `openlog()`
- **Sanitizers**: `sanitize_text_field()`, `esc_html()`, preg_replace for newlines
- **Patterns**: direct, taint, deep_taint
- **OWASP**: A09:2021
- **Severity**: MEDIUM

---

## New CWEs to Add — Coding Standards Rules (php/coding-standards/)

These are pattern-based (no source → sink taint) but still valuable.

### 7. CWE-611: XML External Entity / XXE (HIGH) — `php/coding-standards/security/xxe.yaml`
- Detect `simplexml_load_string()`, `simplexml_load_file()`, `DOMDocument::loadXML()` without `libxml_disable_entity_loader(true)` or `LIBXML_NOENT`

### 8. CWE-798: Hard-coded Credentials (HIGH) — `php/coding-standards/security/hardcoded-credentials.yaml`
- Detect password/secret/key/token literals in assignments, define() calls, and config arrays

### 9. CWE-614 + CWE-1004: Insecure Cookie Flags (MEDIUM) — `php/coding-standards/security/insecure-cookies.yaml`
- Detect `setcookie()` without `secure` and `httponly` flags

### 10. CWE-327: Weak Cryptography (MEDIUM) — `php/coding-standards/security/weak-crypto.yaml`
- Detect `md5()`, `sha1()` for password hashing, `mcrypt_*` usage, `crypt()` without strong algorithm

### 11. CWE-330: Weak Randomness (MEDIUM) — `php/coding-standards/security/weak-randomness.yaml`
- Detect `rand()`, `mt_rand()`, `array_rand()`, `uniqid()` in security contexts; recommend `random_bytes()`, `random_int()`, `wp_generate_password()`

### 12. CWE-209: Error Message Info Disclosure (LOW) — `php/coding-standards/security/error-disclosure.yaml`
- Detect `ini_set('display_errors', '1')`, `error_reporting(E_ALL)` in non-dev contexts

### 13. CWE-863: Incorrect Authorization (HIGH) — `php/coding-standards/wordpress-security/incorrect-authorization.yaml`
- Detect AJAX/REST handlers (`wp_ajax_nopriv_*`, `rest_api_init`) without `current_user_can()` checks

---

## Summary: 13 New CWEs → Total Coverage: 40 CWEs

| # | CWE | Name | Rule Type | Severity | Priority |
|---|-----|------|-----------|----------|----------|
| 1 | CWE-22 | Path Traversal | Vulnerability | HIGH | P1 |
| 2 | CWE-77 | Command Injection | Vulnerability | CRITICAL | P1 |
| 3 | CWE-434 | Unrestricted File Upload | Vulnerability | HIGH | P1 |
| 4 | CWE-113 | HTTP Header Injection | Vulnerability | MEDIUM | P2 |
| 5 | CWE-470 | Unsafe Reflection | Vulnerability | CRITICAL | P2 |
| 6 | CWE-117 | Log Injection | Vulnerability | MEDIUM | P2 |
| 7 | CWE-611 | XXE | Coding Std | HIGH | P2 |
| 8 | CWE-798 | Hard-coded Credentials | Coding Std | HIGH | P2 |
| 9 | CWE-614/1004 | Insecure Cookie Flags | Coding Std | MEDIUM | P3 |
| 10 | CWE-327 | Weak Cryptography | Coding Std | MEDIUM | P3 |
| 11 | CWE-330 | Weak Randomness | Coding Std | MEDIUM | P3 |
| 12 | CWE-209 | Error Info Disclosure | Coding Std | LOW | P3 |
| 13 | CWE-863 | Incorrect Authorization | Coding Std | HIGH | P2 |

## File structure for new rules
```
php/wordpress/path_traversal/getpost.yaml         # CWE-22
php/wordpress/command_injection/getpost.yaml       # CWE-77
php/wordpress/file_upload/upload.yaml              # CWE-434
php/wordpress/header_injection/getpost.yaml        # CWE-113
php/wordpress/unsafe_reflection/getpost.yaml       # CWE-470
php/wordpress/log_injection/getpost.yaml           # CWE-117
php/coding-standards/security/xxe.yaml             # CWE-611
php/coding-standards/security/hardcoded-credentials.yaml  # CWE-798
php/coding-standards/security/insecure-cookies.yaml       # CWE-614/1004
php/coding-standards/security/weak-crypto.yaml     # CWE-327
php/coding-standards/security/weak-randomness.yaml # CWE-330
php/coding-standards/security/error-disclosure.yaml # CWE-209
php/coding-standards/wordpress-security/incorrect-authorization.yaml # CWE-863
```

## Verification
```bash
# Validate all new rules
semgrep --validate --config php/wordpress/path_traversal/
semgrep --validate --config php/wordpress/command_injection/
semgrep --validate --config php/wordpress/file_upload/
semgrep --validate --config php/wordpress/header_injection/
semgrep --validate --config php/wordpress/unsafe_reflection/
semgrep --validate --config php/wordpress/log_injection/
semgrep --validate --config php/coding-standards/security/xxe.yaml
semgrep --validate --config php/coding-standards/security/hardcoded-credentials.yaml
semgrep --validate --config php/coding-standards/security/insecure-cookies.yaml
semgrep --validate --config php/coding-standards/security/weak-crypto.yaml
semgrep --validate --config php/coding-standards/security/weak-randomness.yaml
semgrep --validate --config php/coding-standards/security/error-disclosure.yaml
semgrep --validate --config php/coding-standards/wordpress-security/incorrect-authorization.yaml
```
