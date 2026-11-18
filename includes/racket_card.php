<?php
require_once __DIR__.'/racket_photo.php';
$rank=$rank??null;$brandClass=strtolower((string)($r['series']??''));$photo=racketPhoto((string)$r['model']);
?>
<article class="racket-card">
 <div class="racket-art <?= e($brandClass) ?>"><span class="racket-mini-brand"><?= e($r['brand']) ?> · <?= e($r['series']) ?></span>
 <?php if($photo):?><img class="racket-photo" src="<?= e($photo['url']) ?>" alt="<?= e($r['model']) ?>" loading="lazy" referrerpolicy="no-referrer" onerror="this.style.display='none';this.nextElementSibling.style.display='block'"><?php endif; ?>
 <svg class="racket-svg fallback-racket" viewBox="0 0 240 210" aria-label="Generic racket illustration" style="<?= $photo?'display:none':'' ?>"><g fill="none" stroke="currentColor" stroke-linecap="round"><ellipse cx="120" cy="74" rx="43" ry="62" stroke-width="7"/><g opacity=".55" stroke-width="1.5"><path d="M86 37h68 M81 52h78 M77 67h86 M78 82h84 M83 97h74 M91 112h58 M95 17v115 M109 14v122 M123 13v123 M137 17v115 M150 32v82"/></g><path d="M120 137v51" stroke-width="8"/><path d="M120 176v25" stroke-width="14"/></g></svg>
 <?php if($rank!==null):?><span class="rank-badge">#<?= (int)$rank ?> MATCH</span><?php endif; ?>
 <?php if($photo):?><a class="photo-credit" href="<?= e($photo['credit']) ?>" target="_blank" rel="noopener noreferrer">PHOTO SOURCE ↗</a><?php endif;?></div>
 <div class="racket-info"><p class="eyebrow dark-eyebrow"><?= e($r['series']) ?> · <?= e((($r['price_tier']??'')==='entry')?(language()==='zh'?'入门价位':'Entry tier'):((($r['price_tier']??'')==='mid')?(language()==='zh'?'中阶价位':'Mid tier'):(language()==='zh'?'高端价位':'Premium tier'))) ?></p><h3><?= e(modelName($r)) ?></h3>
 <div class="chip-row"><span class="chip"><?= e(gameLabel($r['balance'])) ?></span><span class="chip"><?= e(gameLabel($r['flex'])) ?></span><span class="chip"><?= e($r['weight']) ?></span></div>
 <?php if(isset($r['score'])):?><div class="match-number"><strong><?= (int)$r['score'] ?><small>%</small></strong><span><?= e(t('match')) ?></span></div><?php endif; ?>
 <div class="product-footer"><div><span class="caption-small"><?= e(t('price')) ?></span><strong><?= e(formatPrice(referencePrice($r))) ?></strong></div><a href="<?= e($r['source_url']) ?>" target="_blank" rel="noopener noreferrer"><?= e(t('specs')) ?> ↗</a></div>
 <div class="racket-extra-actions"><a href="battle.php?id[]=<?= (int)$r['id'] ?>"><?= language()==='zh'?'加入比较':'Compare' ?> ↗</a>
 <?php if(currentUser()):?><form method="post" action="save_racket.php"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="return" value="<?= isset($r['score'])?'results.php':'catalogue.php' ?>"><button type="submit"><?= language()==='zh'?'☆ 收藏':'☆ Save Racket' ?></button></form><?php endif; ?></div>
 <div class="market-meta"><span><?= e($r['price_source']??'') ?> · <?= e($r['price_checked']??'') ?></span>
 <?php if(!empty($r['price_url'])):?><a href="<?= e($r['price_url']) ?>" target="_blank" rel="noopener noreferrer"><?= language()==='zh'?'查看商家标价':'Price source' ?> ↗</a><?php endif; ?></div>
 <?php if(!empty($r['variant_note'])):?><p class="variant-note"><?= language()==='zh'?('版本：'.$r['variant_note']):('Variant: '.$r['variant_note']) ?></p><?php endif; ?>
 <?php if(isset($r['score'])):?><div class="reasons"><b><?= e(t('why')) ?></b><?php foreach($r['reasons'] as $reason):?><p>✦ <?= e($reason) ?></p><?php endforeach;?></div><div class="tradeoffs"><b><?= e(t('weakness')) ?></b><?php foreach($r['drawbacks'] as $weakness):?><p>• <?= e($weakness) ?></p><?php endforeach;?></div><a class="button button-main choose-racket" href="builder.php?racket=<?= (int)$r['id'] ?>"><?= e(t('choose')) ?> ↗</a><?php endif; ?>
 </div></article>
