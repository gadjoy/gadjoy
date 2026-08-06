"""Generate the reviewed-images manifest that the build-phase PII gate reads.

The gate needs to be fast on every build but still catch a newly added image that
publishes someone's IMEI. So the manifest records the content hash of every image that
has already been OCR-reviewed and found clean (or redacted). The test then OCRs only
images whose hash it has never seen — which in steady state is none, and after a
re-migration or a new post is exactly the new files.

Run after a full sweep:
    python migration/scripts/scan_device_identifiers.py --out /tmp/pii.json
    python migration/scripts/build_reviewed_manifest.py --findings /tmp/pii.json
"""
import argparse
import hashlib
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from device_identifiers import iter_images  # noqa: E402

REPO_ROOT = Path(__file__).resolve().parents[2]
UPLOADS = REPO_ROOT / "static" / "img" / "uploads"
MANIFEST = REPO_ROOT / "migration" / "tests" / "data" / "reviewed_images.json"

WHY = (
    "Content hashes of upload images already OCR-reviewed for device/customer identifiers "
    "(IMEI, serial, MAC, owner name). test_no_device_identifiers.py OCRs only images whose "
    "hash is absent here, so the gate is fast in steady state and still catches new content. "
    "Regenerate with migration/scripts/build_reviewed_manifest.py after a full sweep."
)


def sha256(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as fh:
        for chunk in iter(lambda: fh.read(1 << 20), b""):
            h.update(chunk)
    return h.hexdigest()


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--findings", help="JSON from scan_device_identifiers.py")
    ap.add_argument("--forbidden", help=(
        "JSON map {path: sha256} of PRE-redaction originals to record as forbidden, so "
        "restoring one fails the gate on hash alone, without needing OCR"))
    ap.add_argument("--root", default=str(UPLOADS))
    ap.add_argument("--out", default=str(MANIFEST))
    args = ap.parse_args()

    flagged = set()
    if args.findings:
        for f in json.loads(Path(args.findings).read_text(encoding="utf-8")):
            flagged.add(str(Path(f["path"]).resolve()))

    existing = {}
    out_path = Path(args.out)
    if out_path.is_file():
        existing = json.loads(out_path.read_text(encoding="utf-8"))

    cleared, still_flagged = [], {}
    for p in iter_images(Path(args.root)):
        digest = sha256(p)
        rel = p.relative_to(REPO_ROOT).as_posix()
        if str(p.resolve()) in flagged:
            still_flagged[rel] = digest
        else:
            cleared.append(digest)

    # Hashes that must never reappear: the pre-redaction originals. Belt and braces —
    # this fires even when tesseract is unavailable.
    forbidden = dict(existing.get("forbidden_sha256", {}))
    if args.forbidden:
        for path, digest in json.loads(Path(args.forbidden).read_text(encoding="utf-8")).items():
            rel = Path(path)
            rel = rel.relative_to(REPO_ROOT).as_posix() if rel.is_absolute() else rel.as_posix()
            forbidden[digest] = rel
    cleared_set = set(cleared)
    overlap = cleared_set & set(forbidden)
    if overlap:
        sys.exit(
            f"refusing to write: {len(overlap)} hash(es) are listed as both cleared and "
            f"forbidden — an un-redacted original is still on disk"
        )

    manifest = {
        "_why": WHY,
        "cleared_sha256": sorted(cleared_set),
        "forbidden_sha256": forbidden,
        "redacted": existing.get("redacted", {}),
    }
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(json.dumps(manifest, indent=2) + "\n", encoding="utf-8")

    print(f"cleared: {len(manifest['cleared_sha256'])} hashes -> {out_path}")
    if still_flagged:
        print(f"STILL FLAGGED ({len(still_flagged)}) — redact these, then re-run:")
        for rel, d in still_flagged.items():
            print(f"  {rel}  {d[:16]}…")
        return 1
    return 0


if __name__ == "__main__":
    sys.exit(main())
