# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Purpose

This is a collection of Semgrep security rules used for detecting vulnerabilities in source code. The rules are maintained by Code Vigilant and made publicly available.

## Structure

```
php/
  wordpress/
    SQLi/
      getpost.yaml     # Rules for $_GET and $_POST sources
      cookie.yaml      # Rules for $_COOKIE source
      server.yaml      # Rules for generic $_SERVER source
      user_agent.yaml  # Rules for User-Agent header
    xss/
      basic_xss.yaml               # Direct echo of user input
      variables_substitutes.yaml   # Variable substitution then echo
      variables_substitutes_1.yaml # Deep taint variable patterns
      filtered_add_query_arg.yaml  # add_query_arg XSS patterns
    ssrf/
      ssrf_test.yaml   # file_get_contents, fsockopen, curl_*
      ssrf_new.yaml    # wp_remote_get/post/request
    rce/
      rce_basic-fileGetContents-eval.yaml  # file_get_contents + eval chain
    deserialisation/
      deserialize.yaml # unserialize with user input
```

Rules are organized by: `<language>/<framework>/<vulnerability-category>/<source>.yaml`

## Vulnerability Categories (30 Rules Total)

### SQL Injection (SQLi) - 16 rules
- **Sources**: `$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`, `User-Agent`
- **Sinks**: `$wpdb->get_results()`, `$wpdb->get_row()`, `$wpdb->get_var()`, `$wpdb->query()`
- **Patterns**: direct, taint, deep_taint

### Cross-Site Scripting (XSS) - 4 rules
- Direct echo of user input
- Variable substitution patterns
- add_query_arg URL injection

### Server-Side Request Forgery (SSRF) - 5 rules
- PHP native: `file_get_contents()`, `fsockopen()`, `curl_init()`, `curl_setopt()`
- WordPress HTTP API: `wp_remote_get()`, `wp_remote_post()`, `wp_remote_request()`

### Remote Code Execution (RCE) - 1 rule
- `file_get_contents()` chained with `eval()`

### Insecure Deserialization - 1 rule
- `unserialize()` with user-controlled input

## Rule ID Naming Convention

All rule IDs follow a consistent namespace: `codevigilant.php.wordpress.<category>.<source>.<pattern-type>`

| Category | Namespace |
|----------|-----------|
| SQL Injection | `sqli` |
| XSS | `xss` |
| SSRF | `ssrf` |
| RCE | `rce` |
| Deserialization | `insecure_deserialization` |

### Pattern Types
- `direct` - User input directly in sink
- `taint` - User input flows via spread operator
- `deep_taint` - User input assigned to variable then used
- `chain` - Multi-function vulnerability chain

### Examples
- `codevigilant.php.wordpress.sqli.getpost.query.direct`
- `codevigilant.php.wordpress.xss.getpost.echo.direct`
- `codevigilant.php.wordpress.ssrf.getpost.file_get_contents.direct`
- `codevigilant.php.wordpress.rce.getpost.file_get_contents_eval.chain`

## Rule Conventions

All rules follow these best practices for multi-rule scenarios:

1. **Mode**: All rules explicitly use `mode: search`
2. **Severity**: Standardized to `CRITICAL`, `HIGH`, `MEDIUM`, `LOW`
   - RCE: `CRITICAL`
   - SQLi/XSS/SSRF/Deserialization: `HIGH`
3. **Languages**: `languages: [php]`
4. **Metadata**: All rules include:
   - `category: security`
   - `cwe`: CWE identifier
   - `owasp`: OWASP Top 10 reference
   - `technology: wordpress`
   - `confidence`: HIGH or MEDIUM
   - `references`: Documentation links

### Sanitization Functions Excluded

**SQLi sanitizers:**
- `$wpdb->prepare()`, `$wpdb->escape()`, `intval()`, `sprintf()`, `esc_sql()`, `rawurlencode()`

**XSS sanitizers:**
- `esc_html()`, `esc_attr()`, `esc_url()`, `esc_url_raw()`, `esc_js()`, `esc_textarea()`
- `htmlspecialchars()`, `htmlentities()`, `strip_tags()`, `wp_strip_all_tags()`
- `sanitize_text_field()`, `sanitize_key()`, `wp_kses()`, `wp_kses_post()`
- `intval()`, `absint()`

**SSRF sanitizers:**
- `esc_url()`, `esc_url_raw()`, `filter_var()`, `wp_http_validate_url()`

**RCE sanitizers:**
- `esc_url()`, `esc_url_raw()`

## Testing Rules

```bash
# Validate all rule syntax
semgrep --validate --config php/wordpress/

# Test all rules against a target
semgrep --config php/wordpress/ /path/to/wordpress/code

# Test a specific category
semgrep --config php/wordpress/SQLi/ /path/to/target
semgrep --config php/wordpress/xss/ /path/to/target
semgrep --config php/wordpress/ssrf/ /path/to/target

# Test a single rule file
semgrep --config php/wordpress/SQLi/getpost.yaml /path/to/target
```
