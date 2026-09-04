# Phase 1 — CEO: Business Understanding & Success Criteria

**Project:** Dola AI Video Generator
**Phase:** 1 of 6

## [CEO]

### Business Objective
Build **Dola AI**, a SaaS product that lets non-technical users (SMEs, e-commerce sellers,
content creators) turn a text prompt or product photo into a short marketing/social video in
minutes, sold on a freemium + credit-based subscription model. Launch first to a market the
founder already has reach into — Qatar/GCC and Pakistan SME/e-commerce sellers — using the
founder's own e-commerce import business as the first proof-of-concept customer.

### Success Criteria (measurable)
1. MVP live within 4 months supporting text-to-video and image-to-video, 3 export presets
   (9:16, 1:1, 16:9).
2. 500 signups and 100 credit-consuming (paying or active free-tier) users within 90 days
   of public launch.
3. Gross margin on generation ≥ 60% at steady state (i.e., what we charge per video covers
   third-party model API cost with healthy margin) — tracked per job from day one.
4. Monthly churn < 5% and NPS > 30 by month 6.
5. At least 20% of early customers sourced from the e-commerce/SME segment, validated first
   internally against the founder's own Qatar↔Pakistan import store.
6. Break-even on direct infra + API costs (not fully loaded, excludes founder's own time)
   within 9 months of launch.

### Strategic Risks
- **Founder-market fit:** this is a technical AI/media-infra build, distinct from the user's
  Dynamics CRM background. Mitigation: build on top of existing third-party generation APIs
  rather than in-house ML — keeps the technical lift closer to integration work, not research.
- **Crowded, fast-moving market:** Runway, Pika, Sora, Luma, Kling, Synthesia, HeyGen, Canva,
  CapCut are all active in this space with far more capital. A generic "AI video generator"
  cannot win head-on. **Recommend narrowing the wedge**: AI product-video generation for
  e-commerce/SME sellers, bilingual English/Arabic, distributed where these sellers already
  are (WhatsApp, Instagram, TikTok) rather than competing as a general-purpose tool.
- **Margin exposure to third-party API pricing** — since generation is outsourced to model
  providers, unit economics move whenever those providers reprice. Needs a provider-abstraction
  layer and credit-based pricing that can be re-tuned without a product rebuild (see Phase 2).
- **Content risk:** generated video/voice can be misused (deepfakes, likeness/voice cloning).
  Needs a moderation gate and a policy on identity-related features before those ship.
- **Data residency:** no strict regulatory requirement for a consumer SaaS today, but Qatar's
  PDPPL and general GCC data-protection trends should be tracked if enterprise/government
  customers are pursued later — this is materially lighter than the QCB/on-prem constraints
  that apply to this company's usual banking-CRM engagements, and should not be over-engineered
  for at MVP.
- **Bandwidth/capital:** this is a side project alongside a full-time role and an existing
  e-commerce venture. Recommend validating with a thin prototype before committing to a full
  custom build (see Phase 6).

### Final Decision (Phase 1 gate)
**Revised — proceed to Phase 2 (Architecture), conditional on:**
1. Narrowing the initial positioning to e-commerce/SME product-video generation (bilingual
   EN/AR) rather than a generic "video generator."
2. Confirming the asset-light strategy: wrap third-party generation APIs, do not train or
   self-host generative video models at MVP.

Not rejected — the underlying idea is viable as a thin, well-positioned wrapper product.
Not unconditionally approved — "video generator" as literally stated is too broad to compete
against incumbents with materially more capital; the niche and the build strategy need to be
locked before backend/frontend work starts.
