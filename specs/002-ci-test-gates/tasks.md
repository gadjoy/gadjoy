---
description: "Task list for CI-enforced test gates"
---

# Tasks: CI-Enforced Test Gates

> **Status: COMPLETE — 2026-08-05.** Kept current as work landed, per Constitution v2.0.0.
> Verification at completion: `pytest` **32 passed** (was 2 failed / 23 passed), all four
> mutations caught, `scripts/smoke.sh` 12/13 against production — the one failure being the
> real, still-unmerged repair-claim fix from PR #13.

**Input**: `specs/002-ci-test-gates/{spec,plan}.md` | **Constitution**: v2.0.0

---

## Phase 1: Make the existing gate honest

- [x] T001 Reproduce the failure: run `pytest` on `main` → confirm RED
      (2 failed / 23 passed; `test_card_banner_present_and_resolves`,
      `test_all_media_resolve_on_disk`).
- [x] T002 Diagnose: `/2021/12/17/redmi-4-dead-condition/index.md` references
      `redmi-4-*.webp`; files on disk are `Redmi-4-*.webp`. Confirm live impact —
      lowercase URLs 404, capitalised 200 on `gadjoy.in`.
- [x] T003 Fix the three references (banner + two body images) to match disk exactly.
- [x] T004 [P] Add `conftest.media_ref_resolves()` comparing against the real directory
      listing, so the check cannot pass on a case-insensitive filesystem; point both
      failing tests at it.
- [x] T005 Run `pytest` → GREEN (25 passed).

## Phase 2: Build-output guards (US1)

- [x] T006 Add session-scoped `built_site` fixture (one build, no
      `GOOGLE_PLACES_API_KEY`, so the reviews fallback path is what is asserted).
- [x] T007 [P] `test_contact_page_uses_project_layout` — guards #6/#7.
- [x] T008 [P] `test_contact_form_endpoint_is_live` — guards #10/#11.
- [x] T009 [P] `test_repair_claim_matches_config` — guards #13; asserts both that the
      configured total is shown and that the post count is not.
- [x] T010 [P] `test_internal_refs_resolve` — guards the Redmi-4 class.
- [x] T011 [P] `test_no_template_leakage` — unrendered `{{`, `ZgotmplZ`.
- [x] T012 **Mutation-test every guard** (Principle VII): reintroduce each bug, confirm the
      test fails. First run: 2 of 4 caught.
- [x] T013 Fix guard #1 — replace the 25-post sample with a full scan over every built page,
      deduplicating references (the sample had missed the real bug).
- [x] T014 Fix guard #2 — attribute extraction required quotes, but `--minify` emits them
      unquoted, so it matched nothing and passed vacuously. Accept all quoting forms; strip
      `<script>`/`<style>`; decode percent-encoded paths; add a `MIN_EXPECTED_REFS` floor.
- [x] T015 Re-run mutations → **4 of 4 caught**.

## Phase 3: Toolchain parity (US3)

- [x] T016 Add `.hugo-version` (`0.147.2`) as the single source of truth.
- [x] T017 `test_toolchain.py`: workflow must read the pin and must not hardcode a version;
      hard-fail on minor drift, warn on patch drift.
- [x] T018 Confirm the warning path works (local 0.147.8 vs pinned 0.147.2 → 1 warning, pass).

## Phase 4: CI + post-deploy (US1, US2)

- [x] T019 Restructure `.github/workflows/hugo.yml` to `test → build → deploy → smoke`;
      move Checkout ahead of the Hugo install so the pin can be read.
- [x] T020 Add `pull_request` trigger; gate `build`/`deploy`/`smoke` on
      `github.event_name != 'pull_request'` so PRs run the suite without touching Pages.
- [x] T021 Add the `test` job: Python 3.13 → `pip install -r migration/requirements.txt` →
      `pytest`. Deploy is now unreachable unless it passes (FR-002).
- [x] T022 [P] Write `scripts/smoke.sh` — pages, bespoke contact markup, form action,
      homepage claim, a migrated post at its original URL, the two previously-404ing images;
      retries for edge propagation; no POST to the live form.
- [x] T023 Validate the workflow YAML parses and the pin-resolution step resolves to `0.147.2`.
- [x] T024 Run the smoke script against production → 12 pass, 1 fail (the unmerged #13 claim),
      exit code 1 confirmed.

## Phase 5: Re-scope the SDD artifacts

- [x] T025 Amend the constitution to **v2.0.0**: retitle from "Migration" to "Website"; retain
      I–VI as content invariants; add VII (every prod bug becomes a test), VIII (gates in CI),
      IX (verify the deployed site), X (claims trace to a source).
- [x] T026 State in the workflow section **when a spec is required and when it is not** — the
      omission that let eleven consecutive PRs ship without one.
- [x] T027 Close out `001`'s task list (0 of 31 → 31 of 31) with a status header; verify T031
      by checking the live certificate (Let's Encrypt, valid to 2026-10-31).
- [x] T028 Write this spec/plan/tasks set.

## Phase 6: Spec backfill + spec-first enforcement

Added after the initial gate work, on the same feature: the constitution now says when a spec
is required, so the next question is what makes that stick, and what to do about the eleven
PRs that already shipped without one.

- [x] T029 Establish the evidence base for backfilling. No prior gadjoy session transcripts
      exist on this machine (the work was done elsewhere — the `Makefile` syncs from a Mac), so
      ground every backfilled requirement in PR bodies, commit history and the shipped code.
      Record that provenance in each spec rather than implying recovered planning notes.
- [x] T030 Backfill `003-visual-identity` (PRs #2/#3/#4), including the deviation that the
      palette went navy+amber → mono B&W → mono light across three commits on one branch while
      the PR description still advertises the palette that was abandoned.
- [x] T031 Backfill `004-live-google-reviews` (PR #2) — external dependency, credential
      handling, degrade-gracefully requirement, and the fallback's own untraceable claim.
- [x] T032 Backfill `005-interactive-service-pages` (PR #5).
- [x] T033 Backfill `006-gallery-lightbox` (PR #5), incl. the IMEI/serial privacy constraint as
      FR-007, unenforced.
- [x] T034 Backfill `007-contact-and-enquiry-delivery` (PRs #5/#6/#7/#10/#11) — four of the
      five production incidents live here.
- [x] T035 Backfill `008-media-optimization` (PR #8), incl. the case-sensitivity deviation that
      shipped the two live 404s.
- [x] T036 Write `specs/README.md`: spec index, a verdict on every merged PR that has no spec,
      and a consolidated Tests Owed register split by cost (cheap / needs-a-browser /
      deliberately-manual / unknown).
- [x] T037 Bring `001` and `002` up to the same standard — frontmatter, and a coverage position
      against every success criterion. (`001` turned out to be 6 of 6 guarded.)
- [x] T038 `test_spec_hygiene.py`: spec dirs complete, frontmatter valid, requirements and
      success criteria present, every criterion carries a coverage position, forward specs keep
      plan+tasks, backfilled specs carry As Built, every spec listed in the index, and no task
      list left with unchecked boxes and no Status/Deferred declaration.
- [x] T039 Mutation-test it (Principle VII applies to this guard too): hollow spec, spec absent
      from the index, forward spec missing `plan.md`, silently stale task list → **4 of 4
      caught**.
- [x] T040 CI step: a `feat/*` PR whose diff touches no `specs/` file fails, with an error
      message naming the exemption list. Placed first in the job so it fails in seconds.
- [x] T041 `.github/pull_request_template.md` — spec link, tests-written-not-just-passing,
      test-first-for-bugs, guards-verified-by-breaking-them, claims-trace-to-source, and an
      explicit "anything left undone" section.

---

## Deferred (deliberately not in this PR)

- [ ] D001 Visual-regression coverage for the #12 class (invisible call button). Needs a
      browser harness; the spec argues that cost is not yet justified.
- [ ] D002 Make the `test` job a **required status check** in branch-protection settings.
      A repo setting, not a file — cannot be committed, needs doing in the GitHub UI.
- [ ] D003 Re-seed `resumefit` from a corrected master résumé `.docx`; it still says
      "1000+ devices per month", which would reintroduce the figure PR #13 fixed.
