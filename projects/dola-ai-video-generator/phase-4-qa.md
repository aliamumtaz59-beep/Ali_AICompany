# Phase 4 — QA: Test Strategy

**Project:** Dola AI Video Generator
**Phase:** 4 of 6

## [QA]

### Test strategy areas
1. **Generation pipeline correctness**
   - Job state machine: queued → processing → rendering → complete/failed, no illegal
     transitions, no jobs stuck in `processing` indefinitely (timeout handling).
   - Provider webhook idempotency: duplicate/replayed webhook delivery must not double-process
     a job or double-charge/refund credits.
   - Retry logic: bounded retries on transient provider failures, correct dead-letter handling
     when retries are exhausted.

2. **Billing / credit accuracy**
   - No double-charge on client retry of `generate` (idempotency key enforcement).
   - Automatic refund on job failure, verified against the ledger, not just the UI.
   - Race condition test: two simultaneous generate requests from the same user near their
     credit limit — must not both succeed if funds are insufficient for both.
   - Stripe webhook handling for subscription upgrade/downgrade/cancellation edge cases.

3. **Provider resilience**
   - Simulate a provider outage/timeout — confirm graceful failure, refund, and (if configured)
     fallback to a secondary provider rather than a silent hang.
   - Cost-tracking accuracy: logged provider cost per job matches actual vendor billing.

4. **Content moderation gate**
   - Verify no generated output reaches a user-facing download/share link before the moderation
     check has run and passed.
   - Test moderation bypass attempts (e.g., direct object-storage URL guessing) are blocked.

5. **Bilingual / RTL**
   - Full UI walkthrough in Arabic: layout mirroring, form input direction, date/number
     formatting.
   - Arabic voiceover and caption quality spot-checked against the chosen vendor(s) — this was
     flagged in Phase 2 as a possible quality gap.

6. **Export correctness**
   - Each platform preset (9:16, 1:1, 16:9) produces correct resolution, aspect ratio, and
     codec/format accepted by the target platform.

7. **Security**
   - Auth flows, signed/expiring URLs on private asset storage, webhook signature verification
     for every provider and for Stripe (reject unsigned/forged callbacks).

8. **Edge cases**
   - Empty prompt, oversized upload, unsupported file type/codec, expired job-status polling,
     user cancels mid-generation, credit balance hits zero mid-job.

9. **Performance / load**
   - Concurrent job submission load test against queue backpressure and provider concurrency
     caps (from Phase 3 middleware).
   - Wall-clock render-time SLA measured per template/preset, tracked against user-facing
     "estimated time remaining" messaging.

### Exit criteria for MVP QA sign-off
- Zero known double-billing or credit-leak defects.
- Moderation gate verified unbypassable in test.
- Provider outage simulation passes with correct refund behavior.
- Arabic UI/voice/caption path validated end-to-end at least once before public launch.
