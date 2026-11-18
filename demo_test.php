<?php
// CLI: php demo_test.php . Does not require MySQL (checks scoring function with sample data).
require __DIR__ . '/includes/functions.php';
$data = require __DIR__ . '/includes/demo_data.php';
$tests = [
    ['budget'=>500, 'skill'=>'intermediate','style'=>'power', 'expectedSeries'=>'ASTROX'],
    ['budget'=>600, 'skill'=>'beginner', 'style'=>'speed', 'expectedSeries'=>'NANOFLARE'],
    ['budget'=>1200, 'skill'=>'advanced', 'style'=>'control', 'expectedSeries'=>'ARCSABER'],
];
foreach ($tests as $test) {
    $found = recommend($data,$test['budget'],$test['skill'],$test['style']);
    $good = count($found) > 0 && $found[0]['series'] === $test['expectedSeries'];
    echo ($good ? 'PASS' : 'FAIL').' '.json_encode($test).' => '.($found[0]['model'] ?? 'none').PHP_EOL;
    if (!$good) exit(1);
}
$none = recommend($data,200,'beginner','power');
assert(count($none) === 0);
echo 'PASS out-of-budget case'.PHP_EOL;
