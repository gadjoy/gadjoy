# WordPress → Hugo Re-Migration — Summary

**Branch**: `fix/wp-hugo-remigration` · **Date**: 2026-06-03 · **Status**: content complete, all tests green; deploy + HTTPS pending

## What was wrong before
The previous migration corrupted the site:
- Post bodies shredded by a `mysql -e` tab-split bug; categories/tags **fabricated** from title keywords; only built `public/` HTML existed (no Markdown source); inconsistent/broken image paths; and every URL carried a wrong `/blog/` prefix.

## How it was redone (Spec-Driven + TDD)
1. **Spec Kit** (official CLI) scaffolded `.specify/`; authored `constitution.md` + `specs/001-wp-hugo-migration/{spec,plan,tasks}.md`.
2. **Restored the real source**: reassembled the 18-part `.wpress` backup, extracted `database.sql`, imported into Docker MySQL, brought WordPress up. Confirmed **1,508 published posts** and the canonical permalink **`/%year%/%monthnum%/%day%/%postname%/`** (no `/blog/`).
3. **TDD**: wrote a failing `pytest` suite (golden + invariants) first, then implemented the extractor to green.
4. **New extractor** `migration/scripts/wp_rest_to_hugo.py` uses the WP **REST API** (`content.rendered`, real `_embedded` taxonomy, featured media):
   - `markdownify`-based HTML→Markdown (headings, lists, images, **videos**, captions); strips any leftover shortcodes.
   - Normalizes every media URL (any host) → canonical `/img/uploads/...`.
   - Front matter from real fields only; **no fabricated taxonomy**.
   - Reproduces original URLs via Hugo permalinks; adds `aliases` to rescue the old broken `/blog/...` URLs (verified: they emit redirect stubs).
   - Drops references to 3 media files that never existed in the backup (logged in `migration/missing_media_report.txt`).
5. **Config**: `hugo.yaml` → `baseURL: https://gadjoy.in/`, `permalinks.blog: /:year/:month/:day/:slug/`, goldmark `unsafe: true` (for `<video>`).

## Verification (all green)
- `pytest` (22 tests): post count == 1508, no WP artifacts, real taxonomy, every media ref resolves on disk, URL parity + alias redirects, `hugo --minify` builds clean.
- Built site: 1508 articles at `/YYYY/MM/DD/slug/`; legacy `/blog/...` → redirect to canonical; videos render.

## Known source-data conditions (faithful, not bugs)
- **2** posts are genuinely untitled in WordPress — preserved as-is (not fabricated).
- **3** media references point to files absent from the backup — dropped + logged (live site would 404 on them too).

## Deployment notes / still to do
- CI (`.github/workflows/hugo.yml`) rebuilds with Hugo and deploys the artifact, so committed `public/` is unused — added `.gitignore` for `public/`, `migration/.venv/`, caches. Removing the 14,070 stale tracked `public/` files from git is recommended (pending decision).
- **Phase 4 — HTTPS/cert for gadjoy.in** (GitHub Pages custom domain): verify DNS, add a `CNAME` (`gadjoy.in`) to the deploy, enable "Enforce HTTPS". Sequenced after content cutover.
- Local restored WordPress (Docker `gadjoy` project) left running at `http://localhost:8080` for spot-checking; `cd wordpress && docker compose down -v` to tear down.

## Key files
- `migration/scripts/wp_rest_to_hugo.py`, `migration/tests/**`, `specs/001-wp-hugo-migration/**`, `.specify/memory/constitution.md`, `hugo.yaml`, `content/blog/**` (1508 bundles).
