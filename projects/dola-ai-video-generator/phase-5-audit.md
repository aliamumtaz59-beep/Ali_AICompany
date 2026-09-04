# Phase 5 — Auditor: Risk, Compliance & Governance Review

**Project:** Dola AI Video Generator
**Phase:** 5 of 6

## [Auditor]

### Scope note
This engagement is a consumer/SME SaaS product, **not** a Dynamics CRM system for a regulated
bank. The QCB / on-premise CRM governance framework this company normally audits against does
not apply here in its usual form. This review applies general data-protection, content-safety,
and financial-handling risk instead, and flags where GCC-specific rules could become relevant.

### Findings & recommendations

1. **PII in user-submitted content** (Moderate risk)
   Uploaded product photos and prompts may contain personal data (people's faces, addresses on
   packaging, etc.). Recommend: clear retention policy, a user-facing deletion/takedown path,
   and no use of user-submitted content for model training/improvement without explicit opt-in.

2. **Payment data** (Low risk if handled correctly)
   Use Stripe (or equivalent PSP) tokenized checkout — never store raw card data. This keeps
   PCI scope minimal. Verify webhook signatures for every payment event (also a QA item).

3. **Content moderation / identity misuse** (High risk — needs a hard control)
   Video/voice generation carries deepfake and likeness/voice-cloning misuse risk. Recommend:
   moderation gate before any output is delivered (already required in Phase 2/3 architecture);
   **block or heavily gate any real-person face-swap or voice-cloning feature at MVP**, or
   restrict it to verified enterprise accounts with signed usage agreements. This is a
   reputational and potential legal exposure if left unaddressed.

4. **Data residency** (Low risk at MVP, monitor)
   No strict regulatory requirement for a consumer SaaS launch. However, Qatar's PDPPL and
   broader GCC data-protection direction should be tracked, particularly if the product later
   pursues government or enterprise customers — at that point, regional hosting or data
   segregation may become a hard requirement (as it already is for this company's QDB/CRM work).
   Recommend a clear, published privacy policy from day one regardless.

5. **IP / copyright of generated content** (Moderate risk — contractual, not just technical)
   Generated video content's ownership and permitted-use terms must be explicit in the product's
   Terms of Service. Separately, verify each third-party model provider's contractual
   indemnification regarding their training data and output licensing **before commercial
   launch** — this sits outside engineering and needs a legal/contract review, not just an
   architecture decision.

6. **Vendor concentration / business continuity** (Moderate risk)
   Reliance on third-party generation APIs (Phase 2) is a deliberate, correct MVP strategy, but
   it is also a single point of failure for the whole product if one vendor changes terms,
   pricing, or availability. The provider-abstraction layer in Phase 2/3 is the right mitigation
   — flagging it here as a governance item to keep enforced as the product matures, not just an
   engineering nicety.

7. **Export control / sanctions** — Not applicable at current scope (no dual-use technology,
   no restricted-destination concerns identified for a consumer video SaaS).

### Overall assessment
No blocking issues for proceeding to build, provided the moderation gate (#3) and the ToS/vendor
licensing review (#5) are in place **before public launch**, not deferred to "later." Both are
addressed at the architecture/process level in Phases 2–4; #5 additionally needs a non-technical
legal step the CEO decision (Phase 6) should explicitly call out.
