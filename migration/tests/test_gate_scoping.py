"""Prove the re-scoped migration gate still catches what it was built to catch.

`test_post_count_matches_source` used to assert `len(posts) == 1508`. That forbade
publishing anything ever again — the gate that was supposed to protect the migration also
froze the blog. It is now scoped to *migrated* posts (those with no `origin` key).

Loosening an acceptance gate is exactly where a suite quietly stops working, so these tests
attack the new version directly: they hand doctored post lists to the real invariant
functions and require them to fail. Without this, "migrated == 1508" could be satisfied by
the wrong 1,508 posts and nobody would know.
"""
from pathlib import Path

import pytest

from conftest import EXPECTED_PUBLISHED_POSTS, is_migrated

# Import the MODULE, not the names. `from test_invariants import test_x` would make pytest
# collect those functions again here, where the fixtures they need do not exist.
import test_invariants as inv

pytestmark = pytest.mark.generated

MIGRATED_FM = {
    "title": "x", "date": "2024-01-01T00:00:00", "slug": "x",
    "categories": ["repair"], "aliases": ["/blog/2024/01/01/x/"],
}
DECK_FM = dict(MIGRATED_FM, origin="deck", aliases=[])


def _fake(fm, body=""):
    return (Path("content/blog/2024/01/01/x/index.md"), dict(fm), body)


# --- the discriminator -------------------------------------------------------
def test_is_migrated_discriminates_on_origin():
    assert is_migrated({}) is True, "a post with no origin key is a migrated post"
    assert is_migrated({"origin": "deck"}) is False
    assert is_migrated({"origin": ""}) is True, "an empty origin must not exempt a post"


# --- the gate still bites ----------------------------------------------------
def test_losing_a_migrated_post_still_fails(generated_posts):
    migrated = [p for p in generated_posts if is_migrated(p[1])]
    assert len(migrated) == EXPECTED_PUBLISHED_POSTS, "precondition: corpus is intact"
    with pytest.raises(AssertionError):
        inv.test_post_count_matches_source(migrated[:-1])


def test_a_new_post_cannot_substitute_for_a_lost_migrated_one(generated_posts):
    """The failure mode the re-scoping could have introduced.

    If the count were still a bare total, deleting a migrated post and publishing a new
    repair would net to 1,508 and pass. Scoping to migrated posts is what prevents that,
    so it is asserted rather than assumed.
    """
    migrated = [p for p in generated_posts if is_migrated(p[1])]
    padded = migrated[:-1] + [_fake(DECK_FM)]
    assert len(padded) == EXPECTED_PUBLISHED_POSTS, "precondition: totals net out"
    with pytest.raises(AssertionError):
        inv.test_post_count_matches_source(padded)


def test_gaining_a_migrated_post_still_fails(generated_posts):
    """Guards the other direction: an `origin`-less post appearing from nowhere."""
    migrated = [p for p in generated_posts if is_migrated(p[1])]
    with pytest.raises(AssertionError):
        inv.test_post_count_matches_source(migrated + [_fake(MIGRATED_FM)])


# --- new posts are exempt from ONE rule only ---------------------------------
def test_new_posts_are_exempt_from_legacy_aliases_only(generated_posts):
    """`aliases: []` is legitimate for a deck post and only for a deck post."""
    inv.test_url_preservation_via_aliases([_fake(DECK_FM)])          # must not raise
    with pytest.raises(AssertionError):
        inv.test_url_preservation_via_aliases([_fake(dict(MIGRATED_FM, aliases=[]))])


def test_new_posts_still_bound_by_every_other_invariant():
    """The exemption must not become a general amnesty. A deck post with a WordPress
    artifact, or a banner that does not resolve, must fail exactly like a migrated one."""
    with pytest.raises(AssertionError):
        inv.test_no_wordpress_artifacts([_fake(DECK_FM, "leftover <!-- wp:paragraph -->")])

    with pytest.raises(AssertionError):
        inv.test_card_banner_present_and_resolves([
            _fake(dict(DECK_FM, banner="/img/uploads/2026/08/does-not-exist.webp"))
        ])

    with pytest.raises(AssertionError):
        # has images but no banner -> no card thumbnail
        inv.test_card_banner_present_and_resolves([
            _fake(DECK_FM, "![](/img/uploads/2025/02/image.webp)")
        ])
