---
description: Run a comprehensive cross-domain audit of a project (architecture, security, quality, governance)
---

# /audit <project-name>

Broader than `/compliance-check` — this runs a full cross-domain audit
covering architecture soundness, security, code/design quality, and
governance, not just regulatory compliance.

## Steps

1. Read every file under `projects/<project-name>/`.
2. Call these agents **in parallel**, each reviewing the full context
   with a domain-specific lens:
   - `architect` — re-validate architecture decisions still hold;
     flag drift between phase-2 and what phase-3 actually produced
   - `qa` — assess whether the test strategy in phase-4 actually
     covers what backend/frontend/middleware/crm-developer built
   - `auditor` — full governance and compliance review (same depth
     as a normal Phase 5)
3. Synthesize the three outputs into a single report with sections:
   `[Architecture Findings]`, `[Quality Findings]`, `[Governance Findings]`,
   `[Overall Risk Rating]` (Low/Medium/High/Critical).
4. Write the result to `projects/<project-name>/audit-report-<date>.md`.

Use this before a go-live decision or when the CEO calls for a project
health check, not as a substitute for the normal Phase 5 review during
a first-time engagement.
