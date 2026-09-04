# Project Brief — Dola AI Video Generator

**Date:** 2026-09-04
**Requested by:** waqas1991@ymail.com
**Raw instruction:** "dola ai video generator" (4 words — interpreted by orchestrator)

## Interpretation

Treated as a **new business/product engagement (Pattern A — Full engagement)**: the user wants
to explore/build an AI-powered video generation product, working name **"Dola AI"**. This is a
side-project idea outside the user's day job (MS Dynamics CRM consulting, ~13 years), in the
same spirit as their other explorations (Qatar↔Pakistan e-commerce import business, investments).

### Working definition
Dola AI = a SaaS tool/platform that lets a non-technical user turn a text prompt, product photo,
or short script into a short video (social media / marketing / product-showcase video) using
AI generation models — not a from-scratch AI research project, not a Dynamics CRM extension.

### Assumptions made (please confirm or correct)
1. "Dola AI" is the product/brand name, not a reference to an existing product.
2. Primary use case at MVP: **text-to-video and image-to-video for short-form marketing/social
   videos** (9:16 Reels/TikTok/Shorts, 1:1, 16:9) — not long-form film generation.
3. Target users: SMEs, e-commerce sellers, and content creators — with a natural first customer
   being the user's own Qatar↔Pakistan e-commerce venture (product videos for listings/ads).
4. Business model: freemium + credit-based subscription SaaS, web app + API.
5. Build strategy: **wrap third-party AI video/audio/image generation APIs** (asset-light) rather
   than train or host proprietary generative models — critical for a side-project's economics.
6. Not a Dynamics CRM / QDB-regulated engagement — standard consumer/SME SaaS, hosted on public
   cloud, with only light data-residency consideration for Qatar-based users.

### Open questions for the user
- Is "Dola AI" purely a working name, or is there an existing brand/domain reserved?
- Primary language priority: English only at MVP, or English+Arabic (RTL) from day one?
- Is the intent to build this personally/solo, hire developers, or use a no-code/API-wrapper
  approach first to validate demand before custom-building?
- Budget/timeline expectations, given this runs alongside a full-time job?
- Any interest in tying this into the e-commerce business as the first paying use case
  (auto-generate product videos from listing photos)?

## Engagement scope run
Pattern A — Full engagement, all 6 phases executed by the orchestrator (acting directly as
each specialist role — see note in `full-engagement.md` on tooling constraints in this session).

## Files in this engagement
- `brief.md` — this file
- `phase-1-ceo.md` — business framing, success criteria, strategic risks
- `phase-2-arch.md` — architecture, technology stack, **and the concrete "how do we actually
  generate the video" build path**
- `phase-3-tech.md` — backend, frontend, middleware, CRM-developer perspectives (parallel phase)
- `phase-4-qa.md` — test strategy
- `phase-5-audit.md` — risk, compliance, governance review
- `phase-6-ceo.md` — final approve/reject/revise decision
- `full-engagement.md` — consolidated summary
