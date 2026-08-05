---
description: "Live Google reviews fetched at build time, with a verifiable fallback and no committed key"
status: shipped
shipped: 2026-06-06
prs: [2]
backfilled: 2026-08-05
---

# Feature Specification: Live Google Reviews with Verifiable Fallback

**Shipped**: 2026-06-06 via PR #2 | **Backfilled**: 2026-08-05 | **Constitution**: v2.0.0

> **Retrospective spec**, reconstructed from PR #2's description, `hugo.yaml`, and
> `layouts/partials/testimonials.html`. Given a spec of its own rather than folded into `003`
> because it is the site's only external runtime dependency, it carries a credential, and it
> is the one feature with an explicit degrade-gracefully requirement.

## Problem

The theme ships placeholder testimonials as static YAML (`data/testimonials/*.yaml`) — invented
quotes from invented people. The business has genuinely strong Google reviews (4.7★ across 516
at time of build). Publishing fabricated social proof next to real repair case studies is both
dishonest and weaker than the truth. But a build-time API call introduces two risks a static
site cannot normally tolerate: a secret to leak, and a network dependency that can fail the
build.

## User Scenarios

### US1 — A visitor sees real, checkable reviews (P1)
The reviews section shows the actual rating, the actual review count, and real review text,
each linking out to the Google listing so a sceptical visitor can verify it.

### US2 — The build never breaks because Google is unavailable (P1)
If the API key is absent, the Place ID is wrong, or the request fails, the site still builds
and the section still renders something truthful — never an error, never an empty block, never
fabricated quotes.

### US3 — A contributor without the key can work normally (P2)
Cloning the repo and running `hugo server` with no credential produces a working site.

## Requirements

- **FR-001** Reviews MUST be fetched from the Google Places API **at build time**, not by
  client-side JavaScript (no key exposed to browsers, no runtime dependency for visitors).
- **FR-002** The API key MUST come only from the `GOOGLE_PLACES_API_KEY` environment variable —
  read from the environment locally and from a GitHub Actions repository secret in CI. It MUST
  NOT be committed, and `hugo.yaml` MUST allow exactly this variable through
  `security.funcs.getenv`.
- **FR-003** The Place ID MUST live in `hugo.yaml` (`params.googlePlaceId`) — it is not secret.
- **FR-004** On success the section MUST show the rating, the total review count, and up to 8
  reviews rated ≥4, each attributed to its author with a relative date.
- **FR-005** On any failure — missing key, missing Place ID, non-OK API status, network error —
  the build MUST succeed and a fallback "proof" block MUST render instead. The failure MUST be
  surfaced as a Hugo warning, not swallowed silently.
- **FR-006** The fallback MUST contain only claims that are independently true and traceable
  (Constitution X) — never invented quotes, never a fabricated rating.
- **FR-007** The placeholder `data/testimonials/*.yaml` fixtures MUST be deleted, so there is
  no path by which fake testimonials can render.

## Success Criteria

| ID | Criterion | Test coverage |
|---|---|---|
| SC-001 | Build succeeds **with** the key and renders the live badge | **OWED** (needs a credential in CI) |
| SC-002 | Build succeeds **without** the key and renders the fallback | ✅ implicitly — `built_site` fixture builds keyless, and every build-output test asserts against that output |
| SC-003 | No API key value appears anywhere in the repository or built output | **OWED** |
| SC-004 | No fabricated testimonial content exists in `data/` | **OWED** |
| SC-005 | Fallback block states only traceable figures | ✅ partially — `test_repair_claim_matches_config` covers the repair figure it used to carry |

## Out of Scope

- Caching or rate-limit handling. One request per build is well inside free-tier limits.
- Review moderation or selection beyond the ≥4★ filter.
- Replying to reviews, or any write access to the listing.

## As Built

`layouts/partials/testimonials.html` implements the whole feature in one partial:
`resources.GetRemote` wrapped in `try`, `warnf` on error, `transform.Unmarshal`, a
`where .reviews "rating" ">=" 4` filter, `first 8`, and an `{{ else }}` branch holding the
fallback proof block. `data/testimonials/3.yaml` and `4.yaml` were deleted in `f2bba26a`.

PR #2 flagged an explicit follow-up action — *"Add a GitHub repo secret
`GOOGLE_PLACES_API_KEY`"* — which is the correct way to hand off a step that cannot be
committed. The workflow passes it to the build step, so it was actioned.

### Deviation: the fallback carried an untraceable claim for two months

FR-006 is stated here as it was clearly intended, but the fallback block as shipped led with
`{{ $repairs }}+ / Repairs documented & counting`, deriving its headline number from the count
of blog posts. That is a real number measuring the wrong thing, and it is the same defect PR
#13 later fixed on the homepage hero. Corrected in PR #13; now guarded by
`test_repair_claim_matches_config`.

Worth noting because it shows the failure mode this spec was needed to prevent: the
degrade-gracefully requirement was implemented carefully and correctly, and then the fallback
content itself — the part nobody specified — was wrong.

## Tests Owed

SC-003 (no key in repo or output) is cheap and worth adding: a grep-style assertion over the
tree and the built site. SC-004 likewise. SC-001 needs a credential available to CI and is the
one criterion that may be reasonably left manual.
