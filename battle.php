<?php
require __DIR__.'/includes/gear_features.php';
$all=getCatalog();$byId=[];foreach($all as $r)$byId[(int)$r['id']]=$r;
$requested=(array)($_GET['id']??[]);$selected=[];
foreach($requested as $v){if(count($selected)>=3)break;$id=filter_var($v,FILTER_VALIDATE_INT);if($id && isset($byId[$id]))$selected[$id]=$byId[$id];}
$actor=currentActor();$ass=$actor?loadAssessment($actor['id']):null;
$zh=language()==='zh';$page='Battle';require __DIR__.'/includes/header.php';
?>
<main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / RACKET BATTLE</p><h1><?= $zh?'球拍对比':'COMPARE RACKETS' ?></h1><p><?= $zh?'最多三支：比较真实型号、版本、参考价及已知的规格。缺少官方核实的数据不会编造。':'Compare up to three real variants and their sourced prices. Unverified manufacturer details are clearly marked.' ?></p></div></section>
<section class="section"><div class="container"><div class="profile-card"><h2><?= $zh?'选择球拍（最多 3 款）':'Choose up to 3 rackets' ?></h2>
<form action="battle.php" method="get" class="battle-picker">
<?php for($i=0;$i<3;$i++):?><label><?= $zh?'球拍':'Racket' ?> <?= $i+1 ?><select name="id[]"><option value=""><?= $zh?'不选择':'None' ?></option><?php foreach($all as $r):?><option value="<?= (int)$r['id'] ?>" <?= isset($selected[(int)$r['id']]) && array_search((int)$r['id'],array_keys($selected),true)===$i?'selected':'' ?>><?= e($r['model']) ?> · <?= e($r['weight']) ?> · <?= e(formatPrice(referencePrice($r))) ?></option><?php endforeach;?></select></label><?php endfor; ?>
<button type="submit" class="button button-main"><?= $zh?'开始比较':'Compare' ?> →</button></form></div>
<?php if($selected):?><div class="profile-card battle-table-wrap"><h2><?= $zh?'并排比较结果':'Side-by-side comparison' ?></h2>
<div class="battle-scroll"><table class="battle-table"><thead><tr><th><?= $zh?'项目':'Specification' ?></th><?php foreach($selected as $r):?><th><?= e(modelName($r)) ?></th><?php endforeach;?></tr></thead><tbody>
<?php foreach([
  [$zh?'品牌':'Brand','brand'],[$zh?'重量版本':'Weight variant','weight'],[$zh?'价位':'Price tier','price_tier'],[$zh?'主打打法（初步分类）':'Style (provisional tag)','category'],[$zh?'拍头平衡（初步分类）':'Balance (provisional tag)','balance'],[$zh?'中杆（初步分类）':'Shaft (provisional tag)','flex']
] as $spec):?><tr><th><?= e($spec[0]) ?></th><?php foreach($selected as $r):?><td><?= in_array($spec[1],['balance','flex'],true)?e(gameLabel($r[$spec[1]])):e($r[$spec[1]]) ?></td><?php endforeach;?></tr><?php endforeach; ?>
<tr><th><?= $zh?'官方穿线范围（按具体版本）':'Official tension limit (variant)' ?></th><?php foreach($selected as $r):$lim=verifiedTension((int)$r['id']);?><td><?= $lim?((int)$lim['min_lbs'].'–'.(int)$lim['max_lbs'].' lbs'):($zh?'未完成官方核实':'Not manufacturer-verified') ?><?php if($lim):?><br><a href="<?= e($lim['official_url']) ?>" target="_blank" rel="noopener"><?= $zh?'官方来源':'Official source' ?></a><?php endif;?></td><?php endforeach;?></tr>
<tr><th><?= $zh?'马来西亚参考价':'MY reference price' ?></th><?php foreach($selected as $r):?><td><b><?= e(formatPrice(referencePrice($r))) ?></b><br><a href="<?= e($r['price_url']??$r['source_url']) ?>" target="_blank" rel="noopener"><?= e($r['price_source']??'Source') ?></a> · <?= e($r['price_checked']??'') ?></td><?php endforeach;?></tr>
<?php if($ass):?><tr><th><?= $zh?'你的规则匹配度':'Your rule-based match' ?></th><?php foreach($selected as $r):$m=matchRacket($r,$ass['answers'],$ass['profile']);?><td><strong><?= (int)$m['score'] ?>%</strong><small class="battle-small"><?= e(implode(' ',array_slice($m['reasons'],0,2))) ?></small></td><?php endforeach;?></tr><?php endif; ?>
<tr><th><?= $zh?'下一步':'Next step' ?></th><?php foreach($selected as $r):?><td><a class="button button-outline" href="<?= $actor?'builder.php?racket='.(int)$r['id']:'login.php?return=questionnaire.php' ?>"><?= $zh?'自定义':'Build setup' ?> →</a></td><?php endforeach;?></tr>
</tbody></table></div><p class="reference-help"><?= $zh?'重量、拍头和中杆等部分旧数据仍属初步分类，Racket Battle 不把它们伪装为已核实的精确数值。':'Existing balance/shaft labels are provisional where official per-variant specifications have not been verified; comparisons are descriptive, not measured performance claims.' ?></p><p><?= e(marketNotice()) ?></p></div>
<?php else:?><div class="profile-card"><p><?= $zh?'选择两或三支球拍后按“开始比较”。':'Select two or three rackets and press Compare.' ?></p></div><?php endif; ?>
</div></section></main>
<?php require __DIR__.'/includes/footer.php'; ?>
