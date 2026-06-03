"""Shared fixtures and source-of-truth constants for the migration test suite.

These constants were read directly from the restored WordPress instance
(imported from the 2025-04-25 .wpress backup) during Phase 1a:
  - permalink_structure  : wp_options
  - published post count : SELECT COUNT(*) ... post_type='post' AND post_status='publish'
"""
import json
import re
import sys
from pathlib import Path

import pytest

# --- paths -------------------------------------------------------------------
TESTS_DIR = Path(__file__).resolve().parent
MIGRATION_DIR = TESTS_DIR.parent
REPO_ROOT = MIGRATION_DIR.parent
SCRIPTS_DIR = MIGRATION_DIR / "scripts"
FIXTURES_DIR = TESTS_DIR / "fixtures"
CONTENT_BLOG_DIR = REPO_ROOT / "content" / "blog"
STATIC_DIR = REPO_ROOT / "static"

# make the extractor importable once it exists (Phase 2)
sys.path.insert(0, str(SCRIPTS_DIR))

# --- source-of-truth constants (from restored WP) ---------------------------
WP_BASE_URL = "http://localhost:8080"
PERMALINK_STRUCTURE = "/%year%/%monthnum%/%day%/%postname%/"
EXPECTED_PUBLISHED_POSTS = 1508

# Strings that must NEVER appear in generated post bodies.
FORBIDDEN_SUBSTRINGS = [
    "<!-- wp:",          # Gutenberg block comments
    "wp-block-",         # Gutenberg block CSS classes (raw HTML leaked through)
    "srcset",            # unstripped responsive-image attributes
    "gadjoy.in/wp-content",   # un-normalized absolute image host
    "localhost:8080",    # local dev host leaked into output
    "/wp-content/uploads",    # un-normalized upload path
]
SHORTCODE_RE = re.compile(r"\[(?:caption|gallery|et_pb_|vc_row|embed)[^\]]*\]")


# --- helpers -----------------------------------------------------------------
def split_front_matter(text: str):
    """Return (front_matter_dict, body) for a Hugo Markdown file."""
    import yaml  # local import so collection still works if PyYAML missing
    if not text.startswith("---"):
        return {}, text
    _, fm, body = text.split("---", 2)
    return yaml.safe_load(fm) or {}, body


def iter_generated_posts():
    """Yield (path, front_matter, body) for every generated blog post bundle."""
    for md in CONTENT_BLOG_DIR.rglob("index.md"):
        text = md.read_text(encoding="utf-8")
        fm, body = split_front_matter(text)
        yield md, fm, body


def media_paths_in(body: str):
    """All /img/uploads/... references in a markdown body (img + raw video/src)."""
    return re.findall(r"/img/uploads/[^\s\)\"'<>]+", body)


# --- fixtures ----------------------------------------------------------------
@pytest.fixture(scope="session")
def fixtures_dir():
    return FIXTURES_DIR


@pytest.fixture(scope="session")
def golden_html():
    """Map fixture-slug -> rendered HTML captured from the WP REST API."""
    return {p.stem: p.read_text(encoding="utf-8")
            for p in FIXTURES_DIR.glob("*.html")}


@pytest.fixture(scope="session")
def wp_terms():
    """Real WordPress taxonomy term names/slugs captured from REST."""
    return json.loads((FIXTURES_DIR / "wp_terms.json").read_text(encoding="utf-8"))


@pytest.fixture(scope="session")
def generated_posts():
    posts = list(iter_generated_posts())
    return posts
