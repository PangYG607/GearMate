<?php
require __DIR__.'/includes/gear_features.php';$actor=currentActor();
if(!$actor){header('Location: login.php?return=questionnaire.php');exit;}
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);$card=loadGearCard($actor['id'],$id?:null);
if(!$card){http_response_code(404);$page='Setup Card';require __DIR__.'/includes/header.php';echo '<main class="section"><div class="container profile-card"><h1>'.(language()==='zh'?'未找到配置单':'Setup not found').'</h1></div></main>';require __DIR__.'/includes/footer.php';exit;}
$d=$card['details'];$zh=language()==='zh';$page='Setup Card';require __DIR__.'/includes/header.php';
$rows=[
 [$zh?'球拍型号':'Racket model',$d['racket_model']],
 [$zh?'重量版本':'Weight variant',$d['racket_weight']],
 [$zh?'球线':'String',$d['string_name']],
 [$zh?'磅数':'Tension',$d['tension_lbs'].' lbs'],
 [$zh?'手胶':'Grip',$d['grip_name']]
];
?>
<main class="section gear-print-page"><div class="container"><div class="gear-card-sheet">
<div class="gear-card-head"><div><p class="eyebrow green-eyebrow">GEARMATE / PERSONAL SETUP</p><h1><?= $zh?'我的装备配置单':'MY GEAR SETUP CARD' ?></h1><p><?= $zh?'玩家：':'Player: ' ?><?= e($d['nickname']?:$actor['name']) ?> · <?= e($d['created_at']) ?></p></div><div class="gear-card-stamp"><?= $d['tension_status']==='verified_in_range'?($zh?'官方范围内':'VERIFIED RANGE'):($zh?'待店家确认':'CHECK WITH SHOP') ?></div></div>
<div class="gear-card-rows"><?php foreach($rows as [$name,$value]):?><div><span><?= e($name) ?></span><strong><?= e($value) ?></strong></div><?php endforeach;?></div>
<h2><?= $zh?'装备参考价格明细':'REFERENCE PRICE BREAKDOWN' ?></h2>
<div class="gear-card-rows"><?php foreach([[$d['racket_model'],$d['racket_price']],[$d['string_name'],$d['string_price']],[$d['grip_name'],$d['grip_price']]] as [$name,$price]):?><div><span><?= e($name) ?></span><strong><?= e(formatPrice((float)$price)) ?></strong></div><?php endforeach;?></div>
<div class="gear-card-total"><span><?= $zh?'参考总价（不含穿线人工）':'Reference total (no stringing labour)' ?></span><strong><?= e(formatPrice((float)$d['total'])) ?></strong></div>
<h2><?= $zh?'为什么这样搭配？':'WHY THIS SETUP?' ?></h2><p><?= $zh?'所选球拍来自已有的规则式匹配与玩家选择；球线和手胶是你自己选择的配置。':'This racket was selected from the existing rule-based recommendations or catalogue; the string and grip reflect your own choices.' ?></p>
<p><b><?= $zh?'球线特点：':'String profile: ' ?></b><?= e($d[$zh?'string_note_zh':'string_note_en']) ?></p><p><b><?= $zh?'手胶特点：':'Grip profile: ' ?></b><?= e($d[$zh?'grip_note_zh':'grip_note_en']) ?></p>
<div class="gear-verify-box"><b><?= $zh?'穿线注意事项':'STRINGING CHECK' ?></b><p><?php if($d['tension_status']==='verified_in_range' && $d['official_tension']):?><?= $zh?'选择的磅数处于已核实厂家范围内，但仍要确认球线及穿线服务。':'Selected tension is within a recorded manufacturer range; confirm compatibility with your stringer.' ?> <a target="_blank" rel="noopener" href="<?= e($d['official_tension']['official_url']) ?>"><?= $zh?'厂家来源':'Manufacturer source' ?></a><?php else:?><?= $zh?'此确切球拍重量版本的厂家穿线限制尚未确认。这是一份草稿，不代表 GearMate 已确认穿线安全，请让穿线师核实。':'Official tension limits for this exact racket/weight variant are not yet confirmed. This is a draft, not an approved stringing specification. Ask a qualified stringer.' ?><?php endif;?></p></div>
<p class="reference-help"><?= e($d[$zh?'price_disclaimer_zh':'price_disclaimer_en']) ?> <?= $zh?'穿线人工未计入。':'Stringing labour is not included.' ?></p>
<div class="gear-card-sources"><h2><?= $zh?'价格资料来源':'PRICE SOURCES' ?></h2>
<?php foreach([['name'=>$d['racket_price_source'],'url'=>$d['racket_price_url']],['name'=>$d['string_source'],'url'=>$d['string_url']],['name'=>$d['grip_source'],'url'=>$d['grip_url']]] as $src):?><a href="<?= e($src['url']) ?>" target="_blank" rel="noopener"><?= e($src['name']) ?> ↗</a><?php endforeach;?><span><?= $zh?'球拍价格查阅：':'Racket price checked: ' ?><?= e($d['price_checked']) ?></span></div>
</div><div class="gear-card-actions"><button onclick="window.print()" class="button button-main"><?= $zh?'打印／另存为 PDF':'Print / Save as PDF' ?> ↗</button><a class="button button-outline" href="<?= $actor['id']?'account.php':'guest.php' ?>"><?= $zh?'返回':'Back' ?></a></div></div></main>
<?php require __DIR__.'/includes/footer.php'; ?>
