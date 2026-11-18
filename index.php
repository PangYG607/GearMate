<?php
require_once __DIR__.'/includes/language.php';
require_once __DIR__.'/includes/racket_photo.php';
$page='Home';
$all=getCatalog();$featured=[];
foreach(['ASTROX 77 PLAY','NANOFLARE 700 PLAY','ARCSABER 7 PLAY'] as $name)foreach($all as $item)if($item['model']===$name)$featured[]=$item;
require __DIR__.'/includes/header.php';
?>
<main>
<section class="hero"><div class="container hero-grid">
 <div class="hero-text"><p class="eyebrow"><span class="live-dot"></span> <?= e(t('home_eyebrow')) ?></p>
 <h1><?= e(t('home_title')) ?><br><em><?= e(t('home_title_2')) ?></em></h1>
 <p class="hero-sub"><?= e(t('home_description')) ?></p>
 <p class="hero-caption"><?= e(t('home_note')) ?></p><a class="button button-main mobile-hero-cta" href="<?= currentActor()?'questionnaire.php':'login.php?return=questionnaire.php' ?>"><?= e(t('home_cta')) ?> ↗</a>
 <a class="button button-ghost" href="catalogue.php"><?= e(t('browse')) ?> →</a>
 </div>
 <div class="hero-visual"><div class="orbit orbit-a"></div><div class="orbit orbit-b"></div><div class="hero-racket" aria-hidden="true"><svg viewBox="0 0 300 520"><g fill="none" stroke="#45EDFF" stroke-linecap="round"><ellipse cx="150" cy="158" rx="103" ry="144" stroke-width="12"/><g opacity=".55" stroke-width="3"><path d="M64 82h172 M51 118h198 M47 153h206 M51 189h198 M68 229h164 M91 265h118 M87 45v231 M122 21v274 M158 15v286 M192 32v257 M222 68v210"/></g><path d="M150 306v154" stroke-width="17"/><path d="M150 423v85" stroke-width="29"/></g></svg></div>
 <a class="hero-main-cta" href="<?= currentActor()?'questionnaire.php':'login.php?return=questionnaire.php' ?>"><span>✦ <?= e(t('home_cta')) ?></span><b>↗</b></a>
 </div>
</div></section>
<section class="intro-band"><div class="container band-grid"><span>01 / <?= e(t('feature_1')) ?></span><span>02 / <?= e(t('feature_2')) ?></span><span>03 / <?= e(t('feature_3')) ?></span></div></section>
<section class="section showcase-picks"><div class="container"><p class="eyebrow green-eyebrow"><?= e(t('popular')) ?></p><h2><?= e(t('popular_title')) ?></h2><p class="showcase-desc"><?= e(t('popular_sub')) ?></p>
<div class="featured-grid">
<?php foreach($featured as $r):$photo=racketPhoto((string)$r['model']); ?>
<article class="feature-pick"><div class="feature-pick-art"><?php if($photo):?><img src="<?= e($photo['url']) ?>" loading="lazy" referrerpolicy="no-referrer" onerror="this.style.display='none';this.nextElementSibling.style.display='block'" alt="<?= e($r['model']) ?>"><span class="image-alt">🏸</span><?php else: ?><span class="image-alt" style="display:block">🏸</span><?php endif; ?></div><div class="feature-pick-body"><small><?= e($r['brand']) ?> · <?= e($r['weight']) ?></small><h3><?= e(modelName($r)) ?></h3><p><?= e(gameLabel($r['balance'])) ?> · <?= e(gameLabel($r['flex'])) ?></p><p class="featured-market-price"><?= e(formatPrice(referencePrice($r))) ?> <small><?= language()==="zh"?"商家参考价":"retailer reference" ?></small></p><a href="<?= e($r['source_url']) ?>" rel="noopener noreferrer" target="_blank"><?= e(t('explore')) ?> ↗</a></div></article>
<?php endforeach; ?>
<article class="feature-pick spotlight"><div class="feature-pick-art lining-art"><img src="https://li-ningsports.co.uk/cdn/shop/files/AYPU081-4.png?v=1764785364&amp;width=1520" alt="Li-Ning Axforce Cannon Pro AYPU081-4" referrerpolicy="no-referrer" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='block'"><span class="image-alt">🏸</span><span class="spotlight-tag">OFFICIAL 2025 CATALOGUE</span></div><div class="feature-pick-body"><small>LI-NING · 4U / 5U</small><h3><?= language()==='zh'?'李宁 雷霆小钢炮PRO':'LI-NING AXFORCE LITTLE CANNON PRO' ?></h3><p><?= language()==='zh'?'李宁官方中文产品名；尚未加入自动推荐数据库':'Official Chinese model name; not yet in the scoring catalogue' ?></p><a href="https://li-ningsports.co.uk/products/li-ning-axforce-cannon-pro-black-and-blue-racket-aypu081-4" rel="noopener noreferrer" target="_blank"><?= e(t('explore')) ?> ↗</a></div></article>
</div><div class="catalog-market-notice" style="margin-top:22px"><b><?= language()==="zh"?"参考价提示":"Price notice" ?></b><p><?= e(marketNotice()) ?></p></div></div></section>
<section class="section how-section"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / PLAYER ANALYSIS</p><h2><?= e(t('feature_1')) ?>.<br><?= e(t('feature_2')) ?>.</h2><div class="feature-grid">
<?php foreach([1,2,3] as $n): ?><div><span class="feature-num">0<?= $n ?></span><h3><?= e(t('feature_'.$n)) ?></h3><p><?= e(t('feature_'.$n.'_detail')) ?></p></div><?php endforeach; ?>
</div><a style="margin-top:26px" class="button button-main" href="<?= currentActor()?'questionnaire.php':'login.php?return=questionnaire.php' ?>"><?= e(t('start')) ?> ↗</a></div></section>
</main><?php require __DIR__.'/includes/footer.php'; ?>
