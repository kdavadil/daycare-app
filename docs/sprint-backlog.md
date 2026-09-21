# Sibol — four-week sprint backlog

Saved from the planning conversation on 21 September 2026. Estimates remain provisional; see technical-design.md for additional foundation work and dependencies.

For a one-month build, I’d target a **Sibol pilot with one preschool**, covering the parent app and a lightweight staff portal. The core outcome: teachers record a child’s day, parents receive updates, and the school manages pickup and fees.

**Planning assumptions:** four one-week sprints; two full-time engineers; part-time design/product and QA support; a responsive web app/PWA for parents and staff. Native app-store releases would be a follow-up. With one engineer, we should reduce scope.

All estimates below are relative story points, not days. The proposed **80-point backlog is provisional** until the team checks its capacity.

### MVP boundaries

**Included:** invitations, parent/staff permissions, child profiles, attendance, daily journal, messaging, approved pickup contacts, announcements, invoices, and manually verified payments.

**Deferred:** live GCash/Maya checkout, payroll, enrollment workflows, curriculum assessments, automated billing, offline editing, and multi-school administration.

For the pilot, parents can record a payment made through the school’s existing channels; staff verify it. Live payment integration becomes a stretch goal only if provider access is ready early.

### Sprint 1 — Accounts, access, and attendance
**Goal:** A parent and teacher can securely access the correct child, and the teacher can record attendance. **20 points**

| ID | User story | Acceptance criteria | Points |
|---|---|---|---:|
| SIB-01 | As a school administrator, I want to invite staff and parents so only approved people can access our school. | Admin creates an invitation with a role; recipient activates an account; expired invitations can be reissued; users can sign out and recover access. | 5 |
| SIB-02 | As a parent, I want access limited to my children so their information stays private. | Server enforces school, class, and guardian permissions; teachers see assigned classes; unrelated users cannot access child records or media through direct links. | 5 |
| SIB-03 | As an administrator, I want to manage classes, children, and guardian relationships so accounts connect to the right records. | Admin creates/edits records, assigns teachers, and links guardians; a guardian can have multiple children; deactivating access preserves historical records. | 3 |
| SIB-04 | As a teacher, I want to check children in and out so attendance is accurate. | Staff records arrival/departure; duplicate or invalid transitions are blocked; entries show actor and Philippine local time; corrections retain the original entry and reason. | 5 |
| SIB-05 | As a parent, I want a mobile home screen showing my child’s school status so I can quickly check their day. | Shows selected child, class, latest attendance, and navigation; supports multiple children; includes loading, empty, and error states. | 2 |

**Sprint demo:** Invite a parent, link a child, check the child in as a teacher, and see the status on the parent’s phone.

### Sprint 2 — Daily updates and family communication
**Goal:** Teachers share meaningful updates and parents can respond. **20 points**

| ID | User story | Acceptance criteria | Points |
|---|---|---|---:|
| SIB-06 | As a teacher, I want to post a learning moment with a photo so families can see their child’s activities. | Teacher selects authorized recipients, adds text and one photo, and publishes; uploads are validated and privately stored; staff can correct or remove a post. | 5 |
| SIB-07 | As a teacher, I want to log meals, rest, and care activities so parents understand their child’s day. | Structured entries include category, time, and optional note; meals include amount eaten; only authorized staff can create or correct entries. | 3 |
| SIB-08 | As a parent, I want a chronological journal so I can follow my child’s updates. | Feed contains only permitted entries; supports pagination, empty states, and a reversible “love” reaction; removed content is no longer accessible. | 3 |
| SIB-09 | As a parent or teacher, I want a child-specific message thread so school communication stays in one place. | Text-only messages appear in order with sender/time; access follows child/class permissions; failed sends can be retried without duplicates; new messages have an unread indicator. | 5 |
| SIB-10 | As an administrator, I want to publish school or class announcements so families receive relevant notices. | Supports title, body, audience, and an urgent flag; parents see only their audience’s notices; urgent notices appear prominently on Home. | 2 |
| SIB-11 | As a parent, I want an in-app notification inbox so I can find new messages and updates. | Notifications link to the correct content, support read/unread state, and do not expose another family’s information. | 2 |

**Sprint demo:** Publish a learning moment and a meal entry, send a parent message, and publish a sample weather-related school notice.

### Sprint 3 — Pickup arrangements and school fees
**Goal:** The school can verify pickup and track invoices without relying on simulated functionality. **20 points**

| ID | User story | Acceptance criteria | Points |
|---|---|---|---:|
| SIB-12 | As a parent, I want to request authorized pickup contacts so family members can collect my child. | Parent submits name and relationship; school approves or rejects the request; only approved contacts are selectable; pickup-only contacts do not gain access to journal, chat, or billing. | 5 |
| SIB-13 | As a parent, I want to select today’s pickup person and time so staff know whom to expect. | Parent selects an approved contact; changes appear to staff with actor/time; unapproved or revoked contacts cannot be selected. | 3 |
| SIB-14 | As a teacher, I want to record a verified handoff so the school has an accurate pickup record. | Staff sees approved contact details and confirms identity verification; records collector and time; checkout is recorded; changes retain an audit trail. | 3 |
| SIB-15 | As an administrator, I want to issue itemized peso invoices so families know what they owe. | Invoice includes child, reference, line items, total, and due date; server calculates totals; draft/issued status exists; issued invoice corrections are recorded. | 5 |
| SIB-16 | As a parent and administrator, we want to submit and verify payment records so balances reflect confirmed payments. | Parent views invoices and submits reference/proof for the exact invoice total; submission stays pending until staff verifies it; approval updates the balance once; rejection includes a reason; proof is privately accessible. | 4 |

**Sprint demo:** Approve Lola Rose, assign her for pickup, record the handoff, issue a ₱6,850 invoice, and verify a submitted payment.

### Sprint 4 — Localization, reliability, and pilot launch
**Goal:** Real school staff and families can use the app reliably on their phones. **20 points**

Reserve roughly half this sprint’s effort for validation and fixes rather than adding features.

| ID | User story | Acceptance criteria | Points |
|---|---|---|---:|
| SIB-17 | As a user, I want English or Filipino interface text so I can use the app comfortably. | Language preference persists; navigation, forms, validation, and statuses are translated; school-authored content remains in its original language; money and dates display consistently. | 3 |
| SIB-18 | As a parent on a slower mobile connection, I want clear loading and retry behavior so I know whether an action succeeded. | Photos are resized; feeds load incrementally; failed requests offer retry; repeated taps do not duplicate writes; stale or unavailable data is clearly identified. | 3 |
| SIB-19 | As a school administrator, I want accountable access and record handling so I can manage sensitive information responsibly. | Access can be revoked; critical changes are logged; production logs exclude message bodies and sensitive child details; retention and deletion procedures are documented and agreed with the pilot school. | 3 |
| SIB-20 | As the delivery team, we want a recoverable production environment so we can support the pilot. | Separate staging/production environments; error monitoring; automated backups with a tested restore; deployment rollback procedure; named support owner. | 3 |
| SIB-21 | As a pilot user, I want the main workflows to work on my phone so I can rely on Sibol during a school day. | Parent/staff acceptance testing covers the full workflow; access-control and billing calculations have automated tests; target mobile browsers are checked; launch-blocking defects are fixed. | 5 |
| SIB-22 | As school staff, I want guided onboarding so our first week runs smoothly. | Pilot roster is checked; invitations are ready; staff receive a short guide and walkthrough; a support channel and pilot feedback process are established. | 3 |

**Sprint demo:** Run a full school day with pilot users—from check-in through updates, messaging, payment review, and pickup.

### Definition of done

A story is complete when:

- Its acceptance criteria pass for every relevant role.
- It works on a small mobile screen, including loading, empty, error, and success states.
- Permissions are enforced by the server.
- Relevant automated checks pass and the change is reviewed.
- It is deployed to staging and accepted by the product owner.

### Decisions to settle on day one

| Decision | Recommended starting point |
|---|---|
| Pilot audience | One preschool, one or two classes, a small invited parent group |
| Delivery platform | Mobile-first PWA plus responsive staff portal |
| Messaging | One shared school thread per child; text only |
| Payments | Manual verification; no automated settlement or refunds |
| Pickup approval | School-approved contacts; staff verifies identity at handoff |
| Alerts | In-app notifications; email/push delivery evaluated separately |
| Language | English default, Filipino interface option |

If capacity falls behind, cut **reactions, full Filipino translation, and payment-proof submission** first; staff can record verified payments manually. Preserve access controls, attendance accuracy, pickup verification, and launch testing.

**Month-one success:** the pilot school completes daily attendance, updates, communication, pickup, and invoice tracking in Sibol, with no unresolved launch-blocking defects.
