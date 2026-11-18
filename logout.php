<?php
require __DIR__.'/includes/auth.php';
bootAuth();
if($_SERVER['REQUEST_METHOD']!=='POST'||!verifyCsrf((string)($_POST['csrf']??''))){http_response_code(405);exit('Use the account sign-out button.');}
$lang=$_SESSION['lang']??$_COOKIE['gm_lang']??'en';
$_SESSION=['lang'=>in_array($lang,['zh','en'],true)?$lang:'en'];
session_regenerate_id(true);
header('Location: index.php');exit;
