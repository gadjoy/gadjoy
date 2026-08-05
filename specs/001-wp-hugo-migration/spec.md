---
description: "Faithful WordPress → Hugo migration: real content, real taxonomy, original URLs preserved"
status: shipped
shipped: 2026-06-06
prs: [2]
---

# Feature Specification: Faithful WordPress → Hugo Migration

**Feature Branch**: `fix/wp-hugo-remigration`

**Created**: 2026-06-03

**Status**: Draft

**Input**: User description: "The migration was not done correctly. The WordPress backup is the
reference. The current Hugo site is unacceptable. Rectify it ASAP — important live site. Do it
with spec-driven development and TDD this time."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Every post migrated with intact content at its original URL (Priority: P1) 🎯 MVP

A visitor (or search engine) opening any of the site's ~1,508 articles sees the full, correctly
formatted article — text, headings, lists, and images — at the exact same URL the WordPress
site used.

**Why this priority**: This is the entire point. The current site corrupts bodies, fabricates
taxonomy, breaks images, and likely changed URLs. Restoring faithful content at stable URLs is
the minimum viable outcome and protects the site's SEO/traffic.

**Independent Test**: Restore the backup, run the extractor, build with Hugo, and assert via the
pytest suite that post count == 1,508, no body contains WordPress artifacts, every image
resolves, and a sample of slugs map to the original permalink paths.

**Acceptance Scenarios**:

1. **Given** the restored WordPress DB, **When** the extractor runs, **Then** `content/blog/`
   contains exactly 1,508 published-post Markdown bundles (+ known drafts handled explicitly).
2. **Given** a generated post, **When** its body is inspected, **Then** it contains no
   `<!-- wp:` comments, no `[shortcode]` residue, and no stray raw HTML tags.
3. **Given** a generated post that had images in WordPress, **When** the build runs, **Then**
   every `<img>`/figure `src` is of the form `/img/uploads/...` and the file exists on disk.
4. **Given** the original `permalink_structure`, **When** a post is built, **Then** its output
   path equals the original URL path and an `aliases:` entry mirrors that path.

### User Story 2 - Correct taxonomy and metadata (Priority: P2)

Category and tag archive pages list the right posts because each post carries the real
categories/tags, title, date, and excerpt from WordPress — not values guessed from the title.

**Why this priority**: Wrong/junk taxonomy ("10+", "130") breaks navigation and looks
unprofessional, but the site is still usable if P1 is met. Depends on the same extraction pass.

**Independent Test**: Assert every category/tag emitted exists in the WordPress term set and
that no purely numeric junk tags are produced; spot-check a post's terms against the DB.

**Acceptance Scenarios**:

1. **Given** a post with WP categories/tags, **When** migrated, **Then** its front-matter
   `categories`/`tags` equal the WP terms (by name/slug).
2. **Given** the full output, **When** all tags are collected, **Then** none are fabricated
   title fragments and none are purely numeric noise.

### User Story 3 - Maintainable, reproducible source (Priority: P3)

A maintainer can edit a post in `content/blog/` and rebuild, and can re-run the migration to
reproduce the same source from the backup.

**Why this priority**: Long-term maintainability and disaster recovery. Valuable but not
required for the immediate fix.

**Independent Test**: `content/blog/` holds Markdown bundles (not just built HTML); `hugo`
rebuilds the full site from source with no errors; a second migration run is idempotent.

**Acceptance Scenarios**:

1. **Given** the Markdown source, **When** `hugo --minify` runs, **Then** the build succeeds and
   `public/` is regenerated from source.
2. **Given** an unchanged backup, **When** the migration is re-run, **Then** the generated
   source is materially identical.

### Edge Cases

- Posts with **no images** in WordPress → migrate with empty media, no broken `<img>`, no
  injected placeholder banners.
- Posts with **duplicate slugs** (e.g. `...-2`) → preserve the real WP slug; no collisions.
- **Gutenberg galleries / multi-image** posts → all images preserved with captions.
- Posts whose content uses **shortcodes/Elementor** → use WP-rendered HTML so they are already
  expanded before conversion.
- **Non-ASCII / emoji** titles and slugs → preserved correctly (UTF-8).
- **Drafts** (2 known) → excluded from the published count; handled by explicit rule, not by
  accident.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST extract posts from the restored WordPress instance (REST API of the
  Docker site imported from the `.wpress` backup), never from `posts.json` or built HTML.
- **FR-002**: System MUST convert each post's WP-rendered HTML to Markdown without losing
  headings, lists, galleries, image captions, or alt text, and without leaving `<!-- wp: -->`
  comments, shortcodes, or stray HTML.
- **FR-003**: System MUST set front matter from real source fields: title, date (`date_gmt`),
  slug (real `post_name`), categories, tags, description (excerpt), featured image.
- **FR-004**: System MUST read categories/tags from the real WP taxonomy; it MUST NOT infer
  them from post titles.
- **FR-005**: System MUST reproduce each post's original URL exactly, driven by the DB's
  `permalink_structure`, and MUST add an `aliases:` entry equal to that URL path.
- **FR-006**: System MUST normalize every image reference to a single canonical
  `/img/uploads/<rel>` form and ensure the referenced file exists under `static/img/uploads/`.
- **FR-007**: System MUST write Markdown page bundles to `content/blog/...` such that `hugo`
  regenerates the site from source.
- **FR-008**: System MUST migrate the WP pages (Home, We Repair, We Build, Contact, Gallery),
  reconciling with existing hand-authored pages (keep hand-authored where better).
- **FR-009**: A pytest suite MUST encode FR-001..FR-006 as automated checks and MUST be written
  to fail before implementation (TDD).

### Key Entities

- **Post**: a WordPress article. Attributes: id, title, slug, date, rendered HTML content,
  excerpt, categories[], tags[], featured image, original URL.
- **MediaItem**: an uploaded file referenced by posts; canonical path `/img/uploads/<year>/<month>/<file>`.
- **PermalinkStructure**: the WP `wp_options.permalink_structure` string that defines every
  post's canonical URL.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 1,508 published posts migrated (100% of the published inventory); count asserted by test.
  — ✅ `test_post_count_matches_source`
- **SC-002**: 0 posts contain WordPress artifacts (`<!-- wp:`, `[shortcode]`, stray HTML) — asserted by test.
  — ✅ `test_no_wordpress_artifacts`
- **SC-003**: 0 broken image references in the built site; 100% of `<img>` srcs resolve to files on disk.
  — ✅ `test_all_media_resolve_on_disk` (source, case-exact) + `test_internal_refs_resolve` (built output)
- **SC-004**: 100% of sampled post URLs match the original WordPress permalink path; old paths
  resolve via aliases.
  — ✅ `test_url_preservation_via_aliases` + `test_sample_post_served_at_original_url`
- **SC-005**: 0 fabricated/numeric-junk tags; 100% of emitted terms exist in the WP taxonomy.
  — ✅ `test_taxonomy_is_real` + `test_taxonomy_term_pages_exist_with_titles`
- **SC-006**: `hugo --minify` builds with 0 errors from `content/` source alone.
  — ✅ `test_hugo_builds_clean`

> **Coverage: 6 of 6 guarded.** This spec is the one part of the project that was fully
> test-covered from the start, which is why the blog content has never been the source of a
> production incident. Note that SC-003 was only *genuinely* met once the check became
> case-exact — see `008`'s As Built for the two images that shipped 404ing.

## Assumptions

- The 2025-04-25 `.wpress` backup is canonical; content published after that date is
  unrecoverable and out of scope (confirmed with user — no live WP exists elsewhere).
- The local environment can run Docker to host the restored WordPress and reach its REST API at
  `http://localhost:8080/wp-json/`.
- The existing copied uploads under `static/img/uploads/` and/or
  `migration/wp-export/wp-content/uploads/` contain the media; missing files are re-copied from
  the backup.
- The HTTPS/certificate fix for `gadjoy.in` (GitHub Pages) is a separate later phase and out of
  scope for this spec's acceptance.
