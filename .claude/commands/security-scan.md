---
description: Run a technical security review of a project's design and integration points
---

# /security-scan <project-name>

Focused technical security pass — narrower and more technical than a
full `/audit`, aimed at concrete attack surface and control gaps
rather than broad governance.

## Steps

1. Read `projects/<project-name>/phase-2-arch.md`, `phase-3-tech.md`,
   and any middleware/crm-developer sections in particular.
2. Call the `auditor` agent (or the `security-engineer` agent instead,
   if one has been added to this company) with instruction to review
   specifically for:
   - Authentication and service account scope (least privilege)
   - Data in transit / at rest handling
   - CRM plugin sandbox boundaries — no direct network calls,
     no credentials in plugin code
   - Queue/message payload exposure (IDs only, no entity data —
     per middleware standards)
   - Input validation and injection risks in API contracts
   - Secrets management (no hardcoded credentials or connection strings)
3. Output findings as a table: `Finding | Severity | Location | Mitigation`.
4. Append to `projects/<project-name>/phase-5-audit.md` under a
   `## Security Scan — <date>` heading, or write standalone to
   `projects/<project-name>/security-scan-<date>.md` if run outside
   a full engagement.

Flag every gap — over-flagging is preferred to missing a risk,
consistent with the auditor's standing governance standard.
