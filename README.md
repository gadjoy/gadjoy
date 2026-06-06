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

# Run the migration/optimization test suite
cd migration && python -m venv .venv && .venv/bin/pip install -r requirements.txt
.venv/bin/python -m pytest -q                  # acceptance gate
```

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
Push to `main` → `.github/workflows/hugo.yml` builds with Hugo (extended, v0.147.2) and publishes to
GitHub Pages. Custom domain **gadjoy.in** is configured in the repo's Pages settings (HTTPS enforced).

## License
Site content and images are property of Gadjoy Repair Service. The site code/configuration is
available under the MIT license.
