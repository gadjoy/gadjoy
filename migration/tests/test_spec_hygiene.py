"""Spec hygiene: keep the written record honest and complete.

Constitution v2.0.0 requires a spec for anything that adds or reshapes a user-facing
capability. That rule is enforced in two halves:

  - **Was a spec written at all?** A CI step on pull requests: a `feat/*` branch whose
    diff touches no `specs/` file fails. Cannot be checked here — it needs the PR diff.
  - **Is the spec actually usable?** This file. A directory containing an empty
    `spec.md` satisfies the letter of the rule and none of its purpose, so these tests
    assert the parts that make a spec worth having: requirements, success criteria with
    a stated coverage position, and — for retrospectives — what actually shipped.

The point is narrow but real: eleven consecutive PRs shipped with no spec, and `001`'s
task list read 0 of 31 for two months after it was done. Both were invisible because
nothing looked.
"""
import re

import pytest
import yaml

from conftest import REPO_ROOT

SPECS_DIR = REPO_ROOT / "specs"
INDEX = SPECS_DIR / "README.md"
SPEC_DIR_RE = re.compile(r"^\d{3}-[a-z0-9][a-z0-9-]*$")


def spec_dirs():
    if not SPECS_DIR.is_dir():
        return []
    return sorted(p for p in SPECS_DIR.iterdir() if p.is_dir() and SPEC_DIR_RE.match(p.name))


def _frontmatter(md_path):
    text = md_path.read_text(encoding="utf-8")
    if not text.startswith("---"):
        return {}, text
    _, fm, body = text.split("---", 2)
    return yaml.safe_load(fm) or {}, body


# Parametrise over directories so a failure names the offending spec.
ALL_SPECS = spec_dirs()
IDS = [p.name for p in ALL_SPECS]


def test_specs_directory_exists():
    assert ALL_SPECS, "no specs/NNN-slug directories found"


def test_index_exists():
    assert INDEX.is_file(), "specs/README.md (the index) is missing"


@pytest.mark.parametrize("spec", ALL_SPECS, ids=IDS)
def test_spec_has_spec_md(spec):
    assert (spec / "spec.md").is_file(), f"{spec.name}/spec.md is missing"


@pytest.mark.parametrize("spec", ALL_SPECS, ids=IDS)
def test_spec_frontmatter_declares_status(spec):
    fm, _ = _frontmatter(spec / "spec.md")
    assert fm.get("description"), f"{spec.name}/spec.md frontmatter has no description"
    status = fm.get("status")
    assert status in {"shipped", "in-progress", "draft"}, (
        f"{spec.name}/spec.md frontmatter status must be shipped|in-progress|draft, got {status!r}"
    )
    if status == "shipped":
        assert fm.get("shipped"), f"{spec.name}: status is shipped but no shipped date"
        assert fm.get("prs"), f"{spec.name}: status is shipped but no prs listed"


@pytest.mark.parametrize("spec", ALL_SPECS, ids=IDS)
def test_spec_states_requirements_and_coverage(spec):
    """A spec without requirements is a wish; without a coverage position it hides its
    own gaps. The coverage table is what feeds the Tests Owed register in the index."""
    _, body = _frontmatter(spec / "spec.md")
    assert re.search(r"^##+\s+Requirements", body, re.M), (
        f"{spec.name}/spec.md has no Requirements section"
    )
    assert re.search(r"\bFR-\d{3}\b", body), f"{spec.name}/spec.md has no FR-nnn requirements"
    assert re.search(r"^##+\s+Success Criteria", body, re.M), (
        f"{spec.name}/spec.md has no Success Criteria section"
    )
    assert re.search(r"\bSC-\d{3}\b", body), f"{spec.name}/spec.md has no SC-nnn criteria"
    # Every criterion must state whether it is guarded. "OWED" is an acceptable answer;
    # silence is not.
    assert ("OWED" in body) or ("✅" in body), (
        f"{spec.name}/spec.md success criteria state no test-coverage position — mark each "
        f"criterion with the test that guards it, or OWED"
    )


@pytest.mark.parametrize("spec", ALL_SPECS, ids=IDS)
def test_forward_specs_carry_plan_and_tasks(spec):
    """A spec written before the work keeps the full Spec Kit set. A retrospective one
    documents what shipped instead — see specs/README.md for why."""
    fm, _ = _frontmatter(spec / "spec.md")
    if fm.get("backfilled"):
        return
    for artifact in ("plan.md", "tasks.md"):
        assert (spec / artifact).is_file(), (
            f"{spec.name}/{artifact} is missing. Specs written before implementation carry "
            f"spec -> plan -> tasks; mark the spec `backfilled:` in frontmatter if it is a "
            f"retrospective record instead."
        )


@pytest.mark.parametrize("spec", ALL_SPECS, ids=IDS)
def test_backfilled_specs_record_what_shipped(spec):
    fm, body = _frontmatter(spec / "spec.md")
    if not fm.get("backfilled"):
        return
    assert re.search(r"^##+\s+As Built", body, re.M), (
        f"{spec.name}: a backfilled spec must carry an 'As Built' section recording what "
        f"actually shipped and where it diverged from the intent"
    )


@pytest.mark.parametrize("spec", ALL_SPECS, ids=IDS)
def test_spec_is_listed_in_the_index(spec):
    """An unlisted spec is a spec nobody will find."""
    index = INDEX.read_text(encoding="utf-8")
    assert spec.name in index, (
        f"{spec.name} is not referenced in specs/README.md — add it to the index table"
    )


def test_task_lists_are_not_silently_stale():
    """A task list with unchecked boxes must say so out loud.

    `001` read 0 of 31 for two months after shipping. Either the boxes are ticked, or the
    file states plainly that work is outstanding (a Status line, or a 'Deferred' section
    for items parked on purpose).
    """
    stale = []
    for spec in ALL_SPECS:
        tasks = spec / "tasks.md"
        if not tasks.is_file():
            continue
        text = tasks.read_text(encoding="utf-8")
        unchecked = len(re.findall(r"^\s*-\s*\[ \]", text, re.M))
        if not unchecked:
            continue
        declares = re.search(r"^\s*>?\s*\*{0,2}Status", text, re.M) or re.search(
            r"^##+\s+Deferred", text, re.M
        )
        if not declares:
            stale.append((spec.name, unchecked))
    assert not stale, (
        f"task lists with unchecked items and no Status/Deferred declaration: {stale}. "
        f"Tick them, or say in the file what is outstanding and why."
    )
