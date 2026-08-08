---
description: "Implementation plan for deck-to-posts publishing"
---

# Implementation Plan: Weekly Repair Deck → Published Posts

**Branch**: `feat/deck-to-posts` | **Date**: 2026-08-08 | **Spec**: `./spec.md`

## Summary

Parse the team's existing weekly `.pptx`, turn each repair slide into a Hugo post bundle with
identifiers redacted, and open an auto-merging PR. All compute runs in the public repo; only the
raw photos sit briefly in a private intake repo.

## Technical Context

**Language**: Python 3.13. **New dependencies: none** — `.pptx` is a zip of XML, and stdlib
`zipfile` + `xml.etree.ElementTree` was verified against the reference deck during planning
(9 slides, 14 media, shape geometry all readable). `python-pptx` stays a documented fallback.

**Reused wholesale**: `migration/scripts/device_identifiers.py` (OCR, `OMP_THREAD_LIMIT=1`),
`redact_device_identifiers.py` (`auto_boxes`, `mosaic`, `MAX_REDACT_FRACTION`),
`build_reviewed_manifest.py`, and `conftest.media_ref_resolves` / `iter_generated_posts`.

**Fixture**: `migration/wp-export/wp-content/uploads/2022/02/Repair-1.1.7.pptx` is already tracked
in git — a real deck from the team, usable as a golden fixture with no new binary to commit.

## Constitution Check (v2.0.0)

- **I / VII. Test-First**: PASS — parser tests are written against the fixture and confirmed RED
  before the parser exists; the PII interlock is mutation-tested.
- **III. No Fabricated Data**: PASS — categories only from existing slugs (FR-013); no invented
  legacy aliases (FR-018); slide dates ignored rather than guessed (FR-009).
- **VIII. Gates in CI**: PASS — publication goes through a PR so the existing suite runs; the
  automation branch prefix is chosen so it does not defeat the spec gate (FR-022).
- **X. Claims trace to a source**: PASS — captions and titles come from the deck verbatim.

No violations → Complexity Tracking empty.

## Approach

### Phase A — unblock growth (must land first)
Two invariants currently forbid any new post:

- `test_post_count_matches_source` asserts `len(posts) == 1508`.
- `test_url_preservation_via_aliases` requires a `/blog/…` alias on *every* post.

Re-scope both to **migrated** posts, discriminated by the absence of an `origin` key. The
migration invariant keeps full force (`migrated == 1508`) while total posts may grow
(`total >= 1508`). Each change ships with a test proving the migration gate still bites, because a
loosened gate that no longer catches the original bug is worse than no change at all.

### Phase B — parser (`tools/deck_to_posts.py`)
Pure function: `parse_deck(path) -> (repairs, skipped)`. No filesystem writes, no network, so it is
trivially testable. Repair-slide discrimination per FR-004; geometry per FR-005..FR-008.

### Phase C — generator
`build_post(repair, date, issue, existing_slugs) -> (front_matter, body, images)`. Redaction runs
here, between extraction and writing, so nothing un-redacted is ever written into `static/`.
Idempotence via `deck_issue` + slug lookup.

### Phase D — publisher (`tools/publish_decks.py` + workflow)
Reads intake issues via `INTAKE_TOKEN`, downloads decks, runs C, regenerates the reviewed-images
manifest, opens a `content/deck-<week>` PR, comments back, closes the issue.

## Key risks

- **Deck-format drift** — the parser must fail loudly (FR-011) since publication is unattended.
- **25MB attachment cap** — ~1.6MB per repair in the reference deck, so ~15 repairs approaches it;
  the run accepts several decks per batch.
- **Auto-merge** — accepted; bounded by the gates, one commit per deck for clean revert, and a
  loud parser.

## Phases

- **A**: invariant re-scoping + proving tests
- **B**: parser + golden fixtures (RED → GREEN)
- **C**: generator, idempotence, PII interlock (mutation-tested)
- **D**: publisher + workflow, rehearsed against the private repo
- **E**: enable auto-merge only after one batch has been watched by hand

## Complexity Tracking

*No constitution violations — section intentionally empty.*
