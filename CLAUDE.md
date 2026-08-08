# Gadjoy — contributor & agent guide

> **Read [`.specify/memory/constitution.md`](.specify/memory/constitution.md) first.**
> Six non-negotiables governing the WordPress → Hugo migration. Principle I exists because
> **the previous migration shipped with zero tests and silently corrupted 1,500+ posts** —
> these are not style preferences.

This file exists so the constitution actually reaches you: `CLAUDE.md` is loaded
automatically in every session, `.specify/memory/` is not. It replaces a Spec Kit
placeholder that said only "read the current plan" and never mentioned the constitution
sitting beside it.

Machine-wide engineering principles are in `~/.claude/CLAUDE.md` and apply here too.

## What this is

The website for **Gadjoy Repair Service**, a device-repair shop in Bangalore. Live at
<https://gadjoy.in/>. Hugo static site, deployed by the `hugo.yml` workflow.

It is a **real business's live site**. A broken build or a dead URL costs the shop
customers, which is why URL parity (Principle IV) is a tested invariant rather than a
hope.

## The six, in one line each

1. **Test-First (non-negotiable)** — red → green → refactor. No extractor or conversion
   code before a failing test exists for it.
2. **Single source of truth** — content derives from the restored WordPress instance.
   `migration/wp-export/posts.json` and the existing broken `public/` HTML are **not**
   sources and must never be used to reconstruct content.
3. **No fabricated data** — taxonomy, dates, slugs and excerpts are read as stored.
   Guessing a category from a post title is forbidden. **Absent in the source means
   absent in the output.**
4. **Exact URL preservation** — every post reachable at its original URL, permalinks read
   from `wp_options.permalink_structure`, plus an `aliases:` entry as a safety net.
5. **Lossless, verifiable content** — no `<!-- wp: -->` comments, no `[shortcode]`
   residue; every image path canonical and the file present on disk.
6. **Reproducible source** — Markdown in `content/blog/`, regenerable by `hugo`. Content
   that exists only as built HTML is unacceptable.

Principles II and III are the same rule this project keeps needing: **record what the
source says, never what you can infer.** A guessed category is indistinguishable from a
real one once written, and it is the guess you will publish.

## Commands

```bash
pytest migration/tests/          # the permanent acceptance gate — must stay green
hugo                             # build; CI also installs dart-sass
hugo server -D                   # local preview including drafts
```

`migration/tests/` holds `test_build.py`, `test_conversion.py`, `test_golden.py` and
`test_invariants.py`. The invariant and golden tests are what make Principles IV and V
enforceable rather than aspirational — do not weaken an invariant to make a change pass.

**Gate on exit codes, never on reading output** (CON-VER-001) — the constitution carries the rule and the incident behind it; what follows is how it bites here. A summary line can say "passed" while
the process exits non-zero.

## Layout

- `content/blog/` — migrated Markdown (the authoritative source)
- `migration/` — extractors, converters and the test suite
- `wordpress/backup/` — the `.wpress` backup everything derives from
- `specs/001-wp-hugo-migration/` — the migration spec
- `layouts/`, `themes/`, `static/`, `data/` — Hugo site
- `public/` — **build output; never a source**

## Workflow

Every change lands through a reviewed PR. New behaviour starts with a spec, then tests
derived from its acceptance criteria, then code — Principle I is explicit that the order
matters.

If you add a rule, add the check that catches its violation in the same change. A rule
with no enforcing mechanism is a wish, and this project already learned what an
unenforced "be careful" is worth: 1,500 corrupted posts.
