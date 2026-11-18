<?php
require __DIR__.'/includes/gear_features.php';$actor=currentUser();if(!$actor){header('Location: login.php?return=account.php');exit;}
$zh=language()==='zh';$rows=[];$error='';
try {$db=getDbConnection();$s=$db->prepare('SELECT id,profile_json,created_at FROM player_assessments WHERE user_id=? ORDER BY id DESC LIMIT 30');$s->execute([$actor['id']]);$rows=$s->fetchAll();}catch(Throwable $e){$error=$zh?'无法加载评估历史。':'Cannot load assessment history.';}
$selected=[];
if(count($rows)>1){
 $ids=[(int)($_GET['before']??$rows[1]['id']),(int)($_GET['after']??$rows[0]['id'])];
 foreach($ids as $id){foreach($rows as $row){if((int)$row['id']===$id){$profile=json_decode($row['profile_json'],true);if(is_array($profile))$selected[]=['row'=>$row,'profile'=>$profile];break;}}}
 if(count($selected)!==2)$selected=[];
}
$axes=['attack','speed','control','defence','footwork','consistency'];
function radarOverlayPoints(array $profile,array $axes):string{
 $points=[];foreach($axes as $i=>$axis){$v=$profile['radar'][$axis]??null;$v=is_numeric($v)?min(100,max(0,$v)):0;$a=deg2rad(-90+60*$i);$points[]=round(170+$v*cos($a),1).','.round(153+$v*sin($a),1);}return implode(' ',$points);
}
$page='Progress';require __DIR__.'/includes/header.php';
?><main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / PLAYER PROGRESS</p><h1><?= $zh?'玩家成长记录':'PLAYER PROGRESS' ?></h1><p><?= $zh?'只比较你主动选择“新增技术评估”时保存的历史。修改预算或当前问卷不会多加一次成长记录。':'Compare your explicitly saved skill assessments. Preference edits do not count as progress.' ?></p></div></section>
<section class="section"><div class="container"><div class="profile-card"><h2><?= $zh?'技术评估历史':'Saved skill assessments' ?></h2>
<?php if($error):?><div class="form-alert"><?= e($error) ?></div><?php endif; ?>
<?php if(!$rows):?><p><?= $zh?'还没有保存记录。':'No saved assessments yet.' ?></p><?php endif; ?>
<div class="progress-actions"><a class="button button-main" href="questionnaire.php?mode=new"><?= $zh?'新增一次技术评估':'New Skill Assessment' ?> →</a><a class="button button-outline" href="questionnaire.php?mode=edit"><?= $zh?'修改当前问卷':'Edit Current Assessment' ?></a><a class="button button-outline" href="preferences.php"><?= $zh?'只修改装备偏好':'Update Preferences Only' ?></a></div>
<?php if(count($rows)>1):?><form method="get" class="progress-pickers"><label><?= $zh?'较早记录':'Earlier record' ?><select name="before"><?php foreach($rows as $r):?><option value="<?= (int)$r['id'] ?>" <?= count($selected)>0 && (int)$r['id']===(int)$selected[0]['row']['id']?'selected':'' ?>><?= e($r['created_at']) ?> #<?= (int)$r['id'] ?></option><?php endforeach;?></select></label><label><?= $zh?'较新记录':'Later record' ?><select name="after"><?php foreach($rows as $r):?><option value="<?= (int)$r['id'] ?>" <?= count($selected)>1 && (int)$r['id']===(int)$selected[1]['row']['id']?'selected':'' ?>><?= e($r['created_at']) ?> #<?= (int)$r['id'] ?></option><?php endforeach;?></select></label><button type="submit" class="button button-outline"><?= $zh?'对比':'Compare' ?></button></form><?php endif; ?>
<?php if(count($selected)===2):?><div class="progress-visual"><svg viewBox="0 0 340 320" role="img" aria-label="<?= $zh?'两次自评雷达图':'Comparison of two self-reported radar profiles' ?>">
<?php foreach([20,40,60,80,100] as $rad):?><polygon points="<?= implode(' ',array_map(static function($i)use($rad){$a=deg2rad(-90+$i*60);return round(170+cos($a)*$rad,1).','.round(153+sin($a)*$rad,1);},range(0,5))) ?>" fill="none" stroke="currentColor" stroke-opacity=".16"/><?php endforeach;?>
<?php foreach($axes as $i=>$axis):$a=deg2rad(-90+$i*60);?><line x1="170" y1="153" x2="<?= 170+100*cos($a) ?>" y2="<?= 153+100*sin($a) ?>" stroke="currentColor" stroke-opacity=".14"/><text x="<?= round(170+123*cos($a),1) ?>" y="<?= round(157+123*sin($a),1) ?>" font-size="12" fill="currentColor" text-anchor="middle"><?= e(t($axis)) ?></text><?php endforeach;?>
<polygon points="<?= radarOverlayPoints($selected[0]['profile'],$axes) ?>" fill="#45c9ed" fill-opacity=".16" stroke="#21b6d0" stroke-width="2.8"/><polygon points="<?= radarOverlayPoints($selected[1]['profile'],$axes) ?>" fill="#a7e65a" fill-opacity=".13" stroke="#8fc83c" stroke-width="2.8"/>
</svg><div class="progress-legend"><p><span style="color:#21b6d0">■</span> <?= e($selected[0]['row']['created_at']) ?></p><p><span style="color:#8fc83c">■</span> <?= e($selected[1]['row']['created_at']) ?></p><p><?= $zh?'这是玩家自评变化，并不等于实际竞技成绩的客观提升。':'These are changes in self-reported responses, not independently measured on-court improvement.' ?></p></div></div>
<?php else:?><p class="reference-help"><?= $zh?'至少需要两条主动保存的技术评估记录，才可以显示成长对比。':'At least two saved skill assessments are needed to compare progress.' ?></p><?php endif; ?>
</div></div></section></main><?php require __DIR__.'/includes/footer.php'; ?>
