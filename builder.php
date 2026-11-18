<?php
require __DIR__.'/includes/gear_features.php';
$user=currentActor();if(!$user){header('Location: login.php?return=questionnaire.php');exit;}
$zh=language()==='zh';$id=filter_input(INPUT_GET,'racket',FILTER_VALIDATE_INT) ?: (int)($_POST['racket']??0);
$r=racketById($id);if(!$r){header('Location: results.php');exit;}
$access=getAccessoryCatalog();$strings=[];$grips=[];
foreach($access as $a){if($a['type']==='string')$strings[$a['code']]=$a;elseif($a['type']==='grip')$grips[$a['code']]=$a;}
if(!$strings||!$grips){http_response_code(503);exit('Accessory catalogue unavailable');}
$selectedString=(string)($_POST['string']??'BG65');$selectedGrip=(string)($_POST['grip']??'AC102');
if(!isset($strings[$selectedString]))$selectedString=array_key_first($strings);
if(!isset($grips[$selectedGrip]))$selectedGrip=array_key_first($grips);
$tension=filter_var($_POST['tension']??22,FILTER_VALIDATE_INT);if($tension===false)$tension=22;
$total=referencePrice($r)+(float)$strings[$selectedString]['price']+(float)$grips[$selectedGrip]['price'];
$limit=verifiedTension((int)$r['id']);$error='';$success=false;$cardId=null;
$ass=loadAssessment($user['id']);$profile=$ass['profile']??[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf((string)($_POST['csrf']??'')))$error=$zh?'页面已过期，请刷新。':'Session expired; please refresh.';
    elseif($tension<16||$tension>32)$error=$zh?'请选择 16–32 lbs；这是界面输入范围，不代表厂家许可范围。':'Choose 16–32 lbs; this input range is NOT a manufacturer-approved range.';
    elseif($limit && ($tension<(int)$limit['min_lbs']||$tension>(int)$limit['max_lbs']))$error=$zh?'磅数超出这支球拍已核实的厂家范围，不能保存此配置。':'Tension exceeds the verified manufacturer range for this variant. Setup blocked.';
    else{
      $detail=setupDetails($r,$strings[$selectedString],$grips[$selectedGrip],$tension,$profile);
      $cardId=saveGearCard($user['id'],$detail);
      if($cardId===null)$error=$zh?'无法保存到 MySQL。请导入 v04_features_migration.sql。':'Could not save to MySQL. Import v04_features_migration.sql.';
      else $success=true;
    }
}
require_once __DIR__.'/includes/racket_photo.php';
$photo=racketPhoto((string)$r['model']);
$page='Builder';require __DIR__.'/includes/header.php';
?>
<main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / DIY</p><h1><?= e(t('builder')) ?></h1><p><?= e(t('builder_desc')) ?></p></div></section>
<section class="section builder-section"><div class="container builder-grid">
<aside class="profile-card builder-racket"><p class="eyebrow green-eyebrow"><?= e(t('selected_racket')) ?></p>
<?php if($photo):?><img src="<?= e($photo['url']) ?>" referrerpolicy="no-referrer" alt="<?= e($r['model']) ?>"><?php endif; ?>
<h2><?= e(modelName($r)) ?></h2><p><?= e(gameLabel($r['balance'])) ?> · <?= e(gameLabel($r['flex'])) ?> · <?= e($r['weight']) ?></p>
<strong><?= e(formatPrice(referencePrice($r))) ?></strong>
<div class="market-meta"><span><?= e($r['price_source']??'') ?> · <?= e($r['price_checked']??'') ?></span><a href="<?= e($r['price_url']??$r['source_url']) ?>" target="_blank" rel="noopener noreferrer"><?= $zh?'商家价格来源':'Retail listing' ?> ↗</a></div><p class="reference-help"><?= e(marketNotice()) ?></p></aside>
<div class="profile-card builder-form"><h2><?= e(t('builder')) ?></h2>
<?php if($success):?><div class="mode-success">✓ <?= $zh?'配置已生成。':'Setup card created.' ?> <a href="setup_card.php<?= $cardId?'?id='.$cardId:'' ?>"><?= $zh?'查看及导出配置单':'View / export setup card' ?> →</a></div><?php endif;?>
<?php if($error):?><div class="builder-error" role="alert"><?= e($error) ?></div><?php endif;?>
<form method="post" action="builder.php?racket=<?= (int)$id ?>">
<input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="racket" value="<?= (int)$id ?>">
<label><?= e(t('strings')) ?><select id="buildString" name="string">
<?php foreach($strings as $key=>$item):?><option value="<?= e($key) ?>" data-price="<?= e($item['price']) ?>" data-note="<?= e($item[$zh?'note_zh':'note_en']) ?>" data-source="<?= e($item['source']) ?>" data-source-url="<?= e($item['url']) ?>" data-checked="<?= e($item['checked_at']??'') ?>" <?= $selectedString===$key?'selected':'' ?>><?= e($item['name']) ?> · +<?= e(formatPrice((float)$item['price'])) ?></option><?php endforeach;?></select></label>
<p class="accessory-desc" id="stringNote"><?= e($strings[$selectedString][$zh?'note_zh':'note_en']) ?></p>
<p class="accessory-price-source" id="stringSource"><?= $zh?'球线参考价来源：':'String price reference: ' ?><a id="stringSourceLink" href="<?= e($strings[$selectedString]['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($strings[$selectedString]['source']) ?></a> · <span id="stringChecked"><?= e($strings[$selectedString]['checked_at']??'') ?></span></p>
<label><?= e(t('tension')) ?><input type="number" name="tension" min="16" max="32" step="1" value="<?= (int)$tension ?>" required></label>
<div class="tension-guide"><strong><?= $zh?'玩家水平建议起点（非厂家通用限制）':'Skill-based starting guidance (not official limits)' ?></strong>
<div><?= $zh?'新手 18–22 · 初中级 22–24 · 中级 23–26 · 高级 25–28 · 竞技 28+ lbs':'Beginner 18–22 · Developing 22–24 · Intermediate 23–26 · Advanced 25–28 · Competitive 28+ lbs' ?></div>
<small><?= $zh?'并非磅数越高越强。高磅通常更不宽容，未适应时可能导致击球费力或手臂不适。请与专业穿线师确认具体球拍、重量版本和球线的限制。':'Higher tension does not guarantee more power. It may be less forgiving and uncomfortable when not suited to your technique. Confirm the exact racket variant and string with a qualified stringer.' ?></small></div>
<?php if($limit):?><div class="mode-success">✓ <?= $zh?'本型号官方穿线范围已录入：':'Verified manufacturer range: ' ?><?= (int)$limit['min_lbs'] ?>–<?= (int)$limit['max_lbs'] ?> lbs · <a href="<?= e($limit['official_url']) ?>" target="_blank" rel="noopener"><?= $zh?'来源':'Source' ?></a></div>
<?php else:?><div class="builder-warning">⚠ <?= $zh?'本重量版本的厂家穿线范围尚未完成核实。可保存“待穿线师确认”的草稿，但系统不会声称该磅数安全。':'Manufacturer tension limit for this exact variant has not been verified. You may save a DRAFT for a stringer to confirm; GearMate cannot certify the tension as safe.' ?></div><?php endif; ?>
<label><?= e(t('grip')) ?><select id="buildGrip" name="grip">
<?php foreach($grips as $key=>$item):?><option value="<?= e($key) ?>" data-price="<?= e($item['price']) ?>" data-note="<?= e($item[$zh?'note_zh':'note_en']) ?>" data-source="<?= e($item['source']) ?>" data-source-url="<?= e($item['url']) ?>" data-checked="<?= e($item['checked_at']??'') ?>" <?= $selectedGrip===$key?'selected':'' ?>><?= e($item['name']) ?> · +<?= e(formatPrice((float)$item['price'])) ?></option><?php endforeach;?></select></label>
<p class="accessory-desc" id="gripNote"><?= e($grips[$selectedGrip][$zh?'note_zh':'note_en']) ?></p>
<p class="accessory-price-source" id="gripSource"><?= $zh?'手胶参考价来源：':'Grip price reference: ' ?><a id="gripSourceLink" href="<?= e($grips[$selectedGrip]['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($grips[$selectedGrip]['source']) ?></a> · <span id="gripChecked"><?= e($grips[$selectedGrip]['checked_at']??'') ?></span></p>
<div class="builder-total"><span><?= e(t('estimate')) ?></span><strong id="builderTotal" data-base="<?= e(referencePrice($r)) ?>"><?= e(formatPrice($total)) ?></strong></div>
<p class="reference-help"><?= $zh?'总价是球拍＋一组球线＋一条手胶的参考价，不含未核实的穿线人工、运费或折扣。颜色选择已移除，因为无法真实预览。':'Reference total: racket + one string pack + one grip, excluding unverified stringing labour, shipping and promotions. Colour selection is omitted without a faithful visual preview.' ?></p>
<button class="button button-main" type="submit"><?= $zh?'生成并保存装备配置单':'Generate & save setup card' ?> ↗</button>
</form><p class="reference-help"><a href="academy.php"><?= $zh?'看不懂穿线磅数或球线差异？查看装备知识库':'Confused by strings or tension? Read the Gear Academy' ?> →</a></p>
</div></div></section></main><?php require __DIR__.'/includes/footer.php'; ?>
