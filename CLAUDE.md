# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Purpose

This is a collection of Semgrep security rules used for detecting vulnerabilities in source code. The rules are maintained by Code Vigilant and made publicly available.

## Structure

```
php/
  wordpress/
    SQLi/           # SQL Injection detection rules
```

Rules are organized by: `<language>/<framework>/<vulnerability-category>/`

## Rule Conventions

- All rules use `mode: search` for pattern matching
- Severity levels: `WARNING` for potential vulnerabilities
- Language specification: `languages: [php]`

### SQL Injection Pattern Structure

Rules detect user input (`$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`) flowing into WordPress `$wpdb` methods without sanitization. Safe functions excluded via `pattern-not-inside`:
- `$wpdb->prepare()`
- `$wpdb->escape()`
- `intval()`
- `sprintf()`
- `esc_sql()`
- `rawurlencode()`

### Rule Types

1. **Direct taint**: User input directly in SQL method calls
2. **Variable substitution**: User input assigned to variable then used in SQL (uses `<... $V ...>` spread operator)

## Testing Rules

```bash
# Test a single rule against a target file/directory
semgrep --config path/to/rule.yaml /path/to/target/code

# Test all rules
semgrep --config php/ /path/to/target/code

# Validate rule syntax
semgrep --validate --config path/to/rule.yaml
```
