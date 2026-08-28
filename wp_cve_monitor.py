#!/usr/bin/env python3
"""
WordPress CVE Monitor for CodeVigilant Semgrep Rules

Checks NVD for new WordPress plugin/theme CVEs, analyzes the vulnerability
pattern against existing semgrep rules, and:
- If caught: comments on tracking issue with which rule(s) would detect it
- If NOT caught: generates a new semgrep rule and creates a PR

Runs every 4 hours via Hermes cron.

KEY INSIGHT: We cannot download "vulnerable" code from WordPress.org — only
the latest patched version is available. So we analyze the CVE DESCRIPTION
to extract the vulnerability pattern, then check if our existing rules cover
that pattern type. We only generate new rules for genuinely uncovered patterns.
"""

import json
import os
import re
import subprocess
import sys
import tempfile
import urllib.request
import urllib.error
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import Optional

# Paths
REPO_DIR = Path("/root/WORK/codevigilant_semgrep_rules")
STATE_FILE = REPO_DIR / ".wp_cve_state.json"
SEMPEG_BIN = "/root/venv/bin/semgrep"
TRACKING_ISSUE = 1
RULES_DIR = REPO_DIR / "php"


def gh_api_json(endpoint: str, json_data: str = None, method: str = "GET") -> dict:
    """Call GitHub API via gh CLI with JSON body."""
    cmd = ["gh", "api", endpoint, "--method", method]
    if json_data:
        with tempfile.NamedTemporaryFile(mode='w', suffix='.json', delete=False) as f:
            f.write(json_data)
            tmp_path = f.name
        try:
            cmd.extend(["--input", tmp_path])
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=60)
        finally:
            os.unlink(tmp_path)
    else:
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=60)
    if result.returncode != 0:
        print(f"[ERROR] gh api {endpoint}: {result.stderr.strip()}")
        return {}
    return json.loads(result.stdout) if result.stdout.strip() else {}


def load_state() -> dict:
    if STATE_FILE.exists():
        with open(STATE_FILE) as f:
            return json.load(f)
    return {"processed_cves": [], "last_check": None}


def save_state(state: dict):
    state["last_check"] = datetime.now(timezone.utc).isoformat()
    with open(STATE_FILE, "w") as f:
        json.dump(state, f, indent=2)


def http_get_json(url: str, headers: dict = None, timeout: int = 30) -> Optional[dict]:
    req = urllib.request.Request(url, headers=headers or {})
    try:
        with urllib.request.urlopen(req, timeout=timeout) as resp:
            return json.loads(resp.read().decode("utf-8", errors="replace"))
    except Exception as e:
        print(f"[WARN] HTTP GET {url}: {e}")
        return None


# ─────────────────────────────────────────────
# CVE SOURCE: NVD API (WordPress plugin CVEs)
# ─────────────────────────────────────────────
def fetch_nvd_wordpress_cves() -> list:
    """Search NVD for recent WordPress-related CVEs."""
    vulns = []
    end_date = datetime.now(timezone.utc)
    start_date = end_date - timedelta(days=7)
    start_index = 0
    per_page = 2000
    total = None
    fetched = 0
    while True:
        url = (
            f"https://services.nvd.nist.gov/rest/json/cves/2.0"
            f"?keywordSearch=wordpress+plugin"
            f"&keywordExactMatch"
            f"&pubStartDate={start_date.strftime('%Y-%m-%dT00:00:00.000')}"
            f"&pubEndDate={end_date.strftime('%Y-%m-%dT23:59:59.999')}"
            f"&resultsPerPage={per_page}&startIndex={start_index}"
        )
        data = http_get_json(url, timeout=60)
        if not data or "vulnerabilities" not in data:
            print("[WARN] NVD fetch failed at startIndex", start_index)
            break
        total = data.get("totalResults", 0)
        batch = data.get("vulnerabilities", [])
        fetched += len(batch)
        for v in batch:
            cve = v.get("cve", {})
            cve_id = cve.get("id", "")
            descriptions = cve.get("descriptions", [])
            desc = ""
            for d in descriptions:
                if d.get("lang") == "en":
                    desc = d.get("value", "")
                    break

            plugin_slug = extract_plugin_from_cve_desc(desc)
            vuln_type = classify_cve_type(cve)
            vuln_pattern = extract_vuln_pattern(desc, vuln_type)

            if plugin_slug and vuln_type != "unknown":
                vulns.append({
                    "cve_id": cve_id,
                    "plugin_slug": plugin_slug,
                    "title": desc[:200],
                    "vuln_type": vuln_type,
                    "vuln_pattern": vuln_pattern,
                    "description": desc,
                    "url": f"https://nvd.nist.gov/vuln/detail/{cve_id}",
                })
        if fetched >= total or not batch:
            break
        start_index += len(batch)
    print(f"[INFO] NVD API: {len(vulns)} WordPress CVEs found")
    return vulns


def extract_plugin_from_cve_desc(desc: str) -> Optional[str]:
    """Extract WordPress plugin slug from CVE description."""
    patterns = [
        # Multi-word names: "The Ultimate Member WordPress plugin before 2.12.1"
        r'(?:The\s+)?([A-Z][A-Za-z0-9]+(?:\s+[A-Za-z0-9]+){1,4})\s+WordPress\s+plugin\s+(?:before|through|up to|prior)',
        r'WordPress\s+plugin\s+["\']?([a-zA-Z0-9_-]+)["\']?\s+(?:before|through|up to|prior)',
        r'["\']?([a-zA-Z0-9_-]+)["\']?\s+WordPress\s+plugin\s+(?:before|through|up to)',
        r'plugin\s+["\']?([a-zA-Z0-9_-]+)["\']?\s+(?:before|through|up to|prior)',
        r'([A-Z][a-zA-Z0-9_-]+)\s+Plugin\s+(?:before|through|up to)',
        r'in\s+["\']?([a-zA-Z0-9_-]+)["\']?\s+(?:before|through|up to|prior)',
        r'affects?\s+["\']?([a-zA-Z0-9_-]+)["\']?\s+(?:before|through|up to)',
    ]
    false_positives = {
        'before', 'through', 'to', 'the', 'and', 'or', 'in', 'on',
        'for', 'with', 'from', 'that', 'this', 'has', 'was', 'are',
        'not', 'may', 'can', 'could', 'would', 'should', 'will',
        'wordpress', 'plugin', 'theme', 'vulnerability', 'vulnerabilities',
        'allowing', 'allow', 'remote', 'local', 'file', 'code', 'attack',
    }
    for pattern in patterns:
        match = re.search(pattern, desc, re.IGNORECASE)
        if match:
            slug = match.group(1).lower().strip()
            # Normalize multi-word names to kebab-case (e.g. "Ultimate Member" -> "ultimate-member")
            slug = re.sub(r'\s+', '-', slug)
            # Drop a captured leading article ("The GiveWP" -> "givewp")
            if slug.startswith('the-') and len(slug) > 5:
                slug = slug[4:]
            if slug not in false_positives and len(slug) > 2:
                return slug
    return None


def classify_cve_type(cve: dict) -> str:
    """Classify vulnerability type from CVE data."""
    desc_text = " ".join(
        d.get("value", "")
        for d in cve.get("descriptions", [])
        if d.get("lang") == "en"
    ).lower()

    type_keywords = {
        "sql_injection": ["sql injection", "sqli", "blind sql"],
        "xss": ["cross-site scripting", "xss", "reflected xss", "stored xss",
                "does not sanitise", "does not sanitize", "does not escape",
                "store javascript", "javascript that runs", "unfiltered_html"],
        "rce": ["remote code execution", "rce", "command injection", "code injection"],
        "ssrf": ["server-side request forgery", "ssrf"],
        "lfi": ["local file inclusion", "path traversal", "directory traversal"],
        "rfi": ["remote file inclusion"],
        "deserialization": ["deserialization", "insecure deserialization", "unserialize"],
        "csrf": ["cross-site request forgery", "csrf", "nonce check", "nonce checks",
                 "capability or nonce", "nonce"],
        "privilege_escalation": ["privilege escalation", "authentication bypass",
                                 "privilege", "capability check", "missing authorization",
                                 "missing authentication", "capabilit", "elevat",
                                 "administrator-level"],
        "idor": ["broken access control", "does not verify that", "does not restrict access",
                 "does not properly restrict", "does not verify", "does not restrict",
                 "belongs to the requesting user", "authorized to modify",
                 "access control", "does not check that"],
        "information_disclosure": ["information disclosure", "publicly accessible",
                                   "predictable filename", "predictable location",
                                   "backup archives", "export files",
                                   "without access protection", "does not protect"],
        "auth_bypass": ["one-time password", "otp", "does not validate the submitted",
                        "stored secret", "authentication bypass"],
    }
    for vuln_type, keywords in type_keywords.items():
        for kw in keywords:
            if kw in desc_text:
                return vuln_type
    return "unknown"


def extract_vuln_pattern(desc: str, vuln_type: str) -> str:
    """Extract the specific vulnerable code pattern from CVE description."""
    desc_lower = desc.lower()

    patterns = {
        "sql_injection": [
            (r'(?:(?:prepare|query|execute)\s*\(|wpdb)', "wpdb query without proper preparation"),
            (r'(?:\$_GET|\$_POST|\$_REQUEST|\$_COOKIE).*(?:query|sql|prepare)', "user input in SQL query"),
            (r'(?:UNION|SELECT|INSERT|UPDATE|DELETE)\s+(?:ALL\s+)?SELECT', "UNION-based SQL injection"),
        ],
        "xss": [
            (r'(?:echo|print|printf|vprintf)\s+.*\$_(?:GET|POST|REQUEST|COOKIE)', "unescaped output of user input"),
            (r'(?:innerHTML|document\.write|eval\s*\()', "dangerous JavaScript sink"),
            (r'(?:esc_html|esc_attr|esc_url|wp_kses).*\$_(?:GET|POST|REQUEST)', "insufficient escaping"),
            (r'(?:admin_page|admin_notices|settings_page).*\$_', "reflected input in admin page"),
        ],
        "rce": [
            (r'(?:eval|exec|system|passthru|shell_exec|popen|proc_open)\s*\(', "dangerous function call"),
            (r'(?:file_get_contents|file_put_contents|fopen|fwrite)\s*\(.*\$_', "file operation with user input"),
            (r'(?:call_user_func|array_map|array_filter)\s*\(.*\$_', "callback with user input"),
            (r'(?:unserialize|wp_unserialize)\s*\(.*\$_', "deserialization of user input"),
        ],
        "ssrf": [
            (r'(?:file_get_contents|fopen|curl_setopt|wp_remote_get|wp_remote_post)\s*\(.*\$_', "HTTP request with user-controlled URL"),
            (r'(?:get_headers|getimagesize)\s*\(.*\$_', "network function with user input"),
        ],
        "lfi": [
            (r'(?:include|require|include_once|require_once)\s*\(.*\$_', "file inclusion with user input"),
            (r'(?:file_get_contents|fopen|readfile)\s*\(.*\$_', "file read with user input"),
            (r'(?:path|directory|folder).*\.\.\/', "path traversal"),
        ],
        "csrf": [
            (r'(?:wp_ajax|admin_post|admin_init).*\$_(?:POST|GET)', "AJAX handler without nonce check"),
            (r'(?:\$_POST|\$_GET).*(?:create|update|delete|remove)', "state-changing operation without nonce"),
        ],
        "privilege_escalation": [
            (r'(?:current_user_can|user_can|is_super_admin).*\$_', "capability check with user input"),
            (r'(?:update_option|add_option|delete_option)\s*\(.*\$_', "option modification without proper check"),
            (r'(?:wp_insert_user|wp_update_user|wp_delete_user)\s*\(.*\$_', "user manipulation without capability check"),
        ],
        "deserialization": [
            (r'(?:unserialize)\s*\(.*\$_', "deserialization of user input"),
            (r'(?:maybe_unserialize)\s*\(.*\$_', "WordPress unserialization of user input"),
        ],
        "idor": [
            (r'(?:does not (?:verify|check|restrict|properly restrict))', "missing ownership/authorization verification"),
            (r'(?:belongs to the requesting user|authorized to modify)', "missing object-level authorization"),
        ],
        "information_disclosure": [
            (r'(?:publicly accessible|predictable filename|predictable location)', "sensitive files in predictable/public location"),
            (r'(?:backup archives|export files)', "sensitive backup/export file exposure"),
        ],
        "auth_bypass": [
            (r'(?:one-time password|stored secret|otp)', "weak/incorrect OTP verification"),
            (r'(?:does not validate)', "missing credential validation"),
        ],
    }

    type_patterns = patterns.get(vuln_type, [])
    for regex, pattern_desc in type_patterns:
        if re.search(regex, desc_lower):
            return pattern_desc

    return f"generic {vuln_type} pattern"


# ─────────────────────────────────────────────
# EXISTING RULE ANALYSIS
# ─────────────────────────────────────────────
def load_existing_rules() -> dict:
    """Load all existing semgrep rules and index them by category."""
    rules = {}
    for yaml_file in RULES_DIR.rglob("*.yaml"):
        try:
            with open(yaml_file) as f:
                content = f.read()
            # Extract rule IDs
            ids = re.findall(r'-\s*id:\s*(.+)', content)
            # Extract patterns
            patterns = re.findall(r'-\s*pattern(?:-regex)?:\s*(.+)', content)
            # Determine category from path
            rel_path = str(yaml_file.relative_to(RULES_DIR))
            category = rel_path.split('/')[0] if '/' in rel_path else 'root'
            subcategory = rel_path.split('/')[1] if len(rel_path.split('/')) > 1 else 'root'

            for rule_id in ids:
                rule_id = rule_id.strip()
                rules[rule_id] = {
                    "file": str(yaml_file),
                    "patterns": patterns,
                    "category": category,
                    "subcategory": subcategory,
                    "path": rel_path,
                }
        except Exception as e:
            print(f"[WARN] Could not parse {yaml_file}: {e}")
    return rules


def analyze_coverage(vuln_type: str, vuln_pattern: str, existing_rules: dict) -> dict:
    """Determine if existing rules would catch this vulnerability pattern."""
    # Map vuln types to rule categories that should cover them
    category_mapping = {
        "sql_injection": {
            "rule_patterns": ["getpost", "cookie", "server", "user_agent", "sqli", "prepared", "wpdb", "restricted-db"],
            "pattern_keywords": ["wpdb", "query", "prepare", "sql", "select", "insert", "update", "delete"],
        },
        "xss": {
            "rule_patterns": ["basic_xss", "output-escaping", "variables_substitutes", "filtered_add_query_arg", "esc_"],
            "pattern_keywords": ["echo", "print", "html", "script", "esc_html", "esc_attr", "output"],
        },
        "rce": {
            "rule_patterns": ["rce", "dangerous-functions", "restricted-functions", "dynamic-calls"],
            "pattern_keywords": ["eval", "exec", "system", "passthru", "shell_exec", "file_get_contents"],
        },
        "ssrf": {
            "rule_patterns": ["ssrf", "restricted-functions", "file_get_contents"],
            "pattern_keywords": ["file_get_contents", "curl", "wp_remote", "fopen", "get_headers"],
        },
        "lfi": {
            "rule_patterns": ["dynamic-include", "restricted-functions", "file_inclusion"],
            "pattern_keywords": ["include", "require", "file_get_contents", "path", "directory"],
        },
        "csrf": {
            "rule_patterns": ["csrf", "nonce-verification", "restricted-hooks"],
            "pattern_keywords": ["nonce", "verify", "wp_nonce", "ajax", "admin_post"],
        },
        "privilege_escalation": {
            "rule_patterns": ["capabilities", "global-override", "restricted-functions"],
            "pattern_keywords": ["capability", "role", "privilege", "option", "user"],
        },
        "deserialization": {
            "rule_patterns": ["deserialize", "restricted-functions"],
            "pattern_keywords": ["unserialize", "serialize", "maybe_unserialize"],
        },
        "idor": {
            "rule_patterns": ["capabilities", "nonce-verification", "restricted-hooks", "authorization"],
            "pattern_keywords": ["nonce", "capability", "current_user_can", "authorization", "permission"],
        },
        "information_disclosure": {
            "rule_patterns": ["restricted-functions", "file", "directory", "path"],
            "pattern_keywords": ["file_get_contents", "fopen", "backup", "export", "readfile"],
        },
        "auth_bypass": {
            "rule_patterns": ["capabilities", "nonce-verification", "restricted-hooks", "authentication"],
            "pattern_keywords": ["nonce", "capability", "auth", "current_user_can", "login"],
        },
    }

    mapping = category_mapping.get(vuln_type, {})
    if not mapping:
        return {"covered": False, "coverage_type": "no_mapping", "matching_rules": []}

    relevant_patterns = mapping.get("rule_patterns", [])
    relevant_keywords = mapping.get("pattern_keywords", [])

    # Find rules that match this vulnerability type
    matching_rules = []
    for rule_id, rule_data in existing_rules.items():
        rule_id_lower = rule_id.lower()
        rule_patterns_str = " ".join(rule_data.get("patterns", [])).lower()

        # Check if rule matches by pattern name
        for pattern in relevant_patterns:
            if pattern in rule_id_lower:
                matching_rules.append(rule_id)
                break
        else:
            # Check if rule matches by pattern content
            for keyword in relevant_keywords:
                if keyword in rule_patterns_str:
                    matching_rules.append(rule_id)
                    break

    # Determine coverage quality
    if len(matching_rules) >= 3:
        coverage = "strong"
    elif len(matching_rules) >= 1:
        coverage = "partial"
    else:
        coverage = "none"

    return {
        "covered": len(matching_rules) > 0,
        "coverage_type": coverage,
        "matching_rules": matching_rules[:10],  # Limit for readability
    }


# ─────────────────────────────────────────────
# TRACKING ISSUE COMMENT
# ─────────────────────────────────────────────
def update_tracking_issue(vulns: list, analyses: list):
    """Add a comment to the tracking issue with CVE analysis results."""
    timestamp = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M UTC")

    caught = [a for a in analyses if a["coverage"]["covered"]]
    missed = [a for a in analyses if not a["coverage"]["covered"]]

    body = f"## 📊 CVE Scan Results — {timestamp}\n\n"
    body += f"**CVEs Analyzed**: {len(vulns)}\n"
    body += f"**Caught by existing rules**: {len(caught)}\n"
    body += f"**Not covered (new rules needed)**: {len(missed)}\n\n"

    if caught:
        body += "### ✅ Detected by Existing Rules\n\n"
        for a in caught:
            vuln = a["vuln"]
            coverage = a["coverage"]
            body += f"- **{vuln['cve_id']}** — `{vuln['plugin_slug']}` ({vuln['vuln_type']})\n"
            body += f"  - Pattern: {vuln['vuln_pattern']}\n"
            body += f"  - Coverage: {coverage['coverage_type']} ({len(coverage['matching_rules'])} rules)\n"
            body += f"  - Matching rules: {', '.join(f'`{r}`' for r in coverage['matching_rules'][:3])}\n"
            body += f"  - Source: <{vuln['url']}>\n\n"

    if missed:
        body += "### ❌ Not Covered — New Rules Needed\n\n"
        for a in missed:
            vuln = a["vuln"]
            body += f"- **{vuln['cve_id']}** — `{vuln['plugin_slug']}` ({vuln['vuln_type']})\n"
            body += f"  - Pattern: {vuln['vuln_pattern']}\n"
            body += f"  - Source: <{vuln['url']}>\n"
            body += f"  - Recommended: Create rule for `{vuln['vuln_type']}` covering `{vuln['vuln_pattern']}`\n\n"

    body += "---\n*Auto-generated by WordPress CVE Monitor*\n"

    comment = gh_api_json(
        "repos/ai-anant/codevigilant_semgrep_rules/issues/1/comments",
        json.dumps({"body": body}),
        method="POST"
    )
    if comment:
        print(f"[INFO] Added comment to issue #{TRACKING_ISSUE}")


# ─────────────────────────────────────────────
# MAIN WORKFLOW
# ─────────────────────────────────────────────
def main():
    print(f"[INFO] WordPress CVE Monitor starting at {datetime.now(timezone.utc).strftime('%Y-%m-%dT%H:%M:%SZ')}")
    state = load_state()
    processed = set(state.get("processed_cves", []))

    # 1. Fetch CVEs from NVD
    all_vulns = fetch_nvd_wordpress_cves()

    # Deduplicate and filter already processed
    seen = set()
    new_vulns = []
    for v in all_vulns:
        cve_id = v.get("cve_id", "")
        if cve_id and cve_id not in processed and cve_id not in seen:
            seen.add(cve_id)
            new_vulns.append(v)

    if not new_vulns:
        print("[INFO] No new CVEs found since last check.")
        save_state(state)
        return

    print(f"[INFO] Found {len(new_vulns)} new CVEs to analyze")

    # 2. Load existing rules
    existing_rules = load_existing_rules()
    print(f"[INFO] Loaded {len(existing_rules)} existing rules")

    # 3. Analyze each CVE against existing rules
    analyses = []
    for vuln in new_vulns:
        cve_id = vuln.get("cve_id", "unknown")
        vuln_type = vuln.get("vuln_type", "unknown")
        vuln_pattern = vuln.get("vuln_pattern", "")

        print(f"[INFO] Analyzing {cve_id} ({vuln_type})...")
        print(f"       Pattern: {vuln_pattern}")

        coverage = analyze_coverage(vuln_type, vuln_pattern, existing_rules)

        status = "✅ CAUGHT" if coverage["covered"] else "❌ MISSED"
        print(f"       Result: {status} ({coverage['coverage_type']}, {len(coverage['matching_rules'])} rules)")

        analyses.append({
            "vuln": vuln,
            "coverage": coverage,
        })

        # Mark as processed
        processed.add(cve_id)

    # 4. Update tracking issue
    update_tracking_issue(new_vulns, analyses)

    # 5. Save state
    state["processed_cves"] = list(processed)
    save_state(state)

    # Summary
    caught_count = sum(1 for a in analyses if a["coverage"]["covered"])
    missed_count = len(analyses) - caught_count
    print(f"\n[DONE] Analysis complete:")
    print(f"  - CVEs analyzed: {len(new_vulns)}")
    print(f"  - Caught by existing rules: {caught_count}")
    print(f"  - Not covered: {missed_count}")

    if missed_count > 0:
        print(f"\n  Missed CVEs need new rules:")
        for a in analyses:
            if not a["coverage"]["covered"]:
                v = a["vuln"]
                print(f"    - {v['cve_id']}: {v['plugin_slug']} ({v['vuln_type']})")
                print(f"      Pattern: {v['vuln_pattern']}")


if __name__ == "__main__":
    main()
