"""Build-phase gate: no published image may expose device or customer identifiers.

The shop photographed devices' **About** screens as repair proof, so some before/after
images published the customer's own data — owner name, serial number, IMEIs, Wi-Fi and
Bluetooth MAC addresses. Those are the identifiers used in device fraud and the customers
did not consent to publication. Two images (`2023/02/image-53.webp`, `image-54.webp`)
carried a serial and both IMEIs and were live for months.

`specs/006-gallery-lightbox/` FR-007 states the rule. This enforces it.

**How it stays fast.** OCR over 2,711 images takes minutes, which is too slow for every
build. So `migration/tests/data/reviewed_images.json` records the content hash of every
image already reviewed. This test hashes the tree (seconds) and OCRs only images whose
hash it has never seen — none in steady state, exactly the new files after a re-migration
or a new post.

**Why hashes and not paths.** A path-keyed allowlist would let someone swap an image's
contents while keeping its name and sail through. Keying on content means any edit —
including restoring a pre-redaction original — is an unknown hash and gets OCR'd.

Requires the `tesseract` binary *only* when unknown hashes are present.
"""
import hashlib
import json
import sys
from pathlib import Path

import pytest

from conftest import REPO_ROOT

sys.path.insert(0, str(REPO_ROOT / "migration" / "scripts"))

from device_identifiers import (  # noqa: E402
    find_identifiers, have_tesseract, iter_images, ocr_words,
)

UPLOADS = REPO_ROOT / "static" / "img" / "uploads"
MANIFEST_PATH = Path(__file__).resolve().parent / "data" / "reviewed_images.json"

# Cap the OCR work a single run will do. If a change adds more new images than this, the
# reviewer should run the full sweep and regenerate the manifest rather than have CI grind
# through it — and the test says so instead of silently checking a subset.
MAX_UNREVIEWED = 40

# Detection is keyword- and pattern-based, so it has a known false-positive class: posts
# in the `code`/`build` categories include console screenshots where a printed float
# (60/17 -> 3529411764705883) matches the IMEI digit-run pattern. Those images are
# reviewed and cleared via the manifest like any other; this note exists so the next
# person to see "long_digit_run" on a terminal screenshot does not redact maths output.


def _sha256(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as fh:
        for chunk in iter(lambda: fh.read(1 << 20), b""):
            h.update(chunk)
    return h.hexdigest()


@pytest.fixture(scope="module")
def manifest():
    if not MANIFEST_PATH.is_file():
        pytest.fail(
            f"{MANIFEST_PATH.relative_to(REPO_ROOT)} is missing. Generate it with "
            f"migration/scripts/build_reviewed_manifest.py after a full sweep."
        )
    return json.loads(MANIFEST_PATH.read_text(encoding="utf-8"))


@pytest.fixture(scope="module")
def hashed_images():
    if not UPLOADS.is_dir():
        pytest.skip("no uploads directory")
    return [(p, _sha256(p)) for p in iter_images(UPLOADS)]


def test_manifest_covers_the_upload_tree(manifest, hashed_images):
    """Sanity: the manifest should describe roughly this tree, not a stale fraction of it.

    Guards against the manifest being generated once and then left behind while hundreds
    of images change — at which point every build would try to OCR the difference and the
    MAX_UNREVIEWED ceiling below would start failing for the wrong reason.
    """
    cleared = set(manifest.get("cleared_sha256", []))
    assert cleared, "manifest lists no cleared hashes"
    known = sum(1 for _p, d in hashed_images if d in cleared)
    ratio = known / len(hashed_images)
    assert ratio > 0.9, (
        f"only {known}/{len(hashed_images)} images ({ratio:.0%}) are in the reviewed "
        f"manifest — regenerate it with build_reviewed_manifest.py"
    )


def test_no_forbidden_image_content(manifest, hashed_images):
    """Pre-redaction originals must never come back.

    This is the belt-and-braces check: it needs no OCR, so it fires even where tesseract
    is unavailable, and it names the exact file if someone restores an unredacted copy.
    """
    forbidden = manifest.get("forbidden_sha256", {}) or {}
    if not forbidden:
        pytest.skip("no forbidden hashes recorded")
    back = [
        (p.relative_to(REPO_ROOT).as_posix(), forbidden[d])
        for p, d in hashed_images if d in forbidden
    ]
    assert not back, (
        f"un-redacted image content has reappeared: {back}. These images publish a "
        f"customer's device identifiers; re-apply the redaction with "
        f"migration/scripts/redact_device_identifiers.py"
    )


def test_new_images_expose_no_device_identifiers(manifest, hashed_images):
    """OCR every image the manifest has not already cleared."""
    cleared = set(manifest.get("cleared_sha256", []))
    unknown = [p for p, d in hashed_images if d not in cleared]
    if not unknown:
        return                                    # steady state: nothing to do

    if len(unknown) > MAX_UNREVIEWED:
        pytest.fail(
            f"{len(unknown)} images are not in the reviewed manifest (ceiling is "
            f"{MAX_UNREVIEWED}). Run the full sweep and regenerate the manifest:\n"
            f"  python migration/scripts/scan_device_identifiers.py --out /tmp/pii.json\n"
            f"  python migration/scripts/build_reviewed_manifest.py --findings /tmp/pii.json"
        )

    if not have_tesseract():
        pytest.fail(
            f"{len(unknown)} image(s) are new or modified and cannot be checked for "
            f"customer identifiers because tesseract is not installed. Install it "
            f"(apt-get install tesseract-ocr) or review and add them to the manifest.\n"
            + "\n".join(f"  {p.relative_to(REPO_ROOT)}" for p in unknown[:10])
        )

    offenders = []
    for path in unknown:
        text, words = ocr_words(path)
        hits = find_identifiers(text, words)
        if hits:
            kinds = sorted({h["kind"] for h in hits})
            offenders.append((path.relative_to(REPO_ROOT).as_posix(), kinds))

    assert not offenders, (
        "image(s) appear to publish device/customer identifiers:\n"
        + "\n".join(f"  {rel}: {', '.join(kinds)}" for rel, kinds in offenders)
        + "\n\nRedact the identifier region — do NOT delete the photo:\n"
        "  python migration/scripts/redact_device_identifiers.py <image> --auto\n"
        "then regenerate the manifest. See specs/006-gallery-lightbox/ FR-007."
    )
