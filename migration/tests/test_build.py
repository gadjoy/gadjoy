"""US3: the site must rebuild from Markdown source, and a sampled post must be
served at its ORIGINAL WordPress URL (no /blog/ prefix).

Marked `build` (slow). RED until content is migrated and hugo.yaml is fixed.
"""
import subprocess

import pytest

from conftest import CONTENT_BLOG_DIR, REPO_ROOT, iter_generated_posts

pytestmark = pytest.mark.build


def test_hugo_builds_clean(tmp_path):
    hugo = subprocess.run(
        ["hugo", "--minify", "--destination", str(tmp_path / "public"),
         "--logLevel", "warn"],
        cwd=REPO_ROOT, capture_output=True, text=True,
    )
    assert hugo.returncode == 0, f"hugo build failed:\n{hugo.stderr}\n{hugo.stdout}"


def test_sample_post_served_at_original_url(tmp_path):
    """Pick one generated post; assert Hugo emits it at /YYYY/MM/DD/slug/ (no /blog/)."""
    posts = list(iter_generated_posts())
    if not posts:
        pytest.fail("No generated posts — run the migration first.")
    _path, fm, _body = posts[0]
    date = str(fm["date"])
    y, m, d = date[0:4], date[5:7], date[8:10]
    slug = fm["slug"]

    out = tmp_path / "public"
    hugo = subprocess.run(
        ["hugo", "--destination", str(out), "--logLevel", "warn"],
        cwd=REPO_ROOT, capture_output=True, text=True,
    )
    assert hugo.returncode == 0, hugo.stderr
    canonical = out / y / m / d / slug / "index.html"
    assert canonical.exists(), f"post not served at original URL: {canonical}"
    # the full article lives at the canonical URL (not under /blog/)
    assert f"<title>{fm['title']}" in canonical.read_text(encoding="utf-8") \
        or fm["title"] == "", "canonical page is not the full article"
    # the legacy /blog/ URL must be a redirect alias to the canonical, not a duplicate
    legacy = out / "blog" / y / m / d / slug / "index.html"
    if legacy.exists():
        html = legacy.read_text(encoding="utf-8")
        assert "http-equiv=\"refresh\"" in html and f"/{y}/{m}/{d}/{slug}/" in html, \
            "legacy /blog/ path is not a redirect to the canonical URL"
