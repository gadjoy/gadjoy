# Gadjoy Website Constitution

**Scope note (v2.0.0).** v1.0.0 governed a one-off project: the WordPress → Hugo
migration. It did its job — the previous regex/SQL migration silently corrupted 1,500+
posts; the test-first rewrite did not. But the migration finished, and the project became
an ongoing website while this document still only bound "migration code". Principles I–VI
below are therefore retained **unchanged as content invariants**, and VII–X are added to
govern the live site.

The gap this closes was measurable. Of the twelve PRs merged after v1.0.0 was ratified,
five were fixing something already in production: the contact page silently falling back
to the theme's layout (#6/#7), a contact form posting to a dead endpoint (#10), an
invisible call button (#12), and a headline repair figure that contradicted the résumé
(#13) — plus two images that 404'd for months. Every one was found by a human looking at
the live site, because CI ran no tests at all and the quality gate below was honoured
from memory.

## Core Principles

### I. Test-First (NON-NEGOTIABLE)
TDD is mandatory for all migration code. The cycle is strictly: write a test → confirm it
FAILS (red) → implement the minimum to make it pass (green) → refactor with tests green. No
extractor or conversion code is written before a failing test exists for the behaviour it
delivers. The pytest suite under `migration/tests/` is the permanent acceptance gate — the
migration is "done" only when it is green, and any future change must keep it green. The
previous migration shipped with zero tests and silently corrupted 1,500+ posts; this principle
exists specifically to make that failure mode impossible to repeat.

### II. Single Source of Truth
All post content, metadata, taxonomy, and media derive from the restored WordPress instance
(imported from the 2025-04-25 `.wpress` backup in `wordpress/backup/`). No other input is
authoritative. The minimal `migration/wp-export/posts.json` (metadata only) and the existing
broken `public/` HTML are NOT sources and must never be used to reconstruct content.

### III. No Fabricated Data
Categories, tags, dates, slugs, and excerpts are read from the WordPress database/REST API
exactly as stored. Deriving taxonomy by guessing from post titles (as the old
`wp_to_hugo.py` did) is forbidden. If a field is absent in the source, it is absent in the
output — never invented.

### IV. Exact URL Preservation
Every migrated post must be reachable at the same URL the original WordPress site served. The
canonical permalink structure is read from `wp_options.permalink_structure` in the restored DB
and reproduced exactly via Hugo `permalinks` config. Every post additionally carries an
`aliases:` entry equal to its original URL path as a redirect safety net. URL parity is a
tested invariant, not a hope.

### V. Lossless, Verifiable Content
Conversion preserves the rendered content: headings, lists, galleries, image captions, and alt
text survive. The output contains NO leftover WordPress artifacts — no `<!-- wp: -->` block
comments, no `[shortcode]` residue, no raw unintended HTML. Every image reference uses one
canonical path form (`/img/uploads/...`) and the referenced file must exist on disk. These are
tested invariants.

### VI. Reproducible Source
The blog must exist as Markdown source in `content/blog/` and be fully regenerable by `hugo`.
A site whose content lives only in built `public/` HTML (the current broken state) is
unacceptable. Re-running the documented migration against the same backup must produce the
same source.

### VII. Every Production Bug Becomes a Test (NON-NEGOTIABLE)
A bug found in production is not fixed until a test exists that would have caught it. The
test is written **first**, confirmed red against the unfixed code, and only then made green
— the same red/green discipline as Principle I, applied to the live site. Fixing the symptom
alone is forbidden, because a bug that reached production has already proved the suite cannot
see that class of failure. `migration/tests/test_site_output.py` is the home for these
guards, and each one names the incident it descends from.

A guard is only real if it fails when the bug is reintroduced. Verify that deliberately
before trusting it. The first version of the internal-link guard required quoted HTML
attributes, `--minify` emits them unquoted, and so it silently checked nothing and passed a
knowingly broken image — a green test asserting nothing is worse than no test, because it
reads as coverage.

### VIII. Gates Run in CI, Not From Memory
`pytest` green and a clean `hugo` build are required before merge, and that requirement is
enforced by `.github/workflows/hugo.yml` on every pull request — not by whoever remembers to
run it. A gate that depends on discipline is not a gate: for months the suite was red on
`main` (two images referenced with the wrong case) while PR descriptions truthfully reported
"pytest 25 green", because it had last been run on a case-insensitive filesystem.

Corollaries: tests must be runnable with no services running (fixtures are captured, not
fetched); a test that only passes on one operating system is a bug in the test; and the
toolchain version lives in exactly one place, `.hugo-version`, read by both CI and humans.

### IX. Verify the Deployed Site, Not Just the Build
A successful build is not evidence that the site works. Both the layout-fallback bug and the
dead form returned HTTP 200 the entire time they were broken. Every deploy is therefore
followed by `scripts/smoke.sh` against the live origin, asserting the pages exist, the
bespoke markup is present, and the assets referenced are actually served. Checks that would
send real traffic to a third party (submitting the contact form) are out of bounds —
reachability only.

### X. Public Claims Must Trace to a Source
Any factual claim rendered on the site — repair counts, ratings, turnaround times, warranty
terms — must be traceable to a value in `hugo.yaml` (or another named source), never
hardcoded in a template and never silently derived from something that merely correlates.
The homepage claimed "1508+ repairs" for months because the figure was derived from the
number of published case studies: a real number that measures the wrong thing. Where a
derived number and a real-world number differ, the config comment must say why.

## Additional Constraints

- **Stack**: Hugo (extended) static site, `hugo-universal-theme`; deployed to GitHub Pages at
  custom domain `gadjoy.in`. Migration tooling is Python 3.13 + pytest; HTML→Markdown via
  `markdownify`. WordPress restored via Docker (`wordpress/docker-compose.yml`).
- **Hugo version**: pinned in `.hugo-version` and read from there by CI. Never duplicated
  into the workflow.
- **Inventory to match**: 1,508 published posts (+2 drafts), 2021-12 → 2025-02; the WP pages
  (Home, We Repair, We Build, Contact, Gallery); ~10,630 uploaded media files.

## Development Workflow

- Spec-Driven Development: `constitution → spec → plan → tasks → implement`. Artifacts live in
  `.specify/` and `specs/NNN-slug/`.
- **When a spec is required**: any change that adds or reshapes a user-facing capability, or
  that touches migration/test infrastructure. **When it is not**: copy edits, CSS and styling
  tweaks, dependency bumps, and single-file bug fixes — these go straight to a branch and PR.
  v1.0.0 was silent on this, so eleven consecutive PRs shipped with no spec and the practice
  quietly lapsed; naming the exemption is what keeps the rule credible.
- A spec's `tasks.md` is kept current as work lands. A checklist nobody ticks trains everyone
  to ignore checklists — `001`'s sat at 0 of 31 checked long after it shipped.
- All work on a feature branch; never commit directly to `main`. At MR time, ask which branch
  to target. Commit messages carry no AI-tool attribution.
- Quality gate before any merge: `pytest` green AND `hugo --minify` clean, **enforced by CI**
  (Principle VIII). Post-deploy: `scripts/smoke.sh` green (Principle IX).

## Governance

This constitution supersedes ad-hoc practice. Any deviation (shipping code without a failing
test first, fabricating a missing field, publishing an untraceable claim) must be justified in
writing in the plan's Complexity Tracking and approved by the user before proceeding.
Compliance is verified by CI, not by assertion in a PR description.

**Version**: 2.0.0 | **Ratified**: 2026-06-03 | **Last Amended**: 2026-08-05
