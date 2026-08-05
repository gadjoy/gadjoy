---
description: "Implementation plan for CI-enforced test gates"
---

# Implementation Plan: CI-Enforced Test Gates

**Branch**: `chore/test-gates` | **Date**: 2026-08-05 | **Spec**: `./spec.md`

## Summary

Add the missing layer of the pyramid. The existing suite asserts over Markdown source; this
adds assertions over **built output** and over the **live site**, then makes CI enforce all of
it on pull requests so the gate stops depending on memory.

## Technical Context

**Language**: Python 3.13 + pytest (existing suite), bash (smoke test)

**New dependencies**: none. `PyYAML` (already required) reads `hugo.yaml`; output parsing uses
stdlib `re`. Deliberately no browser driver — see spec Out of Scope.

**Testing**: `migration/tests/test_site_output.py` (new, `build`-marked),
`migration/tests/test_toolchain.py` (new), `scripts/smoke.sh` (new, post-deploy)

**Constraints**: must run offline; must behave identically on macOS and Linux; must not POST
to the live contact form.

## Constitution Check (v2.0.0)

- **I / VII. Test-First**: PASS — each guard was confirmed RED against its reintroduced bug
  before being trusted (see Validation below).
- **VIII. Gates in CI**: PASS — this feature *is* that principle's implementation.
- **IX. Verify deployed site**: PASS — `scripts/smoke.sh`, wired as a post-deploy job.
- **X. Claims trace to source**: PASS — `test_repair_claim_matches_config` enforces it.
- **III. No fabricated data**: PASS — the Redmi-4 fix points references at the real filenames
  on disk; no content invented, nothing renamed.

No violations → Complexity Tracking empty.

## Approach

### 1. Fix the red suite first
Two failures on `main`, one root cause: `/2021/12/17/redmi-4-dead-condition/index.md`
referenced `redmi-4-*.webp` while the files are `Redmi-4-*.webp`. Correct the three references
(banner + two body images) to match disk. Confirmed 404 → 200 on the live URLs.

Then make the check platform-proof: `conftest.media_ref_resolves()` compares against the real
directory listing instead of `Path.exists()`, which is case-insensitive on macOS. Without this
the guard silently weakens on the machine the site is developed on — which is exactly how the
bug shipped.

### 2. Build-output assertions (`test_site_output.py`)
A session-scoped `built_site` fixture builds once (~30s) without `GOOGLE_PLACES_API_KEY`, so
the reviews fallback path — where a stale hardcoded claim would hide — is what gets asserted.
Five tests, four of them descended from a named incident:

| Test | Guards against |
|---|---|
| `test_contact_page_uses_project_layout` | #6/#7 layout fallback |
| `test_contact_form_endpoint_is_live` | #10/#11 dead form endpoint / unwired key |
| `test_repair_claim_matches_config` | #13 untraceable public claim |
| `test_internal_refs_resolve` | the Redmi-4 404s, and any broken link/asset |
| `test_no_template_leakage` | unrendered `{{` or Hugo's `ZgotmplZ` marker |

### 3. Toolchain pin (`test_toolchain.py`)
`.hugo-version` becomes the single source; CI reads it into `$GITHUB_ENV`. Tests assert the
workflow does not hardcode a version, hard-fail on a **minor** mismatch (the class that broke
prod) and warn on a **patch** mismatch (currently local 0.147.8 vs pinned 0.147.2).

### 4. Workflow restructure
`test → build → deploy → smoke`, with `pull_request` added as a trigger and the three deploy
jobs opting out of PR events. `test` is a separate job so PRs get the full gate without
touching Pages. Checkout now precedes the Hugo install, so the pin can be read.

### 5. Smoke test (`scripts/smoke.sh`)
Retries for edge propagation; checks the six real pages, the bespoke contact markup, the
Web3Forms action, the homepage claim, a migrated post at its original URL, and the two
previously-404ing images by name. Endpoint reachability only — never a POST.

## Validation: mutation testing

A guard that has never failed is a guess. Each was verified by reintroducing its bug:

| Mutation | Result |
|---|---|
| Remove `layouts/_default/contact.html` | ✅ caught |
| Point the hero stat back at the post count | ✅ caught |
| Restore the lowercase image references | ✅ caught |
| Strip the `access_key` input from the form | ✅ caught |

Two guards failed this exercise on the first attempt and were rewritten:

1. **Sampling hid the bug.** The reference check originally sampled 25 posts and the offending
   post was not among them. Now it scans every built page, deduplicating references so it
   stays fast (~5,000 distinct targets, stat-ed once each).
2. **The extractor matched nothing.** Its attribute regex required quotes, but `--minify`
   emits `src=/img/x.webp` unquoted — so it found ~no references and passed a knowingly
   broken image. Fixed to accept all three quoting forms, `<script>`/`<style>` bodies stripped
   to avoid phantom refs, percent-encoded paths decoded (migrated filenames contain en
   dashes), and a `MIN_EXPECTED_REFS` floor added so it can never go vacuous again.

The second is the more important finding: a green test that asserts nothing is worse than no
test, because it reads as coverage. Principle VII now says so explicitly.

## Phases

- **Phase 1**: Fix the red suite; make the media check case-strict. *(done)*
- **Phase 2**: Build-output guards + mutation validation. *(done)*
- **Phase 3**: Toolchain pin + tests. *(done)*
- **Phase 4**: Workflow restructure; smoke script. *(done)*
- **Phase 5**: Constitution v2.0.0; close out `001`'s task list. *(done)*
- **Phase 6 (follow-on, not in this PR)**: visual regression for the #12 class; make the
  `test` job a *required* status check in branch protection — a repo setting, not a file.

## Complexity Tracking

*No constitution violations — section intentionally empty.*
