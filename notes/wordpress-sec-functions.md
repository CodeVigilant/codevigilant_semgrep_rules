# WordPress Security Functions Reference

This document catalogs all WordPress security-related functions for building Semgrep rule whitelists/blacklists.

## 1. Escaping Functions (XSS Prevention)

These functions sanitize output for safe HTML rendering.

### Core Escaping
| Function | Purpose |
|----------|---------|
| `esc_html()` | Escapes HTML entities |
| `esc_attr()` | Escapes HTML attributes |
| `esc_url()` | Escapes URLs for display |
| `esc_url_raw()` | Escapes URLs for storage/API |
| `esc_js()` | Escapes for JavaScript strings |
| `esc_textarea()` | Escapes for textarea content |
| `esc_sql()` | Escapes for SQL queries (use $wpdb->prepare instead) |
| `esc_xml()` | Escapes for XML output |

### Translation + Escaping
| Function | Purpose |
|----------|---------|
| `esc_html__()` | Translate + escape HTML |
| `esc_html_e()` | Echo translated + escaped HTML |
| `esc_html_x()` | Translate with context + escape |
| `esc_attr__()` | Translate + escape attribute |
| `esc_attr_e()` | Echo translated + escaped attribute |
| `esc_attr_x()` | Translate with context + escape attribute |

### URL Sanitization
| Function | Purpose |
|----------|---------|
| `sanitize_url()` | Sanitizes URL |
| `esc_like()` | Escapes for LIKE queries |

---

## 2. Sanitization Functions (Input Validation)

These functions sanitize user input for storage/processing.

### Text Sanitization
| Function | Purpose |
|----------|---------|
| `sanitize_text_field()` | Sanitizes text input |
| `sanitize_textarea_field()` | Sanitizes textarea input |
| `sanitize_title()` | Sanitizes title strings |
| `sanitize_title_for_query()` | Sanitizes title for DB query |
| `sanitize_title_with_dashes()` | Sanitizes title with dashes |
| `sanitize_user()` | Sanitizes username |
| `sanitize_email()` | Sanitizes email address |
| `sanitize_key()` | Sanitizes array/object keys |

### HTML/Content Sanitization
| Function | Purpose |
|----------|---------|
| `sanitize_html_class()` | Sanitizes HTML class names |
| `sanitize_mime_type()` | Sanitizes MIME types |
| `sanitize_file_name()` | Sanitizes filenames |
| `sanitize_option()` | Sanitizes option values |
| `sanitize_meta()` | Sanitizes metadata |
| `sanitize_term()` | Sanitizes term data |
| `sanitize_term_field()` | Sanitizes term fields |
| `sanitize_category()` | Sanitizes category data |
| `sanitize_category_field()` | Sanitizes category fields |
| `sanitize_post()` | Sanitizes post data |
| `sanitize_post_field()` | Sanitizes post fields |
| `sanitize_bookmark()` | Sanitizes bookmark data |
| `sanitize_bookmark_field()` | Sanitizes bookmark fields |
| `sanitize_user_field()` | Sanitizes user fields |
| `sanitize_comment_cookies()` | Sanitizes comment cookies |

### Color Sanitization
| Function | Purpose |
|----------|---------|
| `sanitize_hex_color()` | Sanitizes hex color |
| `sanitize_hex_color_no_hash()` | Sanitizes hex color without hash |

### Other Sanitization
| Function | Purpose |
|----------|---------|
| `sanitize_sql_orderby()` | Sanitizes SQL ORDER BY |
| `sanitize_locale_name()` | Sanitizes locale names |
| `sanitize_trackback_urls()` | Sanitizes trackback URLs |

---

## 3. KSES Functions (HTML Filtering)

These functions filter HTML to allow only safe tags/attributes.

| Function | Purpose |
|----------|---------|
| `wp_kses()` | Filters HTML with allowed tags |
| `wp_kses_post()` | KSES for post content |
| `wp_kses_post_deep()` | Deep KSES for arrays |
| `wp_kses_data()` | KSES for arbitrary data |
| `wp_kses_one_attr()` | KSES for single attribute |
| `wp_kses_allowed_html()` | Gets allowed HTML tags |
| `wp_kses_hook()` | KSES filter hook |
| `wp_kses_attr()` | Parses HTML attributes |
| `wp_kses_attr_check()` | Validates attributes |
| `wp_kses_bad_protocol()` | Removes bad protocols |
| `wp_kses_split()` | Splits content for processing |
| `wp_kses_version()` | Returns KSES version |
| `wp_strip_all_tags()` | Removes all HTML tags |

---

## 4. Nonce Functions (CSRF Protection)

These functions handle WordPress nonce creation and verification.

| Function | Purpose |
|----------|---------|
| `wp_create_nonce()` | Creates a nonce |
| `wp_verify_nonce()` | Verifies a nonce |
| `wp_nonce_field()` | Outputs nonce form field |
| `wp_nonce_url()` | Adds nonce to URL |
| `wp_nonce_tick()` | Gets nonce tick |
| `wp_nonce_ays()` | "Are you sure?" confirmation |
| `check_admin_referer()` | Verifies admin referer + nonce |
| `filter_nonces()` | Filters nonce values |

---

## 5. Capability Functions (Authorization)

These functions check user permissions.

| Function | Purpose |
|----------|---------|
| `current_user_can()` | Checks current user capability |
| `current_user_can_for_blog()` | Checks capability for specific blog |
| `current_user_can_for_site()` | Checks capability for specific site |
| `user_can()` | Checks if user has capability |
| `user_can_for_site()` | Checks user capability for site |
| `auth_redirect()` | Redirects to login if not logged in |

### Legacy (Deprecated)
| Function | Purpose |
|----------|---------|
| `user_can_create_post()` | Legacy post creation check |
| `user_can_edit_post()` | Legacy post edit check |
| `user_can_delete_post()` | Legacy post delete check |
| `user_can_edit_user()` | Legacy user edit check |

---

## 6. Safe Redirect Functions

| Function | Purpose |
|----------|---------|
| `wp_safe_redirect()` | Safe redirect (validates URL) |
| `wp_redirect()` | Basic redirect (use wp_safe_redirect) |

---

## 7. Safe HTTP Request Functions

| Function | Purpose |
|----------|---------|
| `wp_safe_remote_get()` | Safe GET request |
| `wp_safe_remote_post()` | Safe POST request |
| `wp_safe_remote_head()` | Safe HEAD request |
| `wp_safe_remote_request()` | Safe generic request |

---

## 8. Password/Hash Functions

| Function | Purpose |
|----------|---------|
| `wp_hash()` | Creates hash |
| `wp_hash_password()` | Hashes password |
| `wp_verify_fast_hash()` | Verifies fast hash |
| `wp_generate_password()` | Generates random password |
| `wp_password_needs_rehash()` | Checks if password needs rehash |
| `wp_set_password()` | Sets user password |
| `wp_validate_application_password()` | Validates app password |

---

## 9. Authentication Functions

| Function | Purpose |
|----------|---------|
| `wp_signon()` | Authenticates user |
| `wp_logout()` | Logs out user |
| `wp_logout_url()` | Gets logout URL |
| `wp_set_auth_cookie()` | Sets auth cookie |
| `wp_validate_auth_cookie()` | Validates auth cookie |
| `wp_parse_auth_cookie()` | Parses auth cookie |
| `wp_get_current_user()` | Gets current user |

---

## 10. Database Escaping Functions

| Function | Purpose |
|----------|---------|
| `$wpdb->prepare()` | Prepares SQL query (PRIMARY) |
| `esc_sql()` | Escapes SQL (use prepare instead) |
| `like_escape()` | Escapes for LIKE queries |
| `sanitize_sql_orderby()` | Sanitizes ORDER BY clause |

---

## 11. Data Type Casting (Safe)

| Function | Purpose |
|----------|---------|
| `intval()` | Cast to integer |
| `absint()` | Cast to positive integer |
| `floatval()` | Cast to float |
| `boolval()` | Cast to boolean |

---

## 12. String Processing (Safe)

| Function | Purpose |
|----------|---------|
| `wp_unslash()` | Removes slashes (WP-compat) |
| `stripslashes_deep()` | Deep stripslashes |
| `stripslashes_from_strings_only()` | Stripslashes strings only |

---

## 13. Validation Functions

| Function | Purpose |
|----------|---------|
| `validate_file()` | Validates file path |
| `validate_file_to_edit()` | Validates file for editing |
| `validate_email()` | Validates email |
| `validate_username()` | Validates username |
| `validate_plugin()` | Validates plugin |
| `validate_current_theme()` | Validates current theme |
| `validate_redirects()` | Validates redirects |
| `validate_cookie()` | Validates cookie |
| `validate_request_permission()` | Validates request permission |

---

## 14. URL Functions (Safe)

| Function | Purpose |
|----------|---------|
| `admin_url()` | Admin area URL |
| `self_admin_url()` | Current admin URL |
| `network_admin_url()` | Network admin URL |
| `user_admin_url()` | User admin URL |
| `home_url()` | Home URL |
| `site_url()` | Site URL |

---

## 15. Admin Check Functions

| Function | Purpose |
|----------|---------|
| `is_admin()` | Checks if in admin area |
| `is_admin_bar_showing()` | Checks if admin bar visible |
| `check_admin_referer()` | Verifies admin referer |
| `user_can_access_admin_page()` | Checks admin page access |

---

## Usage in Semgrep Rules

### Whitelist (Safe Functions) - Use in pattern-sanitizers

**XSS Sanitizers:**
```
esc_html, esc_attr, esc_url, esc_url_raw, esc_js, esc_textarea, esc_xml
wp_kses, wp_kses_post, wp_kses_data, wp_kses_post_deep, wp_strip_all_tags
sanitize_text_field, sanitize_textarea_field, sanitize_key
htmlspecialchars, htmlentities, strip_tags
intval, absint, floatval
```

**SQL Injection Sanitizers:**
```
$wpdb->prepare, esc_sql, intval, absint, floatval, like_escape
sanitize_sql_orderby
```

**CSRF Sanitizers:**
```
wp_verify_nonce, check_admin_referer
```

### Blacklist (Dangerous Functions) - Use in pattern-sinks

**Dangerous Output:**
```
echo, print, printf, die, exit
```

**Dangerous SQL:**
```
$wpdb->query, $wpdb->get_results, $wpdb->get_var, $wpdb->get_row, $wpdb->get_col
mysql_query, mysqli_query (deprecated)
```

**Dangerous File Operations:**
```
include, require, include_once, require_once
file_get_contents, file_put_contents
fopen, fwrite
```

**Dangerous Execution:**
```
eval, assert, system, exec, shell_exec, passthru, popen, proc_open
```

---

## Notes

1. **Always prefer $wpdb->prepare() over esc_sql()** for SQL queries
2. **Always prefer wp_safe_redirect() over wp_redirect()**
3. **Always prefer wp_safe_remote_*() over wp_remote_*()** for external requests
4. **Use appropriate escaping for context** (esc_html for content, esc_attr for attributes, esc_url for URLs)
5. **Combine sanitization + escaping** for defense in depth

