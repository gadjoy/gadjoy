<!--
Branch prefix declares whether this needs a spec:
  feat/*   -> new or reshaped user-facing capability, or migration/test infrastructure.
              CI requires a change under specs/.
  fix/*    -> bug fix. CI requires the regression test described below.
  chore/*  -> dependency bump, CI config, tooling. No spec.
See .specify/memory/constitution.md → Development Workflow.
-->

## What and why

<!-- What changes, and what problem it solves. Link the spec if there is one. -->

Spec: <!-- specs/NNN-slug/ — or "not required: <copy edit | CSS tweak | dep bump | single-file bug fix>" -->

## Checklist

- [ ] **Spec** — `feat/*`: `specs/NNN-slug/{spec,plan,tasks}.md` exist, and `tasks.md` reflects
      what actually landed. Not `feat/*`: the exemption is named above.
- [ ] **Tests written, not just passing.** Every success criterion this PR claims to satisfy is
      either guarded by a test or marked `OWED` in the spec's coverage table, with the owed item
      added to the register in `specs/README.md`. A criterion with no stated coverage position
      is not done.
- [ ] **Fixing a bug? The test came first.** Constitution VII: write the test, watch it fail
      against the unfixed code, then fix. A bug that reached production has already proved the
      suite cannot see that class of failure.
- [ ] **Guards verified by breaking them.** Any new test has been shown to fail when its bug is
      reintroduced. Two guards in PR #14 passed vacuously on the first attempt — one sampled
      the wrong 25 posts, one had a regex that matched nothing. A green test asserting nothing
      is worse than no test, because it reads as coverage.
- [ ] **`pytest` green locally**, and the Hugo version matches `.hugo-version`.
- [ ] **Public claims trace to a source** (Constitution X) — any number, rating or guarantee
      rendered on the site comes from `hugo.yaml` or another named source, not a template
      literal and not derived from something that merely correlates.
- [ ] **No secrets committed.** `GOOGLE_PLACES_API_KEY` comes from the environment only.
      (`params.web3forms_key` is public by design — see the comment beside it in `hugo.yaml`.)

## Verification

<!--
What you actually ran, with results — not what you intended to run.
  pytest:  N passed
  hugo --minify:  clean
  ./scripts/smoke.sh <url>:  N/M   (if this touches deployed behaviour)
  mutation check:  which bug you reintroduced, and that the test failed
-->

## Anything left undone

<!--
Owed tests, deferred items, manual steps the reviewer must take (secrets to add, settings to
flip). Say it here rather than leaving it to be discovered later — a dead contact form went
unnoticed for hours because "shipped" and "working" were assumed to be the same thing.
-->
