# Gadjoy WordPress → Hugo Migration Constitution

## Core Principles

### I. Test-First (NON-NEGOTIABLE)
TDD is mandatory for all migration code. The cycle is strictly: write a test → confirm it
FAILS (red) → implement the minimum to make it pass (green) → refactor with tests green. No
extractor or conversion code is written before a failing test exists for the behaviour it
delivers. The pytest suite under `migration/tests/` is the permanent acceptance gate — the
migration is "done" only when it is green, and any future change must keep it green. The
previous migration shipped with zero tests and silently corrupted 1,500+ posts; this principle
exists specifically to make that failure mode impossible to repeat.

### II. Single Source of Truth
All post content, metadata, taxonomy, and media derive from the restored WordPress instance
(imported from the 2025-04-25 `.wpress` backup in `wordpress/backup/`). No other input is
authoritative. The minimal `migration/wp-export/posts.json` (metadata only) and the existing
broken `public/` HTML are NOT sources and must never be used to reconstruct content.

### III. No Fabricated Data
Categories, tags, dates, slugs, and excerpts are read from the WordPress database/REST API
exactly as stored. Deriving taxonomy by guessing from post titles (as the old
`wp_to_hugo.py` did) is forbidden. If a field is absent in the source, it is absent in the
output — never invented.

### IV. Exact URL Preservation
Every migrated post must be reachable at the same URL the original WordPress site served. The
canonical permalink structure is read from `wp_options.permalink_structure` in the restored DB
and reproduced exactly via Hugo `permalinks` config. Every post additionally carries an
`aliases:` entry equal to its original URL path as a redirect safety net. URL parity is a
tested invariant, not a hope.

### V. Lossless, Verifiable Content
Conversion preserves the rendered content: headings, lists, galleries, image captions, and alt
text survive. The output contains NO leftover WordPress artifacts — no `<!-- wp: -->` block
comments, no `[shortcode]` residue, no raw unintended HTML. Every image reference uses one
canonical path form (`/img/uploads/...`) and the referenced file must exist on disk. These are
tested invariants.

### VI. Reproducible Source
The blog must exist as Markdown source in `content/blog/` and be fully regenerable by `hugo`.
A site whose content lives only in built `public/` HTML (the current broken state) is
unacceptable. Re-running the documented migration against the same backup must produce the
same source.

## Additional Constraints

- **Stack**: Hugo (extended) static site, `hugo-universal-theme`; deployed to GitHub Pages at
  custom domain `gadjoy.in`. Migration tooling is Python 3.13 + pytest; HTML→Markdown via
  `markdownify`. WordPress restored via Docker (`wordpress/docker-compose.yml`).
- **Inventory to match**: 1,508 published posts (+2 drafts), 2021-12 → 2025-02; the WP pages
  (Home, We Repair, We Build, Contact, Gallery); ~10,630 uploaded media files.
- **Sequencing**: Content correctness ships first. The `gadjoy.in` HTTPS/certificate fix is a
  separate, later phase and must not block the content migration.

## Development Workflow

- Spec-Driven Development: `constitution → spec → plan → tasks → implement`. Artifacts live in
  `.specify/` and `specs/001-wp-hugo-migration/`.
- All work on a feature branch (`fix/wp-hugo-remigration`). No commits without explicit user
  approval. At MR time, ask which branch to target — never assume `main`. Commit messages carry
  no AI-tool attribution.
- Quality gate before any merge: `pytest` green AND `hugo --minify` builds with no errors.

## Governance

This constitution supersedes ad-hoc migration practices. Any deviation (e.g. shipping code
without a failing test first, or fabricating a missing field) must be justified in writing in
the plan's Complexity Tracking and approved by the user before proceeding. Compliance is
verified by the pytest suite and a clean Hugo build.

**Version**: 1.0.0 | **Ratified**: 2026-06-03 | **Last Amended**: 2026-06-03
