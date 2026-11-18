BADMINTON GEARMATE | SHOWCASE v0.3.1 | YONEX MARKET CATALOGUE UPDATE
=====================================================================
This is an incremental update based on the user's original Week6 PHP/MySQL project,
then Showcase v0.2/v0.3. It preserves login/register, 40-question assessment,
6-axis radar chart, Top 3 rule-based recommendation, and DIY.

WHAT IS NEW
- 38 YONEX catalogue entries spanning Attack / Speed / Control and Entry / Mid / Premium
  with 3 or more entries in each combination. Includes some deliberately labelled
  COLOR and WEIGHT VARIANTS, not 38 independent original models.
- 38 source-linked public Malaysian retailer reference prices (RM) and checked date
  (25 Oct 2026). Some listings may be out of stock, and discounts may change.
- Catalogue style / tier filters, keyword search, bilingual disclaimer,
  per-item reference-price source and retailer link.
- 7 Yonex strings and 3 Yonex grips with source-linked reference prices in DIY.
- DIY removes colour selector (no genuine live product recolouring), shows brief
  item explanations, and warns about tension. All per-racket string tension limits
  still need precise official verification before a real-world setup is accepted.
- Existing SQL database records, original user IDs and account tables stay intact.

XAMPP INSTALL / MIGRATION (EXISTING USER WITH V0.3)
1) BACK UP the badminton_gearmate database using phpMyAdmin > Export > SQL.
2) Unzip this folder to C:\xampp\htdocs\Badminton_GearMate_Showcase_v031\
3) Open XAMPP Control Panel and start Apache + MySQL.
4) Open http://localhost/phpmyadmin and choose badminton_gearmate.
5) Click Import and choose v031_catalog_migration.sql. Click Go.
   Import the NEW MIGRATION ONLY; do not drop/replace your current database.
6) Visit http://localhost/Badminton_GearMate_Showcase_v031/index.php
7) Browse Catalogue > Attack / Speed / Control and Entry / Mid / Premium.
8) Login normally, complete assessment and visit results > DIY to check prices.

XAMPP INSTALL (IF STARTING FROM A COMPLETELY EMPTY DATABASE)
1) Import database.sql first.
2) Import v03_migration.sql second.
3) Import v031_catalog_migration.sql last.
4) Start the website at the v031 URL above.

PRICE DISCLAIMER SHOWN ON THE WEBSITE
中文: 参考价格来自马来西亚商家公开挂牌价（查阅于2026-10-25），仅供参考，非实时售价。优惠、
库存、重量版本及穿线服务均可能影响最终金额；实际价格与是否有货以商家最新报价为准。
GearMate 不提供购买服务。
EN: Prices are Malaysian retailer public listings checked on 25 Oct 2026, for
reference only—not live quotations. Promotions, stock, weight variants and
stringing services may change the final cost. Confirm with the retailer.

PRICE AND DATA INTEGRITY
- Price values are taken from Malaysian online LISTINGS, not guaranteed live prices
  and not necessarily available at checkout. Each listing provides the store and URL.
- Standard/limited-edition colours and 3U/4U weights count as separate catalogue
  entries where indicated. They are not separate families/models; product cards mark this.
- "Entry/Mid/Premium" is PRICE tier, not player SKILL. A premium racket does not
  automatically mean it suits an advanced player.
- IMPORTANT: Brand family positioning is used as a starting point, while the
  weight/shaft and suitable-level tags added for new models are PRELIMINARY. Not every
  product variant's flex and stringing limit was checked against exact official specs.
  Do NOT market this as a professionally validated recommendation system yet.
- DIY estimated total = reference racket price + ONE racket-length string packet +
  ONE overgrip. It EXCLUDES unverified stringing labour, delivery and other fees.
- saved_setups.demo_total is retained as a legacy integer amount. Future migration
  can store precise-decimal pricing snapshots and a price-source audit trail.
- Existing retailer-hosted photos are reused where available; new listings without
  verified matching photographs use the racket drawing fallback. Do not assume the
  drawing accurately depicts the exact model.
- For Racket Battle/full real-product matching, we still need official per-version
  specifications and sources for EVERY racket; the preliminary data must be reviewed.

REFERENCES FOR PRICE EXAMPLES
YONEX models: https://www.rexcoolsports.com/collections/yonex
Sports Direct: https://www.sportsdirect.com.my/yonex/all-yonex-badminton-rackets
Titan: https://titanbadminton.com/collections/yonex-arcsaber-series
Vsmash: https://vsmash.com/product-category/racquet/badminton-racquet/nanoflare
Shin Niji: https://www.shinnijisports.my/products/special-offers
Racket Lab strings: https://racketlab.com.my/collections/badminton-strings
Sports Direct grips: https://www.sportsdirect.com.my/yonex/yonex-accessories
All row-specific sources: includes/market_catalog.php and data_catalogue_reference.json

AUTOMATED TESTS (PHP CLI)
php tests_v03.php
php demo_test.php
php tests_catalog_v031.php

PREVIEWS
Preview_Home_ZH.html / Preview_Home_EN.html
Preview_Catalogue_ZH.html / Preview_Catalogue_EN.html
Static previews show layout but do not run PHP account, database or recommendation flows.

Please keep the v0.3 folder and database backup as an exhibition-safe fallback.
