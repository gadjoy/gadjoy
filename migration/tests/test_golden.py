"""Golden-fixture tests: convert real captured WP post HTML and assert the
output is clean and faithful (Constitution V).

Fixtures are real `content.rendered` payloads captured from the restored WP
REST API (tests/fixtures/*.html). Property assertions are used rather than
byte-exact matching because real posts contain videos, multi-column layouts,
zero-width spaces and entity noise — exact matching would test markdownify's
formatting quirks, not migration correctness.
"""
import re

import pytest
from conftest import FORBIDDEN_SUBSTRINGS, SHORTCODE_RE

wp = pytest.importorskip(
    "wp_rest_to_hugo",
    reason="extractor not implemented yet (Phase 2) — RED until then",
)


def test_fixtures_exist(golden_html):
    # We captured representative posts: single image, columns, text/video, multi-image, list.
    assert len(golden_html) >= 4


def test_converted_fixtures_are_clean(golden_html):
    for slug, html in golden_html.items():
        md = wp.html_to_markdown(html)
        assert md.strip(), f"{slug}: produced empty markdown"
        for bad in FORBIDDEN_SUBSTRINGS:
            assert bad not in md, f"{slug}: forbidden substring {bad!r} in output"
        assert not SHORTCODE_RE.search(md), f"{slug}: leftover shortcode in output"


def test_all_media_normalized(golden_html):
    """Every image/video URL in converted output is a canonical /img/uploads path."""
    url_re = re.compile(r"!\[[^\]]*\]\(([^)]+)\)|src=\"([^\"]+)\"")
    for slug, html in golden_html.items():
        md = wp.html_to_markdown(html)
        for m in url_re.finditer(md):
            url = m.group(1) or m.group(2)
            if "uploads" in url or url.endswith((".png", ".jpg", ".jpeg", ".mp4", ".webp")):
                assert url.startswith("/img/uploads/"), f"{slug}: bad media url {url}"
