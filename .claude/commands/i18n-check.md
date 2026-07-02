---
description: Check Arabic/English (RTL) readiness of UI and form designs
---

# /i18n-check <project-name>

QDB operates in Qatar — Arabic/English bilingual support and RTL
layout are frequently a late-discovered gap. This check surfaces it
early rather than at UAT.

## Steps

1. Read `projects/<project-name>/phase-3-tech.md`, specifically the
   Frontend and CRM Developer sections.
2. Call the `frontend` agent with instruction to review the existing
   UI/form design against:
   - Are all user-facing labels, form field names, and messages
     externalized (no hardcoded English strings)?
   - Does the model-driven app / PCF design account for RTL layout
     (mirrored form layout, right-aligned text, bidirectional-safe
     grid components)?
   - Are dates, numbers, and currency formatted per locale
     (Gregorian vs. Hijri considerations if applicable)?
   - Do dashboards/Power BI reports support Arabic labels?
3. Call the `crm-developer` agent to confirm entity display names,
   option set labels, and security role names have Arabic translations
   registered in the solution (Dynamics CRM label localization).
4. Output a gap list: `Component | Issue | Recommendation`.
5. Append to `projects/<project-name>/phase-3-tech.md` under a
   `## i18n/RTL Review — <date>` heading.

If Arabic/RTL support is explicitly out of scope for this project,
state that assumption clearly instead of flagging false gaps.
