---
description: "Gallery as a before/after lightbox wall rather than blog-style thumbnails"
status: shipped
shipped: 2026-06-06
prs: [5]
backfilled: 2026-08-05
---

# Feature Specification: Before/After Gallery Wall

**Shipped**: 2026-06-06 via PR #5 | **Backfilled**: 2026-08-05 | **Constitution**: v2.0.0

> **Retrospective spec**, reconstructed from PR #5, commits `d8e33e51` and `cdb485f4`, and
> `layouts/_default/gallery.html`.

## Problem

The gallery rendered as a list of blog-like cards — the same treatment as `/blog/`, so it
duplicated the archive without adding anything. The repair photos are the strongest asset the
business has (genuine before/after pairs from the bench), and the card layout buried them at
thumbnail size behind titles and dates.

## User Scenarios

### US1 — A visitor scans a wall of real work (P1)
The gallery presents images at scale as a dense wall, not a paginated list of titled cards.

### US2 — A visitor inspects one repair closely (P1)
Clicking any image opens it full-size in a lightbox, dismissible without leaving the page.

### US3 — A visitor sees before and after together (P2)
Pairs read as pairs, so the transformation is legible rather than requiring the viewer to
reconstruct it.

## Requirements

- **FR-001** The gallery MUST render through `layouts/_default/gallery.html`, selected by
  `layout: gallery` in the page's front matter.
- **FR-002** Images MUST be laid out as a masonry/dense wall, visually distinct from the blog
  list treatment.
- **FR-003** Clicking an image MUST open a lightbox with the full-size asset; it MUST be
  closable by control and by keyboard.
- **FR-004** Lightbox behaviour MUST be implemented in the project's own JS
  (`layouts/partials/custom_headers.html`), not by adding a third-party dependency.
- **FR-005** Images MUST be lazy-loaded, and MUST use the optimised WebP assets produced by
  `008` — the gallery is the heaviest page on the site.
- **FR-006** No blog metadata (date, author, tags, sidebar) may appear on the gallery page.

## Success Criteria

| ID | Criterion | Test coverage |
|---|---|---|
| SC-001 | `/gallery/` returns 200 | ✅ `scripts/smoke.sh` |
| SC-002 | Renders the gallery layout, not the theme fallback | **OWED** (same class as `005` SC-002) |
| SC-003 | Every gallery image reference resolves to a real asset | ✅ `test_internal_refs_resolve` |
| SC-004 | Lightbox opens and closes, including by keyboard | **OWED** (needs a browser) |
| SC-005 | Images are lazy-loaded and served as WebP | **OWED** |

## Out of Scope

- Captions or per-image case notes. The blog posts carry the write-ups; the gallery is visual.
- Filtering by device or repair type.
- Any image upload or admin flow — assets come from the migration and `008`.

## As Built

Two commits inside PR #5: `d8e33e51` created `layouts/_default/gallery.html` (45 lines) with the
grid and typography pass; `cdb485f4` rebuilt it as the before/after lightbox wall (+26/−…) and
added the lightbox JS to `custom_headers.html` (+27 lines).

FR-005's dependency on `008` is real and was satisfied in the other order: the gallery shipped
first (PR #5, 21:07) and the WebP conversion landed later the same evening (PR #8, 22:57), so
for about 100 minutes the gallery was serving unoptimised originals from a 1.49 GB artifact.

### Privacy constraint discovered later, recorded here

A related decision from the portfolio work applies to this feature and belongs in its spec:
repair photographs must not publish customer-identifying data. Cards showing **IMEIs and
serial numbers** were identified on the live site and flagged for removal — those are
identifiers used in device fraud, and the customers did not consent to publication.

**FR-007** (added retrospectively, not yet enforced) — no gallery or post image may display an
IMEI, serial number, or other device/customer identifier.

## Tests Owed

SC-002 folds into the table-driven layout check proposed in `005`. SC-005 is a cheap output
assertion (`loading=lazy` present, `.webp` extensions). FR-007 cannot be checked by parsing
HTML — it needs either a manual review pass or OCR over the image set, and should be tracked as
a content-review task rather than pretended to be automatable.
