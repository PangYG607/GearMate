# GearMate v0.4 — incremental changes from v0.3.1

**Preserved:** Original Week 6 PHP/MySQL architecture, Sport Tech theme, bilingual site, detailed 40-question survey and assessment logic, Yonex source-linked catalogue, Top 3 explainable recommendations and legacy tests.

**New/expanded:**

- Guest mode with temporary PHP Session assessments, recommendations, DIY and setup-card export. Guest registration can carry over the current assessment.
- Separate **Edit Current Assessment**, **New Skill Assessment**, **Update Preferences Only** and **Player Progress** operations, so changing preferences does not generate fake progress records.
- Side-by-side **Racket Battle** for two/three model/variant entries, with source-linked retailer prices and clear provisional-spec labels.
- Bilingual **Gear Academy** explanations for 3U/4U/5U, head balance, shaft, sweet spot, string gauge, tension and grip.
- Detailed DIY builder with source references, date stamps and selectable real products, and a printable bilingual Gear Setup Card, with individual line prices and DIY explanations.
- Important tension integrity: official exact-weight/variant limits only marked verified after administrator entry with manual confirmation; unknown limits produce DRAFT warnings, not an invented safe limit.
- Account Gear Locker with setup-card history and favourites; authorized Admin Racket CRUD with source links, verified Chinese name fields, optional model-matched product photo links and exact tension verification fields.
- Brand filter ready for the later Victor / Li-Ning racket database expansion; no unverified racket model prices invented.
- Added 2 source-linked Victor strings and 2 single-piece Yonex towel grips. Total now 9 strings + 5 grips = 14 accessory entries.
- Added `v04_features_migration.sql` to create new tables (no destructive changes to earlier migrations).
- All user-facing additions work in English and Simplified Chinese, with the current language saved in cookie/session and the registered user account if migrated.

**Known limits / mandatory before public launch:** MySQL registration and permissions need testing on local XAMPP; most racket model variants still need official per-variant verification and photo rights review. A server-side runtime cannot legitimately promise official stringing limits when they have not been sourced.
