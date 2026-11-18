<?php
$page=$page??'Home';
require_once __DIR__.'/language.php';
$user=currentActor();
$current=basename(parse_url($_SERVER['REQUEST_URI']??'index.php',PHP_URL_PATH) ?: 'index.php');
$allowedPages=['index.php','catalogue.php','login.php','register.php','account.php','questionnaire.php','analysis.php','results.php','builder.php','battle.php','progress.php','preferences.php','academy.php','setup_card.php','admin_rackets.php','guest.php'];
if(!in_array($current,$allowedPages,true))$current='index.php';
$currentQuery=(string)(parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_QUERY)??'');
$return=$current.($currentQuery!=='' && !str_contains($currentQuery,'lang=')?'?'.$currentQuery:'');
if(strlen($return)>250)$return=$current;
?><!doctype html><html lang="<?= language()==='zh'?'zh-CN':'en' ?>"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Badminton GearMate — a bilingual, explainable, rule-based racket recommendation and player self-assessment site.">
<meta name="theme-color" content="#090F26"><title><?= e(t(strtolower($page))!==strtolower($page)?t(strtolower($page)):$page) ?> | GearMate</title>
<link rel="stylesheet" href="assets/style.css?v=4.0">
</head><body>
<header class="site-header"><div class="container nav-inner">
 <a class="brand" href="index.php"><span class="brand-icon">✳</span><span>GEAR<span class="accent">MATE</span><small>BADMINTON · MATCH YOUR PLAY</small></span></a>
 <nav class="nav" aria-label="Site navigation">
   <a class="<?= $page==='Home'?'active':'' ?>" href="index.php"><?= e(t('home')) ?></a>
   <a class="<?= $page==='Catalogue'?'active':'' ?>" href="catalogue.php"><?= e(t('catalogue')) ?></a>
   <a href="academy.php"><?= language()==='zh'?'装备知识':'Gear Academy' ?></a>
   <a href="battle.php"><?= language()==='zh'?'球拍对比':'Racket Battle' ?></a>
   <a class="nav-action" href="<?= $user?'questionnaire.php':'login.php?return=questionnaire.php' ?>"><?= e(t('find')) ?> ↗</a>
   <?php if($user): ?><a class="nav-account" href="<?= !empty($user['guest'])?'guest.php':'account.php' ?>">◉ <?= e($user['name']) ?></a><?php else: ?><a class="nav-account" href="login.php?return=questionnaire.php"><?= e(t('login')) ?> ↗</a><?php endif; ?>
   <div class="language-switch" aria-label="Choose website language"><a href="language.php?lang=zh&amp;return=<?= e(rawurlencode($return)) ?>" class="<?= language()==='zh'?'is-active':'' ?>" lang="zh-CN">中文</a><span aria-hidden="true">/</span><a href="language.php?lang=en&amp;return=<?= e(rawurlencode($return)) ?>" class="<?= language()==='en'?'is-active':'' ?>" lang="en">EN</a></div>
 </nav>
</div></header>
