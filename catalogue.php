<?php
require __DIR__.'/includes/language.php';
$all=getCatalog();
$brand=(string)($_GET['brand']??'all');$cat=(string)($_GET['type']??'all');$tier=(string)($_GET['tier']??'all');$q=trim((string)($_GET['q']??''));
if(!in_array($cat,['all','attack','speed','control'],true))$cat='all';
$brands=array_values(array_unique(array_column($all,'brand')));sort($brands);if($brand!=='all'&&!in_array($brand,$brands,true))$brand='all';
if(!in_array($tier,['all','entry','mid','premium'],true))$tier='all';
function catalogQuery(array $changes): string { global $brand,$cat,$tier,$q;
 $params=array_merge(['brand'=>$brand,'type'=>$cat,'tier'=>$tier,'q'=>$q],$changes);
 return 'catalogue.php?'.http_build_query($params);
}
$filtered=array_values(array_filter($all,static function($r)use($brand,$cat,$tier,$q){
 if($brand!=='all' && ($r['brand']??'')!==$brand)return false;
 if($cat!=='all'&&($r['category']??'')!==$cat)return false;
 if($tier!=='all'&&($r['price_tier']??'')!==$tier)return false;
 if($q!==''&&!str_contains((function_exists('mb_strtolower')?mb_strtolower($r['model']):strtolower($r['model'])),(function_exists('mb_strtolower')?mb_strtolower($q):strtolower($q)))&&!str_contains((function_exists('mb_strtolower')?mb_strtolower(modelName($r)):strtolower(modelName($r))),(function_exists('mb_strtolower')?mb_strtolower($q):strtolower($q))))return false;
 return true;
}));
$counts=[];foreach($all as $r){if($brand!=='all' && $r['brand']!==$brand)continue;$key=($r['category']??'unknown').':'.($r['price_tier']??'unknown');$counts[$key]=($counts[$key]??0)+1;}
$page='Catalogue';require __DIR__.'/includes/header.php';$zh=language()==='zh';
?>
<main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE · SOURCE-LINKED MARKET CATALOGUE</p>
<h1><?= e(t('catalogue_title')) ?></h1><p><?= $zh?'按「进攻 / 速度 / 控球」与「入门 / 中阶 / 高端价位」筛选真实 Yonex 型号及版本。':'Explore Yonex racket models and variants by play style and price tier.' ?></p>
<div class="catalog-filters"><p><?= $zh?'品牌':'BRAND' ?></p><div class="tab-row"><a class="<?= $brand==='all'?'selected':'' ?>" href="<?= e(catalogQuery(['brand'=>'all'])) ?>"><?= $zh?'全部品牌':'All brands' ?></a><?php foreach($brands as $b):?><a class="<?= $brand===$b?'selected':'' ?>" href="<?= e(catalogQuery(['brand'=>$b])) ?>"><?= e($b) ?></a><?php endforeach;?></div><p><?= $zh?'球拍类型':'PLAY STYLE' ?></p><div class="tab-row">
<?php foreach(['all'=>($zh?'全部':'All'),'attack'=>($zh?'进攻 · 天斧':'Attack · Astrox'),'speed'=>($zh?'速度 · 疾光':'Speed · Nanoflare'),'control'=>($zh?'控球 · 弓箭':'Control · Arcsaber')] as $k=>$v): ?><a class="<?= $cat===$k?'selected':'' ?>" href="<?= e(catalogQuery(['type'=>$k])) ?>"><?= e($v) ?></a><?php endforeach; ?></div>
<p><?= $zh?'价格档次（不是玩家技术等级）':'PRICE TIER (NOT SKILL LEVEL)' ?></p><div class="tab-row">
<?php foreach(['all'=>($zh?'全部价位':'All prices'),'entry'=>($zh?'入门 · 约 RM350 以下':'Entry · under ~RM350'),'mid'=>($zh?'中阶 · 约 RM350–699':'Mid · ~RM350–699'),'premium'=>($zh?'高端 · RM700 起':'Premium · RM700+')] as $k=>$v): ?><a class="<?= $tier===$k?'selected':'' ?>" href="<?= e(catalogQuery(['tier'=>$k])) ?>"><?= e($v) ?></a><?php endforeach; ?></div>
<form method="get" class="catalog-search"><input type="hidden" name="brand" value="<?= e($brand) ?>"><input type="hidden" name="type" value="<?= e($cat) ?>"><input type="hidden" name="tier" value="<?= e($tier) ?>"><input name="q" value="<?= e($q) ?>" placeholder="<?= $zh?'搜索型号，例如 ASTROX 88D':'Search model, e.g. NANOFLARE 700' ?>" maxlength="100"><button class="button button-main" type="submit"><?= $zh?'搜索':'Search' ?></button></form></div>
</div></section>
<section class="section result-body"><div class="container">
<div class="catalog-market-notice"><b><?= $zh?'价格提醒':'Price disclaimer' ?></b><p><?= e(marketNotice()) ?></p></div>
<div class="catalog-coverage"><div class="catalog-matrix-head"><b><?= $zh?'当前产品库覆盖情况':'Catalogue coverage' ?></b><span><?= count($all) ?> <?= $zh?'个型号／版本条目':'model / variant entries' ?></span></div>
<div class="coverage-grid"><?php foreach(['attack'=>($zh?'进攻':'Attack'),'speed'=>($zh?'速度':'Speed'),'control'=>($zh?'控球':'Control')] as $kind=>$label):?><div class="coverage-row"><strong><?= e($label) ?></strong><?php foreach(['entry'=>($zh?'入门':'Entry'),'mid'=>($zh?'中阶':'Mid'),'premium'=>($zh?'高端':'Premium')] as $range=>$tiername):?><span><?= e($tiername) ?> · <?= (int)($counts[$kind.':'.$range]??0) ?></span><?php endforeach; ?></div><?php endforeach; ?></div>
<p class="reference-help"><?= $zh?'其中部分条目是同型号的重量或配色版本，不能当作不同型号；价格是商家公开标价，库存未逐一确认。球拍技术分级及中杆参数为初步分类，仍需逐型号核对官方规格。':'Some entries are weight or colour variants of the same model. Prices are public listings; live stock is unverified. Preliminary play-level / shaft tags still require per-variant manufacturer confirmation.' ?></p></div>
<div class="result-header"><h2><?= count($filtered) ?> <?= $zh?'个结果':'RESULTS' ?></h2><span><?= ($GLOBALS['data_mode']??'')==='mysql'?'MYSQL + PRICE SOURCES':'BUILT-IN PRICE REFERENCES' ?></span></div>
<?php if(!$filtered):?><div class="no-results"><h2><?= $zh?'当前筛选没有结果':'No matching rackets' ?></h2><p><?= $zh?'试试其他类型或价位。':'Try another play style or price tier.' ?></p><a href="catalogue.php" class="button button-main"><?= $zh?'清除筛选':'Clear filters' ?></a></div><?php else:?><div class="card-grid"><?php foreach($filtered as $r):$rank=null;include __DIR__.'/includes/racket_card.php';endforeach; ?></div><?php endif; ?>
<div class="disclaimer"><?= e(marketNotice()) ?></div></div></section></main>
<?php require __DIR__.'/includes/footer.php'; ?>
