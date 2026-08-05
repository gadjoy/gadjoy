# Gadjoy Repair Service — Website

The website for **Gadjoy Repair Service**, a device-repair shop in Bangalore
(phones, laptops, tablets, desktops). Live at **https://gadjoy.in/**.

It's a [Hugo](https://gohugo.io/) static site (theme: `hugo-universal-theme`, customised to a
monochrome black-and-white look) whose blog of ~1,500 repair case-studies was **migrated from the
original WordPress site**. It's deployed to **GitHub Pages** via GitHub Actions.

## Highlights
- ~1,500 repair posts migrated faithfully from WordPress (real content, taxonomy, original URLs).
- Custom hero, animated stats, horizontally-scrolling "recent repairs", and a before/after gallery
  lightbox — all via project-level layout overrides + `static/css/custom.css` (no theme fork).
- **Live Google reviews** pulled from the Google Places API at build time.
- A reproducible migration + media-optimization pipeline under `migration/`.

## Repository layout
```
content/            Markdown content
  _index.md         homepage copy
  services/         we-repair, we-build (interactive pages)
  contact/          contact page (custom layout)
  gallery/          before/after lightbox wall
  blog/YYYY/MM/DD/slug/index.md   ~1,500 migrated repair posts
data/features/      homepage feature cards (YAML)
layouts/            project overrides of the theme (no fork)
  partials/carousel.html        hero + animated stat counters
  partials/recent_posts.html    horizontal "Real Repairs" scroller
  partials/testimonials.html    live Google reviews (+ proof fallback)
  partials/custom_headers.html  fonts, favicon, count-up + lightbox JS, floating WhatsApp/Call
  _default/gallery.html         gallery lightbox wall
static/
  css/custom.css    the entire visual theme (navy→mono black/white)
  img/uploads/      repair photos/videos (WebP + mp4, optimised — see below)
  img/logo*, favicon.ico, apple-touch-icon.png
migration/          WordPress→Hugo tooling
  scripts/wp_rest_to_hugo.py    REST-API extractor (source of truth = restored WP)
  scripts/optimize_media.py     prune + WebP + video re-encode (artifact slimming)
  tests/            pytest suite (the acceptance gate)
  requirements.txt, .venv/      (venv gitignored)
wordpress/          Dockerised WordPress + .wpress backup (to restore the source DB)
```

## Local development
```bash
# Serve the site (live reviews need the API key; without it a proof fallback shows)
GOOGLE_PLACES_API_KEY=... hugo server         # http://localhost:1313/

# Run the test suite (the same gate CI runs)
cd migration && python -m venv .venv && .venv/bin/pip install -r requirements.txt
.venv/bin/python -m pytest -q                  # 32 tests; needs no WordPress running

# Smoke-test a deployed site from the outside
./scripts/smoke.sh https://gadjoy.in
```

Install the Hugo version pinned in **`.hugo-version`** (currently `0.147.2`) — CI reads that
same file, and `test_toolchain.py` fails on a minor-version gap. A dev/prod Hugo mismatch has
already caused one production-only bug (the contact page fell back to the theme layout).

## Testing
Two layers, both under `migration/tests/`, both offline (fixtures are captured, not fetched):

- **Source invariants** — `test_invariants.py`, `test_conversion.py`, `test_golden.py`: the
  migration's acceptance gate (1,508 posts, no WordPress artifacts, URL parity, every media
  reference resolving *case-sensitively* — `Path.exists()` is case-blind on macOS, which is
  how two images shipped 404ing).
- **Build output** — `test_site_output.py`: assertions over the rendered HTML, one per bug
  class that has actually reached production (contact layout fallback, form wiring, broken
  internal references, a headline claim not traceable to `hugo.yaml`). Each guard has been
  mutation-tested: reintroduce the bug, confirm the test fails.

`scripts/smoke.sh` then checks the deployed origin, because a green build is not evidence the
site works — both the layout fallback and the dead contact form returned HTTP 200 throughout.

## Live Google reviews
`layouts/partials/testimonials.html` fetches the Places API at build time using the
`GOOGLE_PLACES_API_KEY` environment variable (and `params.googlePlaceId` in `hugo.yaml`). The key is
**never committed** — it's read from the env locally and from a GitHub Actions **repository secret**
of the same name in CI. If the key is absent or the request fails, the section renders a verifiable
"proof" fallback instead, so the build never breaks.

## Migration & media pipeline (`migration/`)
The blog is reproducible from the original WordPress backup:
1. Restore WordPress locally: `cd wordpress && docker compose up -d`, import the `.wpress` backup
   (`wordpress/backup/`) — see `wordpress/wordpress-local-dev-setup-guide-gadjoy.md`.
2. Extract to Hugo Markdown: `migration/scripts/wp_rest_to_hugo.py` (reads the WP REST API → real
   content, taxonomy as slugs + term pages, normalised `/img/uploads/...` paths, original URLs with
   `/blog/...` aliases).
3. Optimise media: `migration/scripts/optimize_media.py` — prunes upload files the site doesn't
   reference, converts images to **WebP** (and rewrites references), and re-encodes videos. Run this
   **after** any re-migration. Originals remain recoverable from `wordpress/backup/` and
   `migration/wp-export/`.

## Deployment
`.github/workflows/hugo.yml` runs **test → build → deploy → smoke**:

1. **test** — runs on pull requests *and* pushes to `main`: installs the Hugo version from
   `.hugo-version`, then `pytest`. Nothing deploys unless this passes.
2. **build / deploy** — skipped on pull requests; builds with Hugo (extended) and publishes to
   GitHub Pages. Custom domain **gadjoy.in**, HTTPS enforced.
3. **smoke** — `scripts/smoke.sh` against the live origin after the deploy lands.

> Worth knowing: the `test` job is not yet a *required* status check — that's a branch-protection
> setting in the GitHub UI, not something this repo can commit. Until it's switched on, a red
> suite blocks the deploy but does not block the merge button.

## License
Site content and images are property of Gadjoy Repair Service. The site code/configuration is
available under the MIT license.
