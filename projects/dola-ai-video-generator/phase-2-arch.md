# Phase 2 — Architect: Architecture & "How Do We Actually Generate the Video?"

**Project:** Dola AI Video Generator
**Phase:** 2 of 6

## [Architect]

### The core question: how do we generate the video, concretely?

There are three real options. For a side-project with no existing ML/infra team, only one of
them makes sense to start with.

**Option 1 — Call third-party AI video-generation APIs (recommended).**
You do not train or host a video-generation model. You send a prompt/image to a vendor's API,
they return a rendered clip, you assemble/brand it and serve it to your user. This is how the
large majority of "AI video generator" products on the market actually work under the hood.

Concrete building blocks (pick 1–2 per category to start, add more later behind an abstraction
layer so you're never locked to one vendor):
- **Text/Image → Video:** Runway (Gen-3/Gen-4), Luma AI (Dream Machine), Kling AI, Google Veo,
  Pika Labs. These take a prompt and/or a still image and return a short video clip (typically
  4–10 seconds). For "turn a product photo into a video," image-to-video is the relevant mode.
- **Voiceover / narration:** ElevenLabs (best quality, has Arabic voices) or Azure/Google TTS.
- **Background music / sound:** stock libraries (Epidemic Sound API, Soundstripe) rather than
  generative audio at MVP — cheaper and legally simpler.
- **Subtitles/captions:** OpenAI Whisper (transcription) to auto-generate burned-in captions.
- **Stitching/branding:** these vendor clips are short (4–10s); to get a usable 15–30s social
  video you generate 2–4 clips and assemble them with **FFmpeg** (open source, runs on your own
  server) — add your logo, brand colors, captions, and transitions in this step. This assembly
  step is code you own; the generation itself is bought.

**Option 2 — Self-host an open-source video model** (e.g., Stable Video Diffusion, CogVideoX,
Mochi). Possible, but requires GPU infrastructure (expensive, A100/H100-class hardware or
rented GPU cloud), MLOps effort, and generally lower quality than the closed vendor APIs today.
**Not recommended for MVP** — this is the path that turns a side project into a capital-intensive
infra business before you've validated anyone wants the product.

**Option 3 — No-code wrapper on top of Option 1** (e.g., a simple web form → API call → FFmpeg
job → download link, built fast with minimal custom infra) to validate demand in 2–3 weeks
before writing a full product. **Recommended as the very first step** — see Phase 6.

### End-to-end pipeline (what happens when a user clicks "Generate")
1. User submits a prompt and/or uploads a product photo + picks a template/aspect ratio.
2. Backend creates a `GenerationJob` (status: queued), deducts credits (pre-authorized).
3. Job goes on a queue; a worker calls the chosen provider's API (image-to-video or
   text-to-video). This is **asynchronous** — generation takes anywhere from ~30 seconds to a
   few minutes, so the UI polls or listens for a webhook, it never blocks on the request.
4. Provider returns one or more short clips → worker downloads them, runs them through the
   **FFmpeg assembly step** (stitch clips, overlay logo/captions/music, encode to the target
   platform's spec).
5. Output is pushed to object storage (S3-compatible) behind a CDN; job marked complete;
   moderation check runs before the download link is released to the user.
6. If the provider call fails or times out, the job is retried (bounded) or marked failed and
   credits are refunded automatically.

### System components
- **Frontend (web app):** creator studio — prompt/template input, photo upload, progress view
  for async jobs, brand kit, export presets per platform, bilingual EN/AR with RTL support.
- **Backend API:** auth, billing/credits, project & job management, exposes the "generate" and
  "job status" endpoints.
- **Orchestration/worker service:** the piece that actually talks to the model providers —
  queue consumer, provider adapters, FFmpeg assembly, moderation gate, cost tracking per job.
- **Job queue:** e.g., Redis + BullMQ, or SQS — needed because generation is slow and async.
- **Storage:** S3-compatible object storage + CDN for uploaded assets and rendered output.
- **Database:** Postgres (users, projects, jobs, credit ledger, subscriptions), Redis (queue/cache).
- **Billing:** Stripe (or a regional PSP if needed for Qatar/Pakistan payment methods) plus an
  internal append-only credit ledger.

### Architecture principles applied
- **Provider abstraction, not a hard dependency on one vendor.** Model providers change price
  and quality frequently in this space; the orchestration service should treat "generate a clip
  from a prompt" as an internal interface with swappable adapters per vendor.
- **Async by default.** Generation is a long-running operation; nothing in this pipeline should
  be a synchronous request/response.
- **Cost tracking is a first-class concern**, not an afterthought — log actual provider cost per
  job next to the credits charged, because margin (Phase 1 success criterion #3) depends on it.
- **Moderation before delivery**, always, given identity/likeness misuse risk (see Phase 5).
- **Config-driven, versioned:** prompt templates, provider selection, and pricing tables should
  be data, not hardcoded, so they can change without a redeploy.

### Technology stack (justified)
- **Frontend:** Next.js/React — fast iteration, good SEO for a marketing site, strong RTL/i18n
  ecosystem for the Arabic requirement.
- **Backend/orchestration:** Node.js (NestJS) or Python (FastAPI) — both have mature async job
  and queue support; either is a reasonable, ordinary SaaS backend choice. (ASP.NET Core is a
  viable alternative if the user wants to lean on existing C# familiarity — the async job-queue
  pattern above maps cleanly onto Azure Functions + Service Bus — but it is not required by
  anything in this architecture; it's a "use what you already know" option, not a technical need.)
- **Data:** Postgres + Redis — proven, cheap to run at MVP scale.
- **Storage/CDN:** any S3-compatible object store + CDN — needed for serving video efficiently.
- **Model providers:** per the pipeline above — start with one image/text-to-video vendor and
  one voice vendor; add redundancy once volume justifies it.
- **Hosting:** any mainstream cloud (AWS, Azure, or GCP) — no requirement in this architecture
  forces a specific one; Azure may be operationally convenient given the user's existing
  Microsoft ecosystem familiarity, but this is a convenience choice, not an architectural one.

### Note on scope vs. this company's usual engagements
This is a standalone consumer/SME SaaS product, not a Dynamics CRM extension. CRM
plugin/on-premise constraints and the 2-minute plugin execution limit that normally apply to
this company's Dynamics/QDB work **do not apply here** — see the CRM Developer section in
`phase-3-tech.md` for where a future Dynamics integration could still be relevant as an upsell.

### Architectural risks
- Vendor cost/quality volatility (mitigated by the abstraction layer above).
- FFmpeg assembly/rendering pipeline complexity and worker autoscaling under load.
- Arabic voice/subtitle quality may lag English on some vendors — validate before committing
  to a single voice provider.
- Content moderation must be a hard gate, not best-effort, given misuse risk.
