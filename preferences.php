<?php
require __DIR__.'/includes/gear_features.php';$actor=currentActor();if(!$actor){header('Location: login.php?return=questionnaire.php');exit;}
$ass=loadAssessment($actor['id']);if(!$ass){header('Location: questionnaire.php');exit;}
$zh=language()==='zh';$questions=questionBank();$fields=[25,28,29,30,32,33,34,35,36];$answers=$ass['answers'];$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verifyCsrf((string)($_POST['csrf']??'')))$error=$zh?'无效会话，请刷新。':'Session expired; refresh.';
 else{
  $pending=$answers;
  foreach($fields as $n){$field='q'.$n;$q=$questions[$n];$v=trim((string)($_POST[$field]??''));
   if($q['type']==='budget'){$ok=ctype_digit($v)&&(int)$v>=100&&(int)$v<=3000;if($ok)$pending[$field]=(int)$v;}
   else {$ok=isset($q['choices'][$v]);if($ok)$pending[$field]=$v;}
   if(!$ok){$error=$zh?'部分选项无效，请检查。':'One or more answers are invalid.';break;}
  }
  if(!$error){$profile=playerProfile($pending);if(persistAssessment($actor['id'],$pending,$profile,'edit')){header('Location: analysis.php');exit;}else $error=$zh?'保存失败，请检查数据库。':'Save failed. Check database migrations.';}
 }
}
$page='Preferences';require __DIR__.'/includes/header.php';
?><main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / SETTINGS</p><h1><?= $zh?'修改装备偏好':'UPDATE EQUIPMENT PREFERENCES' ?></h1><p><?= $zh?'只修改预算与装备偏好。技术自评分数不变，也不会新建成长记录。':'Change budget and gear preferences without creating a new skill-progress entry.' ?></p></div></section><section class="section"><div class="container"><div class="profile-card preference-form">
<?php if($error):?><div class="form-alert"><?= e($error) ?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
<?php foreach($fields as $id):$q=$questions[$id];$key='q'.$id;?><fieldset class="pref-item"><legend><?= e($q[$zh?'zh':'en']) ?></legend>
<?php if($q['type']==='budget'):?><label>RM <input type="number" name="<?= $key ?>" min="100" max="3000" step="10" value="<?= (int)$answers[$key] ?>" required></label>
<?php else:?><select name="<?= $key ?>"><?php foreach($q['choices'] as $value=>$label):?><option value="<?= e($value) ?>" <?= ($answers[$key]??'')===$value?'selected':'' ?>><?= e($label[$zh?'zh':'en']) ?></option><?php endforeach;?></select><?php endif;?></fieldset><?php endforeach; ?>
<button class="button button-main" type="submit"><?= $zh?'保存偏好并重新推荐':'Save preferences & recalculate' ?> →</button></form></div></div></section></main>
<?php require __DIR__.'/includes/footer.php'; ?>
