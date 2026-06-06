# Implementation Plan: Faithful WordPress → Hugo Migration

**Branch**: `fix/wp-hugo-remigration` | **Date**: 2026-06-03 | **Spec**: `./spec.md`

**Input**: Feature specification from `specs/001-wp-hugo-migration/spec.md`

## Summary

Replace the broken regex/SQL migration with a REST-API-based extractor that pulls WP-rendered
HTML, real taxonomy, and media from a locally-restored WordPress (imported from the `.wpress`
backup), converts HTML→Markdown losslessly, normalizes image paths, preserves original URLs via
Hugo permalinks + aliases, and writes maintainable Markdown source. Built test-first: a failing
pytest suite encodes the acceptance criteria before any extractor code is written.

## Technical Context

**Language/Version**: Python 3.13

**Primary Dependencies**: `requests` (WP REST API), `markdownify` (HTML→Markdown),
`PyYAML` (front-matter assertions in tests), `pytest`

**Storage**: Restored WordPress MySQL via Docker (`wordpress/docker-compose.yml`); output is
Hugo Markdown bundles under `content/blog/` + media under `static/img/uploads/`

**Testing**: pytest — golden-file conversion tests + invariant/acceptance tests

**Target Platform**: Hugo extended static site → GitHub Pages (`gadjoy.in`)

**Project Type**: Static-site content migration (CLI script + test suite)

**Constraints**: Exact-URL preservation; zero WP artifacts; single canonical image path; source
must be reproducible by `hugo`

**Scale/Scope**: 1,508 published posts + 5 pages; ~10,630 media files

## Constitution Check

*GATE: Must pass before implementation. Re-check after design.*

- **I. Test-First**: PASS — Phase 1b writes failing pytest before Phase 2 implementation.
- **II. Single Source of Truth**: PASS — extractor reads only the restored WP REST API.
- **III. No Fabricated Data**: PASS — taxonomy/metadata from `_embedded` terms, not titles.
- **IV. Exact URL Preservation**: PASS — permalink from `wp_options`, reproduced + aliased.
- **V. Lossless Content**: PASS — markdownify on rendered HTML; artifact-absence is tested.
- **VI. Reproducible Source**: PASS — writes `content/blog/` Markdown; `hugo` rebuilds.

No violations → Complexity Tracking empty.

## Project Structure

### Documentation (this feature)

```text
specs/001-wp-hugo-migration/
├── spec.md       # Feature spec (done)
├── plan.md       # This file
└── tasks.md      # Ordered, test-first task list
```

### Source Code (repository root)

```text
migration/
├── scripts/
│   ├── wp_to_hugo.py          # OLD — broken; left for reference, not run
│   └── wp_rest_to_hugo.py     # NEW — REST extractor + HTML→MD + image normalize + aliases
└── tests/
    ├── conftest.py            # fixtures: restored-WP base URL, generated-content path
    ├── test_conversion.py     # golden-file HTML→Markdown unit tests
    ├── test_invariants.py     # count, no-artifacts, image-resolution, taxonomy, URL parity
    └── fixtures/
        ├── <slug>.html        # input: WP rendered HTML for hand-picked posts
        └── <slug>.expected.md # expected Markdown output

content/blog/<per-permalink-path>/index.md   # generated source
static/img/uploads/<year>/<month>/<file>     # normalized media
hugo.yaml                                      # permalinks + baseURL + taxonomies
```

**Structure Decision**: Single migration tool + pytest suite at repo root under `migration/`.
The old script stays as a reference artifact (not executed). Generated content lands in the
standard Hugo `content/` and `static/` trees.

## Phases

- **Phase 0 (done)**: Spec Kit init; constitution; this spec + plan + tasks.
- **Phase 1a**: Restore WP from backup; read `permalink_structure`; confirm 1,508 published.
- **Phase 1b (RED)**: Write the failing pytest suite (golden + invariants) + ≥4 golden fixtures.
- **Phase 2 (GREEN)**: Implement `wp_rest_to_hugo.py` until all tests pass; refactor green.
- **Phase 3**: Fix `hugo.yaml` (permalinks to match real structure, `baseURL: https://gadjoy.in/`,
  taxonomies), verify `CNAME`; `hugo --minify`; redeploy via Pages workflow.
- **Phase 4 (separate)**: GitHub Pages HTTPS/cert fix for `gadjoy.in`.

## Complexity Tracking

*No constitution violations — section intentionally empty.*
