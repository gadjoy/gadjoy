"""Tests for the intake side of publishing: how a deck is located in an issue.

Network calls are not exercised here — the routing logic is, because that is where a deck
silently goes missing. An issue whose deck cannot be found must produce a failure the team can
act on, never a quiet no-op.
"""
import sys

import pytest

from conftest import REPO_ROOT

sys.path.insert(0, str(REPO_ROOT / "tools"))


@pytest.fixture(scope="module")
def pd():
    import publish_decks
    return publish_decks


ISSUE_WITH_REPO_DECK = """Week ending: 2026-08-09

Deck uploaded to `decks/2026-08-09-week.pptx` (rehearsal run).
"""

ISSUE_WITH_ATTACHMENT = """Week ending: 2026-08-09

[week.pptx](https://github.com/user-attachments/files/12345678/week.pptx)
"""

ISSUE_WITH_NEITHER = "Week ending: 2026-08-09\n\nForgot to attach it, sorry.\n"


def test_repo_committed_deck_is_found(pd):
    assert pd.REPO_DECK_RE.findall(ISSUE_WITH_REPO_DECK) == ["decks/2026-08-09-week.pptx"]


def test_issue_attachment_is_found(pd):
    urls = pd.ATTACHMENT_RE.findall(ISSUE_WITH_ATTACHMENT)
    assert len(urls) == 1 and urls[0].endswith("week.pptx")


def test_a_body_with_no_deck_matches_nothing(pd):
    """Must yield nothing so the caller can report a failure, rather than matching loosely."""
    assert not pd.REPO_DECK_RE.findall(ISSUE_WITH_NEITHER)
    assert not pd.ATTACHMENT_RE.findall(ISSUE_WITH_NEITHER)


def test_week_ending_date_is_extracted(pd):
    assert pd.WEEK_RE.search(ISSUE_WITH_REPO_DECK).group(1) == "2026-08-09"


def test_non_pptx_attachments_are_ignored(pd):
    """A photo dragged into the issue alongside the deck must not be treated as a deck."""
    body = ("Week ending: 2026-08-09\n"
            "![photo](https://github.com/user-attachments/files/999/bench.jpg)\n")
    urls = [u for u in pd.ATTACHMENT_RE.findall(body)
            if u.lower().endswith((".pptx", ".ppt"))]
    assert urls == []


def test_report_lines_surface_redactions_and_resurfaced_slugs(pd):
    """The issue comment is the only channel that teaches the About-screen rule, so both
    warnings must actually appear in it."""
    class FakePost:
        slug = "redmi-note-7-charging-port-replacement-2"

    class FakeReport:
        published = [FakePost()]
        already_present = []
        skipped = []
        redactions = {"redmi-note-7-charging-port-replacement-2": ["imei_keyword"]}
        resurfaced_slugs = ["redmi-note-7-charging-port-replacement-2"]

    text = "\n".join(pd.report_lines("week.pptx", FakeReport()))
    assert "redacted customer identifiers" in text
    assert "About screen" in text, "the comment must say what to do differently next time"
    assert "already exist on the site" in text
