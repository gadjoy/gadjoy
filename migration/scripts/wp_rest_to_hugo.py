#!/usr/bin/env python3
"""WordPress -> Hugo migration via the WordPress REST API.

Source of truth: a locally-restored WordPress (imported from the .wpress backup)
serving its REST API at http://localhost:8080. The REST API returns *rendered*
HTML (Gutenberg/shortcodes already expanded), the real taxonomy, and featured
media — which is why this replaces the old regex/SQL `wp_to_hugo.py`.

Guarantees (verified by migration/tests/):
  * real categories/tags from the WP taxonomy (never inferred from titles)
  * lossless HTML->Markdown (headings, lists, images, videos, captions)
  * no leftover WordPress artifacts (<!-- wp: -->, shortcodes, wp-block- classes)
  * a single canonical media path: /img/uploads/...
  * original URLs preserved via Hugo permalinks; legacy /blog/ URLs rescued via aliases
"""
import argparse
import html
import re
import shutil
import sys
from pathlib import Path

import requests
import yaml
from bs4 import BeautifulSoup
from markdownify import MarkdownConverter

REPO_ROOT = Path(__file__).resolve().parents[2]
CONTENT_DIR = REPO_ROOT / "content"
CONTENT_BLOG_DIR = CONTENT_DIR / "blog"
STATIC_UPLOADS_DIR = REPO_ROOT / "static" / "img" / "uploads"
MISSING_MEDIA_REPORT = REPO_ROOT / "migration" / "missing_media_report.txt"
DEFAULT_BASE_URL = "http://localhost:8080"

# attributes that carry media URLs we must normalize
_URL_ATTRS = ("src", "href", "poster")
# noisy presentation attributes stripped before conversion
_DROP_ATTRS = ("srcset", "sizes", "loading", "decoding", "class", "width",
               "height", "style", "id", "data-id", "aria-describedby")

_UPLOADS_RE = re.compile(r"/wp-content/uploads/(.+)$")
# leftover WordPress shortcodes — scoped to known names so real bracketed text
# (e.g. code, footnote markers) is never touched.
_SHORTCODE_RE = re.compile(
    r"\[/?(?:caption|gallery|et_pb_[a-z_]*|vc_[a-z_]*|embed|audio|playlist)[^\]]*\]"
)


def normalize_media_url(url: str) -> str:
    """Rewrite any host's /wp-content/uploads/<rel> to canonical /img/uploads/<rel>."""
    if not url:
        return url
    if url.startswith("/img/uploads/"):
        return url
    m = _UPLOADS_RE.search(url)
    return "/img/uploads/" + m.group(1) if m else url


class _WPConverter(MarkdownConverter):
    """markdownify converter that preserves <video> (markdown has no video tag)."""

    def convert_video(self, el, text, *args, **kwargs):
        src = el.get("src", "")
        if not src and el.find("source"):
            src = el.find("source").get("src", "")
        return f"\n\n<video controls src=\"{src}\"></video>\n\n" if src else ""

    def convert_source(self, el, text, *args, **kwargs):
        return ""


def html_to_markdown(rendered_html: str) -> str:
    """Convert WP rendered HTML to clean Markdown with normalized media URLs."""
    soup = BeautifulSoup(rendered_html or "", "html.parser")
    for tag in soup.find_all(True):
        for attr in _URL_ATTRS:
            if tag.has_attr(attr):
                tag[attr] = normalize_media_url(tag[attr])
        for attr in _DROP_ATTRS:
            if tag.has_attr(attr):
                del tag[attr]
    md = _WPConverter(heading_style="ATX", bullets="-").convert_soup(soup)
    md = _SHORTCODE_RE.sub("", md)            # drop any leftover WP shortcodes
    md = re.sub(r"\n{3,}", "\n\n", md)        # collapse blank-line runs
    return md.strip()


_MEDIA_REF_RE = re.compile(r"/img/uploads/[^\s\)\"'<>]+")


def prune_missing_media(md: str, uploads_dir: Path = STATIC_UPLOADS_DIR):
    """Remove references to media files that do not exist on disk.

    The WordPress DB references a few attachment files that were never stored in
    the backup (e.g. '-1'/'-edited' variants). We never invent or guess a
    substitute — we drop the dangling pointer so the built site has zero 404s,
    and return the list of dropped refs for reporting (Principle V).
    """
    missing = []
    for ref in set(_MEDIA_REF_RE.findall(md)):
        rel = ref[len("/img/uploads/"):]
        if not (uploads_dir / rel).exists():
            missing.append(ref)
            esc = re.escape(ref)
            md = re.sub(rf"!\[[^\]]*\]\({esc}\)\n*", "", md)          # markdown image
            md = re.sub(rf"<video[^>]*src=\"{esc}\"[^>]*>\s*</video>\n*", "", md)  # video
    md = re.sub(r"\n{3,}", "\n\n", md).strip()
    return md, missing


def _strip_tags(s: str) -> str:
    return html.unescape(re.sub(r"<[^>]+>", "", s or "")).strip()


def _terms(post: dict, taxonomy: str):
    """Real WP terms for a post: list of {slug, name}. Slugs are URL-safe and
    distinguish categories that share a display name (e.g. two 'Water Damage')."""
    out = []
    for grp in post.get("_embedded", {}).get("wp:term", []):
        for t in grp:
            if t.get("taxonomy") == taxonomy:
                out.append({"slug": t["slug"], "name": html.unescape(t["name"])})
    return out


def _exists(url: str) -> bool:
    return bool(url) and url.startswith("/img/uploads/") and \
        (STATIC_UPLOADS_DIR / url[len("/img/uploads/"):]).exists()


def _featured(post: dict):
    for media in post.get("_embedded", {}).get("wp:featuredmedia", []):
        if isinstance(media, dict) and media.get("source_url"):
            return normalize_media_url(media["source_url"])
    return None


def _first_existing_image(rendered_html: str):
    """First in-content image whose file exists on disk — used as the card thumbnail."""
    soup = BeautifulSoup(rendered_html or "", "html.parser")
    for img in soup.find_all("img"):
        src = normalize_media_url(img.get("src", ""))
        if _exists(src):
            return src
    return None


def _card_banner(post: dict):
    """Pick a real thumbnail: the featured image if present+on-disk, else the
    first in-content image that resolves. The theme renders .Params.banner."""
    feat = _featured(post)
    if _exists(feat):
        return feat
    return _first_existing_image(post["content"]["rendered"])


def build_front_matter(post: dict) -> dict:
    """Front matter built only from real source fields (Principle III)."""
    date = post["date"]                       # local post_date — drives the permalink
    y, m, d = date[0:4], date[5:7], date[8:10]
    slug = post["slug"]
    fm = {
        "title": html.unescape(post["title"]["rendered"]),
        "date": date,
        "slug": slug,
        "draft": False,
        # store real WP slugs; display names live on the term pages (Hugo links
        # by slug, so this avoids the '&'->double-hyphen mismatch that 404'd).
        "categories": [t["slug"] for t in _terms(post, "category")],
        "tags": [t["slug"] for t in _terms(post, "post_tag")],
        # canonical URL == original WP URL (via permalink config); rescue the
        # broken live site's /blog/ path so old inbound links don't 404.
        "aliases": [f"/blog/{y}/{m}/{d}/{slug}/"],
    }
    desc = _strip_tags(post.get("excerpt", {}).get("rendered", ""))
    if desc:
        fm["description"] = desc
        fm["summary"] = desc          # plain-text card summary (avoids image overflow)
    banner = _card_banner(post)
    if banner:
        fm["banner"] = banner         # card/list thumbnail (theme reads .Params.banner)
    return fm


def render_post(post: dict):
    fm = build_front_matter(post)
    date = post["date"]
    y, m, d = date[0:4], date[5:7], date[8:10]
    post_dir = CONTENT_BLOG_DIR / y / m / d / post["slug"]
    body = html_to_markdown(post["content"]["rendered"])
    body, missing = prune_missing_media(body)
    front = yaml.safe_dump(fm, allow_unicode=True, sort_keys=False, width=10000).strip()
    terms = {"categories": _terms(post, "category"), "tags": _terms(post, "post_tag")}
    return post_dir / "index.md", f"---\n{front}\n---\n\n{body}\n", missing, terms


def write_term_pages(term_titles: dict):
    """Create content/<taxonomy>/<slug>/_index.md carrying the real display name,
    so taxonomy archives resolve at clean slugs and links show human titles."""
    for taxonomy, mapping in term_titles.items():
        for slug, name in sorted(mapping.items()):
            d = CONTENT_DIR / taxonomy / slug
            d.mkdir(parents=True, exist_ok=True)
            front = yaml.safe_dump({"title": name}, allow_unicode=True).strip()
            (d / "_index.md").write_text(f"---\n{front}\n---\n", encoding="utf-8")


def fetch_all_posts(base_url: str):
    posts, page = [], 1
    while True:
        r = requests.get(
            f"{base_url}/?rest_route=/wp/v2/posts",
            params={"per_page": 100, "page": page, "_embed": 1},
            timeout=120,
        )
        if r.status_code != 200:
            break
        batch = r.json()
        if not isinstance(batch, list) or not batch:
            break
        posts.extend(batch)
        total_pages = int(r.headers.get("X-WP-TotalPages", page))
        if page >= total_pages:
            break
        page += 1
    return posts


def migrate(base_url: str = DEFAULT_BASE_URL, clean: bool = True) -> int:
    if clean:
        for d in (CONTENT_BLOG_DIR, CONTENT_DIR / "categories", CONTENT_DIR / "tags"):
            if d.exists():
                shutil.rmtree(d)
    CONTENT_BLOG_DIR.mkdir(parents=True, exist_ok=True)

    posts = fetch_all_posts(base_url)
    print(f"Fetched {len(posts)} posts from {base_url}")
    all_missing = []
    term_titles = {"categories": {}, "tags": {}}
    for post in posts:
        path, content, missing, terms = render_post(post)
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(content, encoding="utf-8")
        all_missing += [(post["slug"], ref) for ref in missing]
        for t in terms["categories"]:
            term_titles["categories"][t["slug"]] = t["name"]
        for t in terms["tags"]:
            term_titles["tags"][t["slug"]] = t["name"]
    write_term_pages(term_titles)
    print(f"Wrote {len(posts)} blog post bundles to {CONTENT_BLOG_DIR}")
    print(f"Wrote {len(term_titles['categories'])} category + {len(term_titles['tags'])} tag term pages")
    if all_missing:
        lines = [f"{slug}\t{ref}" for slug, ref in sorted(all_missing)]
        MISSING_MEDIA_REPORT.write_text(
            "Media referenced by WordPress but absent from the backup (dropped):\n"
            + "\n".join(lines) + "\n", encoding="utf-8")
        print(f"Dropped {len(all_missing)} dangling media refs -> {MISSING_MEDIA_REPORT}")
    return len(posts)


if __name__ == "__main__":
    ap = argparse.ArgumentParser(description="Migrate WordPress posts to Hugo via REST API")
    ap.add_argument("--base-url", default=DEFAULT_BASE_URL)
    ap.add_argument("--no-clean", action="store_true", help="do not wipe content/blog first")
    args = ap.parse_args()
    n = migrate(args.base_url, clean=not args.no_clean)
    sys.exit(0 if n else 1)
