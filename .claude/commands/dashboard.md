---
description: Show an executive-level status summary across all projects
---

# /dashboard

Cross-project view for the CEO — no agents are called, this only
reads and summarizes existing files under `projects/`.

## Steps

1. List every subdirectory under `projects/`.
2. For each project, inspect which phase files exist and read
   `phase-6-ceo.md` if present for the final decision.
3. Produce a table:

   | Project | Phases Complete | Current Phase | CEO Decision | Last Updated |
   |---------|------------------|----------------|---------------|---------------|

   - "Phases Complete" = count out of 6 (or "brief only" if just
     `brief.md` exists)
   - "CEO Decision" = Approved / Rejected / Revised / Pending
   - "Last Updated" = most recent file modification time in that
     project's directory

4. Below the table, call out:
   - Any project stalled mid-phase (a phase file is missing while a
     later one exists — indicates a broken sequence)
   - Any project with an unresolved Rejected/Revised decision

Do not call any specialist agents for this command — it is a read-only
reporting view, matching Orchestrator Pattern F.
