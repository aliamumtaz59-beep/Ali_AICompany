---
description: Quick status check on a single project — what phase it's on and what's next
---

# /status <project-name>

Single-project version of `/dashboard` — a fast read, no agents called.

## Steps

1. Read the contents of `projects/<project-name>/`.
2. Report:
   - Which phase files exist (1 through 6) and which are missing
   - The most recently modified file and when
   - If `phase-6-ceo.md` exists: the final decision and its
     justification, quoted directly
   - If not: what the next phase in sequence should be
     (per the orchestrator's Pattern A ordering: ceo → architect →
     backend/frontend/middleware/crm-developer → qa → auditor → ceo)
3. Keep the output to a few lines — this is a quick check, not a report.
   Use `/dashboard` for a full cross-project view, or ask the
   orchestrator directly for "what did we decide on X" (Pattern F)
   for a narrative recap.
