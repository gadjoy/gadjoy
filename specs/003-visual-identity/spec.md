---
description: "Monochrome visual identity, hero with animated stats, navigation and homepage structure"
status: shipped
shipped: 2026-06-06
prs: [2, 3, 4]
backfilled: 2026-08-05
---

# Feature Specification: Monochrome Visual Identity & Homepage

**Shipped**: 2026-06-06 via PRs #2, #3, #4 | **Backfilled**: 2026-08-05 | **Constitution**: v2.0.0

> **Retrospective spec.** Written after the fact, from the PR record and commit history (see
> As Built). No prior planning session for this work survives on the current machine, so
> nothing here is reconstructed from memory or invented: every requirement below is one that
> the shipped code demonstrably implements, and where the shipped result *differed* from the
> stated intent that is recorded as a deviation rather than quietly rewritten.

## Problem

The migrated site rendered through an unmodified `hugo-universal-theme`: a generic light-blue
template with stock copy, a rotating carousel, a small logo, and no evidence that the business
is any good. The content was real (1,508 repair case studies) but nothing about the
presentation signalled a specialist chip-level repair bench, and there was no visible route to
contact for a visitor who had just decided they wanted a repair.

## User Scenarios

### US1 — A visitor judges credibility in seconds (P1)
Someone arrives on a cracked phone, on mobile. Within one screen they see what the shop does,
proof it is competent, the turnaround, and two ways to start a conversation — without scrolling
into blog listings.

### US2 — A visitor reaches a human immediately (P1)
From any page, at any scroll position, calling or messaging on WhatsApp is one tap away.

### US3 — A visitor browses recent work without landing in a blog archive (P2)
Recent repairs are visible on the homepage as a scannable strip, not a paginated list.

### US4 — A visitor finds the two service lines directly (P2)
"We Repair" and "We Build" are reachable from the top-level navigation, not hidden in a
dropdown.

## Requirements

- **FR-001** The theme MUST be customised via project-level layout overrides and
  `static/css/custom.css` only. No fork of `hugo-universal-theme`.
- **FR-002** A single palette MUST be applied site-wide, defined as CSS custom properties in
  one file, so a change of direction is one edit and not a hunt.
- **FR-003** The homepage MUST open with a static hero: positioning line, sub-copy naming the
  device classes, two CTAs (quote + WhatsApp), and a trust line. The theme's rotating owl
  carousel MUST be replaced, not merely restyled.
- **FR-004** A stats band MUST present turnaround, warranty, device-brand breadth and repair
  volume, count-animated on scroll into view.
- **FR-005** Animation MUST respect `prefers-reduced-motion: reduce`.
- **FR-006** Floating Call and WhatsApp buttons MUST be present on every page, and MUST remain
  visible against both light and dark sections.
- **FR-007** The homepage MUST show the 15 most recent posts, newest first, as a horizontal
  scroller with a scroll affordance and a link to the full archive.
- **FR-008** Typography MUST be Sora for headings and Inter for body, at a 16px base, applied
  consistently across home, blog, services, contact, sidebar and footer.
- **FR-009** Navigation MUST expose Home, We Repair, We Build, Blog, Gallery, Contact as
  top-level items.
- **FR-010** Footer text and controls MUST meet legibility contrast against the footer
  background (the original shipped black-on-black).
- **FR-011** Every headline figure in the stats band MUST trace to a named source
  (Constitution X); none may be hardcoded in the template.

## Success Criteria

| ID | Criterion | Test coverage |
|---|---|---|
| SC-001 | Hero renders with both CTAs; no owl carousel markup on the homepage | **OWED** |
| SC-002 | Stats band emits 4 counters with values from config/derived data | ✅ `test_repair_claim_matches_config` (repair figure only) |
| SC-003 | Recent-repairs scroller shows exactly 15 posts, newest first | **OWED** |
| SC-004 | All six top-level nav items resolve to real pages | ✅ `test_internal_refs_resolve` |
| SC-005 | Floating Call/WhatsApp present on every page and legible on dark sections | **OWED** (visual — the #12 bug) |
| SC-006 | `prefers-reduced-motion` disables count-up and reveal animation | **OWED** |
| SC-007 | No theme fork: `themes/` unmodified relative to upstream | **OWED** |

## Out of Scope

- Dark mode. The site commits to one light monochrome look.
- Any change to blog post *content* — that is `001`'s territory and is invariant-tested.

## As Built

Shipped across three PRs on 2026-06-06:

- **PR #2** (`feat/site-refresh`) — hero, stats band, floating buttons, palette, copy rewrite.
- **PR #3** (`feat/footer-logo`) — recent-repairs scroller (15 latest), footer legibility fix
  (contact button was black-on-black), logo 56px → 74px.
- **PR #4** (`feat/nav-logo`) — logo 74px → 88px; Services dropdown removed in favour of
  top-level We Repair / We Build.

### Deviation: the palette changed direction twice mid-implementation

PR #2's description states a "deep **navy + warm amber/gold** palette". That is not what is
live. Three successive commits on the same branch tell the real story:

| Commit | Change |
|---|---|
| `f2bba26a` | Site refresh: navy + amber theme, punchy copy, live Google reviews |
| `c4317ade` | Switch to monochrome black & white theme (+167 / −120 in `custom.css`) |
| `406a5ef7` | Switch to monochrome light theme: white bg, black buttons (+92 / −148) |

So the visual direction was decided *during* implementation and rewritten twice, at a cost of
roughly 260 changed CSS lines after the first version was already built, and the PR description
was never corrected — it still advertises a palette the site does not use. FR-002 (one palette,
one file, custom properties) is the requirement that made the second and third attempts cheap,
and is worth keeping for that reason.

This is the clearest argument in the repo for settling a visual direction in a spec before
writing CSS, and it is why this backfill exists.

### Iteration also visible in the record

The logo was resized twice across three PRs (56 → 74 → 88px) and the nav restructured once —
post-launch polish "requested by the owner" per PR #3. Not a defect; but it is the shape of
work that a one-paragraph spec would have collapsed into a single change.

## Tests Owed

Six of seven success criteria have no automated guard, because they are visual or
behavioural. Tracked in `specs/README.md` under Tests Owed; the honest position is that this
feature is currently protected only by "the build succeeds and links resolve". SC-005 is not
hypothetical — it is precisely the bug PR #12 fixed by hand.
