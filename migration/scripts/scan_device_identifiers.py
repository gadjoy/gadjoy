"""Sweep the upload tree for images that publish device/customer identifiers.

Slow by nature (OCR over thousands of files), so this is the *local* sweep. The
build-phase gate re-checks only images whose content hash is not in the cleared
manifest, which keeps CI fast — see migration/tests/test_no_device_identifiers.py.

Usage:
    python migration/scripts/scan_device_identifiers.py                     # whole tree
    python migration/scripts/scan_device_identifiers.py --paths a.webp b.webp
    python migration/scripts/scan_device_identifiers.py --jobs 8 --out findings.json
"""
import argparse
import json
import sys
from concurrent.futures import ProcessPoolExecutor, as_completed
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from device_identifiers import (  # noqa: E402
    have_tesseract, iter_images, scan_image,
)

REPO_ROOT = Path(__file__).resolve().parents[2]
UPLOADS = REPO_ROOT / "static" / "img" / "uploads"


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--root", default=str(UPLOADS))
    ap.add_argument("--paths", nargs="*", help="specific files instead of the whole tree")
    ap.add_argument("--jobs", type=int, default=4)
    ap.add_argument("--out", default=str(REPO_ROOT / "migration" / "pii-findings.json"))
    args = ap.parse_args()

    if not have_tesseract():
        sys.exit("tesseract not found — install it (apt-get install tesseract-ocr)")

    targets = [Path(p) for p in args.paths] if args.paths else list(iter_images(Path(args.root)))
    print(f"scanning {len(targets)} images with {args.jobs} workers", flush=True)

    findings, done = [], 0
    with ProcessPoolExecutor(max_workers=args.jobs) as ex:
        futures = {ex.submit(scan_image, p): p for p in targets}
        for fut in as_completed(futures):
            done += 1
            if done % 100 == 0:
                print(f"  {done}/{len(targets)} … {len(findings)} flagged", flush=True)
            try:
                res = fut.result()
            except Exception as exc:                       # keep sweeping
                print(f"  ! {futures[fut]}: {exc}", flush=True)
                continue
            if res:
                findings.append(res)

    findings.sort(key=lambda f: f["path"])
    Path(args.out).write_text(json.dumps(findings, indent=2), encoding="utf-8")

    print(f"\n{len(findings)} of {len(targets)} images flagged -> {args.out}")
    for f in findings:
        kinds = sorted({h["kind"] for h in f["hits"]})
        print(f"  {Path(f['path']).name}\n      {', '.join(kinds)}")


if __name__ == "__main__":
    main()
