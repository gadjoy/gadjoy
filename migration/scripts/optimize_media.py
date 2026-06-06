#!/usr/bin/env python3
"""Slim published media: prune unreferenced uploads, convert images to WebP
(rewriting references), and re-encode videos. Idempotent; safe to re-run.

"Used" = any /img/uploads reference in content/, layouts/, data/, hugo.yaml
(covers post bodies, front-matter banner:/image:, <video> src, the hero image).
Originals remain in wordpress/backup + migration/wp-export, so this is recoverable.

Requires: cwebp, ffmpeg, sips (macOS).  Run from anywhere:  python optimize_media.py
"""
import re
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
UPLOADS = ROOT / "static" / "img" / "uploads"
SRC_DIRS = ["content", "layouts", "data"]
EXTRA = [ROOT / "hugo.yaml"]
SRC_SUFFIXES = {".md", ".html", ".yaml", ".yml", ".toml", ".json", ".xml"}
IMG_EXT = {".png", ".jpg", ".jpeg"}
VID_EXT = {".mp4", ".webm", ".mov"}

# matches /img/uploads/... or img/uploads/... (the hero uses no leading slash)
REF_RE = re.compile(r"/?img/uploads/[^\s\"')(<>\]\\]+\.[A-Za-z0-9]+")
# greedy path match so only the FINAL extension is swapped (some originals have
# double extensions like "foo.jpg.png" → disk becomes "foo.jpg.webp")
SUB_RE = re.compile(r"((?:/)?img/uploads/[^\s\"')(<>\]\\]+)\.(png|jpe?g)\b", re.IGNORECASE)


def src_files():
    out = []
    for d in SRC_DIRS:
        out += [p for p in (ROOT / d).rglob("*") if p.is_file() and p.suffix.lower() in SRC_SUFFIXES]
    out += [f for f in EXTRA if f.exists()]
    return out


def rel_of(ref):
    return ref.split("img/uploads/", 1)[1]


def img_width(p):
    r = subprocess.run(["sips", "-g", "pixelWidth", str(p)], capture_output=True, text=True)
    m = re.search(r"pixelWidth:\s*(\d+)", r.stdout)
    return int(m.group(1)) if m else 0


def main():
    files = src_files()
    refs = set()
    for p in files:
        try:
            for m in REF_RE.findall(p.read_text(encoding="utf-8", errors="ignore")):
                refs.add(rel_of(m))
        except Exception:
            pass
    missing = sorted(r for r in refs if not (UPLOADS / r).exists())
    print(f"referenced upload files: {len(refs)} | missing on disk: {len(missing)}")
    for m in missing[:10]:
        print("  MISSING (kept as-is):", m)
    keep = {r for r in refs if (UPLOADS / r).exists()}

    # 1) PRUNE unreferenced files
    pruned = 0
    for p in list(UPLOADS.rglob("*")):
        if p.is_file() and p.relative_to(UPLOADS).as_posix() not in keep:
            p.unlink(); pruned += 1
    print(f"pruned {pruned} unreferenced files")

    # 2) CONVERT images -> webp (only downscale oversized; never upscale)
    failed = set()
    converted = 0
    for rel in sorted(keep):
        src = UPLOADS / rel
        if src.suffix.lower() not in IMG_EXT or not src.exists():
            continue
        dst = src.with_suffix(".webp")
        args = ["cwebp", "-quiet", "-q", "80"]
        if img_width(src) > 1400:
            args += ["-resize", "1280", "0"]
        args += [str(src), "-o", str(dst)]
        r = subprocess.run(args)
        if r.returncode == 0 and dst.exists() and dst.stat().st_size > 0:
            src.unlink(); converted += 1
        else:
            failed.add(rel)
            if dst.exists():
                dst.unlink()
    print(f"converted {converted} images to webp | failed {len(failed)}")
    for f in list(failed)[:10]:
        print("  CONVERT FAILED (left as original):", f)

    # 3) REWRITE references .png/.jpg/.jpeg -> .webp (skip any that failed to convert)
    def repl(m):
        rel = rel_of(m.group(0))
        return m.group(0) if rel in failed else m.group(1) + ".webp"
    changed = 0
    for p in files:
        try:
            t = p.read_text(encoding="utf-8")
        except Exception:
            continue
        nt = SUB_RE.sub(repl, t)
        if nt != t:
            p.write_text(nt, encoding="utf-8"); changed += 1
    print(f"rewrote image refs in {changed} source files")

    # 4) RE-ENCODE videos in place (keep .mp4 name; only replace if smaller)
    venc = 0
    for rel in sorted(keep):
        src = UPLOADS / rel
        if src.suffix.lower() not in VID_EXT or not src.exists():
            continue
        tmp = src.with_name(src.stem + ".opt.mp4")
        r = subprocess.run(["ffmpeg", "-y", "-loglevel", "error", "-i", str(src),
                            "-vcodec", "libx264", "-crf", "26", "-preset", "slow",
                            "-acodec", "aac", "-movflags", "+faststart", str(tmp)])
        if r.returncode == 0 and tmp.exists() and 0 < tmp.stat().st_size < src.stat().st_size:
            tmp.replace(src.with_suffix(".mp4")); venc += 1
            if src.suffix.lower() != ".mp4":  # e.g. .mov -> .mp4 (rare; would need ref fix)
                pass
        elif tmp.exists():
            tmp.unlink()
    print(f"re-encoded {venc} videos")

    # 5) resize the oversized logo in place (shown <=~90px)
    logo = ROOT / "static" / "img" / "logo.png"
    if logo.exists():
        subprocess.run(["sips", "-Z", "512", str(logo)], capture_output=True)
        print("resized static/img/logo.png to max 512px")

    # 6) drop now-empty dirs
    for d in sorted([p for p in UPLOADS.rglob("*") if p.is_dir()], reverse=True):
        try:
            d.rmdir()
        except OSError:
            pass
    print("done.")


if __name__ == "__main__":
    sys.exit(main())
