<?php
// CLI-only regression checks; does not connect to MySQL and cannot bypass site login.
if (PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/includes/assessment_engine.php';
$questions=questionBank();assert(count($questions)===40);
$a=[];
foreach($questions as $q){$id=$q['id'];$a['q'.$id]=$q['type']==='nickname'?'Test Player':($q['type']==='budget'?650:(string)array_key_first($q['choices']));}
$a['q4']='doubles';$a['q5']='intermediate';$a['q26']='yes';$a['q23']='smash';
[$clean,$errors]=validateAnswers($a);
if($errors)throw new Exception('Valid inputs rejected: '.implode(',',$errors));
$p=playerProfile($clean);if(count($p['radar'])!==6)throw new Exception('Radar axes missing');
if(!isset($p['style'],$p['budget']))throw new Exception('Profile incomplete');
$all=require __DIR__.'/includes/demo_data.php';
$recommend=selectedRackets($all,$clean,$p);
if(count($recommend['rackets'])===0 || count($recommend['rackets'])>3)throw new Exception('Recommendation count invalid');
$a['q33']=100;[$low,$errors]=validateAnswers($a);if($errors)throw new Exception('Low budget not accepted');
$pLow=playerProfile($low);$fallback=selectedRackets($all,$low,$pLow);
if(!$fallback['fallback'] || count($fallback['rackets'])!==3)throw new Exception('Low budget fallback failure');
$_SESSION['lang']='zh'; $astrox99 = array_values(array_filter($all, static fn($r)=>$r['model']==='ASTROX 99 PLAY'))[0]??null; if(!$astrox99 || modelName($astrox99)!=='尤尼克斯 天斧99 PLAY (ASTROX 99 PLAY)')throw new Exception('Chinese racket name failure');
foreach($fallback['rackets'] as $r){$score=matchRacket($r,$low,$pLow);if($score['score']<0||$score['score']>100||empty($score['drawbacks']))throw new Exception('Invalid score/weakness');}
echo "PASS: 40 bilingual questions, conditional validation, six-axis player profile, Top 3, low-budget fallback, Chinese model names, explanations.\n";
