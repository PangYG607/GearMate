<?php
require __DIR__.'/includes/language.php';
if(currentUser()){header('Location: account.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST') {
  if(!verifyCsrf((string)($_POST['csrf']??''))) {http_response_code(403);exit('Invalid session');}
  $nickname=trim((string)($_POST['nickname']??''));
  if($nickname==='') $nickname=language()==='zh'?'游客玩家':'Guest Player';
  if(unicodeLength($nickname)>40) $nickname=language()==='zh'?'游客玩家':'Guest Player';
  session_regenerate_id(true);
  $_SESSION['guest_enabled']=true;$_SESSION['guest_name']=$nickname;
  // Clear any stale visitor assessment, not signed-in account assessments.
  unset($_SESSION['assessment_answers'],$_SESSION['assessment_profile'],$_SESSION['assessment_owner']);
  header('Location: questionnaire.php');exit;
}
$page='Guest';require __DIR__.'/includes/header.php';$actor=currentActor();
?><main class="section"><div class="container"><div class="profile-card" style="max-width:660px;margin:auto"><p class="eyebrow green-eyebrow">GEARMATE / GUEST ACCESS</p><h1><?= language()==='zh'?'游客快捷体验':'Quick Guest Access' ?></h1><p><?= language()==='zh'?'不需要邮箱。可完成问卷、推荐和 DIY，并导出配置单；关闭会话后资料可能消失。':'No email required. Complete assessment, recommendations and DIY; export a setup card. Session data can be lost when the session ends.' ?></p>
<form method="post" action="guest.php" class="auth-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><label><?= e(t('account_name')) ?></label><input name="nickname" maxlength="40" placeholder="Guest Player"><button type="submit" class="button button-main"><?= language()==='zh'?'以游客身份开始':'Continue as guest' ?> ↗</button></form>
<p><a href="login.php?return=questionnaire.php"><?= language()==='zh'?'登录已有账号':'Sign into an account' ?></a> · <a href="register.php?return=questionnaire.php"><?= language()==='zh'?'注册保存资料':'Register to save' ?></a></p><?php if($actor && !empty($actor['guest'])):?><p><a href="questionnaire.php"><?= language()==='zh'?'继续当前游客问卷':'Continue guest assessment' ?></a></p><?php endif; ?>
</div></div></main><?php require __DIR__.'/includes/footer.php'; ?>