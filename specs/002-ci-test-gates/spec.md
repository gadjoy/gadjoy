---
description: "Catch site regressions before deploy: CI-enforced gates, build-output assertions, post-deploy smoke"
---

# Feature Specification: CI-Enforced Test Gates for the Live Site

**Branch**: `chore/test-gates` | **Date**: 2026-08-05 | **Constitution**: v2.0.0

> **Process note.** Written alongside the implementation, not strictly before it. The work
> began as a direct request to fix five named problems, and the diagnosis that produced this
> spec came out of auditing the repo. Recorded honestly rather than backdated, per the
> Governance clause: Principle I's red-green discipline *was* followed for the guards
> themselves (each was verified to fail against the reintroduced bug — see plan.md).

## Problem

The project has a good test suite and a constitution mandating test-first development, and
both are scoped entirely to the finished WordPress → Hugo migration. Nothing guards the
living website. Concretely:

- `.github/workflows/hugo.yml` ran **no tests**. Its steps were: install Hugo → build →
  upload → deploy. A red suite could not block a deploy.
- CI ran **only on push to `main`**, so nothing at all ran before a merge.
- The suite was **already red on `main`** and had been for some time: two images in
  `/2021/12/17/redmi-4-dead-condition/` were referenced in lowercase while the files on disk
  are capitalised. Both 404'd in production. It passed on the author's macOS because
  `Path.exists()` is case-insensitive there.
- Of the 12 PRs merged since the constitution was ratified, **5 fixed things already live**,
  every one found by a human looking at the site.
- The Hugo version was hardcoded in the workflow, having already caused a prod-only bug once
  (#6/#7) when dev and CI diverged.

The common thread: the tests assert over *Markdown source*, but every one of these bugs lived
in the *rendered output* or the *deployed site*, and each returned HTTP 200 while broken.

## User Scenarios

### US1 — A regression cannot reach production (P1)
A contributor opens a PR that breaks the contact layout, the form wiring, a link, or a public
claim. **CI fails on the pull request, before merge**, naming the specific breakage.

### US2 — A broken deploy is detected immediately (P2)
A deploy succeeds but the live site is wrong (missing asset, dead endpoint, stale CDN copy).
**The pipeline fails after deploy**, rather than the problem sitting live until someone notices.

### US3 — Dev and CI cannot drift apart (P3)
A contributor's local Hugo differs from CI's. **The suite fails on a minor-version gap and
warns on a patch gap**, instead of surfacing as a prod-only rendering difference.

## Requirements

- **FR-001** CI MUST run `pytest` and fail the workflow on any failure, on pull requests as
  well as pushes to `main`.
- **FR-002** Deploy MUST be unreachable unless the test job passed.
- **FR-003** The suite MUST run with no external services (captured fixtures only) and MUST
  produce identical results on case-sensitive and case-insensitive filesystems.
- **FR-004** There MUST be a build-output assertion for each bug class already shipped:
  layout fallback, form wiring, unresolvable internal reference, untraceable public claim.
- **FR-005** Each such guard MUST be verified to FAIL when its bug is deliberately reintroduced.
- **FR-006** Every deploy MUST be followed by a smoke test against the live origin, which MUST
  NOT submit the contact form or otherwise send real traffic to third parties.
- **FR-007** The Hugo version MUST exist in exactly one file, read by CI.

## Success Criteria

- **SC-001** `pytest` is green on `main` (it was not: 2 failed / 23 passed).
- **SC-002** All four regression guards demonstrably fail when their bug is reintroduced.
- **SC-003** The internal-reference check covers **every** built page, not a sample, and
  self-fails if reference extraction stops finding references.
- **SC-004** The two production 404s are fixed and guarded.
- **SC-005** A PR with a broken contact layout fails CI before merge.

## Out of Scope

- Browser/JS end-to-end testing (no Playwright): the shipped bugs were markup, wiring and
  asset problems, all reachable by parsing output. Visual regressions (the invisible call
  button, #12) remain human-caught — noted, not solved.
- Coverage measurement. Line coverage of a Hugo site is close to meaningless; these are
  behavioural guards.
- Rollback automation. Smoke failure alerts; it does not revert.
