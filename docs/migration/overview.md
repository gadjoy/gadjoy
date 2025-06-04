# Migration Understanding and Rules of Engagement

This document provides:

- **Migration Understanding**: A high‑level overview of how the Gadjoy Repair Service site was migrated from WordPress to Hugo.
- **Rules of Engagement**: A clear, step‑by‑step workflow you can follow to run, update, or extend this migration process.

## Rules of Engagement (Workflow Summary)

1. **Spin up local WordPress** via Docker → import the `.wpress` backup.
2. **Extract raw content and media** from `migration/wp-export`.
3. **Run migration scripts** (`migration/scripts/`) to convert posts/pages to Hugo‑compatible Markdown, update front matter, and copy images into `static/img/uploads`.

   - **Note:** The migration scripts now auto‑detect your Docker DB container (service `db`) and connect via `docker exec`. Ensure your WordPress stack is running (e.g., `docker compose up`) before step 3.
4. **Configure Hugo**:
   - Update `hugo.yaml` (site params, menus, taxonomies, permalinks).
   - Ensure `archetypes/default.md` defines the desired front matter.
   - Populate `data/` with any YAML‑driven components (carousel, features, testimonials).
   - Adjust or extend layouts under `layouts/` as needed.
5. **Place content** under `content/` following the Hugo directory structure (`_index.md`, services, contact, gallery, blog/YYYY/MM/DD/slug/index.md`).
6. **Build the site** with Hugo → static output in `public/`.
7. **Deploy** the contents of `public/` (e.g., Netlify, GitHub Pages).

## 1. WordPress Local‑Dev & Export (`wordpress/`)

A Docker‑Compose setup to spin up the original WordPress site locally, import the backup via All‑In‑One WP Migration, and extract content & media.

```text
wordpress/
├── Dockerfile
├── docker‑compose.yml
├── copy‑plugins.sh
└── linux‑file‑split‑and‑merge‑guide.md
```

- **`wordpress-local-dev-setup-guide-gadjoy.md`**: Steps to set up WordPress + MySQL + phpMyAdmin, import the `.wpress` backup, and reset admin credentials.
- **`linux-file-split-and-merge-guide.md`**: How to split and reassemble large export files with `split`/`cat`.

## 2. Raw Export & Migration Scripts (`migration/`)

After importing WordPress locally, the raw WP content and media live under `migration/wp-export/`. Python scripts in `migration/scripts/` convert them into Hugo‑ready Markdown with front matter and update asset paths.

```text
migration/
├── wp-export/
│   ├── posts.json
│   └── wp-content/…
└── scripts/
    ├── wp_to_hugo.py
    ├── fix_frontmatter.py
    ├── fix_image_paths.py
    └── universal_theme_optimization.py
```

- **`wp_to_hugo.py`**: Queries MySQL, transforms Gutenberg blocks, builds front matter, writes posts under `content/blog/…`, copies uploads to `static/img/uploads`, and updates image URLs.
- **Cleanup scripts** (`fix_frontmatter.py`, `fix_image_paths.py`, `universal_theme_optimization.py`, etc.) normalize metadata, audit/fix missing images, and apply theme‑specific tweaks.

## 3. The New Hugo Site

### 3.1 Configuration (`hugo.yaml`)

Defines site parameters (baseURL, theme, menu, taxonomies, permalinks) and global settings (analytics, maps, address, etc.).

### 3.2 Archetypes (`archetypes/default.md`)

Default front matter skeleton for new posts.

### 3.3 Data‑Driven Components (`data/`)

YAML‑driven carousels, feature panels, and testimonials powering the homepage (`data/carousel/`, `data/features/`, `data/testimonials/`).

### 3.4 Custom Layouts (`layouts/`)

Theme overrides for blog listings and partials (e.g., `layouts/blog/list.html`, `layouts/partials/sidebar.html`).

### 3.5 Content Organization (`content/`)

Structured sections:
- Homepage: `content/_index.md`
- Services: `content/services/we-repair/` & `content/services/we-build/`
- Contact: `content/contact/index.md`
- Gallery: `content/gallery/index.md`
- Blog posts: `content/blog/YYYY/MM/DD/slug/index.md`

### 3.6 Static Assets (`static/`)

Static files (CSS/JS/images), including original WordPress uploads under `static/img/uploads/`.

## 4. Generated Output (`public/`)

The built Hugo static site (HTML, CSS, JS) ready for deployment.

## 5. Next Steps

For details on the missing images path normalization issue and how to apply the fix, see [Missing Images Issue and Fix](missing-images-issue.md).

To audit which posts still lack images and link back to the original WordPress posts for verification, see [Posts Missing Images Report (2025-02-18)](missing-images-2025-02-18.md).

To handle Gutenberg gallery blocks (multi‑image side‑by‑side layouts) correctly, see [Gallery Block Conversion Issue and Fix](gallery-block-conversion.md).