# Phase 3 — Technical Design (Backend, Frontend, Middleware, CRM Developer — run in parallel)

**Project:** Dola AI Video Generator
**Phase:** 3 of 6

## [Backend]

**Data model:**
- `User` (auth, plan tier, locale)
- `Project` (owns assets + jobs)
- `Asset` (uploaded photo/logo/brand asset, storage URL)
- `GenerationJob` (status: queued/processing/rendering/complete/failed; provider; prompt;
  cost_cents; output_url; retry_count)
- `CreditLedger` (append-only transaction log: debit on job create, credit on refund)
- `Subscription`/`Plan` (Stripe-linked)

**Key APIs:**
- `POST /projects`, `POST /projects/{id}/generate` (creates an async `GenerationJob`)
- `GET /jobs/{id}` (poll status) + `POST /webhooks/{provider}` (provider callback)
- `POST /webhooks/stripe` (subscription/payment events)

**Business logic:**
- Credit costing engine: price per job derived from provider cost (per-second/per-resolution)
  plus target margin (Phase 1 criterion: ≥60% gross margin).
- Pre-authorize credits on job creation; refund automatically on job failure.
- Idempotency keys on `generate` requests so a client retry never double-charges or double-runs
  a job.
- Rate limiting per plan tier to bound concurrent generation cost exposure.

**Stack note:** Node/TypeScript or Python is the natural default for a greenfield SaaS product.
ASP.NET Core is a valid alternative if leaning on existing C# experience is preferred — the
async job/queue pattern (Azure Functions + Service Bus) maps directly onto this design — but
nothing here requires it.

## [Frontend]

- Web-first, responsive "creator studio": prompt/template input, drag-and-drop photo upload
  (product photo → auto product video is the flagship e-commerce use case), live async job
  progress (poll or websocket), simple trim/timeline editor, brand kit (logo, colors), export
  presets per destination platform (TikTok, Instagram Reels, YouTube Shorts, WhatsApp Status).
- Bilingual UI (English + Arabic) with full RTL layout support from day one, per Phase 1 scope.
- Template gallery curated toward e-commerce workflows: product showcase, before/after,
  testimonial-style — reinforcing the narrowed positioning from Phase 1.
- Credit balance / usage widget visible at all times, since cost-per-generation is central to
  the business model.
- Not applicable: Power Apps / PCF / model-driven forms — this is a public SaaS product, not
  an internal Dynamics app, so those patterns don't apply here.

## [Middleware]

- **Provider orchestration service**: internal uniform `GenerationRequest` schema, translated
  per vendor via adapters (Runway/Luma/Kling for video, ElevenLabs for voice, Whisper for
  captions) — this is the layer that implements the "how do we generate the video" pipeline
  described in `phase-2-arch.md`.
- Async job queue (Redis/BullMQ or SQS) with bounded retry + backoff and a dead-letter queue
  for jobs that exhaust retries.
- Webhook normalization: every provider's callback format is mapped into one internal
  job-status event so the rest of the system only ever sees one shape.
- Cost-tracking middleware: captures actual provider billing per job, feeding the margin
  reporting the CEO's success criteria depend on.
- Content moderation gate: runs before a job is marked "complete" and before its output URL
  is released to the requesting user (see Phase 5 for why this must be a hard gate).
- Quota/concurrency middleware enforcing per-plan-tier limits and per-provider concurrency caps
  (protects against a single user or bug spiking API spend).

## [CRM Developer]

**Not applicable to the MVP.** This is not a Dynamics CRM/Dataverse engagement, and none of the
plugin, entity, security-role, or Power Automate patterns this role normally covers apply here.

The one plausible future touchpoint: if Dola AI later adds an enterprise tier for agencies or
larger SME customers, a Dynamics 365 connector (sync leads/customers/usage into a client's own
CRM via the Dataverse Web API or Power Automate) could be a differentiated upsell, drawing
directly on the founder's CRM background. This is explicitly **out of scope for MVP** and should
not gate or delay the core build.
