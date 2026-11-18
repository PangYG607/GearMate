<?php
require __DIR__.'/includes/language.php';
if(currentUser()){header('Location: account.php');exit;}
$page='Login';$error='';$email='';$return=safeReturnTo((string)($_POST['return']??$_GET['return']??'questionnaire.php'));
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');$attempts=(int)($_SESSION['login_attempts']??0);
  if(!verifyCsrf((string)($_POST['csrf']??'')))$error=language()==='zh'?'登录状态无效，请刷新后重试。':'Invalid session; refresh and try again.';
  elseif($attempts>=10)$error=language()==='zh'?'尝试次数过多，请重新开启浏览器会话后再试。':'Too many attempts in this session; try a new browser session.';
  else{
    try{$db=getDbConnection();$stmt=$db->prepare('SELECT id, display_name, password_hash FROM users WHERE email=? LIMIT 1');$stmt->execute([$email]);$u=$stmt->fetch();
      if($u && password_verify($password,$u['password_hash'])){session_regenerate_id(true);unset($_SESSION['guest_enabled'],$_SESSION['guest_name'],$_SESSION['assessment_answers'],$_SESSION['assessment_profile'],$_SESSION['assessment_owner']);$_SESSION['login_attempts']=0;$_SESSION['account_id']=(int)$u['id'];$_SESSION['account_name']=$u['display_name'];
        try {
          $pref=$db->prepare('SELECT preferred_language FROM users WHERE id=?');$pref->execute([$u['id']]);$storedLang=$pref->fetchColumn();
          if(!isset($_SESSION['lang'])&&!isset($_COOKIE['gm_lang'])&&in_array($storedLang,['en','zh'],true))$_SESSION['lang']=$storedLang;
          $update=$db->prepare('UPDATE users SET preferred_language=? WHERE id=?');$update->execute([language(),$u['id']]);
        }catch(Throwable $ignore){} // v0.3.x accounts may not yet have this field.
        header('Location: '.$return);exit;}
      $_SESSION['login_attempts']=$attempts+1;$error=language()==='zh'?'邮箱或密码不正确。':'Incorrect email or password.';
    }catch(PDOException $ex){$error=language()==='zh'?'数据库不可用。请启动 MySQL 并导入数据库。':'Database unavailable. Start MySQL and import the database.';}
  }
}
require __DIR__.'/includes/header.php';
?>
<main class="auth-page"><div class="container auth-grid"><div class="auth-story"><p class="eyebrow">GEARMATE / PLAYER ACCESS</p><h1><?= e(t('login_title')) ?><br><em>GEAR UP.</em></h1><p><?= e(t('login_sub')) ?></p><div class="auth-points"><span>✦ <?= e(t('feature_1')) ?></span><span>✦ <?= e(t('feature_2')) ?></span><span>✦ <?= e(t('feature_3')) ?></span></div></div>
<div class="auth-panel"><p class="eyebrow green-eyebrow">PLAYER ACCESS</p><h2><?= e(t('login_title')) ?></h2><p class="auth-muted"><?= e(t('no_account')) ?> <a href="register.php?return=<?= e(rawurlencode($return)) ?>"><?= e(t('register')) ?> →</a></p>
<?php if($error):?><div class="form-alert" role="alert"><?= e($error) ?></div><?php endif; ?>
<form action="login.php" method="post" class="auth-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="return" value="<?= e($return) ?>">
<label for="email"><?= e(t('email')) ?></label><input id="email" name="email" type="email" value="<?= e($email) ?>" placeholder="you@example.com" autocomplete="email" required>
<label for="password"><?= e(t('password')) ?></label><input id="password" name="password" type="password" autocomplete="current-password" required>
<button class="button button-main" type="submit"><?= e(t('login_button')) ?> ↗</button></form><div class="guest-cta"><p><?= language()==='zh'?'不想创建账号？':'Just want to try it?' ?></p><a class="button button-outline" href="guest.php"><?= language()==='zh'?'以游客身份继续':'Continue as Guest' ?> →</a></div></div></div></main>
<?php require __DIR__.'/includes/footer.php'; ?>
