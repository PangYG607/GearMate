<?php
require __DIR__.'/includes/assessment_engine.php';
$user=currentActor();if(!$user){header('Location: login.php?return=results.php');exit;}
$ass=loadAssessment($user['id']);if(!$ass){header('Location: questionnaire.php');exit;}
$a=$ass['answers'];$p=$ass['profile'];$all=getCatalog();$picked=selectedRackets($all,$a,$p);$results=$picked['rackets'];
$page='Results';require __DIR__.'/includes/header.php';
?>
<main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow"><?= e(t('pro_label')) ?></p><h1><?= e(t('results_title')) ?></h1><p><?= e(t('hi')) ?>, <?= e($p['nickname']) ?>. <?= e(t('results_sub')) ?></p><div class="profile-pills"><span>RM <?= number_format($p['budget']) ?></span><span><?= e(gameLabel($p['self_level'])) ?></span><span><?= e(gameLabel($p['style'])) ?></span></div><a class="text-link" href="analysis.php">← <?= e(t('assessment')) ?></a></div></section>
<section class="section result-body"><div class="container">
 <div class="<?= ($GLOBALS['data_mode']??'sample')==='mysql'?'mode-success':'mode-warning' ?>"><?= e(($GLOBALS['data_mode']??'sample')==='mysql'?t('data_mysql'):(language()==='zh'?'当前使用带来源的内建参考目录。请执行 v031_catalog_migration.sql 同步 MySQL。':'Using built-in, source-linked price references; import v031_catalog_migration.sql to update MySQL.')) ?></div>
 <?php if($picked['fallback']):?><div class="fallback-notice"><strong>⚠ <?= e(t('fallback_title')) ?></strong><p><?= e(t('fallback_text')) ?></p></div><?php endif; ?>
 <div class="result-header"><h2><?= e($picked['fallback']?(language()==='zh'?'3 款跨价位替代选择':'3 ALTERNATIVE OPTIONS'):t('within_budget')) ?></h2><span>TOP <?= count($results) ?> · RULE-BASED</span></div>
 <div class="card-grid recommendation-grid"><?php foreach($results as $i=>$r):$rank=$i+1;include __DIR__.'/includes/racket_card.php';endforeach;?></div>
 <div class="disclaimer"><b><?= e(t('score_explain')) ?></b> <?= e(marketNotice()) ?><?= ($p['include_accessories']?(language()==='zh'?' 整套预算将按当前最便宜球线与手胶的参考价预留费用，最终 DIY 仍可能不同。':' When full setup budget is selected, the cheapest listed string and grip reference prices are reserved; your DIY choices may cost more.'): '') ?></div>
 <div class="result-more"><a href="questionnaire.php?mode=edit" class="button button-outline">← <?= e(t('retake')) ?></a><a href="battle.php" class="button button-outline"><?= language()==="zh"?"球拍比较":"Compare Rackets" ?></a><a href="analysis.php" class="button button-outline"><?= e(t('assessment')) ?></a></div>
</div></section></main><?php require __DIR__.'/includes/footer.php'; ?>
