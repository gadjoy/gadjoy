---
description: "Task list for deck-to-posts publishing"
---

# Tasks: Weekly Repair Deck → Published Posts

> **Status: Phases A–C COMPLETE; publisher + workflow written but NOT yet rehearsed.**
> T021/T022/T025 need the private intake repo and its token — owner actions, so they are
> unticked rather than assumed. Verified: `pytest` 132 passed (was 98); deck mutations 4/4
> caught; dry run against the reference deck produces 4 posts and 5 reasoned skips.

**Input**: `specs/009-deck-to-posts/{spec,plan}.md` | **Constitution**: v2.0.0

---

## Phase A: Unblock growth (blocking prerequisite)

- [x] T001 Confirm RED: add a throwaway post and watch `test_post_count_matches_source` and
      `test_url_preservation_via_aliases` fail. Proves the blocker is real before touching it.
- [x] T002 Add an `is_migrated()` helper to `conftest.py` — a post is migrated iff it has no
      `origin` key.
- [x] T003 Re-scope `test_post_count_matches_source`: migrated `== 1508` **and** total `>= 1508`.
- [x] T004 Re-scope `test_url_preservation_via_aliases` to migrated posts only (FR-018).
- [x] T005 Prove the loosened gate still bites: a test asserting the migrated-count check fails if
      a migrated post disappears, and that an `origin: deck` post is not exempt from the other
      invariants (taxonomy, banner, artifacts).

## Phase B: Parser (US1, US4)

- [x] T006 [P] Golden fixture test: reference deck → exactly 4 repairs, 5 skips with reasons
      (SC-001). Confirm RED.
- [x] T007 [P] Test before/after by x-position, asserting **slide 7** specifically, where filename
      order is inverted (SC-002).
- [x] T008 [P] Test title = topmost shape; both captions verbatim; footer furniture stripped
      (SC-003).
- [x] T009 [P] Test a repair-shaped slide with no usable title raises (SC-004).
- [x] T010 [P] Test the video-bearing slide (9) does not crash (SC-005).
- [x] T011 Implement `parse_deck()` in `tools/deck_to_posts.py` until B is GREEN.

## Phase C: Generation + privacy interlock (US2, US3)

- [x] T012 [P] Test slug derivation + dedupe against all 1,508 existing slugs (SC-010).
- [x] T013 [P] Test categories emit only existing slugs; unmapped → `[repair]` (FR-013).
- [x] T014 [P] Test post date comes from the issue, never the slide (FR-009).
- [x] T015 [P] Test idempotence: same deck twice → no second post (SC-007).
- [x] T016 [P] Test the PII interlock redacts an About-screen image before it is written (SC-008).
- [x] T017 Implement `build_post()` + writer until C is GREEN.
- [x] T018 **Mutation-test the interlock**: disable it and confirm the test fails. A guard never
      seen to fail is a guess.
- [x] T019 Integration: generated posts pass the full existing suite + `hugo --minify` + the
      build-output tests (SC-006).
- [x] T020 Wire manifest regeneration into the publish workflow, with a re-scan of the written
      images that fails the run rather than opening a PR if any identifier survived. (Not
      exercised against real output yet — the dry run writes nothing, so this is verified by
      inspection until T025.)

## Phase D: Publisher + workflow (deferred — needs the intake repo)

- [ ] T021 Create the private intake repo and its `deck.yml` Issue Form.
- [ ] T022 Create the `INTAKE_TOKEN` fine-grained PAT (read-only: issues + contents).
- [x] T023 `tools/publish_decks.py` — issue discovery, deck download, PR creation, issue
      comment/close.
- [x] T024 `.github/workflows/publish-decks.yml` — `workflow_dispatch`, branch `content/deck-<week>`
      (**not** `feat/`, or the spec gate blocks it).
- [ ] T025 Rehearse end to end with the reference deck in a real intake issue.

## Phase E: Hand over

- [ ] T026 Watch one real batch through by hand, then enable auto-merge.
- [ ] T027 Update `specs/README.md` coverage + Tests Owed register as SC rows turn ✅.

---

## Deferred / decided against

- [ ] D001 Back-fill the 2025-02 → 2026-08 gap from older decks — one run once the pipeline is
      trusted, not part of building it.
- [ ] D002 `/tv/` page. The deck already drives the store TV; the site does not need to.
