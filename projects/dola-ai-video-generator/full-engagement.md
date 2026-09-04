# Full Engagement — Dola AI Video Generator

**Date:** 2026-09-04 | **Pattern:** A (Full engagement) | **Status:** All 6 phases complete

## Routing note
Reading "dola ai video generator" as Pattern A (new project, described from scratch). All 6
phases were run against the interpretation captured in `brief.md`.

**Tooling note:** this session had no live Task-tool/subagent infrastructure to spawn the
`ceo`/`architect`/`backend`/`frontend`/`middleware`/`crm-developer`/`qa`/`auditor` agents as
separate processes. The orchestrator produced each phase's output directly, in that role's
voice and scope (per each role's definition file under `.claude/agents/`), and wrote it to the
corresponding phase file. Phase 3 covers backend, frontend, middleware, and CRM-developer
together in one file since they represent one parallel phase, not four separate deliverables.

## [CEO] — Phase 1
Business objective: build Dola AI as an e-commerce/SME-focused, bilingual AI video generator,
asset-light (wraps third-party APIs), first proven against the founder's own e-commerce venture.
Six measurable success criteria set (signups, margin, churn, break-even). Key risk: "video
generator" as stated is too broad to compete against well-funded incumbents — decision is
**Revised**, proceed to architecture conditional on narrowing the niche and confirming the
asset-light strategy. Full detail: `phase-1-ceo.md`.

## [Architect] — Phase 2
**Direct answer to "how do we generate the video":** don't train/host a model — call third-party
generation APIs (Runway/Luma/Kling for video, ElevenLabs for voice, Whisper for captions),
assemble the results with FFmpeg, serve via an async job pipeline (queue → provider call →
FFmpeg assembly → moderation gate → delivery). Self-hosting a generative video model is
explicitly not recommended for MVP (GPU capex, MLOps burden, lower quality than vendor APIs
today). Full pipeline, component list, and stack justification: `phase-2-arch.md`.

## [Backend] / [Frontend] / [Middleware] / [CRM Developer] — Phase 3
Data model and APIs for async generation jobs and a credit ledger (Backend); bilingual
(EN/AR, RTL) creator-studio web UI centered on product-photo-to-video (Frontend); the
provider-orchestration/adapter/moderation layer that actually implements the Phase 2 pipeline
(Middleware); confirmed **not applicable** to MVP, with a possible future Dynamics 365 upsell
integration noted (CRM Developer). Full detail: `phase-3-tech.md`.

## [QA] — Phase 4
Test strategy across pipeline correctness, billing/credit accuracy, provider resilience,
moderation-gate enforcement, bilingual/RTL correctness, export format correctness, security,
edge cases, and load. Exit criteria defined for MVP sign-off. Full detail: `phase-4-qa.md`.

## [Auditor] — Phase 5
No blocking issues. Two pre-launch (not pre-build) conditions: the content-moderation gate must
be functioning, and third-party model providers' content-licensing terms must be legally
reviewed, before public/commercial launch. Data residency and PII handling flagged as
low-to-moderate risk to monitor, not blockers. Full detail: `phase-5-audit.md`.

## [CEO Final Decision] — Phase 6
**Approved** — proceed with a 3-week thin prototype first (one video API + one voice API +
FFmpeg assembly + download link) to validate real per-video cost against the margin target,
before committing to the full MVP build. Full detail: `phase-6-ceo.md`.

---

## Direct answer: how do we actually generate the video?

1. **Don't build a video-generation model.** Call an existing AI video API.
2. **Pick one provider to start** — e.g., **Runway** or **Luma AI** for text/image-to-video, and
   **ElevenLabs** for voiceover (it has Arabic voices, relevant to this positioning).
3. **Send it a prompt or a product photo** → it returns a short clip (typically 4–10 seconds).
4. **Stitch multiple clips together with FFmpeg** (open source, runs on any server) to reach a
   usable 15–30 second video, adding your logo, captions (via Whisper transcription), and music.
5. **Do this asynchronously** — generation takes seconds to minutes, so the request creates a
   job, and the app polls or gets a webhook when it's ready; it's never a blocking request.
6. **Track the actual API cost of every job** against what you charge in credits — this is your
   margin, and it's the single most important number to watch as a side project.

This is the same approach nearly every "AI video generator" product on the market uses under
the hood at launch — the product differentiation is in the UX, the templates, and the niche
(here: e-commerce/SME product videos, bilingual EN/AR), not in owning the generation model.

## Open items before further phases (e.g., a Phase 3 revision into buildable tickets)
- Confirm the assumptions in `brief.md` (brand name, language priority, build approach, budget).
- Pick the specific first-choice video and voice API vendors (pricing/quality comparison not
  yet done — reasonable next step if this proceeds).
- Legal review of vendor content-licensing terms (Phase 5, item 5) before commercial launch.
