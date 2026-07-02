---
description: Assess whether a project is ready to deploy — checks all phases are complete and signed off
---

# /pre-deploy <project-name>

Go/no-go readiness gate. Does not re-do analysis — verifies the
engagement's own artifacts are complete and consistent.

## Steps

1. Check `projects/<project-name>/` for the presence and completeness of:
   - `phase-1-ceo.md` — success criteria defined
   - `phase-2-arch.md` — architecture finalized (no open questions)
   - `phase-3-tech.md` — backend, frontend, middleware, and
     crm-developer sections all present
   - `phase-4-qa.md` — test strategy exists and covers Phase 3 scope
   - `phase-5-audit.md` — auditor findings exist, no unresolved
     Critical/High findings
   - `phase-6-ceo.md` — final decision recorded as **Approved**
2. If any file is missing or a phase-6 decision is not "Approved",
   **stop and report** exactly what's missing — do not proceed to a
   readiness verdict.
3. If all present and approved, call `auditor` for a final lightweight
   check specifically on:
   - CRM plugin sandbox/timing constraints respected
   - Service account provisioning complete
   - Rollback plan exists
4. Output a single verdict: **GO** or **NO-GO**, with a numbered list
   of any blocking items for NO-GO.

Write the result to `projects/<project-name>/pre-deploy-<date>.md`.
