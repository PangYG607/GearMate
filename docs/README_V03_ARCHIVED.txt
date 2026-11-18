BADMINTON GEARMATE · SHOWCASE v0.3
Based on the user's original Badminton_GearMate_Week6_Demo(1).zip and Showcase v0.2.
This is a PHP + MySQL website, NOT a static HTML-only app.

INSTALL WITH XAMPP (WINDOWS)
1. Extract the folder Badminton_GearMate_Showcase_v03 to C:\xampp\htdocs\
2. Open XAMPP Control Panel. Start Apache + MySQL.
3. Go to http://localhost/phpmyadmin
4. If you do not already have the GearMate database:
   - Use phpMyAdmin > Import > database.sql (this creates badminton_gearmate + rackets + users)
   - Then Import > v03_migration.sql (creates player_assessments + saved_setups)
5. If you already imported v0.2 database.sql and registered a user:
   - DO NOT delete your old database. Just Import > v03_migration.sql.
6. Visit: http://localhost/Badminton_GearMate_Showcase_v03/index.php
7. Choose EN / 中文 in the top navigation before login.
8. Click the large Hero 'Find My Perfect Racket' button to register/login.
9. Complete the staged, conditional questionnaire; view radar chart + rationale;
   view Top 3 (or different-price fallback) and choose a racket to customise.

KEY FILES
index.php                  Hero + beginner/next-step spotlight
login.php / register.php   Existing real PHP/MySQL account features, now bilingual
language.php               Session-based language switch
includes/lang/en.php       English site dictionary
includes/lang/zh.php       Natural Simplified Chinese dictionary
includes/question_bank.php 40 candidate bilingual questions
includes/assessment_engine.php Self-reported radar, rule-based scoring, budget fallback
questionnaire.php          Login-required dynamic questionnaire (1-3 questions per step)
analysis.php               Six-axis self-reported radar + interpretation
results.php                Three recommendations + reasons/weaknesses
builder.php                Basic string/tension/grip DIY and save
account.php                Latest player assessment and saved setup
v03_migration.sql          Adds assessment and DIY tables (import after database.sql)

DEMO / ACCURACY NOTES
- Current MySQL catalogue remains 12 YONEX racket models from v0.2. Their demo RM
  prices and level tags are illustrative, NOT verified live Malaysian prices.
- Li-Ning 雷霆小钢炮PRO (AYPU081-4) appears ONLY in the HOME spotlight, not in
  the automatic recommendation catalogue because a verified Malaysian RM price
  has not yet been established. The English product name and photograph are
  linked to a corresponding product listing, with official Chinese naming from
  the Li-Ning 2025 equipment catalogue: https://www.shopnings.com/pub/media/descriptions/2025fallwinter.pdf
- Other racket photos link to external manufacturer/retailer URLs. Internet is
  needed; generic racket art is shown when photos fail to load.
- No manufacturer-certified string tension limits are stored for the 12 Yonex
  demo variants. DIY always warns users to confirm safe lbs with a stringer;
  the 16-32 lbs form limit is not a manufacturer-certified safe limit.
- Player radar is from questionnaire self-reports only. Match % is a deterministic
  compatibility score, NOT an experimentally validated performance probability.
- Assessment is preserved in PHP session even if the new tables were not
  imported, but it is not saved to MySQL until v03_migration.sql is imported.
- Passwords use PHP password_hash and verification; no default/demo user exists.

QUICK TEST WITHOUT MYSQL (site still needs Apache/PHP)
- Home, catalogue, and language toggle load with built-in sample racket data.
- Register/Login/Save requires MySQL and must be verified on the user's XAMPP.
- CLI regression test: php tests_v03.php

STATIC DESIGN PREVIEWS
Preview_Home.html; Preview_Home_ZH.html; Preview_Questionnaire_ZH.html;
Preview_Analysis_ZH.html; Preview_Results_ZH.html; Preview_DIY_ZH.html.
These are snapshots for viewing DESIGN only and are not functional standalone PHP apps.

CREDITS
Yonex catalogue manufacturer/source links are included per racket.
Li-Ning name: brand-published 2025 equipment catalogue above.
Li-Ning homepage model photo from Li-Ning Sports AYPU081-4 source:
https://li-ningsports.co.uk/products/li-ning-axforce-cannon-pro-black-and-blue-racket-aypu081-4
No rights transfer is implied for linked manufacturer/retailer photographs.
