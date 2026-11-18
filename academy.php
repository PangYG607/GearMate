<?php require __DIR__.'/includes/language.php';$zh=language()==='zh';$page='Academy';require __DIR__.'/includes/header.php';
$terms=[
 ['3U / 4U / 5U','球拍重量等级，数字越大通常越轻；同型号具体克重依厂家标示。','Racket weight classes; a larger number typically means a lighter racket. Check exact manufacturer specs.'],
 ['Head-heavy / 头重','拍头惯性较明显，可能适合偏进攻需求，但挥拍和防守手感因人而异。','More head inertia; can suit attacking preference but handling varies by player.'],
 ['Head-light / 头轻','挥动轻快是常见取向，但不代表所有头轻拍都更适合防守。','Often quicker-handling; not automatically superior for every defensive player.'],
 ['Even balance / 平衡型','拍头与拍柄重量分配相对均衡，常作为综合打法选择。','Balanced weight distribution; often considered for all-round use.'],
 ['Shaft flex / 中杆硬度','偏软中杆、适中、硬中杆的反馈不同；并不是越硬越专业。','Flexible, medium, and stiff shafts feel different; stiffer is not always better.'],
 ['Sweet spot / 甜区','球拍击球更容易获得有效反馈的区域；稳定击中有助于发挥球拍性能。','The area where contact is most effective; consistent clean contact matters.'],
 ['String gauge / 球线线径','线径以毫米表示；细线可能更清脆，但耐用性还取决于具体材料和用法。','String thickness in mm; thin strings may feel crisp, but durability depends on construction and use.'],
 ['String tension / 穿线磅数','高磅不等于更强。高磅通常更不宽容，要核实球拍型号和重量版本上限。','Higher is not always stronger. High tension is often less forgiving; respect manufacturer variant limits.'],
 ['Grip / 手胶','湿黏、干爽、毛巾胶及缓冲厚度带来不同手感，选择应以舒适和防滑为主。','Tacky, dry, towel and cushioned grips affect comfort and handling.'],
];
?><main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / GEAR ACADEMY</p><h1><?= $zh?'羽球装备入门知识':'GEAR ACADEMY' ?></h1><p><?= $zh?'看懂问卷和器材规格，不再只凭型号或价格选择。':'Understand racket terminology before making a choice.' ?></p></div></section><section class="section"><div class="container academy-grid">
<?php foreach($terms as $item):?><article class="profile-card"><h2><?= e($item[0]) ?></h2><p><?= e($item[$zh?1:2]) ?></p></article><?php endforeach; ?></div></section></main><?php require __DIR__.'/includes/footer.php'; ?>
