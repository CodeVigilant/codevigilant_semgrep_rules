#!/usr/bin/env bash
# =============================================================================
# Semgrep Rule Accuracy Test Runner
#
# Validates that:
#   test-pass/  → 0 findings with ALL rules  (no false positives)
#   test-fail/  → exact expected rules fire   (no missing/extra detections)
#
# Usage: ./test/run-tests.sh
# =============================================================================

set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$REPO_DIR"

PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

# ---------------------------------------------------------------------------
# Helper: extract EXPECTED_RULES from PHP file header comments
# ---------------------------------------------------------------------------
extract_expected_rules() {
    local file="$1"
    grep '// EXPECTED_RULES:' -A 200 "$file" \
        | grep '^// codevigilant\.' \
        | sed 's|^// ||' \
        | sort
}

# ---------------------------------------------------------------------------
# Helper: extract actual rule IDs from semgrep JSON output
# ---------------------------------------------------------------------------
extract_actual_rules() {
    python3 -c "
import json, sys
data = json.load(sys.stdin)
rules = sorted(set(
    r['check_id'].split('codevigilant.')[-1]
    for r in data.get('results', [])
))
for r in rules:
    print('codevigilant.' + r)
"
}

# ---------------------------------------------------------------------------
# TEST-PASS: scan with ALL rules should produce 0 findings
# ---------------------------------------------------------------------------
echo ""
echo "=== TEST-PASS: Verifying safe code produces 0 findings (all rules) ==="
echo ""

for test_file in test/test-pass/*.php; do
    basename=$(basename "$test_file")

    count=$(semgrep scan --config php/ "$test_file" --json 2>/dev/null \
        | python3 -c "import json,sys; print(len(json.load(sys.stdin).get('results',[])))")

    if [ "$count" -eq 0 ]; then
        pass "test-pass/${basename} → 0 findings"
    else
        fail "test-pass/${basename} → $count findings (expected 0)"
        semgrep scan --config php/ "$test_file" --json 2>/dev/null \
            | python3 -c "
import json,sys
data = json.load(sys.stdin)
for r in data.get('results',[]):
    print(f'    Line {r[\"start\"][\"line\"]}: {r[\"check_id\"].split(\"codevigilant.\")[-1]}')
"
    fi
done

# ---------------------------------------------------------------------------
# TEST-FAIL: scan with matching config should produce exactly expected rules
# ---------------------------------------------------------------------------
echo ""
echo "=== TEST-FAIL: Verifying vulnerable code triggers specific rules ==="
echo ""

# Map test files to their config directories
declare -A CONFIG_MAP
CONFIG_MAP[wordpress]="php/wordpress/"
CONFIG_MAP[coding-standards]="php/coding-standards/"

for test_file in test/test-fail/*.php; do
    basename=$(basename "$test_file" .php)
    config_dir="${CONFIG_MAP[$basename]:-}"

    if [ -z "$config_dir" ]; then
        echo "  SKIP: test-fail/${basename}.php (no config mapping)"
        continue
    fi

    expected_file=$(mktemp)
    actual_file=$(mktemp)

    # Extract expected rules from file header
    extract_expected_rules "$test_file" > "$expected_file"

    # Run semgrep and extract actual rules
    semgrep scan --config "$config_dir" "$test_file" --json 2>/dev/null \
        | extract_actual_rules > "$actual_file"

    expected_count=$(wc -l < "$expected_file" | tr -d ' ')
    actual_count=$(wc -l < "$actual_file" | tr -d ' ')

    # Compare
    missing=$(comm -23 "$expected_file" "$actual_file")
    extra=$(comm -13 "$expected_file" "$actual_file")

    if [ -z "$missing" ] && [ -z "$extra" ]; then
        pass "test-fail/${basename}.php → $actual_count/$expected_count rules match exactly"
    else
        fail "test-fail/${basename}.php → rules mismatch"
        if [ -n "$missing" ]; then
            echo "    MISSING (expected but not found):"
            echo "$missing" | sed 's/^/      /'
        fi
        if [ -n "$extra" ]; then
            echo "    EXTRA (found but not expected):"
            echo "$extra" | sed 's/^/      /'
        fi
    fi

    rm -f "$expected_file" "$actual_file"
done

# ---------------------------------------------------------------------------
# Summary
# ---------------------------------------------------------------------------
echo ""
echo "==========================================="
TOTAL=$((PASS + FAIL))
echo "Results: $PASS/$TOTAL passed, $FAIL failed"
echo "==========================================="

if [ "$FAIL" -gt 0 ]; then
    exit 1
fi
