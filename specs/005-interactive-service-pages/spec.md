---
description: "We Repair / We Build as interactive capability pages rather than prose"
status: shipped
shipped: 2026-06-06
prs: [5]
backfilled: 2026-08-05
---

# Feature Specification: Interactive Service Pages

**Shipped**: 2026-06-06 via PR #5 | **Backfilled**: 2026-08-05 | **Constitution**: v2.0.0

> **Retrospective spec**, reconstructed from PR #5, commit `3cb30465`, and
> `layouts/services/single.html`.

## Problem

The two service pages were prose blocks rendered through the default single-page template, and
"We Build" was described in PR #2 as outright **broken** and needing a full rewrite. Neither
page answered the only question a visitor actually has — *do you fix my specific device, and
what happens after I hand it over?* — without reading several paragraphs.

## User Scenarios

### US1 — A visitor confirms their device class is covered (P1)
Scanning, not reading, a visitor sees device and capability tiles and recognises their own case
(phone / laptop / tablet / desktop; chip-level, water damage, display, hinge, data).

### US2 — A visitor understands the process before committing (P1)
A step timeline sets expectations: what happens on drop-off, diagnosis, quote, repair, return.

### US3 — A visitor understands why here and not a corner shop (P2)
Reason cards state the differentiators (chip-level capability, genuine parts, warranty, honest
quotes).

## Requirements

- **FR-001** Both service pages MUST render through a dedicated project layout
  (`layouts/services/single.html`), not the theme's default single template.
- **FR-002** Page content MUST stay in front matter / Markdown under
  `content/services/<slug>/index.md` so copy is editable without touching templates.
- **FR-003** Each page MUST present: device/capability tiles, an ordered step timeline, and
  reason cards.
- **FR-004** Sections MUST reveal on scroll, and the reveal MUST be disabled under
  `prefers-reduced-motion: reduce`.
- **FR-005** The "We Build" copy MUST be rewritten to describe the actual offering.
- **FR-006** Both pages MUST be reachable from top-level navigation (see `003` FR-009) and MUST
  offer the same contact routes as the rest of the site.

## Success Criteria

| ID | Criterion | Test coverage |
|---|---|---|
| SC-001 | `/services/we-repair/` and `/services/we-build/` both return 200 | ✅ `scripts/smoke.sh` |
| SC-002 | Both render the project services layout, not the theme fallback | **OWED** — this is exactly the #6/#7 bug class, guarded for `contact` but **not** for services |
| SC-003 | Tiles, timeline and reason cards all present on both pages | **OWED** |
| SC-004 | Scroll-reveal disabled under reduced-motion | **OWED** |
| SC-005 | All links/assets on both pages resolve | ✅ `test_internal_refs_resolve` |

## Out of Scope

- Per-device pricing (deliberately not published; quotes are per-case).
- A booking or scheduling system. The contact routes in `007` are the intake path.

## As Built

Commit `3cb30465` added `layouts/services/single.html` (105 lines), rewrote both
`content/services/*/index.md`, added the scroll-reveal observer to
`layouts/partials/custom_headers.html` (+18 lines) and the styles to `custom.css` (+67).

Shipped inside PR #5 alongside three unrelated changes — a typography pass, the gallery
rebuild, and the contact redesign. Four distinct features in one PR is why the record for this
work is thin: the PR description gives each one a single bullet, and the gallery and contact
work needed their own specs (`006`, `007`).

## Tests Owed

**SC-002 is the priority.** `test_contact_page_uses_project_layout` exists only because the
contact page's layout lookup broke in production (#6/#7). The services pages resolve their
layout by the *same* mechanism — `layouts/services/single.html` is a section template, a
different lookup path again — and have no equivalent guard. A Hugo upgrade could silently
degrade them to the theme default exactly as happened to contact, and nothing would fail.

Generalising `test_contact_page_uses_project_layout` into a table-driven check over
(URL, expected bespoke marker) for contact, services, and gallery is a small change that closes
SC-002 here and SC-002 in `006`.
