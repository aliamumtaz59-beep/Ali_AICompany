---
description: Scaffold a new specialist agent definition file for this company
---

# /create-expert <role-name> <one-line-purpose>

Meta-skill for extending the company with a new specialist agent
without hand-writing the frontmatter and structure each time.

## Steps

1. Confirm the role doesn't already exist in `.claude/agents/`.
2. Ask the user (if not already given) which phase or pattern this
   agent belongs to:
   - A Phase 3 parallel specialist (like backend/frontend/middleware/
     crm-developer) — called during full engagements
   - A standalone specialist (like qa/auditor) — called at a fixed
     phase number
   - A consult-only specialist — never auto-invoked by Pattern A,
     only reachable via Pattern B/D
3. Generate `.claude/agents/<role-name>.md` following the existing
   convention used by every other agent in this company:
   - YAML frontmatter with `name` and a multi-line `description`
     stating exactly when to invoke it
   - A responsibilities list (5-7 bullet points, specific not generic)
   - Explicit standards/constraints section if the role has hard rules
     (like crm-developer's sandbox limits or backend's GUID/audit
     field standards)
   - A closing boundary line: "Never produce X" — stating what this
     agent must NOT do, to keep agent boundaries clean
4. Update `CLAUDE.md`'s "Available agents" list to include the new role.
5. If the new agent joins Phase 3, update
   `.claude/agents/orchestrator.md`'s agent table and Pattern A
   parallel-spawn instruction to include it.

Do not invent responsibilities the user didn't specify — ask rather
than guess what the new agent's boundaries should be.
