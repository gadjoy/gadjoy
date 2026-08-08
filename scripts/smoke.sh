#!/usr/bin/env bash
# Post-deploy smoke test: prove the LIVE site actually works.
#
# The build-output tests (migration/tests/test_site_output.py) check what Hugo
# produced. They cannot check what the world receives — DNS, TLS, the custom domain,
# CDN caching, or a file that exists locally but was never published. Two real bugs
# lived exactly in that gap:
#
#   - the contact form posted to a Formspree ID that 404'd in production
#   - /2021/12/17/redmi-4-dead-condition/ served two 404 images for months
#
# Usage:  ./scripts/smoke.sh [base-url]        (default: https://gadjoy.in)
# Exits non-zero on the first hard failure, after retrying for propagation.

set -uo pipefail

BASE="${1:-https://gadjoy.in}"
BASE="${BASE%/}"
RETRIES="${SMOKE_RETRIES:-5}"
SLEEP="${SMOKE_SLEEP:-15}"

fails=0
pass() { printf '  ok    %s\n' "$1"; }
fail() { printf '  FAIL  %s — %s\n' "$1" "$2"; fails=$((fails + 1)); }

# curl once; echo "<http_code>\t<body>" (body only when we need to grep it)
fetch() { curl -sS -L --max-time 25 -o "$2" -w '%{http_code}' "$1" 2>/dev/null || echo "000"; }

# Retry because a Pages deploy can take a moment to become visible at the edge.
# Only the *last* attempt reports a failure.
check() {
  local label="$1" url="$2" expect="${3:-200}" needle="${4:-}"
  local body code i
  body="$(mktemp)"
  for i in $(seq 1 "$RETRIES"); do
    code="$(fetch "$url" "$body")"
    if [ "$code" = "$expect" ]; then
      if [ -z "$needle" ] || grep -qF -- "$needle" "$body"; then
        pass "$label"
        rm -f "$body"
        return 0
      fi
      # right status, wrong content: retry (stale CDN copy), then fail
      if [ "$i" -eq "$RETRIES" ]; then
        fail "$label" "$url returned $code but did not contain '$needle'"
        rm -f "$body"
        return 1
      fi
    elif [ "$i" -eq "$RETRIES" ]; then
      fail "$label" "$url returned $code, expected $expect"
      rm -f "$body"
      return 1
    fi
    sleep "$SLEEP"
  done
}

echo "Smoke-testing $BASE"

# --- pages that must exist ---------------------------------------------------
check "homepage"          "$BASE/"                       200
check "contact page"      "$BASE/contact/"               200
check "gallery"           "$BASE/gallery/"               200
check "we-repair"         "$BASE/services/we-repair/"    200
check "we-build"          "$BASE/services/we-build/"     200
check "blog index"        "$BASE/blog/"                  200

# --- the bespoke bits that have silently broken before -----------------------
# Contact form must be the project layout with a live Web3Forms target, not the
# theme fallback and not a dead endpoint (PR #6/#7 and PR #10).
check "contact form present"   "$BASE/contact/" 200 "gj-contact-form"
check "contact form endpoint"  "$BASE/contact/" 200 "api.web3forms.com/submit"

# Headline repair figure must be the lifetime total, not the blog-post count (PR #13).
check "homepage repair claim"  "$BASE/"         200 "15,000"

# A migrated post must still be served at its original WordPress URL.
check "migrated post URL"  "$BASE/2021/12/17/redmi-4-dead-condition/" 200

# Regression guard for the exact case-mismatch bug: these two 404'd in production
# while existing on disk under a different case.
check "post image 1" "$BASE/img/uploads/2021/12/Redmi-4-Before-Dead-Condition.webp" 200
check "post image 2" "$BASE/img/uploads/2021/12/Redmi-4-After-Dead-Condition.webp"  200

# --- form delivery reachability ---------------------------------------------
# Deliberately NOT a POST: submitting would deliver junk to a real inbox. Just
# confirm the endpoint's host resolves and terminates TLS.
if curl -sS -o /dev/null --max-time 20 "https://api.web3forms.com/" 2>/dev/null; then
  pass "web3forms endpoint reachable"
else
  fail "web3forms endpoint reachable" "could not reach api.web3forms.com"
fi

echo
if [ "$fails" -gt 0 ]; then
  echo "SMOKE FAILED: $fails check(s) failed against $BASE"
  exit 1
fi
echo "SMOKE PASSED against $BASE"
