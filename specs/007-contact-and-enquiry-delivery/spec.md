---
description: "Contact page and working enquiry delivery from a static site (WhatsApp primary, email fallback)"
status: shipped
shipped: 2026-06-07
prs: [5, 6, 7, 10, 11]
backfilled: 2026-08-05
---

# Feature Specification: Contact Page & Enquiry Delivery

**Shipped**: 2026-06-06 → 2026-06-07 via PRs #5, #6, #7, #10, #11 | **Backfilled**: 2026-08-05
| **Constitution**: v2.0.0

> **Retrospective spec**, reconstructed from five PRs and commits `cdb485f4`, `8f7800db`,
> `51b9d8fc`, `8bcff5a3`, `433c5e25`.
>
> This is the most valuable of the backfilled specs, because it is where the project's whole
> bug history is concentrated: **four of the five production incidents happened here.** Every
> requirement below has a corresponding incident, which is the strongest possible argument that
> it needed to be written down first.

## Problem

Two problems, one visible and one not.

**Visible:** the contact page rendered as a blog post — sidebar, date, no map, no structure —
so a visitor who had decided to get in touch was given the least useful page on the site.

**Invisible, and worse:** the contact form posted to a placeholder Formspree ID that returned
**404**. Every enquiry submitted through it was silently discarded. A static site on GitHub
Pages has no backend, so "add a form" is not a solved problem — it needs a third-party
endpoint or a client-side compose, and the first attempt shipped a dead one.

## User Scenarios

### US1 — A visitor sends an enquiry that actually arrives (P1)
A visitor fills in the form and sends it. The message reaches the business. It is never
accepted-and-dropped.

### US2 — A visitor with no email client still gets through (P1)
The primary route requires no account, no key, no server: it composes the message into WhatsApp,
which is how this customer base already communicates.

### US3 — A visitor finds the shop physically (P2)
Address, hours, phone, email and an embedded map are all on the page.

### US4 — The page looks like the rest of the site (P2)
Contact renders with the site's own hero and card treatment — no blog sidebar, no post date.

## Requirements

- **FR-001** The contact page MUST render through a project layout selected by
  `layout: contact` in front matter, with no blog chrome (sidebar, date, author).
- **FR-002** That layout MUST resolve **on every Hugo version the project builds with** —
  local and CI. Template-lookup behaviour differs between versions, and a lookup miss
  degrades silently to the theme default while still returning HTTP 200.
- **FR-003** The primary send route MUST be **WhatsApp**: compose the form fields into a
  prefilled message and open `wa.me/919110624049`. It MUST require no configuration to work.
- **FR-004** A secondary **email** route MUST post to Web3Forms when `params.web3forms_key` is
  set, delivering to the business inbox.
- **FR-005** When no key is configured, the email route MUST degrade to a prefilled `mailto:`
  link — never to a dead POST, and never to a silently failing submit.
- **FR-006** No enquiry route may accept a submission it cannot deliver. An endpoint that
  returns 4xx/5xx is a defect, not a fallback.
- **FR-007** All dead endpoint configuration MUST be removed, not left inert in `hugo.yaml`.
- **FR-008** The Web3Forms access key is public by design (it is a submission target, not a
  credential) and MAY be committed — but this MUST be stated in `hugo.yaml` next to the value,
  so nobody "fixes" it into a secret or panics on finding it.
- **FR-009** The page MUST present info cards (visit / call+WhatsApp / email / hours) and an
  embedded map.

## Success Criteria

| ID | Criterion | Test coverage |
|---|---|---|
| SC-001 | `/contact/` returns 200 | ✅ `scripts/smoke.sh` |
| SC-002 | Renders the project contact layout, not the theme fallback | ✅ `test_contact_page_uses_project_layout` + smoke |
| SC-003 | Form action points at a live endpoint; no Formspree reference survives | ✅ `test_contact_form_endpoint_is_live` + smoke |
| SC-004 | Configured `web3forms_key` is actually rendered into the form | ✅ `test_contact_form_endpoint_is_live` |
| SC-005 | With no key configured, a `mailto:` route is present instead | ✅ same test, `else` branch |
| SC-006 | WhatsApp compose route present | ✅ same test (`wa.me/`) |
| SC-007 | The delivery endpoint is reachable from the internet | ✅ `scripts/smoke.sh` (reachability only — never a POST) |
| SC-008 | Info cards and map render | **OWED** |
| SC-009 | A submitted enquiry is received end-to-end | **OWED — deliberately manual.** Automating it would deliver junk to a real inbox. Verify by hand after any change to the form. |

## Out of Scope

- Spam protection beyond what Web3Forms provides. No CAPTCHA.
- Storing enquiries anywhere in the repo. There is no backend and no database by design.
- Automated end-to-end submission testing — see SC-009.

## As Built

| PR | Date | What |
|---|---|---|
| #5 | 06-06 21:07 | Contact page redesigned: hero, form, info cards, map (`layouts/contact/single.html`) — but pointed at Formspree |
| #6 | 06-06 21:15 | Layout moved `layouts/contact/single.html` → `layouts/_default/contact.html` so the lookup resolves on prod's Hugo 0.144 as well as local 0.147 |
| #7 | 06-06 21:23 | CI Hugo pinned 0.144 → 0.147.2 for dev/prod parity — the root cause behind #6 |
| #10 | 06-06 23:38 | Formspree dropped; WhatsApp compose added as primary; Web3Forms wired for email; dead params removed |
| #11 | 06-07 00:12 | Web3Forms access key set, replacing the `mailto:` fallback |

### The incident record, and what each requirement costs

Four production incidents in one feature, inside 3 hours:

1. **Layout fallback (#6).** The redesigned page was live for 8 minutes rendering as a blog
   post. It returned 200 the entire time — which is why FR-002 exists and why a
   build-success check could never have caught it. Now guarded (SC-002).
2. **Version skew (#7).** #6's fix treated the symptom; the cause was CI building on a
   different Hugo than the author. Now single-sourced in `.hugo-version` with a test
   (`002`, `test_toolchain.py`).
3. **Dead form (#10).** The most serious: enquiries — actual customers — were silently
   discarded for however long the placeholder ID was live. FR-006 is the rule that forbids it.
   Now guarded at build (SC-003) and against production (SC-007).
4. **Unwired key (#11).** The email path existed but delivered nowhere until the key was set.
   FR-004/FR-005 distinguish "configured" from "wired", and SC-004 tests exactly that.

Three of the four were found by a human looking at the live site. The fourth (#7) was found
only because #6 was investigated properly rather than patched.

## Tests Owed

SC-008 only. This feature is now the **best-guarded** part of the site — which is what happens
when four incidents in one area finally produce tests. The instructive point for the backfill:
that coverage was written in PR #14, two months after the bugs, and every one of these guards
could have been written before the feature shipped from the requirements above.
