<?php
require __DIR__.'/includes/language.php';
if(currentUser()){header('Location: account.php');exit;}
$page='Register';$errors=[];$name='';$email='';$return=safeReturnTo((string)($_POST['return']??$_GET['return']??'questionnaire.php'));
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=trim((string)($_POST['name']??''));$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');$confirm=(string)($_POST['confirm']??'');
  if(!verifyCsrf((string)($_POST['csrf']??'')))$errors[]=language()==='zh'?'页面已过期，请刷新。':'Invalid session; refresh the page.';
  if(unicodeLength($name)<2||unicodeLength($name)>60)$errors[]=language()==='zh'?'显示名称请输入 2–60 字。':'Display name must be 2–60 characters.';
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>190)$errors[]=language()==='zh'?'请输入有效的邮箱。':'Enter a valid email address.';
  if(strlen($password)<8)$errors[]=language()==='zh'?'密码至少需要 8 个字符。':'Password must have at least 8 characters.';
  if($password!==$confirm)$errors[]=language()==='zh'?'两次输入的密码不一致。':'Passwords do not match.';
  if(!$errors){
    try{$db=getDbConnection();
      $hash=password_hash($password,PASSWORD_DEFAULT);
      try{$stmt=$db->prepare('INSERT INTO users (display_name,email,password_hash,preferred_language) VALUES (?,?,?,?)');$stmt->execute([$name,$email,$hash,language()]);}
      catch(PDOException $prefEx){
        // Backward compatible when the v0.4 language column was not migrated yet.
        if(!str_contains($prefEx->getMessage(),'preferred_language'))throw $prefEx;
        $stmt=$db->prepare('INSERT INTO users (display_name,email,password_hash) VALUES (?,?,?)');$stmt->execute([$name,$email,$hash]);
      }
      session_regenerate_id(true);unset($_SESSION['guest_enabled'],$_SESSION['guest_name']);$_SESSION['assessment_owner']=0;$_SESSION['account_id']=(int)$db->lastInsertId();$_SESSION['account_name']=$name;
      if(isset($_SESSION['assessment_answers'],$_SESSION['assessment_profile'])) {
        require_once __DIR__.'/includes/assessment_engine.php';
        persistAssessment((int)$_SESSION['account_id'],$_SESSION['assessment_answers'],$_SESSION['assessment_profile'],'new');
      }
      header('Location: '.$return);exit;
    }catch(PDOException $ex){$errors[]=($ex->getCode()==='23000')?(language()==='zh'?'这个邮箱已经注册，请直接登录。':'That email is already registered.'):(language()==='zh'?'数据库不可用。请检查 XAMPP MySQL。':'Database unavailable. Check XAMPP MySQL.');}
  }
}
require __DIR__.'/includes/header.php';
?>
<main class="auth-page"><div class="container auth-grid"><div class="auth-story"><p class="eyebrow">GEARMATE / NEW PLAYER</p><h1><?= e(t('register_title')) ?><br><em>YOUR GAME.</em></h1><p><?= e(t('register_sub')) ?></p><div class="auth-points"><span>✦ <?= e(t('feature_1')) ?></span><span>✦ <?= e(t('feature_2')) ?></span><span>✦ <?= e(t('feature_3')) ?></span></div></div>
<div class="auth-panel"><p class="eyebrow green-eyebrow">JOIN GEARMATE</p><h2><?= e(t('register')) ?></h2><p class="auth-muted"><?= e(t('already')) ?> <a href="login.php?return=<?= e(rawurlencode($return)) ?>"><?= e(t('login')) ?> →</a></p>
<?php foreach($errors as $error):?><div class="form-alert" role="alert"><?= e($error) ?></div><?php endforeach; ?>
<form action="register.php" method="post" class="auth-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="return" value="<?= e($return) ?>">
<label for="name"><?= e(t('name')) ?></label><input id="name" name="name" value="<?= e($name) ?>" maxlength="60" autocomplete="nickname" required>
<label for="email"><?= e(t('email')) ?></label><input id="email" type="email" name="email" value="<?= e($email) ?>" maxlength="190" autocomplete="email" required>
<label for="password"><?= e(t('password')) ?> <small>(<?= e(t('password_8')) ?>)</small></label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
<label for="confirm"><?= e(t('confirm')) ?></label><input id="confirm" name="confirm" type="password" minlength="8" autocomplete="new-password" required>
<button class="button button-main" type="submit"><?= e(t('register_button')) ?> ↗</button></form></div></div></main>
<?php require __DIR__.'/includes/footer.php'; ?>
