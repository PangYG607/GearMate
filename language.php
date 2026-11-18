<?php
require __DIR__.'/includes/language.php';
$lang=(string)($_GET['lang']??'en');
$_SESSION['lang']=in_array($lang,['zh','en'],true)?$lang:'en';
setcookie('gm_lang', $_SESSION['lang'], ['expires'=>time()+31536000,'path'=>'/', 'samesite'=>'Lax', 'secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off', 'httponly'=>true]);
if ($user=currentUser()) {
    try {$db=getDbConnection();$stmt=$db->prepare('UPDATE users SET preferred_language=? WHERE id=?');$stmt->execute([$_SESSION['lang'],$user['id']]);}catch(Throwable $e){} // Language cookie always works without migration.
}
$return=(string)($_GET['return']??'index.php');
if (!preg_match('/^(index|catalogue|login|register|account|questionnaire|analysis|results|builder|battle|academy|progress|preferences|setup_card|admin_rackets|guest)\.php(?:\?[a-zA-Z0-9_=&%+.-]*)?$/',$return)) $return='index.php';
header('Location: '.$return);exit;
