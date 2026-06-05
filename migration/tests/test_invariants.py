"""Invariant / acceptance tests over the GENERATED content/blog source.

These encode the spec's measurable success criteria (SC-001..SC-005) and the
constitution's principles II-V. They run against the Markdown the extractor
writes, so they are RED until Phase 2 has run the migration.
"""
import re

import pytest

from conftest import (
    EXPECTED_PUBLISHED_POSTS,
    FORBIDDEN_SUBSTRINGS,
    SHORTCODE_RE,
    STATIC_DIR,
    media_paths_in,
)

pytestmark = pytest.mark.generated


@pytest.fixture(scope="module")
def posts(generated_posts):
    if not generated_posts:
        pytest.fail("No generated posts found under content/blog — run the migration (Phase 2).")
    return generated_posts


# SC-001 -----------------------------------------------------------------------
def test_post_count_matches_source(posts):
    assert len(posts) == EXPECTED_PUBLISHED_POSTS


# SC-002 -----------------------------------------------------------------------
def test_no_wordpress_artifacts(posts):
    for path, _fm, body in posts:
        for bad in FORBIDDEN_SUBSTRINGS:
            assert bad not in body, f"{path}: forbidden {bad!r}"
        assert not SHORTCODE_RE.search(body), f"{path}: leftover shortcode"


# Front matter integrity (Principle III) --------------------------------------
def test_front_matter_complete(posts):
    # 2 of the 1508 WP posts are genuinely untitled at the source; faithful
    # migration preserves the empty title rather than fabricating one (Principle III).
    for path, fm, _body in posts:
        assert "title" in fm and isinstance(fm["title"], str), f"{path}: title key missing"
        assert fm.get("date"), f"{path}: missing date"
        assert fm.get("slug"), f"{path}: missing slug"
        assert isinstance(fm.get("categories"), list), f"{path}: categories not a list"
        assert isinstance(fm.get("aliases"), list), f"{path}: aliases not a list"


# SC-004 : URL preservation ----------------------------------------------------
def test_url_preservation_via_aliases(posts):
    """Canonical URL is the original /YYYY/MM/DD/slug/ (via permalink config);
    aliases must rescue the broken live site's /blog/YYYY/MM/DD/slug/ path."""
    for path, fm, _body in posts:
        date = str(fm["date"])
        y, m, d = date[0:4], date[5:7], date[8:10]
        slug = fm["slug"]
        legacy = f"/blog/{y}/{m}/{d}/{slug}/"
        assert legacy in fm["aliases"], f"{path}: legacy alias {legacy} missing"


# SC-005 : real taxonomy, no fabricated/junk terms -----------------------------
def test_taxonomy_is_real(posts, wp_terms):
    cat_names = {c["name"] for c in wp_terms["categories"]}
    tag_names = {t["name"] for t in wp_terms["tags"]}
    numeric_junk = re.compile(r"^[\d\W]+$")
    for path, fm, _body in posts:
        for c in fm.get("categories", []):
            assert c in cat_names, f"{path}: fabricated category {c!r}"
        for t in fm.get("tags", []) or []:
            assert t in tag_names, f"{path}: fabricated tag {t!r}"
            assert not numeric_junk.match(t), f"{path}: numeric junk tag {t!r}"


# Card rendering: real thumbnail + plain-text summary (homepage/list grid) ------
def test_card_banner_present_and_resolves(posts):
    img_re = re.compile(r"/img/uploads/[^\s\)\"'<>]+\.(?:png|jpe?g|gif|webp)", re.I)
    for path, fm, body in posts:
        if img_re.search(body):
            assert fm.get("banner"), f"{path}: post has images but no card banner"
        banner = fm.get("banner")
        if banner:
            rel = banner[len("/img/"):]
            assert (STATIC_DIR / "img" / rel).exists(), f"{path}: banner missing on disk {banner}"


def test_summary_is_plaintext(posts):
    for path, fm, _body in posts:
        s = fm.get("summary")
        if s:
            assert "<" not in s, f"{path}: summary contains HTML (would overflow card)"


# SC-003 : every referenced media file exists on disk --------------------------
def test_all_media_resolve_on_disk(posts):
    missing = []
    for path, _fm, body in posts:
        for ref in media_paths_in(body):
            rel = ref[len("/img/"):]            # -> uploads/YYYY/MM/file
            if not (STATIC_DIR / "img" / rel).exists():
                missing.append((str(path), ref))
    assert not missing, f"{len(missing)} broken media refs, e.g. {missing[:5]}"
