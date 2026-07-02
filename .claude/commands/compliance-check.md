---
description: Run a QCB/governance compliance check against a project's phase outputs
---

# /compliance-check <project-name>

Invoke the **auditor** agent to run a focused compliance pass over an
existing engagement, independent of a full Phase 5 review.

## Steps

1. Read all available files under `projects/<project-name>/`
   (brief.md, phase-1 through phase-6, full-engagement.md — whichever exist).
2. Call the `auditor` agent with:
   - The business objective and success criteria (from phase-1-ceo.md)
   - The architecture and data model (from phase-2-arch.md, phase-3-tech.md)
   - Instruction: **compliance-only pass** — do not repeat a full risk
     review, focus specifically on:
     - QCB supervisory alignment
     - Data residency / Qatar sovereignty
     - Audit trail completeness and immutability
     - Service account least-privilege
     - Rule/config change chain of custody
3. Output a pass/fail per compliance dimension with specific citations
   to the reviewed documents (not generic statements).
4. Append the result to `projects/<project-name>/phase-5-audit.md` under
   a `## Compliance Check — <date>` heading. Do not overwrite prior content.

If no phase files exist yet for the named project, say so and stop —
do not fabricate compliance findings against a project that hasn't
gone through Phase 1–3.
