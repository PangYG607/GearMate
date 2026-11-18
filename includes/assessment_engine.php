<?php
require_once __DIR__.'/language.php';
require_once __DIR__.'/question_bank.php';
function conditionApplies(?string $condition,array $answers):bool {
    if ($condition===null) return true;
    return match($condition) {
      'experienced' => !in_array($answers['q5']??'beginner',['beginner','developing'],true),
      'advanced' => ($answers['q5']??'')==='advanced',
      'doubles' => in_array($answers['q4']??'',['doubles','mixed','both'],true),
      'current_racket' => ($answers['q26']??'no')==='yes',
      'attack' => in_array($answers['q25']??'',['power'],true) || ($answers['q23']??'')==='smash',
      default => false,
    };
}
function validateAnswers(array $posted): array {
    $clean=[];$errors=[];$questions=questionBank();
    foreach($questions as $id=>$q){
        $value=trim((string)($posted['q'.$id]??''));
        if($q['type']==='nickname') {
            $len=unicodeLength($value,'UTF-8');
            if($len<2 || $len>40) $errors[]=$id;
            else $clean['q'.$id]=$value;
        }elseif($q['type']==='budget'){
            if(!ctype_digit($value) || (int)$value<100 || (int)$value>3000) $errors[]=$id;
            else $clean['q'.$id]=(int)$value;
        }else{
            if($value!=='' && isset($q['choices'][$value])) $clean['q'.$id]=$value;
        }
    }
    foreach($questions as $id=>$q){
        if(conditionApplies($q['condition'],$clean) && !array_key_exists('q'.$id,$clean)) $errors[]=$id;
        if(!conditionApplies($q['condition'],$clean)) unset($clean['q'.$id]);
    }
    return [$clean,array_values(array_unique($errors))];
}
function dimensionScore(array $a,array $ids): ?int {
    $n=[];
    foreach($ids as $id){$v=$a['q'.$id]??null;if(is_string($v) && in_array($v,['1','2','3','4'],true))$n[]=(int)$v;}
    if(!$n) return null;
    return (int)round(((array_sum($n)/count($n))-1)*100/3);
}
function playerProfile(array $a): array {
    $axes=['attack'=>[9,10],'speed'=>[14,16,17],'control'=>[8,11,12],'defence'=>[13,15],'footwork'=>[16,17],'consistency'=>[7,8,18]];
    $radar=[];foreach($axes as $axis=>$ids)$radar[$axis]=dimensionScore($a,$ids);
    $vals=array_values(array_filter($radar,static fn($v)=>$v!==null));
    $mean=$vals ? array_sum($vals)/count($vals) : 50;
    $inferred = $mean<36?'beginner':($mean<58?'developing':($mean<79?'intermediate':'advanced'));
    $fav=array_filter($radar,static fn($v)=>$v!==null);arsort($fav);$strong=array_slice(array_keys($fav),0,2);
    asort($fav);$improve=array_slice(array_keys($fav),0,2);
    $styles=['power'=>0,'speed'=>0,'control'=>0,'allround'=>0];
    foreach(['q25'=>['power'=>'power','speed'=>'speed','control'=>'control','ease'=>'allround'],
             'q20'=>['fast'=>'speed','long'=>'allround','tactical'=>'control'],
             'q23'=>['smash'=>'power','drop'=>'control','drive'=>'speed','safe'=>'allround'],
             'q21'=>['smash'=>'power','net'=>'control','drives'=>'speed','defence'=>'speed','clear'=>'allround'] ] as $key=>$map) {
       $value=(string)($a[$key]??'');if(isset($map[$value])) $styles[$map[$value]]+=($key==='q25'?3:1);
    }
    arsort($styles);$primary=(string)array_key_first($styles);
    $balance=(string)($a['q29']??'unsure');
    if(!in_array($balance,['head-heavy','head-light','even'],true))$balance=['power'=>'head-heavy','speed'=>'head-light','control'=>'even','allround'=>'even'][$primary];
    $flex=(string)($a['q30']??'unsure');
    if(!in_array($flex,['hi-flex','medium','stiff'],true))$flex=$inferred==='advanced'?'stiff':($inferred==='beginner'?'hi-flex':'medium');
    $known=count($vals);
    return ['nickname'=>$a['q1'],'self_level'=>$a['q5'],'assessed_level'=>$inferred,'game_type'=>$a['q4'],
            'style'=>$primary,'balance'=>$balance,'flex'=>$flex,'radar'=>$radar,'strong'=>$strong,'improve'=>$improve,
            'budget'=>(int)$a['q33'],'include_accessories'=>($a['q34']==='complete'),
            'brand'=>$a['q32'],'weight'=>$a['q28'],'rule_version'=>'gearmate-v04-selfreport','confidence'=>$known===6?'all_six':'limited'];
}
function matchRacket(array $r,array $a,array $p):array {
    $score=0;$why=[];$drawbacks=[];$zh=language()==='zh';
    $balance=(string)$r['balance'];$level=(string)$r['level'];$flex=(string)$r['flex'];$weight=(string)$r['weight'];
    if($balance===$p['balance']){$score+=25;$why[]=$zh?'拍头平衡符合你的打法与手感偏好。':'Racket balance aligns with your play-style and feel preference.';}
    elseif($balance==='even'){$score+=15;$why[]=$zh?'平衡型设计值得作为全面打法的替代选择。':'Even balance is a versatile alternative worth considering.';}
    else{$score+=6;$drawbacks[]=$zh?'拍头平衡与你的主要偏好不同，建议实际试挥。':'Balance differs from your preferred feel; try a swing before deciding.';}
    $skillOrder=['beginner'=>0,'intermediate'=>2,'advanced'=>3];$inferredOrder=['beginner'=>0,'developing'=>1,'intermediate'=>2,'advanced'=>3];
    $diff=abs(($skillOrder[$level]??1)-($inferredOrder[$p['assessed_level']]??1));
    if($diff===0){$score+=25;$why[]=$zh?'产品的演示适用等级与你的问卷表现接近。':'Demo skill suitability tag aligns with your questionnaire profile.';}
    elseif($diff===1){$score+=17;$why[]=$zh?'这支球拍可以作为相邻技术阶段的选择。':'This racket sits near your estimated experience range.';}
    else{$score+=4;$drawbacks[]=t('weak_skill');}
    $pref=$p['weight'];
    if($pref==='unsure'){$score+=14;$why[]=$zh?'球拍重量属于常见规格，可以结合实际手感判断。':'Weight is a familiar common variant; confirm the feel in person.';}
    elseif(($pref==='3u'&&$weight==='3U')||($pref==='4u'&&$weight==='4U')||($pref==='5u'&&in_array($weight,['5U','6U','7U'],true))){$score+=20;$why[]=$zh?'重量规格符合你的明确偏好。':'Weight matches the preference you selected.';}
    elseif($pref==='5u'&&$weight==='4U'){$score+=12;}
    else{$score+=6;$drawbacks[]=$zh?'重量与你选的手感偏好存在差异。':'Weight may feel different from your stated preference.';}
    if($flex===$p['flex']){$score+=15;$why[]=$zh?'中杆硬度符合建议的击球手感。':'Shaft flexibility fits the suggested playing feel.';}
    elseif(($flex==='medium'&&in_array($p['flex'],['hi-flex','stiff'],true))||($p['flex']==='medium'&&in_array($flex,['hi-flex','stiff'],true))){$score+=9;}
    else{$score+=3;}
    if($p['assessed_level']==='beginner'||$p['assessed_level']==='developing'){
        if(in_array($flex,['hi-flex','medium'],true)){$score+=15;$why[]=$zh?'中杆偏软或适中，可能更容易适应。':'Softer or medium shaft may be more forgiving while developing technique.';}
        else{$score+=4;$drawbacks[]=t('weak_stiff');}
    }else{
        if($p['style']==='power'&&in_array($flex,['stiff','extra-stiff'],true))$score+=15;
        elseif($flex==='medium')$score+=12;
        else $score+=9;
    }
    if($balance==='head-heavy'&&$p['style']==='speed')$drawbacks[]=t('weak_speed');
    if($balance==='head-light'&&$p['style']==='power')$drawbacks[]=t('weak_power');
    if($flex==='hi-flex'&&$p['assessed_level']==='advanced')$drawbacks[]=t('weak_soft');
    if($p['brand']===$r['brand']){$score=min(100,$score+3);}
    if(!$drawbacks)$drawbacks[]=t('weak_generic');
    return ['score'=>min(100,$score),'reasons'=>array_slice(array_unique($why),0,4),'drawbacks'=>array_slice(array_unique($drawbacks),0,2)];
}
function rankedRackets(array $all,array $a,array $profile):array{
  $out=[];foreach($all as $r){$ranking=matchRacket($r,$a,$profile);$out[]=array_merge($r,$ranking);}
  usort($out,static fn($x,$y)=>($y['score']<=>$x['score'])?:(referencePrice($x)<=>referencePrice($y)));return $out;
}
function selectedRackets(array $all,array $answers,array $profile):array {
  $ranked=rankedRackets($all,$answers,$profile);
  $minimumAccessory=0.0;
  if($profile['include_accessories']) {
    $catalog=getAccessoryCatalog();$stringPrices=[];$gripPrices=[];
    foreach($catalog as $item) {
      if($item['type']==='string')$stringPrices[]=(float)$item['price'];
      if($item['type']==='grip')$gripPrices[]=(float)$item['price'];
    }
    $minimumAccessory=($stringPrices?min($stringPrices):0)+($gripPrices?min($gripPrices):0);
  }
  $budget=$profile['budget']-$minimumAccessory;
  $budget=max(0,$budget);
  $eligible=array_values(array_filter($ranked,static fn($r)=>referencePrice($r)<=$budget && $r['score']>=72));
  if($eligible)return ['fallback'=>false,'rackets'=>array_slice($eligible,0,3)];
  $selected=[];
  $add=function(?array $r)use(&$selected):void{if($r!==null&&!isset($selected[$r['id']]))$selected[$r['id']]=$r;};
  // Different price bands; fallback never invents available inventory or prices.
  $within=array_values(array_filter($ranked,static fn($r)=>referencePrice($r)<=$budget));
  $near=array_values(array_filter($ranked,static fn($r)=>referencePrice($r)>$budget));
  $add($within[0]??null);
  $add($near[0]??null);
  $add($ranked[0]??null);
  foreach($ranked as $r){if(count($selected)>=3)break;$add($r);}
  return ['fallback'=>true,'rackets'=>array_slice(array_values($selected),0,3)];
}
function persistAssessment(int $userId,array $a,array $p,string $mode='new'):bool {
  $_SESSION['assessment_answers']=$a;
  $_SESSION['assessment_profile']=$p;
  $_SESSION['assessment_owner']=$userId;
  if ($userId<=0) return true; // Guest results exist only in this browser session.
  try {
    $db=getDbConnection();
    $jsonA=json_encode($a,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
    $jsonP=json_encode($p,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
    if($mode==='edit') {
      $query=$db->prepare('SELECT id FROM player_assessments WHERE user_id=? ORDER BY id DESC LIMIT 1');
      $query->execute([$userId]);$id=$query->fetchColumn();
      if($id) {
        $update=$db->prepare('UPDATE player_assessments SET answers_json=?,profile_json=? WHERE id=? AND user_id=?');
        $update->execute([$jsonA,$jsonP,$id,$userId]);return true;
      }
    }
    $stmt=$db->prepare('INSERT INTO player_assessments (user_id,answers_json,profile_json) VALUES (?,?,?)');
    $stmt->execute([$userId,$jsonA,$jsonP]);
    return true;
  } catch(Throwable $ex){return false;}
}
function loadAssessment(int $userId):?array {
  if(($_SESSION['assessment_owner']??null)===$userId && isset($_SESSION['assessment_answers'],$_SESSION['assessment_profile']))
    return ['answers'=>$_SESSION['assessment_answers'],'profile'=>$_SESSION['assessment_profile']];
  if ($userId<=0) return null;
  try {
    $db=getDbConnection();
    $stmt=$db->prepare('SELECT answers_json,profile_json FROM player_assessments WHERE user_id=? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId]);$r=$stmt->fetch();
    if($r) {
      $a=json_decode($r['answers_json'],true,512,JSON_THROW_ON_ERROR);
      $p=json_decode($r['profile_json'],true,512,JSON_THROW_ON_ERROR);
      $_SESSION['assessment_answers']=$a;$_SESSION['assessment_profile']=$p;$_SESSION['assessment_owner']=$userId;
      return ['answers'=>$a,'profile'=>$p];
    }
  }catch(Throwable $ex){}
  return null;
}
