---
description: "Publish repair posts from the team's existing weekly PowerPoint deck, on an owner-triggered build event"
status: in-progress
prs: [18]
---

# Feature Specification: Weekly Repair Deck → Published Posts

**Branch**: `feat/deck-to-posts` | **Date**: 2026-08-08 | **Constitution**: v2.0.0

## Problem

No repair has been published to gadjoy.in since **2025-02-18**, despite the shop continuing to do
the work. The blog stopped because nothing connects the work to the site.

The reason that matters: the team *already* produces a **weekly PowerPoint** of before/after repair
photos for the TV in the store. The record exists; it just never reaches the website. Any solution
that asks the team to re-enter a repair — a form, a CMS, a WordPress admin — adds a second job to
someone's week and will lapse exactly as the previous flow did. The only durable design publishes
from the artifact they already make for another reason.

Two properties of the current setup are deliberate and must survive:

- **Zero hosting cost.** The move off WordPress was to stop paying for it and to drop the VM/VPC
  bill. Static Hugo on GitHub Pages has no compute cost, and Actions minutes are free and
  unlimited on public repositories.
- **Public by design.** These case studies are meant to be public information; publishing from a
  public repo is the point, not a compromise.

One tension: repair photos have been shown to contain customer identifiers (229 images redacted in
PR #16 — serials, IMEI pairs, MAC addresses). A deck arriving from the bench will contain more of
them. Intake therefore cannot be a public issue, because a photo dropped into one is world-readable
the instant it posts and its attachment URL outlives the issue.

## User Scenarios

### US1 — The team hands over a week's work without extra effort (P1)
A team member opens one issue in a private intake repo, attaches the deck they already built for
the TV, and is done. No transcription, no per-repair form, no learning a CMS.

### US2 — The owner publishes when ready (P1)
The owner triggers one build event. Every pending deck is turned into posts, and the site updates
without further intervention.

### US3 — A customer's identifiers never reach the public site (P1)
Photos of About screens are redacted before publication, automatically, with the team told what
was removed.

### US4 — A malformed deck is obvious, not silent (P2)
If the team changes the template, the run reports exactly which slides it could not read and why,
rather than publishing a subset and appearing to succeed.

## Requirements

### Intake
- **FR-001** Intake MUST be a **private** repository. Deck photos MUST NOT be publicly reachable
  before redaction.
- **FR-002** Intake MUST be a single issue per deck, created from an Issue Form capturing the
  week-ending date, the deck attachment, and optional notes.
- **FR-003** No workflow may run in the intake repo. Actions minutes are metered on private repos
  and unlimited on public ones, so all compute MUST run in the public site repo, reading intake
  through a read-only fine-grained token.

### Parsing (contract verified against `migration/wp-export/.../Repair-1.1.7.pptx`)
- **FR-004** A slide is a **repair slide** iff it has exactly 2 picture shapes AND its text
  contains both `Before` and `After`. All other slides MUST be skipped with a recorded reason.
- **FR-005** Before/after MUST be decided by shape **x-position** relative to the slide midpoint,
  never by filename or shape order. (In the reference deck, slide 7 has `image8` left of `image7`.)
- **FR-006** The title MUST be taken from the topmost text shape.
- **FR-007** Captions MUST be taken from the low-`y` text shape on each side, with runs
  concatenated per shape (bold formatting splits a sentence across runs).
- **FR-008** Footer furniture MUST be discarded: deck version, page number, the slide's date shape,
  and the `Gadjoy Repair Services` footer.
- **FR-009** The slide's own date MUST be ignored. Every repair slide in the reference deck reads
  `10-03-2021` in a deck dated `20-02-2022` — it is an unmaintained placeholder. The post date
  comes from the intake issue, defaulting to the run date.
- **FR-010** A slide carrying a video MUST NOT crash the run. Either embed it or skip it with a
  recorded reason.
- **FR-011** A slide that looks like a repair slide but yields no usable title MUST fail the run
  loudly. Silent partial success is forbidden — publication is unattended.

### Generation
- **FR-012** Generated posts MUST satisfy every existing content invariant: real taxonomy slugs,
  existing term pages, a resolving banner, plaintext summary, no WordPress artifacts.
- **FR-013** Categories MUST be drawn from a keyword map that emits **only** category slugs that
  already exist, falling back to `[repair]`. Inventing a taxonomy term is forbidden (Principle III).
- **FR-014** Slugs MUST be derived from the title and de-duplicated against all existing posts
  using the established `-N` suffix convention.
- **FR-015** Image paths MUST be deterministic and self-describing:
  `/img/uploads/YYYY/MM/<slug>-{before,after}.webp`.
- **FR-016** Posts MUST carry `origin: deck` and `deck_issue: <n>` for provenance.
- **FR-017** Generation MUST be **idempotent**: re-running a deck already published MUST produce
  no second post.
- **FR-018** New posts MUST NOT carry a fabricated `/blog/…` legacy alias. They never had a legacy
  URL, and inventing one is fabricated data.

### Privacy interlock
- **FR-019** Every extracted image MUST be scanned for device/customer identifiers and redacted
  before it is written into the site, reusing the existing detector and redactor.
- **FR-020** Whatever was redacted MUST be reported back on the intake issue, so the practice can
  be corrected at the bench.

### Publication
- **FR-021** The publisher MUST open a pull request, never commit to `main` directly, so all
  existing gates run.
- **FR-022** The PR branch MUST NOT use the `feat/` prefix. CI fails any `feat/*` PR that touches
  no `specs/` file, so a `feat/`-prefixed automation branch would block itself on every run.
- **FR-023** The PR MAY auto-merge once every gate passes.
- **FR-024** The run MUST report per-deck outcome — published, skipped, redacted — on the intake
  issue, and close it on success.

## Success Criteria

| ID | Criterion | Test coverage |
|---|---|---|
| SC-001 | Reference deck yields exactly 4 repairs; 5 boilerplate slides skipped with reasons | ✅ `test_only_repair_slides_are_taken`, `test_boilerplate_slides_are_skipped_with_reasons` |
| SC-002 | Slide 7's before/after are correct despite inverted filename order | ✅ `test_before_after_assigned_by_position_not_filename`, `test_slides_7_and_8_have_inverted_filename_order` |
| SC-003 | Titles and both captions extracted verbatim; footer furniture stripped | ✅ `test_title_and_captions_extracted_verbatim`, `test_footer_furniture_never_leaks_into_fields` |
| SC-004 | A repair-shaped slide with no usable title fails the run | ✅ `test_repair_shaped_slide_without_a_title_raises` |
| SC-005 | The video-bearing slide does not crash the run | ✅ `test_video_slide_does_not_break_the_run` |
| SC-006 | Generated posts pass every existing invariant | ✅ `test_generated_posts_satisfy_the_real_content_invariants` — invokes the real acceptance functions, not a lookalike |
| SC-007 | Re-running the same deck adds nothing | ✅ `test_republishing_the_same_deck_adds_nothing` |
| SC-008 | An About-screen photo publishes redacted; interlock mutation-tested | ✅ `test_identifiers_are_redacted_before_an_image_is_written` (real OCR), `test_build_post_runs_every_image_through_the_interlock`; mutations 4/4 caught |
| SC-009 | Migrated-post count invariant still fails if a migrated post is removed | ✅ `test_gate_scoping.py` |
| SC-010 | Slugs never collide with the 1,508 existing posts | ✅ `test_generated_slugs_never_collide_with_the_migrated_corpus` |

All ten are covered. Every row was `OWED` when this spec was written and moved to ✅ as its test
landed, which is the order Constitution I requires.

**Not covered by a test, and deliberately so:** the end-to-end rehearsal (real intake issue →
workflow run → PR → auto-merge). It needs the private repo and token, which are owner actions.
Tracked as T021/T022/T025 in `tasks.md`, not pretended to be automated.

## Out of Scope

- **A `/tv/` page.** The deck already drives the in-store TV; the site does not need to.
- **Editing or deleting a published repair.** Corrections go through the normal PR flow.
- **OCR of captions from images.** Captions come from the deck's text shapes, which are reliable.
- **Migrating the 2025-02 → 2026-08 backlog.** This builds the pipeline; back-filling old decks is
  a separate run once it is trusted.
- **python-pptx.** Stdlib `zipfile` + `ElementTree` is proven against the reference deck, and the
  repo values minimal dependencies. Revisit only if a real deck defeats it.

## Dependencies

Requires PR #16 (`device_identifiers.py`, `redact_device_identifiers.py`,
`build_reviewed_manifest.py`) — the privacy interlock in FR-019 is built entirely from it.
