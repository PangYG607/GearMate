<?php
require __DIR__.'/includes/assessment_engine.php';
$user=currentActor();if(!$user){header('Location: login.php?return=analysis.php');exit;}
$assessment=loadAssessment($user['id']);if(!$assessment){header('Location: questionnaire.php');exit;}
$p=$assessment['profile'];$a=$assessment['answers'];$keys=['attack','speed','control','defence','footwork','consistency'];
$cx=180;$cy=180;$radius=125;
function polyPoints(array $values,int $cx,int $cy,int $max):string {
  $points=[];$i=0;foreach($values as $v){$a=deg2rad(-90+$i*60);$length=$max*($v===null?.05:max(0,min(100,(float)$v))/100);$points[]=round($cx+cos($a)*$length,1).','.round($cy+sin($a)*$length,1);$i++;}return implode(' ',$points);
}
$profileInfo=language()==='zh'?
[ 'style'=>['power'=>'你的偏好偏向进攻和主动压迫。','speed'=>'你的打法偏好快速反应与连续平抽挡。','control'=>'你更注重控球落点和主动控制回合节奏。','allround'=>'你的打法比较全面，愿意兼顾进攻、防守和稳定性。'],
  'note'=>'这是通过自评答案得出的初步倾向，并不等于经教练实际测量的竞技水平。',
  'feel'=>'可以优先了解'.gameLabel($p['balance']).'、'.gameLabel($p['flex']).'的球拍，现场试挥后再决定。']:
[ 'style'=>['power'=>'You favour initiating attacks and applying pressure.','speed'=>'You prefer fast reactions and quick flat exchanges.','control'=>'You value placement and steering the rhythm of rallies.','allround'=>'You favour versatility across attacking, defensive and steady exchanges.'],
  'note'=>'This is a self-report indication, not a skill certification measured by a coach.',
  'feel'=>'Consider a '.gameLabel($p['balance']).' racket with a '.gameLabel($p['flex']).', then test how it feels in person.'];
$page='Analysis';require __DIR__.'/includes/header.php';
?>
<main><section class="results-heading profile-hero"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / SELF-REPORTED PROFILE</p><h1><?= e(t('analysis_title')) ?></h1><p><?= e(t('hi')) ?>, <b><?= e($p['nickname']) ?></b>. <?= e(t('analysis_sub')) ?></p><div class="profile-pills"><span><?= e(t('self_level')) ?>: <?= e(gameLabel($p['self_level'])) ?></span><span><?= e(t('estimated_level')) ?>: <?= e(gameLabel($p['assessed_level'])) ?></span><span><?= e(t('style')) ?>: <?= e(gameLabel($p['style'])) ?></span></div></div></section>
<section class="section profile-section"><div class="container profile-layout">
 <article class="profile-card radar-card"><p class="eyebrow green-eyebrow"><?= e(t('six_axes')) ?></p><h2><?= e($p['nickname']) ?> / PLAYER RADAR</h2>
 <div class="radar-figure"><svg viewBox="0 0 360 365" role="img" aria-label="<?= e(t('six_axes')) ?>">
  <?php foreach([20,40,60,80,100] as $n):?><polygon points="<?= polyPoints(array_fill(0,6,$n),$cx,$cy,$radius) ?>" fill="none" stroke="#a0bee4" stroke-width="1" stroke-opacity=".5"/><?php endforeach;?>
  <?php foreach($keys as $i=>$key):$angle=deg2rad(-90+$i*60);$tx=$cx+cos($angle)*$radius;$ty=$cy+sin($angle)*$radius;$lx=$cx+cos($angle)*($radius+30);$ly=$cy+sin($angle)*($radius+30); ?><line x1="<?= $cx ?>" y1="<?= $cy ?>" x2="<?= round($tx,1) ?>" y2="<?= round($ty,1) ?>" stroke="#9fbee5" stroke-opacity=".55"/><text x="<?= round($lx,1) ?>" y="<?= round($ly+4,1) ?>" fill="#536885" font-size="12" font-weight="700" text-anchor="middle"><?= e(t($key)) ?></text><?php endforeach; ?>
  <polygon points="<?= polyPoints(array_values($p['radar']),$cx,$cy,$radius) ?>" fill="#36c8e8" fill-opacity=".25" stroke="#27a9ed" stroke-width="3" stroke-linejoin="round"/>
  <?php foreach($p['radar'] as $i=>$value):$pos=array_search($i,$keys,true);$angle=deg2rad(-90+$pos*60);$v=$value??5;?><circle cx="<?= round($cx+cos($angle)*$radius*$v/100,1) ?>" cy="<?= round($cy+sin($angle)*$radius*$v/100,1) ?>" r="4" fill="#5165fb"/><?php endforeach; ?>
 </svg></div><div class="radar-stats"><?php foreach($keys as $key):?><div><span><?= e(t($key)) ?></span><strong><?= $p['radar'][$key]===null?'—':$p['radar'][$key].'/100' ?></strong></div><?php endforeach;?></div>
 <p class="radar-note">ⓘ <?= e(t('radar_note')) ?></p></article>
 <div class="profile-column"><article class="profile-card"><p class="eyebrow green-eyebrow">PLAYER INSIGHT</p><h2><?= e(gameLabel($p['style'])) ?></h2><p><?= e($profileInfo['style'][$p['style']]) ?></p><p><?= e($profileInfo['note']) ?></p><div class="analysis-two"><div><h3><?= e(t('favourite')) ?></h3><?php foreach($p['strong'] as $key):?><span><?= e(t($key)) ?></span><?php endforeach;?></div><div><h3><?= e(t('opportunity')) ?></h3><?php foreach($p['improve'] as $key):?><span><?= e(t($key)) ?></span><?php endforeach;?></div></div></article>
 <article class="profile-card profile-feel"><p class="eyebrow green-eyebrow"><?= e(t('equipment')) ?></p><h3><?= e(gameLabel($p['balance'])) ?> + <?= e(gameLabel($p['flex'])) ?></h3><p><?= e($profileInfo['feel']) ?></p><p class="radar-note"><?= e(t('profile_hint')) ?></p><div class="profile-pills light"><span>RM <?= number_format($p['budget']) ?></span><span><?= e(gameLabel($p['style'])) ?></span></div></article>
 <a class="button button-main analysis-button" href="results.php"><?= e(t('see_matches')) ?> ↗</a><a class="text-link" href="questionnaire.php?mode=edit">← <?= e(t('edit_answers')) ?></a></div>
 <p><a class="text-link" href="preferences.php"><?= language()==='zh'?'只修改装备偏好':'Change equipment preferences only' ?> →</a></p></div></section></main>
<?php require __DIR__.'/includes/footer.php'; ?>
