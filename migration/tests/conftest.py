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


def is_migrated(front_matter: dict) -> bool:
    """True for the 1,508 posts that came from WordPress.

    Migrated posts carry no `origin` key; anything published afterwards declares its
    source (`origin: deck` for the weekly-PowerPoint pipeline). The distinction exists so
    the migration's acceptance criteria stay exact — "1,508 posts, every one reachable at
    its original WordPress URL" — while the blog is still allowed to grow. Without it,
    publishing a single new repair would fail the migration gate, which is how the blog
    came to be frozen since 2025-02 in the first place.
    """
    return not front_matter.get("origin")


def media_ref_resolves(ref: str) -> bool:
    """True if a /img/... reference resolves to a real file, matching case EXACTLY.

    `Path.exists()` is case-INSENSITIVE on macOS, so a post referencing
    `redmi-4-before.webp` when the file is `Redmi-4-Before.webp` passes locally and
    then 404s on the Linux Pages server. That exact bug shipped (two dead images on
    /2021/12/17/redmi-4-dead-condition/) precisely because this check used
    `.exists()`. Compare against the real directory listing so the result is
    identical on every platform.
    """
    target = STATIC_DIR / "img" / ref[len("/img/"):]
    try:
        return target.name in {p.name for p in target.parent.iterdir()}
    except (OSError, FileNotFoundError):
        return False


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


@pytest.fixture(scope="session")
def built_site(tmp_path_factory):
    """Build the whole site ONCE per session; yield the output directory.

    Session-scoped because a full build is ~30s and every build-output test wants
    the same artifact. Deliberately built WITHOUT GOOGLE_PLACES_API_KEY so the
    reviews partial takes its fallback path — that is what CI produces for a PR,
    and the fallback is where a stale hard-coded claim would hide.
    """
    import os
    import subprocess

    out = tmp_path_factory.mktemp("site")
    env = {k: v for k, v in os.environ.items() if k != "GOOGLE_PLACES_API_KEY"}
    proc = subprocess.run(
        ["hugo", "--minify", "--destination", str(out), "--logLevel", "warn"],
        cwd=REPO_ROOT, capture_output=True, text=True, env=env,
    )
    if proc.returncode != 0:
        pytest.fail(f"hugo build failed:\n{proc.stderr}\n{proc.stdout}")
    return out
