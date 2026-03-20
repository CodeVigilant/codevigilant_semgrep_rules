# CodeVigilant Semgrep Rules

A curated collection of Semgrep security rules for **WordPress and PHP development**. This repository focuses exclusively on WordPress-specific security vulnerabilities.

> **Note**: We focus exclusively on **security rules**. Coding style and quality rules are not included.

## Quick Start

```bash
# Run ALL rules (117 total)
semgrep --config php/ <folder-to-scan>

# Run CodeVigilant original security rules only (30 rules)
semgrep --config php/wordpress/ <folder-to-scan>

# Run WordPress/VIP coding standard rules (87 rules)
semgrep --config php/coding-standards/ <folder-to-scan>

# Run a specific rule category
semgrep --config php/wordpress/SQLi/ <folder-to-scan>
semgrep --config php/coding-standards/security/ <folder-to-scan>
```

> ⚠️ **Important**: Do not run these rules on your entire WordPress codebase. Run them against specific plugins or themes you want to analyze. Semgrep may run out of memory on large codebases.

## Rule Sources

This repository contains rules from two sources:

1. **Original CodeVigilant Rules** - Custom rules developed by the CodeVigilant team
2. **Ported Rules** - Rules ported from other security tools with proper attribution

When rules are ported from other tools, we clearly credit the original source in the rule metadata and documentation.

## Current Status

| Category | Rules | Source |
|----------|-------|--------|
| SQL Injection (SQLi) | 28 | Original + Ported |
| Cross-Site Scripting (XSS) | 18 | Original + Ported |
| Server-Side Request Forgery (SSRF) | 5 | Original |
| Remote Code Execution (RCE) | 12 | Original + Ported |
| Insecure Deserialization | 1 | Original |
| Input Validation | 5 | Ported |
| File Inclusion (LFI/RFI) | 3 | Ported |
| Open Redirect | 3 | Ported |
| CSRF/Nonce | 3 | Ported |
| Template Injection | 4 | Ported |
| DoS/Resource Exhaustion | 5 | Ported |
| VIP-Specific Security | 21 | Ported |
| WordPress Security | 13 | Ported |
| PHP Security | 19 | Ported |
| **Total** | **117** | **All Categories** |

## Attribution & Credits

This repository contains **117 total rules**:
- **30 original CodeVigilant rules** (php/wordpress/)
- **87 ported rules** from WPCS/VIPCS (php/coding-standards/)

### Original CodeVigilant Rules
Core security rules developed by the CodeVigilant team covering SQLi, XSS, SSRF, RCE, and deserialization patterns.

### Ported from WordPress Coding Standards (WPCS)
Selected security-focused rules converted from PHPCS sniffs.

- **Repository**: https://github.com/WordPress/WordPress-Coding-Standards
- **License**: MIT License
- **Copyright**: (c) 2009 John Godley and contributors

### Ported from VIP Coding Standards (VIPCS)
Selected security-focused rules converted from PHPCS sniffs.

- **Repository**: https://github.com/Automattic/VIP-Coding-Standards
- **License**: GPL-2.0-or-later
- **Copyright**: (c) 2016 Automattic Inc

## Contributors

### CodeVigilant Team
- **Anant Shrivastava** ([@anantshri](https://github.com/anantshri))
- **Shreya Pohekar** ([@shreyapohekar](https://github.com/shreyapohekar))
- **Sheeraz Ali** ([@pwnmeow](https://github.com/pwnmeow))

## Rule Classification

### Security Rules (Implement)
Rules that detect security vulnerabilities that could lead to:
- SQL Injection
- Cross-Site Scripting (XSS)
- Remote Code Execution (RCE)
- Local/Remote File Inclusion (LFI/RFI)
- Open Redirects
- CSRF vulnerabilities
- Template Injection
- Denial of Service (DoS)
- Information Disclosure

### Coding Rules (Not Included)
Rules that detect code quality or style issues are not included in this repository:
- Naming conventions
- Spacing and formatting
- Deprecated functions (non-security)
- Performance optimizations
- Best practices (non-security)
- Documentation issues

## Directory Structure

```
php/
  wordpress/                    # Original CodeVigilant rules (30 rules)
    SQLi/                       # SQL Injection (19 rules)
      getpost.yaml              # $_GET, $_POST sources
      cookie.yaml               # $_COOKIE source
      server.yaml               # $_SERVER source
      user_agent.yaml           # User-Agent header
    xss/                        # Cross-Site Scripting (4 rules)
      basic_xss.yaml
      variables_substitutes.yaml
      variables_substitutes_1.yaml
      filtered_add_query_arg.yaml
    ssrf/                       # Server-Side Request Forgery (5 rules)
      ssrf_test.yaml
      ssrf_new.yaml
    rce/                        # Remote Code Execution (1 rule)
      rce_basic-fileGetContents-eval.yaml
    deserialisation/            # Insecure Deserialization (1 rule)
      deserialize.yaml

  coding-standards/             # Ported from WPCS/VIPCS (87 rules)
    security/                   # 22 rules - escaping, validation, nonce, templates
      output-escaping.yaml
      input-validation.yaml
      nonce-verification.yaml
      safe-redirect.yaml
      template-injection.yaml
      filter-functions.yaml
    database/                   # 7 rules - prepared SQL, restricted DB
      prepared-sql.yaml
      restricted-db.yaml
    file-inclusion/             # 3 rules - dynamic includes
      dynamic-include.yaml
    php-security/               # 19 rules - dangerous functions
      restricted-functions.yaml
      dangerous-functions.yaml
      development-functions.yaml
      posix-functions.yaml
    wordpress-security/         # 13 rules - WP-specific security
      global-override.yaml
      deprecated.yaml
      capabilities.yaml
      enqueued-resources.yaml
      menu-slug.yaml
    vip-security/               # 21 rules - VIP platform restrictions
      escaping-functions.yaml
      exit-after-redirect.yaml
      strip-tags.yaml
      dynamic-calls.yaml
      restricted-functions.yaml
      restricted-classes.yaml
      restricted-hooks.yaml
      restricted-variables.yaml
      str-replace-sanitization.yaml
    dos/                        # 5 rules - DoS prevention
      performance-dos.yaml
```

## Rule ID Convention

All rule IDs follow: `codevigilant.php.<namespace>.<category>.<source>.<pattern-type>`

| Namespace | Description |
|----------|-------------|
| `wordpress` | Original CodeVigilant rules |
| `coding-standards` | Ported from WPCS/VIPCS |

| Category | Namespace |
|----------|-----------|
| SQL Injection | `sqli` |
| XSS | `xss` |
| SSRF | `ssrf` |
| RCE | `rce` |
| Deserialization | `insecure_deserialization` |

## Contributing

When contributing rules:
1. Document the source in the rule's metadata (if ported from another tool)
2. Include a reference link to the original source
3. Respect the original license terms
4. Only submit **security** rules (not coding style)

## License Notice

- **Original CodeVigilant rules**: MIT License
- **Rules ported from WPCS**: Respect MIT license terms
- **Rules ported from VIPCS**: Respect GPL-2.0-or-later license terms
