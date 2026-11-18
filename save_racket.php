<?php
require __DIR__.'/includes/gear_features.php';$u=currentUser();
if(!$u){header('Location: login.php?return=catalogue.php');exit;}
if($_SERVER['REQUEST_METHOD']!=='POST'||!verifyCsrf((string)($_POST['csrf']??''))){http_response_code(403);exit('Invalid request');}
$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);$action=(string)($_POST['action']??'save');
if($id && racketById((int)$id)){
 try {$db=getDbConnection();if($action==='remove'){$s=$db->prepare('DELETE FROM saved_rackets WHERE user_id=? AND racket_id=?');}
 else {$s=$db->prepare('INSERT IGNORE INTO saved_rackets(user_id,racket_id) VALUES(?,?)');}
 $s->execute([$u['id'],$id]);}catch(Throwable $e){}
}
$return=(string)($_POST['return']??'catalogue.php');
header('Location: '.safeReturnTo($return));exit;
