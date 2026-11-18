<?php
declare(strict_types=1);

function unicodeLength(string $s): int { return preg_match_all('/./us', $s) ?: 0; }
function firstUnicodeChar(string $s): string { preg_match('/^./us', $s, $m); return $m[0] ?? '?'; }

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getDbConnection(): PDO {
    $cfg = require __DIR__ . '/../config.php';
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['host'], $cfg['database']);
    return new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function getCatalog(): array {
    try {
        $db = getDbConnection();
        // v0.3.1 migration provides the model-level market price and source.
        $sql = 'SELECT r.id, r.brand, r.model, r.series, r.balance, r.flex, r.weight, r.level, r.demo_price, r.source_url,
                       m.category, m.price_tier, m.price_rm, m.price_source, m.price_url, m.price_checked,
                       m.variant_note, m.spec_note
                  FROM rackets r INNER JOIN racket_market_meta m ON m.model = r.model
                  ORDER BY m.category, m.price_tier, m.price_rm, r.model';
        $rows = $db->query($sql)->fetchAll();
        if (count($rows) > 0) { $GLOBALS['data_mode'] = 'mysql'; return $rows; }
    } catch (Throwable $ex) {
        // If the migration was not imported yet, don't silently use old illustrative prices.
        $GLOBALS['data_mode'] = 'price_reference_fallback';
    }
    $GLOBALS['data_mode'] = 'price_reference_fallback';
    return require __DIR__ . '/market_catalog.php';
}

function referencePrice(array $r): float { return (float)($r['price_rm'] ?? $r['demo_price']); }
function formatPrice(float $value): string { return 'RM '.number_format($value, 2, '.', ','); }
function marketNotice(): string {
    return language() === 'zh'
        ? '参考价格来自马来西亚商家公开挂牌价（球拍资料查阅于2026-10-25，配件另列日期），仅供参考，非实时售价。优惠、库存、重量版本及穿线服务均可能影响最终金额；实际价格与是否有货以商家最新报价为准。GearMate 不提供购买服务。'
        : 'Racket prices are Malaysian retailer public listings checked on 25 Oct 2026 (accessory check dates vary), for reference only—not live quotations. Promotions, stock, weight variants and stringing services may change the final cost. Confirm price and availability with the store. GearMate does not sell products.';
}
function getAccessoryCatalog(): array {
    try {
        $pdo=getDbConnection();
        $records=$pdo->query('SELECT item_code AS code,item_type AS type,display_name AS name,price_rm AS price,price_source AS source,price_url AS url,note_zh,note_en FROM accessory_market_prices ORDER BY item_type,item_code')->fetchAll();
        if($records) return $records;
    } catch (Throwable $ex) {}
    return require __DIR__.'/accessory_catalog.php';
}

function styleLabel(string $style): string {
    return [ 'power' => 'Power / Attacking', 'speed' => 'Speed / Defence',
             'control' => 'Control / Precision', 'allround' => 'All-round' ][$style] ?? 'All-round';
}
function balanceLabel(string $balance): string {
    return ['head-heavy'=>'Head-heavy','head-light'=>'Head-light','even'=>'Even-balanced'][$balance] ?? $balance;
}
function levelLabel(string $level): string {
    return ['beginner'=>'Beginner','intermediate'=>'Intermediate','advanced'=>'Advanced'][$level] ?? $level;
}
function flexLabel(string $flex): string { return ucwords(str_replace('-', ' ', $flex)); }

function scoreRacket(array $racket, string $skill, string $style, int $budget): array {
    // Rule-based prototype. All scores are explainable rule points, NOT AI probabilities.
    $score = 0;
    $reasons = [];
    $wanted = ['power'=>'head-heavy','speed'=>'head-light','control'=>'even','allround'=>'even'][$style];
    if ($racket['balance'] === $wanted) {
        $score += 42;
        $reasons[] = balanceLabel($racket['balance']).' design fits your '.strtolower(styleLabel($style)).' preference';
    } else if ($style === 'allround') {
        $score += 18;
        $reasons[] = 'Can also be considered as an alternative for mixed play';
    } else {
        $score += 5;
    }
    if ($racket['level'] === $skill) {
        $score += 27;
        $reasons[] = 'Recommended for your '.$skill.' skill level (demo suitability tag)';
    } else if (($skill === 'intermediate' && $racket['level'] !== 'intermediate') ||
               ($skill === 'advanced' && $racket['level'] === 'intermediate')) {
        $score += 10;
    }
    $recommendedFlex = [
        'beginner' => ['hi-flex','medium'],
        'intermediate' => ['medium','stiff'],
        'advanced' => ['stiff','extra-stiff'],
    ];
    if (in_array($racket['flex'], $recommendedFlex[$skill], true)) {
        $score += 18;
        $reasons[] = flexLabel($racket['flex']).' shaft flex matches your experience range';
    }
    $ratio = (referencePrice($racket) / $budget);
    $score += $ratio >= .65 ? 13 : 8;
    $reasons[] = 'Within the selected demo budget (RM '.number_format((int)$racket['demo_price']).')';
    return [ 'score'=>min($score,100), 'reasons'=>$reasons ];
}

function recommend(array $all, int $budget, string $skill, string $style): array {
    $ranked = [];
    foreach ($all as $racket) {
        if (referencePrice($racket) > $budget) { continue; } // hard filter
        $rank = scoreRacket($racket, $skill, $style, $budget);
        $racket['score'] = $rank['score'];
        $racket['reasons'] = $rank['reasons'];
        $ranked[] = $racket;
    }
    usort($ranked, static function ($a, $b) {
        return ($b['score'] <=> $a['score']) ?: (referencePrice($a) <=> referencePrice($b));
    });
    return array_slice($ranked, 0, 3);
}
