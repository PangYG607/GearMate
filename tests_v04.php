<?php
// Run with: php tests_v04.php. No database writes. Guest integration logic tested in CLI.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/includes/gear_features.php';
function check04(bool $cond,string $text):void {if(!$cond)throw new RuntimeException($text);}
$_SESSION['lang']='zh';
check04(currentActor()===null,'Expected no actor before guest');
$_SESSION['guest_enabled']=true;$_SESSION['guest_name']='测试玩家';
check04(isGuest() && currentActor()['id']===0,'Guest session failed');
$q=questionBank();$a=[];
foreach($q as $item){$a['q'.$item['id']]=$item['type']==='nickname'?'测试玩家':($item['type']==='budget'?450:(string)array_key_first($item['choices']));}
$a['q5']='intermediate';$a['q4']='doubles';$a['q26']='yes';$a['q23']='smash';
[$clean,$errors]=validateAnswers($a);check04(!$errors,'Question validation failed');
$p=playerProfile($clean);check04(count($p['radar'])===6,'Missing radar');
check04(persistAssessment(0,$clean,$p,'new'),'Guest save failed');
$loaded=loadAssessment(0);check04($loaded!==null && $loaded['profile']['nickname']==='测试玩家','Guest reload failed');
$catalog=getCatalog();check04(count($catalog)>=38,'Legacy catalogue missing');
$acc=getAccessoryCatalog();check04(count($acc)>=14,'Expected extra source-backed strings and grips');$str=null;$grip=null;
foreach($acc as $item){if($item['type']==='string' && $str===null)$str=$item;if($item['type']==='grip' && $grip===null)$grip=$item;}
$d=setupDetails($catalog[0],$str,$grip,22,$p);
check04($d['tension_status']==='unverified','Should NOT label unverified tension as approved');
check04(abs($d['total']-((float)referencePrice($catalog[0])+(float)$str['price']+(float)$grip['price']))<0.001,'Wrong cost breakdown');
check04(saveGearCard(0,$d)===0,'Guest card not saved');
$copy=loadGearCard(0);check04($copy!==null && $copy['details']['string_name']===$str['name'],'Guest setup card not loaded');
$rank=selectedRackets($catalog,$clean,$p);check04(count($rank['rackets'])>0 && count($rank['rackets'])<=3,'Invalid recommendation length');
check04(safeReturnTo('https://evil.example')==='account.php','External redirect accepted');
check04(!isAdmin(0),'Guest should never be admin');
echo "PASS: Guest access, questionnaire storage, six-axis profile, linked catalogue, honest tension status, source-linked setup card, prices, Top 3, redirect protection.\n";
