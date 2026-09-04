# Phase 6 — CEO: Final Decision

**Project:** Dola AI Video Generator
**Phase:** 6 of 6

## [CEO Final Decision]

**Decision: Approved to proceed — start with a scoped prototype, not a full build.**

### Justification
The architecture (Phase 2), technical design (Phase 3), test strategy (Phase 4), and risk
review (Phase 5) all converge on a coherent, buildable plan: an asset-light wrapper around
third-party AI video-generation APIs, positioned narrowly at e-commerce/SME product videos
(bilingual EN/AR) rather than competing as a generic tool. This is achievable without the
capital or ML-research investment that would be required to train or host proprietary models.

No phase raised a blocking issue. Two items are conditions on **launch**, not on starting build:
- Content moderation gate must be functioning before any output reaches a user (Phase 3, Phase 5).
- Vendor/ToS legal review on generated-content licensing must complete before commercial launch
  (Phase 5) — this is a legal task, not an engineering one, and can run in parallel with build.

### Recommended path forward (given this is a side project)
1. **Weeks 1–3: thin prototype**, not a full product — a simple form that takes a prompt or
   product photo, calls one video-generation API and one voice API, assembles the result with
   FFmpeg, and returns a download link. This validates the core loop from Phase 2 with minimal
   investment and lets you sanity-check actual per-video API cost against the Phase 1 margin
   target before committing further.
2. **Weeks 4–8:** if the prototype validates demand and margin, build the MVP scope from
   Phases 2–4 (accounts, credits/billing, async job queue, bilingual UI, moderation gate).
3. Use the e-commerce venture as the first real customer/case study (Phase 1 criterion #5) —
   generate product videos for actual listings before opening signups broadly.
4. Re-run Phase 5 legal/vendor-licensing check before taking payment from the public.

### Success criteria to track from day one (restated from Phase 1)
- Cost-per-video vs. margin target (≥60% gross margin) — instrument this from the prototype,
  not after MVP.
- 500 signups / 100 active paying users within 90 days of public launch.
- <5% monthly churn, NPS > 30 by month 6.
