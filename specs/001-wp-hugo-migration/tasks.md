---
description: "Task list for faithful WordPress → Hugo migration"
---

# Tasks: Faithful WordPress → Hugo Migration

> **Status: COMPLETE — shipped 2026-06-06 via PR #2** (`feat/site-refresh`; the earlier
> attempt, PR #1, was closed in favour of it). Boxes below were ticked retrospectively on
> 2026-08-05: the work had been done and deployed for two months while this file still read
> 0 of 31, which is why Constitution v2.0.0 now requires a spec's task list to be kept
> current. Verified at tick time: 1,508 post bundles under `content/blog/`, `pytest` green
> (32 tests), and `https://gadjoy.in` serving on a valid Let's Encrypt certificate (T031).
>
> Follow-on work lives in `specs/002-ci-test-gates/`.

**Input**: Design documents from `specs/001-wp-hugo-migration/`

**Prerequisites**: plan.md, spec.md, constitution.md

**Tests**: REQUIRED and TEST-FIRST per Constitution Principle I. Tests are written and MUST FAIL
before any implementation task in their story begins.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: US1 (content+URLs), US2 (taxonomy), US3 (reproducible source)

---

## Phase 1: Setup (Shared Infrastructure)

- [x] T001 Create `migration/tests/` package + `migration/requirements.txt`
  (`requests`, `markdownify`, `PyYAML`, `pytest`) and a venv; `pip install`.
- [x] T002 [P] Add `pytest.ini`/`conftest.py` with fixtures: `WP_BASE_URL`
  (http://localhost:8080), `CONTENT_BLOG_DIR`, `STATIC_UPLOADS_DIR`.

## Phase 2: Foundational (Blocking Prerequisites)

**⚠️ CRITICAL**: Restored source must exist before tests can assert against it.

- [x] T003 Restore WordPress: reassemble + import the 18-part `.wpress` backup per
  `wordpress/wordpress-local-dev-setup-guide-gadjoy.md`; `docker compose up -d`.
- [x] T004 Verify source facts that seed tests: `SELECT option_value FROM wp_options WHERE
  option_name='permalink_structure';` and published-post count (== 1,508). Record both in
  `conftest.py` as constants.

**Checkpoint**: Restored WP reachable at `localhost:8080`; canonical permalink + count known.

---

## Phase 3: User Story 1 - Content + original URLs (Priority: P1) 🎯 MVP

**Goal**: Every post migrated, body intact, images resolving, at its original URL.

**Independent Test**: pytest invariants for count, artifacts, images, URL parity pass.

### Tests for User Story 1 (WRITE FIRST — MUST FAIL) ⚠️

- [x] T005 [P] [US1] Golden fixtures: capture WP-rendered HTML for ≥4 hand-picked posts (plain,
  list, gallery, multi-image) → `migration/tests/fixtures/<slug>.html` + author
  `<slug>.expected.md`.
- [x] T006 [P] [US1] `test_conversion.py`: assert `html_to_markdown(<slug>.html)` ==
  `<slug>.expected.md` for each fixture (no `<!-- wp:`, no shortcodes, captions preserved).
- [x] T007 [P] [US1] `test_invariants.py::test_post_count` — generated bundles == 1,508.
- [x] T008 [P] [US1] `test_invariants.py::test_no_wp_artifacts` — no `<!-- wp:` / `[shortcode]`
  / stray HTML in any body.
- [x] T009 [P] [US1] `test_invariants.py::test_images_resolve` — every `<img>`/figure src is
  `/img/uploads/...` AND the file exists under `static/img/uploads/`.
- [x] T010 [P] [US1] `test_invariants.py::test_url_parity` — sampled post output paths match the
  path implied by `permalink_structure`; each post has an `aliases:` entry == original URL.
- [x] T011 [US1] Run `pytest` → confirm RED (all the above fail; nothing implemented).

### Implementation for User Story 1 (make tests GREEN)

- [x] T012 [US1] `wp_rest_to_hugo.py`: REST client paging
  `GET /wp-json/wp/v2/posts?per_page=100&page=N&_embed`.
- [x] T013 [US1] `html_to_markdown()` via `markdownify` on `content.rendered`; strip residual
  block comments; keep figures/captions. (satisfies T006)
- [x] T014 [US1] Front matter writer: title, `date_gmt`, real slug, description, featured image.
- [x] T015 [US1] Permalink/aliases: derive output path from `permalink_structure`; emit
  `aliases:`. (satisfies T010)
- [x] T016 [US1] Image normalizer: rewrite every src → `/img/uploads/<rel>`; ensure file exists
  under `static/img/uploads/` (re-copy from backup uploads where missing). (satisfies T008,T009)
- [x] T017 [US1] Write Markdown bundles to `content/blog/...`; run full extraction → 1,508.
  (satisfies T007)
- [x] T018 [US1] Run `pytest` → US1 tests GREEN.

**Checkpoint**: Content + URLs correct and verified.

---

## Phase 4: User Story 2 - Correct taxonomy & metadata (Priority: P2)

### Tests for User Story 2 (WRITE FIRST — MUST FAIL) ⚠️

- [x] T019 [P] [US2] `test_invariants.py::test_taxonomy_real` — every emitted category/tag
  exists in the WP term set; no purely numeric junk tags.
- [x] T020 [US2] Run → confirm RED.

### Implementation for User Story 2

- [x] T021 [US2] Read categories/tags from `_embedded["wp:term"]`; write to front matter; delete
  any title-keyword inference. (satisfies T019)
- [x] T022 [US2] Run `pytest` → US1+US2 GREEN.

**Checkpoint**: Taxonomy faithful to source.

---

## Phase 5: User Story 3 - Reproducible source (Priority: P3)

### Tests for User Story 3 (WRITE FIRST — MUST FAIL) ⚠️

- [x] T023 [P] [US3] `test_build.py::test_hugo_builds` — `hugo --minify` exits 0 from
  `content/` source.
- [x] T024 [US3] Run → confirm RED (until pages/config reconciled).

### Implementation for User Story 3

- [x] T025 [US3] Migrate WP pages (Home, We Repair, We Build, Contact, Gallery); reconcile with
  hand-authored `content/` pages.
- [x] T026 [US3] Fix `hugo.yaml`: `permalinks.blog` to match real structure, `baseURL:
  https://gadjoy.in/`, taxonomies; verify `CNAME`. (satisfies T023)
- [x] T027 [US3] Run `pytest` (all) + `hugo --minify` → fully GREEN.

**Checkpoint**: Site rebuilds from source; all stories green.

---

## Phase 6: Polish & Deploy

- [x] T028 `hugo server` manual click-through: blog list, a post, a category, a tag.
- [x] T029 Spot-check ~15 posts against restored WP at `localhost:8080`.
- [x] T030 Get user approval → commit → ask target branch → open MR.
- [x] T031 (separate) Phase 4: GitHub Pages HTTPS/cert fix for `gadjoy.in`.

---

## Dependencies & Execution Order

- Setup (P1) → Foundational (P2, restore WP) blocks everything.
- US1 tests (T005-T011) before US1 impl (T012-T018).
- US2 after US1 (same extractor); US3 after content exists.
- Within a story: tests written + failing before implementation (Principle I).

## Notes

- Verify RED before writing implementation for each story.
- No commits without user approval; clean messages, no AI attribution.
