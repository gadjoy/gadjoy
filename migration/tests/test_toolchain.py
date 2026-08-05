"""Toolchain parity: dev and CI must build with the same Hugo.

PRs #6 and #7 were one bug in two parts. Production built with Hugo 0.144 while the
site was developed and verified on 0.147, and a template-lookup difference between
them made the contact page silently fall back to the theme's default layout. The
build succeeded and the page returned 200 the whole time.

The version now lives in ONE file, `.hugo-version`, which CI reads. These tests keep
it that way, and warn when the local binary drifts from the pin.
"""
import re
import subprocess

import pytest

from conftest import REPO_ROOT

PIN_FILE = REPO_ROOT / ".hugo-version"
WORKFLOW = REPO_ROOT / ".github" / "workflows" / "hugo.yml"
SEMVER_RE = re.compile(r"^(\d+)\.(\d+)\.(\d+)$")


def _pinned():
    assert PIN_FILE.exists(), (
        ".hugo-version is missing — it is the single source of truth for the Hugo "
        "version used by both local builds and CI"
    )
    raw = PIN_FILE.read_text(encoding="utf-8").strip()
    m = SEMVER_RE.match(raw)
    assert m, f".hugo-version must contain a bare semver like '0.147.2', got {raw!r}"
    return tuple(int(g) for g in m.groups())


def test_hugo_version_is_single_sourced():
    """CI must read the pin, not carry its own copy of the number."""
    major, minor, patch = _pinned()
    wf = WORKFLOW.read_text(encoding="utf-8")

    assert ".hugo-version" in wf, (
        "the deploy workflow does not read .hugo-version — if it hardcodes a version "
        "instead, dev and prod can drift apart again (the PR #6/#7 bug)"
    )

    # A literal `HUGO_VERSION: 0.147.2` line would be a second source of truth.
    hardcoded = re.findall(r"HUGO_VERSION:\s*['\"]?(\d+\.\d+\.\d+)", wf)
    assert not hardcoded, (
        f"workflow hardcodes HUGO_VERSION={hardcoded} — delete it and let the step read "
        f".hugo-version (currently {major}.{minor}.{patch})"
    )


def test_local_hugo_matches_pin():
    """Hard-fail on minor drift (that is what broke prod); warn on patch drift."""
    try:
        out = subprocess.run(["hugo", "version"], capture_output=True, text=True, timeout=60)
    except (FileNotFoundError, subprocess.TimeoutExpired):
        pytest.skip("hugo binary not available")
    if out.returncode != 0:
        pytest.skip("could not determine local hugo version")

    m = re.search(r"v(\d+)\.(\d+)\.(\d+)", out.stdout)
    if not m:
        pytest.skip(f"unparseable hugo version output: {out.stdout.strip()[:80]}")
    local = tuple(int(g) for g in m.groups())
    pinned = _pinned()

    assert local[:2] == pinned[:2], (
        f"local Hugo is {'.'.join(map(str, local))} but the project pins "
        f"{'.'.join(map(str, pinned))}. A minor-version gap is exactly what made the "
        f"contact page render differently in production. Install the pinned version, "
        f"or update .hugo-version deliberately."
    )
    if local[2] != pinned[2]:
        import warnings
        warnings.warn(
            f"local Hugo {'.'.join(map(str, local))} differs from the pin "
            f"{'.'.join(map(str, pinned))} at the patch level — usually harmless, but "
            f"CI will build with the pin.",
            stacklevel=2,
        )
